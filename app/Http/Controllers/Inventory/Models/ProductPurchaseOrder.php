<?php

namespace App\Http\Controllers\Inventory\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPurchaseOrder extends Model
{
    use HasFactory;

    protected $guarded = [];

    // public function contact() {
    //     return $this->hasOne(ProductSupplierContact::class, 'product_supplier_id');
    // }
    public function order_products() {
        return $this->hasMany(ProductPurchaseOrderProduct::class, 'product_purchase_order_id');
    }

    public function creator() {
        return $this->belongsTo(User::class, 'creator'); 
    }

    public function supplier() {
        return $this->belongsTo(ProductSupplier::class, 'product_supplier_id');
    }

    public function warehouse() {
        return $this->belongsTo(ProductWarehouse::class, 'product_warehouse_id');
    }

}
