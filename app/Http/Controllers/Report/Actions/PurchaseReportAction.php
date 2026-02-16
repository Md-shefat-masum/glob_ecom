<?php

namespace App\Http\Controllers\Report\Actions;

use App\Http\Controllers\Inventory\Models\ProductPurchaseOrder;
use Carbon\Carbon;

class PurchaseReportAction extends ReportAction
{
    public function run(array $filters): array
    {
        $query = ProductPurchaseOrder::query();

        if (!empty($filters['date_from'])) {
            $query->where('date', '>=', Carbon::parse($filters['date_from'])->startOfDay());
        }
        if (!empty($filters['date_to'])) {
            $query->where('date', '<=', Carbon::parse($filters['date_to'])->endOfDay());
        }
        if (!empty($filters['warehouse_id'])) {
            $query->where('product_warehouse_id', $filters['warehouse_id']);
        }
        if (!empty($filters['supplier_id'])) {
            $query->where('product_supplier_id', $filters['supplier_id']);
        }

        $purchases = $query->with(['supplier', 'warehouse'])->orderBy('date', 'desc')->get();

        $summary = [
            'total_purchases' => $purchases->count(),
            'total_amount' => $purchases->sum('total'),
        ];

        return [
            'data' => $purchases->toArray(),
            'summary' => $summary,
        ];
    }

    public function getTitle(): string
    {
        return 'Purchase Report';
    }

    public function getCsvHeaders(): array
    {
        return ['Order Code', 'Date', 'Supplier', 'Warehouse', 'Total'];
    }

    public function formatForCsv(array $data): array
    {
        $rows = [];
        foreach ($data as $purchase) {
            $rows[] = [
                $purchase['order_code'] ?? '',
                $purchase['date'] ?? '',
                $purchase['supplier']['name'] ?? '',
                $purchase['warehouse']['title'] ?? '',
                $purchase['total'] ?? 0,
            ];
        }
        return $rows;
    }

    public function getFiltersConfig(): array
    {
        return array_merge(parent::getFiltersConfig(), [
            [
                'type' => 'select',
                'name' => 'warehouse_id',
                'label' => 'Warehouse',
                'required' => false,
            ],
            [
                'type' => 'select',
                'name' => 'supplier_id',
                'label' => 'Supplier',
                'required' => false,
            ],
        ]);
    }
}
