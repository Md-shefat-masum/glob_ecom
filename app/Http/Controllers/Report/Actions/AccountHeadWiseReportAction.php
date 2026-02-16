<?php

namespace App\Http\Controllers\Report\Actions;

use App\Models\AccountTransaction;
use App\Http\Controllers\Account\Models\AcAccount;
use Carbon\Carbon;

class AccountHeadWiseReportAction extends ReportAction
{
    public function run(array $filters): array
    {
        $dateFrom = !empty($filters['date_from']) ? Carbon::parse($filters['date_from'])->startOfDay() : null;
        $dateTo = !empty($filters['date_to']) ? Carbon::parse($filters['date_to'])->endOfDay() : null;

        $accounts = AcAccount::where('status', 'active');
        if (!empty($filters['account_id'])) {
            $accounts->where('id', $filters['account_id']);
        }
        $accounts = $accounts->get();

        $data = [];
        foreach ($accounts as $account) {
            $debitQuery = AccountTransaction::where('debit_account_id', $account->id);
            $creditQuery = AccountTransaction::where('credit_account_id', $account->id);

            if ($dateFrom) {
                $debitQuery->where('created_at', '>=', $dateFrom);
                $creditQuery->where('created_at', '>=', $dateFrom);
            }
            if ($dateTo) {
                $debitQuery->where('created_at', '<=', $dateTo);
                $creditQuery->where('created_at', '<=', $dateTo);
            }

            $totalDebit = $debitQuery->sum('debit_amt');
            $totalCredit = $creditQuery->sum('credit_amt');
            $balance = $totalDebit - $totalCredit;

            $data[] = [
                'account_id' => $account->id,
                'account_name' => $account->account_name,
                'account_code' => $account->account_code ?? '',
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'balance' => $balance,
            ];
        }

        $summary = [
            'total_accounts' => count($data),
            'total_debit' => array_sum(array_column($data, 'total_debit')),
            'total_credit' => array_sum(array_column($data, 'total_credit')),
        ];

        return [
            'data' => $data,
            'summary' => $summary,
        ];
    }

    public function getTitle(): string
    {
        return 'Account Head Wise Report';
    }

    public function getCsvHeaders(): array
    {
        return ['Account', 'Account Code', 'Total Debit', 'Total Credit', 'Balance'];
    }

    public function formatForCsv(array $data): array
    {
        $rows = [];
        foreach ($data as $item) {
            $rows[] = [
                $item['account_name'] ?? '',
                $item['account_code'] ?? '',
                $item['total_debit'] ?? 0,
                $item['total_credit'] ?? 0,
                $item['balance'] ?? 0,
            ];
        }
        return $rows;
    }

    public function getFiltersConfig(): array
    {
        return array_merge(parent::getFiltersConfig(), [
            [
                'type' => 'select',
                'name' => 'account_id',
                'label' => 'Account',
                'required' => false,
            ],
        ]);
    }
}
