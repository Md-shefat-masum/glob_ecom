<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductReviewResource extends JsonResource
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
        $userInfo = User::where("id", $this->user_id)->first();
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'user_id' => $this->user_id,
            'user_name' => $userInfo ? $userInfo->name : '',
            'user_image' => $userInfo ? $userInfo->image : '',
            'rating' => $this->rating,
            'review' => $this->review,
            'reply' => $this->reply,
            'status' => $this->status,
            'slug' => $this->slug,
            'created_at' => $this->created_at,
        ];
    }
}
