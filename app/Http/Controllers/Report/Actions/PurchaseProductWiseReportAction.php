<?php

namespace App\Http\Controllers\Report\Actions;

use App\Http\Controllers\Inventory\Models\ProductPurchaseOrderProduct;
use App\Http\Controllers\Inventory\Models\ProductPurchaseOrder;
use Carbon\Carbon;

class PurchaseProductWiseReportAction extends ReportAction
{
    public function run(array $filters): array
    {
        $query = ProductPurchaseOrderProduct::query()
            ->join('product_purchase_orders', 'product_purchase_order_products.product_purchase_order_id', '=', 'product_purchase_orders.id')
            ->join('products', 'product_purchase_order_products.product_id', '=', 'products.id');

        if (!empty($filters['date_from'])) {
            $query->where('product_purchase_orders.date', '>=', Carbon::parse($filters['date_from'])->startOfDay());
        }
        if (!empty($filters['date_to'])) {
            $query->where('product_purchase_orders.date', '<=', Carbon::parse($filters['date_to'])->endOfDay());
        }

        $items = $query->selectRaw('products.id, products.name, SUM(product_purchase_order_products.qty) as total_qty, SUM(product_purchase_order_products.purchase_price * product_purchase_order_products.qty) as total_amount')
            ->groupBy('products.id', 'products.name')
            ->get();

        $data = $items->toArray();

        $summary = [
            'total_products' => count($data),
            'total_qty' => array_sum(array_column($data, 'total_qty')),
            'total_amount' => array_sum(array_column($data, 'total_amount')),
        ];

        return [
            'data' => $data,
            'summary' => $summary,
        ];
    }

    public function getTitle(): string
    {
        return 'Purchase Product Wise Report';
    }

    public function getCsvHeaders(): array
    {
        return ['Product', 'Quantity Purchased', 'Amount'];
    }

    public function formatForCsv(array $data): array
    {
        $rows = [];
        foreach ($data as $item) {
            $rows[] = [
                $item['name'] ?? '',
                $item['total_qty'] ?? 0,
                $item['total_amount'] ?? 0,
            ];
        }
        return $rows;
    }
}
