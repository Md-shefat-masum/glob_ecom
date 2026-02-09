<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * Supports both old product_variants structure and new product_variant_combinations structure
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // Check if this is the new structure (has variant_values property)
        if (isset($this->variant_values) || (is_object($this->resource) && property_exists($this->resource, 'variant_values'))) {
            // New structure: ProductVariantCombination
            $variantValues = is_array($this->variant_values) ? $this->variant_values : (is_string($this->variant_values) ? json_decode($this->variant_values, true) : []);
            
            // Get color info from variant_values
            $colorName = $variantValues['color'] ?? null;
            $colorCode = null;
            if ($colorName) {
                $colorKey = DB::table('product_stock_variants_group_keys')
                    ->where('group_id', 1) // Color group
                    ->where('key_name', $colorName)
                    ->first();
                $colorCode = $colorKey->key_value ?? null;
            }

            // Build image full URL if image exists
            $imageFullUrl = null;
            if (isset($this->image) && $this->image) {
                $imageFullUrl = asset($this->image);
            }

            return [
                'id' => $this->id ?? null,
                'product_id' => $this->product_id ?? null,
                'image' => $this->image ?? null,
                'image_full_url' => $imageFullUrl,
                'color_id' => null, // Not available in new structure
                'color_name' => $colorName,
                'color_code' => $colorCode,
                'size_id' => null, // Not available in new structure
                'size_name' => $variantValues['size'] ?? null,
                'region_id' => null, // Not available in new structure
                'region_name' => $variantValues['region'] ?? null,
                'sim_id' => null, // Not available in new structure
                'sim_type' => $variantValues['sim_type'] ?? null,
                'storage_type_id' => null, // Not available in new structure
                'storage_type' => $variantValues['storage_type'] ?? null,
                'stock' => (int)($this->stock ?? 0),
                'price' => (float)($this->price ?? 0),
                'discounted_price' => isset($this->discount_price) && $this->discount_price ? (float)$this->discount_price : null,
                'device_condition_id' => null, // Not available in new structure
                'device_condition' => $variantValues['device_condition'] ?? null,
                'warrenty_id' => null,
                'product_warrenty' => null,
                'created_at' => $this->created_at ?? null,
                'updated_at' => $this->updated_at ?? null,
            ];
        } else {
            // Old structure: product_variants (backward compatibility)
            return [
                'id' => $this->id ?? null,
                'product_id' => $this->product_id ?? null,
                'image' => $this->image ?? null,
                'color_id' => $this->color_id ?? null,
                'color_name' => $this->color_name ?? null,
                'size_id' => $this->size_id ?? null,
                'size_name' => $this->size_name ?? null,
                'color_code' => $this->color_code ?? null,
                'region_id' => $this->region_id ?? null,
                'region_name' => $this->region_name ?? null,
                'sim_id' => $this->sim_id ?? null,
                'sim_type' => $this->sim_type ?? null,
                'storage_type_id' => $this->storage_type_id ?? null,
                'storage_type' => $this->storage_type ?? null,
                'stock' => (int)($this->stock ?? 0),
                'price' => (float)($this->price ?? 0),
                'discounted_price' => isset($this->discounted_price) && $this->discounted_price ? (float)$this->discounted_price : null,
                'device_condition_id' => $this->device_condition_id ?? null,
                'warrenty_id' => $this->warrenty_id ?? null,
                'product_warrenty' => $this->product_warrenty ?? null,
                'created_at' => $this->created_at ?? null,
                'updated_at' => $this->updated_at ?? null,
            ];
        }
    }
}
