<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSalesTarget extends Model
{
    use HasFactory;

    protected $table = 'user_sales_targets';

    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'target' => 'decimal:2',
        'completed' => 'decimal:2',
        'remains' => 'decimal:2',
        'is_evaluated' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
