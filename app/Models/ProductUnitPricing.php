<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductUnitPricing extends Model
{
    use HasFactory;

    protected $table = 'product_unit_pricing';

    protected $fillable = [
        'product_id',
        'unit_id',
        'unit_title',
        'unit_value',
        'unit_label',
        'price',
        'discount_price',
        'discount_percent',
        'reward_points',
        'is_default',
        'status'
    ];

    protected $casts = [
        'unit_value' => 'double',
        'price' => 'double',
        'discount_price' => 'double',
        'discount_percent' => 'integer',
        'reward_points' => 'double',
        'is_default' => 'boolean',
        'status' => 'boolean'
    ];

    /**
     * Get the product that owns the unit pricing
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the unit
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}

