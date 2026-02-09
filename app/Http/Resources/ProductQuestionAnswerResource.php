<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductQuestionAnswerResource extends JsonResource
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
            'product_id' => $this->product_id,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'question' => $this->question,
            'answer' => $this->answer,
            'submitted_at' => $this->created_at,
            'replied_at' => $this->updated_at,
        ];
    }
}
