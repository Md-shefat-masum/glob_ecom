<?php

namespace App\Http\Controllers\Report\Actions;

use App\Models\ProductOrder;
use App\Models\BillingAddress;
use Carbon\Carbon;

class LocationReportAction extends ReportAction
{
    public function run(array $filters): array
    {
        $query = ProductOrder::query();

        if (!empty($filters['date_from'])) {
            $query->where('sale_date', '>=', Carbon::parse($filters['date_from'])->startOfDay());
        }
        if (!empty($filters['date_to'])) {
            $query->where('sale_date', '<=', Carbon::parse($filters['date_to'])->endOfDay());
        }

        $orders = $query->with('customer')->get();

        // Group by district/division from billing addresses
        $locations = [];
        foreach ($orders as $order) {
            $billing = BillingAddress::where('order_id', $order->id)->first();
            $districtId = $billing ? $billing->district_id : null;
            $districtName = $districtId ? 'District ' . $districtId : 'Unknown';

            if (!isset($locations[$districtName])) {
                $locations[$districtName] = [
                    'region_name' => $districtName,
                    'order_count' => 0,
                    'total_sales' => 0,
                ];
            }
            $locations[$districtName]['order_count']++;
            $locations[$districtName]['total_sales'] += $order->total;
        }

        $data = array_values($locations);

        $summary = [
            'total_regions' => count($data),
            'total_orders' => array_sum(array_column($data, 'order_count')),
        ];

        return [
            'data' => $data,
            'summary' => $summary,
        ];
    }

    public function getTitle(): string
    {
        return 'Location Report';
    }

    public function getCsvHeaders(): array
    {
        return ['Region', 'Order Count', 'Total Sales'];
    }

    public function formatForCsv(array $data): array
    {
        $rows = [];
        foreach ($data as $item) {
            $rows[] = [
                $item['region_name'] ?? '',
                $item['order_count'] ?? 0,
                $item['total_sales'] ?? 0,
            ];
        }
        return $rows;
    }
}
