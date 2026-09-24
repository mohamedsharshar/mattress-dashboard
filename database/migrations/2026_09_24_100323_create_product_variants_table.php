<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Parent Product
            |--------------------------------------------------------------------------
            |
            | Example:
            |
            | Product: City Englander
            | Variant: 25 CM
            |
            */

            $table->foreignId('product_id')
                ->constrained()
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Variant Name
            |--------------------------------------------------------------------------
            |
            | Examples:
            |
            | 25 CM
            | 30 CM
            | 25 CM 1S
            | 30 CM 2S
            | Standard
            |
            | المنتجات اللي ملهاش Variant حقيقي هنقدر نديها
            | Variant باسم Standard بعدين.
            |
            */

            $table->string('name', 191);

            /*
            |--------------------------------------------------------------------------
            | Thickness
            |--------------------------------------------------------------------------
            |
            | Example:
            |
            | 15
            | 20
            | 25
            | 27
            | 30
            | 35
            |
            */

            $table->decimal('thickness_cm', 6, 2)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Specification
            |--------------------------------------------------------------------------
            |
            | نستخدمه للحاجات الإضافية اللي موجودة في المصدر.
            |
            | Example:
            |
            | 1S
            | 2S
            |
            | وممكن بعدين نستفيد منه لأي specification مشابهة.
            |
            */

            $table->string('specification', 100)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Internal Notes
            |--------------------------------------------------------------------------
            */

            $table->text('description')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Ordering / Status
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index('product_id');
            $table->index('thickness_cm');
            $table->index('is_active');

            /*
             * نفس المنتج مينفعش يكون له Variant
             * بنفس الاسم مرتين.
             *
             * Example:
             *
             * Medical + 25 CM 1S
             * لا يتكرر مرتين.
             */

            $table->unique(
                ['product_id', 'name'],
                'product_variants_product_name_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};