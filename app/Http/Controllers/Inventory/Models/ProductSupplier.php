<?php

namespace App\Http\Controllers\Inventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSupplier extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function contact() {
        return $this->hasOne(ProductSupplierContact::class, 'product_supplier_id');
    }

}
