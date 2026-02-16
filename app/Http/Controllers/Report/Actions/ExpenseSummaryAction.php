<?php

namespace App\Http\Controllers\Report\Actions;

use App\Http\Controllers\Account\Models\DbExpense;
use Carbon\Carbon;

class ExpenseSummaryAction extends ReportAction
{
    public function run(array $filters): array
    {
        $query = DbExpense::query();

        if (!empty($filters['date_from'])) {
            $query->where('expense_date', '>=', Carbon::parse($filters['date_from'])->startOfDay());
        }
        if (!empty($filters['date_to'])) {
            $query->where('expense_date', '<=', Carbon::parse($filters['date_to'])->endOfDay());
        }

        $expenses = $query->with('expense_category')->get();

        // Group by category
        $categories = [];
        foreach ($expenses as $expense) {
            $categoryId = $expense->category_id ?? 0;
            $categoryName = $expense->expense_category ? $expense->expense_category->category_name : 'Uncategorized';
            
            if (!isset($categories[$categoryId])) {
                $categories[$categoryId] = [
                    'category_name' => $categoryName,
                    'total_amount' => 0,
                    'count' => 0,
                ];
            }
            $categories[$categoryId]['total_amount'] += $expense->expense_amt ?? 0;
            $categories[$categoryId]['count']++;
        }

        $data = array_values($categories);

        $summary = [
            'total_categories' => count($data),
            'total_expenses' => array_sum(array_column($data, 'total_amount')),
        ];

        return [
            'data' => $data,
            'summary' => $summary,
        ];
    }

    public function getTitle(): string
    {
        return 'Expense Summary Report';
    }

    public function getCsvHeaders(): array
    {
        return ['Category', 'Count', 'Total Amount'];
    }

    public function formatForCsv(array $data): array
    {
        $rows = [];
        foreach ($data as $item) {
            $rows[] = [
                $item['category_name'] ?? '',
                $item['count'] ?? 0,
                $item['total_amount'] ?? 0,
            ];
        }
        return $rows;
    }
}
