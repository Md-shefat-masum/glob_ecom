<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Account\Models\AcAccount;
use App\Http\Controllers\Account\Models\AcTransaction;
use Carbon\Carbon;

class LedgerController extends Controller
{




    public function index(Request $request)
    {
        // Validate the request parameters
        $request->validate([
            'account_id' => 'nullable|exists:ac_accounts,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        // Default to last 30 days if no dates are provided
        $startDate = $request->start_date ?? now()->subDays(30)->format('Y-m-d');
        $endDate = $request->end_date ?? now()->format('Y-m-d');

        // Fetch all accounts (required for the dropdown in the form)
        $accounts = AcAccount::all();

        // Initialize variables
        $account = null;
        $transactions = collect(); // Initialize as an empty collection
        $allTransactions = [];

        // Fetch transactions based on the request
        if ($request->account_id) {
            $account = AcAccount::findOrFail($request->account_id);

            // Fetch transactions for the selected account
            $transactions = AcTransaction::where(function ($query) use ($account) {
                $query->where('debit_account_id', $account->id)
                    ->orWhere('credit_account_id', $account->id);
            })->when($startDate, function ($query) use ($startDate) {
                $query->where('transaction_date', '>=', $startDate);
            })->when($endDate, function ($query) use ($endDate) {
                $query->where('transaction_date', '<=', $endDate);
            })->orderBy('transaction_date')->get();
        } else {
            // Fetch transactions for all accounts
            foreach ($accounts as $acc) {
                $accTransactions = AcTransaction::where(function ($query) use ($acc) {
                    $query->where('debit_account_id', $acc->id)
                        ->orWhere('credit_account_id', $acc->id);
                })->whereBetween('transaction_date', [$startDate, $endDate])
                    ->orderBy('transaction_date')
                    ->get();

                if ($accTransactions->isNotEmpty()) {
                    $allTransactions[$acc->id] = [
                        'account' => $acc,
                        'transactions' => $accTransactions,
                    ];
                }
            }
        }

        return view('backend.ledger.index', compact('accounts', 'account', 'transactions', 'allTransactions'));
    }






    public function journal(Request $request)
    {
        // Validate the request parameters
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        // Default to last 30 days if no dates are provided
        $startDate = $request->start_date ?? now()->subDays(30)->format('Y-m-d');
        $endDate = $request->end_date ?? now()->format('Y-m-d');

        // Fetch transactions for the given date range, ordered by ID descending
        $transactions = AcTransaction::whereBetween('transaction_date', [$startDate, $endDate])
            ->orderBy('id', 'asc')
            ->with(['debitAccount', 'creditAccount'])
            ->get();

        // Calculate totals
        $totalDebit = $transactions->sum('debit_amt');
        $totalCredit = $transactions->sum('credit_amt');

        return view('backend.ledger.journal', compact('transactions', 'totalDebit', 'totalCredit', 'startDate', 'endDate'));
    }









































































    // Balancesheet
    // public function balanceSheet(Request $request)
    // {
    //     $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
    //     $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());

    //     $accountCategories = ['Assets', 'Liabilities', 'Equity'];
    //     $accounts = AcAccount::whereIn('account_name', $accountCategories)
    //         ->where('parent_id', 0)
    //         ->with([
    //             'children',
    //             'debitTransactions' => function ($query) use ($startDate, $endDate) {
    //                 $query->whereBetween('transaction_date', [$startDate, $endDate]);
    //             },
    //             'creditTransactions' => function ($query) use ($startDate, $endDate) {
    //                 $query->whereBetween('transaction_date', [$startDate, $endDate]);
    //             },
    //             'children.debitTransactions' => function ($query) use ($startDate, $endDate) {
    //                 $query->whereBetween('transaction_date', [$startDate, $endDate]);
    //             },
    //             'children.creditTransactions' => function ($query) use ($startDate, $endDate) {
    //                 $query->whereBetween('transaction_date', [$startDate, $endDate]);
    //             }
    //         ])
    //         ->get();

    //     $accounts->each(function ($account) {
    //         $account->balance = $this->calculateAccountBalanceSheet($account);
    //     });

    //     $assets = $accounts->where('account_name', 'Assets');
    //     $liabilities = $accounts->where('account_name', 'Liabilities');
    //     $equity = $accounts->where('account_name', 'Equity');

    //     return view('backend.ledger.balance_sheet', compact('assets', 'liabilities', 'equity', 'startDate', 'endDate'));
    // }


    // private function calculateAccountBalanceSheet($account)
    // {
    //     // Fetch transactions where this account is used
    //     $debits = $account->debitTransactions->sum('debit_amt');
    //     $credits = $account->creditTransactions->sum('credit_amt');

    //     // Initialize balance
    //     $balance = 0;

    //     if ($account->account_name === 'Assets') {
    //         $balance = $debits - $credits;
    //     } elseif (in_array($account->account_name, ['Liabilities', 'Equity'])) {
    //         $balance = $credits - $debits;
    //     }

    //     // Include children's transactions and balances
    //     foreach ($account->children as $child) {
    //         $childDebits = $child->debitTransactions->sum('debit_amt');
    //         $childCredits = $child->creditTransactions->sum('credit_amt');

    //         if ($account->account_name === 'Assets') {
    //             $balance += $childDebits - $childCredits;
    //         } elseif (in_array($account->account_name, ['Liabilities', 'Equity'])) {
    //             $balance += $childCredits - $childDebits;
    //         }

    //         $balance += $this->calculateAccountBalanceSheet($child);
    //     }

    //     return $balance;
    // }





    public function balanceSheet(Request $request)
    {
        // Set default start_date to 30 days ago and end_date to today
        $startDate = $request->input('start_date', now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $accountCategories = ['Assets', 'Liabilities', 'Equity'];
        $accounts = AcAccount::whereIn('account_name', $accountCategories)
            ->where('parent_id', 0)
            ->with([
                'children',
                'debitTransactions' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('transaction_date', [$startDate, $endDate]);
                },
                'creditTransactions' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('transaction_date', [$startDate, $endDate]);
                },
                'children.debitTransactions' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('transaction_date', [$startDate, $endDate]);
                },
                'children.creditTransactions' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('transaction_date', [$startDate, $endDate]);
                }
            ])
            ->get();

