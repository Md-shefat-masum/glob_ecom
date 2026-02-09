<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductFilterAttributeMapping extends Model
{
    use HasFactory;

    protected $table = 'product_filter_attribute_mappings';

    protected $fillable = [
        'variant_group_id',
        'variant_key_id',
        'category_id',
        'subcategory_id',
        'childcategory_id',
        'brand_id',
        'product_id',
    ];
}

