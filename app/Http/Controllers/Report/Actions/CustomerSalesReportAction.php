<?php

namespace App\Http\Controllers\Report\Actions;

use App\Models\ProductOrder;
use Carbon\Carbon;

class CustomerSalesReportAction extends ReportAction
{
    public function run(array $filters): array
    {
        $query = ProductOrder::query();

        if (!empty($filters['date_from'])) {
            $query->where('sale_date', '>=', Carbon::parse($filters['date_from'])->startOfDay());
        }
        if (!empty($filters['date_to'])) {
            $query->where('sale_date', '<=', Carbon::parse($filters['date_to'])->endOfDay());
        }
        if (!empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        $orders = $query->with('customer')->get();

        // Group by customer
        $customers = [];
        foreach ($orders as $order) {
            $customerId = $order->customer_id ?? 0;
            if (!isset($customers[$customerId])) {
                $customers[$customerId] = [
                    'customer_id' => $customerId,
                    'customer_name' => $order->customer ? $order->customer->name : 'Walk-in',
                    'total_sales' => 0,
                    'total_paid' => 0,
                    'total_due' => 0,
                    'order_count' => 0,
                ];
            }
            $customers[$customerId]['total_sales'] += $order->total;
            $customers[$customerId]['total_paid'] += $order->paid_amount;
            $customers[$customerId]['total_due'] += $order->due_amount;
            $customers[$customerId]['order_count']++;
        }

        $data = array_values($customers);

        $summary = [
            'total_customers' => count($data),
            'total_sales' => array_sum(array_column($data, 'total_sales')),
        ];

        return [
            'data' => $data,
            'summary' => $summary,
        ];
    }

    public function getTitle(): string
    {
        return 'Customer Sales Report';
    }

    public function getCsvHeaders(): array
    {
        return ['Customer', 'Total Sales', 'Total Paid', 'Total Due', 'Order Count'];
    }

    public function formatForCsv(array $data): array
    {
        $rows = [];
        foreach ($data as $item) {
            $rows[] = [
                $item['customer_name'] ?? '',
                $item['total_sales'] ?? 0,
                $item['total_paid'] ?? 0,
                $item['total_due'] ?? 0,
                $item['order_count'] ?? 0,
            ];
        }
        return $rows;
    }

    public function getFiltersConfig(): array
    {
        return array_merge(parent::getFiltersConfig(), [
            [
                'type' => 'select',
                'name' => 'customer_id',
                'label' => 'Customer',
                'required' => false,
            ],
        ]);
    }
}
