<?php

namespace App\Http\Controllers\Customer\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerNextContactDate extends Model
{
    use HasFactory;

    protected $guarded = [];
    public function customer() {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    
}
