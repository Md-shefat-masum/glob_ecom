<?php

namespace App\Http\Controllers\Inventory\Models;

use App\Models\Product;
use App\Models\ProductVariantCombination;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPurchaseOrderProduct extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function variantCombination()
    {
        return $this->belongsTo(ProductVariantCombination::class, 'variant_combination_id');
    }
}
