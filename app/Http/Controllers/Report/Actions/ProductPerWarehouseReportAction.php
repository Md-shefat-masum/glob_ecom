<?php

namespace App\Http\Controllers\Report\Actions;

use App\Http\Controllers\Inventory\Models\ProductStock;
use App\Models\Product;
use App\Http\Controllers\Inventory\Models\ProductWarehouse;

class ProductPerWarehouseReportAction extends ReportAction
{
    public function run(array $filters): array
    {
        $warehouses = ProductWarehouse::where('status', 'active')->get();
        $data = [];

        foreach ($warehouses as $warehouse) {
            $products = Product::where('status', 'active')
                ->whereHas('stocks', function($q) use ($warehouse) {
                    $q->where('product_warehouse_id', $warehouse->id);
                })
                ->with(['stocks' => function($q) use ($warehouse) {
                    $q->where('product_warehouse_id', $warehouse->id);
                }])
                ->get();

            foreach ($products as $product) {
                $stock = $product->stocks->first();
                $data[] = [
                    'warehouse' => $warehouse->title,
                    'product_name' => $product->name,
                    'sku' => $product->sku ?? '',
                    'stock' => $stock ? $stock->qty : 0,
                ];
            }
        }

        $summary = [
            'total_warehouses' => $warehouses->count(),
            'total_products' => count($data),
        ];

        return [
            'data' => $data,
            'summary' => $summary,
        ];
    }

    public function getTitle(): string
    {
        return 'Product Per Warehouse Report';
    }

    public function getCsvHeaders(): array
    {
        return ['Warehouse', 'Product', 'SKU', 'Stock'];
    }

    public function formatForCsv(array $data): array
    {
        $rows = [];
        foreach ($data as $item) {
            $rows[] = [
                $item['warehouse'] ?? '',
                $item['product_name'] ?? '',
                $item['sku'] ?? '',
                $item['stock'] ?? 0,
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
        ];
    }
}