        $accounts->each(function ($account) {
            $account->balance = $this->calculateAccountBalanceSheet($account);
        });

        $assets = $accounts->where('account_name', 'Assets');
        $liabilities = $accounts->where('account_name', 'Liabilities');
        $equity = $accounts->where('account_name', 'Equity');

        return view('backend.ledger.balance_sheet', compact('assets', 'liabilities', 'equity', 'startDate', 'endDate'));
    }

    private function calculateAccountBalanceSheet($account)
    {
        // Fetch transactions where this account is used
        $debits = $account->debitTransactions->sum('debit_amt');
        $credits = $account->creditTransactions->sum('credit_amt');

        // Initialize balance
        $balance = 0;

        if ($account->account_name === 'Assets') {
            $balance = $debits - $credits;
        } elseif (in_array($account->account_name, ['Liabilities', 'Equity'])) {
            $balance = $credits - $debits;
        }

        // Include children's transactions and balances
        foreach ($account->children as $child) {
            $childDebits = $child->debitTransactions->sum('debit_amt');
            $childCredits = $child->creditTransactions->sum('credit_amt');

            if ($account->account_name === 'Assets') {
                $balance += $childDebits - $childCredits;
            } elseif (in_array($account->account_name, ['Liabilities', 'Equity'])) {
                $balance += $childCredits - $childDebits;
            }

            $balance += $this->calculateAccountBalanceSheet($child);
        }

        return $balance;
    }




























    // Income Statement
    public function incomeStatement(Request $request)
    {
        // Default to the last 30 days if no date range is provided
        $startDate = $request->input('start_date', now('Asia/Dhaka')->subDays(30)->toDateString());
        $endDate = $request->input('end_date', now('Asia/Dhaka')->toDateString());

        $incomeStatement = $this->calculateIncomeStatement($startDate, $endDate);

        return view('backend.ledger.income_statement', compact('incomeStatement', 'startDate', 'endDate'));
    }



    private function calculateIncomeStatement($startDate, $endDate)
    {
        $revenues = AcAccount::where('account_name', 'Revenue')->with('children')->first();
        $expenses = AcAccount::where('account_name', 'Expense')->with('children')->first();

        $revenueAccounts = $this->getAllChildAccounts($revenues);
        $expenseAccounts = $this->getAllChildAccounts($expenses);

        $revenueData = $this->aggregateTransactions($revenueAccounts, 'credit_account_id', 'credit_amt', $startDate, $endDate);
        $expenseData = $this->aggregateTransactions($expenseAccounts, 'debit_account_id', 'debit_amt', $startDate, $endDate);

        $totalRevenue = array_sum(array_column($revenueData, 'amount'));
        $totalExpense = array_sum(array_column($expenseData, 'amount'));

        $netIncome = $totalRevenue - $totalExpense;

        return compact('revenueData', 'expenseData', 'totalRevenue', 'totalExpense', 'netIncome');
    }

    private function getAllChildAccounts($account)
    {
        $accounts = collect([$account]);
        if ($account && $account->children->isNotEmpty()) {
            foreach ($account->children as $child) {
                $accounts = $accounts->merge($this->getAllChildAccounts($child));
            }
        }
        return $accounts;
    }

    private function aggregateTransactions($accounts, $field, $amountField, $startDate, $endDate)
    {
        $accountIds = $accounts->pluck('id')->toArray();

        return AcTransaction::whereIn($field, $accountIds)
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->groupBy($field)
            ->selectRaw("$field as account_id, SUM($amountField) as amount")
            ->get()
            ->map(function ($item) {
                return [
                    'account_name' => AcAccount::find($item->account_id)->account_name ?? 'Unknown',
                    'amount' => $item->amount
                ];
            })->toArray();
    }




}
