<?php

namespace App\Http\Resources;

use App\Models\Subcategory;
use App\Models\Category;
use Illuminate\Http\Resources\Json\JsonResource;

class ChildcategoryResource extends JsonResource
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
        $categoryInfo = Category::where('id', $this->category_id)->first();
        $subCategoryInfo = Subcategory::where('id', $this->subcategory_id)->first();

        return [
            'id' => $this->id,
            'stage' => 3,
            'category_id' => $this->category_id,
            'category_name' => $categoryInfo ? $categoryInfo->name : '',
            'category_slug' => $categoryInfo ? $categoryInfo->slug : '',
            'subcategory_id' => $this->subcategory_id,
            'subcategory_name' => $subCategoryInfo ? $subCategoryInfo->name : '',
            'subcategory_slug' => $subCategoryInfo ? $subCategoryInfo->slug : '',
            'name' => $this->name,
            'slug' => $this->slug,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
