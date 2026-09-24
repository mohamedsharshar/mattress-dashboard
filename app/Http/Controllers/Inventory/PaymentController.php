<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StorePaymentRequest;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\Sale;
use App\Models\SalesReturn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    public function store(
        StorePaymentRequest $request
    ): JsonResponse {
        $data = $request->validated();

        $payable = $this->resolvePayable(
            $data['payable_type'],
            (int) $data['payable_id']
        );

        $payment = $payable
            ->payments()
            ->create([
                'direction' =>
                    $data['direction'],

                'method' =>
                    $data['method'],

                'amount' =>
                    $data['amount'],

                'reference_number' =>
                    $data['reference_number']
                    ?? null,

                'paid_at' =>
                    $data['paid_at'] ?? now(),

                'user_id' =>
                    $request->user()?->id,

                'notes' =>
                    $data['notes'] ?? null,
            ]);

        if (
            $payable instanceof Sale ||
            $payable instanceof Purchase
        ) {
            $paid = (float) $payable
                ->payments()
                ->sum('amount');

            $payable->update([
                'paid_amount' => $paid,
            ]);
        }

        return response()->json([
            'data' => $payment,
        ], 201);
    }

    private function resolvePayable(
        string $type,
        int $id
    ): Model {
        $class = match ($type) {
            'sale' =>
                Sale::class,

            'purchase' =>
                Purchase::class,

            'sales_return' =>
                SalesReturn::class,

            'purchase_return' =>
                PurchaseReturn::class,
        };

        return $class::findOrFail($id);
    }
}