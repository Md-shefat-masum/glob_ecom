<?php

namespace App\Http\Controllers\Report\Actions;

use App\Http\Controllers\Inventory\Models\ProductPurchaseOrder;
use App\Models\ProductPurchaseReturn;
use App\Http\Controllers\Account\Models\DbPurchasePayment;
use Illuminate\Support\Facades\DB;

class SupplierDueReportAction extends ReportAction
{
    public function run(array $filters): array
    {
        $suppliers = DB::table('product_suppliers')->where('status', 'active')->get();

        $data = [];
        foreach ($suppliers as $supplier) {
            $totalPurchase = ProductPurchaseOrder::where('product_supplier_id', $supplier->id)
                ->where('status', 'active')
                ->sum('total');

            $totalReturn = ProductPurchaseReturn::where('product_supplier_id', $supplier->id)
                ->where('status', 'active')
                ->sum('total');

            $totalPaid = DbPurchasePayment::where('supplier_id', $supplier->id)
                ->where('status', 'active')
                ->sum('payment');

            $due = ($totalPurchase - $totalReturn) - $totalPaid;

            if ($due > 0 || empty($filters['show_only_due'])) {
                $data[] = [
                    'supplier_id' => $supplier->id,
                    'supplier_name' => $supplier->name,
                    'total_purchase' => $totalPurchase,
                    'total_return' => $totalReturn,
                    'total_paid' => $totalPaid,
                    'due' => $due,
                ];
            }
        }

        $summary = [
            'total_suppliers' => count($data),
            'total_due' => array_sum(array_column($data, 'due')),
        ];

        return [
            'data' => $data,
            'summary' => $summary,
        ];
    }

    public function getTitle(): string
    {
        return 'Supplier Due Report';
    }

    public function getCsvHeaders(): array
    {
        return ['Supplier', 'Total Purchase', 'Total Return', 'Total Paid', 'Due'];
    }

    public function formatForCsv(array $data): array
    {
        $rows = [];
        foreach ($data as $item) {
            $rows[] = [
                $item['supplier_name'] ?? '',
                $item['total_purchase'] ?? 0,
                $item['total_return'] ?? 0,
                $item['total_paid'] ?? 0,
                $item['due'] ?? 0,
            ];
        }
        return $rows;
    }

    public function getFiltersConfig(): array
    {
        return [
            [
                'type' => 'checkbox',
                'name' => 'show_only_due',
                'label' => 'Show Only Suppliers with Due',
                'required' => false,
            ],
        ];
    }
}
