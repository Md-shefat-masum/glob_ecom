<?php

namespace App\Http\Controllers\Inventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\Inventory\Models\ProductWarehouse;
use App\Http\Controllers\Inventory\Models\ProductWarehouseRoom;

class ProductWarehouseRoomCartoon extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function productWarehouse() {
        return $this->belongsTo(ProductWarehouse::class, 'product_warehouse_id');
    }

    public function productWarehouseRoom() {
        return $this->belongsTo(ProductWarehouseRoom::class, 'product_warehouse_room_id');
    }

}
