<?php

namespace App\Http\Controllers\Report\Actions;

use App\Models\ProductOrderProduct;
use App\Models\ProductOrder;
use App\Http\Controllers\Inventory\Models\ProductPurchaseOrderProduct;
use App\Http\Controllers\Inventory\Models\ProductPurchaseOrder;
use Carbon\Carbon;

class SingleProductSaleReportAction extends ReportAction
{
    public function run(array $filters): array
    {
        if (empty($filters['product_id'])) {
            return [
                'sales_data' => [],
                'purchase_data' => [],
                'summary' => []
            ];
        }

        $productId = $filters['product_id'];
        $dateFrom = !empty($filters['date_from']) ? Carbon::parse($filters['date_from'])->startOfDay() : null;
        $dateTo = !empty($filters['date_to']) ? Carbon::parse($filters['date_to'])->endOfDay() : null;

        // Sales data
        $salesQuery = ProductOrderProduct::where('product_id', $productId)
            ->join('product_orders', 'product_order_products.product_order_id', '=', 'product_orders.id');

        if ($dateFrom) {
            $salesQuery->where('product_orders.sale_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $salesQuery->where('product_orders.sale_date', '<=', $dateTo);
        }

        $salesItems = $salesQuery->select(
                'product_orders.sale_date as date',
                'product_order_products.qty',
                'product_order_products.sale_price',
                'product_order_products.discount_amount',
                'product_order_products.purchase_price'
            )
            ->orderBy('product_orders.sale_date', 'desc')
            ->get();

        $salesData = $salesItems->map(function($item) {
            $total = ($item->qty * $item->sale_price) - ($item->discount_amount ?? 0);
            return [
                'date' => $item->date,
                'qty' => $item->qty,
                'total' => $total,
            ];
        })->toArray();

        // Purchase data
        $purchaseQuery = ProductPurchaseOrderProduct::where('product_id', $productId)
            ->join('product_purchase_orders', 'product_purchase_order_products.product_purchase_order_id', '=', 'product_purchase_orders.id');

        if ($dateFrom) {
            $purchaseQuery->where('product_purchase_orders.date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $purchaseQuery->where('product_purchase_orders.date', '<=', $dateTo);
        }

        $purchaseItems = $purchaseQuery->select(
                'product_purchase_orders.date',
                'product_purchase_order_products.qty',
                'product_purchase_order_products.purchase_price'
            )
            ->orderBy('product_purchase_orders.date', 'desc')
            ->get();

        $purchaseData = $purchaseItems->map(function($item) {
            return [
                'date' => $item->date,
                'qty' => $item->qty,
                'total' => $item->qty * ($item->purchase_price ?? 0),
            ];
        })->toArray();

        // Calculate summary metrics
        $totalSold = array_sum(array_column($salesData, 'total'));
        $totalPurchased = array_sum(array_column($purchaseData, 'total'));
        $soldQty = array_sum(array_column($salesData, 'qty'));
        $purchaseValue = array_sum(array_column($purchaseData, 'total'));
        $totalDiscounts = $salesItems->sum('discount_amount') ?? 0;
        $profitLoss = $totalSold - $purchaseValue;

        $summary = [
            'total_sold' => $totalSold,
            'total_purchased' => $totalPurchased,
            'sold_qty' => $soldQty,
            'purchase_value' => $purchaseValue,
            'profit_loss' => $profitLoss,
            'total_discounts' => $totalDiscounts,
            'is_profit' => $profitLoss >= 0,
        ];

        return [
            'sales_data' => $salesData,
            'purchase_data' => $purchaseData,
            'summary' => $summary,
        ];
    }

    public function getTitle(): string
    {
        return 'Single Product Sale Report';
    }

    public function getCsvHeaders(): array
    {
        return ['Type', 'Date', 'Quantity', 'Total'];
    }

    public function formatForCsv(array $data): array
    {
        $rows = [];
        
        // Add sales data
        foreach (($data['sales_data'] ?? []) as $item) {
            $rows[] = [
                'Sale',
                $item['date'] ?? '',
                $item['qty'] ?? 0,
                $item['total'] ?? 0,
            ];
        }
        
        // Add purchase data
        foreach (($data['purchase_data'] ?? []) as $item) {
            $rows[] = [
                'Purchase',
                $item['date'] ?? '',
                $item['qty'] ?? 0,
                $item['total'] ?? 0,
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
