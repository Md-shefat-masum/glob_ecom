<?php

namespace App\Http\Controllers\Customer\Models;

use App\Http\Controllers\Outlet\Models\CustomerSourceType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function customerCategory() {
        return $this->belongsTo(CustomerCategory::class, 'customer_category_id');
    }

    public function customerSourceType() {
        return $this->belongsTo(CustomerSourceType::class, 'customer_source_type_id');
    }

    public function referenceBy() {
        return $this->belongsTo(User::class, 'reference_by');
    }


}
