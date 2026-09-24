<?php

namespace App\Services\Inventory;

use App\Models\StockMovement;
use App\Models\WarehouseStock;
use DomainException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class StockService
{
    public function increase(
        int $warehouseId,
        int $skuId,
        float $quantity,
        string $movementType,
        ?float $unitCost = null,
        ?Model $reference = null,
        ?int $userId = null,
        ?string $notes = null,
    ): WarehouseStock {
        $this->assertPositiveQuantity(
            $quantity
        );

        return DB::transaction(function () use (
            $warehouseId,
            $skuId,
            $quantity,
            $movementType,
            $unitCost,
            $reference,
            $userId,
            $notes
        ) {
            $stock = $this->lockedStock(
                $warehouseId,
                $skuId
            );

            $oldQuantity =
                (float) $stock->quantity;

            $oldAverage =
                (float) $stock->average_cost;

            $newQuantity =
                $oldQuantity + $quantity;

            $newAverage =
                $oldAverage;

            if ($unitCost !== null) {
                $newAverage = (
                    ($oldQuantity * $oldAverage)
                    +
                    ($quantity * $unitCost)
                ) / $newQuantity;
            }

            $stock->update([
                'quantity' =>
                    $newQuantity,

                'average_cost' =>
                    $newAverage,

                'last_movement_at' =>
                    now(),
            ]);

            $this->recordMovement(
                stock: $stock,
                quantityChange: $quantity,
                movementType: $movementType,
                unitCost: $unitCost,
                reference: $reference,
                userId: $userId,
                notes: $notes,
            );

            return $stock->fresh();
        });
    }

    public function decrease(
        int $warehouseId,
        int $skuId,
        float $quantity,
        string $movementType,
        ?float $unitCost = null,
        ?Model $reference = null,
        ?int $userId = null,
        ?string $notes = null,
    ): WarehouseStock {
        $this->assertPositiveQuantity(
            $quantity
        );

        return DB::transaction(function () use (
            $warehouseId,
            $skuId,
            $quantity,
            $movementType,
            $unitCost,
            $reference,
            $userId,
            $notes
        ) {
            $stock = $this->lockedStock(
                $warehouseId,
                $skuId
            );

            $available =
                $this->availableQuantity(
                    $stock
                );

            if ($quantity > $available) {
                throw new DomainException(
                    "Insufficient stock. Available: {$available}, requested: {$quantity}."
                );
            }

            $movementCost =
                $unitCost
                ??
                (float) $stock->average_cost;

            $stock->update([
                'quantity' =>
                    (float) $stock->quantity
                    - $quantity,

                'last_movement_at' =>
                    now(),
            ]);

            $this->recordMovement(
                stock: $stock,
                quantityChange: -$quantity,
                movementType: $movementType,
                unitCost: $movementCost,
                reference: $reference,
                userId: $userId,
                notes: $notes,
            );

            return $stock->fresh();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Reserve
    |--------------------------------------------------------------------------
    */

    public function reserve(
        int $warehouseId,
        int $skuId,
        float $quantity
    ): WarehouseStock {
        $this->assertPositiveQuantity(
            $quantity
        );

        return DB::transaction(function () use (
            $warehouseId,
            $skuId,
            $quantity
        ) {
            $stock = $this->lockedStock(
                $warehouseId,
                $skuId
            );

            $available =
                $this->availableQuantity(
                    $stock
                );

            if ($quantity > $available) {
                throw new DomainException(
                    "Insufficient available stock for reservation. Available: {$available}, requested: {$quantity}."
                );
            }

            $stock->update([
                'reserved_quantity' =>
                    (float) $stock->reserved_quantity
                    + $quantity,
            ]);

            return $stock->fresh();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Release Reservation
    |--------------------------------------------------------------------------
    */

    public function release(
        int $warehouseId,
        int $skuId,
        float $quantity
    ): WarehouseStock {
        $this->assertPositiveQuantity(
            $quantity
        );

        return DB::transaction(function () use (
            $warehouseId,
            $skuId,
            $quantity
        ) {
            $stock = $this->lockedStock(
                $warehouseId,
                $skuId
            );

            $reserved =
                (float) $stock->reserved_quantity;

            if ($quantity > $reserved) {
                throw new DomainException(
                    "Cannot release {$quantity}. Reserved quantity is {$reserved}."
                );
            }

            $stock->update([
                'reserved_quantity' =>
                    $reserved - $quantity,
            ]);

            return $stock->fresh();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Consume Reserved Stock
    |--------------------------------------------------------------------------
    */

    public function consumeReserved(
        int $warehouseId,
        int $skuId,
        float $quantity,
        string $movementType,
        ?Model $reference = null,
        ?int $userId = null,
        ?string $notes = null,
    ): WarehouseStock {
        $this->assertPositiveQuantity(
            $quantity
        );

        return DB::transaction(function () use (
            $warehouseId,
            $skuId,
            $quantity,
            $movementType,
            $reference,
            $userId,
            $notes
        ) {
            $stock = $this->lockedStock(
                $warehouseId,
                $skuId
            );

            $reserved =
                (float) $stock->reserved_quantity;

            $physical =
                (float) $stock->quantity;

            if ($quantity > $reserved) {
                throw new DomainException(
                    "Reserved quantity is insufficient. Reserved: {$reserved}, requested: {$quantity}."
                );
            }

            if ($quantity > $physical) {
                throw new DomainException(
                    "Physical stock is insufficient."
                );
            }

            $unitCost =
                (float) $stock->average_cost;

            $stock->update([
                'quantity' =>
                    $physical - $quantity,

                'reserved_quantity' =>
                    $reserved - $quantity,

                'last_movement_at' =>
                    now(),
            ]);

            $this->recordMovement(
                stock: $stock,
                quantityChange: -$quantity,
                movementType: $movementType,
                unitCost: $unitCost,
                reference: $reference,
                userId: $userId,
                notes: $notes,
            );

            return $stock->fresh();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Adjustment
    |--------------------------------------------------------------------------
    */

    public function adjustTo(
        int $warehouseId,
        int $skuId,
        float $targetQuantity,
        string $movementType = 'inventory_adjustment',
        ?float $unitCost = null,
        ?Model $reference = null,
        ?int $userId = null,
        ?string $notes = null,
    ): WarehouseStock {
        if ($targetQuantity < 0) {
            throw new InvalidArgumentException(
                'Target quantity cannot be negative.'
            );
        }

        return DB::transaction(function () use (
            $warehouseId,
            $skuId,
            $targetQuantity,
            $movementType,
            $unitCost,
            $reference,
            $userId,
            $notes
        ) {
            $stock = $this->lockedStock(
                $warehouseId,
                $skuId
            );

            $reserved =
                (float) $stock->reserved_quantity;

            if ($targetQuantity < $reserved) {
                throw new DomainException(
                    "Target quantity cannot be lower than reserved quantity ({$reserved})."
                );
            }

            $oldQuantity =
                (float) $stock->quantity;

            $difference =
                $targetQuantity
                - $oldQuantity;

            if (
                abs($difference)
                < 0.000001
            ) {
                return $stock;
            }

            $oldAverage =
                (float) $stock->average_cost;

            $newAverage =
                $oldAverage;

            if (
                $difference > 0
                &&
                $unitCost !== null
                &&
                $targetQuantity > 0
            ) {
                $newAverage = (
                    ($oldQuantity * $oldAverage)
                    +
                    ($difference * $unitCost)
                ) / $targetQuantity;
            }

            $stock->update([
                'quantity' =>
                    $targetQuantity,

                'average_cost' =>
                    $newAverage,

                'last_movement_at' =>
                    now(),
            ]);

            $this->recordMovement(
                stock: $stock,
                quantityChange: $difference,
                movementType: $movementType,
                unitCost:
                    $unitCost ?? $newAverage,
                reference: $reference,
                userId: $userId,
                notes: $notes,
            );

            return $stock->fresh();
        });
    }

    public function currentQuantity(
        int $warehouseId,
        int $skuId
    ): float {
        return DB::transaction(function () use (
            $warehouseId,
            $skuId
        ) {
            return (float) $this
                ->lockedStock(
                    $warehouseId,
                    $skuId
                )
                ->quantity;
        });
    }

    public function availableQuantity(
        WarehouseStock $stock
    ): float {
        return
            (float) $stock->quantity
            -
            (float) $stock->reserved_quantity;
    }

    private function lockedStock(
        int $warehouseId,
        int $skuId
    ): WarehouseStock {
        WarehouseStock::firstOrCreate(
            [
                'warehouse_id' =>
                    $warehouseId,

                'product_sku_id' =>
                    $skuId,
            ],
            [
                'quantity' => 0,
                'reserved_quantity' => 0,
                'minimum_stock' => 0,
                'average_cost' => 0,
            ]
        );

        return WarehouseStock::query()
            ->where(
                'warehouse_id',
                $warehouseId
            )
            ->where(
                'product_sku_id',
                $skuId
            )
            ->lockForUpdate()
            ->firstOrFail();
    }

    private function recordMovement(
        WarehouseStock $stock,
        float $quantityChange,
        string $movementType,
        ?float $unitCost,
        ?Model $reference,
        ?int $userId,
        ?string $notes,
    ): void {
        StockMovement::create([
            'warehouse_id' =>
                $stock->warehouse_id,

            'product_sku_id' =>
                $stock->product_sku_id,

            'user_id' =>
                $userId,

            'movement_type' =>
                $movementType,

            'quantity_change' =>
                $quantityChange,

            'balance_after' =>
                $stock->quantity,

            'unit_cost' =>
                $unitCost,

            'reference_type' =>
                $reference?->getMorphClass(),

            'reference_id' =>
                $reference?->getKey(),

            'occurred_at' =>
                now(),

            'notes' =>
                $notes,
        ]);
    }

    private function assertPositiveQuantity(
        float $quantity
    ): void {
        if ($quantity <= 0) {
            throw new InvalidArgumentException(
                'Quantity must be greater than zero.'
            );
        }
    }
}