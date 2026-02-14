<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Account\Models\AcAccount;
use App\Http\Controllers\Account\Models\AcTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LedgerController extends Controller
{
    /**
     * Ledger index: account-wise transactions or all accounts overview.
     */
    public function index(Request $request)
    {
        $request->validate([
            'account_id' => 'nullable|exists:ac_accounts,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date ?? now()->subDays(30)->format('Y-m-d');
        $endDate = $request->end_date ?? now()->format('Y-m-d');
        $accounts = AcAccount::all();
        $account = null;
        $transactions = collect();
        $allTransactions = [];

        if ($request->account_id) {
            $account = AcAccount::findOrFail($request->account_id);
            $transactions = $this->getTransactionsForAccount($account->id, $startDate, $endDate);
        } else {
            foreach ($accounts as $acc) {
                $accTransactions = $this->getTransactionsForAccount($acc->id, $startDate, $endDate);
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

    /**
     * Journal: structured by date and transaction_type with opening balance.
     * Payload: from, to, page. Response: [ opening_balance, ...days with grouped transactions ].
     */
    public function journal(Request $request)
    {
        $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
            'page' => 'nullable|integer|min:1',
        ]);

        $from = $request->input('from', now()->subDays(30)->format('Y-m-d'));
        $to = $request->input('to', now()->format('Y-m-d'));
        $page = (int) $request->input('page', 1);
        $perPage = 100;

        $journalData = [];

        // 1. Opening balance: dr/cr before from date + dr/cr from all previous pages in [from, to]
        $openingDr = AcTransaction::where('transaction_date', '<', $from)->sum('debit_amt');
        $openingCr = AcTransaction::where('transaction_date', '<', $from)->sum('credit_amt');

        if ($page > 1) {
            $offset = ($page - 1) * $perPage;
            $prevIds = AcTransaction::whereBetween('transaction_date', [$from, $to])
                ->orderBy('transaction_date')
                ->orderBy('id')
                ->limit($offset)
                ->pluck('id');
            if ($prevIds->isNotEmpty()) {
                $openingDr += AcTransaction::whereIn('id', $prevIds)->sum('debit_amt');
                $openingCr += AcTransaction::whereIn('id', $prevIds)->sum('credit_amt');
            }
        }

        $dayBeforeFrom = Carbon::parse($from)->subDay()->format('Y-m-d');
        $journalData[] = [
            'date' => $dayBeforeFrom,
            'dr' => (float) $openingDr,
            'cr' => (float) $openingCr,
        ];

        // 2. Transactions between from and to, limit 1000 (paginated by offset)
        $query = AcTransaction::whereBetween('transaction_date', [$from, $to])
            ->orderBy('transaction_date')
            ->orderBy('id')
            ->with(['debitAccount', 'creditAccount']);

        $totalInRange = $query->count();
        $transactions = $query->paginate($perPage)->appends(request()->all());

        // 3. Group by date -> payment_code -> transaction_type
        $byDate = $transactions->groupBy('transaction_date');

        foreach ($byDate as $date => $dayTransactions) {
            $byPaymentCode = $dayTransactions->groupBy('payment_code');
            $paymentCodeBlocks = [];

            foreach ($byPaymentCode as $paymentCode => $paymentCodeTransactions) {
                $byType = $paymentCodeTransactions->groupBy('transaction_type');
                $typeBlocks = [];

                foreach ($byType as $type => $typeTransactions) {
                    $rows = $typeTransactions->map(function ($t) {
                        $debitAmount = (float) ($t->debit_amt ?? 0);
                        $creditAmount = (float) ($t->credit_amt ?? 0);
                        $accountHead = $debitAmount > 0
                            ? ($t->debitAccount->account_name ?? '')
                            : ($t->creditAccount->account_name ?? '');

                        return [
                            'debit_amount' => $debitAmount,
                            'credit_amount' => $creditAmount,
                            'note' => $t->note ?? '',
                            'account_head' => $accountHead,
                            'debit_account' => $t->debitAccount->account_name ?? '',
                            'credit_account' => $t->creditAccount->account_name ?? '',
                        ];
                    })->values()->all();

                    $typeBlocks[] = [
                        'type' => $type,
                        'total_records' => count($rows),
                        'transactions' => $rows,
                    ];
                }

                $paymentCodeBlocks[] = [
                    'payment_code' => $paymentCode ?? '',
                    'transactions' => $typeBlocks,
                ];
            }

            $journalData[] = [
                'date' => $date,
                'total_records' => $dayTransactions->count(),
                'payment_codes' => $paymentCodeBlocks,
            ];
        }

        // Backward compatibility for view
        $totalDebit = $transactions->sum('debit_amt');
        $totalCredit = $transactions->sum('credit_amt');

        return view('backend.ledger.journal', [
            'journalData' => $journalData,
            'transactions' => $transactions,
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit,
            'startDate' => $from,
            'endDate' => $to,
            'from' => $from,
            'to' => $to,
            'page' => $page,
            'perPage' => $perPage,
            'totalInRange' => $totalInRange,
        ]);
    }

    /**
     * Balance sheet: Assets, Liabilities, Equity for date range.
     */
    public function balanceSheet(Request $request)
    {
        $startDate = $request->input('start_date', now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $accountCategories = ['Assets', 'Liabilities', 'Equity'];
        $accounts = AcAccount::whereIn('account_name', $accountCategories)
            ->where('parent_id', 0)
            ->with([
                'children',
                'debitTransactions' => fn ($q) => $q->whereBetween('transaction_date', [$startDate, $endDate]),
                'creditTransactions' => fn ($q) => $q->whereBetween('transaction_date', [$startDate, $endDate]),
                'children.debitTransactions' => fn ($q) => $q->whereBetween('transaction_date', [$startDate, $endDate]),
                'children.creditTransactions' => fn ($q) => $q->whereBetween('transaction_date', [$startDate, $endDate]),
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

    /**
     * Income statement: Revenue, Expense, Net Income for date range.
     */
    public function incomeStatement(Request $request)
    {
        $startDate = $request->input('start_date', now('Asia/Dhaka')->subDays(30)->toDateString());
        $endDate = $request->input('end_date', now('Asia/Dhaka')->toDateString());

        $incomeStatement = $this->calculateIncomeStatement($startDate, $endDate);

        return view('backend.ledger.income_statement', compact('incomeStatement', 'startDate', 'endDate'));
    }

    /**
     * Transactions for a single account in date range.
     */
    private function getTransactionsForAccount(int $accountId, string $startDate, string $endDate)
    {
        return AcTransaction::where(function ($query) use ($accountId) {
            $query->where('debit_account_id', $accountId)
                ->orWhere('credit_account_id', $accountId);
        })
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->orderBy('transaction_date')
            ->get();
    }

    private function calculateAccountBalanceSheet($account): float
    {
        $debits = $account->debitTransactions->sum('debit_amt');
        $credits = $account->creditTransactions->sum('credit_amt');

        $isAssets = $account->account_name === 'Assets';
        $balance = $isAssets ? ($debits - $credits) : ($credits - $debits);

        foreach ($account->children as $child) {
            $childDebits = $child->debitTransactions->sum('debit_amt');
            $childCredits = $child->creditTransactions->sum('credit_amt');
            $balance += $isAssets ? ($childDebits - $childCredits) : ($childCredits - $childDebits);
            $balance += $this->calculateAccountBalanceSheet($child);
        }

        return $balance;
    }

    private function calculateIncomeStatement(string $startDate, string $endDate): array
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

    private function aggregateTransactions($accounts, string $field, string $amountField, string $startDate, string $endDate): array
    {
        $accountIds = $accounts->pluck('id')->toArray();

        return AcTransaction::whereIn($field, $accountIds)
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->groupBy($field)
            ->selectRaw("{$field} as account_id, SUM({$amountField}) as amount")
            ->get()
            ->map(fn ($item) => [
                'account_name' => AcAccount::find($item->account_id)->account_name ?? 'Unknown',
                'amount' => $item->amount,
            ])
            ->toArray();
    }
}
