<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageProductItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_product_id',
        'package_id',
        'product_id',
        'product_variant_id',
        'variant_combination_id',
        'color_id',
        'size_id',
        'quantity',
        'unit_price',
        'compare_at_price',
        'variant_snapshot',
        'position',
    ];

    protected $casts = [
        'variant_snapshot' => 'array',
        'unit_price' => 'float',
        'compare_at_price' => 'float',
    ];

    /**
     * Get the package product that owns this item
     */
    public function packageProduct()
    {
        return $this->belongsTo(Product::class, 'package_product_id');
    }

    /**
     * Get the product included in this package
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the color of the product
     */
    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    /**
     * Get the size of the product
     */
    public function size()
    {
        return $this->belongsTo(ProductSize::class, 'size_id');
    }

    /**
     * Linked marketing package entity.
     */
    public function package()
    {
        return $this->belongsTo(PackageProduct::class, 'package_id');
    }

    /**
     * Reference legacy variant entry.
     */
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /**
     * Reference combination variant entry.
     */
    public function combination()
    {
        return $this->belongsTo(ProductVariantCombination::class, 'variant_combination_id');
    }
}
