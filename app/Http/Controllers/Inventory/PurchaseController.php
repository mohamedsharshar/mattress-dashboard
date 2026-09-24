<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StorePurchaseRequest;
use App\Models\Purchase;
use App\Services\Inventory\InventoryWorkflowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Purchase::query()
                ->with([
                    'supplier',
                    'warehouse',
                ])
                ->latest('purchase_date')
                ->paginate(25),
        ]);
    }

    public function store(
        StorePurchaseRequest $request
    ): JsonResponse {
        $purchase = DB::transaction(function () use ($request) {
            $data = $request->validated();

            $headerDiscount =
                (float) ($data['discount_amount'] ?? 0);

            $headerTax =
                (float) ($data['tax_amount'] ?? 0);

            $purchase = Purchase::create([
                'purchase_number' =>
                    $this->number('PUR'),

                'supplier_id' =>
                    $data['supplier_id'],

                'warehouse_id' =>
                    $data['warehouse_id'],

                'user_id' =>
                    $request->user()?->id,

                'invoice_number' =>
                    $data['invoice_number'] ?? null,

                'status' =>
                    'draft',

                'purchase_date' =>
                    $data['purchase_date'],

                'discount_amount' =>
                    $headerDiscount,

                'tax_amount' =>
                    $headerTax,

                'notes' =>
                    $data['notes'] ?? null,
            ]);

            $subtotal = 0;
            $itemDiscounts = 0;
            $itemTaxes = 0;

            foreach ($data['items'] as $item) {
                $quantity =
                    (float) $item['quantity'];

                $unitCost =
                    (float) $item['unit_cost'];

                $discount =
                    (float) ($item['discount_amount'] ?? 0);

                $tax =
                    (float) ($item['tax_amount'] ?? 0);

                $base =
                    $quantity * $unitCost;

                $lineTotal =
                    max(
                        0,
                        $base - $discount + $tax
                    );

                $purchase->items()->create([
                    'product_sku_id' =>
                        $item['product_sku_id'],

                    'quantity' =>
                        $quantity,

                    'unit_cost' =>
                        $unitCost,

                    'discount_amount' =>
                        $discount,

                    'tax_amount' =>
                        $tax,

                    'line_total' =>
                        $lineTotal,

                    'notes' =>
                        $item['notes'] ?? null,
                ]);

                $subtotal += $base;
                $itemDiscounts += $discount;
                $itemTaxes += $tax;
            }

            $purchase->update([
                'subtotal' =>
                    $subtotal,

                'discount_amount' =>
                    $headerDiscount
                    + $itemDiscounts,

                'tax_amount' =>
                    $headerTax
                    + $itemTaxes,

                'total_amount' =>
                    max(
                        0,
                        $subtotal
                        - $headerDiscount
                        - $itemDiscounts
                        + $headerTax
                        + $itemTaxes
                    ),
            ]);

            return $purchase;
        });

        return response()->json([
            'data' => $purchase->load([
                'supplier',
                'warehouse',
                'items.sku',
            ]),
        ], 201);
    }

    public function show(
        Purchase $purchase
    ): JsonResponse {
        return response()->json([
            'data' => $purchase->load([
                'supplier',
                'warehouse',
                'user',
                'items.sku.variant.product',
                'payments',
            ]),
        ]);
    }

    public function receive(
        Purchase $purchase,
        InventoryWorkflowService $workflow
    ): JsonResponse {
        $purchase = $workflow->receivePurchase(
            $purchase->id,
            request()->user()?->id
        );

        return response()->json([
            'data' => $purchase,
        ]);
    }

    private function number(
        string $prefix
    ): string {
        return $prefix
            . '-'
            . now()->format('YmdHis')
            . '-'
            . strtoupper(Str::random(5));
    }
}