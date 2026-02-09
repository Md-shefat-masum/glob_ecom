<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Account\Models\AcAccount;
use App\Http\Controllers\Account\Models\AcTransaction;
use App\Http\Controllers\Account\Models\AcMoneyDeposit;
use App\Http\Controllers\Account\Models\AcMoneyWithdraw;
use App\Http\Controllers\Account\Models\DbPaymentType;
use App\Models\User;
// use App\Http\Controllers\Account\Models\DbExpenseCategory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function addNewDeposit()
    {
        $paymentTypes = DbPaymentType::where('status', 'active')->get();
        $investors = User::where('user_type', '5')->where('status', 1)->get();
        return view('backend.transaction.create', compact('paymentTypes', 'investors'));
    }

    public function saveNewDeposit(Request $request)
    {
        $request->validate([
            'deposit_date' => ['required', 'date'],
            'payment_type' => ['required'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'investor_id' => ['nullable'],
            'note' => ['nullable', 'string'],
        ], [
            'deposit_date.required' => 'Deposit date is required.',
            'payment_type.required' => 'Payment type is required.',
            'amount.required' => 'Amount is required.',
            'amount.numeric' => 'Amount must be a number.',
            'amount.min' => 'Amount must be greater than 0.',
        ]);

        try {
            DB::beginTransaction();

            $user = auth()->user();
            $paymentType = DbPaymentType::findOrFail($request->payment_type);

            // Get accounts linked to this payment type
            $paymentAccount = AcAccount::where('paymenttypes_id', $paymentType->id)
                ->where('status', 'active')
                ->first();

            if (!$paymentAccount) {
                Toastr::error('No account found for selected payment type!', 'Error');
                return back();
            }

            // Get debit and credit accounts from paymentAccount
            // Check if paymentAccount has debit_account_id and credit_account_id fields
            $debitAccountId = $paymentAccount->debit_account_id ?? null;
            $creditAccountId = $paymentAccount->credit_account_id ?? null;

            // If not found in paymentAccount, use the paymentAccount itself as debit
            if (!$debitAccountId) {
                $debitAccountId = $paymentAccount->id;
            }

            // Handle investor account if investor_id is provided
            $creditAccountId = null;
            if ($request->investor_id) {
                $investor = User::findOrFail($request->investor_id);
                
                // Find investor account with account_name = investor_{investor->id}
                $investorAccountName = 'investor_' . $investor->id;
                $investorAccount = AcAccount::where('account_name', $investorAccountName)
                    ->where('status', 'active')
                    ->first();

                // If not found, create new account head for investor
                if (!$investorAccount) {
                    $investorNote = ($investor->name ?? auth()->user()->name);
                    if ($investor->email) {
                        $investorNote .= ', ' . $investor->email;
                    }
                    if ($investor->phone) {
                        $investorNote .= ', ' . $investor->phone;
                    }

                    // Find equity account (id = 15) and get max short_code from its children
                    $equityAccount = AcAccount::find(15);
                    if (!$equityAccount) {
                        Toastr::error('Equity account (ID: 15) not found!', 'Error');
                        return back();
                    }

                    // Get all children of equity account (parent_id = 15) and find max short_code
                    $childrenAccounts = AcAccount::where('parent_id', 15)
                        ->where('status', 'active')
                        ->get();

                    // Find max numeric short_code value
                    $maxShortCodeValue = 3000; // Start from equity account code
                    foreach ($childrenAccounts as $child) {
                        if ($child->short_code) {
                            // Extract numeric value from short_code
                            $numericValue = preg_replace('/[^0-9]/', '', $child->short_code);
                            if ($numericValue && is_numeric($numericValue)) {
                                $numericValue = (int)$numericValue;
                                if ($numericValue > $maxShortCodeValue) {
                                    $maxShortCodeValue = $numericValue;
                                }
                            }
                        }
                    }

                    // Increment short_code
                    $newShortCode = $maxShortCodeValue + 1;

                    // Generate account_code
                    $accountCode = 'AC-' . $newShortCode;

                    $investorAccount = AcAccount::create([
                        'store_id' => $user->store_id ?? null,
                        'account_type' => 'equity',
                        'parent_id' => 15,
                        'normal_balance' => 'credit',
                        'account_name' => $investorAccountName,
                        'account_selection_name' => $investorAccountName,
                        'note' => $investorNote,
                        'balance' => 0.0000,
            'status' => 'active',
                        'creator' => $user->id,
                        'slug' => Str::slug($investorAccountName) . '-' . time(),
                        'short_code' => $newShortCode,
                        'account_code' => $accountCode,
                        'created_at' => Carbon::now('Asia/Dhaka'),
                        'updated_at' => Carbon::now('Asia/Dhaka')
        ]);
                }

                $creditAccountId = $investorAccount->id;
            } else {
                // Use credit_account_id from paymentAccount if available
                if ($creditAccountId) {
                    $creditAccount = AcAccount::find($creditAccountId);
                    if (!$creditAccount || $creditAccount->status !== 'active') {
                        // Fallback to Owner's Equity account
                        $equityAccount = AcAccount::where('account_type', 'equity')
                            ->where('status', 'active')
                            ->first();
                        if ($equityAccount) {
                            $creditAccountId = $equityAccount->id;
                        } else {
                            Toastr::error('Credit account not found!', 'Error');
                            return back();
                        }
                    }
                } else {
                    // Get Owner's Equity account (credit account)
                    $equityAccount = AcAccount::where('account_type', 'equity')
                        ->where('status', 'active')
                        ->first();

                    if (!$equityAccount) {
                        Toastr::error('Owner\'s Equity account not found!', 'Error');
                        return back();
                    }

                    $creditAccountId = $equityAccount->id;
                }
            }

            // Get the actual account objects for transactions
            $debitAccount = AcAccount::findOrFail($debitAccountId);
            $creditAccount = AcAccount::findOrFail($creditAccountId);

            // Generate payment code (MD = Money Deposit)
            $paymentCode = generate_payment_code('MD');
            
            // Generate slug
            $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower($paymentType->payment_type));
            $slug = preg_replace('!\s+!', '-', $clean) . '-' . time();

            // Generate reference number
            $referenceNo = 'DEP-' . date('Ymd') . '-' . str_pad(AcMoneyDeposit::max('id') + 1 ?? 1, 4, '0', STR_PAD_LEFT);

            // Save to ac_moneydeposits table
            $moneyDeposit = AcMoneyDeposit::create([
                'store_id' => $user->store_id ?? null,
                'payment_type_id' => $paymentType->id,
                'deposit_date' => $request->deposit_date,
                'reference_no' => $referenceNo,
                'debit_account_id' => $debitAccountId,
                'credit_account_id' => $creditAccountId,
                'amount' => $request->amount,
                'note' => $request->note ?? ($request->investor_id ? 'Investor invested money in the business' : 'Owner invested money in the business'),
                'investor_id' => $request->investor_id ?? null,
                'created_by' => substr($user->name, 0, 50),
                'created_date' => $request->deposit_date,
                'created_time' => date('H:i:s'),
                'creator' => $user->id,
                'slug' => $slug,
            'status' => 'active',
                'created_at' => Carbon::now('Asia/Dhaka'),
                'updated_at' => Carbon::now('Asia/Dhaka')
            ]);

            // Get account type name for note
            $accountTypeName = $debitAccount->account_name ?? $debitAccount->account_selection_name ?? 'Account';

            // Create double entry transactions
            $transactions = [];

            // Row 1: Debit Entry Only
            $transactions[] = [
                'store_id' => $user->store_id ?? null,
                'payment_code' => $paymentCode,
                'transaction_date' => $request->deposit_date,
                'transaction_type' => 'MONEY_DEPOSIT',
                'debit_account_id' => $debitAccountId,
                'credit_account_id' => null,
                'debit_amt' => $request->amount,
                'credit_amt' => null,
                'note' => 'Cash received in ' . $accountTypeName. ' - '. ($investor->name ?? auth()->user()->name),
                'ref_moneydeposits_id' => $moneyDeposit->id,
                'created_by' => substr($user->name, 0, 50),
                'creator' => $user->id,
                'slug' => uniqid() . time(),
                'status' => 'active',
                'created_at' => Carbon::now('Asia/Dhaka'),
                'updated_at' => Carbon::now('Asia/Dhaka')
            ];

            // Row 2: Credit Entry Only
            $creditNote = $request->investor_id 
                ? 'Investor invested money in the business' 
                : 'Owner invested money in the business';
            
            $transactions[] = [
                'store_id' => $user->store_id ?? null,
                'payment_code' => $paymentCode,
                'transaction_date' => $request->deposit_date,
                'transaction_type' => 'MONEY_DEPOSIT',
                'debit_account_id' => null,
                'credit_account_id' => $creditAccountId,
                'debit_amt' => null,
                'credit_amt' => $request->amount,
                'note' => $creditNote. ' - '. ($investor->name ?? auth()->user()->name),
                'ref_moneydeposits_id' => $moneyDeposit->id,
                'created_by' => substr($user->name, 0, 50),
                'creator' => $user->id,
                'slug' => uniqid() . time(),
                'status' => 'active',
                'created_at' => Carbon::now('Asia/Dhaka'),
                'updated_at' => Carbon::now('Asia/Dhaka')
            ];

            // Insert transactions
            AcTransaction::insert($transactions);

            DB::commit();

            Toastr::success('Deposit recorded successfully!', 'Success');
            return redirect()->route('ViewAllDeposit');

        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error('Error: ' . $e->getMessage(), 'Error');
        return back();
        }
    }

    public function viewAllDeposit(Request $request)
    {
        if ($request->ajax()) {
            $data = AcMoneyDeposit::with([
                    'investor:id,name,email,phone',
                    'paymentType:id,payment_type',
                    'debitAccount:id,account_name',
                    'creditAccount:id,account_name',
                    'creator_info:id,name,email,phone'
                ])
                ->where('status', 'active');

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('owner_name', function ($data) {
                    // Check if investor_id exists and investor relationship is loaded and is an object
                    if ($data->investor_id && isset($data->investor) && is_object($data->investor) && isset($data->investor->name)) {
                        return '<span style="color:blue; font-weight: 600;">' . $data->investor->name . '</span>';
                    }
                    return '<span style="color:green; font-weight: 600;">Owner</span>';
                })
                ->addColumn('date', function ($data) {
                    return $data->deposit_date ? date('Y-m-d', strtotime($data->deposit_date)) : 'N/A';
                })
                ->addColumn('note', function ($data) {
                    $note = $data->note ?? 'N/A';
                    $noteLength = strlen($note);
                    if ($noteLength > 50) {
                        return '<span title="' . htmlspecialchars($note) . '">' . substr($note, 0, 50) . '...</span>';
                    }
                    return $note;
                })
                ->addColumn('media', function ($data) {
                    // Check if paymentType relationship is loaded and is an object
                    if (isset($data->paymentType) && is_object($data->paymentType) && isset($data->paymentType->payment_type)) {
                        return '<span style="color:purple; font-weight: 600;">' . $data->paymentType->payment_type . '</span>';
                    }
                    return '<span style="color:red;">N/A</span>';
                })
                ->addColumn('amount', function ($data) {
                    $amount = $data->amount ?? 0;
                    $formatted = number_format($amount, 2);
                    return '<span style="color:green; font-weight: 600;">৳ ' . $formatted . '</span>';
                })
                ->addColumn('creator_name', function ($data) {
                    // Check if creator relationship is loaded and is an object
                    return $data->creator_info->name ?? '';
                })
                ->editColumn('created_at', function ($data) {
                    return date("Y-m-d h:i a", strtotime($data->created_at));
                })
                ->rawColumns(['owner_name', 'note', 'media', 'amount'])
                ->make(true);
        }
        return view('backend.transaction.view');
    }


    private function buildTree($accounts, $parentId = null)
    {
        $tree = [];

        foreach ($accounts as $account) {
            if ($account->parent_id == $parentId) {
                $children = $this->buildTree($accounts, $account->id);
                $node = [
                    'id' => $account->id,
                    'text' => $account->account_name,
                ];

                if (!empty($children)) {
                    $node['inc'] = $children;
                }

                $tree[] = $node;
            }
        }

        return $tree;
    }


    public function editDeposit($slug)
    {
        $data = AcTransaction::where('status', 'active')->where('slug', $slug)->first();
        $accounts = AcAccount::where('status', 'active')->get();
        $nestedData = $this->buildTree($accounts);

        return view('backend.transaction.edit', compact('data', 'nestedData'));
    }

    public function updateDeposit(Request $request)
    {
        // dd(request()->all());
        // dd(request()->all());
        $request->validate([
            'deposit_date' => ['required'],
            'debit_credit_amount' => ['required'],
        ], [
            'deposit_date.required' => 'deposit date is required.',
            'debit_credit_amount.required' => 'amount is required',
        ]);



        // Check if the selected product_warehouse_room_id exists for the selected product_warehouse_id        
        $data = AcTransaction::where('id', request()->deposit_id)->first();
        $data_two = AcTransaction::where('payment_code', $data->payment_code)->get();
        // dd($data_two);
     


        // Fetch all transactions with the same payment_code
        $data_two = AcTransaction::where('payment_code', $data->payment_code)->get();

        // Loop through the transactions and update based on conditions
        foreach ($data_two as $item) {

            if(request()->has('credit_id') && $item->credit_account_id != 0) {                
                $item->credit_account_id = request()->credit_id;
                $item->debit_amt = 0.0000;
                $item->credit_amt = request()->debit_credit_amount;
            }

            if(request()->has('debit_id') && $item->debit_account_id != 0) {                
                $item->debit_account_id = request()->debit_id;
                $item->credit_amt = 0.0000;
                $item->debit_amt = request()->debit_credit_amount;
            }

            if(request()->has('credit_id') && $item->credit_account_id == 0) {
                $item->debit_amt = request()->debit_credit_amount;
            }
            if(request()->has('debit_id') && $item->debit_account_id == 0) {
                $item->credit_amt = request()->debit_credit_amount;
            }


            $item->transaction_date = request()->deposit_date ?? $item->transaction_date;
            $item->note = request()->note ?? $item->note;
            $item->creator = auth()->user()->id;
            $item->status = request()->status ?? $item->status;
            $item->updated_at = Carbon::now('Asia/Dhaka');
            $item->save();
            // dd($item);
        }

        Toastr::success('Successfully Updated', 'Success!');
        return redirect()->route('ViewAllDeposit');
    }



    public function deleteDeposit($slug)
    {
        $data = AcTransaction::where('slug', $slug)->first();

        $data->delete();
        // $data->status = 'inactive';
        // $data->save();

        return response()->json([
            'success' => 'Deleted successfully!',
            'data' => 1
        ]);
    }





    public function showLedger() {
        return view('');
    }

    /**
     * Show create withdraw form
     */
    public function addNewWithdraw()
    {
        $paymentTypes = DbPaymentType::where('status', 'active')->get()->map(function($pt) {
            return [
                'id' => $pt->id,
                'payment_type' => $pt->payment_type,
                'total_amount' => $pt->total_amount ?? 0
            ];
        });
        $investors = User::where('user_type', '5')->where('status', 1)->get();
        return view('backend.transaction.withdraw', compact('paymentTypes', 'investors'));
    }

    /**
     * Store new withdraw
     */
    public function saveNewWithdraw(Request $request)
    {
        $request->validate([
            'withdraw_date' => ['required', 'date'],
            'payment_type' => ['required'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'investor_id' => ['required'],
            'note' => ['required', 'string', 'min:10'],
        ], [
            'withdraw_date.required' => 'Withdraw date is required.',
            'payment_type.required' => 'Payment type is required.',
            'amount.required' => 'Amount is required.',
            'amount.numeric' => 'Amount must be a number.',
            'amount.min' => 'Amount must be greater than 0.',
            'investor_id.required' => 'Investor is required for withdraw.',
            'note.required' => 'Note is required.',
            'note.min' => 'Note must be at least 10 characters.',
        ]);

        try {
            DB::beginTransaction();

            $user = auth()->user();
            $paymentType = DbPaymentType::findOrFail($request->payment_type);
            $investor = User::findOrFail($request->investor_id);

            // Get accounts linked to this payment type
            $paymentAccount = AcAccount::where('paymenttypes_id', $paymentType->id)
                ->where('status', 'active')
                ->first();

            if (!$paymentAccount) {
                Toastr::error('No account found for selected payment type!', 'Error');
                return back();
            }

            // Check payment type balance
            $paymentTypeBalance = $paymentType->total_amount ?? 0;
            if ($paymentTypeBalance <= 0) {
                Toastr::error('Selected payment type has no available balance! Available: ৳' . number_format($paymentTypeBalance, 2), 'Error');
                return back()->withInput();
            }

            // Check if withdrawal amount exceeds payment type balance
            if ($request->amount > $paymentTypeBalance) {
                Toastr::error('Insufficient balance in selected payment type! Available: ৳' . number_format($paymentTypeBalance, 2), 'Error');
                return back()->withInput();
            }

            // Get debit and credit accounts from paymentAccount
            $debitAccountId = $paymentAccount->debit_account_id ?? null;
            if (!$debitAccountId) {
                $debitAccountId = $paymentAccount->id;
            }

            // Find investor account
            $investorAccountName = 'investor_' . $investor->id;
            $investorAccount = AcAccount::where('account_name', $investorAccountName)
                ->where('status', 'active')
                ->first();

            if (!$investorAccount) {
                Toastr::error('Investor account not found! Investor may not have deposited yet.', 'Error');
                return back();
            }

            // Calculate investor balance
            $investorDebits = AcTransaction::where('debit_account_id', $investorAccount->id)
                ->where('status', 'active')
                ->sum('debit_amt') ?? 0;

            $investorCredits = AcTransaction::where('credit_account_id', $investorAccount->id)
                ->where('status', 'active')
                ->sum('credit_amt') ?? 0;

            // For equity accounts: balance = credits - debits
            $investorBalance = $investorCredits - $investorDebits;

            // Check if investor has enough balance
            if ($request->amount > $investorBalance) {
                Toastr::error('Insufficient balance! Available: ৳' . number_format($investorBalance, 2), 'Error');
                return back()->withInput();
            }

            // Generate payment code (MW = Money Withdraw)
            $paymentCode = generate_payment_code('MW');
            
            // Generate slug
            $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower($paymentType->payment_type));
            $slug = preg_replace('!\s+!', '-', $clean) . '-' . time();

            // Generate reference number
            $referenceNo = 'WDR-' . date('Ymd') . '-' . str_pad(AcMoneyWithdraw::max('id') + 1 ?? 1, 4, '0', STR_PAD_LEFT);

            // Save to ac_money_withdraws table
            $moneyWithdraw = AcMoneyWithdraw::create([
                'store_id' => $user->store_id ?? null,
                'payment_type_id' => $paymentType->id,
                'withdraw_date' => $request->withdraw_date,
                'reference_no' => $referenceNo,
                'debit_account_id' => $investorAccount->id, // Investor account (reversed from deposit)
                'credit_account_id' => $debitAccountId, // Payment account (reversed from deposit)
                'amount' => $request->amount,
                'note' => $request->note,
                'investor_id' => $request->investor_id,
                'created_by' => substr($user->name, 0, 50),
                'created_date' => $request->withdraw_date,
                'created_time' => date('H:i:s'),
                'creator' => $user->id,
                'slug' => $slug,
                'status' => 'active',
                'created_at' => Carbon::now('Asia/Dhaka'),
                'updated_at' => Carbon::now('Asia/Dhaka')
            ]);

            // Get account type name for note
            $accountTypeName = $paymentAccount->account_name ?? $paymentAccount->account_selection_name ?? 'Account';

            // Create double entry transactions (REVERSED from deposit)
            $transactions = [];

            // Row 1: Debit Entry Only (Investor account - money going out)
            $transactions[] = [
                'store_id' => $user->store_id ?? null,
                'payment_code' => $paymentCode,
                'transaction_date' => $request->withdraw_date,
                'transaction_type' => 'MONEY_WITHDRAW',
                'debit_account_id' => $investorAccount->id,
                'credit_account_id' => null,
                'debit_amt' => $request->amount,
                'credit_amt' => null,
                'note' => 'Investor withdrew money from business - ' . ($investor->name ?? auth()->user()->name),
                'ref_moneydeposits_id' => null, // Using moneydeposits_id field as reference
                'created_by' => substr($user->name, 0, 50),
                'creator' => $user->id,
                'slug' => uniqid() . time(),
                'status' => 'active',
                'created_at' => Carbon::now('Asia/Dhaka'),
                'updated_at' => Carbon::now('Asia/Dhaka')
            ];

            // Row 2: Credit Entry Only (Payment account - cash going out)
            $transactions[] = [
                'store_id' => $user->store_id ?? null,
                'payment_code' => $paymentCode,
                'transaction_date' => $request->withdraw_date,
                'transaction_type' => 'MONEY_WITHDRAW',
                'debit_account_id' => null,
                'credit_account_id' => $debitAccountId,
                'debit_amt' => null,
                'credit_amt' => $request->amount,
                'note' => 'Cash paid from ' . $accountTypeName . ' - ' . ($investor->name ?? auth()->user()->name),
                'ref_moneydeposits_id' => null,
                'created_by' => substr($user->name, 0, 50),
                'creator' => $user->id,
                'slug' => uniqid() . time(),
                'status' => 'active',
                'created_at' => Carbon::now('Asia/Dhaka'),
                'updated_at' => Carbon::now('Asia/Dhaka')
            ];

            // Insert transactions
            AcTransaction::insert($transactions);

            DB::commit();

            Toastr::success('Withdraw recorded successfully!', 'Success');
            return redirect()->route('ViewAllWithdraw');

        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error('Error: ' . $e->getMessage(), 'Error');
            // Save error message in log file
            Log::error('Withdraw Save Error: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all(),
                'user_id' => $user->id ?? null,
            ]);
            return back()->withInput();
        }
    }

    /**
     * View all withdraws
     */
    public function viewAllWithdraw(Request $request)
    {
        if ($request->ajax()) {
            $data = AcMoneyWithdraw::with([
                    'investor:id,name,email,phone',
                    'paymentType:id,payment_type',
                    'debitAccount:id,account_name',
                    'creditAccount:id,account_name',
                    'creator_info:id,name'
                ])
                ->where('status', 'active');

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('owner_name', function ($data) {
                    if ($data->investor_id && isset($data->investor) && is_object($data->investor) && isset($data->investor->name)) {
                        return '<span style="color:blue; font-weight: 600;">' . $data->investor->name . '</span>';
                    }
                    return '<span style="color:green; font-weight: 600;">Owner</span>';
                })
                ->addColumn('date', function ($data) {
                    return $data->withdraw_date ? date('Y-m-d', strtotime($data->withdraw_date)) : 'N/A';
                })
                ->addColumn('note', function ($data) {
                    $note = $data->note ?? 'N/A';
                    $noteLength = strlen($note);
                    if ($noteLength > 50) {
                        return '<span title="' . htmlspecialchars($note) . '">' . substr($note, 0, 50) . '...</span>';
                    }
                    return $note;
                })
                ->addColumn('media', function ($data) {
                    if (isset($data->paymentType) && is_object($data->paymentType) && isset($data->paymentType->payment_type)) {
                        return '<span style="color:purple; font-weight: 600;">' . $data->paymentType->payment_type . '</span>';
                    }
                    return '<span style="color:red;">N/A</span>';
                })
                ->addColumn('amount', function ($data) {
                    $amount = $data->amount ?? 0;
                    $formatted = number_format($amount, 2);
                    return '<span style="color:red; font-weight: 600;">৳ ' . $formatted . '</span>';
                })
                ->addColumn('creator_name', function ($data) {
                    if (isset($data->creator_info) && is_object($data->creator_info) && isset($data->creator_info->name)) {
                        return ucfirst($data->creator_info->name);
                    }
                    return 'N/A';
                })
                ->editColumn('created_at', function ($data) {
                    return date("Y-m-d h:i a", strtotime($data->created_at));
                })
                ->rawColumns(['owner_name', 'note', 'media', 'amount'])
                ->make(true);
        }
        return view('backend.transaction.withdraw_list');
    }

    /**
     * Get investor balance (AJAX)
     */
    public function getInvestorBalance(Request $request)
    {
        try {
            $investorId = $request->investor_id;
            
            if (!$investorId) {
                return response()->json([
                    'success' => false,
                    'balance' => 0,
                    'message' => 'Investor ID is required'
                ]);
            }

            $investor = User::findOrFail($investorId);
            
            // Find investor account
            $investorAccountName = 'investor_' . $investor->id;
            $investorAccount = AcAccount::where('account_name', $investorAccountName)
                ->where('status', 'active')
                ->first();

            if (!$investorAccount) {
                return response()->json([
                    'success' => false,
                    'balance' => 0,
                    'message' => 'Investor account not found. Investor may not have deposited yet.'
                ]);
            }

            // Calculate investor balance
            $investorDebits = AcTransaction::where('debit_account_id', $investorAccount->id)
                ->where('status', 'active')
                ->sum('debit_amt') ?? 0;

            $investorCredits = AcTransaction::where('credit_account_id', $investorAccount->id)
                ->where('status', 'active')
                ->sum('credit_amt') ?? 0;

            // For equity accounts: balance = credits - debits
            $balance = $investorCredits - $investorDebits;

            return response()->json([
                'success' => true,
                'balance' => $balance,
                'formatted_balance' => number_format($balance, 2),
                'message' => 'Balance retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'balance' => 0,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get payment type balance (AJAX)
     */
    public function getPaymentTypeBalance(Request $request)
    {
        try {
            $paymentTypeId = $request->payment_type_id;
            
            if (!$paymentTypeId) {
                return response()->json([
                    'success' => false,
                    'balance' => 0,
                    'message' => 'Payment type ID is required'
                ]);
            }

            $paymentType = DbPaymentType::findOrFail($paymentTypeId);
            $balance = $paymentType->total_amount ?? 0;

            return response()->json([
                'success' => true,
                'balance' => $balance,
                'formatted_balance' => number_format($balance, 2),
                'message' => 'Balance retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'balance' => 0,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Print deposit invoice
     */
    public function printDeposit($id)
    {
        $deposit = AcMoneyDeposit::with(['investor', 'paymentType', 'creator_info'])
            ->findOrFail($id);
        
        $generalInfo = DB::table('general_infos')->where('id', 1)->first();
        
        return view('backend.transaction.print_deposit', compact('deposit', 'generalInfo'));
    }

    /**
     * Print withdraw invoice
     */
    public function printWithdraw($id)
    {
        $withdraw = AcMoneyWithdraw::with(['investor', 'paymentType', 'creator_info'])
            ->findOrFail($id);
        
        $generalInfo = DB::table('general_infos')->where('id', 1)->first();
        
        return view('backend.transaction.print_withdraw', compact('withdraw', 'generalInfo'));
    }

}
