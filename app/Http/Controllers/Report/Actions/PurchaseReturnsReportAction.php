<?php

namespace App\Http\Controllers\Report\Actions;

use App\Models\ProductPurchaseReturn;
use Carbon\Carbon;

class PurchaseReturnsReportAction extends ReportAction
{
    public function run(array $filters): array
    {
        $query = ProductPurchaseReturn::query();

        if (!empty($filters['date_from'])) {
            $query->where('date', '>=', Carbon::parse($filters['date_from'])->startOfDay());
        }
        if (!empty($filters['date_to'])) {
            $query->where('date', '<=', Carbon::parse($filters['date_to'])->endOfDay());
        }

        $returns = $query->with(['supplier', 'warehouse'])->orderBy('date', 'desc')->get();

        $data = $returns->toArray();

        $summary = [
            'total_returns' => count($data),
            'total_amount' => array_sum(array_column($data, 'total')),
        ];

        return [
            'data' => $data,
            'summary' => $summary,
        ];
    }

    public function getTitle(): string
    {
        return 'Purchase Returns Report';
    }

    public function getCsvHeaders(): array
    {
        return ['Return Code', 'Date', 'Supplier', 'Total'];
    }

    public function formatForCsv(array $data): array
    {
        $rows = [];
        foreach ($data as $item) {
            $rows[] = [
                $item['order_code'] ?? '',
                $item['date'] ?? '',
                $item['supplier']['name'] ?? '',
                $item['total'] ?? 0,
            ];
        }
        return $rows;
    }
}
