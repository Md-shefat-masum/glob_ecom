<?php

namespace App\Http\Controllers\Report\Actions;

use App\Models\ProductOrder;
use App\Models\User;
use Carbon\Carbon;

class SalesmanSalesReportAction extends ReportAction
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
        if (!empty($filters['order_source'])) {
            $query->where('order_source', $filters['order_source']);
        }

        $orders = $query->with('creator')->get();

        // Group by creator (salesman)
        $salesmen = [];
        foreach ($orders as $order) {
            $creatorId = $order->creator_id ?? $order->creator ?? 0;
            if (!isset($salesmen[$creatorId])) {
                $user = User::find($creatorId);
                $salesmen[$creatorId] = [
                    'user_id' => $creatorId,
                    'salesman_name' => $user ? $user->name : 'Unknown',
                    'order_count' => 0,
                    'subtotal' => 0,
                    'total' => 0,
                    'paid' => 0,
                    'due' => 0,
                ];
            }
            $salesmen[$creatorId]['order_count']++;
            $salesmen[$creatorId]['subtotal'] += $order->subtotal ?? 0;
            $salesmen[$creatorId]['total'] += $order->total;
            $salesmen[$creatorId]['paid'] += $order->paid_amount;
            $salesmen[$creatorId]['due'] += $order->due_amount;
        }

        $data = array_values($salesmen);
        usort($data, fn($a, $b) => $b['total'] <=> $a['total']);

        $summary = [
            'total_salesmen' => count($data),
            'total_sales' => array_sum(array_column($data, 'total')),
        ];

        return [
            'data' => $data,
            'summary' => $summary,
        ];
    }

    public function getTitle(): string
    {
        return 'Salesman Sales Report';
    }

    public function getCsvHeaders(): array
    {
        return ['Salesman', 'Order Count', 'Subtotal', 'Total', 'Paid', 'Due'];
    }

    public function formatForCsv(array $data): array
    {
        $rows = [];
        foreach ($data as $item) {
            $rows[] = [
                $item['salesman_name'] ?? '',
                $item['order_count'] ?? 0,
                $item['subtotal'] ?? 0,
                $item['total'] ?? 0,
                $item['paid'] ?? 0,
                $item['due'] ?? 0,
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
            ],
            [
                'type' => 'select',
                'name' => 'order_source',
                'label' => 'Order Source',
                'required' => false,
                'options' => [
                    ['value' => 'pos', 'label' => 'POS'],
                    ['value' => 'ecommerce', 'label' => 'Ecommerce'],
                    ['value' => 'website', 'label' => 'Website'],
                ],
            ],
        ]);
    }
}
