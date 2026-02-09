<?php

namespace App\Http\Controllers\Account\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DbPaymentType extends Model
{
    use HasFactory;
    protected $guarded = []; 

    protected $table = "db_paymenttypes";

    protected $appends = ['total_amount'];

    public function user() {
        return $this->belongsTo(User::class, 'creator');
    }

    /**
     * Relationship to accounts that use this payment type
     */
    public function accounts()
    {
        return $this->hasMany(AcAccount::class, 'paymenttypes_id');
    }

    /**
     * Calculate total amount for this payment type
     * Sums up balances from all accounts linked to this payment type
     */
    public function getTotalAmountAttribute()
    {
        $totalAmount = 0;

        // Get all accounts linked to this payment type
        $accounts = AcAccount::where('paymenttypes_id', $this->id)->get();

        foreach ($accounts as $account) {
            // Calculate debits and credits for this account (only active transactions)
            $debits = AcTransaction::where('debit_account_id', $account->id)
                ->where('status', 'active')
                ->sum('debit_amt') ?? 0;

            $credits = AcTransaction::where('credit_account_id', $account->id)
                ->where('status', 'active')
                ->sum('credit_amt') ?? 0;

            // Calculate balance based on account type
            $balance = 0;
            if ($account->account_type === 'asset' || $account->account_type === 'expense') {
                // For asset and expense accounts: balance = debits - credits
                $balance = $debits - $credits;
            } elseif ($account->account_type === 'liability' || $account->account_type === 'equity' || $account->account_type === 'revenue') {
                // For liability, equity, and revenue accounts: balance = credits - debits
                $balance = $credits - $debits;
            } else {
                // Default: debits - credits
                $balance = $debits - $credits;
            }

            $totalAmount += $balance;
        }

        return $totalAmount;
    }

}
