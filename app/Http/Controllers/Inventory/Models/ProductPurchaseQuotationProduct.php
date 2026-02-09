<?php

namespace App\Http\Controllers\Inventory\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPurchaseQuotationProduct extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function quotation() {
        return $this->belongsTo(ProductPurchaseQuotation::class, 'product_purchase_quotation_id');
    }

    public function product() {
        return $this->belongsTo(Product::class, 'product_id');
    }

}
