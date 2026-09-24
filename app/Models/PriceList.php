<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'is_active',
    ];

    protected $casts = [
        'effective_date' => 'date',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function productLines(): HasMany
    {
        return $this->hasMany(ProductLine::class)->orderBy('sort_order');
    }
}
