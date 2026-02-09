<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Account\Models\DbExpense;
use App\Http\Controllers\Account\Models\DbExpenseCategory;
use App\Http\Controllers\Account\Models\DbPaymentType;
use App\Http\Controllers\Account\Models\AcAccount;
use App\Http\Controllers\Account\Models\AcTransaction;
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

class ExpenseController extends Controller
{
    public function addNewExpense()
    {
        $expense_categories = DbExpenseCategory::where('status', 'active')
            ->with(['debitAccount', 'creditAccount'])
            ->get();
        return view('backend.expense.create', compact('expense_categories'));
    }

    /**
     * Get expense category details including account mappings and balance
     */
    public function getExpenseCategoryDetails(Request $request)
    {
        try {
            $categoryId = $request->category_id;

            if (!$categoryId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category ID is required'
                ]);
            }

            $category = DbExpenseCategory::findOrFail($categoryId);
            
            if (!$category->credit_id || !$category->debit_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Expense category account mapping is incomplete'
                ]);
            }

            // Get credit account and calculate balance
            $creditAccount = AcAccount::findOrFail($category->credit_id);
            $debitAccount = AcAccount::findOrFail($category->debit_id);

            // Calculate balance for credit account
            $debits = AcTransaction::where('debit_account_id', $creditAccount->id)
                ->where('status', 'active')
                ->sum('debit_amt') ?? 0;
            $credits = AcTransaction::where('credit_account_id', $creditAccount->id)
                ->where('status', 'active')
                ->sum('credit_amt') ?? 0;
            
            $balance = 0;
            if ($creditAccount->account_type === 'asset' || $creditAccount->account_type === 'expense') {
                $balance = $debits - $credits;
            } elseif ($creditAccount->account_type === 'liability' || $creditAccount->account_type === 'equity' || $creditAccount->account_type === 'revenue') {
                $balance = $credits - $debits;
            } else {
                $balance = $debits - $credits;
            }

            return response()->json([
                'success' => true,
                'credit_account' => [
                    'id' => $creditAccount->id,
                    'name' => $creditAccount->account_name,
                    'balance' => $balance,
                    'formatted_balance' => number_format($balance, 2)
                ],
                'debit_account' => [
                    'id' => $debitAccount->id,
                    'name' => $debitAccount->account_name
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function saveNewExpense(Request $request)
    {
        $request->validate([
            'expense_for' => ['required', 'string', 'max:255'],
            'expense_amt' => ['required', 'numeric', 'min:0.01'],
            'expense_date' => ['required', 'date'],
            'expense_category_id' => ['required', 'exists:db_expense_categories,id'],
            'reference_no' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
        ], [
            'expense_for.required' => 'Expense for is required.',
            'expense_for.max' => 'Expense for must not exceed 255 characters.',
            'expense_amt.required' => 'Expense amount is required.',
            'expense_amt.numeric' => 'Expense amount must be a number.',
            'expense_amt.min' => 'Expense amount must be greater than 0.',
            'expense_date.required' => 'Expense date is required.',
            'expense_date.date' => 'Expense date must be a valid date.',
            'expense_category_id.required' => 'Expense category is required.',
            'expense_category_id.exists' => 'Selected expense category does not exist.',
        ]);

        // Get expense category with account mappings
        $expenseCategory = DbExpenseCategory::findOrFail($request->expense_category_id);
        
        if (!$expenseCategory->credit_id || !$expenseCategory->debit_id) {
            Toastr::error('Expense category account mapping is incomplete!', 'Error');
            return back()->withInput();
        }

        // Check available balance for credit account
        $creditAccount = AcAccount::findOrFail($expenseCategory->credit_id);
        $debits = AcTransaction::where('debit_account_id', $creditAccount->id)
            ->where('status', 'active')
            ->sum('debit_amt') ?? 0;
        $credits = AcTransaction::where('credit_account_id', $creditAccount->id)
            ->where('status', 'active')
            ->sum('credit_amt') ?? 0;
        
        // Calculate balance based on account type
        $balance = 0;
        if ($creditAccount->account_type === 'asset' || $creditAccount->account_type === 'expense') {
            $balance = $debits - $credits;
        } elseif ($creditAccount->account_type === 'liability' || $creditAccount->account_type === 'equity' || $creditAccount->account_type === 'revenue') {
            $balance = $credits - $debits;
        } else {
            $balance = $debits - $credits;
        }

        // Validate balance
        if ($balance < $request->expense_amt) {
            Toastr::error('Insufficient balance! Available: ৳' . number_format($balance, 2), 'Error');
            return back()->withInput();
        }

        // Generate expense code (EX20001, EX20002, ...)
        $lastCountId = DbExpense::max('count_id') ?? 0;
        $countId = $lastCountId + 1;
        $expenseCode = 'EX' . (20000 + $countId);

        $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower($request->expense_for));
        $slug = preg_replace('!\s+!', '-', $clean);

        // Generate payment code for transactions
        $paymentCode = generate_payment_code('EXP');

        DB::beginTransaction();
        try {
            DbExpense::create([
                'store_id' => auth()->user()->store_id ?? 1,
                'count_id' => $countId,
                'category_id' => $expenseCategory->id,
                'payment_type_id' => null, // Not needed based on category mapping
                'account_id' => $expenseCategory->debit_id,
                'debit_account_id' => $expenseCategory->debit_id,
                'credit_account_id' => $expenseCategory->credit_id,
                'expense_code' => $expenseCode,
                'expense_date' => $request->expense_date,
                'expense_for' => $request->expense_for,
                'expense_amt' => $request->expense_amt,
                'reference_no' => $request->reference_no ?? '',
                'note' => $request->note ?? '',
                'creator' => auth()->user()->id,
                'slug' => $slug . time() . rand(1000, 9999),
                'status' => 'active',
                'created_at' => Carbon::now('Asia/Dhaka')
            ]);

            // Create debit transaction (credit account debited)
            AcTransaction::create([
                'store_id' => auth()->user()->store_id ?? null,
                'payment_code' => $paymentCode,
                'transaction_date' => $request->expense_date,
                'transaction_type' => 'EXPENSE',
                'debit_account_id' => $expenseCategory->debit_id,
                'debit_amt' => $request->expense_amt,
                'credit_account_id' => null,
                'credit_amt' => null,
                'ref_expense_id' => $expenseCategory->id,
                'note' => $request->note ?? 'Expense: ' . $request->expense_for,
                'creator' => auth()->user()->id,
                'slug' => Str::slug('expense-' . $expenseCode) . '-' . time() . rand(1000, 9999),
                'status' => 'active',
                'created_at' => Carbon::now('Asia/Dhaka')
            ]);

            // Create credit transaction (debit account credited)
            AcTransaction::create([
                'store_id' => auth()->user()->store_id ?? null,
                'payment_code' => $paymentCode,
                'transaction_date' => $request->expense_date,
                'transaction_type' => 'EXPENSE',
                'debit_account_id' => null,
                'debit_amt' => null,
                'credit_account_id' => $expenseCategory->credit_id,
                'credit_amt' => $request->expense_amt,
                'ref_expense_id' => $expenseCategory->id,
                'note' => $request->note ?? 'Expense: ' . $request->expense_for,
                'creator' => auth()->user()->id,
                'slug' => Str::slug('expense-' . $expenseCode) . '-' . time() . rand(10000, 99999),
                'status' => 'active',
                'created_at' => Carbon::now('Asia/Dhaka')
            ]);

            DB::commit();
            Toastr::success('Expense added successfully!', 'Success');
            return redirect()->route('ViewAllExpense');

        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error('Error: ' . $e->getMessage(), 'Error');
            Log::error('Expense Create Error: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all(),
            ]);
            return back()->withInput();
        }
    }

    public function viewAllExpense(Request $request)
    {
        // dd(5);
        if ($request->ajax()) {
            $data = DbExpense::with([
                'user', 
                'expense_category.debitAccount',
                'expense_category.creditAccount',
                'payment_type'
            ]);

            return Datatables::of($data)
                ->addColumn('from_account', function ($data) {
                    if ($data->expense_category && $data->expense_category->creditAccount) {
                        return $data->expense_category->creditAccount->account_name;
                    }
                    return 'N/A';
                })
                ->addColumn('to_account', function ($data) {
                    if ($data->expense_category && $data->expense_category->debitAccount) {
                        return $data->expense_category->debitAccount->account_name;
                    }
                    return 'N/A';
                })
                ->addColumn('payment_type', function ($data) {
                    return $data->payment_type ? $data->payment_type->payment_type : 'N/A';
                })
                ->addColumn('user', function ($data) {
                    return $data->user ? $data->user->name : 'N/A';
                })
                ->editColumn('expense_amt', function ($data) {
                    return '৳ ' . number_format($data->expense_amt, 2);
                })
                ->editColumn('created_at', function ($data) {
                    return date("Y-m-d h:i", strtotime($data->created_at));
                })
                ->addColumn('action', function ($data) {
                    $btn = '<a href="' . route('ViewExpenseDetails', $data->id) . '" class="btn-sm btn-info rounded" title="View Details"><i class="fas fa-eye"></i></a>';
                    $btn .= ' <a href="' . route('PrintExpense', $data->id) . '" target="_blank" class="btn-sm btn-primary rounded" title="Print Voucher"><i class="fas fa-print"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('backend.expense.view');
    }

    public function editExpense($slug)
    {
        $data = DbExpense::where('status', 'active')->where('slug', $slug)->first();
        $accounts = AcAccount::where('status', 'active')->get();
        $expense_categories = DbExpenseCategory::where('status', 'active')->get();
        $payment_types = DbPaymentType::where('status', 'active')->get();
        // $nestedData = $this->buildTree($accounts);
        $nestedDataAll = $this->buildTree($accounts);
        // $nestedDataAll =AcAccount::where('status', 'active')
        //                                 ->where('account_name', '!=', 'Expense')
        //                                 ->get();
        $nestedData = AcAccount::where('account_name', 'Expense')->with('inc')->where('status', 'active')->get();

        // $transaction = AcTransaction::where('status', 'active')->where('payment_code', $data->expense_code)->get();;


        return view('backend.expense.edit', compact(
            'data',
            'accounts',
            'expense_categories',
            'payment_types',
            'nestedData',
            'nestedDataAll',
        )
        );
    }

    private function buildTree($accounts, $parentId = null)
    {
        $tree = [];

        foreach ($accounts as $account) {
            // Skip 'Expense' account and its children
            if ($account->account_name === 'Expense') {
                continue;  // Skip this account and its children
            }

            if ($account->parent_id == $parentId) {
                // Recursively build the tree for children
                $children = $this->buildTree($accounts, $account->id);

                // Build the node for the current account
                $node = [
                    'id' => $account->id,
                    'text' => $account->account_name,
                ];

                // If there are children, add them to the node
                if (!empty($children)) {
                    $node['inc'] = $children;
                }

                // Add the current node to the tree
                $tree[] = $node;
            }
        }

        return $tree;
    }

    public function updateExpense(Request $request)
    {
        // dd(request()->all());
        $request->validate([
            'expense_for' => ['required', 'string', 'max:255'],
            'expense_amt' => ['required'],
            'expense_date' => ['required'],
            'payment_type_id' => ['required'],
        ], [
            'expense_for.required' => 'expense for is required.',
            'expense_for.max' => 'expense for must not exceed 100 characters.',
            'expense_amt.required' => 'expense amount is required',
            'expense_date.required' => 'expense date is required',
            'payment_type_id.required' => 'payment type is required',
        ]);

        // Check if the selected product_warehouse_room_id exists for the selected product_warehouse_id        
        $data = DbExpense::where('id', request()->expense_id)->first();

        $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower($data->expense_for)); //remove all non alpha numeric
        $slug = preg_replace('!\s+!', '-', $clean);

        $data->store_id = request()->expense_store_id ?? $data->expense_store_id;
        $data->category_id = request()->expense_category_id ?? $data->expense_category_id;
        $data->account_id = request()->expense_account_id ?? $data->account_id;
        $data->debit_account_id = request()->expense_account_id ?? $data->expense_account_id;
        $data->credit_account_id = request()->asset_cash_account_id ?? $data->asset_cash_account_id;
        $data->payment_type_id = request()->payment_type_id ?? $data->payment_type_id;
        $data->expense_for = request()->expense_for ?? $data->expense_for;
        $data->expense_code = $data->expense_code ?? '';
        $data->expense_date = request()->expense_date ?? $data->expense_date;
        $data->expense_amt = request()->expense_amt ?? $data->expense_amt;
        $data->reference_no = request()->reference_no ?? $data->reference_no;
        $data->note = request()->note ?? $data->note;


        if ($data->expense_for != $request->expense_for) {
            $data->slug = $slug . time() . rand();
        }

        $data->creator = auth()->user()->id;
        $data->status = request()->status ?? $data->status;
        $data->updated_at = Carbon::now('Asia/Dhaka');
        $data->save();






        // $request->validate([
        //     'deposit_date' => ['required'],
        //     'debit_credit_amount' => ['required'],
        // ], [
        //     'deposit_date.required' => 'deposit date is required.',
        //     'debit_credit_amount.required' => 'amount is required',
        // ]);



        // Check if the selected product_warehouse_room_id exists for the selected product_warehouse_id        
        // $data = AcTransaction::where('id', request()->deposit_id)->first();
        // $data_two = AcTransaction::where('payment_code', $data->payment_code)->get();
        // dd($data_two);


        // Fetch all transactions with the same payment_code
        $data_two = AcTransaction::where('payment_code', $data->expense_code)->get();

        // Loop through the transactions and update based on conditions
        foreach ($data_two as $item) {

            if (request()->has('asset_cash_account_id') && $item->credit_account_id != 0) {
                $item->credit_account_id = request()->asset_cash_account_id;
                $item->debit_amt = 0.0000;
                $item->credit_amt = request()->expense_amt;
            }

            if (request()->has('expense_account_id') && $item->debit_account_id != 0) {
                $item->debit_account_id = request()->expense_account_id;
                $item->credit_amt = 0.0000;
                $item->debit_amt = request()->expense_amt;
            }

            if (request()->has('asset_cash_account_id') && $item->credit_account_id == 0) {
                $item->debit_amt = request()->expense_amt;
            }
            if (request()->has('expense_account_id') && $item->debit_account_id == 0) {
                $item->credit_amt = request()->expense_amt;
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
        return redirect()->route('ViewAllExpense');
    }

    /**
     * Show expense details
     */
    public function showExpense($id)
    {
        $expense = DbExpense::with([
            'user',
            'expense_category.debitAccount',
            'expense_category.creditAccount',
            'payment_type'
        ])->findOrFail($id);

        return view('backend.expense.details', compact('expense'));
    }

    /**
     * Print expense voucher
     */
    public function printExpense($id)
    {
        $expense = DbExpense::with([
            'user',
            'expense_category.debitAccount',
            'expense_category.creditAccount',
            'payment_type'
        ])->findOrFail($id);

        // Get general info for company details
        $generalInfo = \App\Models\GeneralInfo::first();

        return view('backend.expense.print', compact('expense', 'generalInfo'));
    }
}
