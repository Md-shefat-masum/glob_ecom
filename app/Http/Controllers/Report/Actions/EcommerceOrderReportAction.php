<?php

namespace App\Http\Controllers\Report\Actions;

use App\Models\ProductOrder;
use App\Models\ProductOrderReturn;
use Carbon\Carbon;

class EcommerceOrderReportAction extends ReportAction
{
    public function run(array $filters): array
    {
        $query = ProductOrder::whereIn('order_source', ['ecommerce', 'website']);

        if (!empty($filters['date_from'])) {
            $query->where('sale_date', '>=', Carbon::parse($filters['date_from'])->startOfDay());
        }
        if (!empty($filters['date_to'])) {
            $query->where('sale_date', '<=', Carbon::parse($filters['date_to'])->endOfDay());
        }

        $orders = $query->get();

        $pending = $orders->where('order_status', 'pending');
        $completed = $orders->whereIn('order_status', ['invoiced', 'delivered']);
        $returned = ProductOrderReturn::whereIn('product_order_id', $orders->pluck('id'))->get();

        $summary = [
            'pending_count' => $pending->count(),
            'pending_amount' => $pending->sum('total'),
            'completed_count' => $completed->count(),
            'completed_amount' => $completed->sum('total'),
            'returned_count' => $returned->count(),
            'returned_amount' => $returned->sum('total'),
            'total_revenue' => $completed->sum('total'),
        ];

        $data = $orders->toArray();

        return [
            'data' => $data,
            'summary' => $summary,
        ];
    }

    public function getTitle(): string
    {
        return 'Ecommerce Order Report';
    }

    public function getCsvHeaders(): array
    {
        return ['Order Code', 'Date', 'Status', 'Total'];
    }

    public function formatForCsv(array $data): array
    {
        $rows = [];
        foreach ($data as $item) {
            $rows[] = [
                $item['order_code'] ?? '',
                $item['sale_date'] ?? '',
                $item['order_status'] ?? '',
                $item['total'] ?? 0,
            ];
        }
        return $rows;
    }
}
