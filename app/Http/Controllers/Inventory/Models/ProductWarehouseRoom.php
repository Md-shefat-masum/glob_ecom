<?php

namespace App\Http\Controllers\Inventory\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\Inventory\Models\ProductWarehouse;
use App\Http\Controllers\Inventory\Models\ProductWarehouseRoomCartoon;

class ProductWarehouseRoom extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function productWarehouse() {
        return $this->belongsTo(ProductWarehouse::class, 'product_warehouse_id');
    }

    public function productWarehouseRoomCartoon() {
        return $this->hasMany(ProductWarehouseRoomCartoon::class);
    }

}
