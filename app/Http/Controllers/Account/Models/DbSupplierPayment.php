<?php

namespace App\Http\Controllers\Account\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DbSupplierPayment extends Model
{
    use HasFactory;
    protected $guarded = [];
    
    protected $table = 'db_supplier_payments';
    
    public function supplier()
    {
        return $this->belongsTo(\App\Http\Controllers\Inventory\Models\ProductSupplier::class, 'supplier_id');
    }
    
    public function purchasePayment()
    {
        return $this->belongsTo(DbPurchasePayment::class, 'purchasepayment_id');
    }
}
