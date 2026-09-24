<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

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
     * Price lists issued by this brand.
     *
     * Example:
     * Englander -> April 2026 price list
     */
    public function priceLists(): HasMany
    {
        return $this->hasMany(PriceList::class);
    }

    /**
     * Products belonging to this brand.
     *
     * Example:
     *
     * Englander
     * ├── Brilliant
     * ├── City Englander
     * ├── Victoria
     * └── Classic
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}