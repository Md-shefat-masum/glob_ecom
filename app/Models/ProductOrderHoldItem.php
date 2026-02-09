<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOrderHoldItem extends Model
{
    use HasFactory;

    protected $table = 'product_order_hold_items';

    protected $guarded = [];

    protected $casts = [
        'meta' => 'array',
    ];

    public function hold()
    {
        return $this->belongsTo(ProductOrderHold::class, 'hold_id');
    }
}


