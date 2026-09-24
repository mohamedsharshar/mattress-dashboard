<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSku;
use App\Models\ProductVariant;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventorySecurityAuditReservationTest extends TestCase
{
    use RefreshDatabase;

    public function test_permissions_audit_and_reservations(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Viewer Permissions
        |--------------------------------------------------------------------------
        */

        $viewer =
            User::factory()->create();

        $viewer->forceFill([
            'role' => 'viewer',
        ])->save();

        $this->actingAs($viewer);

        $this->getJson(
            route('inventory.catalog.index')
        )
            ->assertOk();

        $this->postJson(
            route('inventory.warehouses.store'),
            [
                'name' => 'Blocked Warehouse',
                'code' => 'BLOCKED',
            ]
        )
            ->assertForbidden();

        /*
        |--------------------------------------------------------------------------
        | Admin
        |--------------------------------------------------------------------------
        */

        $admin =
            User::factory()->create();

        $admin->forceFill([
            'role' => 'admin',
        ])->save();

        $this->actingAs($admin);

        /*
        |--------------------------------------------------------------------------
        | Automatic Audit
        |--------------------------------------------------------------------------
        */

        $warehouseResponse =
            $this->postJson(
                route(
                    'inventory.warehouses.store'
                ),
                [
                    'name' =>
                        'Main Warehouse',

                    'code' =>
                        'MAIN',

                    'is_default' =>
                        true,
                ]
            )
            ->assertCreated();

        $warehouseId =
            $warehouseResponse
                ->json('data.id');

        $this->assertDatabaseHas(
            'audit_logs',
            [
                'user_id' =>
                    $admin->id,

                'event' =>
                    'created',

                'auditable_type' =>
                    Warehouse::class,

                'auditable_id' =>
                    $warehouseId,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Catalog Setup
        |--------------------------------------------------------------------------
        */

        $brand = Brand::create([
            'name' =>
                'Test Brand',

            'name_en' =>
                'Test Brand',

            'is_active' =>
                true,
        ]);

        $category = Category::create([
            'name' =>
                'مراتب',

            'slug' =>
                'security-test-mattresses',

            'sort_order' =>
                1,

            'is_active' =>
                true,
        ]);

        $product = Product::create([
            'brand_id' =>
                $brand->id,

            'category_id' =>
                $category->id,

            'name' =>
                'Medical',

            'unit' =>
                'piece',

            'is_active' =>
                true,
        ]);

        $variant =
            ProductVariant::create([
                'product_id' =>
                    $product->id,

                'name' =>
                    '30 CM 2S',

                'thickness_cm' =>
                    30,

                'specification' =>
                    '2S',

                'sort_order' =>
                    1,

                'is_active' =>
                    true,
            ]);

        $sku = ProductSku::create([
            'product_variant_id' =>
                $variant->id,

            'sku' =>
                'SEC-TEST-160X200',

            'width_cm' =>
                160,

            'length_cm' =>
                200,

            'size_label' =>
                '160*200',

            'sort_order' =>
                1,

            'is_active' =>
                true,
        ]);

        WarehouseStock::create([
            'warehouse_id' =>
                $warehouseId,

            'product_sku_id' =>
                $sku->id,

            'quantity' =>
                10,

            'reserved_quantity' =>
                0,

            'minimum_stock' =>
                0,

            'average_cost' =>
                100,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Create Sale
        |--------------------------------------------------------------------------
        */

        $saleResponse =
            $this->postJson(
                route(
                    'inventory.sales.store'
                ),
                [
                    'warehouse_id' =>
                        $warehouseId,

                    'sale_date' =>
                        now()
                            ->toDateString(),

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
            ->assertCreated();

        $saleId =
            $saleResponse
                ->json('data.id');

        /*
        |--------------------------------------------------------------------------
        | Reserve
        |--------------------------------------------------------------------------
        */

        $this->postJson(
            route(
                'inventory.sales.reserve',
                $saleId
            )
        )
            ->assertOk()
            ->assertJsonPath(
                'data.status',
                'reserved'
            );

        $stock =
            WarehouseStock::where([
                'warehouse_id' =>
                    $warehouseId,

                'product_sku_id' =>
                    $sku->id,
            ])->firstOrFail();

        $this->assertSame(
            10.0,
            (float) $stock->quantity
        );

        $this->assertSame(
            3.0,
            (float) $stock->reserved_quantity
        );

        /*
        |--------------------------------------------------------------------------
        | Complete Reserved Sale
        |--------------------------------------------------------------------------
        */

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

        $stock->refresh();

        $this->assertSame(
            7.0,
            (float) $stock->quantity
        );

        $this->assertSame(
            0.0,
            (float) $stock->reserved_quantity
        );

        $this->assertSame(
            1,
            StockMovement::where([
                'movement_type' =>
                    'sale',

                'reference_type' =>
                    Sale::class,

                'reference_id' =>
                    $saleId,
            ])->count()
        );

        /*
        |--------------------------------------------------------------------------
        | Release Reservation
        |--------------------------------------------------------------------------
        */

        $saleTwo =
            Sale::create([
                'sale_number' =>
                    'RES-TEST-002',

                'warehouse_id' =>
                    $warehouseId,

                'user_id' =>
                    $admin->id,

                'status' =>
                    'draft',

                'sale_date' =>
                    now()->toDateString(),

                'subtotal' =>
                    300,

                'total_amount' =>
                    300,
            ]);

        $saleTwo
            ->items()
            ->create([
                'product_sku_id' =>
                    $sku->id,

                'quantity' =>
                    2,

                'unit_price' =>
                    150,

                'line_total' =>
                    300,
            ]);

        $this->postJson(
            route(
                'inventory.sales.reserve',
                $saleTwo->id
            )
        )
            ->assertOk();

        $stock->refresh();

        $this->assertSame(
            2.0,
            (float) $stock->reserved_quantity
        );

        $this->postJson(
            route(
                'inventory.sales.release-reservation',
                $saleTwo->id
            )
        )
            ->assertOk()
            ->assertJsonPath(
                'data.status',
                'draft'
            );

        $stock->refresh();

        $this->assertSame(
            0.0,
            (float) $stock->reserved_quantity
        );

        /*
        |--------------------------------------------------------------------------
        | Audit Exists Beyond Warehouse
        |--------------------------------------------------------------------------
        */

        $this->assertGreaterThan(
            0,
            AuditLog::where(
                'auditable_type',
                Sale::class
            )->count()
        );
    }
}