<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Basic Information
            |--------------------------------------------------------------------------
            */

            $table->string('name', 191);

            /*
             * كود داخلي ثابت للمخزن.
             *
             * Examples:
             *
             * MAIN
             * WH-01
             * SHEBIN-01
             */
            $table->string('code', 50)
                ->unique();

            /*
            |--------------------------------------------------------------------------
            | Contact / Location
            |--------------------------------------------------------------------------
            */

            $table->string('phone', 50)
                ->nullable();

            $table->text('address')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Default Warehouse
            |--------------------------------------------------------------------------
            |
            | المخزن الافتراضي اللي النظام يختاره تلقائيًا
            | في بعض العمليات لاحقًا.
            |
            */

            $table->boolean('is_default')
                ->default(false);

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_active')
                ->default(true);

            /*
            |--------------------------------------------------------------------------
            | Internal Notes
            |--------------------------------------------------------------------------
            */

            $table->text('notes')
                ->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index('name');
            $table->index('is_default');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};