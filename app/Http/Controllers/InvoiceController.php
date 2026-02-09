<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\User;
use App\Models\ShippingInfo;
use App\Models\BillingAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;

class InvoiceController extends Controller
{
    /**
     * Generate invoice for a POS order
     */
    public function generateInvoice($orderId)
    {
        try {
            $order = Order::findOrFail($orderId);
            
            // Check if order is from POS and completed
            if ($order->order_from != 3 || $order->complete_order != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only completed POS orders can have invoices generated'
                ]);
            }

            // Check if invoice already exists
            if (Invoice::hasInvoice($orderId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice already exists for this order'
                ]);
            }

            // Generate invoice using the Invoice model
            $invoice = Invoice::find($orderId);
            $invoice->markAsInvoiced();

            return response()->json([
                'success' => true,
                'message' => 'Invoice generated successfully',
                'invoice_no' => $invoice->invoice_no
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate invoice: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Display invoice details
     */
    public function showInvoice($orderId)
    {
        $order = Order::with(['shippingInfo', 'billingAddress'])->findOrFail($orderId);
        
        // Check if invoice exists
        if (!Invoice::hasInvoice($orderId)) {
            session()->flash('error', 'Invoice not found for this order');
            return redirect()->back();
        }

        // Get order details with product information
        $orderDetails = DB::table('order_details')
            ->leftJoin('products', 'order_details.product_id', '=', 'products.id')
            ->leftJoin('units', 'order_details.unit_id', '=', 'units.id')
            ->leftJoin('colors', 'order_details.color_id', '=', 'colors.id')
            ->leftJoin('product_sizes', 'order_details.size_id', '=', 'product_sizes.id')
            ->select(
                'order_details.*',
                'products.name as product_name',
                'products.code as product_code',
                'units.name as unit_name',
                'colors.name as color_name',
                'product_sizes.name as size_name'
            )
            ->where('order_details.order_id', $orderId)
            ->get();

        // Get customer information
        $customer = null;
        if ($order->user_id) {
            $customer = User::find($order->user_id);
        }

        // Get company/general information
        $generalInfo = DB::table('general_infos')
            ->select('logo', 'logo_dark', 'company_name', 'address', 'email')
            ->first();

        return view('backend.invoices.show', compact(
            'order', 
            'orderDetails', 
            'customer', 
            'generalInfo'
        ));
    }

    /**
     * Print invoice
     */
    public function printInvoice($orderId)
    {
        $order = Order::with(['shippingInfo', 'billingAddress'])->findOrFail($orderId);
        
        // Check if invoice exists
        if (!Invoice::hasInvoice($orderId)) {
            session()->flash('error', 'Invoice not found for this order');
            return redirect()->back();
        }

        // Get order details with product information
        $orderDetails = DB::table('order_details')
            ->leftJoin('products', 'order_details.product_id', '=', 'products.id')
            ->leftJoin('units', 'order_details.unit_id', '=', 'units.id')
            ->leftJoin('colors', 'order_details.color_id', '=', 'colors.id')
            ->leftJoin('product_sizes', 'order_details.size_id', '=', 'product_sizes.id')
            ->select(
                'order_details.*',
                'products.name as product_name',
                'products.code as product_code',
                'units.name as unit_name',
                'colors.name as color_name',
                'product_sizes.name as size_name'
            )
            ->where('order_details.order_id', $orderId)
            ->get();

        // Get customer information
        $customer = null;
        if ($order->user_id) {
            $customer = User::find($order->user_id);
        }

        // Get company/general information
        $generalInfo = DB::table('general_infos')
            ->select('logo', 'logo_dark', 'company_name', 'address', 'email')
            ->first();

        return view('backend.invoices.print', compact(
            'order', 
            'orderDetails', 
            'customer', 
            'generalInfo'
        ));
    }

    /**
     * Print POS invoice - Thermal printer friendly format
     */
    public function posInvoicePrint($orderId)
    {
        $order = Order::with(['shippingInfo', 'billingAddress'])->findOrFail($orderId);
        
        // Auto-generate invoice if it doesn't exist
        if (!Invoice::hasInvoice($orderId)) {
            $invoice = Invoice::find($orderId);
            $invoice->markAsInvoiced();
        }

        // Get order details with product information
        $orderDetails = DB::table('order_details')
            ->leftJoin('products', 'order_details.product_id', '=', 'products.id')
            ->leftJoin('units', 'order_details.unit_id', '=', 'units.id')
            ->leftJoin('colors', 'order_details.color_id', '=', 'colors.id')
            ->leftJoin('product_sizes', 'order_details.size_id', '=', 'product_sizes.id')
            ->select(
                'order_details.*',
                'products.name as product_name',
                'products.code as product_code',
                'units.name as unit_name',
                'colors.name as color_name',
                'product_sizes.name as size_name'
            )
            ->where('order_details.order_id', $orderId)
            ->get();

        // Get customer information
        $customer = null;
        if ($order->user_id) {
            $customer = User::find($order->user_id);
        }

        // Get company/general information
        $generalInfo = DB::table('general_infos')
            ->select('logo', 'logo_dark', 'company_name', 'address', 'email', 'contact')
            ->first();

        return view('backend.orders.pos.invoice_print', compact(
            'order', 
            'orderDetails', 
            'customer', 
            'generalInfo'
        ));
    }

    /**
     * Get printable content for POS invoice (AJAX)
     */
    public function getPrintableContent($orderId)
    {
        $order = Order::with(['shippingInfo', 'billingAddress'])->findOrFail($orderId);
        
        // Auto-generate invoice if it doesn't exist
        if (!Invoice::hasInvoice($orderId)) {
            $invoice = Invoice::find($orderId);
            $invoice->markAsInvoiced();
        }

        // Get order details with product information
        $orderDetails = DB::table('order_details')
            ->leftJoin('products', 'order_details.product_id', '=', 'products.id')
            ->leftJoin('units', 'order_details.unit_id', '=', 'units.id')
            ->leftJoin('colors', 'order_details.color_id', '=', 'colors.id')
            ->leftJoin('product_sizes', 'order_details.size_id', '=', 'product_sizes.id')
            ->select(
                'order_details.*',
                'products.name as product_name',
                'products.code as product_code',
                'units.name as unit_name',
                'colors.name as color_name',
                'product_sizes.name as size_name'
            )
            ->where('order_details.order_id', $orderId)
            ->get();

        // Get customer information
        $customer = null;
        if ($order->user_id) {
            $customer = User::find($order->user_id);
        }

        // Get company/general information
        $generalInfo = DB::table('general_infos')
            ->select('logo', 'logo_dark', 'company_name', 'address', 'email', 'contact')
            ->first();

        return view('backend.orders.pos.invoice_content_only', compact(
            'order', 
            'orderDetails', 
            'customer', 
            'generalInfo'
        ));
    }

    /**
     * List all invoices
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('orders')
                ->leftJoin('shipping_infos', 'shipping_infos.order_id', '=', 'orders.id')
                ->select(
                    'orders.*',
                    'shipping_infos.full_name as customer_name',
                    'shipping_infos.phone as customer_phone'
                )
                ->where('orders.order_from', 3) // POS orders only
                ->where('orders.invoice_generated', 1) // Only invoiced orders
                ->orderBy('orders.id', 'desc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('invoice_date', function ($data) {
                    return date('d M Y', strtotime($data->invoice_date));
                })
                ->editColumn('total', function ($data) {
                    return "৳ " . number_format($data->total, 2);
                })
                ->addColumn('action', function ($data) {
                    $btn = '';
                    $btn .= '<a href="' . route('ShowInvoice', $data->id) . '" class="btn btn-sm btn-primary mr-1"><i class="fas fa-eye"></i> View</a>';
                    $btn .= '<a href="javascript:void(0)" onclick="printInvoiceInline(' . $data->id . ')" class="btn btn-sm btn-success mr-1"><i class="fas fa-print"></i> Print</a>';
                    // $btn .= '<a href="' . route('POSInvoicePrint', $data->id) . '" target="_blank" class="btn btn-sm btn-info mr-1"><i class="fas fa-external-link-alt"></i> Open & Print</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('backend.invoices.index');
    }

    /**
     * Auto-generate invoice for completed POS order
     */
    public static function autoGenerateInvoice($orderId)
    {
        try {
            $order = Order::find($orderId);
            
            // Check if it's a POS order and completed
            if ($order && $order->order_from == 3 && $order->complete_order == 1) {
                // Check if invoice doesn't already exist
                if (!Invoice::hasInvoice($orderId)) {
                    $invoice = Invoice::find($orderId);
                    $invoice->markAsInvoiced();
                    
                    return [
                        'success' => true,
                        'invoice_no' => $invoice->invoice_no
                    ];
                }
            }
            
            return ['success' => false, 'message' => 'Conditions not met for invoice generation'];
            
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
