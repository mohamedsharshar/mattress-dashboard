<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class InventoryUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_inventory_ui_pages_are_available(): void
    {
        $admin =
            User::factory()->create();

        $admin->forceFill([
            'role' => 'admin',
        ])->save();

        $this->actingAs($admin);

        $routes = [
            'dashboard',

            'manage.catalog',
            'manage.stock',
            'manage.warehouses',

            'manage.purchases',
            'manage.sales',
            'manage.transfers',
            'manage.counts',

            'manage.suppliers',
            'manage.customers',

            'manage.movements',
        ];

        foreach ($routes as $route) {
            $this->assertTrue(
                Route::has($route),
                "Missing route: {$route}"
            );
        }

        foreach ($routes as $route) {
            $this->get(
                route($route)
            )->assertOk();
        }
    }
}