<?php

namespace App\Http\Controllers\Report\Actions;

use App\Http\Controllers\Customer\Models\Customer;
use Illuminate\Support\Facades\DB;

class CustomersAdvanceReportAction extends ReportAction
{
    public function run(array $filters): array
    {
        $customers = Customer::where('status', 'active')
            ->where(function($query) {
                $query->whereNotNull('available_advance')
                    ->where('available_advance', '>', 0);
            })
            ->get();

        $data = [];
        foreach ($customers as $customer) {
            $availableAdvance = $customer->available_advance ?? 0;
            if ($availableAdvance > 0) {
                $data[] = [
                    'customer_id' => $customer->id,
                    'customer_name' => $customer->name,
                    'phone' => $customer->phone,
                    'email' => $customer->email,
                    'available_advance' => $availableAdvance,
                ];
            }
        }

        $summary = [
            'total_customers' => count($data),
            'total_advance' => array_sum(array_column($data, 'available_advance')),
        ];

        return [
            'data' => $data,
            'summary' => $summary,
        ];
    }

    public function getTitle(): string
    {
        return 'Customers Advance Report';
    }

    public function getCsvHeaders(): array
    {
        return ['Customer', 'Phone', 'Email', 'Available Advance'];
    }

    public function formatForCsv(array $data): array
    {
        $rows = [];
        foreach ($data as $item) {
            $rows[] = [
                $item['customer_name'] ?? '',
                $item['phone'] ?? '',
                $item['email'] ?? '',
                $item['available_advance'] ?? 0,
            ];
        }
        return $rows;
    }

    public function getFiltersConfig(): array
    {
        return [];
    }
}
