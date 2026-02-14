<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOrderCourierMethod extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'config', 'status'];

    protected $casts = [
        'config' => 'array',
    ];
}
