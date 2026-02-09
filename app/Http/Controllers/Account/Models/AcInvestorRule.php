<?php

namespace App\Http\Controllers\Account\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcInvestorRule extends Model
{
    use HasFactory;
    
    protected $guarded = [];
    protected $table = 'ac_investor_rules';

    /**
     * Relationship to investor (user with user_type = 5)
     */
    public function investor()
    {
        return $this->belongsTo(User::class, 'investor_id');
    }

    /**
     * Relationship to creator user
     */
    public function creator_info()
    {
        return $this->belongsTo(User::class, 'creator');
    }
}

