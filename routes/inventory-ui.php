<?php

use App\Http\Controllers\Inventory\InventoryUiController;
use App\Http\Middleware\EnsureInventoryPermission;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth',
    'verified',
])
    ->prefix('manage')
    ->name('manage.')
    ->group(function () {

        Route::get(
            'catalog',
            [
                InventoryUiController::class,
                'catalog',
            ]
        )
            ->middleware(
                EnsureInventoryPermission::class
                . ':catalog.view'
            )
            ->name('catalog');

        Route::get(
            'stock',
            [
                InventoryUiController::class,
                'stock',
            ]
        )
            ->middleware(
                EnsureInventoryPermission::class
                . ':warehouses.view'
            )
            ->name('stock');

        Route::get(
            'warehouses',
            [
                InventoryUiController::class,
                'warehouses',
            ]
        )
            ->middleware(
                EnsureInventoryPermission::class
                . ':warehouses.view'
            )
            ->name('warehouses');

        Route::get(
            'purchases',
            [
                InventoryUiController::class,
                'purchases',
            ]
        )
            ->middleware(
                EnsureInventoryPermission::class
                . ':purchases.view'
            )
            ->name('purchases');

        Route::get(
            'sales',
            [
                InventoryUiController::class,
                'sales',
            ]
        )
            ->middleware(
                EnsureInventoryPermission::class
                . ':sales.view'
            )
            ->name('sales');

        Route::get(
            'transfers',
            [
                InventoryUiController::class,
                'transfers',
            ]
        )
            ->middleware(
                EnsureInventoryPermission::class
                . ':transfers.view'
            )
            ->name('transfers');

        Route::get(
            'counts',
            [
                InventoryUiController::class,
                'counts',
            ]
        )
            ->middleware(
                EnsureInventoryPermission::class
                . ':counts.view'
            )
            ->name('counts');

        Route::get(
            'suppliers',
            [
                InventoryUiController::class,
                'suppliers',
            ]
        )
            ->middleware(
                EnsureInventoryPermission::class
                . ':suppliers.view'
            )
            ->name('suppliers');

        Route::get(
            'customers',
            [
                InventoryUiController::class,
                'customers',
            ]
        )
            ->middleware(
                EnsureInventoryPermission::class
                . ':customers.view'
            )
            ->name('customers');

        Route::get(
            'movements',
            [
                InventoryUiController::class,
                'movements',
            ]
        )
            ->middleware(
                EnsureInventoryPermission::class
                . ':warehouses.view'
            )
            ->name('movements');
    });