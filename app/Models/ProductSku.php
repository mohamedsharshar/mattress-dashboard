<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSku extends Model
{
    protected $fillable = [
        'product_variant_id',
        'sku',
        'barcode',
        'width_cm',
        'length_cm',
        'size_label',
        'notes',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'width_cm' => 'decimal:2',
        'length_cm' => 'decimal:2',
        'sort_order' => 'integer',
        'is_active' => 'boolean'
    ];

    public function variant()
    {
        return $this->belongsTo(
            ProductVariant::class,
            'product_variant_id'
        );
    }

    public function warehouseStocks()
    {
        return $this->hasMany(
            WarehouseStock::class,
            'product_sku_id'
        );
    }

    public function stockMovements()
    {
        return $this->hasMany(
            StockMovement::class,
            'product_sku_id'
        );
    }

    public function purchaseItems()
    {
        return $this->hasMany(
            PurchaseItem::class,
            'product_sku_id'
        );
    }

    public function saleItems()
    {
        return $this->hasMany(
            SaleItem::class,
            'product_sku_id'
        );
    }

    public function transferItems()
    {
        return $this->hasMany(
            StockTransferItem::class,
            'product_sku_id'
        );
    }

    public function inventoryCountItems()
    {
        return $this->hasMany(
            InventoryCountItem::class,
            'product_sku_id'
        );
    }

    public function salesReturnItems()
    {
        return $this->hasMany(
            SalesReturnItem::class,
            'product_sku_id'
        );
    }

    public function purchaseReturnItems()
    {
        return $this->hasMany(
            PurchaseReturnItem::class,
            'product_sku_id'
        );
    }

    public function priceListPrices()
    {
        return $this->hasMany(
            PriceListSkuPrice::class,
            'product_sku_id'
        );
    }
}
