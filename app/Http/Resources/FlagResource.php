<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Resources\Json\JsonResource;

class FlagResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);

        $data = DB::table('products')
                    ->join('categories', 'products.category_id', '=', 'categories.id')
                    ->leftJoin('subcategories', 'products.subcategory_id', '=', 'subcategories.id')
                    ->leftJoin('child_categories', 'products.childcategory_id', '=', 'child_categories.id')
                    ->leftJoin('units', 'products.unit_id', '=', 'units.id')
                    ->leftJoin('flags', 'products.flag_id', '=', 'flags.id')
                    ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
                    ->leftJoin('product_models', 'products.model_id', '=', 'product_models.id')
                    ->leftJoin('product_warrenties', 'products.warrenty_id', '=', 'product_warrenties.id')
                    ->select('products.*', 'categories.name as category_name', 'subcategories.name as subcategory_name', 'child_categories.name as childcategory_name', 'units.name as unit_name', 'flags.name as flag_name', 'brands.name as brand_name', 'product_models.name as model_name', 'product_warrenties.name as product_warrenty')
                    ->where('products.status', 1)
                    ->where('products.flag_id', $this->id)
                    ->orderBy('products.id', 'desc')
                    ->skip(0)
                    ->limit(10)
                    ->get();

        return [
            'id' => $this->id,
            'icon' => $this->icon,
            'name' => $this->name,
            'slug' => $this->slug,
            'status' => $this->status,
            'featured' => $this->featured,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'products' => ProductResource::collection($data)
        ];
    }
}
