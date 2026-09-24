<?php

namespace App\Providers;

use App\Models\Customer;
use App\Models\InventoryCount;
use App\Models\InventoryCountItem;
use App\Models\Payment;
use App\Models\PriceListSkuPrice;
use App\Models\Product;
use App\Models\ProductSku;
use App\Models\ProductVariant;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalesReturn;
use App\Models\SalesReturnItem;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Observers\AuditObserver;
use Illuminate\Support\ServiceProvider;

class InventoryAuditServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $models = [
            Warehouse::class,

            Supplier::class,
            Customer::class,

            Product::class,
            ProductVariant::class,
            ProductSku::class,
            PriceListSkuPrice::class,

            Purchase::class,
            PurchaseItem::class,

            Sale::class,
            SaleItem::class,

            StockTransfer::class,
            StockTransferItem::class,

            InventoryCount::class,
            InventoryCountItem::class,

            SalesReturn::class,
            SalesReturnItem::class,

            PurchaseReturn::class,
            PurchaseReturnItem::class,

            Payment::class,
        ];

        foreach ($models as $model) {
            $model::observe(
                AuditObserver::class
            );
        }
    }
}