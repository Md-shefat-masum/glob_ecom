<?php

namespace App\Http\Controllers\Report\Actions;

use App\Models\ProductOrderReturn;
use Carbon\Carbon;

class SalesReturnsReportAction extends ReportAction
{
    public function run(array $filters): array
    {
        $query = ProductOrderReturn::query();

        if (!empty($filters['date_from'])) {
            $query->where('created_at', '>=', Carbon::parse($filters['date_from'])->startOfDay());
        }
        if (!empty($filters['date_to'])) {
            $query->where('created_at', '<=', Carbon::parse($filters['date_to'])->endOfDay());
        }

        $returns = $query->with(['order.customer'])->orderBy('created_at', 'desc')->get();

        $data = $returns->map(function($return) {
            return [
                'return_id' => $return->id,
                'order_code' => $return->order->order_code ?? '',
                'customer_name' => $return->order->customer->name ?? 'N/A',
                'date' => $return->created_at,
                'total' => $return->total ?? 0,
            ];
        })->toArray();

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
        return 'Sales Returns Report';
    }

    public function getCsvHeaders(): array
    {
        return ['Return ID', 'Order Code', 'Customer', 'Date', 'Total'];
    }

    public function formatForCsv(array $data): array
    {
        $rows = [];
        foreach ($data as $item) {
            $rows[] = [
                $item['return_id'] ?? '',
                $item['order_code'] ?? '',
                $item['customer_name'] ?? '',
                $item['date'] ?? '',
                $item['total'] ?? 0,
            ];
        }
        return $rows;
    }
}
