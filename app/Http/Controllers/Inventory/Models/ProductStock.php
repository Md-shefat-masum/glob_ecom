<?php

namespace App\Http\Controllers\Inventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = [
        'variant_data' => 'array',
    ];

}
