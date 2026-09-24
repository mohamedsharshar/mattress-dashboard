<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    protected $fillable = [
        'name',
        'name_en',
        'logo_path',
        'phone',
        'email',
        'website',
        'tax_number',
        'address',
        'is_active',
    ];
    public function priceLists(): HasMany
    {
        return $this->hasMany(PriceList::class);
    }
}
