<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class WishListController extends Controller
{
    public function customersWishlist(Request $request){
        if ($request->ajax()) {

            $data = DB::table('wish_lists')
                ->join('users', 'wish_lists.user_id', '=', 'users.id')
                ->join('products', 'wish_lists.product_id', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->select('wish_lists.*', 'categories.name as category_name', 'products.image', 'products.name as product_name', 'users.name as customer_name', 'users.email', 'users.phone')
                ->orderBy('wish_lists.id', 'desc')
                ->get();

            return Datatables::of($data)
                    ->editColumn('image', function($data) {
                        if($data->image && file_exists(public_path($data->image)))
                            return $data->image;
                    })
                    ->editColumn('created_at', function($data) {
                        return date("Y-m-d h:i:s a", strtotime($data->created_at));
                    })
                    ->addIndexColumn()
                    ->make(true);
        }
        return view('backend.wishlist.view');
    }
}
