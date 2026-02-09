<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserCardResource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserCard;
use App\Models\Product;
use Carbon\Carbon;
use Image;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function product_details($slug){
        $data = Product::where(function($q) use($slug){
            $q->where('slug', $slug);
            $q->orWhere('id', $slug);
        })
            ->with([
                'variants' => function($q){
                    $q->with([
                        'size',
                        'color',    
                    ]);
                },    
            ])
            ->first();
            
        return response()->json($data);
    }
}