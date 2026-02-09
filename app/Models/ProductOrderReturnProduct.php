<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOrderReturnProduct extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Get the parent return
     */
    public function return()
    {
        return $this->belongsTo(ProductOrderReturn::class, 'product_order_return_id');
    }

    /**
     * Get the product
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Get the original order product
     */
    public function originalOrderProduct()
    {
        return $this->belongsTo(ProductOrderProduct::class, 'product_order_product_id');
    }
}

