<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOrderHold extends Model
{
    use HasFactory;

    protected $table = 'product_order_hold';

    protected $guarded = [];

    protected $casts = [
        'meta' => 'array',
    ];

    public function warehouse()
    {
        return $this->belongsTo(\App\Http\Controllers\Inventory\Models\ProductWarehouse::class, 'product_warehouse_id');
    }

    public function items()
    {
        return $this->hasMany(ProductOrderHoldItem::class, 'hold_id');
    }
}


