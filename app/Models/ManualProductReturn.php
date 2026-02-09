<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\Customer\Models\Customer;

class ManualProductReturn extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Get the return items
     */
    public function return_items()
    {
        return $this->hasMany(ManualProductReturnItem::class, 'manual_product_return_id');
    }

    /**
     * Get the customer
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Get the creator
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator');
    }
}

