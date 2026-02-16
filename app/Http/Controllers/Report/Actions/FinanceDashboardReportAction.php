<?php

namespace App\Http\Controllers\Report\Actions;

use App\Models\ProductOrder;
use App\Http\Controllers\Inventory\Models\ProductPurchaseOrder;
use App\Models\ProductOrderProduct;
use App\Http\Controllers\Account\Models\AcAccount;
use Carbon\Carbon;

class FinanceDashboardReportAction extends ReportAction
{
    public function run(array $filters): array
    {
        $topWarehousesBySales = ProductOrder::selectRaw('product_warehouse_id, SUM(total) as total_sales')
            ->groupBy('product_warehouse_id')
            ->orderBy('total_sales', 'desc')
            ->limit(5)
            ->with('warehouse')
            ->get()
            ->map(function($item) {
                return [
                    'warehouse' => $item->warehouse->title ?? 'N/A',
                    'total_sales' => $item->total_sales,
                ];
            })
            ->toArray();

        $topWarehousesByPurchase = ProductPurchaseOrder::selectRaw('product_warehouse_id, SUM(total) as total_purchase')
            ->groupBy('product_warehouse_id')
            ->orderBy('total_purchase', 'desc')
            ->limit(5)
            ->with('warehouse')
            ->get()
            ->map(function($item) {
                return [
                    'warehouse' => $item->warehouse->title ?? 'N/A',
                    'total_purchase' => $item->total_purchase,
                ];
            })
            ->toArray();

        $topProducts = ProductOrderProduct::selectRaw('product_id, SUM(qty) as total_qty')
            ->join('product_orders', 'product_order_products.product_order_id', '=', 'product_orders.id')
            ->groupBy('product_id')
            ->orderBy('total_qty', 'desc')
            ->limit(5)
            ->with('product')
            ->get()
            ->map(function($item) {
                return [
                    'product_name' => $item->product->name ?? 'N/A',
                    'total_qty' => $item->total_qty,
                ];
            })
            ->toArray();

        $cashAccount = AcAccount::where('account_name', 'like', '%cash%')->first();
        $bankAccount = AcAccount::where('account_name', 'like', '%bank%')->first();

        $data = [
            'top_warehouses_sales' => $topWarehousesBySales,
            'top_warehouses_purchase' => $topWarehousesByPurchase,
            'top_products' => $topProducts,
        ];

        $summary = [
            'cash_balance' => $cashAccount ? $cashAccount->balance : 0,
            'bank_balance' => $bankAccount ? $bankAccount->balance : 0,
        ];

        return [
            'data' => $data,
            'summary' => $summary,
        ];
    }

    public function getTitle(): string
    {
        return 'Finance Dashboard';
    }

    public function getCsvHeaders(): array
    {
        return ['Item', 'Value'];
    }

    public function formatForCsv(array $data): array
    {
        return [];
    }

    public function getFiltersConfig(): array
    {
        return [];
    }
}
