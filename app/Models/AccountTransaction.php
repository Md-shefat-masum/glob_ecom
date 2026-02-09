<?php

namespace App\Models;

use App\Http\Controllers\Inventory\Models\ProductPurchaseOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountTransaction extends Model
{
    use HasFactory;
    protected $table = 'ac_transactions';
    protected $guarded = [];

    public function debitAccount()
    {
        return $this->belongsTo(AccountTransaction::class, 'debit_account_id');
    }

    public function creditAccount()
    {
        return $this->belongsTo(AccountTransaction::class, 'credit_account_id');
    }

    public function purchse_info()
    {
        return $this->belongsTo(ProductPurchaseOrder::class, 'ref_purchase_id');
    }

    public function sale_info()
    {
        return $this->belongsTo(ProductOrder::class, 'ref_sale_id');
    }

    public function expense_info()
    {
        return $this->belongsTo(AccountExpense::class, 'ref_expense_id');
    }
}
