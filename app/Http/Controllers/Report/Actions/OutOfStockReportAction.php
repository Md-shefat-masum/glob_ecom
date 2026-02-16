<?php

namespace App\Http\Controllers\Report\Actions;

use App\Models\Product;

class OutOfStockReportAction extends ReportAction
{
    public function run(array $filters): array
    {
        $query = Product::where('status', 'active')
            ->where(function($q) {
                $q->where('stock', '<=', 0)
                    ->orWhereNull('stock');
            });

        if (!empty($filters['warehouse_id'])) {
            // Filter by warehouse stock if ProductStock is used
        }

        $products = $query->get();

        $data = [];
        foreach ($products as $product) {
            $data[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'sku' => $product->sku ?? '',
                'stock' => $product->stock ?? 0,
            ];
        }

        $summary = [
            'total_products' => count($data),
        ];

        return [
            'data' => $data,
            'summary' => $summary,
        ];
    }

    public function getTitle(): string
    {
        return 'Out of Stock Report';
    }

    public function getCsvHeaders(): array
    {
        return ['Product', 'SKU', 'Stock'];
    }

    public function formatForCsv(array $data): array
    {
        $rows = [];
        foreach ($data as $item) {
            $rows[] = [
                $item['product_name'] ?? '',
                $item['sku'] ?? '',
                $item['stock'] ?? 0,
            ];
        }
        return $rows;
    }

    public function getFiltersConfig(): array
    {
        return [];
    }
}
