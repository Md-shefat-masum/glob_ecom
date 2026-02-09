<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public static function getDropDownList($fieldName, $id=NULL){
        $str = "<option value=''>Select One</option>";
        $lists = self::where('status', 1)->orderBy($fieldName,'asc')->get();
        if($lists){
            foreach($lists as $list){
                if($id !=NULL && $id == $list->id){
                    $str .= "<option  value='".$list->id."' selected>".$list->$fieldName."</option>";
                }else{
                    $str .= "<option  value='".$list->id."'>".$list->$fieldName."</option>";
                }

            }
        }
        return $str;
    }

    // protected $fillable = [
    //     'category_id', 'subcategory_id', 'childcategory_id', 'brand_id', 'model_id', 'name', 'code', 'image', 
    //     'multiple_images', 'short_description', 'description', 'specification', 'warrenty_policy', 'price', 'discount_price', 'stock', 'unit_id', 
    //     'tags', 'video_url', 'warrenty_id', 'slug', 'flag_id', 'meta_title', 'meta_keywords', 'meta_description', 'status', 'has_variant', 
    //     'is_demo', 'is_package', 'created_at', 'contact_description', 'availability_status', 'related_similar_products', 'related_recommended_products', 'related_addon_products',
    //     'notification_title', 'notification_description', 'notification_button_text', 'notification_button_url', 'notification_image_path', 'notification_image_id', 'notification_is_show'
    // ];

    protected $guarded = [];

    protected $casts = [
        'related_similar_products' => 'array',
        'related_recommended_products' => 'array',
        'related_addon_products' => 'array',
        'faq' => 'array',
        'notification_is_show' => 'boolean',
    ];


    /**
     * Get the category that owns the product
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Get the subcategory that owns the product
     */
    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class, 'subcategory_id');
    }

    /**
     * Get the child category that owns the product
     */
    public function childCategory()
    {
        return $this->belongsTo(ChildCategory::class, 'childcategory_id');
    }

    /**
     * Get the brand that owns the product
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    /**
     * Get the model that owns the product
     */
    public function model()
    {
        return $this->belongsTo(ProductModel::class, 'model_id');
    }

    /**
     * Get the unit that owns the product
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    /**
     * Get product variants
     */
    public function variants() {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }

    /**
     * Get variant combinations
     */
    public function variantCombinations()
    {
        return $this->hasMany(ProductVariantCombination::class, 'product_id');
    }

    /**
     * Get filter attributes
     */
    public function filterAttributes()
    {
        return $this->hasMany(ProductFilterAttribute::class, 'product_id');
    }

    /**
     * Get unit pricing
     */
    public function unitPricing()
    {
        return $this->hasMany(ProductUnitPricing::class, 'product_id');
    }

    /**
     * Get product stocks
     */
    public function stocks()
    {
        return $this->hasMany(\App\Http\Controllers\Inventory\Models\ProductStock::class, 'product_id');
    }

    /**
     * Get total stock (including variants)
     */
    public function totalStock()    {
        if ($this->has_variant) {
            return $this->variantCombinations()->sum('stock');
        }
        return $this->stock;
    }

    /**
     * Package products relationships
     */
    public function packageItems() {
        return $this->hasMany(PackageProductItem::class, 'package_product_id');
    }

    public function packageItemProducts() {
        return $this->belongsToMany(Product::class, 'package_product_items', 'package_product_id', 'product_id')
                    ->withPivot('color_id', 'size_id', 'quantity');
    }

    /**
     * Check if this product is a package
     */
    public function isPackage() {
        return $this->is_package == 1;
    }

    /**
     * Check if this product has variants
     */
    public function hasVariants()
    {
        return $this->has_variant == 1;
    }

    /**
     * Get package items with full details
     */
    public function getPackageItemsWithDetails() {
        return $this->packageItems()->with(['product', 'color', 'size']);
    }
    
}
