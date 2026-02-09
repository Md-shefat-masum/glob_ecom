<?php

namespace App\Http\Controllers\Account\Models;

use App\Http\Controllers\Outlet\Models\CustomerSourceType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcMoneyDeposit extends Model
{
    use HasFactory;
    protected $guarded = []; 
    protected $table = 'ac_moneydeposits';

    /**
     * Relationship to investor (user with user_type = 5)
     */
    public function investor()
    {
        return $this->belongsTo(User::class, 'investor_id');
    }

    /**
     * Relationship to payment type
     */
    public function paymentType()
    {
        return $this->belongsTo(DbPaymentType::class, 'payment_type_id');
    }

    /**
     * Relationship to debit account
     */
    public function debitAccount()
    {
        return $this->belongsTo(AcAccount::class, 'debit_account_id');
    }

    /**
     * Relationship to credit account
     */
    public function creditAccount()
    {
        return $this->belongsTo(AcAccount::class, 'credit_account_id');
    }

    /**
     * Relationship to creator user
     */
    public function creator_info()
    {
        return $this->belongsTo(User::class, 'creator');
    }

    /**
     * Get owner name (investor name or owner name)
     */
    public function getOwnerNameAttribute()
    {
        if ($this->investor_id && $this->investor) {
            return $this->investor->name;
        }
        
        // Get owner name from credit account if it's an investor account
        if ($this->creditAccount && strpos($this->creditAccount->account_name, 'investor_') === 0) {
            // Extract investor ID from account name
            $investorId = str_replace('investor_', '', $this->creditAccount->account_name);
            $investor = User::find($investorId);
            if ($investor) {
                return $investor->name;
            }
        }
        
        // Default to "Owner" if no investor
        return 'Owner';
    }
}
