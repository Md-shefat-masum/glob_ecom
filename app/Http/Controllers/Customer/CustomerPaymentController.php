<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Account\Models\AcTransaction;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Customer\Models\Customer;
use App\Http\Controllers\Account\Models\DbCustomerPayment;
use App\Http\Controllers\Account\Models\DbPaymentType;
use App\Models\ProductOrder;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Brian2694\Toastr\Facades\Toastr;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerPaymentController extends Controller
{
    /**
     * Show payment form with order
     */
    public function createWithOrder($orderId)
    {
        $order = ProductOrder::with(['customer', 'order_products'])->findOrFail($orderId);
        
        // Get all due orders for this customer
        $dueOrders = ProductOrder::where('customer_id', $order->customer_id)
            ->where('due_amount', '>', 0)
            ->where('status', 'active')
            ->orderBy('sale_date', 'asc')
            ->get();
        
        // Get customer's available advance
        $customer = Customer::find($order->customer_id);
        $availableAdvance = $customer->available_advance ?? 0;
        
        return view('backend.customer_payment.create_with_order', compact('order', 'dueOrders', 'availableAdvance'));
    }

    /**
     * Show payment form without order (advance payment)
     */
    public function create()
    {
        $paymentMethods = DbPaymentType::where('status', 'active')->get();
        $customers = Customer::where('status', 'active')->get();
        return view('backend.customer_payment.create', compact('customers', 'paymentMethods'));
    }

    /**
     * Get customer due orders (AJAX)
     */
    public function getCustomerDueOrders($customerId)
    {
        try {
            $customer = Customer::findOrFail($customerId);
            
            $dueOrders = ProductOrder::where('customer_id', $customerId)
                ->where('due_amount', '>', 0)
                ->where('status', 'active')
                ->orderBy('sale_date', 'asc')
                ->get(['id', 'order_code', 'sale_date', 'total', 'paid_amount', 'due_amount', 'order_status']);
            
            return response()->json([
                'success' => true,
                'customer' => $customer,
                'due_orders' => $dueOrders,
                'available_advance' => $customer->available_advance ?? 0,
                'total_due' => $dueOrders->sum('due_amount')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching customer data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store payment (with FIFO allocation or advance)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'payment_amount' => 'required|numeric|min:0.01',
            'payment_mode' => 'required|exists:db_paymenttypes,id',
            'payment_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $customer = Customer::findOrFail($request->customer_id);
            $user = auth()->user();
            $paymentAmount = floatval($request->payment_amount);
            $paymentMethod = DbPaymentType::where('id', $request->payment_mode)->first();

            // Check if payment allocations are provided (FIFO)
            if ($request->payment_allocations) {
                $allocations = json_decode($request->payment_allocations, true);
                
                if (!$allocations || !is_array($allocations)) {
                    throw new \Exception('Invalid payment allocation data');
                }

                $totalAllocated = 0;
                $ordersPaid = [];

                foreach ($allocations as $allocation) {
                    $allocAmount = floatval($allocation['payment_amount']);
                    
                    if ($allocAmount <= 0) continue;

                    // Payment to an order
                    if (!empty($allocation['order_id'])) {
                        $order = ProductOrder::findOrFail($allocation['order_id']);
                        
                        // Validate payment doesn't exceed order's due amount
                        if ($allocAmount > $order->due_amount) {
                            throw new \Exception("Payment amount (৳{$allocAmount}) exceeds due amount (৳{$order->due_amount}) for order {$order->order_code}");
                        }

                        // Update order payment
                        $order->paid_amount += $allocAmount;
                        $order->due_amount -= $allocAmount;
                        
                        // Update payments array
                        $payments = is_array($order->payments) ? $order->payments : json_decode($order->payments, true) ?? [];
                        $payments[$paymentMethod] = ($payments[$paymentMethod] ?? 0) + $allocAmount;
                        $payments['total_paid'] = $order->paid_amount;
                        $payments['total_due'] = $order->due_amount;
                        $order->payments = $payments;
                        $order->save();

                        // Record customer payment for this order
                        $customerPayment = DbCustomerPayment::create([
                            'customer_id' => $customer->id,
                            'order_id' => $order->id,
                            'payment_date' => $request->payment_date,
                            'payment_type' => 'received',
                            'payment' => $allocAmount,
                            'payment_mode' => $request->payment_mode,
                            'payment_mode_title' => $paymentMethod->title,
                            'payment_note' => $request->payment_note ?? "Payment for order {$order->order_code}",
                            'creator' => $user->id,
                            'slug' => Str::orderedUuid() . uniqid(),
                            'status' => 'active',
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ]);

                        record_customer_due_payment_accounting(
                            customer: $customer,
                            paymentAmount: $allocAmount,
                            paymentDate: $request->payment_date,
                            note: $request->payment_note,
                            paymentTypeId: $request->payment_mode,
                            fromAccountId: $paymentMethod->debit_account_id,
                            customerPayment: $customerPayment,
                            order: $order
                        );

                        $ordersPaid[] = $order->order_code;
                        $totalAllocated += $allocAmount;
                    } 
                    // Advance payment
                    else if (!empty($allocation['is_advance'])) {
                        // Record customer payment as advance
                        DbCustomerPayment::create([
                            'customer_id' => $customer->id,
                            'order_id' => null,
                            'payment_date' => $request->payment_date,
                            'payment_type' => 'advance',
                            'payment' => $allocAmount,
                            'payment_mode' => $request->payment_mode,
                            'payment_note' => $request->payment_note ?? 'Advance payment',
                            'creator' => $user->id,
                            'slug' => Str::orderedUuid() . uniqid(),
                            'status' => 'active',
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ]);

                        record_customer_advance_payment_accounting(
                            customer: $customer,
                            paymentAmount: $allocAmount,
                            paymentDate: $request->payment_date,
                            note: $request->payment_note,
                            paymentTypeId: $request->payment_mode,
                            fromAccountId: $paymentMethod->debit_account_id,
                            customerPayment: $customerPayment
                        );

                        $totalAllocated += $allocAmount;
                    }
                }

                // Validate total allocated matches payment amount
                if (abs($totalAllocated - $paymentAmount) > 0.01) {
                    throw new \Exception('Payment allocation mismatch');
                }

                calc_customer_balance($customer->id);

                DB::commit();

                $message = count($ordersPaid) > 0 
                    ? 'Payment allocated to ' . count($ordersPaid) . ' order(s) successfully!' 
                    : 'Advance payment recorded successfully!';

                Toastr::success($message, 'Success');
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'redirect' => route('ViewAllCustomerPayments')
                ]);
            }
            // Fallback: No allocation data (shouldn't happen with new UI)
            else {

                $customerPayment = DbCustomerPayment::create([
                    'customer_id' => $customer->id,
                    'order_id' => null,
                    'payment_date' => $request->payment_date,
                    'payment_type' => 'advance',
                    'payment' => $paymentAmount,
                    'payment_mode' => $request->payment_mode,
                    'payment_mode_title' => $paymentMethod->payment_type,
                    'payment_note' => $request->payment_note,
                    'creator' => $user->id,
                    'slug' => Str::orderedUuid() . uniqid(),
                    'status' => 'active',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);

                record_customer_advance_payment_accounting(
                    customer: $customer,
                    paymentAmount: $paymentAmount,
                    paymentDate: $request->payment_date,
                    note: $request->payment_note,
                    paymentTypeId: $request->payment_mode,
                    fromAccountId: $paymentMethod->debit_account_id,
                    customerPayment: $customerPayment
                );

                calc_customer_balance($customer->id);

                DB::commit();

                Toastr::success('Advance payment recorded successfully!', 'Success');
                return response()->json([
                    'success' => true,
                    'message' => 'Advance payment recorded successfully!',
                    'redirect' => route('ViewAllCustomerPayments')
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong! ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show payment return form
     */
    public function createReturn()
    {
        $customers = Customer::where('status', 'active')
            ->where('available_advance', '>', 0)
            ->get();
        
        return view('backend.customer_payment.create_return', compact('customers'));
    }

    /**
     * Process payment return (refund)
     */
    public function processReturn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'refund_amount' => 'required|numeric|min:0.01',
            'payment_mode' => 'required|string',
            'payment_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $customer = Customer::findOrFail($request->customer_id);
            $user = auth()->user();
            $refundAmount = floatval($request->refund_amount);

            // Validate refund doesn't exceed available advance
            if ($refundAmount > $customer->available_advance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Refund amount exceeds available advance balance!'
                ], 422);
            }

            // Deduct from customer's available advance
            $customer->available_advance -= $refundAmount;
            $customer->save();

            // Record customer payment return
            DbCustomerPayment::create([
                'customer_id' => $customer->id,
                'order_id' => null,
                'payment_date' => $request->payment_date,
                'payment_type' => 'refund',
                'payment' => -$refundAmount, // Negative amount for refund
                'payment_mode' => $request->payment_mode,
                'payment_note' => $request->payment_note ?? 'Advance payment refund',
                'creator' => $user->id,
                'slug' => Str::orderedUuid() . uniqid(),
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            DB::commit();

            Toastr::success('Refund processed successfully!', 'Success');
            return response()->json([
                'success' => true,
                'message' => 'Refund processed successfully!',
                'redirect' => route('ViewAllCustomerPayments')
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
     * View all customer payments
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DbCustomerPayment::with(['customer', 'order'])
                ->orderBy('id', 'desc')
                ->get();

            return Datatables::of($data)
                ->editColumn('customer', function ($data) {
                    return $data->customer ? $data->customer->name : 'N/A';
                })
                ->editColumn('order_code', function ($data) {
                    if ($data->order_id && $data->order) {
                        return $data->order->order_code;
                    }
                    return '<span class="badge badge-info">Advance Payment</span>';
                })
                ->editColumn('payment_type', function ($data) {
                    $badges = [
                        'received' => '<span class="badge badge-success">Received</span>',
                        'advance' => '<span class="badge badge-info">Advance</span>',
                        'refund' => '<span class="badge badge-danger">Refund</span>',
                        'credit' => '<span class="badge badge-warning">Credit</span>',
                        'adjustment' => '<span class="badge badge-secondary">Adjustment</span>',
                    ];
                    return $badges[$data->payment_type] ?? '<span class="badge badge-light">' . ucfirst($data->payment_type) . '</span>';
                })
                ->editColumn('payment', function ($data) {
                    $amount = number_format(abs($data->payment), 2);
                    if ($data->payment < 0) {
                        return '<span class="text-danger">-৳' . $amount . '</span>';
                    }
                    return '<span class="text-success">৳' . $amount . '</span>';
                })
                ->editColumn('payment_date', function ($data) {
                    return date("Y-m-d", strtotime($data->payment_date));
                })
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $btn = '<a href="' . route('ViewCustomerPaymentHistory', $data->customer_id) . '" class="btn-sm btn-info rounded">';
                    $btn .= '<i class="fas fa-history"></i> History</a>';
                    return $btn;
                })
                ->rawColumns(['order_code', 'payment_type', 'payment', 'action'])
                ->make(true);
        }
        return view('backend.customer_payment.index');
    }

    /**
     * View customer payment history
     */
    public function history($customerId)
    {
        $customer = Customer::findOrFail($customerId);
        
        $payments = DbCustomerPayment::with('order')
            ->where('customer_id', $customerId)
            ->orderBy('payment_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        
        // Calculate totals
        $totalPayments = $payments->where('payment', '>', 0)->sum('payment');
        $totalRefunds = abs($payments->where('payment', '<', 0)->sum('payment'));
        $netBalance = $totalPayments - $totalRefunds;
        
        return view('backend.customer_payment.history', compact('customer', 'payments', 'totalPayments', 'totalRefunds', 'netBalance'));
    }

    /**
     * Add relationship to DbCustomerPayment model
     */
    protected function addRelationships()
    {
        // This is just a note - actual relationship should be added to the model class
        // DbCustomerPayment::customer() -> belongsTo(Customer::class)
        // DbCustomerPayment::order() -> belongsTo(ProductOrder::class)
    }
}

