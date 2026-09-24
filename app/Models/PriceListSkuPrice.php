<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceListSkuPrice extends Model
{
    protected $fillable = [
        'price_list_id',
        'product_sku_id',
        'price',
        'notes'
    ];

    protected $casts = [
        'price' => 'decimal:2'
    ];

    public function priceList()
    {
        return $this->belongsTo(PriceList::class);
    }

    public function sku()
    {
        return $this->belongsTo(
            ProductSku::class,
            'product_sku_id'
        );
    }
}
