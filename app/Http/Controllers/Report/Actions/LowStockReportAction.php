<?php

namespace App\Http\Controllers\Report\Actions;

use App\Models\Product;

class LowStockReportAction extends ReportAction
{
    public function run(array $filters): array
    {
        $threshold = (int) ($filters['threshold'] ?? 10);

        $query = Product::where('status', 'active')
            ->where('stock', '>', 0)
            ->where('stock', '<', $threshold);

        if (!empty($filters['warehouse_id'])) {
            // Filter by warehouse if ProductStock is used
        }

        $products = $query->get();

        $data = [];
        foreach ($products as $product) {
            $data[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'sku' => $product->sku ?? '',
                'stock' => $product->stock ?? 0,
                'threshold' => $threshold,
            ];
        }

        $summary = [
            'total_products' => count($data),
            'threshold' => $threshold,
        ];

        return [
            'data' => $data,
            'summary' => $summary,
        ];
    }

    public function getTitle(): string
    {
        return 'Low Stock Report';
    }

    public function getCsvHeaders(): array
    {
        return ['Product', 'SKU', 'Stock', 'Threshold'];
    }

    public function formatForCsv(array $data): array
    {
        $rows = [];
        foreach ($data as $item) {
            $rows[] = [
                $item['product_name'] ?? '',
                $item['sku'] ?? '',
                $item['stock'] ?? 0,
                $item['threshold'] ?? 10,
            ];
        }
        return $rows;
    }

    public function getFiltersConfig(): array
    {
        return [
            [
                'type' => 'number',
                'name' => 'threshold',
                'label' => 'Threshold',
                'required' => false,
                'default' => 10,
            ],
            [
                'type' => 'select',
                'name' => 'warehouse_id',
                'label' => 'Warehouse',
                'required' => false,
            ],
        ];
    }
}
