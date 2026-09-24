<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $fillable = [
        'name',
        'code',
        'phone',
        'address',
        'is_default',
        'is_active',
        'notes'
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function stocks()
    {
        return $this->hasMany(WarehouseStock::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function outgoingTransfers()
    {
        return $this->hasMany(
            StockTransfer::class,
            'from_warehouse_id'
        );
    }

    public function incomingTransfers()
    {
        return $this->hasMany(
            StockTransfer::class,
            'to_warehouse_id'
        );
    }

    public function inventoryCounts()
    {
        return $this->hasMany(InventoryCount::class);
    }

    public function salesReturns()
    {
        return $this->hasMany(SalesReturn::class);
    }

    public function purchaseReturns()
    {
        return $this->hasMany(PurchaseReturn::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }
}
