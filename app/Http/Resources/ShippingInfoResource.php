<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ShippingInfoResource extends JsonResource
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
            'order_id' => $this->order_id,
            'full_name' => $this->full_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'gender' => $this->gender,
            'address' => $this->address,
            'thana' => $this->thana,
            'post_code' => $this->post_code,
            'city' => $this->city,
            'country' => $this->country,
        ];
    }
}
