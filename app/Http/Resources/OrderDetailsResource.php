<?php

namespace App\Http\Resources;

use App\Models\Color;
use App\Models\DeviceCondition;
use App\Models\Product;
use App\Models\ProductSize;
use App\Models\ProductWarrenty;
use App\Models\Sim;
use App\Models\StorageType;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailsResource extends JsonResource
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
        $prodInfo = Product::where('id', $this->product_id)->first();
        $unitInfo = Unit::where('id', $this->unit_id)->first();


        $colorInfo = Color::where('id', $this->color_id)->first();
        $colorName = $colorInfo ? $colorInfo->name : '';
        $sizeInfo = ProductSize::where('id', $this->size_id)->first();
        $sizeName = $sizeInfo ? $sizeInfo->name : '';
        $regionInfo = DB::table('country')->where('id', $this->region_id)->first();
        $regionName = $regionInfo ? $regionInfo->name : '';
        $simInfo = Sim::where('id', $this->sim_id)->first();
        $simName = $simInfo ? $simInfo->name : '';
        $storageInfo = StorageType::where('id', $this->storage_id)->first();
        $storageName = $storageInfo ? $storageInfo->ram."/".$storageInfo->rom : '';
        $warrentyInfo = ProductWarrenty::where('id', $this->warrenty_id)->first();
        $warrentyName = $warrentyInfo ? $warrentyInfo->name : '';
        $deviceConditionInfo = DeviceCondition::where('id', $this->device_condition_id)->first();
        $deviceConditionName = $deviceConditionInfo ? $deviceConditionInfo->name : '';


        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'product_id' => $this->product_id,
            'product_name' => $prodInfo ? $prodInfo->name : '',
            'product_image' => $prodInfo ? $prodInfo->image : '',
            'unit_id' => $this->unit_id,
            'unit_name' => $unitInfo ? $unitInfo->name : '',

            // variants
            'color_id' => $this->color_id,
            'color_name' => $colorName,
            'size_id' => $this->size_id,
            'size_name' => $sizeName,
            'region_id' => $this->region_id,
            'region_name' => $regionName,
            'sim_id' => $this->sim_id,
            'sim_name' => $simName,
            'storage_id' => $this->storage_id,
            'storage_name' => $storageName,
            'warrenty_id' => $this->warrenty_id,
            'warrenty_name' => $warrentyName,
            'device_condition_id' => $this->device_condition_id,
            'device_condition_name' => $deviceConditionName,

            'qty' => $this->qty,
            'unit_price' => $this->unit_price,
            'total_price' => $this->total_price,
        ];
    }
}
