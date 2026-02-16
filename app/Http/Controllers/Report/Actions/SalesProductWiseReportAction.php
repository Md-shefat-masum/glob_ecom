<?php

namespace App\Http\Controllers\Report\Actions;

use App\Models\ProductOrderProduct;
use App\Models\ProductOrder;
use Carbon\Carbon;

class SalesProductWiseReportAction extends ReportAction
{
    public function run(array $filters): array
    {
        $query = ProductOrderProduct::query()
            ->join('product_orders', 'product_order_products.product_order_id', '=', 'product_orders.id')
            ->join('products', 'product_order_products.product_id', '=', 'products.id');

        if (!empty($filters['date_from'])) {
            $query->where('product_orders.sale_date', '>=', Carbon::parse($filters['date_from'])->startOfDay());
        }
        if (!empty($filters['date_to'])) {
            $query->where('product_orders.sale_date', '<=', Carbon::parse($filters['date_to'])->endOfDay());
        }

        $items = $query->selectRaw('products.id, products.name, SUM(product_order_products.qty) as total_qty, SUM(product_order_products.sale_price * product_order_products.qty) as total_revenue')
            ->groupBy('products.id', 'products.name')
            ->get();

        $data = $items->toArray();

        $summary = [
            'total_products' => count($data),
            'total_qty' => array_sum(array_column($data, 'total_qty')),
            'total_revenue' => array_sum(array_column($data, 'total_revenue')),
        ];

        return [
            'data' => $data,
            'summary' => $summary,
        ];
    }

    public function getTitle(): string
    {
        return 'Sales Product Wise Report';
    }

    public function getCsvHeaders(): array
    {
        return ['Product', 'Quantity Sold', 'Revenue'];
    }

    public function formatForCsv(array $data): array
    {
        $rows = [];
        foreach ($data as $item) {
            $rows[] = [
                $item['name'] ?? '',
                $item['total_qty'] ?? 0,
                $item['total_revenue'] ?? 0,
            ];
        }
        return $rows;
    }
}
