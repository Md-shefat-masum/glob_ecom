<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariantCombination extends Model
{
    use HasFactory;

    protected $table = 'product_variant_combinations';

    protected $fillable = [
        'product_id',
        'combination_key',
        'variant_values',
        'price',
        'discount_price',
        'additional_price',
        'stock',
        'low_stock_alert',
        'sku',
        'barcode',
        'image',
        'product_warehouse_id',
        'product_warehouse_room_id',
        'product_warehouse_room_cartoon_id',
        'status'
    ];

    protected $casts = [
        'product_id' => 'integer',
        'variant_values' => 'array',
        'price' => 'double',
        'discount_price' => 'double',
        'additional_price' => 'double',
        'stock' => 'double',
        'low_stock_alert' => 'integer',
        'product_warehouse_id' => 'integer',
        'product_warehouse_room_id' => 'integer',
        'product_warehouse_room_cartoon_id' => 'integer',
        'status' => 'boolean'
    ];

    /**
     * Get the product this combination belongs to
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope for active combinations
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Check if stock is low
     */
    public function isLowStock()
    {
        if ($this->low_stock_alert === null) {
            return false;
        }
        
        return $this->stock <= $this->low_stock_alert;
    }

    /**
     * Get the final price
     */
    public function getFinalPrice()
    {
        // If specific price is set, use it
        if ($this->price !== null) {
            return $this->price;
        }

        // Otherwise, add additional price to product base price
        return $this->product->price + $this->additional_price;
    }

    /**
     * Get the effective discount price
     */
    public function getEffectiveDiscountPrice()
    {
        return $this->discount_price ?? $this->product->discount_price;
    }

    /**
     * Get variant value by group slug
     */
    public function getVariantValue($groupSlug)
    {
        return $this->variant_values[$groupSlug] ?? null;
    }

    /**
     * Get combinations by product ID
     */
    public static function getByProduct($productId)
    {
        return self::where('product_id', $productId)
            ->where('status', 1)
            ->get();
    }

    /**
     * Generate combination key from variant values
     */
    public static function generateCombinationKey($variantValues)
    {
        // Sort by key to ensure consistency
        ksort($variantValues);
        
        $parts = [];
        foreach ($variantValues as $value) {
            $parts[] = $value;
        }
        
        return implode('-', $parts);
    }

    /**
     * Get the name attribute (human-readable variant combination)
     * This accessor allows us to use $variant->name in views
     */
    public function getNameAttribute()
    {
        if (empty($this->variant_values)) {
            return 'Standard';
        }

        // Convert variant_values array to a readable string
        // For example: ['color' => 'Red', 'size' => 'Large'] becomes "Red - Large"
        $values = array_values($this->variant_values);
        return implode(' - ', $values);
    }
}

