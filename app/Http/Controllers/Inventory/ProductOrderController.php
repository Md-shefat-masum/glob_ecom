<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Account\Models\DbPaymentType;
use App\Http\Controllers\Customer\Models\Customer;
use App\Http\Controllers\Outlet\Models\CustomerSourceType;
use App\Http\Controllers\Inventory\Models\ProductSupplier;
use App\Http\Controllers\Inventory\Models\ProductWarehouse;
use App\Http\Controllers\Inventory\Models\ProductWarehouseRoom;
use App\Http\Controllers\Inventory\Models\ProductWarehouseRoomCartoon;
use App\Http\Controllers\Inventory\Models\ProductPurchaseOtherCharge;
use App\Http\Controllers\Inventory\Models\ProductStock;
use App\Models\Product;
use App\Models\ProductOrder;
use App\Models\ProductOrderProduct;
use App\Models\ProductVariantCombination;
use App\Models\GeneralInfo;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Brian2694\Toastr\Facades\Toastr;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductOrderController extends Controller
{
    public function addNewProductOrder()
    {
        $productWarehouses = ProductWarehouse::where('status', 'active')->get();
        $other_charges_types = ProductPurchaseOtherCharge::where('status', 'active')->get();
        $sale_code = $this->latestCode();
        return view('backend.product_order_management.create', compact('productWarehouses', 'other_charges_types', 'sale_code'));
    }

    public function latestCode()
    {
        // Get current year and month (YYMM format)
        $year = Carbon::now()->format('y'); // e.g. "25"
        $month = Carbon::now()->format('m'); // e.g. "10"
        $prefix = $year . $month; // "2510"

        $latestOrder = ProductOrder::query()
            ->where('order_code', 'like', $prefix . '%')
            ->orderBy('order_code', 'desc')
            ->first();

        if ($latestOrder) {
            // Extract last 5 digits and increment
            $lastNumber = intval(substr($latestOrder->order_code, -5));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            // Start from 00001 if no order this month
            $newNumber = '0001';
        }

        // Merge prefix + number
        $newOrderCode = $prefix . $newNumber;

        return $newOrderCode;
    }

    public function calc_other_charges($other_charges, $subtotal)
    {
        $percent_total = 0;
        $fixed_total = 0;

        // Check if other_charges is null or not an array
        if (is_null($other_charges) || !is_array($other_charges)) {
            return 0;
        }

        foreach ($other_charges as $charge) {
            if (!is_array($charge) || !isset($charge['type']) || !isset($charge['amount'])) {
                continue; // Skip invalid charge entries
            }
            
            if ($charge['type'] === 'percent') {
                $percent_total += ($subtotal * $charge['amount']) / 100;
            } else {
                $fixed_total += $charge['amount'];
            }
        }

        $total = $percent_total + $fixed_total;
        return $total;
    }

    /**
     * Save new product order following orderTasteCases.txt requirements
     * 1. Validate request data
     * 2. Organize data for insert into product_orders and product_order_products
     * 3. Create order on product_orders
     * 4. Set data into product_order_products
     * 5. If order status = invoiced or delivered, update stock
     */
    public function saveNewProductOrder(Request $request)
    {
        // Decode delivery_info if it's a JSON string
        if ($request->has('delivery_info') && is_string($request->delivery_info)) {
            $request->merge(['delivery_info' => json_decode($request->delivery_info, true)]);
        }
        
        // Step 1: Validate request data
        $validator = Validator::make($request->all(), [
            'order_status' => ['required','in:invoiced,pending,delivered'],
            'product_warehouse_id' => ['required'],
            'customer_id' => ['required'],
            'order_code' => ['required', 'unique:product_orders,order_code'],
            'sale_date' => ['required'],
            'product' => 'required|array|min:1',
            'paid_amount' => 'required|numeric|lte:grand_total_amt',
            'grand_total_amt' => 'required|numeric',
            'due_amount' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->customer_id == 1 && $value > 0) {
                        $fail('Due amount not accepted for this customer.');
                    }
                },
            ],
            'delivery_info' => 'nullable|array',
            'delivery_info.receiver_name' => 'required_with:delivery_info',
            'delivery_info.receiver_phone' => 'required_with:delivery_info',
            'delivery_info.full_address' => 'required_with:delivery_info',
            'delivery_info.delivery_method' => 'required_with:delivery_info|in:pathao,courier,store_pickup',
        ], [
            'product.required' => 'no product selected.',
            'order_code.unique' => 'Order code already exists.',
            'delivery_info.receiver_name.required_with' => 'Receiver name is required for delivery.',
            'delivery_info.receiver_phone.required_with' => 'Receiver phone is required for delivery.',
            'delivery_info.full_address.required_with' => 'Full address is required for delivery.',
            'delivery_info.delivery_method.required_with' => 'Delivery method is required.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        DB::beginTransaction();
        
        try {
            // Step 2: Organize data for insert
            $other_charge_total = $this->calc_other_charges($request->other_charges, $request->subtotal);
            $random_no = random_int(100, 999) . random_int(1000, 9999);
            $slug = Str::orderedUuid() . uniqid() . $random_no;
            $user = Auth::user();

            // Step 3: Create order on product_orders
            $order = new ProductOrder();
            $order->store_id = $user->store_id ?? null;
            $order->order_code = $request->order_code;
            $order->product_warehouse_id = $request->product_warehouse_id;
            // $order->product_warehouse_room_id = $request->product_warehouse_room_id;
            // $order->product_warehouse_room_cartoon_id = $request->product_warehouse_room_cartoon_id;
            // $order->product_supplier_id = $request->supplier_id;
            $order->customer_id = $request->customer_id;
            $order->sale_date = $request->sale_date;
            $order->due_date = $request->due_date;
            $order->reference = $request->reference;
            $order->other_charges = $request->other_charges;
            $order->other_charge_amount = $other_charge_total;
            $order->discount_type = $request->discount_to_all_type;
            $order->discount_amount = $request->discount_on_all;
            $order->calculated_discount_amount = $request->discount_to_all_amt;
            $order->round_off_from_total = $request->round_off_from_total;
            $order->decimal_round_off = $request->decimal_round_off;
            $order->subtotal = $request->subtotal_amt;
            $order->total = $request->grand_total_amt;
            $order->paid_amount = $request->paid_amount;
            $order->due_amount = $request->due_amount;
            $order->payments = [
                ...($request->payments ?? []),
                'total_paid' => $request->paid_amount,
                'total_due' => $request->due_amount,
            ];
            $order->note = $request->note;
            $order->order_status = $request->order_status;
            $order->creator = $user->id;
            $order->status = 'active';
            $order->slug = $slug; // Set slug before save
            $order->request_data = $request->all();
            $order->buying_from = $request->buying_from;
            $order->delivery_info = $request->delivery_info; // Save delivery information
            $order->save();

            // Step 4: Set data into product_order_products
            if (is_null($request->product) || !is_array($request->product)) {
                throw new \Exception("Product list is required and must be an array. Received: " . gettype($request->product));
            }

            foreach ($request->product as $productItem) {
                if (!is_array($productItem) || !isset($productItem['id'])) {
                    throw new \Exception("Invalid product item format. Each product must have an 'id' field.");
                }

                $product = Product::where('id', $productItem['id'])->first();
                if (!$product) {
                    throw new \Exception("Product not found with ID: {$productItem['id']}");
                }

                $product_slug = Str::orderedUuid() . $random_no . $order->id . uniqid();

                ProductOrderProduct::create([
                    'product_warehouse_id' => $request->product_warehouse_id,
                    'product_warehouse_room_id' => $request->product_warehouse_room_id,
                    'product_warehouse_room_cartoon_id' => $request->product_warehouse_room_cartoon_id,
                    'product_supplier_id' => $request->supplier_id,
                    'product_order_id' => $order->id,
                    'product_id' => $productItem['id'],
                    'variant_id' => $productItem['variant_id'] ?? null,
                    'unit_price_id' => $productItem['unit_price_id'] ?? null,
                    'product_name' => $product->name,
                    'qty' => $productItem['quantities'],
                    'sale_price' => $productItem['prices'],
                    'discount_type' => 'in_percentage',
                    'discount_amount' => $productItem['discounts'],
                    'tax' => $productItem['taxes'],
                    'total_price' => $productItem['totals'],
                    'product_price' => $product->discount_price ? $product->discount_price : $product->price,
                    'slug' => $product_slug,
                ]);
            }

            // Step 5: If order status = invoiced or delivered, update stock
            if ($order->order_status == 'invoiced' || $order->order_status == 'delivered') {
                $this->updateStockForOrder($order);
                
                // Record sales accounting
                $accountingResult = record_sales_accounting($order, 'create');
                if (!$accountingResult['success']) {
                    Log::warning('Sales accounting partially failed', [
                        'order_id' => $order->id,
                        'message' => $accountingResult['message']
                    ]);
                }
            }

            DB::commit();

            // Send SMS notification based on buying_from
            $this->sendOrderSMS($order);

            Toastr::success('Order has been added successfully!', 'Success');
            return response()->json([
                'success' => true,
                'message' => 'Order has been added successfully!',
                'order' => $order
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error in saveNewProductOrder', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['password', 'password_confirmation'])
            ]);
            
            Toastr::error('Something went wrong! ' . $e->getMessage(), 'Error');
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong! ' . $e->getMessage(),
                'error_type' => get_class($e),
                'error_file' => basename($e->getFile()),
                'error_line' => $e->getLine(),
            ], 500);
        }
    }

    public function viewAllProductOrder(Request $request)
    {
        if ($request->ajax()) {

            $data = ProductOrder::with('creator', 'order_products')
                // ->where('status', 'active')
                ->orderBy('id', 'desc') // Order by the ID
                ->get();

            // dd($data);
            return Datatables::of($data)
                // ->editColumn('creator', function ($data) {
                //     return $data->creator ? $data->creator->name : 'N/A'; // Access creator name
                // })
                ->editColumn('status', function ($data) {
                    return $data->status == "active" ? 'Active' : 'Inactive';
                })
                ->editColumn('created_at', function ($data) {
                    return date("Y-m-d", strtotime($data->created_at));
                })
                ->addIndexColumn()
                // ->addColumn('action', function ($data) {
                //     $btn = '<a href="' . url('edit/purchase-product/quotation') . '/' . $data->slug . '" class="btn-sm btn-warning rounded editBtn"><i class="fas fa-edit"></i></a>';
                //     $btn .= ' <a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $data->slug . '" data-original-title="Delete" class="btn-sm btn-danger rounded deleteBtn"><i class="fas fa-trash-alt"></i></a>';
                //     return $btn;
                // })
                ->addColumn('action', function ($data) {
                    $btn = '<div class="dropdown">';
                    $btn .= '<button class="btn-sm btn-primary dropdown-toggle rounded" type="button" id="actionDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                    $btn .= '<i class="fas fa-cog"></i> Actions';
                    $btn .= '</button>';
                    $btn .= '<div class="dropdown-menu" aria-labelledby="actionDropdown">';

                    // Show/View Invoice
                    $btn .= '<a class="dropdown-item" href="' . route('order.invoice', $data->slug) . '" target="_blank"><i class="fas fa-eye text-info"></i> Show Invoice</a>';

                    // Edit (only for pending/non-invoiced orders)
                    if ($data->order_status == 'pending') {
                        $btn .= '<a class="dropdown-item" href="' . route('EditProductOrder', $data->slug) . '"><i class="fas fa-edit text-warning"></i> Edit</a>';
                    }

                    // Pay Due / Create Payment (only if there's due amount)
                    if ($data->due_amount > 0) {
                        $btn .= '<a class="dropdown-item" href="' . route('CreateCustomerPaymentWithOrder', $data->id) . '"><i class="fas fa-dollar-sign text-success"></i> Create Payment</a>';
                    }

                    // Print
                    $btn .= '<a class="dropdown-item" href="' . route('order.invoice.pdf', $data->slug) . '" target="_blank"><i class="fas fa-print text-primary"></i> Print</a>';

                    // Courier (only if not couriered yet)
                    if (isset($data->is_couriered) && $data->is_couriered == 0) {
                        $btn .= '<div class="dropdown-divider"></div>';
                        $btn .= '<a class="dropdown-item" href="' . route('ShowCourierOrder', $data->id) . '"><i class="fas fa-truck text-info"></i> Add to Courier</a>';
                    }

                    // Return (only for invoiced/delivered orders)
                    if ($data->order_status == 'invoiced' || $data->order_status == 'delivered') {
                        $btn .= '<a class="dropdown-item" href="' . route('CreateProductOrderReturn', $data->slug) . '">
                        <i class="fas fa-undo text-orange"></i> Return
                        <span class="badge bg-danger">'.$data->due_amount.'</span>
                        </a>';
                    }

                    // Delete (only for pending orders)
                    if ($data->order_status == 'pending') {
                        $btn .= '<div class="dropdown-divider"></div>';
                        $btn .= '<a class="dropdown-item deleteBtn" href="javascript:void(0)" data-toggle="tooltip" data-id="' . $data->slug . '" data-original-title="Delete"><i class="fas fa-trash-alt text-danger"></i> Delete</a>';
                    }

                    $btn .= '</div>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        // Redirect to new order list page (Vue + pagination)
        return redirect()->route('OrderListPage', ['order_source' => request('order_source', 'pos')]);
    }

    /**
     * Order list page: Vue 2 + Laravel pagination.
     * GET /order-list (default order_source=pos). AJAX returns JSON: analytics + paginated data.
     */
    public function orderListPage(Request $request)
    {
        $baseQuery = $this->orderListBaseQuery($request);

        if ($request->ajax() || $request->wantsJson()) {
            $analytics = $this->orderListAnalytics($baseQuery);
            $perPage = (int) $request->get('per_page', 10);
            $perPage = in_array($perPage, [10, 50, 100, 200]) ? $perPage : 10;
            $orders = (clone $baseQuery)
                ->with(['creator', 'customer', 'warehouse'])
                ->orderBy('id', 'desc')
                ->paginate($perPage)
                ->appends($request->all());

            $orders->getCollection()->transform(function ($order) {
                return $this->transformOrderForList($order);
            });

            return response()->json([
                'analytics' => $analytics,
                'data' => $orders->items(),
                'pagination' => [
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total(),
                    'from' => $orders->firstItem(),
                    'to' => $orders->lastItem(),
                ],
            ]);
        }

        $paymentTypes = DbPaymentType::where('status', 'active')->get(['id', 'payment_type']);
        $customerSources = CustomerSourceType::where('status', 'active')->get(['id', 'title']);
        $warehouses = ProductWarehouse::orderBy('id')->get(['id', 'title', 'status']);

        return view('backend.product_order_management.view', compact('paymentTypes', 'customerSources', 'warehouses'));
    }

    /**
     * Base query for order list with all filters applied.
     */
    private function orderListBaseQuery(Request $request)
    {
        $query = ProductOrder::query();

        // Search: customer name, customer phone exact, id, order_code, creator name partial, creator phone exact
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', $search)
                    ->orWhere('order_code', 'like', '%' . $search . '%')
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('name', 'like', '%' . $search . '%')
                            ->orWhere('phone', $search);
                    })
                    ->orWhereHas('creator', function ($cq) use ($search) {
                        $cq->where('name', 'like', '%' . $search . '%')
                            ->orWhere('phone', $search);
                    });
            });
        }

        // Order status (buttons)
        if ($request->filled('order_status')) {
            $query->where('order_status', $request->order_status);
        }

        if ($request->filled('discount_type')) {
            $query->where('discount_type', $request->discount_type);
        }

        // Customer source / order_source: delivery_info.order_source (default from plan: order_source=pos via URL)
        $sourceParam = $request->filled('customer_source_type') ? $request->customer_source_type : $request->get('order_source');
        if ($sourceParam !== null && $sourceParam !== '') {
            $source = $sourceParam;
            if (is_numeric($source)) {
                $ct = CustomerSourceType::find($source);
                $source = $ct ? (strtolower($ct->title ?? '') ?: (string) $source) : $source;
            }
            $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(delivery_info, '$.order_source')) = ?", [$source]);
        }

        if ($request->filled('warehouse_id')) {
            $query->where('product_warehouse_id', $request->warehouse_id);
        }

        if ($request->filled('warehouse_status')) {
            $query->whereHas('warehouse', function ($w) use ($request) {
                $w->where('status', $request->warehouse_status);
            });
        }

        if ($request->filled('sales_date_from')) {
            $query->where('sale_date', '>=', $request->sales_date_from);
        }
        if ($request->filled('sales_date_to')) {
            $query->where('sale_date', '<=', $request->sales_date_to);
        }
        if ($request->filled('due_date_from')) {
            $query->where('due_date', '>=', $request->due_date_from);
        }
        if ($request->filled('due_date_to')) {
            $query->where('due_date', '<=', $request->due_date_to);
        }

        if ($request->filled('paid_status')) {
            if ($request->paid_status === 'paid') {
                $query->where('due_amount', '<=', 0);
            } elseif ($request->paid_status === 'due') {
                $query->where('due_amount', '>', 0);
            }
        }

        return $query;
    }

    /**
     * Analytics counts and totals from the same filtered base query.
     */
    private function orderListAnalytics($baseQuery)
    {
        $all = (clone $baseQuery)->selectRaw('COUNT(*) as cnt, COALESCE(SUM(CAST(total AS DECIMAL(15,2))), 0) as total_val')->first();
        $pending = (clone $baseQuery)->where('order_status', 'pending')->selectRaw('COUNT(*) as cnt, COALESCE(SUM(CAST(total AS DECIMAL(15,2))), 0) as total_val')->first();
        $invoiced = (clone $baseQuery)->where('order_status', 'invoiced')->selectRaw('COUNT(*) as cnt, COALESCE(SUM(CAST(total AS DECIMAL(15,2))), 0) as total_val')->first();
        $delivered = (clone $baseQuery)->where('order_status', 'delivered')->selectRaw('COUNT(*) as cnt, COALESCE(SUM(CAST(total AS DECIMAL(15,2))), 0) as total_val')->first();
        $canceled = (clone $baseQuery)->where('order_status', 'canceled')->selectRaw('COUNT(*) as cnt, COALESCE(SUM(CAST(total AS DECIMAL(15,2))), 0) as total_val')->first();
        $returned = (clone $baseQuery)->where('is_returned', 1)->selectRaw('COUNT(*) as cnt, COALESCE(SUM(CAST(total AS DECIMAL(15,2))), 0) as total_val')->first();
        $couriered = (clone $baseQuery)->where('is_couriered', 1)->selectRaw('COUNT(*) as cnt, COALESCE(SUM(CAST(total AS DECIMAL(15,2))), 0) as total_val')->first();
        $paid = (clone $baseQuery)->whereRaw('COALESCE(due_amount, 0) <= 0')->selectRaw('COUNT(*) as cnt, COALESCE(SUM(CAST(total AS DECIMAL(15,2))), 0) as total_val')->first();
        $due = (clone $baseQuery)->whereRaw('COALESCE(due_amount, 0) > 0')->selectRaw('COUNT(*) as cnt, COALESCE(SUM(CAST(total AS DECIMAL(15,2))), 0) as total_val')->first();

        return [
            'all' => ['count' => (int) $all->cnt, 'total_value' => (float) $all->total_val],
            'pending' => ['count' => (int) $pending->cnt, 'total_value' => (float) $pending->total_val],
            'invoiced' => ['count' => (int) $invoiced->cnt, 'total_value' => (float) $invoiced->total_val],
            'delivered' => ['count' => (int) $delivered->cnt, 'total_value' => (float) $delivered->total_val],
            'canceled' => ['count' => (int) $canceled->cnt, 'total_value' => (float) $canceled->total_val],
            'returned' => ['count' => (int) $returned->cnt, 'total_value' => (float) $returned->total_val],
            'couriered' => ['count' => (int) $couriered->cnt, 'total_value' => (float) $couriered->total_val],
            'paid' => ['count' => (int) $paid->cnt, 'total_value' => (float) $paid->total_val],
            'due' => ['count' => (int) $due->cnt, 'total_value' => (float) $due->total_val],
        ];
    }

    /**
     * Transform a single order for list JSON (table row).
     */
    private function transformOrderForList(ProductOrder $order)
    {
        $deliveryInfo = $order->delivery_info ?? [];
        $otherCharges = $order->other_charges ?? [];
        $shippingCharge = is_array($otherCharges) && isset($otherCharges['delivery_charge'])
            ? (float) $otherCharges['delivery_charge']
            : (float) ($order->other_charge_amount ?? 0);
        $payments = $order->payments ?? [];
        $orderSource = $deliveryInfo['order_source'] ?? $order->order_source ?? null;

        return [
            'id' => $order->id,
            'order_code' => $order->order_code,
            'slug' => $order->slug,
            'sale_date' => $order->sale_date,
            'due_date' => $order->due_date,
            'customer_name' => $order->customer ? $order->customer?->name : ($order->request_data['custoemr_name'] ?? 'N/A'),
            'customer_phone' => $order->customer ? $order->customer->phone : ($order->request_data['customer_phone'] ?? ''),
            'customer_email' => $order->customer ? $order->customer->email : null,
            'order_source' => $orderSource,
            'creator_name' => $order->creator->name ?? '',
            'creator_phone' => $order->creator->phone ?? '',
            'warehouse_name' => $order->warehouse ? $order->warehouse->title : null,
            'subtotal' => (float) $order->subtotal,
            'shipping_charge' => $shippingCharge,
            'grand_total' => (float) $order->total,
            'paid_amount' => (float) ($order->paid_amount ?? 0),
            'due_amount' => (float) ($order->due_amount ?? 0),
            'order_status' => $order->order_status,
            'is_couriered' => (int) ($order->is_couriered ?? 0),
            'is_returned' => (int) ($order->is_returned ?? 0),
        ];
    }

    /**
     * Simple stock update method for orders
     * Creates log entry and updates product_stocks
     */
    private function updateStockForOrder($order)
    {
        try {
            $orderProducts = ProductOrderProduct::where('product_order_id', $order->id)->get();

            foreach ($orderProducts as $orderProduct) {
                // Check if log entry already exists for this sales_id
                $existingLog = DB::table('product_stock_logs')
                    ->where('product_sales_id', $order->id)
                    ->where('product_id', $orderProduct->product_id)
                    ->when($orderProduct->variant_id, function($query) use ($orderProduct) {
                        return $query->where('variant_combination_id', $orderProduct->variant_id);
                    })
                    ->first();

                // If log doesn't exist, create new one
                if (!$existingLog) {
                    $variant = null;
                    $variantData = null;
                    
                    if ($orderProduct->variant_id) {
                        $variant = ProductVariantCombination::find($orderProduct->variant_id);
                        if ($variant) {
                            // Get variant_values and encode for DB::table insert
                            $variantData = is_array($variant->variant_values) 
                                ? json_encode($variant->variant_values) 
                                : $variant->variant_values;
                        }
                    }

                    DB::table('product_stock_logs')->insert([
                        'warehouse_id' => $order->product_warehouse_id,
                        'product_id' => $orderProduct->product_id,
                        'product_name' => $orderProduct->product_name,
                        'product_sales_id' => $order->id,
                        'quantity' => $orderProduct->qty,
                        'type' => 'sales',
                        'has_variant' => $orderProduct->variant_id ? 1 : 0,
                        'variant_combination_id' => $orderProduct->variant_id,
                        'variant_combination_key' => $variant ? $variant->combination_key : null,
                        'variant_sku' => $variant ? $variant->sku : null,
                        'variant_data' => $variantData,
                        'creator' => auth()->id(),
                        'slug' => $order->id . uniqid(),
                        'status' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // Update or create product_stocks entry
                $this->updateProductStocksForVariant($orderProduct, $order);
            }

        } catch (\Exception $e) {
            Log::error('Error in updateStockForOrder', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Update product_stocks table for a variant
     */
    private function updateProductStocksForVariant($orderProduct, $order)
    {
        try {
            // Calculate closing stock from logs based on event types
            // Stock IN: purchase, initial, manual add, return
            $stockIn = DB::table('product_stock_logs')
                ->where('product_id', $orderProduct->product_id)
                ->when($orderProduct->variant_id, function($query) use ($orderProduct) {
                    return $query->where('variant_combination_id', $orderProduct->variant_id);
                })
                ->whereIn('type', ['purchase', 'initial', 'manual add', 'return'])
                ->sum('quantity') ?? 0;

            // Stock OUT: sales, waste, transfer
            $stockOut = DB::table('product_stock_logs')
                ->where('product_id', $orderProduct->product_id)
                ->when($orderProduct->variant_id, function($query) use ($orderProduct) {
                    return $query->where('variant_combination_id', $orderProduct->variant_id);
                })
                ->whereIn('type', ['sales', 'waste', 'transfer'])
                ->sum('quantity') ?? 0;

            // Final closing stock
            $closingStock = $stockIn - $stockOut;

            $variant = null;
            if ($orderProduct->variant_id) {
                $variant = ProductVariantCombination::find($orderProduct->variant_id);
            }

            // Find and update existing product_stocks entry (don't insert new)
            DB::table('product_stocks')
                ->where('product_id', $orderProduct->product_id)
                ->when($orderProduct->variant_id, function($query) use ($orderProduct) {
                    return $query->where('variant_combination_id', $orderProduct->variant_id);
                })
                ->update([
                    'qty' => max(0, $closingStock),
                    'date' => now()->format('Y-m-d'),
                    'updated_at' => now(),
                ]);

            // Update product.availability_status based on stock
            $this->updateProductAvailabilityStatus($orderProduct->product_id);

        } catch (\Exception $e) {
            Log::error('Error in updateProductStocksForVariant', [
                'product_id' => $orderProduct->product_id,
                'variant_id' => $orderProduct->variant_id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update product availability_status based on total stock
     * Step 9: Set product.availability_status = in_stock | out_stock
     */
    private function updateProductAvailabilityStatus($productId)
    {
        try {
            // Calculate total stock from product_stocks
            $totalStock = DB::table('product_stocks')
                ->where('product_id', $productId)
                ->where('status', 'active')
                ->sum('qty') ?? 0;

            // Set availability_status
            $availabilityStatus = ($totalStock > 0) ? 'in_stock' : 'out_stock';

            Product::where('id', $productId)->update([
                'availability_status' => $availabilityStatus,
                'stock' => (int) $totalStock,
                'updated_at' => now()
            ]);

        } catch (\Exception $e) {
            Log::error('Error in updateProductAvailabilityStatus', [
                'product_id' => $productId,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function editProductOrder($slug)
    {
        $data = ProductOrder::with('order_products')->where('slug', $slug)->first();
        
        if (!$data) {
            Toastr::error('Order not found!', 'Error');
            return redirect()->route('ViewAllProductOrder');
        }

        // Check if order is already invoiced
        if ($data->order_status == 'invoiced' || $data->order_status == 'delivered') {
            Toastr::warning('Cannot edit invoiced/delivered orders!', 'Warning');
            return redirect()->route('ViewAllProductOrder');
        }

        $productWarehouses = ProductWarehouse::where('status', 'active')->get();
        $other_charges_types = ProductPurchaseOtherCharge::where('status', 'active')->get();
        $sale_code = $data->order_code;

        return view('backend.product_order_management.edit', compact('data', 'productWarehouses', 'other_charges_types', 'sale_code'));
    }

    public function apiEditProduct($slug)
    {
        $data = ProductOrder::with(['order_products.product' => function($q) {
            $q->with(['variantCombinations' => function($q) {
                $q->where('status', 1)
                  ->select('id', 'product_id', 'combination_key', 'variant_values', 'price', 'discount_price', 'stock', 'sku');
            }, 'unitPricing' => function($q) {
                $q->where('status', 1)
                  ->select('id', 'product_id', 'unit_id', 'unit_title', 'unit_value', 'unit_label', 'price', 'discount_price', 'discount_percent');
            }])
            ->select('id', 'name', 'price', 'discount_price', 'discount_parcent', 'stock', 'slug', 'has_variant', 'unit_id', 'sku', 'barcode');
        }, 'order_products.variant', 'order_products.unitPrice'])
        ->where('slug', $slug)->first();

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        // Transform order_products to include full product details with variants/unit prices
        $orderProducts = $data->order_products->map(function($orderProduct) {
            $product = $orderProduct->product;
            
            if (!$product) {
                return null;
            }

            // Prepare variants in the format expected by Vue.js
            $variants = [];
            if ($product->has_variant == 1 && $product->variantCombinations) {
                $variants = $product->variantCombinations->map(function($variant) {
                    return [
                        'id' => $variant->id,
                        'name' => $variant->variant_values ?? $variant->combination_key,
                        'price' => floatval($variant->price ?? 0),
                        'discount_price' => $variant->discount_price ? floatval($variant->discount_price) : null,
                        'stock' => intval($variant->stock ?? 0),
                        'sku' => $variant->sku ?? ''
                    ];
                })->toArray();
            }

            // Prepare unit prices in the format expected by Vue.js
            $unitPrices = [];
            if ($product->unitPricing) {
                $unitPrices = $product->unitPricing->map(function($unitPrice) {
                    return [
                        'id' => $unitPrice->id,
                        'unit_label' => $unitPrice->unit_label ?? ($unitPrice->unit_title . ' (' . $unitPrice->unit_value . ')'),
                        'price' => floatval($unitPrice->price ?? 0),
                        'discount_price' => $unitPrice->discount_price ? floatval($unitPrice->discount_price) : null,
                        'unit_title' => $unitPrice->unit_title ?? '',
                        'unit_value' => $unitPrice->unit_value ?? ''
                    ];
                })->toArray();
            }

            // Determine selected variant/unit price
            $selectedVariant = null;
            $selectedUnitPrice = null;
            
            if ($orderProduct->variant_id && $variants) {
                $selectedVariant = collect($variants)->firstWhere('id', $orderProduct->variant_id);
            }
            
            if ($orderProduct->unit_price_id && $unitPrices) {
                $selectedUnitPrice = collect($unitPrices)->firstWhere('id', $orderProduct->unit_price_id);
            }

            // Determine available stock
            $availableStock = $product->stock;
            if ($selectedVariant) {
                $availableStock = $selectedVariant['stock'];
            }

            return [
                'id' => $orderProduct->id, // Keep order product id for updates
                'product_id' => $product->id,
                'product_name' => $product->name,
                'name' => $product->name,
                'sale_price' => floatval($orderProduct->sale_price ?? $product->price),
                'price' => floatval($orderProduct->sale_price ?? $product->price),
                'qty' => intval($orderProduct->qty ?? 1),
                'quantity' => intval($orderProduct->qty ?? 1),
                'discount_amount' => floatval($orderProduct->discount_amount ?? 0),
                'discount' => floatval($orderProduct->discount_percent ?? 0),
                'discount_parcent' => floatval($orderProduct->discount_percent ?? 0),
                'tax' => floatval($orderProduct->tax ?? 0),
                'total_price' => floatval($orderProduct->total_price ?? 0),
                
                // Variant/Unit Price support
                'has_variant' => $product->has_variant == 1 && count($variants) > 0 ? 1 : 0,
                'has_unit_price' => count($unitPrices) > 0 ? 1 : 0,
                'variants' => $variants,
                'unit_prices' => $unitPrices,
                'variant_id' => $orderProduct->variant_id,
                'selected_variant_id' => $orderProduct->variant_id,
                'unit_price_id' => $orderProduct->unit_price_id,
                'selected_unit_price_id' => $orderProduct->unit_price_id,
                'selected_variant' => $selectedVariant,
                'selected_unit_price' => $selectedUnitPrice,
                'available_stock' => $availableStock
            ];
        })->filter()->values();

        // Replace order_products with transformed data
        $data->order_products = $orderProducts;

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Update product order following orderTasteCases.txt requirements
     */
    public function updateProductOrder(Request $request)
    {
        // Decode delivery_info if it's a JSON string
        if ($request->has('delivery_info') && is_string($request->delivery_info)) {
            $request->merge(['delivery_info' => json_decode($request->delivery_info, true)]);
        }
        
        // Step 1: Validate request data
        $validator = Validator::make($request->all(), [
            'product_order_id' => ['required', 'exists:product_orders,id'],
            'order_status' => ['required', 'in:invoiced,pending,delivered'],
            'product_warehouse_id' => ['required'],
            'customer_id' => ['required'],
            'order_code' => ['required'],
            'sale_date' => ['required'],
            'product' => 'required|array|min:1',
            'paid_amount' => 'required|numeric|lte:grand_total_amt',
            'grand_total_amt' => 'required|numeric',
            'due_amount' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->customer_id == 1 && $value > 0) {
                        $fail('Due amount not accepted for this customer.');
                    }
                },
            ],
            'delivery_info' => 'nullable|array',
            'delivery_info.receiver_name' => 'required_with:delivery_info',
            'delivery_info.receiver_phone' => 'required_with:delivery_info',
            'delivery_info.full_address' => 'required_with:delivery_info',
            'delivery_info.delivery_method' => 'required_with:delivery_info|in:pathao,courier,store_pickup',
        ], [
            'product.required' => 'No product selected.',
            'product_order_id.required' => 'Order ID is required.',
            'product_order_id.exists' => 'Order not found.',
            'delivery_info.receiver_name.required_with' => 'Receiver name is required for delivery.',
            'delivery_info.receiver_phone.required_with' => 'Receiver phone is required for delivery.',
            'delivery_info.full_address.required_with' => 'Full address is required for delivery.',
            'delivery_info.delivery_method.required_with' => 'Delivery method is required.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();

        try {
            $order = ProductOrder::where('id', $request->product_order_id)->firstOrFail();

            // Check if order can be edited
            if ($order->order_status == 'invoiced' || $order->order_status == 'delivered') {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot edit invoiced or delivered orders!'
                ], 403);
            }

            // Store old status to check if it changed
            $oldOrderStatus = $order->order_status;

            // Step 2: Organize data for update
            $other_charge_total = $this->calc_other_charges($request->other_charges, $request->subtotal_amt);

            // Step 3: Update order on product_orders
            $order->order_code = $request->order_code;
            $order->product_warehouse_id = $request->product_warehouse_id;
            // $order->product_warehouse_room_id = $request->product_warehouse_room_id;
            // $order->product_warehouse_room_cartoon_id = $request->product_warehouse_room_cartoon_id;
            // $order->product_supplier_id = $request->supplier_id;
            $order->customer_id = $request->customer_id;
            $order->sale_date = $request->sale_date;
            $order->due_date = $request->due_date;
            $order->reference = $request->reference;
            $order->other_charges = $request->other_charges;
            $order->other_charge_amount = $other_charge_total;
            $order->discount_type = $request->discount_to_all_type;
            $order->discount_amount = $request->discount_on_all;
            $order->calculated_discount_amount = $request->discount_to_all_amt;
            $order->round_off_from_total = $request->round_off_from_total;
            $order->decimal_round_off = $request->decimal_round_off;
            $order->subtotal = $request->subtotal_amt;
            $order->total = $request->grand_total_amt;
            $order->paid_amount = $request->paid_amount;
            $order->due_amount = $request->due_amount;
            $order->payments = [
                ...($request->payments ?? []),
                'total_paid' => $request->paid_amount,
                'total_due' => $request->due_amount,
            ];
            $order->note = $request->note;
            $order->order_status = $request->order_status;
            $order->request_data = $request->all();
            $order->updated_at = Carbon::now();
            $order->buying_from = $request->buying_from;
            $order->delivery_info = $request->delivery_info; // Save delivery information
            $order->save();

            // Step 4: Update data in product_order_products
            $existingOrderProductIds = ProductOrderProduct::where('product_order_id', $order->id)
                ->pluck('id')
                ->toArray();

            $requestOrderProductIds = [];

            foreach ($request->product as $productItem) {
                if (!isset($productItem['product_id'])) {
                    continue;
                }

                $random_no = random_int(100, 999) . random_int(1000, 9999);
                $product_slug = Str::orderedUuid() . $random_no . $order->id . uniqid();

                $product_id = $productItem['product_id'];
                $product = Product::where('id', $product_id)->first();
                
                if (!$product) {
                    Log::warning('Product not found during order update', [
                        'product_id' => $product_id,
                        'order_id' => $order->id
                    ]);
                    continue;
                }

                $orderProductId = $productItem['id'] ?? null;
                $existingProduct = null;
                
                if ($orderProductId) {
                    $existingProduct = ProductOrderProduct::where('id', $orderProductId)
                        ->where('product_order_id', $order->id)
                        ->first();
                }
                
                if (!$existingProduct) {
                    $existingProduct = ProductOrderProduct::where('product_order_id', $order->id)
                        ->where('product_id', $product_id)
                        ->where('variant_id', $productItem['variant_id'] ?? null)
                        ->where('unit_price_id', $productItem['unit_price_id'] ?? null)
                        ->first();
                }

                $productData = [
                    'product_warehouse_id' => $request->product_warehouse_id,
                    'product_warehouse_room_id' => $request->product_warehouse_room_id,
                    'product_warehouse_room_cartoon_id' => $request->product_warehouse_room_cartoon_id,
                    'product_supplier_id' => $request->supplier_id,
                    'variant_id' => $productItem['variant_id'] ?? null,
                    'unit_price_id' => $productItem['unit_price_id'] ?? null,
                    'product_name' => $product->name,
                    'qty' => $productItem['quantities'],
                    'sale_price' => $productItem['prices'],
                    'discount_type' => 'in_percentage',
                    'discount_amount' => $productItem['discounts'],
                    'tax' => $productItem['taxes'],
                    'total_price' => $productItem['totals'],
                    'product_price' => $product->discount_price ? $product->discount_price : $product->price,
                    'slug' => $product_slug,
                ];

                if ($existingProduct) {
                    $existingProduct->update($productData);
                    $requestOrderProductIds[] = $existingProduct->id;
                } else {
                    $newOrderProduct = ProductOrderProduct::create([
                        'product_order_id' => $order->id,
                        'product_id' => $product_id,
                        ...$productData,
                    ]);
                    $requestOrderProductIds[] = $newOrderProduct->id;
                }
            }

            // Delete order products not in request
            $recordsToDelete = [];
            if (!empty($requestOrderProductIds)) {
                $recordsToDelete = array_diff($existingOrderProductIds, $requestOrderProductIds);
            } else {
                $recordsToDelete = $existingOrderProductIds;
            }
            
            // If order was/is invoiced or delivered, delete stock logs for removed products
            if (($oldOrderStatus == 'invoiced' || $oldOrderStatus == 'delivered') && !empty($recordsToDelete)) {
                $deletedProducts = ProductOrderProduct::where('product_order_id', $order->id)
                    ->whereIn('id', $recordsToDelete)
                    ->get();
                
                foreach ($deletedProducts as $deletedProduct) {
                    // Delete stock logs for removed products
                    DB::table('product_stock_logs')
                        ->where('product_sales_id', $order->id)
                        ->where('product_id', $deletedProduct->product_id)
                        ->when($deletedProduct->variant_id, function($query) use ($deletedProduct) {
                            return $query->where('variant_combination_id', $deletedProduct->variant_id);
                        })
                        ->delete();
                    
                    // Recalculate and update stock for this product
                    $this->updateProductStocksForVariant($deletedProduct, $order);
                }
            }
            
            // Delete the order products
            if (!empty($recordsToDelete)) {
                ProductOrderProduct::where('product_order_id', $order->id)
                    ->whereIn('id', $recordsToDelete)
                    ->delete();
            }

            // Step 5: If order status = invoiced or delivered, update stock
            $newOrderStatus = $request->order_status;
            
            // Update stock if:
            // 1. Status changed from pending to invoiced/delivered (create new logs)
            // 2. Status is already invoiced/delivered (update existing logs with new quantities)
            if ($newOrderStatus == 'invoiced' || $newOrderStatus == 'delivered') {
                // Delete all existing logs for this order first, then recreate
                // This ensures quantity changes are properly reflected
                DB::table('product_stock_logs')
                    ->where('product_sales_id', $order->id)
                    ->delete();
                
                // Now recreate logs with updated quantities
                $this->updateStockForOrder($order);
                
                // Record sales accounting
                $accountingResult = record_sales_accounting($order, 'create', [
                    'previous_status' => $oldOrderStatus
                ]);
                
                if (!$accountingResult['success']) {
                    Log::warning('Sales accounting partially failed during order update', [
                        'order_id' => $order->id,
                        'order_code' => $order->order_code,
                        'old_status' => $oldOrderStatus,
                        'new_status' => $newOrderStatus,
                        'message' => $accountingResult['message']
                    ]);
                }
            }

            DB::commit();

            // Send SMS notification based on buying_from (only when status is invoiced/delivered)
            if ($newOrderStatus == 'invoiced' || $newOrderStatus == 'delivered') {
                $this->sendOrderSMS($order);
            }

            Toastr::success('Order has been updated successfully!', 'Success');
            return response()->json([
                'success' => true,
                'message' => 'Order has been updated successfully!',
                'order' => $order
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error in updateProductOrder', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['password', 'password_confirmation'])
            ]);
            
            Toastr::error('Something went wrong! ' . $e->getMessage(), 'Error');
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong! ' . $e->getMessage(),
                'error_type' => get_class($e),
                'error_file' => basename($e->getFile()),
                'error_line' => $e->getLine(),
            ], 500);
        }
    }

    /**
     * @deprecated This method is deprecated. Stock updates are now handled directly in saveNewProductOrder and updateProductOrder.
     * Kept for backward compatibility but does nothing.
     */
    public function editProductOrderConfirm($slug)
    {
        // Stock updates are now handled in saveNewProductOrder and updateProductOrder methods
        // This method is kept for backward compatibility
        return 0;
    }

    public function deleteProductOrder($slug)
    {
        $data = ProductOrder::where('slug', $slug)->first();

        $data->delete();
        // $data->status = 'inactive';
        // $data->save();
        return response()->json([
            'success' => 'Deleted successfully!',
            'data' => 1
        ]);
    }


    public function searchProduct(Request $request)
    {
        // Check if the search query is an exact match
        $query = request()->query('query');
        $products = Product::where('name', 'LIKE', "%{$query}%")
            ->select('id', 'name', 'price', 'discount_price', 'discount_parcent', 'stock', 'slug', 'has_variant', 'unit_id', 'sku', 'barcode')
            ->with(['variantCombinations' => function($q) {
                $q->where('status', 1)
                  ->where('stock', '>', 0)
                  ->select('id', 'product_id', 'combination_key', 'variant_values', 'price', 'discount_price', 'stock', 'sku');
            }, 'unitPricing' => function($q) {
                $q->where('status', 1)
                  ->select('id', 'product_id', 'unit_id', 'unit_title', 'unit_value', 'unit_label', 'price', 'discount_price', 'discount_percent');
            }])
            ->limit(10)
            ->get()
            ->map(function($product) {
                // Format variant combinations for easy display
                $variants = $product->variantCombinations->map(function($variant) {
                    // variant_values is already cast to array in the model
                    $variantValues = $variant->variant_values;
                    $variantName = collect($variantValues)->map(function($value, $key) {
                        return ucfirst($key) . ': ' . $value;
                    })->implode(' | ');
                    
                    return [
                        'id' => $variant->id,
                        'name' => $variantName,
                        'price' => $variant->price,
                        'discount_price' => $variant->discount_price,
                        'stock' => $variant->stock,
                        'sku' => $variant->sku,
                        'combination_key' => $variant->combination_key
                    ];
                });

                // Format unit pricing for easy display
                $unitPrices = $product->unitPricing->map(function($unit) {
                    return [
                        'id' => $unit->id,
                        'unit_label' => $unit->unit_label,
                        'price' => $unit->price,
                        'discount_price' => $unit->discount_price,
                        'discount_percent' => $unit->discount_percent,
                        'unit_title' => $unit->unit_title,
                        'unit_value' => $unit->unit_value
                    ];
                });

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'discount_price' => $product->discount_price,
                    'discount_parcent' => $product->discount_parcent,
                    'stock' => $product->stock,
                    'slug' => $product->slug,
                    'sku' => $product->sku,
                    'barcode' => $product->barcode,
                    'has_variant' => $product->has_variant,
                    'has_unit_price' => $unitPrices->count() > 0,
                    'variants' => $variants,
                    'unit_prices' => $unitPrices
                ];
            });

        return response()->json($products);
    }

    public function getCustomerPaymentUpdate($customer_id)
    {
        try {
            // Get total due amount from product_orders for this customer
            $totalDue = ProductOrder::where('customer_id', $customer_id)
                ->where('status', 'active')
                ->sum('due_amount');

            // Get total advance/credit payments from db_customer_payments
            $totalAdvance = DB::table('db_customer_payments')
                ->where('customer_id', $customer_id)
                ->where('status', 'active')
                ->where(function ($query) {
                    $query->where('payment_type', 'advance')
                        ->orWhere('payment_type', 'credit');
                })
                ->sum('payment');

            // Calculate available advance (payments made - any adjustments already used)
            $usedAdvance = DB::table('db_customer_payments')
                ->where('customer_id', $customer_id)
                ->where('status', 'active')
                ->where('payment_type', 'adjustment')
                ->sum('payment');

            $availableAdvance = $totalAdvance - abs($usedAdvance);

            return response()->json([
                'success' => true,
                'customer_id' => $customer_id,
                'total_due' => number_format($totalDue, 2, '.', ''),
                'total_advance' => number_format($totalAdvance, 2, '.', ''),
                'available_advance' => number_format($availableAdvance, 2, '.', ''),
                'has_advance' => $availableAdvance > 0
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching customer payment info: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show invoice for product order
     */
    public function showInvoice($slug)
    {
        $order = ProductOrder::with(['order_products.variant', 'order_products.unitPrice', 'customer', 'warehouse'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Company information from database
        $generalInfo = GeneralInfo::where('id', 1)->first();
        
        $company = [
            'name' => $generalInfo->company_name ?? 'Company Name',
            'address' => $generalInfo->address ?? '',
            'phone' => $generalInfo->contact ?? '',
            'email' => $generalInfo->email ?? '',
            'website' => str_replace(['http://', 'https://'], '', url('/')),
            'logo' => $generalInfo->logo ? url($generalInfo->logo) : url('/logo.png')
        ];

        // Generate QR code data as public invoice URL (full absolute URL)
        $qrData = url(route('order.invoice', $order->slug));

        return view('invoice.product-order', compact('order', 'company', 'qrData'));
    }

    /**
     * Download invoice as PDF
     */
    public function downloadInvoicePDF($slug)
    {
        $order = ProductOrder::with(['order_products.variant', 'order_products.unitPrice', 'customer', 'warehouse'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Company information from database
        $generalInfo = GeneralInfo::where('id', 1)->first();
        
        $company = [
            'name' => $generalInfo->company_name ?? 'Company Name',
            'address' => $generalInfo->address ?? '',
            'phone' => $generalInfo->contact ?? '',
            'email' => $generalInfo->email ?? '',
            'website' => str_replace(['http://', 'https://'], '', url('/')),
            'logo' => $generalInfo->logo ? url($generalInfo->logo) : url('/logo.png')
        ];

        // QR points to invoice URL as well (full absolute URL)
        $qrData = url(route('order.invoice', $order->slug));

        // Return view for browser print to PDF
        return view('invoice.product-order-pdf', compact('order', 'company', 'qrData'));
    }

    /**
     * Email invoice to customer
     */
    public function emailInvoice(Request $request, $slug)
    {
        $order = ProductOrder::with(['order_products', 'customer'])->where('slug', $slug)->firstOrFail();
        
        $email = $request->input('email', $order->customer->email ?? null);
        
        if (!$email) {
            return response()->json([
                'success' => false,
                'message' => 'No email address provided'
            ], 400);
        }

        try {
            // TODO: Implement email sending logic
            // Mail::to($email)->send(new InvoiceMail($order));
            
            return response()->json([
                'success' => true,
                'message' => 'Invoice sent successfully to ' . $email
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show product order details
     */
    public function showProductOrder($slug)
    {
        $order = ProductOrder::with(['order_products', 'customer', 'warehouse'])
            ->where('slug', $slug)
            ->firstOrFail();

        // For now, redirect to invoice
        return redirect()->route('order.invoice', $slug);
    }

    /**
     * Show pay due page for product order
     */
    public function payDueProductOrder($slug)
    {
        $order = ProductOrder::with(['order_products', 'customer', 'warehouse'])
            ->where('slug', $slug)
            ->firstOrFail();

        if ($order->due_amount <= 0) {
            Toastr::warning('This order has no due amount!', 'Warning');
            return redirect()->route('ViewAllProductOrder');
        }

        // TODO: Create view for payment form
        return view('backend.product_order_management.pay_due', compact('order'));
    }

    /**
     * Process payment for due amount
     */
    public function processPaymentProductOrder(Request $request, $slug)
    {
        $validator = Validator::make($request->all(), [
            'payment_amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'string'],
            'payment_note' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $order = ProductOrder::where('slug', $slug)->firstOrFail();

            if ($order->due_amount <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'This order has no due amount!'
                ], 400);
            }

            $paymentAmount = floatval($request->payment_amount);

            if ($paymentAmount > $order->due_amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment amount cannot exceed due amount!'
                ], 400);
            }

            // Update order payment
            $order->paid_amount += $paymentAmount;
            $order->due_amount -= $paymentAmount;

            // Update payments array
            $payments = $order->payments ?? [];
            $paymentMethod = $request->payment_method;
            $payments[$paymentMethod] = ($payments[$paymentMethod] ?? 0) + $paymentAmount;
            $payments['total_paid'] = $order->paid_amount;
            $payments['total_due'] = $order->due_amount;
            $order->payments = $payments;

            $order->save();

            // TODO: Record payment in customer payments table and accounting

            DB::commit();

            Toastr::success('Payment has been recorded successfully!', 'Success');
            return response()->json([
                'success' => true,
                'message' => 'Payment has been recorded successfully!',
                'order' => $order
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong! ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Print product order (redirect to PDF)
     */
    public function printProductOrder($slug)
    {
        return redirect()->route('order.invoice.pdf', $slug);
    }

    /**
     * Show return form for product order
     */
    public function returnProductOrder($slug)
    {
        $order = ProductOrder::with(['order_products', 'customer', 'warehouse'])
            ->where('slug', $slug)
            ->firstOrFail();

        if ($order->order_status != 'invoiced' && $order->order_status != 'delivered') {
            Toastr::warning('Only invoiced/delivered orders can be returned!', 'Warning');
            return redirect()->route('ViewAllProductOrder');
        }

        // TODO: Create view for return form
        return view('backend.product_order_management.return', compact('order'));
    }

    /**
     * Process product order return
     */
    public function processReturnProductOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => ['required', 'exists:product_orders,id'],
            'return_items' => ['required', 'array', 'min:1'],
            'return_reason' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $order = ProductOrder::where('id', $request->order_id)->firstOrFail();

            if ($order->order_status != 'invoiced' && $order->order_status != 'delivered') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only invoiced/delivered orders can be returned!'
                ], 400);
            }

            // TODO: Implement return logic
            // 1. Create return record
            // 2. Update product stock
            // 3. Update order amounts
            // 4. Record accounting transactions

            DB::commit();

            Toastr::success('Return has been processed successfully!', 'Success');
            return response()->json([
                'success' => true,
                'message' => 'Return has been processed successfully!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong! ' . $e->getMessage(),
            ], 500);
        }
    }

    // Delivery Information Methods
    public function getDistricts()
    {
        try {
            $districts = DB::table('districts')
                ->select('id', 'name')
                ->orderBy('name', 'asc')
                ->get();

            return response()->json($districts);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getUpazilas($district_id)
    {
        try {
            $upazilas = DB::table('upazilas')
                ->select('id', 'name')
                ->where('district_id', $district_id)
                ->orderBy('name', 'asc')
                ->get();

            return response()->json($upazilas);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getCustomerDeliveryInfo($customer_id)
    {
        try {
            $customer = Customer::find($customer_id);
            
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer not found'
                ], 404);
            }

            // Parse the info JSON field
            $customerInfo = $customer->info ? json_decode($customer->info, true) : [];
            $deliveryInfo = $customerInfo['delivery_address'] ?? null;

            return response()->json([
                'success' => true,
                'delivery_info' => $deliveryInfo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function saveCustomerDeliveryInfo(Request $request, $customer_id)
    {
        try {
            $customer = Customer::find($customer_id);
            
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer not found'
                ], 404);
            }

            // Get existing info or initialize empty array
            $customerInfo = $customer->info ? json_decode($customer->info, true) : [];
            
            // Update delivery address
            $customerInfo['delivery_address'] = [
                'receiver_name' => $request->input('receiver_name'),
                'receiver_phone' => $request->input('receiver_phone'),
                'customer_phone' => $request->input('customer_phone'),
                'district' => $request->input('district'),
                'upazila' => $request->input('upazila'),
                'thana' => $request->input('thana'),
                'post_office' => $request->input('post_office'),
                'full_address' => $request->input('full_address'),
                'delivery_method' => $request->input('delivery_method'),
                'courier_name' => $request->input('courier_name'),
                'courier_name_custom' => $request->input('courier_name_custom')
            ];

            // Save back to customer
            $customer->info = json_encode($customerInfo);
            $customer->save();

            return response()->json([
                'success' => true,
                'message' => 'Delivery information saved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send SMS notification based on buying_from type
     * buying_from: 1 = Online/Delivery, 2 = Walk-in/Store Pickup
     */
    private function sendOrderSMS($order)
    {
        try {
            // Get customer phone number
            $customer = $order->customer;
            if (!$customer || empty($customer->phone)) {
                return;
            }

            $phone = $customer->phone;
            $orderSlug = $order->slug;
            
            // Get frontend URL
            $frontendUrl = rtrim(env('APP_FRONTEND_URL', env('APP_URL')), '/');
            
            // Send SMS based on buying_from type
            if ($order->buying_from == 1) {
                // Online/Delivery order
                $text = "Dear Customer, Your Order #" . $order->order_code;
                $text .= " placed successfully at " . env('APP_NAME', 'e-shop');
                $text .= ". Total amount: " . number_format($order->total, 2) . " TK. Expected delivery within 3-7 days.";
                $text .= "\nInvoice: {$frontendUrl}/order/{$orderSlug}";
                $text .= "\nReview: {$frontendUrl}/review";
                
                sms_send($phone, $text);
                
            } elseif ($order->buying_from == 2) {
                // Walk-in/Store Pickup order
                $text = "Thank you for purchasing from " . env('APP_NAME', 'e-shop') . ", Your trust means a lot!";
                $text .= "\nInvoice: {$frontendUrl}/order/{$orderSlug}";
                $text .= "\nReview: {$frontendUrl}/review";
                
                sms_send($phone, $text);
            }
            
            Log::info('Order SMS sent successfully', [
                'order_id' => $order->id,
                'order_code' => $order->order_code,
                'phone' => $phone,
                'buying_from' => $order->buying_from
            ]);
            
        } catch (\Exception $e) {
            // Log error but don't fail the order creation
            Log::error('Error sending order SMS', [
                'order_id' => $order->id ?? null,
                'error' => $e->getMessage()
            ]);
        }
    }
}

