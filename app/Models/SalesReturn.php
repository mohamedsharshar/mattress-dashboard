<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesReturn extends Model
{
    protected $fillable = [
        'return_number',
        'sale_id',
        'warehouse_id',
        'user_id',
        'status',
        'return_date',
        'refund_total',
        'notes'
    ];

    protected $casts = [
        'return_date' => 'date',
        'refund_total' => 'decimal:2'
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(SalesReturnItem::class);
    }

    public function payments()
    {
        return $this->morphMany(
            Payment::class,
            'payable'
        );
    }
}
