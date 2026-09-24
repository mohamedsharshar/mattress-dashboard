<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceList extends Model
{
    protected $fillable = [
        'brand_id',
        'category_id',
        'title',
        'effective_date',
        'round_addition_price',
        'quarter_addition_price',
        'notes',
        'is_active'
    ];

    protected $casts = [
        'effective_date' => 'date',
        'round_addition_price' => 'decimal:2',
        'quarter_addition_price' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function productLines()
    {
        return $this->hasMany(ProductLine::class)
            ->orderBy('sort_order');
    }

    public function skuPrices()
    {
        return $this->hasMany(PriceListSkuPrice::class);
    }
}
