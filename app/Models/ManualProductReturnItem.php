<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManualProductReturnItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Get the parent return
     */
    public function return()
    {
        return $this->belongsTo(ManualProductReturn::class, 'manual_product_return_id');
    }

    /**
     * Get the product (if linked)
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}

