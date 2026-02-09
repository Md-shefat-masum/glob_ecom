<?php

namespace App\Http\Controllers\Outlet\Models;

use App\Http\Controllers\Customer\Models\Customer;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerSourceType extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function orders() {
        return $this->hasMany(Order::class, 'customer_src_type_id');
    }

    public function customers() {
        return $this->hasMany(Customer::class, 'customer_source_type_id');
    }

}
