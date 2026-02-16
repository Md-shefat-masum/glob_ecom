<?php

namespace App\Http\Controllers\Report\Actions;

use App\Models\ProductOrder;
use Carbon\Carbon;

class SalesReportAction extends ReportAction
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
        if (!empty($filters['warehouse_id'])) {
            $query->where('product_warehouse_id', $filters['warehouse_id']);
        }
        if (!empty($filters['order_status'])) {
            $query->where('order_status', $filters['order_status']);
        }

        $orders = $query->with(['customer', 'warehouse'])->orderBy('sale_date', 'desc')->get();

        // Format data with specific columns
        $data = $orders->map(function($order) {
            return [
                'date' => $order->sale_date ?? '',
                'order_code' => $order->order_code ?? '',
                'customer_name' => $order->customer ? $order->customer->name : 'Walk-in',
                'phone' => $order->customer ? ($order->customer->phone ?? '') : '',
                'grand_total' => $order->total ?? 0,
                'paid' => $order->paid_amount ?? 0,
                'due' => $order->due_amount ?? 0,
            ];
        })->toArray();

        $summary = [
            'total_orders' => $orders->count(),
            'total_grand_total' => $orders->sum('total'),
            'total_paid' => $orders->sum('paid_amount'),
            'total_due' => $orders->sum('due_amount'),
        ];

        return [
            'data' => $data,
            'summary' => $summary,
        ];
    }

    public function getTitle(): string
    {
        return 'Sales Report';
    }

    public function getCsvHeaders(): array
    {
        return ['Date', 'Order Code', 'Customer Name', 'Phone', 'Grand Total', 'Paid', 'Due'];
    }

    public function formatForCsv(array $data): array
    {
        $rows = [];
        foreach ($data as $order) {
            $rows[] = [
                $order['date'] ?? '',
                $order['order_code'] ?? '',
                $order['customer_name'] ?? 'Walk-in',
                $order['phone'] ?? '',
                $order['grand_total'] ?? 0,
                $order['paid'] ?? 0,
                $order['due'] ?? 0,
            ];
        }
        return $rows;
    }

    public function getFiltersConfig(): array
    {
        return array_merge(parent::getFiltersConfig(), [
            [
                'type' => 'select',
                'name' => 'warehouse_id',
                'label' => 'Warehouse',
                'required' => false,
                'options' => 'warehouses', // Will be loaded via AJAX
            ],
            [
                'type' => 'select',
                'name' => 'order_status',
                'label' => 'Order Status',
                'required' => false,
                'options' => [
                    ['value' => 'pending', 'label' => 'Pending'],
                    ['value' => 'invoiced', 'label' => 'Invoiced'],
                    ['value' => 'delivered', 'label' => 'Delivered'],
                ],
            ],
        ]);
    }
}
