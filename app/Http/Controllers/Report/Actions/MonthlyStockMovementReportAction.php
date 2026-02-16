<?php

namespace App\Http\Controllers\Report\Actions;

use App\Models\Product;
use App\Models\ProductOrderProduct;
use App\Http\Controllers\Inventory\Models\ProductPurchaseOrderProduct;
use Carbon\Carbon;

class MonthlyStockMovementReportAction extends ReportAction
{
    public function run(array $filters): array
    {
        $month = $filters['month'] ?? date('m');
        $year = $filters['year'] ?? date('Y');
        $startDate = Carbon::create($year, $month, 1)->startOfDay();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();

        $products = Product::where('status', 'active')->get();
        $data = [];

        foreach ($products as $product) {
            // Previous stock (before start of month)
            $previousStock = $product->stock ?? 0; // Simplified - should calculate from stock logs

            // Stock in (purchases in month)
            $stockIn = ProductPurchaseOrderProduct::where('product_id', $product->id)
                ->join('product_purchase_orders', 'product_purchase_order_products.product_purchase_order_id', '=', 'product_purchase_orders.id')
                ->whereBetween('product_purchase_orders.date', [$startDate, $endDate])
                ->sum('product_purchase_order_products.qty');

            // Stock out (sales in month)
            $stockOut = ProductOrderProduct::where('product_id', $product->id)
                ->join('product_orders', 'product_order_products.product_order_id', '=', 'product_orders.id')
                ->whereBetween('product_orders.sale_date', [$startDate, $endDate])
                ->sum('product_order_products.qty');

            // Current stock
            $currentStock = $previousStock + $stockIn - $stockOut;

            $data[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'previous_stock' => $previousStock,
                'stock_in' => $stockIn,
                'sold' => $stockOut,
                'waste' => 0, // If tracked
                'current_stock' => $currentStock,
            ];
        }

        $summary = [
            'month' => $month,
            'year' => $year,
            'total_products' => count($data),
        ];

        return [
            'data' => $data,
            'summary' => $summary,
        ];
    }

    public function getTitle(): string
    {
        return 'Monthly Stock Movement Report';
    }

    public function getCsvHeaders(): array
    {
        return ['Product', 'Previous Stock', 'Stock In', 'Sold', 'Waste', 'Current Stock'];
    }

    public function formatForCsv(array $data): array
    {
        $rows = [];
        foreach ($data as $item) {
            $rows[] = [
                $item['product_name'] ?? '',
                $item['previous_stock'] ?? 0,
                $item['stock_in'] ?? 0,
                $item['sold'] ?? 0,
                $item['waste'] ?? 0,
                $item['current_stock'] ?? 0,
            ];
        }
        return $rows;
    }

    public function getFiltersConfig(): array
    {
        return [
            [
                'type' => 'select',
                'name' => 'month',
                'label' => 'Month',
                'required' => true,
                'options' => [
                    ['value' => '01', 'label' => 'January'],
                    ['value' => '02', 'label' => 'February'],
                    ['value' => '03', 'label' => 'March'],
                    ['value' => '04', 'label' => 'April'],
                    ['value' => '05', 'label' => 'May'],
                    ['value' => '06', 'label' => 'June'],
                    ['value' => '07', 'label' => 'July'],
                    ['value' => '08', 'label' => 'August'],
                    ['value' => '09', 'label' => 'September'],
                    ['value' => '10', 'label' => 'October'],
                    ['value' => '11', 'label' => 'November'],
                    ['value' => '12', 'label' => 'December'],
                ],
            ],
            [
                'type' => 'number',
                'name' => 'year',
                'label' => 'Year',
                'required' => true,
                'default' => date('Y'),
            ],
        ];
    }
}
