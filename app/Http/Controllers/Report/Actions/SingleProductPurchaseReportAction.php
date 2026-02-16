<?php

namespace App\Http\Controllers\Report\Actions;

use App\Http\Controllers\Inventory\Models\ProductPurchaseOrderProduct;
use App\Http\Controllers\Inventory\Models\ProductPurchaseOrder;
use Carbon\Carbon;

class SingleProductPurchaseReportAction extends ReportAction
{
    public function run(array $filters): array
    {
        if (empty($filters['product_id'])) {
            return ['data' => [], 'summary' => []];
        }

        $query = ProductPurchaseOrderProduct::where('product_id', $filters['product_id'])
            ->join('product_purchase_orders', 'product_purchase_order_products.product_purchase_order_id', '=', 'product_purchase_orders.id');

        if (!empty($filters['date_from'])) {
            $query->where('product_purchase_orders.date', '>=', Carbon::parse($filters['date_from'])->startOfDay());
        }
        if (!empty($filters['date_to'])) {
            $query->where('product_purchase_orders.date', '<=', Carbon::parse($filters['date_to'])->endOfDay());
        }

        $items = $query->select('product_purchase_orders.order_code', 'product_purchase_orders.date', 'product_purchase_order_products.qty', 'product_purchase_order_products.purchase_price')
            ->orderBy('product_purchase_orders.date', 'desc')
            ->get();

        $data = $items->map(function($item) {
            return [
                'order_code' => $item->order_code,
                'date' => $item->date,
                'qty' => $item->qty,
                'value' => $item->qty * ($item->purchase_price ?? 0),
            ];
        })->toArray();

        $summary = [
            'total_qty' => array_sum(array_column($data, 'qty')),
            'total_value' => array_sum(array_column($data, 'value')),
        ];

        return [
            'data' => $data,
            'summary' => $summary,
        ];
    }

    public function getTitle(): string
    {
        return 'Single Product Purchase Report';
    }

    public function getCsvHeaders(): array
    {
        return ['Order Code', 'Date', 'Quantity', 'Value'];
    }

    public function formatForCsv(array $data): array
    {
        $rows = [];
        foreach ($data as $item) {
            $rows[] = [
                $item['order_code'] ?? '',
                $item['date'] ?? '',
                $item['qty'] ?? 0,
                $item['value'] ?? 0,
            ];
        }
        return $rows;
    }

    public function getFiltersConfig(): array
    {
        return array_merge(parent::getFiltersConfig(), [
            [
                'type' => 'select',
                'name' => 'product_id',
                'label' => 'Product',
                'required' => true,
            ],
        ]);
    }
}
