<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductSku;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'products' =>
                Product::where(
                    'is_active',
                    true
                )->count(),

            'skus' =>
                ProductSku::where(
                    'is_active',
                    true
                )->count(),

            'warehouses' =>
                Warehouse::where(
                    'is_active',
                    true
                )->count(),

            'stock_quantity' =>
                (float) WarehouseStock::sum(
                    'quantity'
                ),

            'reserved_quantity' =>
                (float) WarehouseStock::sum(
                    'reserved_quantity'
                ),

            'inventory_value' =>
                (float) (
                    DB::table(
                        'warehouse_stocks'
                    )
                        ->selectRaw(
                            'COALESCE(SUM(quantity * average_cost), 0) as total'
                        )
                        ->value('total')
                    ?? 0
                ),

            'today_sales' =>
                (float) Sale::query()
                    ->whereDate(
                        'sale_date',
                        today()
                    )
                    ->where(
                        'status',
                        'completed'
                    )
                    ->sum('total_amount'),

            'today_purchases' =>
                (float) Purchase::query()
                    ->whereDate(
                        'purchase_date',
                        today()
                    )
                    ->where(
                        'status',
                        'received'
                    )
                    ->sum('total_amount'),
        ];

        $lowStock = WarehouseStock::query()
            ->with([
                'warehouse',
                'sku.variant.product',
            ])
            ->where(
                'minimum_stock',
                '>',
                0
            )
            ->whereRaw(
                '(quantity - reserved_quantity) <= minimum_stock'
            )
            ->orderByRaw(
                '(minimum_stock - (quantity - reserved_quantity)) DESC'
            )
            ->limit(8)
            ->get();

        $recentMovements =
            StockMovement::query()
                ->with([
                    'warehouse',
                    'sku.variant.product',
                ])
                ->latest(
                    'occurred_at'
                )
                ->limit(10)
                ->get();

        $recentSales =
            Sale::query()
                ->with([
                    'customer',
                    'warehouse',
                ])
                ->latest(
                    'created_at'
                )
                ->limit(6)
                ->get();

        return view(
            'dashboard',
            compact(
                'stats',
                'lowStock',
                'recentMovements',
                'recentSales',
            )
        );
    }
}