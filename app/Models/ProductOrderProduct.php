<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOrderProduct extends Model
{
    use HasFactory;
    protected $guarded = [];

    /**
     * Get the variant associated with this product order item
     */
    public function variant()
    {
        return $this->belongsTo(ProductVariantCombination::class, 'variant_id');
    }

    /**
     * Get the unit price associated with this product order item
     */
    public function unitPrice()
    {
        return $this->belongsTo(ProductUnitPricing::class, 'unit_price_id');
    }

    /**
     * Get the product associated with this order item
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
