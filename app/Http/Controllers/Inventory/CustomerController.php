<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Customer::query()
            ->orderBy('name');

        if ($request->filled('search')) {
            $search = $request->string('search');

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        return response()->json([
            'data' => $query->paginate(25),
        ]);
    }

    public function store(
        StoreCustomerRequest $request
    ): JsonResponse {
        $customer = Customer::create([
            ...$request->validated(),
            'is_active' => $request->boolean(
                'is_active',
                true
            ),
        ]);

        return response()->json([
            'data' => $customer,
        ], 201);
    }

    public function update(
        StoreCustomerRequest $request,
        Customer $customer
    ): JsonResponse {
        $customer->update(
            $request->validated()
        );

        return response()->json([
            'data' => $customer->fresh(),
        ]);
    }
}