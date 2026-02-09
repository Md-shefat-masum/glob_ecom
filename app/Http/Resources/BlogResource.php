<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
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
            'category_id' => (int) $this->category_id,
            'image' => $this->image,
            'title' => $this->title,
            'short_description' => $this->short_description,
            'description' => $this->description,
            'tags' => $this->tags,
            'slug' => $this->slug,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'blog_category_name' => $this->blog_category_name,
            'category_slug' => $this->category_slug,
        ];
    }
}
