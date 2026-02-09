<?php

namespace App\Http\Resources;

use App\Models\Category;
use App\Models\ChildCategory;
use App\Models\Flag;
use App\Models\ProductImage;
use App\Models\ProductQuestionAnswer;
use App\Models\ProductReview;
use App\Models\ProductVariantCombination;
use App\Models\Subcategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // Get variant combinations using the new structure
        $variantCombinations = ProductVariantCombination::where('product_id', $this->id)
            ->where('status', 1)
            ->get();

        $categoryInfo = Category::where('id', $this->category_id)->first();
        $subcategoryInfo = Subcategory::where('id', $this->subcategory_id)->first();
        $childcategoryInfo = ChildCategory::where('id', $this->childcategory_id)->first();
        $flagInfo = Flag::where('id', $this->flag_id)->first();
        
        // Calculate total stock from variants
        $totalStockAllVariants = 0;
        if($variantCombinations && count($variantCombinations) > 0){
            foreach ($variantCombinations as $variant) {
                $totalStockAllVariants = $totalStockAllVariants + (int) $variant->stock;
            }
        }
        
        // If product has variants, use variant stock total, otherwise use product stock
        if ($this->has_variant == 1) {
            $totalStockAllVariants = $totalStockAllVariants > 0 ? $totalStockAllVariants : (int)$this->stock;
        } else {
            $totalStockAllVariants = (int)$this->stock;
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'category_id' => $this->category_id,
            'category_name' => $this->category_name,
            'category_slug' => $categoryInfo ? $categoryInfo->slug : '',
            'subcategory_id' => $this->subcategory_id,
            'subcategory_name' => $this->subcategory_name,
            'subcategory_slug' => $subcategoryInfo ? $subcategoryInfo->slug : '',
            'childcategory_id' => $this->childcategory_id,
            'childcategory_name' => $this->childcategory_name,
            'childcategory_slug' => $childcategoryInfo ? $childcategoryInfo->slug : '',
            'brand_id' => $this->brand_id,
            'brand_name' => $this->brand_name,
            'model_id' => $this->model_id,
            'model_name' => $this->model_name,
            'image' => $this->image,
            'multiple_images' => ProductImageResource::collection(ProductImage::where('product_id', $this->id)->get()),
            // 'short_description' => $this->short_description,
            // 'description' => $this->description,
            'specification' => $this->specification,
            'warrenty_policy' => $this->warrenty_policy,
            'price' => $this->price,
            'discount_price' => $this->discount_price,
            'discount_parcent' => $this->discount_parcent,
            'stock' => $totalStockAllVariants,
            'unit_id' => $this->unit_id,
            'unit_name' => $this->unit_name,
            'tags' => $this->tags,
            'video_url' => $this->video_url,
            'warrenty_id' => $this->warrenty_id,
            'product_warrenty' => $this->product_warrenty,
            'slug' => $this->slug,
            'meta_title' => $this->meta_title,
            'meta_keywords' => $this->meta_keywords,
            'meta_description' => $this->meta_description,
            'status' => $this->status,
            'flag_id' => $this->flag_id,
            'flag_name' => $this->flag_name,
            'flag_slug' => $flagInfo ? $flagInfo->slug : null,
            'flag_icon' => $flagInfo ? $flagInfo->icon : null,
            'average_rating' => number_format(ProductReview::where('product_id', $this->id)->where('status', 1)->avg('rating'), 1),
            'review_count' => ProductReview::where('product_id', $this->id)->where('status', 1)->count(),
            'reviews' => ProductReviewResource::collection(ProductReview::where('product_id', $this->id)->where('status', 1)->get()),
            'has_variant' => $this->has_variant,
            'variants' => ProductVariantResource::collection($variantCombinations),
            'questions' => ProductQuestionAnswerResource::collection(ProductQuestionAnswer::where('product_id', $this->id)->get()),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}