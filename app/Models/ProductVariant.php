<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;
    
    public function size(){
        return $this->belongsTo(ProductSize::class,'size_id');
    }
    
    public function color(){
        return $this->belongsTo(Color::class,'color_id');
    }
    
}
