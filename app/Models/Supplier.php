<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'code',
        'name',
        'phone',
        'email',
        'tax_number',
        'address',
        'notes',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
