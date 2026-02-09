<?php

namespace App\Http\Controllers\Account\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountIncome extends Model
{
    use HasFactory;
    protected $guarded = []; 

    protected $table = "ac_incomes";

    public function user() {
        return $this->belongsTo(User::class, 'creator');
    }

    public function income_category() {
        return $this->belongsTo(AccountIncomeCategory::class, 'category_id');
    }

    public function debitAccount() {
        return $this->belongsTo(AcAccount::class, 'debit_account_id');
    }

    public function creditAccount() {
        return $this->belongsTo(AcAccount::class, 'credit_account_id');
    }
}

