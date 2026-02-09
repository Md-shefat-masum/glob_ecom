<?php

namespace App\Http\Resources;

use App\Models\Category;
use App\Models\ChildCategory;
use Illuminate\Http\Resources\Json\JsonResource;

class SubcategoryResource extends JsonResource
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

        return [
            'id' => $this->id,
            'stage' => 2,
            'category_id' => $this->category_id,
            'category_name' => $categoryInfo ? $categoryInfo->name : '',
            'category_slug' => $categoryInfo ? $categoryInfo->slug : '',
            'name' => $this->name,
            'icon' => $this->icon,
            'image' => $this->image,
            'slug' => $this->slug,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'children' => ChildcategoryResource::collection(ChildCategory::where('category_id', $this->category_id)->where('subcategory_id', $this->id)->get())
        ];
    }
}
