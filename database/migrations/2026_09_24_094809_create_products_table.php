<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Product Classification
            |--------------------------------------------------------------------------
            */

            $table->foreignId('brand_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('category_id')
                ->constrained()
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Basic Product Data
            |--------------------------------------------------------------------------
            |
            | أمثلة:
            |
            | City Englander
            | Pocket
            | Medical
            | مخدة فايبر
            | مرتبة تطرية ميموري فوم
            |
            | السمك والمقاس مش هنا؛ دول هيبقوا في ProductVariant.
            |
            */

            $table->string('name', 191);

            $table->string('name_en', 191)
                ->nullable();

            $table->string('slug', 220)
                ->nullable();

            $table->text('description')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Product Unit
            |--------------------------------------------------------------------------
            |
            | حاليًا معظم المخزون قطعة.
            |
            | لكن بنسيبه String بدل Enum عشان مستقبلًا نستوعب:
            |
            | piece
            | set
            | meter
            | pack
            |
            */

            $table->string('unit', 30)
                ->default('piece');

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index('brand_id');
            $table->index('category_id');
            $table->index('name');
            $table->index('is_active');

            /*
             * نفس الشركة والتصنيف مينفعش يتكرر عندهم
             * نفس اسم المنتج الأساسي مرتين.
             *
             * مثال:
             * Englander + Mattresses + City Englander
             */
            $table->unique(
                ['brand_id', 'category_id', 'name'],
                'products_brand_category_name_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};