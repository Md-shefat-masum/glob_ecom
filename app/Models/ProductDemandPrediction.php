<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductDemandPrediction extends Model
{
    protected $guarded = [];

    protected $casts = [
        'predicted_for' => 'date',
        'predicted_at' => 'datetime',
        'feature_importance' => 'array',
        'raw_payload' => 'array',
        'restock_recommended' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

