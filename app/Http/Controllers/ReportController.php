<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Inventory\Models\ProductPurchaseOrder;
use App\Models\Order;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function salesReport(){
        return view('backend.report.sales_report');
    }

    public function generateSalesReport(Request $request){

        $startDate = date("Y-m-d", strtotime(str_replace("/","-",$request->start_date)))." 00:00:00";
        $endDate = date("Y-m-d", strtotime(str_replace("/","-",$request->end_date)))." 23:59:59";
        $orderStatus = $request->order_status;
        $paymentStatus = $request->payment_status;
        $paymentMethod = $request->payment_method;

        $query = Order::query();
        $query->whereBetween('order_date', [$startDate, $endDate]);

        if ($orderStatus != '') {
            $query->where('order_status', $orderStatus);
        }
        if ($paymentStatus != '') {
            $query->where('payment_status', $paymentStatus);
        }
        if ($paymentMethod != '') {
            $query->where('payment_method', $paymentMethod);
        }
        $data = $query->orderBy('id', 'desc')->get();

        $returnHTML = view('backend.report.sales_report_view', compact('startDate', 'endDate', 'data'))->render();
        return response()->json(['variant' => $returnHTML]);

    }

    public function productPurchaseReport(Request $request){
        return view('backend.report.product_purchase_report');
    }
    public function generateProductPurchaseReport(Request $request){

        $startDate = date("Y-m-d", strtotime(str_replace("/","-",$request->start_date)))." 00:00:00";
        $endDate = date("Y-m-d", strtotime(str_replace("/","-",$request->end_date)))." 23:59:59";
      
        $query = ProductPurchaseOrder::query();
        $query->whereBetween('date', [$startDate, $endDate])
        ->leftJoin('product_warehouses', 'product_purchase_orders.product_warehouse_id', '=', 'product_warehouses.id')
        ->leftJoin('product_warehouse_rooms', 'product_purchase_orders.product_warehouse_room_id', '=', 'product_warehouse_rooms.id')
        ->leftJoin('product_warehouse_room_cartoons', 'product_purchase_orders.product_warehouse_room_cartoon_id', '=', 'product_warehouse_room_cartoons.id')
        ->leftJoin('product_suppliers', 'product_purchase_orders.product_supplier_id', '=', 'product_suppliers.id')
        ->orderBy('product_purchase_orders.total', 'asc')
        ->select(
            'product_purchase_orders.*',
            'product_warehouses.title as warehouse_name',
            'product_warehouse_rooms.title as room_name',
            'product_warehouse_room_cartoons.title as cartoon_name',
            'product_suppliers.name as supplier_name'
        );
                
        $data = $query->orderBy('id', 'desc')->get();

        $returnHTML = view('backend.report.product_purchase_report_view', compact('startDate', 'endDate', 'data'))->render();
        return response()->json(['variant' => $returnHTML]);
    }
}
