<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [\App\Http\Controllers\Inventory\DashboardController::class, 'index'])
        ->middleware(\App\Http\Middleware\EnsureInventoryPermission::class . ':catalog.view')
        ->name('dashboard');
});

require __DIR__.'/settings.php';

require __DIR__.'/inventory.php';

require __DIR__.'/inventory-ui.php';
