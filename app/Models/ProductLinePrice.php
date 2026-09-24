<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductLinePrice extends Model
{
    protected $fillable = ['product_line_id', 'size_id', 'price'];

    public function productLine(): BelongsTo
    {
        return $this->belongsTo(ProductLine::class);
    }

    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class);
    }
}