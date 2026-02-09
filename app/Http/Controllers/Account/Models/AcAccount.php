<?php

namespace App\Http\Controllers\Account\Models;

use App\Http\Controllers\Outlet\Models\CustomerSourceType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcAccount extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $appends = ['text'];

    protected $table = "ac_accounts";

    /* 
        id
        count_id
        store_id
        parent_id Ascending 1
        account_type
        normal_balance
        is_system_account
        is_control_account
        sort_code
        account_name
        account_code
        short_code
        balance
        note
        created_time
        system_ip
        system_name
        delete_bit
        account_selection_name
        paymenttypes_id
        customer_id
        supplier_id
        expense_id
        creator
        slug
        status
        created_at
        updated_at
    */

    public function user()
    {
        return $this->belongsTo(User::class, 'creator');
    }

    public function debitTransactions()
    {
        return $this->hasMany(AcTransaction::class, 'debit_account_id');
    }

    public function creditTransactions()
    {
        return $this->hasMany(AcTransaction::class, 'credit_account_id');
    }


    // Relationship to fetch children accounts
    public function children()
    {
        return $this->hasMany(AcAccount::class, 'parent_id');
    }

    // Relationship to fetch parent account
    public function parent()
    {
        return $this->belongsTo(AcAccount::class, 'parent_id');
    }

    public function getTextAttribute()
    {
        return $this->account_name;
    }

    public function inc()
    {
        return $this->hasMany(AcAccount::class, 'parent_id')->with('inc');
    }

    // Relationships for event mappings
    public function eventMappingsAsDebit()
    {
        return $this->hasMany(\App\Models\AcEventMapping::class, 'debit_account_id');
    }

    public function eventMappingsAsCredit()
    {
        return $this->hasMany(\App\Models\AcEventMapping::class, 'credit_account_id');
    }

    public function eventMappingsAsSecondaryDebit()
    {
        return $this->hasMany(\App\Models\AcEventMapping::class, 'secondary_debit_account_id');
    }

    public function eventMappingsAsSecondaryCredit()
    {
        return $this->hasMany(\App\Models\AcEventMapping::class, 'secondary_credit_account_id');
    }
}
