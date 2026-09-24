<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::query()
            ->with([
                'brand',
                'category',
                'variants.skus',
            ])
            ->where('is_active', true);

        if ($request->filled('brand_id')) {
            $query->where(
                'brand_id',
                $request->integer('brand_id')
            );
        }

        if ($request->filled('category_id')) {
            $query->where(
                'category_id',
                $request->integer('category_id')
            );
        }

        if ($request->filled('search')) {
            $search = $request->string('search');

            $query->where(function ($q) use ($search) {
                $q->where(
                    'name',
                    'like',
                    "%{$search}%"
                )
                ->orWhereHas(
                    'variants.skus',
                    function ($skuQuery) use ($search) {
                        $skuQuery->where(
                            'sku',
                            'like',
                            "%{$search}%"
                        );
                    }
                );
            });
        }

        return response()->json([
            'data' => $query
                ->orderBy('name')
                ->paginate(25),
        ]);
    }

    public function show(Product $product): JsonResponse
    {
        $product->load([
            'brand',
            'category',
            'variants.skus.warehouseStocks.warehouse',
            'variants.skus.priceListPrices.priceList',
        ]);

        return response()->json([
            'data' => $product,
        ]);
    }
}