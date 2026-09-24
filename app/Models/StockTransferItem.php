<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransferItem extends Model
{
    protected $fillable = [
        'stock_transfer_id',
        'product_sku_id',
        'quantity',
        'received_quantity',
        'notes'
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'received_quantity' => 'decimal:3'
    ];

    public function transfer()
    {
        return $this->belongsTo(
            StockTransfer::class,
            'stock_transfer_id'
        );
    }

    public function sku()
    {
        return $this->belongsTo(
            ProductSku::class,
            'product_sku_id'
        );
    }
}
