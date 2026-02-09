<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStockVariantsGroupKey extends Model
{
    use HasFactory;

    protected $table = 'product_stock_variants_group_keys';

    protected $fillable = [
        'group_id',
        'key_name',
        'key_value',
        'image',
        'sort_order',
        'status'
    ];

    protected $casts = [
        'group_id' => 'integer',
        'sort_order' => 'integer',
        'status' => 'boolean'
    ];

    /**
     * Get the variant group this key belongs to
     */
    public function group()
    {
        return $this->belongsTo(ProductStockVariantGroup::class, 'group_id');
    }

    /**
     * Scope for active keys
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Get keys by group ID
     */
    public static function getByGroup($groupId)
    {
        return self::where('group_id', $groupId)
            ->where('status', 1)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Get keys by group slug
     */
    public static function getByGroupSlug($groupSlug)
    {
        $group = ProductStockVariantGroup::where('slug', $groupSlug)
            ->where('status', 1)
            ->first();

        if (!$group) {
            return collect([]);
        }

        return self::getByGroup($group->id);
    }
}

