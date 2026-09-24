<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreSaleRequest;
use App\Models\Sale;
use App\Services\Inventory\InventoryWorkflowService;
use App\Services\Inventory\SaleReservationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SaleController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Sale::query()
                ->with([
                    'customer',
                    'warehouse',
                ])
                ->latest('sale_date')
                ->paginate(25),
        ]);
    }

    public function store(
        StoreSaleRequest $request
    ): JsonResponse {
        $sale = DB::transaction(function () use (
            $request
        ) {
            $data =
                $request->validated();

            $headerDiscount =
                (float) (
                    $data['discount_amount']
                    ?? 0
                );

            $headerTax =
                (float) (
                    $data['tax_amount']
                    ?? 0
                );

            $sale = Sale::create([
                'sale_number' =>
                    $this->number(),

                'customer_id' =>
                    $data['customer_id']
                    ?? null,

                'warehouse_id' =>
                    $data['warehouse_id'],

                'user_id' =>
                    $request->user()?->id,

                'status' =>
                    'draft',

                'sale_date' =>
                    $data['sale_date'],

                'discount_amount' =>
                    $headerDiscount,

                'tax_amount' =>
                    $headerTax,

                'notes' =>
                    $data['notes']
                    ?? null,
            ]);

            $subtotal = 0;
            $itemDiscounts = 0;
            $itemTaxes = 0;

            foreach (
                $data['items']
                as $item
            ) {
                $quantity =
                    (float) $item['quantity'];

                $unitPrice =
                    (float) $item['unit_price'];

                $discount =
                    (float) (
                        $item['discount_amount']
                        ?? 0
                    );

                $tax =
                    (float) (
                        $item['tax_amount']
                        ?? 0
                    );

                $base =
                    $quantity * $unitPrice;

                $lineTotal =
                    max(
                        0,
                        $base
                        - $discount
                        + $tax
                    );

                $sale->items()->create([
                    'product_sku_id' =>
                        $item['product_sku_id'],

                    'quantity' =>
                        $quantity,

                    'unit_price' =>
                        $unitPrice,

                    'discount_amount' =>
                        $discount,

                    'tax_amount' =>
                        $tax,

                    'line_total' =>
                        $lineTotal,

                    'notes' =>
                        $item['notes']
                        ?? null,
                ]);

                $subtotal += $base;
                $itemDiscounts += $discount;
                $itemTaxes += $tax;
            }

            $sale->update([
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

            return $sale;
        });

        return response()->json([
            'data' => $sale->load([
                'customer',
                'warehouse',
                'items.sku',
            ]),
        ], 201);
    }

    public function show(
        Sale $sale
    ): JsonResponse {
        return response()->json([
            'data' => $sale->load([
                'customer',
                'warehouse',
                'user',
                'items.sku.variant.product',
                'payments',
            ]),
        ]);
    }

    public function reserve(
        Sale $sale,
        SaleReservationService $reservations
    ): JsonResponse {
        $sale = $reservations->reserve(
            $sale->id,
            request()->user()?->id
        );

        return response()->json([
            'data' => $sale,
        ]);
    }

    public function releaseReservation(
        Sale $sale,
        SaleReservationService $reservations
    ): JsonResponse {
        $sale = $reservations->release(
            $sale->id
        );

        return response()->json([
            'data' => $sale,
        ]);
    }

    public function complete(
        Sale $sale,
        InventoryWorkflowService $workflow,
        SaleReservationService $reservations
    ): JsonResponse {
        if ($sale->status === 'reserved') {
            $sale = $reservations->complete(
                $sale->id,
                request()->user()?->id
            );
        } else {
            $sale = $workflow->completeSale(
                $sale->id,
                request()->user()?->id
            );
        }

        return response()->json([
            'data' => $sale,
        ]);
    }

    private function number(): string
    {
        return 'SAL-'
            . now()->format('YmdHis')
            . '-'
            . strtoupper(
                Str::random(5)
            );
    }
}