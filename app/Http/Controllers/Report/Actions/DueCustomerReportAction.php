<?php

namespace App\Http\Controllers\Report\Actions;

use App\Models\ProductOrder;
use Illuminate\Support\Facades\DB;

class DueCustomerReportAction extends ReportAction
{
    public function run(array $filters): array
    {
        $query = ProductOrder::where('due_amount', '>', 0)
            ->where('status', 'active')
            ->with('customer');

        if (!empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        $orders = $query->orderBy('sale_date', 'asc')->get();

        // Group by customer
        $customers = [];
        foreach ($orders as $order) {
            $customerId = $order->customer_id ?? 0;
            if (!isset($customers[$customerId])) {
                $customers[$customerId] = [
                    'customer_id' => $customerId,
                    'customer_name' => $order->customer ? $order->customer->name : 'Walk-in',
                    'total_due' => 0,
                    'orders' => [],
                ];
            }
            $customers[$customerId]['total_due'] += $order->due_amount;
            $customers[$customerId]['orders'][] = [
                'order_code' => $order->order_code,
                'sale_date' => $order->sale_date,
                'due_amount' => $order->due_amount,
            ];
        }

        $data = array_values($customers);

        $summary = [
            'total_customers' => count($data),
            'total_due' => array_sum(array_column($data, 'total_due')),
        ];

        return [
            'data' => $data,
            'summary' => $summary,
        ];
    }

    public function getTitle(): string
    {
        return 'Due Customer Report';
    }

    public function getCsvHeaders(): array
    {
        return ['Customer', 'Total Due', 'Order Count'];
    }

    public function formatForCsv(array $data): array
    {
        $rows = [];
        foreach ($data as $item) {
            $rows[] = [
                $item['customer_name'] ?? '',
                $item['total_due'] ?? 0,
                count($item['orders'] ?? []),
            ];
        }
        return $rows;
    }

    public function getFiltersConfig(): array
    {
        return [
            [
                'type' => 'select',
                'name' => 'customer_id',
                'label' => 'Customer',
                'required' => false,
            ],
        ];
    }
}
