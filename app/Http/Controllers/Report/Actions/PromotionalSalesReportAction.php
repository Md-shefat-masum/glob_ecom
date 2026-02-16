<?php

namespace App\Http\Controllers\Report\Actions;

use App\Models\ProductOrder;
use Carbon\Carbon;

class PromotionalSalesReportAction extends ReportAction
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
        if (!empty($filters['coupon_code'])) {
            // If coupon_code column exists
            // $query->where('coupon_code', $filters['coupon_code']);
            // Or check request_data JSON for coupon
        }

        // Filter orders that used coupon (check calculated_discount_amount or request_data)
        $orders = $query->where('calculated_discount_amount', '>', 0)->get();

        $data = [];
        foreach ($orders as $order) {
            $data[] = [
                'order_code' => $order->order_code,
                'sale_date' => $order->sale_date,
                'total' => $order->total,
                'discount_amount' => $order->calculated_discount_amount ?? 0,
                'coupon_code' => 'N/A', // If column exists, use it
            ];
        }

        $summary = [
            'total_orders' => count($data),
            'total_sales' => array_sum(array_column($data, 'total')),
            'total_discount' => array_sum(array_column($data, 'discount_amount')),
        ];

        return [
            'data' => $data,
            'summary' => $summary,
        ];
    }

    public function getTitle(): string
    {
        return 'Promotional Sales Report';
    }

    public function getCsvHeaders(): array
    {
        return ['Order Code', 'Date', 'Total', 'Discount', 'Coupon Code'];
    }

    public function formatForCsv(array $data): array
    {
        $rows = [];
        foreach ($data as $item) {
            $rows[] = [
                $item['order_code'] ?? '',
                $item['sale_date'] ?? '',
                $item['total'] ?? 0,
                $item['discount_amount'] ?? 0,
                $item['coupon_code'] ?? '',
            ];
        }
        return $rows;
    }

    public function getFiltersConfig(): array
    {
        return array_merge(parent::getFiltersConfig(), [
            [
                'type' => 'text',
                'name' => 'coupon_code',
                'label' => 'Coupon Code',
                'required' => false,
            ],
        ]);
    }
}
