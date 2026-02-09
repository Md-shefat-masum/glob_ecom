<?php

namespace App\Models;

use App\Http\Controllers\Account\Models\AcAccount;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcEventMapping extends Model
{
    use HasFactory;

    protected $table = 'ac_event_mappings';

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the debit account
     */
    public function debitAccount()
    {
        return $this->belongsTo(AcAccount::class, 'debit_account_id');
    }

    /**
     * Get the credit account
     */
    public function creditAccount()
    {
        return $this->belongsTo(AcAccount::class, 'credit_account_id');
    }

    /**
     * Get the secondary debit account
     */
    public function secondaryDebitAccount()
    {
        return $this->belongsTo(AcAccount::class, 'secondary_debit_account_id');
    }

    /**
     * Get the secondary credit account
     */
    public function secondaryCreditAccount()
    {
        return $this->belongsTo(AcAccount::class, 'secondary_credit_account_id');
    }

    /**
     * Scope to get active mappings
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get mapping by event name
     */
    public static function getByEventName($eventName)
    {
        return static::where('event_name', $eventName)
            ->where('is_active', true)
            ->with(['debitAccount', 'creditAccount', 'secondaryDebitAccount', 'secondaryCreditAccount'])
            ->first();
    }
}
