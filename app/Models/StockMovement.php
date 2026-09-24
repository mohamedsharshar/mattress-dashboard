<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'warehouse_id',
        'product_sku_id',
        'user_id',
        'movement_type',
        'quantity_change',
        'balance_after',
        'unit_cost',
        'reference_type',
        'reference_id',
        'occurred_at',
        'notes'
    ];

    protected $casts = [
        'quantity_change' => 'decimal:3',
        'balance_after' => 'decimal:3',
        'unit_cost' => 'decimal:2',
        'occurred_at' => 'datetime'
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }
}
