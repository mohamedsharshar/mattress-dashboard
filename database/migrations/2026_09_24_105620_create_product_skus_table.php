<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_skus', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Parent Variant
            |--------------------------------------------------------------------------
            |
            | Example:
            |
            | Product:
            | Medical
            |
            | Variant:
            | 30 CM / 2S
            |
            | SKU:
            | 160 x 200
            |
            */

            $table->foreignId('product_variant_id')
                ->constrained()
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | SKU
            |--------------------------------------------------------------------------
            |
            | ده الـ Internal Stock Code.
            |
            | لازم يكون Unique على مستوى النظام كله.
            |
            | Example:
            | AIR-MED-30-2S-160X200
            |
            */

            $table->string('sku', 100)
                ->unique();

            /*
            |--------------------------------------------------------------------------
            | Barcode
            |--------------------------------------------------------------------------
            |
            | اختياري.
            |
            | ممكن بعدين الموظف يمسح Barcode
            | ويرجع المنتج والمقاس فورًا.
            |
            */

            $table->string('barcode', 100)
                ->nullable()
                ->unique();

            /*
            |--------------------------------------------------------------------------
            | Physical Size
            |--------------------------------------------------------------------------
            |
            | نخزن المقاس الحقيقي للقطعة.
            |
            | مثال:
            |
            | width_cm  = 160
            | length_cm = 200
            |
            */

            $table->decimal('width_cm', 7, 2)
                ->nullable();

            $table->decimal('length_cm', 7, 2)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Human Readable Size
            |--------------------------------------------------------------------------
            |
            | Example:
            |
            | 160*200
            | 60*40
            | 90
            | Standard
            |
            | مفيد جدًا للحاجات اللي مش أبعادها Width x Length كاملة.
            |
            */

            $table->string('size_label', 100)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Description / Notes
            |--------------------------------------------------------------------------
            */

            $table->text('notes')
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

            $table->index('product_variant_id');

            $table->index([
                'width_cm',
                'length_cm',
            ]);

            $table->index('size_label');

            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_skus');
    }
};