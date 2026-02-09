<?php

namespace App\Http\Resources;

use App\Models\BillingAddress;
use App\Models\OrderDetails;
use App\Models\ShippingInfo;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'order_no' => $this->order_no,
            'user_id' => $this->user_id,
            'order_date' => $this->order_date,
            'estimated_dd' => $this->estimated_dd,
            'delivery_date' => $this->delivery_date,
            'delivery_method' => $this->delivery_method,
            'delivery_method_text' => $this->delivery_method == 1 ? "Home Delivery" : ($this->delivery_method == 2 ? "Store Pickup" : ""),
            'payment_method' => $this->payment_method,
            'payment_method_text' => $this->payment_method == NULL ? "Unpaid" : ($this->payment_method == 1 ? "COD" : ($this->payment_method == 2 ? "bKash" : ($this->payment_method == 3 ? "Nagad" : 'Card'))),
            'payment_status' => $this->payment_status,
            'payment_status_text' => $this->payment_status == 0 ? "Unpaid" : ($this->payment_status == 1 ? "Payment Successful" : 'Payment Failed'),
            'order_status' => $this->order_status,
            'order_status_text' => $this->order_status == 0 ? "Pending" : ($this->order_status == 1 ? "Approved" : ($this->order_status == 2 ? "Intransit" : ($this->order_status == 3 ? "Delivered" : 'Cancelled'))),
            'sub_total' => $this->sub_total,
            'coupon_code' => $this->coupon_code,
            'discount' => $this->discount,
            'delivery_fee' => $this->delivery_fee,
            'vat' => $this->vat,
            'tax' => $this->tax,
            'total' => $this->total,
            'order_note' => $this->order_note,
            'slug' => $this->slug,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'ordered_items' => OrderDetailsResource::collection(OrderDetails::where('order_id', $this->id)->get()),
            'shipping_info' => new ShippingInfoResource(ShippingInfo::where('order_id', $this->id)->first()),
            'billing_address' => new BillingAddressResource(BillingAddress::where('order_id', $this->id)->first()),
        ];
    }
}
