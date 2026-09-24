<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseStock extends Model
{
    protected $fillable = [
        'warehouse_id',
        'product_sku_id',
        'quantity',
        'reserved_quantity',
        'minimum_stock',
        'average_cost',
        'last_movement_at'
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'reserved_quantity' => 'decimal:3',
        'minimum_stock' => 'decimal:3',
        'average_cost' => 'decimal:2',
        'last_movement_at' => 'datetime'
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function sku()
    {
        return $this->belongsTo(
            ProductSku::class,
            'product_sku_id'
        );
    }
}
