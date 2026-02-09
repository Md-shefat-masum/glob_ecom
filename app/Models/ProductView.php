<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductView extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'ip_address',
        'user_agent',
        'viewed_at'
    ];

    protected $casts = [
        'viewed_at' => 'datetime'
    ];

    /**
     * Get the product that was viewed
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user who viewed (null for guests)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Track a product view
     */
    public static function track($productId, $userId = null)
    {
        return self::create([
            'product_id' => $productId,
            'user_id' => $userId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'viewed_at' => now()
        ]);
    }
}

