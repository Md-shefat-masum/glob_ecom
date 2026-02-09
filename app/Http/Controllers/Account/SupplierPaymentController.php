<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Account\Models\DbSupplierPayment;
use App\Http\Controllers\Account\Models\DbPurchasePayment;
use App\Http\Controllers\Account\Models\DbPaymentType;
use App\Http\Controllers\Account\Models\AcAccount;
use App\Http\Controllers\Account\Models\AcTransaction;
use App\Http\Controllers\Inventory\Models\ProductPurchaseOrder;
use App\Models\ProductPurchaseReturn;
use App\Http\Controllers\Inventory\Models\ProductSupplier;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Brian2694\Toastr\Facades\Toastr;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class SupplierPaymentController extends Controller
{
    /**
     * Show supplier payments index page
     */
    public function index(Request $request)
    {
        // Calculate at a glance totals
        $totalPurchaseAmount = ProductPurchaseOrder::where('status', 'active')->sum('total');
        $totalPurchaseReturnAmount = ProductPurchaseReturn::where('status', 'active')->sum('total');
        $totalPaid = DbPurchasePayment::where('status', 'active')->sum('payment');
        $totalPayable = $totalPurchaseAmount - $totalPurchaseReturnAmount - $totalPaid;

        if ($request->ajax()) {
            $suppliers = ProductSupplier::where('status', 'active');
            
            return Datatables::of($suppliers)
                ->editColumn('id', function ($supplier) {
                    return $supplier->id;
                })
                ->editColumn('name', function ($supplier) {
                    return $supplier->name;
                })
                ->editColumn('total_purchase', function ($supplier) {
                    $total = ProductPurchaseOrder::where('product_supplier_id', $supplier->id)
                        ->where('status', 'active')
                        ->sum('total');
                    return '৳' . number_format($total, 2);
                })
                ->editColumn('paid', function ($supplier) {
                    $total = DbPurchasePayment::where('supplier_id', $supplier->id)
                        ->where('status', 'active')
                        ->sum('payment');
                    return '৳' . number_format($total, 2);
                })
                ->editColumn('return_amount', function ($supplier) {
                    $total = ProductPurchaseReturn::where('product_supplier_id', $supplier->id)
                        ->where('status', 'active')
                        ->sum('total');
                    return '৳' . number_format($total, 2);
                })
                ->editColumn('due', function ($supplier) {
                    $totalPurchase = ProductPurchaseOrder::where('product_supplier_id', $supplier->id)
                        ->where('status', 'active')
                        ->sum('total');
                    $totalReturn = ProductPurchaseReturn::where('product_supplier_id', $supplier->id)
                        ->where('status', 'active')
                        ->sum('total');
                    $totalPaid = DbPurchasePayment::where('supplier_id', $supplier->id)
                        ->where('status', 'active')
                        ->sum('payment');
                    $due = ($totalPurchase - $totalReturn) - $totalPaid;
                    $class = $due > 0 ? 'text-danger' : 'text-success';
                    return '<span class="' . $class . '">৳' . number_format($due, 2) . '</span>';
                })
                ->editColumn('advance', function ($supplier) {
                    $total = DbSupplierPayment::where('supplier_id', $supplier->id)
                        ->where('status', 'active')
                        ->where('payment_type', 'advance')
                        ->sum('payment');
                    return '৳' . number_format($total, 2);
                })
                ->addColumn('action', function ($supplier) {
                    $totalPurchase = ProductPurchaseOrder::where('product_supplier_id', $supplier->id)
                        ->where('status', 'active')
                        ->sum('total');
                    $totalReturn = ProductPurchaseReturn::where('product_supplier_id', $supplier->id)
                        ->where('status', 'active')
                        ->sum('total');
                    $totalPaid = DbPurchasePayment::where('supplier_id', $supplier->id)
                        ->where('status', 'active')
                        ->sum('payment');
                    $due = ($totalPurchase - $totalReturn) - $totalPaid;
                    $totalAdvance = DbSupplierPayment::where('supplier_id', $supplier->id)
                        ->where('status', 'active')
                        ->where('payment_type', 'advance')
                        ->sum('payment');
                    
                    $btn = '<div class="dropdown">';
                    $btn .= '<button class="btn-sm btn-primary dropdown-toggle rounded" type="button" id="actionDropdown' . $supplier->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                    $btn .= 'Action';
                    $btn .= '</button>';
                    $btn .= '<div class="dropdown-menu" aria-labelledby="actionDropdown' . $supplier->id . '">';
                    if ($due > 0) {
                        $btn .= '<a class="dropdown-item" href="' . route('CreateSupplierPaymentDue', $supplier->id) . '"><i class="fas fa-money-bill-wave"></i> Pay Due</a>';
                    }
                    if ($totalAdvance > 0) {
                        $btn .= '<a class="dropdown-item" href="' . route('CreateSupplierPaymentAdvance', $supplier->id) . '"><i class="fas fa-hand-holding-usd"></i> Pay Advance</a>';
                    }
                    $btn .= '<a class="dropdown-item" href="' . route('ViewSupplierPayments', $supplier->id) . '"><i class="fas fa-list"></i> Payments</a>';
                    $btn .= '</div>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['due', 'action'])
                ->make(true);
        }

        return view('backend.supplier_payments.index', compact('totalPurchaseAmount', 'totalPurchaseReturnAmount', 'totalPayable'));
    }

    /**
     * Show payment form for paying due purchases
     */
    public function createDue($supplierId = null)
    {
        $suppliers = ProductSupplier::where('status', 'active')->get();
        $selectedSupplier = $supplierId ? ProductSupplier::findOrFail($supplierId) : null;
        $paymentTypes = DbPaymentType::where('status', 'active')->get();
        
        return view('backend.supplier_payments.create_due', compact('suppliers', 'selectedSupplier', 'paymentTypes'));
    }

    /**
     * Show payment form for advance payment
     */
    public function createAdvance($supplierId = null)
    {
        $suppliers = ProductSupplier::where('status', 'active')->get();
        $selectedSupplier = $supplierId ? ProductSupplier::findOrFail($supplierId) : null;
        $paymentTypes = DbPaymentType::where('status', 'active')->get();
        
        return view('backend.supplier_payments.create_advance', compact('suppliers', 'selectedSupplier', 'paymentTypes'));
    }

    /**
     * Get account balance by payment type (AJAX)
     */
    public function getAccountBalance($paymentTypeId)
    {
        try {
            $paymentType = DbPaymentType::findOrFail($paymentTypeId);
            
            // Find account with this paymenttypes_id
            $account = AcAccount::where('paymenttypes_id', $paymentTypeId)
                ->where('status', 'active')
                ->first();
            
            if (!$account) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account not found for this payment type',
                    'balance' => 0
                ]);
            }
            
            // Calculate account balance from transactions
            $debits = AcTransaction::where('debit_account_id', $account->id)->sum('debit_amt');
            $credits = AcTransaction::where('credit_account_id', $account->id)->sum('credit_amt');
            
            // For asset accounts: balance = debits - credits
            // For liability/equity accounts: balance = credits - debits
            $balance = 0;
            if ($account->account_type === 'asset') {
                $balance = $debits - $credits;
            } elseif (in_array($account->account_type, ['liability', 'equity'])) {
                $balance = $credits - $debits;
            } else {
                // Default: debits - credits
                $balance = $debits - $credits;
            }
            
            return response()->json([
                'success' => true,
                'account_id' => $account->id,
                'account_name' => $account->account_name,
                'balance' => $balance,
                'account_type' => $account->account_type
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching account balance: ' . $e->getMessage(),
                'balance' => 0
            ], 500);
        }
    }

    /**
     * Get supplier due purchases data (helper method)
     */
    private function getSupplierDuePurchasesData($supplierId)
    {
        $purchases = ProductPurchaseOrder::where('product_supplier_id', $supplierId)
            ->where('status', 'active')
            ->orderBy('date', 'asc')
            ->orderBy('id', 'asc')
            ->get();
        
        $duePurchases = [];
        foreach ($purchases as $purchase) {
            $totalPurchase = $purchase->total;
            $totalReturn = ProductPurchaseReturn::where('purchase_code', $purchase->code)
                ->where('status', 'active')
                ->sum('total');
            $totalPaid = DbPurchasePayment::where('purchase_id', $purchase->id)
                ->where('status', 'active')
                ->sum('payment');
            
            $dueAmount = ($totalPurchase - $totalReturn) - $totalPaid;
            
            if ($dueAmount > 0) {
                $duePurchases[] = [
                    'id' => $purchase->id,
                    'code' => $purchase->code,
                    'purchase_date' => $purchase->date,
                    'total' => $totalPurchase,
                    'return_amount' => $totalReturn,
                    'paid_amount' => $totalPaid,
                    'due_amount' => $dueAmount,
                ];
            }
        }
        
        return $duePurchases;
    }

    /**
     * Get supplier due purchases (AJAX)
     */
    public function getSupplierDuePurchases($supplierId)
    {
        try {
            $supplier = ProductSupplier::findOrFail($supplierId);
            
            $duePurchases = $this->getSupplierDuePurchasesData($supplierId);
            
            // Get available advance
            $totalAdvance = DbSupplierPayment::where('supplier_id', $supplierId)
                ->where('status', 'active')
                ->where('payment_type', 'advance')
                ->sum('payment');
            
            $usedAdvance = DbSupplierPayment::where('supplier_id', $supplierId)
                ->where('status', 'active')
                ->where('payment_type', 'adjustment')
                ->sum('payment');
            
            $availableAdvance = $totalAdvance - abs($usedAdvance);
            
            return response()->json([
                'success' => true,
                'supplier' => $supplier,
                'due_purchases' => $duePurchases,
                'available_advance' => $availableAdvance,
                'total_due' => collect($duePurchases)->sum('due_amount')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching supplier data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store payment (with FIFO allocation or advance)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|exists:product_suppliers,id',
            'payment_amount' => 'required|numeric|min:0.01',
            'payment_type' => 'required|in:due,advance',
            'payment_mode' => 'required|string',
            'payment_date' => 'required|date',
            'account_id' => 'required|exists:ac_accounts,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $supplier = ProductSupplier::findOrFail($request->supplier_id);
            $user = auth()->user();
            $paymentAmount = floatval($request->payment_amount);
            
            // Get account and validate balance
            $account = AcAccount::findOrFail($request->account_id);
            $debits = AcTransaction::where('debit_account_id', $account->id)->sum('debit_amt');
            $credits = AcTransaction::where('credit_account_id', $account->id)->sum('credit_amt');
            $accountBalance = 0;
            if ($account->account_type === 'asset') {
                $accountBalance = $debits - $credits;
            } elseif (in_array($account->account_type, ['liability', 'equity'])) {
                $accountBalance = $credits - $debits;
            } else {
                $accountBalance = $debits - $credits;
            }
            
            // Validate account balance
            if ($paymentAmount > $accountBalance) {
                return response()->json([
                    'success' => false,
                    'message' => "Insufficient balance in account. Available: ৳" . number_format($accountBalance, 2)
                ], 422);
            }
            
            // For due payments, validate against total due
            if ($request->payment_type === 'due') {
                $duePurchases = $this->getSupplierDuePurchasesData($supplier->id);
                $totalDue = collect($duePurchases)->sum('due_amount');
                if ($paymentAmount > $totalDue) {
                    return response()->json([
                        'success' => false,
                        'message' => "Payment amount exceeds total due amount. Total due: ৳" . number_format($totalDue, 2)
                    ], 422);
                }
            }

            // Check if payment allocations are provided (FIFO)
            if ($request->payment_allocations) {
                $allocations = json_decode($request->payment_allocations, true);
                
                if (!$allocations || !is_array($allocations)) {
                    throw new \Exception('Invalid payment allocation data');
                }

                $totalAllocated = 0;
                $purchasesPaid = [];
                $remainingAmount = $paymentAmount;

                foreach ($allocations as $allocation) {
                    $allocAmount = floatval($allocation['payment_amount']);
                    
                    if ($allocAmount <= 0) continue;

                    // Payment to a purchase
                    if (!empty($allocation['purchase_id'])) {
                        $purchase = ProductPurchaseOrder::findOrFail($allocation['purchase_id']);
                        
                        // Calculate due amount
                        $totalPurchase = $purchase->total;
                        $totalReturn = ProductPurchaseReturn::where('purchase_code', $purchase->code)
                            ->where('status', 'active')
                            ->sum('total');
                        $totalPaid = DbPurchasePayment::where('purchase_id', $purchase->id)
                            ->where('status', 'active')
                            ->sum('payment');
                        $dueAmount = ($totalPurchase - $totalReturn) - $totalPaid;
                        
                        // Validate payment doesn't exceed purchase's due amount
                        if ($allocAmount > $dueAmount) {
                            throw new \Exception("Payment amount (৳{$allocAmount}) exceeds due amount (৳{$dueAmount}) for purchase {$purchase->code}");
                        }

                        // Record purchase payment
                        $purchasePayment = DbPurchasePayment::create([
                            'purchase_id' => $purchase->id,
                            'supplier_id' => $supplier->id,
                            'payment_date' => $request->payment_date,
                            'payment_type' => $request->payment_mode,
                            'payment' => $allocAmount,
                            'payment_note' => $request->payment_note ?? "Payment for purchase {$purchase->code}",
                            'creator' => $user->id,
                            'slug' => Str::orderedUuid() . uniqid(),
                            'status' => 'active',
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ]);

                        // Record supplier payment
                        DbSupplierPayment::create([
                            'purchasepayment_id' => $purchasePayment->id,
                            'supplier_id' => $supplier->id,
                            'payment_date' => $request->payment_date,
                            'payment_type' => 'due',
                            'payment' => $allocAmount,
                            'payment_note' => $request->payment_note ?? "Payment for purchase {$purchase->code}",
                            'creator' => $user->id,
                            'slug' => Str::orderedUuid() . uniqid(),
                            'status' => 'active',
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ]);

                        $purchasesPaid[] = $purchase->code;
                        $totalAllocated += $allocAmount;
                        $remainingAmount -= $allocAmount;
                    } 
                    // Advance payment
                    else if (!empty($allocation['is_advance'])) {
                        // Record as advance payment
                        DbSupplierPayment::create([
                            'purchasepayment_id' => null,
                            'supplier_id' => $supplier->id,
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

                        $totalAllocated += $allocAmount;
                    }
                }

                // Record accounting transaction for total payment
                $supplierPayment = DbSupplierPayment::latest()->first();
                record_supplier_payment_accounting($supplier, $paymentAmount, $request->payment_date, $request->payment_note ?? 'Supplier payment', $account->id, $supplierPayment);

                DB::commit();

                Toastr::success('Payment recorded successfully!', 'Success');
                return response()->json([
                    'success' => true,
                    'message' => 'Payment recorded successfully!',
                    'redirect' => route('ViewAllSupplierPayments')
                ]);
            } else {
                // Simple advance payment without allocations
                $supplierPayment = DbSupplierPayment::create([
                    'purchasepayment_id' => null,
                    'supplier_id' => $supplier->id,
                    'payment_date' => $request->payment_date,
                    'payment_type' => 'advance',
                    'payment' => $paymentAmount,
                    'payment_note' => $request->payment_note ?? 'Advance payment',
                    'creator' => $user->id,
                    'slug' => Str::orderedUuid() . uniqid(),
                    'status' => 'active',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);

                // Record accounting transaction
                record_supplier_payment_accounting(
                    supplier: $supplier, 
                    paymentAmount: $paymentAmount, 
                    paymentDate: $request->payment_date, 
                    note: $request->payment_note ?? 'Advance payment',
                    supplierPayment: $supplierPayment
                );

                DB::commit();

                Toastr::success('Advance payment recorded successfully!', 'Success');
                return response()->json([
                    'success' => true,
                    'message' => 'Advance payment recorded successfully!',
                    'redirect' => route('ViewAllSupplierPayments')
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Supplier Payment Error', [
                'message' => $e->getMessage(),
                'supplier_id' => $request->supplier_id ?? null,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong! ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * View all payments for a supplier
     */
    public function viewPayments(Request $request, $supplierId = null)
    {
        $supplier = $supplierId ? ProductSupplier::findOrFail($supplierId) : null;
        $suppliers = ProductSupplier::where('status', 'active')->get();
        
        if ($request->ajax()) {
            $query = DbSupplierPayment::with('supplier')
                ->orderBy('payment_date', 'desc')
                ->orderBy('id', 'desc');
            
            if ($supplierId) {
                $query->where('supplier_id', $supplierId);
            }
            
            if ($request->date_from) {
                $query->where('payment_date', '>=', $request->date_from);
            }
            
            if ($request->date_to) {
                $query->where('payment_date', '<=', $request->date_to);
            }
            
            $data = $query->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('supplier', function ($data) {
                    return $data->supplier ? $data->supplier->name : 'N/A';
                })
                ->editColumn('purchase_code', function ($data) {
                    if ($data->purchasepayment_id) {
                        $purchasePayment = DbPurchasePayment::find($data->purchasepayment_id);
                        if ($purchasePayment && $purchasePayment->purchase_id) {
                            $purchase = ProductPurchaseOrder::find($purchasePayment->purchase_id);
                            return $purchase ? $purchase->code : 'N/A';
                        }
                    }
                    return '<span class="badge badge-info">Advance Payment</span>';
                })
                ->editColumn('payment_type', function ($data) {
                    $badges = [
                        'due' => '<span class="badge badge-success">Due Payment</span>',
                        'advance' => '<span class="badge badge-info">Advance</span>',
                        'adjustment' => '<span class="badge badge-secondary">Adjustment</span>',
                    ];
                    return $badges[$data->payment_type] ?? '<span class="badge badge-light">' . ucfirst($data->payment_type) . '</span>';
                })
                ->editColumn('payment', function ($data) {
                    $amount = number_format(abs($data->payment), 2);
                    return '<span class="text-success">৳' . $amount . '</span>';
                })
                ->editColumn('payment_date', function ($data) {
                    return date("Y-m-d", strtotime($data->payment_date));
                })
                ->rawColumns(['purchase_code', 'payment_type', 'payment'])
                ->make(true);
        }

        return view('backend.supplier_payments.payments', compact('supplier', 'suppliers'));
    }
}
