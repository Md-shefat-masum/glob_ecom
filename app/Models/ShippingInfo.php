<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;


class ShippingInfo extends Model
{
    use HasFactory;
    protected $table = 'shipping_addresses';
    protected $guarded = [];

    public function order() {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
