<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackageProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'package_code',
        'title',
        'slug',
        'tagline',
        'status',
        'visibility',
        'publish_at',
        'package_price',
        'compare_at_price',
        'calculated_savings_amount',
        'calculated_savings_percent',
        'pricing_breakdown',
        'hero_section',
        'content_blocks',
        'primary_media_id',
        'gallery_media_ids',
        'short_description',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'meta_image_id',
        'landing_settings',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'pricing_breakdown' => 'array',
        'hero_section' => 'array',
        'content_blocks' => 'array',
        'gallery_media_ids' => 'array',
        'landing_settings' => 'array',
        'publish_at' => 'datetime',
        'package_price' => 'float',
        'compare_at_price' => 'float',
        'calculated_savings_amount' => 'float',
        'calculated_savings_percent' => 'float',
    ];

    /**
     * Source product record (used for storefront compatibility).
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Package items included in this package.
     */
    public function items()
    {
        return $this->hasMany(PackageProductItem::class, 'package_id');
    }

    /**
     * Primary hero media.
     */
    public function heroMedia()
    {
        return $this->belongsTo(MediaFile::class, 'primary_media_id');
    }

    /**
     * Meta image.
     */
    public function metaImage()
    {
        return $this->belongsTo(MediaFile::class, 'meta_image_id');
    }

    /**
     * Creator relationship.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Updater relationship.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope active packages.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}

