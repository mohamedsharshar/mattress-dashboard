<?php

namespace App\Services\Inventory;

use App\Models\InventoryCount;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\Sale;
use App\Models\SalesReturn;
use App\Models\StockTransfer;
use DomainException;
use Illuminate\Support\Facades\DB;

class InventoryWorkflowService
{
    public function __construct(
        private StockService $stock
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | Purchase Receive
    |--------------------------------------------------------------------------
    */

    public function receivePurchase(
        int $purchaseId,
        ?int $userId = null
    ): Purchase {
        return DB::transaction(function () use (
            $purchaseId,
            $userId
        ) {
            $purchase = Purchase::query()
                ->with('items')
                ->lockForUpdate()
                ->findOrFail($purchaseId);

            if ($purchase->status === 'received') {
                return $purchase;
            }

            if ($purchase->status === 'cancelled') {
                throw new DomainException(
                    'Cancelled purchase cannot be received.'
                );
            }

            if ($purchase->items->isEmpty()) {
                throw new DomainException(
                    'Purchase has no items.'
                );
            }

            foreach ($purchase->items as $item) {
                $this->stock->increase(
                    warehouseId:
                        $purchase->warehouse_id,

                    skuId:
                        $item->product_sku_id,

                    quantity:
                        (float) $item->quantity,

                    movementType:
                        'purchase_receive',

                    unitCost:
                        (float) $item->unit_cost,

                    reference:
                        $purchase,

                    userId:
                        $userId ?? $purchase->user_id,
                );
            }

            $purchase->update([
                'status' => 'received',
                'received_at' => now(),
            ]);

            return $purchase->fresh('items');
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Sale Complete
    |--------------------------------------------------------------------------
    */

    public function completeSale(
        int $saleId,
        ?int $userId = null
    ): Sale {
        return DB::transaction(function () use (
            $saleId,
            $userId
        ) {
            $sale = Sale::query()
                ->with('items')
                ->lockForUpdate()
                ->findOrFail($saleId);

            if ($sale->status === 'completed') {
                return $sale;
            }

            if ($sale->status === 'cancelled') {
                throw new DomainException(
                    'Cancelled sale cannot be completed.'
                );
            }

            if ($sale->items->isEmpty()) {
                throw new DomainException(
                    'Sale has no items.'
                );
            }

            foreach ($sale->items as $item) {
                $stock = $this->stock->decrease(
                    warehouseId:
                        $sale->warehouse_id,

                    skuId:
                        $item->product_sku_id,

                    quantity:
                        (float) $item->quantity,

                    movementType:
                        'sale',

                    reference:
                        $sale,

                    userId:
                        $userId ?? $sale->user_id,
                );

                if ($item->unit_cost === null) {
                    $item->update([
                        'unit_cost' =>
                            $stock->average_cost,
                    ]);
                }
            }

            $sale->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            return $sale->fresh('items');
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Transfer
    |--------------------------------------------------------------------------
    */

    public function completeTransfer(
        int $transferId,
        ?int $userId = null
    ): StockTransfer {
        return DB::transaction(function () use (
            $transferId,
            $userId
        ) {
            $transfer = StockTransfer::query()
                ->with('items')
                ->lockForUpdate()
                ->findOrFail($transferId);

            if ($transfer->status === 'completed') {
                return $transfer;
            }

            if ($transfer->status === 'cancelled') {
                throw new DomainException(
                    'Cancelled transfer cannot be completed.'
                );
            }

            if (
                $transfer->from_warehouse_id
                ===
                $transfer->to_warehouse_id
            ) {
                throw new DomainException(
                    'Source and destination warehouses must be different.'
                );
            }

            if ($transfer->items->isEmpty()) {
                throw new DomainException(
                    'Transfer has no items.'
                );
            }

            foreach ($transfer->items as $item) {
                $sourceStock =
                    $this->stock->decrease(
                        warehouseId:
                            $transfer->from_warehouse_id,

                        skuId:
                            $item->product_sku_id,

                        quantity:
                            (float) $item->quantity,

                        movementType:
                            'transfer_out',

                        reference:
                            $transfer,

                        userId:
                            $userId ?? $transfer->user_id,
                    );

                $this->stock->increase(
                    warehouseId:
                        $transfer->to_warehouse_id,

                    skuId:
                        $item->product_sku_id,

                    quantity:
                        (float) $item->quantity,

                    movementType:
                        'transfer_in',

                    unitCost:
                        (float) $sourceStock->average_cost,

                    reference:
                        $transfer,

                    userId:
                        $userId ?? $transfer->user_id,
                );

                $item->update([
                    'received_quantity' =>
                        $item->quantity,
                ]);
            }

            $transfer->update([
                'status' => 'completed',
                'transferred_at' => now(),
                'received_at' => now(),
            ]);

            return $transfer->fresh('items');
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Sales Return
    |--------------------------------------------------------------------------
    */

    public function completeSalesReturn(
        int $returnId,
        ?int $userId = null
    ): SalesReturn {
        return DB::transaction(function () use (
            $returnId,
            $userId
        ) {
            $return = SalesReturn::query()
                ->with([
                    'items.saleItem',
                ])
                ->lockForUpdate()
                ->findOrFail($returnId);

            if ($return->status === 'completed') {
                return $return;
            }

            if ($return->status === 'cancelled') {
                throw new DomainException(
                    'Cancelled sales return cannot be completed.'
                );
            }

            foreach ($return->items as $item) {
                if (!$item->restock) {
                    continue;
                }

                $this->stock->increase(
                    warehouseId:
                        $return->warehouse_id,

                    skuId:
                        $item->product_sku_id,

                    quantity:
                        (float) $item->quantity,

                    movementType:
                        'sales_return',

                    unitCost:
                        $item->saleItem?->unit_cost !== null
                            ? (float) $item->saleItem->unit_cost
                            : null,

                    reference:
                        $return,

                    userId:
                        $userId ?? $return->user_id,
                );
            }

            $return->update([
                'status' => 'completed',
            ]);

            return $return->fresh('items');
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Purchase Return
    |--------------------------------------------------------------------------
    */

    public function completePurchaseReturn(
        int $returnId,
        ?int $userId = null
    ): PurchaseReturn {
        return DB::transaction(function () use (
            $returnId,
            $userId
        ) {
            $return = PurchaseReturn::query()
                ->with('items')
                ->lockForUpdate()
                ->findOrFail($returnId);

            if ($return->status === 'completed') {
                return $return;
            }

            if ($return->status === 'cancelled') {
                throw new DomainException(
                    'Cancelled purchase return cannot be completed.'
                );
            }

            foreach ($return->items as $item) {
                $this->stock->decrease(
                    warehouseId:
                        $return->warehouse_id,

                    skuId:
                        $item->product_sku_id,

                    quantity:
                        (float) $item->quantity,

                    movementType:
                        'purchase_return',

                    unitCost:
                        (float) $item->unit_cost,

                    reference:
                        $return,

                    userId:
                        $userId ?? $return->user_id,
                );
            }

            $return->update([
                'status' => 'completed',
            ]);

            return $return->fresh('items');
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Inventory Count
    |--------------------------------------------------------------------------
    */

    public function completeInventoryCount(
        int $countId,
        ?int $userId = null
    ): InventoryCount {
        return DB::transaction(function () use (
            $countId,
            $userId
        ) {
            $count = InventoryCount::query()
                ->with('items')
                ->lockForUpdate()
                ->findOrFail($countId);

            if ($count->status === 'completed') {
                return $count;
            }

            if ($count->status === 'cancelled') {
                throw new DomainException(
                    'Cancelled inventory count cannot be completed.'
                );
            }

            foreach ($count->items as $item) {
                if ($item->counted_quantity === null) {
                    continue;
                }

                $systemQuantity =
                    $this->stock->currentQuantity(
                        $count->warehouse_id,
                        $item->product_sku_id
                    );

                $countedQuantity =
                    (float) $item->counted_quantity;

                $difference =
                    $countedQuantity - $systemQuantity;

                $this->stock->adjustTo(
                    warehouseId:
                        $count->warehouse_id,

                    skuId:
                        $item->product_sku_id,

                    targetQuantity:
                        $countedQuantity,

                    movementType:
                        'inventory_count',

                    reference:
                        $count,

                    userId:
                        $userId ?? $count->user_id,
                );

                $item->update([
                    'system_quantity' =>
                        $systemQuantity,

                    'difference_quantity' =>
                        $difference,
                ]);
            }

            $count->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            return $count->fresh('items');
        });
    }
}