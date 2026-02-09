<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Customer\Models\Customer;
use App\Http\Controllers\Inventory\Models\ProductWarehouse;
use App\Models\Product;
use App\Models\ProductOrder;
use App\Models\ProductOrderProduct;
use App\Models\ProductOrderReturn;
use App\Models\ProductOrderReturnProduct;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Brian2694\Toastr\Facades\Toastr;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductOrderReturnController extends Controller
{
    /**
     * Display a listing of returns
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ProductOrderReturn::with(['creator', 'return_products', 'customer', 'originalOrder'])
                ->orderBy('id', 'desc')
                ->get();

            return Datatables::of($data)
                ->editColumn('return_date', function ($data) {
                    return date("Y-m-d", strtotime($data->return_date));
                })
                ->editColumn('customer', function ($data) {
                    return $data->customer ? $data->customer->name : 'N/A';
                })
                ->editColumn('original_order', function ($data) {
                    return $data->originalOrder ? $data->originalOrder->order_code : 'N/A';
                })
                ->editColumn('status', function ($data) {
                    return $data->status == "active" ? 'Active' : 'Inactive';
                })
                ->editColumn('return_status', function ($data) {
                    $badge = '';
                    if ($data->return_status == 'approved') {
                        $badge = '<span class="badge badge-success">Approved</span>';
                    } elseif ($data->return_status == 'pending') {
                        $badge = '<span class="badge badge-warning">Pending</span>';
                    } else {
                        $badge = '<span class="badge badge-danger">Rejected</span>';
                    }
                    return $badge;
                })
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $btn = '<div class="dropdown">';
                    $btn .= '<button class="btn-sm btn-primary dropdown-toggle rounded" type="button" id="actionDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                    $btn .= '<i class="fas fa-cog"></i> Actions';
                    $btn .= '</button>';
                    $btn .= '<div class="dropdown-menu" aria-labelledby="actionDropdown">';
                    
                    // View Return
                    $btn .= '<a class="dropdown-item" href="' . route('ShowProductOrderReturn', $data->slug) . '" target="_blank"><i class="fas fa-eye text-info"></i> View Return</a>';
                    
                    // Edit
                    $btn .= '<a class="dropdown-item" href="' . route('EditProductOrderReturn', $data->slug) . '"><i class="fas fa-edit text-warning"></i> Edit</a>';
                    
                    // Print
                    $btn .= '<a class="dropdown-item" href="' . route('PrintProductOrderReturn', $data->slug) . '" target="_blank"><i class="fas fa-print text-primary"></i> Print</a>';
                    
                    // Delete
                    $btn .= '<div class="dropdown-divider"></div>';
                    $btn .= '<a class="dropdown-item deleteBtn" href="javascript:void(0)" data-toggle="tooltip" data-id="' . $data->slug . '" data-original-title="Delete"><i class="fas fa-trash-alt text-danger"></i> Delete</a>';
                    
                    $btn .= '</div>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action', 'return_status'])
                ->make(true);
        }
        return view('backend.product_order_return.index');
    }

    /**
     * Show the form for creating a new return
     */
    public function create($slug)
    {
        $order = ProductOrder::with(['order_products', 'customer', 'warehouse'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Check if order is invoiced or delivered
        if ($order->order_status != 'invoiced' && $order->order_status != 'delivered') {
            Toastr::warning('Only invoiced/delivered orders can be returned!', 'Warning');
            return redirect()->route('ViewAllProductOrder');
        }

        // Get available return quantities for each product
        $availableQuantities = $this->getAvailableReturnQuantities($order->id);

        // Check if any products are available for return
        $hasAvailableProducts = false;
        foreach ($availableQuantities as $qty) {
            if ($qty > 0) {
                $hasAvailableProducts = true;
                break;
            }
        }

        if (!$hasAvailableProducts) {
            Toastr::warning('All products from this order have already been returned!', 'Warning');
            return redirect()->route('ViewAllProductOrder');
        }

        $return_code = $this->generateReturnCode();
        
        return view('backend.product_order_return.create', compact('order', 'availableQuantities', 'return_code'));
    }

    /**
     * Store a newly created return
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_order_id' => ['required', 'exists:product_orders,id'],
            'return_date' => ['required', 'date'],
            'return_code' => ['required', 'unique:product_order_returns,return_code'],
            'return_products' => 'required|array|min:1',
            'refund_method' => 'required|in:cash,bkash,rocket,nogod,bank,cheque,advance_payment',
        ], [
            'return_products.required' => 'No products selected for return.',
            'return_code.unique' => 'Return code already exists.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $order = ProductOrder::with('order_products')->findOrFail($request->product_order_id);

            // Validate return quantities
            $availableQuantities = $this->getAvailableReturnQuantities($order->id);
            
            foreach ($request->return_products as $returnProduct) {
                $productId = $returnProduct['product_id'];
                $returnQty = $returnProduct['qty'];
                
                if ($returnQty > ($availableQuantities[$productId] ?? 0)) {
                    DB::rollBack();
                    $product = Product::find($productId);
                    return response()->json([
                        'success' => false,
                        'message' => "Return quantity exceeds available quantity for {$product->name}"
                    ], 422);
                }
            }

            $random_no = random_int(100, 999) . random_int(1000, 9999);
            $slug = Str::orderedUuid() . uniqid() . $random_no;
            $user = auth()->user();

            // Create return record
            $return = new ProductOrderReturn();
            $return->product_order_id = $order->id;
            $return->return_code = $request->return_code;
            $return->product_warehouse_id = $order->product_warehouse_id;
            $return->product_warehouse_room_id = $order->product_warehouse_room_id;
            $return->product_warehouse_room_cartoon_id = $order->product_warehouse_room_cartoon_id;
            $return->customer_id = $order->customer_id;
            $return->return_date = $request->return_date;
            $return->return_reason = $request->return_reason;
            
            $return->other_charges = $request->other_charges ?? [];
            $return->other_charge_amount = $request->other_charge_amount ?? 0;
            
            $return->discount_type = $request->discount_type;
            $return->discount_amount = $request->discount_amount ?? 0;
            $return->calculated_discount_amount = $request->calculated_discount_amount ?? 0;
            
            $return->round_off_from_total = $request->round_off_from_total ?? 0;
            $return->decimal_round_off = $request->decimal_round_off ?? 0;
            
            $return->subtotal = $request->subtotal_amt ?? 0;
            $return->total = $request->grand_total_amt ?? 0;
            
            $return->refund_method = $request->refund_method;
            $return->refund_status = 'completed';
            $return->return_status = 'approved';
            
            $return->note = $request->note;
            $return->creator = $user->id;
            $return->status = 'active';
            $return->created_at = Carbon::now();
            $return->save();

            // Create return products
            foreach ($request->return_products as $returnProduct) {
                $product_slug = Str::orderedUuid() . $random_no . $return->id . uniqid();
                $product = Product::find($returnProduct['product_id']);

                ProductOrderReturnProduct::create([
                    'product_order_return_id' => $return->id,
                    'product_order_product_id' => $returnProduct['order_product_id'] ?? null,
                    'product_warehouse_id' => $order->product_warehouse_id,
                    'product_warehouse_room_id' => $order->product_warehouse_room_id,
                    'product_warehouse_room_cartoon_id' => $order->product_warehouse_room_cartoon_id,
                    'product_id' => $returnProduct['product_id'],
                    'product_name' => $product->name,
                    'qty' => $returnProduct['qty'],
                    'sale_price' => $returnProduct['sale_price'],
                    'discount_type' => $returnProduct['discount_type'] ?? 'in_percentage',
                    'discount_amount' => $returnProduct['discount_amount'] ?? 0,
                    'tax' => $returnProduct['tax'] ?? 0,
                    'total_price' => $returnProduct['total_price'],
                    'product_price' => $product->discount_price ? $product->discount_price : $product->price,
                    'slug' => $product_slug,
                    'creator' => $user->id,
                ]);

                // Update product stock (add back)
                $product->stock += $returnProduct['qty'];
                $product->save();

                // Insert stock log
                insert_stock_log([
                    'warehouse_id' => $order->product_warehouse_id,
                    'product_id' => $returnProduct['product_id'],
                    'product_name' => $product->name,
                    'product_return_id' => $return->id,
                    'quantity' => $returnProduct['qty'],
                    'type' => 'return',
                ]);
            }

            $return->slug = $return->id . $slug;
            $return->save();

            // Record accounting for return (customer gets advance payment)
            $accountingResult = record_sales_accounting($order, 'return_partial', [
                'return' => $return,
                'return_amount' => $return->total
            ]);

            if (!$accountingResult['success']) {
                logger()->warning('Return accounting partially failed', [
                    'return_id' => $return->id,
                    'message' => $accountingResult['message']
                ]);
            }

            DB::commit();

            Toastr::success('Return has been created successfully!', 'Success');
            return response()->json([
                'success' => true,
                'message' => 'Return has been created successfully!',
                'return' => $return,
                'redirect' => route('ViewAllProductOrderReturns')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error('Something went wrong! ' . $e->getMessage(), 'Error');
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong! ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified return
     */
    public function show($slug)
    {
        $return = ProductOrderReturn::with(['return_products', 'customer', 'warehouse', 'originalOrder'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Company information
        $company = [
            'name' => 'BME Trading Company',
            'address' => '123 Business District, Dhaka-1000, Bangladesh',
            'phone' => '+880 1700-000000',
            'email' => 'info@bmetrading.com',
            'website' => 'www.bmetrading.com',
            'logo' => '/logo.png'
        ];

        $qrData = "Return: {$return->return_code}\nDate: {$return->return_date}\nTotal: {$return->total} BDT";

        return view('backend.product_order_return.show', compact('return', 'company', 'qrData'));
    }

    /**
     * Show the form for editing the specified return
     */
    public function edit($slug)
    {
        $returnData = ProductOrderReturn::with(['return_products', 'originalOrder.order_products'])
            ->where('slug', $slug)
            ->firstOrFail();

        $order = $returnData->originalOrder;

        // Get available return quantities (excluding current return)
        $availableQuantities = $this->getAvailableReturnQuantities($order->id, $returnData->id);

        return view('backend.product_order_return.edit', compact('returnData', 'order', 'availableQuantities'));
    }

    /**
     * Update the specified return
     */
    public function update(Request $request, $slug)
    {
        $validator = Validator::make($request->all(), [
            'product_order_return_id' => ['required', 'exists:product_order_returns,id'],
            'return_date' => ['required', 'date'],
            'return_products' => 'required|array|min:1',
            'refund_method' => 'required|in:cash,bkash,rocket,nogod,bank,cheque,advance_payment',
        ], [
            'return_products.required' => 'No products selected for return.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $return = ProductOrderReturn::findOrFail($request->product_order_return_id);
            $order = ProductOrder::with('order_products')->findOrFail($return->product_order_id);

            // Validate return quantities (excluding current return)
            $availableQuantities = $this->getAvailableReturnQuantities($order->id, $return->id);
            
            foreach ($request->return_products as $returnProduct) {
                $productId = $returnProduct['product_id'];
                $returnQty = $returnProduct['qty'];
                
                if ($returnQty > ($availableQuantities[$productId] ?? 0)) {
                    DB::rollBack();
                    $product = Product::find($productId);
                    return response()->json([
                        'success' => false,
                        'message' => "Return quantity exceeds available quantity for {$product->name}"
                    ], 422);
                }
            }

            // Reverse previous stock changes
            foreach ($return->return_products as $oldProduct) {
                $product = Product::find($oldProduct->product_id);
                if ($product) {
                    $product->stock -= $oldProduct->qty; // Remove the previously added stock
                    $product->save();
                }
            }

            // Delete old return products
            ProductOrderReturnProduct::where('product_order_return_id', $return->id)->delete();

            $user = auth()->user();

            // Update return record
            $return->return_date = $request->return_date;
            $return->return_reason = $request->return_reason;
            
            $return->other_charges = $request->other_charges ?? [];
            $return->other_charge_amount = $request->other_charge_amount ?? 0;
            
            $return->discount_type = $request->discount_type;
            $return->discount_amount = $request->discount_amount ?? 0;
            $return->calculated_discount_amount = $request->calculated_discount_amount ?? 0;
            
            $return->round_off_from_total = $request->round_off_from_total ?? 0;
            $return->decimal_round_off = $request->decimal_round_off ?? 0;
            
            $return->subtotal = $request->subtotal_amt ?? 0;
            $return->total = $request->grand_total_amt ?? 0;
            
            $return->refund_method = $request->refund_method;
            $return->note = $request->note;
            $return->updated_at = Carbon::now();
            $return->save();

            $random_no = random_int(100, 999) . random_int(1000, 9999);

            // Create new return products and update stock
            foreach ($request->return_products as $returnProduct) {
                $product_slug = Str::orderedUuid() . $random_no . $return->id . uniqid();
                $product = Product::find($returnProduct['product_id']);

                ProductOrderReturnProduct::create([
                    'product_order_return_id' => $return->id,
                    'product_order_product_id' => $returnProduct['order_product_id'] ?? null,
                    'product_warehouse_id' => $order->product_warehouse_id,
                    'product_warehouse_room_id' => $order->product_warehouse_room_id,
                    'product_warehouse_room_cartoon_id' => $order->product_warehouse_room_cartoon_id,
                    'product_id' => $returnProduct['product_id'],
                    'product_name' => $product->name,
                    'qty' => $returnProduct['qty'],
                    'sale_price' => $returnProduct['sale_price'],
                    'discount_type' => $returnProduct['discount_type'] ?? 'in_percentage',
                    'discount_amount' => $returnProduct['discount_amount'] ?? 0,
                    'tax' => $returnProduct['tax'] ?? 0,
                    'total_price' => $returnProduct['total_price'],
                    'product_price' => $product->discount_price ? $product->discount_price : $product->price,
                    'slug' => $product_slug,
                    'creator' => $user->id,
                ]);

                // Update product stock (add back)
                $product->stock += $returnProduct['qty'];
                $product->save();

                // Insert stock log
                insert_stock_log([
                    'warehouse_id' => $order->product_warehouse_id,
                    'product_id' => $returnProduct['product_id'],
                    'product_name' => $product->name,
                    'product_return_id' => $return->id,
                    'quantity' => $returnProduct['qty'],
                    'type' => 'return',
                ]);
            }

            DB::commit();

            Toastr::success('Return has been updated successfully!', 'Success');
            return response()->json([
                'success' => true,
                'message' => 'Return has been updated successfully!',
                'return' => $return,
                'redirect' => route('ViewAllProductOrderReturns')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error('Something went wrong! ' . $e->getMessage(), 'Error');
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong! ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified return (soft delete)
     */
    public function destroy($slug)
    {
        try {
            DB::beginTransaction();

            $return = ProductOrderReturn::with('return_products')->where('slug', $slug)->firstOrFail();

            // Reverse stock changes
            foreach ($return->return_products as $returnProduct) {
                $product = Product::find($returnProduct->product_id);
                if ($product) {
                    $product->stock -= $returnProduct->qty; // Remove the previously added stock
                    $product->save();
                }
            }

            // Soft delete
            $return->status = 'inactive';
            $return->save();

            DB::commit();

            return response()->json([
                'success' => 'Deleted successfully!',
                'data' => 1
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting return: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Print return invoice
     */
    public function printReturn($slug)
    {
        $return = ProductOrderReturn::with(['return_products', 'customer', 'warehouse', 'originalOrder'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Company information
        $company = [
            'name' => 'BME Trading Company',
            'address' => '123 Business District, Dhaka-1000, Bangladesh',
            'phone' => '+880 1700-000000',
            'email' => 'info@bmetrading.com',
            'website' => 'www.bmetrading.com',
            'logo' => '/logo.png'
        ];

        $qrData = "Return: {$return->return_code}\nDate: {$return->return_date}\nTotal: {$return->total} BDT";

        return view('backend.product_order_return.print', compact('return', 'company', 'qrData'));
    }

    /**
     * Get return history for an order (API endpoint)
     */
    public function getReturnHistory($orderId)
    {
        try {
            $returns = ProductOrderReturn::with('return_products')
                ->where('product_order_id', $orderId)
                ->where('status', 'active')
                ->orderBy('return_date', 'desc')
                ->get();

            $history = [];
            foreach ($returns as $return) {
                foreach ($return->return_products as $product) {
                    $history[] = [
                        'return_code' => $return->return_code,
                        'return_date' => $return->return_date,
                        'product_name' => $product->product_name,
                        'qty' => $product->qty,
                        'total' => $product->total_price,
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'history' => $history
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching return history: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get original invoice data (API endpoint)
     */
    public function getOriginalInvoice($orderId)
    {
        try {
            $order = ProductOrder::with(['order_products', 'customer'])
                ->findOrFail($orderId);

            return response()->json([
                'success' => true,
                'order' => $order
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching original invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper: Get available return quantities for an order
     */
    private function getAvailableReturnQuantities($orderId, $excludeReturnId = null)
    {
        $order = ProductOrder::with('order_products')->findOrFail($orderId);
        $availableQuantities = [];

        foreach ($order->order_products as $orderProduct) {
            // Get total already returned for this product
            $returnedQty = ProductOrderReturnProduct::whereHas('return', function ($query) use ($orderId, $excludeReturnId) {
                $query->where('product_order_id', $orderId)
                      ->where('status', 'active');
                if ($excludeReturnId) {
                    $query->where('id', '!=', $excludeReturnId);
                }
            })
            ->where('product_id', $orderProduct->product_id)
            ->sum('qty');

            $availableQuantities[$orderProduct->product_id] = $orderProduct->qty - $returnedQty;
        }

        return $availableQuantities;
    }

    /**
     * Helper: Generate unique return code
     */
    private function generateReturnCode()
    {
        $year = Carbon::now()->format('y');
        $month = Carbon::now()->format('m');
        $prefix = 'R' . $year . $month;

        $latestReturn = ProductOrderReturn::where('return_code', 'like', $prefix . '%')
            ->orderBy('return_code', 'desc')
            ->first();

        if ($latestReturn) {
            $lastNumber = intval(substr($latestReturn->return_code, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $newNumber;
    }
}

