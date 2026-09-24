<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    protected $fillable = [
        'purchase_id',
        'product_sku_id',
        'quantity',
        'unit_cost',
        'discount_amount',
        'tax_amount',
        'line_total',
        'notes'
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_cost' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'line_total' => 'decimal:2'
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
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
        return $this->hasMany(PurchaseReturnItem::class);
    }
}
