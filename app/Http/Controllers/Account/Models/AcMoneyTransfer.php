<?php

namespace App\Http\Controllers\Account\Models;

use App\Http\Controllers\Outlet\Models\CustomerSourceType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcMoneyTransfer extends Model
{
    use HasFactory;
    protected $guarded = []; 

    // public function customerCategory() {
    //     return $this->belongsTo(CustomerCategory::class, 'customer_category_id');
    // }

    // public function order_products() {
    //     return $this->hasMany(ProductPurchaseOrderProduct::class, 'product_purchase_order_id');
    // }



}
