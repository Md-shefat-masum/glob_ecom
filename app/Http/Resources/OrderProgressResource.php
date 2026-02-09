<?php

namespace App\Http\Resources;

use App\Models\BillingAddress;
use App\Models\Order;
use App\Models\ShippingInfo;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderProgressResource extends JsonResource
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
        $shippingInfo = ShippingInfo::where("order_id", $this->order_id)->first();
        $billingInfo = BillingAddress::where("order_id", $this->order_id)->first();

        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'order_no' => Order::where("id", $this->order_id)->first()->order_no,
            'estimated_dd' => Order::where("id", $this->order_id)->first()->estimated_dd,
            'shipping_address' => $shippingInfo ? $shippingInfo->address : '',
            'billing_address' => $billingInfo ? $billingInfo->address : '',
            'order_status' => $this->order_status,
            'order_status_text' => $this->order_status == 0 ? "Pending" : ($this->order_status == 1 ? "Approved" : ($this->order_status == 2 ? "Intransit" : ($this->order_status == 3 ? "Delivered" : 'Cancelled'))),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
