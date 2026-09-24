<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseReturn extends Model
{
    protected $fillable = [
        'return_number',
        'purchase_id',
        'warehouse_id',
        'user_id',
        'status',
        'return_date',
        'total_amount',
        'notes'
    ];

    protected $casts = [
        'return_date' => 'date',
        'total_amount' => 'decimal:2'
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
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
        return $this->hasMany(PurchaseReturnItem::class);
    }

    public function payments()
    {
        return $this->morphMany(
            Payment::class,
            'payable'
        );
    }
}
