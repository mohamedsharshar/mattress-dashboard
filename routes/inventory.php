<?php

use App\Http\Controllers\Inventory\CatalogController;
use App\Http\Controllers\Inventory\CustomerController;
use App\Http\Controllers\Inventory\InventoryCountController;
use App\Http\Controllers\Inventory\PaymentController;
use App\Http\Controllers\Inventory\PurchaseController;
use App\Http\Controllers\Inventory\PurchaseReturnController;
use App\Http\Controllers\Inventory\SaleController;
use App\Http\Controllers\Inventory\SalesReturnController;
use App\Http\Controllers\Inventory\StockTransferController;
use App\Http\Controllers\Inventory\SupplierController;
use App\Http\Controllers\Inventory\WarehouseController;
use App\Http\Middleware\EnsureInventoryPermission;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth',
    EnsureInventoryPermission::class,
])
    ->prefix('inventory')
    ->name('inventory.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Warehouses
        |--------------------------------------------------------------------------
        */

        Route::get(
            'warehouses',
            [WarehouseController::class, 'index']
        )->name('warehouses.index');

        Route::post(
            'warehouses',
            [WarehouseController::class, 'store']
        )->name('warehouses.store');

        Route::put(
            'warehouses/{warehouse}',
            [WarehouseController::class, 'update']
        )->name('warehouses.update');

        /*
        |--------------------------------------------------------------------------
        | Suppliers
        |--------------------------------------------------------------------------
        */

        Route::get(
            'suppliers',
            [SupplierController::class, 'index']
        )->name('suppliers.index');

        Route::post(
            'suppliers',
            [SupplierController::class, 'store']
        )->name('suppliers.store');

        Route::put(
            'suppliers/{supplier}',
            [SupplierController::class, 'update']
        )->name('suppliers.update');

        /*
        |--------------------------------------------------------------------------
        | Customers
        |--------------------------------------------------------------------------
        */

        Route::get(
            'customers',
            [CustomerController::class, 'index']
        )->name('customers.index');

        Route::post(
            'customers',
            [CustomerController::class, 'store']
        )->name('customers.store');

        Route::put(
            'customers/{customer}',
            [CustomerController::class, 'update']
        )->name('customers.update');

        /*
        |--------------------------------------------------------------------------
        | Catalog
        |--------------------------------------------------------------------------
        */

        Route::get(
            'catalog',
            [CatalogController::class, 'index']
        )->name('catalog.index');

        Route::get(
            'catalog/{product}',
            [CatalogController::class, 'show']
        )->name('catalog.show');

        /*
        |--------------------------------------------------------------------------
        | Purchases
        |--------------------------------------------------------------------------
        */

        Route::get(
            'purchases',
            [PurchaseController::class, 'index']
        )->name('purchases.index');

        Route::post(
            'purchases',
            [PurchaseController::class, 'store']
        )->name('purchases.store');

        Route::get(
            'purchases/{purchase}',
            [PurchaseController::class, 'show']
        )->name('purchases.show');

        Route::post(
            'purchases/{purchase}/receive',
            [PurchaseController::class, 'receive']
        )->name('purchases.receive');

        /*
        |--------------------------------------------------------------------------
        | Sales
        |--------------------------------------------------------------------------
        */

        Route::get(
            'sales',
            [SaleController::class, 'index']
        )->name('sales.index');

        Route::post(
            'sales',
            [SaleController::class, 'store']
        )->name('sales.store');

        Route::get(
            'sales/{sale}',
            [SaleController::class, 'show']
        )->name('sales.show');

        Route::post(
            'sales/{sale}/reserve',
            [SaleController::class, 'reserve']
        )->name('sales.reserve');

        Route::post(
            'sales/{sale}/release-reservation',
            [
                SaleController::class,
                'releaseReservation',
            ]
        )->name('sales.release-reservation');

        Route::post(
            'sales/{sale}/complete',
            [SaleController::class, 'complete']
        )->name('sales.complete');

        /*
        |--------------------------------------------------------------------------
        | Transfers
        |--------------------------------------------------------------------------
        */

        Route::get(
            'transfers',
            [StockTransferController::class, 'index']
        )->name('transfers.index');

        Route::post(
            'transfers',
            [StockTransferController::class, 'store']
        )->name('transfers.store');

        Route::get(
            'transfers/{transfer}',
            [StockTransferController::class, 'show']
        )->name('transfers.show');

        Route::post(
            'transfers/{transfer}/complete',
            [StockTransferController::class, 'complete']
        )->name('transfers.complete');

        /*
        |--------------------------------------------------------------------------
        | Inventory Counts
        |--------------------------------------------------------------------------
        */

        Route::get(
            'counts',
            [InventoryCountController::class, 'index']
        )->name('counts.index');

        Route::post(
            'counts',
            [InventoryCountController::class, 'store']
        )->name('counts.store');

        Route::get(
            'counts/{count}',
            [InventoryCountController::class, 'show']
        )->name('counts.show');

        Route::post(
            'counts/{count}/complete',
            [InventoryCountController::class, 'complete']
        )->name('counts.complete');

        /*
        |--------------------------------------------------------------------------
        | Sales Returns
        |--------------------------------------------------------------------------
        */

        Route::post(
            'sales-returns',
            [SalesReturnController::class, 'store']
        )->name('sales-returns.store');

        Route::get(
            'sales-returns/{salesReturn}',
            [SalesReturnController::class, 'show']
        )->name('sales-returns.show');

        Route::post(
            'sales-returns/{salesReturn}/complete',
            [SalesReturnController::class, 'complete']
        )->name('sales-returns.complete');

        /*
        |--------------------------------------------------------------------------
        | Purchase Returns
        |--------------------------------------------------------------------------
        */

        Route::post(
            'purchase-returns',
            [PurchaseReturnController::class, 'store']
        )->name('purchase-returns.store');

        Route::get(
            'purchase-returns/{purchaseReturn}',
            [PurchaseReturnController::class, 'show']
        )->name('purchase-returns.show');

        Route::post(
            'purchase-returns/{purchaseReturn}/complete',
            [PurchaseReturnController::class, 'complete']
        )->name('purchase-returns.complete');

        /*
        |--------------------------------------------------------------------------
        | Payments
        |--------------------------------------------------------------------------
        */

        Route::post(
            'payments',
            [PaymentController::class, 'store']
        )->name('payments.store');
    });