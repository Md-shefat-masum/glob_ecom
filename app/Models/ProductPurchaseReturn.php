<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPurchaseReturn extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    public function order_products()
    {
        return $this->hasMany(ProductPurchaseReturnProduct::class, 'product_purchase_return_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator');
    }

    public function supplier()
    {
        return $this->belongsTo(\App\Http\Controllers\Inventory\Models\ProductSupplier::class, 'product_supplier_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(\App\Http\Controllers\Inventory\Models\ProductWarehouse::class, 'product_warehouse_id');
    }
}
