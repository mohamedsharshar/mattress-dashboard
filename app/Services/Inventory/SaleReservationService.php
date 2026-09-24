<?php

namespace App\Services\Inventory;

use App\Models\Sale;
use DomainException;
use Illuminate\Support\Facades\DB;

class SaleReservationService
{
    public function __construct(
        private StockService $stock
    ) {
    }

    public function reserve(
        int $saleId,
        ?int $userId = null
    ): Sale {
        return DB::transaction(function () use (
            $saleId
        ) {
            $sale = Sale::query()
                ->with('items')
                ->lockForUpdate()
                ->findOrFail($saleId);

            if ($sale->status === 'reserved') {
                return $sale;
            }

            if ($sale->status === 'completed') {
                throw new DomainException(
                    'Completed sale cannot be reserved.'
                );
            }

            if ($sale->status === 'cancelled') {
                throw new DomainException(
                    'Cancelled sale cannot be reserved.'
                );
            }

            if ($sale->items->isEmpty()) {
                throw new DomainException(
                    'Sale has no items.'
                );
            }

            foreach ($sale->items as $item) {
                $this->stock->reserve(
                    warehouseId:
                        $sale->warehouse_id,

                    skuId:
                        $item->product_sku_id,

                    quantity:
                        (float) $item->quantity,
                );
            }

            $sale->update([
                'status' => 'reserved',
            ]);

            return $sale->fresh('items');
        });
    }

    public function release(
        int $saleId
    ): Sale {
        return DB::transaction(function () use (
            $saleId
        ) {
            $sale = Sale::query()
                ->with('items')
                ->lockForUpdate()
                ->findOrFail($saleId);

            if ($sale->status === 'draft') {
                return $sale;
            }

            if ($sale->status !== 'reserved') {
                throw new DomainException(
                    'Only reserved sales can release reservations.'
                );
            }

            foreach ($sale->items as $item) {
                $this->stock->release(
                    warehouseId:
                        $sale->warehouse_id,

                    skuId:
                        $item->product_sku_id,

                    quantity:
                        (float) $item->quantity,
                );
            }

            $sale->update([
                'status' => 'draft',
            ]);

            return $sale->fresh('items');
        });
    }

    public function complete(
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

            if ($sale->status !== 'reserved') {
                throw new DomainException(
                    'Sale must be reserved before reserved completion.'
                );
            }

            foreach ($sale->items as $item) {
                $stock =
                    $this->stock
                        ->consumeReserved(
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
                                $userId
                                ?? $sale->user_id,
                        );

                if ($item->unit_cost === null) {
                    $item->update([
                        'unit_cost' =>
                            $stock->average_cost,
                    ]);
                }
            }

            $sale->update([
                'status' =>
                    'completed',

                'completed_at' =>
                    now(),
            ]);

            return $sale->fresh('items');
        });
    }
}