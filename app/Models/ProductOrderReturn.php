<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOrderReturn extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'other_charges' => 'array',
    ];

    /**
     * Get the return products
     */
    public function return_products()
    {
        return $this->hasMany(ProductOrderReturnProduct::class, 'product_order_return_id');
    }

    /**
     * Get the original order
     */
    public function originalOrder()
    {
        return $this->belongsTo(ProductOrder::class, 'product_order_id');
    }

    /**
     * Get the customer
     */
    public function customer()
    {
        return $this->belongsTo(\App\Http\Controllers\Customer\Models\Customer::class, 'customer_id');
    }

    /**
     * Get the warehouse
     */
    public function warehouse()
    {
        return $this->belongsTo(\App\Http\Controllers\Inventory\Models\ProductWarehouse::class, 'product_warehouse_id');
    }

    /**
     * Get the creator
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator');
    }
}

