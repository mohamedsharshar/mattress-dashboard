<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreSalesReturnRequest;
use App\Models\SaleItem;
use App\Models\SalesReturn;
use App\Services\Inventory\InventoryWorkflowService;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SalesReturnController extends Controller
{
    public function store(
        StoreSalesReturnRequest $request
    ): JsonResponse {
        $return = DB::transaction(function () use ($request) {
            $data = $request->validated();

            $return = SalesReturn::create([
                'return_number' =>
                    'SR-'
                    . now()->format('YmdHis')
                    . '-'
                    . strtoupper(Str::random(5)),

                'sale_id' =>
                    $data['sale_id'],

                'warehouse_id' =>
                    $data['warehouse_id'],

                'user_id' =>
                    $request->user()?->id,

                'status' =>
                    'draft',

                'return_date' =>
                    $data['return_date'],

                'refund_total' =>
                    0,

                'notes' =>
                    $data['notes'] ?? null,
            ]);

            $total = 0;

            foreach ($data['items'] as $item) {
                $saleItem = SaleItem::findOrFail(
                    $item['sale_item_id']
                );

                if (
                    $saleItem->sale_id
                    !==
                    (int) $data['sale_id']
                ) {
                    throw new DomainException(
                        'Sale item does not belong to selected sale.'
                    );
                }

                $quantity =
                    (float) $item['quantity'];

                $unitPrice =
                    (float) $saleItem->unit_price;

                $lineTotal =
                    $quantity * $unitPrice;

                $return->items()->create([
                    'sale_item_id' =>
                        $saleItem->id,

                    'product_sku_id' =>
                        $saleItem->product_sku_id,

                    'quantity' =>
                        $quantity,

                    'unit_price' =>
                        $unitPrice,

                    'condition' =>
                        $item['condition']
                        ?? 'resellable',

                    'restock' =>
                        $item['restock']
                        ?? true,

                    'line_total' =>
                        $lineTotal,

                    'notes' =>
                        $item['notes'] ?? null,
                ]);

                $total += $lineTotal;
            }

            $return->update([
                'refund_total' =>
                    $total,
            ]);

            return $return;
        });

        return response()->json([
            'data' => $return->load('items'),
        ], 201);
    }

    public function show(
        SalesReturn $salesReturn
    ): JsonResponse {
        return response()->json([
            'data' => $salesReturn->load([
                'sale',
                'warehouse',
                'items.sku',
                'payments',
            ]),
        ]);
    }

    public function complete(
        SalesReturn $salesReturn,
        InventoryWorkflowService $workflow
    ): JsonResponse {
        $return = $workflow->completeSalesReturn(
            $salesReturn->id,
            request()->user()?->id
        );

        return response()->json([
            'data' => $return,
        ]);
    }
}