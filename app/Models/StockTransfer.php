<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransfer extends Model
{
    protected $fillable = [
        'transfer_number',
        'from_warehouse_id',
        'to_warehouse_id',
        'user_id',
        'status',
        'transferred_at',
        'received_at',
        'notes'
    ];

    protected $casts = [
        'transferred_at' => 'datetime',
        'received_at' => 'datetime'
    ];

    public function fromWarehouse()
    {
        return $this->belongsTo(
            Warehouse::class,
            'from_warehouse_id'
        );
    }

    public function toWarehouse()
    {
        return $this->belongsTo(
            Warehouse::class,
            'to_warehouse_id'
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(StockTransferItem::class);
    }
}
