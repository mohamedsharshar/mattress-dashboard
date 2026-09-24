<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreSupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Supplier::query()
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
        StoreSupplierRequest $request
    ): JsonResponse {
        $supplier = Supplier::create([
            ...$request->validated(),
            'is_active' => $request->boolean(
                'is_active',
                true
            ),
        ]);

        return response()->json([
            'data' => $supplier,
        ], 201);
    }

    public function update(
        StoreSupplierRequest $request,
        Supplier $supplier
    ): JsonResponse {
        $supplier->update(
            $request->validated()
        );

        return response()->json([
            'data' => $supplier->fresh(),
        ]);
    }
}