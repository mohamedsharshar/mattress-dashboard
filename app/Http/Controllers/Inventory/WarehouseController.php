<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreWarehouseRequest;
use App\Models\Warehouse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Warehouse::query()
            ->withCount('stocks')
            ->orderByDesc('is_default')
            ->orderBy('name');

        if ($request->filled('search')) {
            $search = $request->string('search');

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        return response()->json([
            'data' => $query->paginate(25),
        ]);
    }

    public function store(
        StoreWarehouseRequest $request
    ): JsonResponse {
        $warehouse = DB::transaction(function () use ($request) {
            $data = $request->validated();

            if ($data['is_default'] ?? false) {
                Warehouse::query()->update([
                    'is_default' => false,
                ]);
            }

            return Warehouse::create([
                ...$data,
                'is_default' => $data['is_default'] ?? false,
                'is_active' => $data['is_active'] ?? true,
            ]);
        });

        return response()->json([
            'data' => $warehouse,
        ], 201);
    }

    public function update(
        StoreWarehouseRequest $request,
        Warehouse $warehouse
    ): JsonResponse {
        DB::transaction(function () use (
            $request,
            $warehouse
        ) {
            $data = $request->validated();

            if ($data['is_default'] ?? false) {
                Warehouse::query()
                    ->whereKeyNot($warehouse->id)
                    ->update([
                        'is_default' => false,
                    ]);
            }

            $warehouse->update($data);
        });

        return response()->json([
            'data' => $warehouse->fresh(),
        ]);
    }
}