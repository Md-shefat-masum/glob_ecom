<?php

namespace App\Http\Controllers\Customer\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class CustomerContactHistory extends Model
{
    use HasFactory;

    public function customer() {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function employee() {
        return $this->belongsTo(User::class, 'employee_id');
    }

}
