<?php

namespace App\Http\Controllers\Courier;

use App\Http\Controllers\Controller;
use App\Models\ProductOrder;
use Illuminate\Http\Request;

class CourierController extends Controller
{
    /**
     * Show courier page for a specific order
     */
    public function showCourier($id)
    {
        $order = ProductOrder::with(['order_products', 'customer', 'warehouse'])->findOrFail($id);
        
        return view('backend.product_order_management.Courier', compact('order'));
    }
}
