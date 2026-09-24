<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Warehouse Stock
        |--------------------------------------------------------------------------
        */

        Schema::create('warehouse_stocks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('warehouse_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('product_sku_id')
                ->constrained('product_skus')
                ->restrictOnDelete();

            $table->decimal('quantity', 14, 3)->default(0);
            $table->decimal('reserved_quantity', 14, 3)->default(0);
            $table->decimal('minimum_stock', 14, 3)->default(0);

            $table->decimal('average_cost', 14, 2)->default(0);

            $table->timestamp('last_movement_at')->nullable();

            $table->timestamps();

            $table->unique(
                ['warehouse_id', 'product_sku_id'],
                'warehouse_stocks_warehouse_sku_unique'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | Suppliers
        |--------------------------------------------------------------------------
        */

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();

            $table->string('code', 50)->unique();
            $table->string('name', 191);

            $table->string('phone', 50)->nullable();
            $table->string('email', 191)->nullable();
            $table->string('tax_number', 100)->nullable();

            $table->text('address')->nullable();
            $table->text('notes')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index('name');
            $table->index('is_active');
        });

        /*
        |--------------------------------------------------------------------------
        | Customers
        |--------------------------------------------------------------------------
        */

        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            $table->string('code', 50)->unique();
            $table->string('name', 191);

            $table->string('phone', 50)->nullable();
            $table->string('email', 191)->nullable();
            $table->string('tax_number', 100)->nullable();

            $table->text('address')->nullable();
            $table->text('notes')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index('name');
            $table->index('phone');
            $table->index('is_active');
        });

        /*
        |--------------------------------------------------------------------------
        | Purchases
        |--------------------------------------------------------------------------
        */

        Schema::create('purchases', function (Blueprint $table) {
            $table->id();

            $table->string('purchase_number', 100)->unique();

            $table->foreignId('supplier_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('warehouse_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('invoice_number', 100)->nullable();

            $table->string('status', 30)->default('draft');

            $table->date('purchase_date');
            $table->timestamp('received_at')->nullable();

            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('discount_amount', 14, 2)->default(0);
            $table->decimal('tax_amount', 14, 2)->default(0);
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->decimal('paid_amount', 14, 2)->default(0);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['status', 'purchase_date']);

            $table->unique(
                ['supplier_id', 'invoice_number'],
                'purchases_supplier_invoice_unique'
            );
        });

        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('purchase_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('product_sku_id')
                ->constrained('product_skus')
                ->restrictOnDelete();

            $table->decimal('quantity', 14, 3);

            $table->decimal('unit_cost', 14, 2);
            $table->decimal('discount_amount', 14, 2)->default(0);
            $table->decimal('tax_amount', 14, 2)->default(0);
            $table->decimal('line_total', 14, 2);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['purchase_id', 'product_sku_id']);
        });

        /*
        |--------------------------------------------------------------------------
        | Sales
        |--------------------------------------------------------------------------
        */

        Schema::create('sales', function (Blueprint $table) {
            $table->id();

            $table->string('sale_number', 100)->unique();

            $table->foreignId('customer_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('warehouse_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('status', 30)->default('draft');

            $table->date('sale_date');
            $table->timestamp('completed_at')->nullable();

            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('discount_amount', 14, 2)->default(0);
            $table->decimal('tax_amount', 14, 2)->default(0);
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->decimal('paid_amount', 14, 2)->default(0);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['status', 'sale_date']);
        });

        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sale_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('product_sku_id')
                ->constrained('product_skus')
                ->restrictOnDelete();

            $table->decimal('quantity', 14, 3);

            $table->decimal('unit_price', 14, 2);
            $table->decimal('unit_cost', 14, 2)->nullable();

            $table->decimal('discount_amount', 14, 2)->default(0);
            $table->decimal('tax_amount', 14, 2)->default(0);
            $table->decimal('line_total', 14, 2);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['sale_id', 'product_sku_id']);
        });

        /*
        |--------------------------------------------------------------------------
        | Payments
        |--------------------------------------------------------------------------
        |
        | payable_type + payable_id:
        | Sale / Purchase / Return ... إلخ
        |--------------------------------------------------------------------------
        */

        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->string('payable_type', 120);
            $table->unsignedBigInteger('payable_id');

            $table->string('direction', 10);
            $table->string('method', 50)->default('cash');

            $table->decimal('amount', 14, 2);

            $table->string('reference_number', 100)->nullable();

            $table->timestamp('paid_at');

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(
                ['payable_type', 'payable_id'],
                'payments_payable_index'
            );

            $table->index('paid_at');
        });

        /*
        |--------------------------------------------------------------------------
        | Stock Transfers
        |--------------------------------------------------------------------------
        */

        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();

            $table->string('transfer_number', 100)->unique();

            $table->foreignId('from_warehouse_id')
                ->constrained('warehouses')
                ->restrictOnDelete();

            $table->foreignId('to_warehouse_id')
                ->constrained('warehouses')
                ->restrictOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('status', 30)->default('draft');

            $table->timestamp('transferred_at')->nullable();
            $table->timestamp('received_at')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('status');
        });

        Schema::create('stock_transfer_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('stock_transfer_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('product_sku_id')
                ->constrained('product_skus')
                ->restrictOnDelete();

            $table->decimal('quantity', 14, 3);
            $table->decimal('received_quantity', 14, 3)->default(0);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(
                ['stock_transfer_id', 'product_sku_id'],
                'stock_transfer_items_transfer_sku_unique'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | Inventory Counts
        |--------------------------------------------------------------------------
        */

        Schema::create('inventory_counts', function (Blueprint $table) {
            $table->id();

            $table->string('count_number', 100)->unique();

            $table->foreignId('warehouse_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('status', 30)->default('draft');

            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('status');
        });

        Schema::create('inventory_count_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('inventory_count_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('product_sku_id')
                ->constrained('product_skus')
                ->restrictOnDelete();

            $table->decimal('system_quantity', 14, 3)->default(0);
            $table->decimal('counted_quantity', 14, 3)->nullable();
            $table->decimal('difference_quantity', 14, 3)->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(
                ['inventory_count_id', 'product_sku_id'],
                'inventory_count_items_count_sku_unique'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | Sales Returns
        |--------------------------------------------------------------------------
        */

        Schema::create('sales_returns', function (Blueprint $table) {
            $table->id();

            $table->string('return_number', 100)->unique();

            $table->foreignId('sale_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('warehouse_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('status', 30)->default('draft');

            $table->date('return_date');

            $table->decimal('refund_total', 14, 2)->default(0);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['status', 'return_date']);
        });

        Schema::create('sales_return_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sales_return_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('sale_item_id')
                ->nullable()
                ->constrained('sale_items')
                ->nullOnDelete();

            $table->foreignId('product_sku_id')
                ->constrained('product_skus')
                ->restrictOnDelete();

            $table->decimal('quantity', 14, 3);

            $table->decimal('unit_price', 14, 2);

            $table->string('condition', 30)
                ->default('resellable');

            $table->boolean('restock')
                ->default(true);

            $table->decimal('line_total', 14, 2);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['sales_return_id', 'product_sku_id']);
        });

        /*
        |--------------------------------------------------------------------------
        | Purchase Returns
        |--------------------------------------------------------------------------
        */

        Schema::create('purchase_returns', function (Blueprint $table) {
            $table->id();

            $table->string('return_number', 100)->unique();

            $table->foreignId('purchase_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('warehouse_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('status', 30)->default('draft');

            $table->date('return_date');

            $table->decimal('total_amount', 14, 2)->default(0);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['status', 'return_date']);
        });

        Schema::create('purchase_return_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('purchase_return_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('purchase_item_id')
                ->nullable()
                ->constrained('purchase_items')
                ->nullOnDelete();

            $table->foreignId('product_sku_id')
                ->constrained('product_skus')
                ->restrictOnDelete();

            $table->decimal('quantity', 14, 3);
            $table->decimal('unit_cost', 14, 2);
            $table->decimal('line_total', 14, 2);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['purchase_return_id', 'product_sku_id']);
        });

        /*
        |--------------------------------------------------------------------------
        | Stock Movement Ledger
        |--------------------------------------------------------------------------
        |
        | quantity_change:
        |
        | +10 = دخول مخزون
        | -3  = خروج مخزون
        |--------------------------------------------------------------------------
        */

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('warehouse_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('product_sku_id')
                ->constrained('product_skus')
                ->restrictOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('movement_type', 50);

            $table->decimal('quantity_change', 14, 3);
            $table->decimal('balance_after', 14, 3);

            $table->decimal('unit_cost', 14, 2)->nullable();

            $table->string('reference_type', 120)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();

            $table->timestamp('occurred_at');

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(
                ['warehouse_id', 'product_sku_id', 'occurred_at'],
                'stock_movements_stock_history_index'
            );

            $table->index(
                ['reference_type', 'reference_id'],
                'stock_movements_reference_index'
            );

            $table->index('movement_type');
        });

        /*
        |--------------------------------------------------------------------------
        | New SKU Pricing
        |--------------------------------------------------------------------------
        */

        Schema::create('price_list_sku_prices', function (Blueprint $table) {
            $table->id();

            $table->foreignId('price_list_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('product_sku_id')
                ->constrained('product_skus')
                ->restrictOnDelete();

            $table->decimal('price', 14, 2);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(
                ['price_list_id', 'product_sku_id'],
                'price_list_sku_prices_unique'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | Audit Log
        |--------------------------------------------------------------------------
        */

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('event', 50);

            $table->string('auditable_type', 120);
            $table->unsignedBigInteger('auditable_id');

            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();

            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->index(
                ['auditable_type', 'auditable_id'],
                'audit_logs_auditable_index'
            );

            $table->index(['event', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('price_list_sku_prices');
        Schema::dropIfExists('stock_movements');

        Schema::dropIfExists('purchase_return_items');
        Schema::dropIfExists('purchase_returns');

        Schema::dropIfExists('sales_return_items');
        Schema::dropIfExists('sales_returns');

        Schema::dropIfExists('inventory_count_items');
        Schema::dropIfExists('inventory_counts');

        Schema::dropIfExists('stock_transfer_items');
        Schema::dropIfExists('stock_transfers');

        Schema::dropIfExists('payments');

        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');

        Schema::dropIfExists('purchase_items');
        Schema::dropIfExists('purchases');

        Schema::dropIfExists('customers');
        Schema::dropIfExists('suppliers');

        Schema::dropIfExists('warehouse_stocks');
    }
};