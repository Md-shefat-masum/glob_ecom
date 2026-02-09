<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Account\Models\AccountIncome;
use App\Http\Controllers\Account\Models\AccountIncomeCategory;
use App\Http\Controllers\Account\Models\AcAccount;
use App\Http\Controllers\Account\Models\AcTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AccountIncomeController extends Controller
{
    public function addNewIncome()
    {
        $income_categories = AccountIncomeCategory::where('status', 'active')
            ->with(['debitAccount', 'creditAccount'])
            ->get();
        return view('backend.income.create', compact('income_categories'));
    }

    /**
     * Get income category details including account mappings and balance
     */
    public function getIncomeCategoryDetails(Request $request)
    {
        try {
            $categoryId = $request->category_id;

            if (!$categoryId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category ID is required'
                ]);
            }

            $category = AccountIncomeCategory::findOrFail($categoryId);
            
            if (!$category->credit_id || !$category->debit_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Income category account mapping is incomplete'
                ]);
            }

            // Get credit account (asset account - where money comes from) and calculate balance
            $creditAccount = AcAccount::findOrFail($category->credit_id);
            $debitAccount = AcAccount::findOrFail($category->debit_id);

            // Calculate balance for credit account (asset account)
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

    public function saveNewIncome(Request $request)
    {
        $request->validate([
            'income_for' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'date' => ['required', 'date'],
            'category_id' => ['required', 'exists:ac_income_categories,id'],
            'reference' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
        ], [
            'income_for.required' => 'Income for is required.',
            'income_for.max' => 'Income for must not exceed 255 characters.',
            'amount.required' => 'Income amount is required.',
            'amount.numeric' => 'Income amount must be a number.',
            'amount.min' => 'Income amount must be greater than 0.',
            'date.required' => 'Income date is required.',
            'date.date' => 'Income date must be a valid date.',
            'category_id.required' => 'Income category is required.',
            'category_id.exists' => 'Selected income category does not exist.',
        ]);

        // Get income category with account mappings
        $incomeCategory = AccountIncomeCategory::findOrFail($request->category_id);
        
        if (!$incomeCategory->credit_id || !$incomeCategory->debit_id) {
            Toastr::error('Income category account mapping is incomplete!', 'Error');
            return back()->withInput();
        }

        // Generate income code (IN10001, IN10002, ...)
        $lastCountId = AccountIncome::max('count_id') ?? 0;
        $countId = $lastCountId + 1;
        $incomeCode = 'IN' . (10000 + $countId);

        $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower($request->income_for));
        $slug = preg_replace('!\s+!', '-', $clean);

        // Generate payment code for transactions
        $paymentCode = generate_payment_code('INC');

        DB::beginTransaction();
        try {
            AccountIncome::create([
                'store_id' => auth()->user()->store_id ?? 1,
                'count_id' => $countId,
                'code' => $incomeCode,
                'category_id' => $incomeCategory->id,
                'date' => $request->date,
                'reference' => $request->reference ?? '',
                'income_for' => $request->income_for,
                'amount' => $request->amount,
                'note' => $request->note ?? '',
                'debit_account_id' => $incomeCategory->debit_id,
                'credit_account_id' => $incomeCategory->credit_id,
                'created_by' => auth()->user()->name ?? null,
                'created_date' => Carbon::now('Asia/Dhaka')->format('Y-m-d'),
                'created_time' => Carbon::now('Asia/Dhaka')->format('H:i:s'),
                'creator' => auth()->user()->id,
                'slug' => $slug . time() . rand(1000, 9999),
                'status' => 'active',
                'created_at' => Carbon::now('Asia/Dhaka')
            ]);

            // Create debit transaction (credit account debited - asset account receives money)
            AcTransaction::create([
                'store_id' => auth()->user()->store_id ?? null,
                'payment_code' => $paymentCode,
                'transaction_date' => $request->date,
                'transaction_type' => 'INCOME',
                'debit_account_id' => $incomeCategory->credit_id,
                'debit_amt' => $request->amount,
                'credit_account_id' => null,
                'credit_amt' => null,
                'ref_expense_id' => null,
                'note' => $request->note ?? 'Income: ' . $request->income_for,
                'creator' => auth()->user()->id,
                'slug' => Str::slug('income-' . $incomeCode) . '-' . time() . rand(1000, 9999),
                'status' => 'active',
                'created_at' => Carbon::now('Asia/Dhaka')
            ]);

            // Create credit transaction (debit account credited - revenue account)
            AcTransaction::create([
                'store_id' => auth()->user()->store_id ?? null,
                'payment_code' => $paymentCode,
                'transaction_date' => $request->date,
                'transaction_type' => 'INCOME',
                'debit_account_id' => null,
                'debit_amt' => null,
                'credit_account_id' => $incomeCategory->debit_id,
                'credit_amt' => $request->amount,
                'ref_expense_id' => null,
                'note' => $request->note ?? 'Income: ' . $request->income_for,
                'creator' => auth()->user()->id,
                'slug' => Str::slug('income-' . $incomeCode) . '-' . time() . rand(10000, 99999),
                'status' => 'active',
                'created_at' => Carbon::now('Asia/Dhaka')
            ]);

            DB::commit();
            Toastr::success('Income added successfully!', 'Success');
            return redirect()->route('ViewAllIncome');

        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error('Error: ' . $e->getMessage(), 'Error');
            Log::error('Income Create Error: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all(),
            ]);
            return back()->withInput();
        }
    }

    public function viewAllIncome(Request $request)
    {
        if ($request->ajax()) {
            $data = AccountIncome::with([
                'user',
                'income_category.debitAccount',
                'income_category.creditAccount'
            ]);

            return Datatables::of($data)
                ->addColumn('from_account', function ($data) {
                    if ($data->income_category && $data->income_category->creditAccount) {
                        return $data->income_category->creditAccount->account_name;
                    }
                    return 'N/A';
                })
                ->addColumn('to_account', function ($data) {
                    if ($data->income_category && $data->income_category->debitAccount) {
                        return $data->income_category->debitAccount->account_name;
                    }
                    return 'N/A';
                })
                ->addColumn('user', function ($data) {
                    return $data->user ? $data->user->name : 'N/A';
                })
                ->editColumn('amount', function ($data) {
                    return '৳ ' . number_format($data->amount, 2);
                })
                ->editColumn('created_at', function ($data) {
                    return date("Y-m-d h:i", strtotime($data->created_at));
                })
                ->addColumn('action', function ($data) {
                    $btn = '<a href="' . route('ViewIncomeDetails', $data->id) . '" class="btn-sm btn-info rounded" title="View Details"><i class="fas fa-eye"></i></a>';
                    $btn .= ' <a href="' . route('PrintIncome', $data->id) . '" target="_blank" class="btn-sm btn-primary rounded" title="Print Voucher"><i class="fas fa-print"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('backend.income.view');
    }

    /**
     * Show income details
     */
    public function showIncome($id)
    {
        $income = AccountIncome::with([
            'user',
            'income_category.debitAccount',
            'income_category.creditAccount'
        ])->findOrFail($id);

        return view('backend.income.details', compact('income'));
    }

    /**
     * Print income voucher
     */
    public function printIncome($id)
    {
        $income = AccountIncome::with([
            'user',
            'income_category.debitAccount',
            'income_category.creditAccount'
        ])->findOrFail($id);

        // Get general info for company details
        $generalInfo = \App\Models\GeneralInfo::first();

        return view('backend.income.print', compact('income', 'generalInfo'));
    }
}

