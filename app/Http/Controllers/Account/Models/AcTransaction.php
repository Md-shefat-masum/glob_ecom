<?php

namespace App\Http\Controllers\Account\Models;

use App\Http\Controllers\Account\Models\AcAccount;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcTransaction extends Model
{
    use HasFactory;
    protected $guarded = []; 

    protected $table = "ac_transactions";

    // Relationship for debit account
    public function debitAccount() {
        return $this->belongsTo(AcAccount::class, 'debit_account_id');
    }

    // Relationship for credit account
    public function creditAccount() {
        return $this->belongsTo(AcAccount::class, 'credit_account_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'creator');
    }

}
