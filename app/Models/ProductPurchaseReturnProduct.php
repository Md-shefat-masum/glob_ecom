<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Product;
use App\Models\ProductVariantCombination;


class ProductPurchaseReturnProduct extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'stock_codes' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function variantCombination()
    {
        return $this->belongsTo(ProductVariantCombination::class, 'variant_combination_id');
    }
}
