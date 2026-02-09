<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductFilterAttribute extends Model
{
    use HasFactory;

    protected $table = 'product_filter_attributes';

    protected $fillable = [
        'product_id',
        'group_slug',
        'selected_values'
    ];

    protected $casts = [
        'selected_values' => 'array'
    ];

    /**
     * Get the product this filter attribute belongs to
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get filter attributes by product ID
     */
    public static function getByProduct($productId)
    {
        return self::where('product_id', $productId)->get();
    }

    /**
     * Get filter values for a specific group
     */
    public static function getFilterValues($productId, $groupSlug)
    {
        $attr = self::where('product_id', $productId)
            ->where('group_slug', $groupSlug)
            ->first();
        
        return $attr ? $attr->selected_values : [];
    }
}

