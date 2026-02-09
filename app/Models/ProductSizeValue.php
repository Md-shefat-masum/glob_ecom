<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSizeValue extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function productSize()
    {
        return $this->belongsTo(ProductSize::class, 'product_size_id');
    }
}
