<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id',
        'product_sku_id',
        'quantity',
        'unit_price',
        'unit_cost',
        'discount_amount',
        'tax_amount',
        'line_total',
        'notes'
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'line_total' => 'decimal:2'
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function sku()
    {
        return $this->belongsTo(
            ProductSku::class,
            'product_sku_id'
        );
    }

    public function returnItems()
    {
        return $this->hasMany(SalesReturnItem::class);
    }
}
