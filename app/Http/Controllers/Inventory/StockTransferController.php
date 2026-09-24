<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreStockTransferRequest;
use App\Models\StockTransfer;
use App\Services\Inventory\InventoryWorkflowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockTransferController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => StockTransfer::query()
                ->with([
                    'fromWarehouse',
                    'toWarehouse',
                ])
                ->latest()
                ->paginate(25),
        ]);
    }

    public function store(
        StoreStockTransferRequest $request
    ): JsonResponse {
        $transfer = DB::transaction(function () use ($request) {
            $data = $request->validated();

            $transfer = StockTransfer::create([
                'transfer_number' =>
                    $this->number(),

                'from_warehouse_id' =>
                    $data['from_warehouse_id'],

                'to_warehouse_id' =>
                    $data['to_warehouse_id'],

                'user_id' =>
                    $request->user()?->id,

                'status' =>
                    'draft',

                'notes' =>
                    $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                $transfer->items()->create([
                    'product_sku_id' =>
                        $item['product_sku_id'],

                    'quantity' =>
                        $item['quantity'],

                    'notes' =>
                        $item['notes'] ?? null,
                ]);
            }

            return $transfer;
        });

        return response()->json([
            'data' => $transfer->load([
                'fromWarehouse',
                'toWarehouse',
                'items.sku',
            ]),
        ], 201);
    }

    public function show(
        StockTransfer $transfer
    ): JsonResponse {
        return response()->json([
            'data' => $transfer->load([
                'fromWarehouse',
                'toWarehouse',
                'user',
                'items.sku.variant.product',
            ]),
        ]);
    }

    public function complete(
        StockTransfer $transfer,
        InventoryWorkflowService $workflow
    ): JsonResponse {
        $transfer = $workflow->completeTransfer(
            $transfer->id,
            request()->user()?->id
        );

        return response()->json([
            'data' => $transfer,
        ]);
    }

    private function number(): string
    {
        return 'TRF-'
            . now()->format('YmdHis')
            . '-'
            . strtoupper(Str::random(5));
    }
}