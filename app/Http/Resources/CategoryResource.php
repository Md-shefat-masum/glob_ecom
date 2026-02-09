<?php

namespace App\Http\Resources;

use App\Models\Subcategory;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
        return [
            'id' => $this->id,
            'stage' => 1,
            'name' => $this->name,
            'icon' => $this->icon,
            'banner_image' => $this->banner_image,
            'slug' => $this->slug,
            'status' => $this->status,
            'featured' => $this->featured,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'children' => SubcategoryResource::collection(Subcategory::where('category_id', $this->id)->get())
        ];
    }
}
