<?php

namespace App\Http\Controllers\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DbPurchasePayment extends Model
{
    use HasFactory;
    protected $guarded = []; 

    protected $table = 'db_purchasepayments';

}
