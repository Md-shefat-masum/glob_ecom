<?php

namespace App\Models;

use App\Http\Controllers\Inventory\Models\ProductPurchaseOrderProduct;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPurchaseOrderProductUnit extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class, 'product_id');
    }

    public function productPurchaseOrderProduct()
    {
        return $this->belongsTo(ProductPurchaseOrderProduct::class, 'product_purchase_order_product_id');
    }

    public function variantCombination()
    {
        return $this->belongsTo(\App\Models\ProductVariantCombination::class, 'variant_combination_id');
    }
}
