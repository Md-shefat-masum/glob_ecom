<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PromoCodeResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'code' => $this->code,
            'effective_date' => $this->effective_date,
            'type' => $this->type,
            'type_text' => $this->type == 1 ? 'Amount' : 'Percentage',
            'value' => $this->value,
            'slug' => $this->slug,
            'status' => $this->status,
            'status_text' => $this->status == 1 ? 'Active' : 'Inactive',
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
