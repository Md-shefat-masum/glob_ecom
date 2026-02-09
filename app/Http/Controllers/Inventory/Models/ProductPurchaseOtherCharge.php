<?php

namespace App\Http\Controllers\Inventory\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPurchaseOtherCharge extends Model
{
    use HasFactory;

    protected $guarded = [];

    // public function order_products() {
    //     return $this->hasMany(ProductPurchaseOrderProduct::class, 'product_purchase_order_id');
    // }

    // public function creator() {
    //     return $this->belongsTo(User::class, 'creator'); 
    // }

}
