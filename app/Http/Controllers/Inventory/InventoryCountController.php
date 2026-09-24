<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreInventoryCountRequest;
use App\Models\InventoryCount;
use App\Models\WarehouseStock;
use App\Services\Inventory\InventoryWorkflowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InventoryCountController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => InventoryCount::query()
                ->with('warehouse')
                ->latest()
                ->paginate(25),
        ]);
    }

    public function store(
        StoreInventoryCountRequest $request
    ): JsonResponse {
        $count = DB::transaction(function () use ($request) {
            $data = $request->validated();

            $count = InventoryCount::create([
                'count_number' =>
                    'CNT-'
                    . now()->format('YmdHis')
                    . '-'
                    . strtoupper(Str::random(5)),

                'warehouse_id' =>
                    $data['warehouse_id'],

                'user_id' =>
                    $request->user()?->id,

                'status' =>
                    'draft',

                'started_at' =>
                    now(),

                'notes' =>
                    $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                $systemQuantity =
                    (float) WarehouseStock::query()
                        ->where(
                            'warehouse_id',
                            $data['warehouse_id']
                        )
                        ->where(
                            'product_sku_id',
                            $item['product_sku_id']
                        )
                        ->value('quantity');

                $countedQuantity =
                    (float) $item['counted_quantity'];

                $count->items()->create([
                    'product_sku_id' =>
                        $item['product_sku_id'],

                    'system_quantity' =>
                        $systemQuantity,

                    'counted_quantity' =>
                        $countedQuantity,

                    'difference_quantity' =>
                        $countedQuantity
                        - $systemQuantity,

                    'notes' =>
                        $item['notes'] ?? null,
                ]);
            }

            return $count;
        });

        return response()->json([
            'data' => $count->load([
                'warehouse',
                'items.sku',
            ]),
        ], 201);
    }

    public function show(
        InventoryCount $count
    ): JsonResponse {
        return response()->json([
            'data' => $count->load([
                'warehouse',
                'user',
                'items.sku.variant.product',
            ]),
        ]);
    }

    public function complete(
        InventoryCount $count,
        InventoryWorkflowService $workflow
    ): JsonResponse {
        $count = $workflow->completeInventoryCount(
            $count->id,
            request()->user()?->id
        );

        return response()->json([
            'data' => $count,
        ]);
    }
}