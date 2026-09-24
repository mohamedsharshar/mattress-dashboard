<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\InventoryCount;
use App\Models\Product;
use App\Models\ProductSku;
use App\Models\ProductVariant;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\Sale;
use App\Models\SalesReturn;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Services\Inventory\InventoryWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_inventory_workflow(): void
    {
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
            'sku' => 'TEST-MED-30-160X200',
            'width_cm' => 160,
            'length_cm' => 200,
            'size_label' => '160*200',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $warehouse1 = Warehouse::create([
            'name' => 'Warehouse A',
            'code' => 'WH-A',
            'is_default' => true,
            'is_active' => true,
        ]);

        $warehouse2 = Warehouse::create([
            'name' => 'Warehouse B',
            'code' => 'WH-B',
            'is_active' => true,
        ]);

        $supplier = Supplier::create([
            'code' => 'SUP-001',
            'name' => 'Test Supplier',
            'is_active' => true,
        ]);

        $customer = Customer::create([
            'code' => 'CUS-001',
            'name' => 'Test Customer',
            'is_active' => true,
        ]);

        $workflow = app(
            InventoryWorkflowService::class
        );

        /*
        |--------------------------------------------------------------------------
        | Purchase: +10
        |--------------------------------------------------------------------------
        */

        $purchase = Purchase::create([
            'purchase_number' => 'PUR-001',
            'supplier_id' => $supplier->id,
            'warehouse_id' => $warehouse1->id,
            'status' => 'draft',
            'purchase_date' => now()->toDateString(),
            'subtotal' => 1000,
            'total_amount' => 1000,
        ]);

        $purchaseItem =
            $purchase->items()->create([
                'product_sku_id' => $sku->id,
                'quantity' => 10,
                'unit_cost' => 100,
                'line_total' => 1000,
            ]);

        $workflow->receivePurchase(
            $purchase->id
        );

        // Idempotency
        $workflow->receivePurchase(
            $purchase->id
        );

        $stock = WarehouseStock::where([
            'warehouse_id' => $warehouse1->id,
            'product_sku_id' => $sku->id,
        ])->firstOrFail();

        $this->assertSame(
            10.0,
            (float) $stock->quantity
        );

        $this->assertSame(
            100.0,
            (float) $stock->average_cost
        );

        /*
        |--------------------------------------------------------------------------
        | Sale: -3
        |--------------------------------------------------------------------------
        */

        $sale = Sale::create([
            'sale_number' => 'SAL-001',
            'customer_id' => $customer->id,
            'warehouse_id' => $warehouse1->id,
            'status' => 'draft',
            'sale_date' => now()->toDateString(),
            'subtotal' => 450,
            'total_amount' => 450,
        ]);

        $saleItem =
            $sale->items()->create([
                'product_sku_id' => $sku->id,
                'quantity' => 3,
                'unit_price' => 150,
                'line_total' => 450,
            ]);

        $workflow->completeSale(
            $sale->id
        );

        // Idempotency
        $workflow->completeSale(
            $sale->id
        );

        $this->assertSame(
            7.0,
            (float) WarehouseStock::where([
                'warehouse_id' => $warehouse1->id,
                'product_sku_id' => $sku->id,
            ])->value('quantity')
        );

        $this->assertSame(
            100.0,
            (float) $saleItem
                ->fresh()
                ->unit_cost
        );

        /*
        |--------------------------------------------------------------------------
        | Transfer: 2 from A to B
        |--------------------------------------------------------------------------
        */

        $transfer = StockTransfer::create([
            'transfer_number' => 'TRF-001',
            'from_warehouse_id' => $warehouse1->id,
            'to_warehouse_id' => $warehouse2->id,
            'status' => 'draft',
        ]);

        $transfer->items()->create([
            'product_sku_id' => $sku->id,
            'quantity' => 2,
        ]);

        $workflow->completeTransfer(
            $transfer->id
        );

        $this->assertSame(
            5.0,
            (float) WarehouseStock::where([
                'warehouse_id' => $warehouse1->id,
                'product_sku_id' => $sku->id,
            ])->value('quantity')
        );

        $this->assertSame(
            2.0,
            (float) WarehouseStock::where([
                'warehouse_id' => $warehouse2->id,
                'product_sku_id' => $sku->id,
            ])->value('quantity')
        );

        /*
        |--------------------------------------------------------------------------
        | Sales Return: +1
        |--------------------------------------------------------------------------
        */

        $salesReturn = SalesReturn::create([
            'return_number' => 'SR-001',
            'sale_id' => $sale->id,
            'warehouse_id' => $warehouse1->id,
            'status' => 'draft',
            'return_date' => now()->toDateString(),
            'refund_total' => 150,
        ]);

        $salesReturn->items()->create([
            'sale_item_id' => $saleItem->id,
            'product_sku_id' => $sku->id,
            'quantity' => 1,
            'unit_price' => 150,
            'condition' => 'resellable',
            'restock' => true,
            'line_total' => 150,
        ]);

        $workflow->completeSalesReturn(
            $salesReturn->id
        );

        $this->assertSame(
            6.0,
            (float) WarehouseStock::where([
                'warehouse_id' => $warehouse1->id,
                'product_sku_id' => $sku->id,
            ])->value('quantity')
        );

        /*
        |--------------------------------------------------------------------------
        | Purchase Return: -1
        |--------------------------------------------------------------------------
        */

        $purchaseReturn =
            PurchaseReturn::create([
                'return_number' => 'PR-001',
                'purchase_id' => $purchase->id,
                'warehouse_id' => $warehouse1->id,
                'status' => 'draft',
                'return_date' => now()->toDateString(),
                'total_amount' => 100,
            ]);

        $purchaseReturn->items()->create([
            'purchase_item_id' => $purchaseItem->id,
            'product_sku_id' => $sku->id,
            'quantity' => 1,
            'unit_cost' => 100,
            'line_total' => 100,
        ]);

        $workflow->completePurchaseReturn(
            $purchaseReturn->id
        );

        $this->assertSame(
            5.0,
            (float) WarehouseStock::where([
                'warehouse_id' => $warehouse1->id,
                'product_sku_id' => $sku->id,
            ])->value('quantity')
        );

        /*
        |--------------------------------------------------------------------------
        | Inventory Count: 5 -> 4
        |--------------------------------------------------------------------------
        */

        $count = InventoryCount::create([
            'count_number' => 'CNT-001',
            'warehouse_id' => $warehouse1->id,
            'status' => 'draft',
            'started_at' => now(),
        ]);

        $countItem =
            $count->items()->create([
                'product_sku_id' => $sku->id,
                'system_quantity' => 5,
                'counted_quantity' => 4,
            ]);

        $workflow->completeInventoryCount(
            $count->id
        );

        $this->assertSame(
            4.0,
            (float) WarehouseStock::where([
                'warehouse_id' => $warehouse1->id,
                'product_sku_id' => $sku->id,
            ])->value('quantity')
        );

        $this->assertSame(
            -1.0,
            (float) $countItem
                ->fresh()
                ->difference_quantity
        );

        /*
        |--------------------------------------------------------------------------
        | Ledger
        |--------------------------------------------------------------------------
        */

        $this->assertSame(
            7,
            StockMovement::count()
        );

        $this->assertSame(
            1,
            StockMovement::where(
                'movement_type',
                'purchase_receive'
            )->count()
        );

        $this->assertSame(
            1,
            StockMovement::where(
                'movement_type',
                'sale'
            )->count()
        );

        $this->assertSame(
            1,
            StockMovement::where(
                'movement_type',
                'transfer_out'
            )->count()
        );

        $this->assertSame(
            1,
            StockMovement::where(
                'movement_type',
                'transfer_in'
            )->count()
        );

        $this->assertSame(
            1,
            StockMovement::where(
                'movement_type',
                'sales_return'
            )->count()
        );

        $this->assertSame(
            1,
            StockMovement::where(
                'movement_type',
                'purchase_return'
            )->count()
        );

        $this->assertSame(
            1,
            StockMovement::where(
                'movement_type',
                'inventory_count'
            )->count()
        );
    }
}