<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'brand_id',
        'category_id',
        'name',
        'name_en',
        'slug',
        'description',
        'unit',
        'is_active',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * الشركة المصنعة للمنتج.
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * تصنيف المنتج.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * الـ Variants التابعة للمنتج.
     *
     * Examples:
     *
     * Product: City Englander
     *
     * Variants:
     * - 15 CM
     * - 20 CM
     * - 25 CM
     *
     *
     * Product: Medical
     *
     * Variants:
     * - 15 CM
     * - 20 CM
     * - 25 CM
     * - 25 CM 1S
     * - 27 CM 1S
     * - 30 CM 2S
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)
            ->orderBy('sort_order');
    }
}