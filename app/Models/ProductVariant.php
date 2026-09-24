<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'product_id',
        'name',
        'thickness_cm',
        'specification',
        'description',
        'sort_order',
        'is_active',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'thickness_cm' => 'decimal:2',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * المنتج الأساسي.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * الـ SKUs التابعة لهذا الـ Variant.
     *
     * Example:
     *
     * Variant:
     * Medical 30 CM 2S
     *
     * SKUs:
     * 100*200
     * 110*200
     * 120*200
     * ...
     * 200*200
     */
    public function skus(): HasMany
    {
        return $this->hasMany(
            ProductSku::class,
            'product_variant_id'
        )->orderBy('sort_order');
    }
}