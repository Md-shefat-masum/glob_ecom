<?php

namespace App\Http\Controllers\Inventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\Inventory\Models\ProductSupplier;

class ProductSupplierContact extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function contactsupplier() {
        return $this->belongsTo(ProductSupplier::class, 'product_supplier_id');
    }
}
