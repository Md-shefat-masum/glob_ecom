<?php

namespace App\Http\Controllers\Report\Actions;

use App\Http\Controllers\Inventory\Models\ProductStock;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class InStockReportAction extends ReportAction
{
    public function run(array $filters): array
    {
        $query = Product::where('status', 'active');

        if (!empty($filters['warehouse_id'])) {
            $query->whereHas('stocks', function($q) use ($filters) {
                $q->where('product_warehouse_id', $filters['warehouse_id']);
            });
        }
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }
        if (!empty($filters['product_id'])) {
            $query->where('id', $filters['product_id']);
        }

        $products = $query->with(['stocks.warehouse'])->get();

        $data = [];
        foreach ($products as $product) {
            $stockQty = $product->stock ?? 0;
            if ($stockQty > 0) {
                $data[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'sku' => $product->sku ?? '',
                    'stock' => $stockQty,
                    'warehouse' => $product->stocks->first()->warehouse->title ?? '',
                ];
            }
        }

        $summary = [
            'total_products' => count($data),
            'total_stock_value' => array_sum(array_column($data, 'stock')),
        ];

        return [
            'data' => $data,
            'summary' => $summary,
        ];
    }

    public function getTitle(): string
    {
        return 'In Stock Report';
    }

    public function getCsvHeaders(): array
    {
        return ['Product', 'SKU', 'Stock', 'Warehouse'];
    }

    public function formatForCsv(array $data): array
    {
        $rows = [];
        foreach ($data as $item) {
            $rows[] = [
                $item['product_name'] ?? '',
                $item['sku'] ?? '',
                $item['stock'] ?? 0,
                $item['warehouse'] ?? '',
            ];
        }
        return $rows;
    }

    public function getFiltersConfig(): array
    {
        return [
            [
                'type' => 'select',
                'name' => 'warehouse_id',
                'label' => 'Warehouse',
                'required' => false,
            ],
            [
                'type' => 'select',
                'name' => 'category_id',
                'label' => 'Category',
                'required' => false,
            ],
            [
                'type' => 'select',
                'name' => 'product_id',
                'label' => 'Product',
                'required' => false,
            ],
        ];
    }
}
