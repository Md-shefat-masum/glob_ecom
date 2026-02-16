<?php

namespace App\Http\Controllers\Report\Actions;

use App\Models\AccountTransaction;
use App\Http\Controllers\Account\Models\AcAccount;
use Carbon\Carbon;

class ProfitLossReportAction extends ReportAction
{
    public function run(array $filters): array
    {
        $dateFrom = !empty($filters['date_from']) ? Carbon::parse($filters['date_from'])->startOfDay() : null;
        $dateTo = !empty($filters['date_to']) ? Carbon::parse($filters['date_to'])->endOfDay() : null;

        // Get revenue accounts (income)
        $revenueAccounts = AcAccount::where('account_type', 'income')->pluck('id');
        $revenue = AccountTransaction::whereIn('credit_account_id', $revenueAccounts);
        if ($dateFrom) $revenue->where('created_at', '>=', $dateFrom);
        if ($dateTo) $revenue->where('created_at', '<=', $dateTo);
        $totalRevenue = $revenue->sum('credit_amt');

        // Get expense accounts
        $expenseAccounts = AcAccount::where('account_type', 'expense')->pluck('id');
        $expenses = AccountTransaction::whereIn('debit_account_id', $expenseAccounts);
        if ($dateFrom) $expenses->where('created_at', '>=', $dateFrom);
        if ($dateTo) $expenses->where('created_at', '<=', $dateTo);
        $totalExpenses = $expenses->sum('debit_amt');

        $profitLoss = $totalRevenue - $totalExpenses;

        $summary = [
            'total_revenue' => $totalRevenue,
            'total_expenses' => $totalExpenses,
            'profit_loss' => $profitLoss,
            'is_profit' => $profitLoss >= 0,
        ];

        return [
            'data' => [],
            'summary' => $summary,
        ];
    }

    public function getTitle(): string
    {
        return 'Profit Loss Report';
    }

    public function getCsvHeaders(): array
    {
        return ['Item', 'Amount'];
    }

    public function formatForCsv(array $data): array
    {
        return [];
    }
}
