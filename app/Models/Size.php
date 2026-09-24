<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Size extends Model
{
    protected $fillable = ['label', 'width_cm', 'length_cm', 'sort_order'];

    public function productLinePrices(): HasMany
    {
        return $this->hasMany(ProductLinePrice::class);
    }
}