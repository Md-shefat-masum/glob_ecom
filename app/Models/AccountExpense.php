<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountExpense extends Model
{
    use HasFactory;
    protected $table = 'ac_expenses';
    protected $guarded = [];

    public function expense_info() {
        return $this->belongsTo(AccountExpense::class, 'ref_expense_id');
    }
}
