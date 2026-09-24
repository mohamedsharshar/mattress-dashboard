<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSku;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\WarehouseStock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class InventoryHttpTest extends TestCase
{
    use RefreshDatabase;

    public function test_inventory_http_layer(): void
    {
        $user = User::factory()->create();

        $user->forceFill([
            'role' => 'admin',
        ])->save();

        $this->actingAs($user);

        /*
        |--------------------------------------------------------------------------
        | Routes
        |--------------------------------------------------------------------------
        */

        $routes = [
            'inventory.warehouses.index',
            'inventory.warehouses.store',
            'inventory.warehouses.update',

            'inventory.suppliers.index',
            'inventory.suppliers.store',
            'inventory.suppliers.update',

            'inventory.customers.index',
            'inventory.customers.store',
            'inventory.customers.update',

            'inventory.catalog.index',
            'inventory.catalog.show',

            'inventory.purchases.index',
            'inventory.purchases.store',
            'inventory.purchases.show',
            'inventory.purchases.receive',

            'inventory.sales.index',
            'inventory.sales.store',
            'inventory.sales.show',
            'inventory.sales.complete',

            'inventory.transfers.index',
            'inventory.transfers.store',
            'inventory.transfers.show',
            'inventory.transfers.complete',

            'inventory.counts.index',
            'inventory.counts.store',
            'inventory.counts.show',
            'inventory.counts.complete',

            'inventory.sales-returns.store',
            'inventory.sales-returns.show',
            'inventory.sales-returns.complete',

            'inventory.purchase-returns.store',
            'inventory.purchase-returns.show',
            'inventory.purchase-returns.complete',

            'inventory.payments.store',
        ];

        foreach ($routes as $route) {
            $this->assertTrue(
                Route::has($route),
                "Missing route: {$route}"
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $this->postJson(
            route('inventory.warehouses.store'),
            []
        )
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
                'code',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Catalog
        |--------------------------------------------------------------------------
        */

        $brand = Brand::create([
            'name' => 'Test Brand',
            'name_en' => 'Test Brand',
            'is_active' => true,
        ]);

        $category = Category::create([
            'name' => 'مراتب',
            'slug' => 'test-mattresses',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $product = Product::create([
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'name' => 'Medical',
            'unit' => 'piece',
            'is_active' => true,
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => '30 CM 2S',
            'thickness_cm' => 30,
            'specification' => '2S',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $sku = ProductSku::create([
            'product_variant_id' => $variant->id,
            'sku' => 'TEST-SKU-160X200',
            'width_cm' => 160,
            'length_cm' => 200,
            'size_label' => '160*200',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Warehouse
        |--------------------------------------------------------------------------
        */

        $warehouseResponse = $this->postJson(
            route('inventory.warehouses.store'),
            [
                'name' => 'Main Warehouse',
                'code' => 'MAIN',
                'is_default' => true,
            ]
        )
            ->assertCreated()
            ->assertJsonPath(
                'data.code',
                'MAIN'
            );

        $warehouseId =
            $warehouseResponse
                ->json('data.id');

        /*
        |--------------------------------------------------------------------------
        | Supplier
        |--------------------------------------------------------------------------
        */

        $supplierResponse = $this->postJson(
            route('inventory.suppliers.store'),
            [
                'code' => 'SUP-001',
                'name' => 'Supplier One',
            ]
        )
            ->assertCreated();

        $supplierId =
            $supplierResponse
                ->json('data.id');

        /*
        |--------------------------------------------------------------------------
        | Customer
        |--------------------------------------------------------------------------
        */

        $customerResponse = $this->postJson(
            route('inventory.customers.store'),
            [
                'code' => 'CUS-001',
                'name' => 'Customer One',
            ]
        )
            ->assertCreated();

        $customerId =
            $customerResponse
                ->json('data.id');

        /*
        |--------------------------------------------------------------------------
        | Purchase
        |--------------------------------------------------------------------------
        */

        $purchaseResponse = $this->postJson(
            route('inventory.purchases.store'),
            [
                'supplier_id' =>
                    $supplierId,

                'warehouse_id' =>
                    $warehouseId,

                'purchase_date' =>
                    now()->toDateString(),

                'items' => [
                    [
                        'product_sku_id' =>
                            $sku->id,

                        'quantity' =>
                            10,

                        'unit_cost' =>
                            100,
                    ],
                ],
            ]
        )
            ->assertCreated()
            ->assertJsonPath(
                'data.total_amount',
                '1000.00'
            );

        $purchaseId =
            $purchaseResponse
                ->json('data.id');

        $this->postJson(
            route(
                'inventory.purchases.receive',
                $purchaseId
            )
        )
            ->assertOk()
            ->assertJsonPath(
                'data.status',
                'received'
            );

        $this->assertSame(
            10.0,
            (float) WarehouseStock::where([
                'warehouse_id' =>
                    $warehouseId,

                'product_sku_id' =>
                    $sku->id,
            ])->value('quantity')
        );

        /*
        |--------------------------------------------------------------------------
        | Sale
        |--------------------------------------------------------------------------
        */

        $saleResponse = $this->postJson(
            route('inventory.sales.store'),
            [
                'customer_id' =>
                    $customerId,

                'warehouse_id' =>
                    $warehouseId,

                'sale_date' =>
                    now()->toDateString(),

                'items' => [
                    [
                        'product_sku_id' =>
                            $sku->id,

                        'quantity' =>
                            3,

                        'unit_price' =>
                            150,
                    ],
                ],
            ]
        )
            ->assertCreated()
            ->assertJsonPath(
                'data.total_amount',
                '450.00'
            );

        $saleId =
            $saleResponse
                ->json('data.id');

        $this->postJson(
            route(
                'inventory.sales.complete',
                $saleId
            )
        )
            ->assertOk()
            ->assertJsonPath(
                'data.status',
                'completed'
            );

        $this->assertSame(
            7.0,
            (float) WarehouseStock::where([
                'warehouse_id' =>
                    $warehouseId,

                'product_sku_id' =>
                    $sku->id,
            ])->value('quantity')
        );

        /*
        |--------------------------------------------------------------------------
        | Payment
        |--------------------------------------------------------------------------
        */

        $this->postJson(
            route('inventory.payments.store'),
            [
                'payable_type' =>
                    'sale',

                'payable_id' =>
                    $saleId,

                'direction' =>
                    'in',

                'method' =>
                    'cash',

                'amount' =>
                    200,
            ]
        )
            ->assertCreated()
            ->assertJsonPath(
                'data.amount',
                '200.00'
            );

        /*
        |--------------------------------------------------------------------------
        | Catalog endpoint
        |--------------------------------------------------------------------------
        */

        $this->getJson(
            route('inventory.catalog.show', $product)
        )
            ->assertOk()
            ->assertJsonPath(
                'data.name',
                'Medical'
            );
    }
}