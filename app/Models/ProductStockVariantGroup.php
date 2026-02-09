<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStockVariantGroup extends Model
{
    use HasFactory;

    protected $table = 'product_stock_variant_groups';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_fixed',
        'is_stock_related',
        'sort_order',
        'status'
    ];

    protected $casts = [
        'is_fixed' => 'boolean',
        'is_stock_related' => 'boolean',
        'sort_order' => 'integer',
        'status' => 'boolean'
    ];

    /**
     * Get the keys for this variant group
     */
    public function keys()
    {
        return $this->hasMany(ProductStockVariantsGroupKey::class, 'group_id');
    }

    /**
     * Get active keys only
     */
    public function activeKeys()
    {
        return $this->hasMany(ProductStockVariantsGroupKey::class, 'group_id')
            ->where('status', 1)
            ->orderBy('sort_order');
    }

    /**
     * Scope for active groups
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope for fixed groups (Color, Size)
     */
    public function scopeFixed($query)
    {
        return $query->where('is_fixed', 1);
    }

    /**
     * Scope for dynamic groups
     */
    public function scopeDynamic($query)
    {
        return $query->where('is_fixed', 0);
    }

    /**
     * Scope for stock-related groups
     */
    public function scopeStockRelated($query)
    {
        return $query->where('is_stock_related', 1);
    }

    /**
     * Scope for filter-related groups
     */
    public function scopeFilterRelated($query)
    {
        return $query->where('is_stock_related', 0);
    }

    /**
     * Get all active groups with their active keys
     */
    public static function getAllWithKeys()
    {
        return self::select('id', 'name', 'slug', 'description', 'is_fixed', 'is_stock_related', 'sort_order', 'status')
            ->active()
            ->with(['activeKeys' => function($query) {
                $query->select('id', 'group_id', 'key_name', 'key_value', 'image', 'sort_order', 'status');
            }])
            ->orderBy('sort_order')
            ->get();
    }
}

