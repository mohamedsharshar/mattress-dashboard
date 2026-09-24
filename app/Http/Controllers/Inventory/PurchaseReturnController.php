<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StorePurchaseReturnRequest;
use App\Models\PurchaseItem;
use App\Models\PurchaseReturn;
use App\Services\Inventory\InventoryWorkflowService;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseReturnController extends Controller
{
    public function store(
        StorePurchaseReturnRequest $request
    ): JsonResponse {
        $return = DB::transaction(function () use ($request) {
            $data = $request->validated();

            $return = PurchaseReturn::create([
                'return_number' =>
                    'PR-'
                    . now()->format('YmdHis')
                    . '-'
                    . strtoupper(Str::random(5)),

                'purchase_id' =>
                    $data['purchase_id'],

                'warehouse_id' =>
                    $data['warehouse_id'],

                'user_id' =>
                    $request->user()?->id,

                'status' =>
                    'draft',

                'return_date' =>
                    $data['return_date'],

                'total_amount' =>
                    0,

                'notes' =>
                    $data['notes'] ?? null,
            ]);

            $total = 0;

            foreach ($data['items'] as $item) {
                $purchaseItem =
                    PurchaseItem::findOrFail(
                        $item['purchase_item_id']
                    );

                if (
                    $purchaseItem->purchase_id
                    !==
                    (int) $data['purchase_id']
                ) {
                    throw new DomainException(
                        'Purchase item does not belong to selected purchase.'
                    );
                }

                $quantity =
                    (float) $item['quantity'];

                $unitCost =
                    (float) $purchaseItem->unit_cost;

                $lineTotal =
                    $quantity * $unitCost;

                $return->items()->create([
                    'purchase_item_id' =>
                        $purchaseItem->id,

                    'product_sku_id' =>
                        $purchaseItem->product_sku_id,

                    'quantity' =>
                        $quantity,

                    'unit_cost' =>
                        $unitCost,

                    'line_total' =>
                        $lineTotal,

                    'notes' =>
                        $item['notes'] ?? null,
                ]);

                $total += $lineTotal;
            }

            $return->update([
                'total_amount' =>
                    $total,
            ]);

            return $return;
        });

        return response()->json([
            'data' => $return->load('items'),
        ], 201);
    }

    public function show(
        PurchaseReturn $purchaseReturn
    ): JsonResponse {
        return response()->json([
            'data' => $purchaseReturn->load([
                'purchase',
                'warehouse',
                'items.sku',
                'payments',
            ]),
        ]);
    }

    public function complete(
        PurchaseReturn $purchaseReturn,
        InventoryWorkflowService $workflow
    ): JsonResponse {
        $return = $workflow->completePurchaseReturn(
            $purchaseReturn->id,
            request()->user()?->id
        );

        return response()->json([
            'data' => $return,
        ]);
    }
}