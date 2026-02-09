<?php

namespace App\Http\Controllers\Inventory\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPurchaseQuotation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function quotation_products() {
        return $this->hasMany(ProductPurchaseQuotationProduct::class, 'product_purchase_quotation_id');
    }


    public function creator() {
        return $this->belongsTo(User::class, 'creator'); 
    }
    

}
