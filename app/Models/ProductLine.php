<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductLine extends Model
{
    protected $fillable = [
        'price_list_id',
        'name',
        'thickness_cm',
        'description',
        'custom_meter_price',
        'sort_order',
        'is_active',
    ];

    public function priceList(): BelongsTo
    {
        return $this->belongsTo(PriceList::class);
    }

    public function prices(): HasMany
    {
        return $this->hasMany(ProductLinePrice::class);
    }
}
