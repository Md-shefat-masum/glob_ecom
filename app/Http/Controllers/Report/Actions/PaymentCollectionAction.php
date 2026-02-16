<?php

namespace App\Http\Controllers\Report\Actions;

use App\Http\Controllers\Account\Models\DbCustomerPayment;
use App\Models\ProductOrder;
use Carbon\Carbon;

class PaymentCollectionAction extends ReportAction
{
    public function run(array $filters): array
    {
        $query = DbCustomerPayment::query();

        if (!empty($filters['date_from'])) {
            $query->where('payment_date', '>=', Carbon::parse($filters['date_from'])->startOfDay());
        }
        if (!empty($filters['date_to'])) {
            $query->where('payment_date', '<=', Carbon::parse($filters['date_to'])->endOfDay());
        }
        if (!empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        $payments = $query->with('customer')->orderBy('payment_date', 'desc')->get();

        // Group by payment mode
        $modes = [];
        foreach ($payments as $payment) {
            $modeId = $payment->payment_mode_id ?? 0;
            $modeName = 'Mode ' . $modeId; // Simplified - can be enhanced with actual payment mode lookup
            
            if (!isset($modes[$modeId])) {
                $modes[$modeId] = [
                    'payment_mode' => $modeName,
                    'total_amount' => 0,
                    'count' => 0,
                ];
            }
            $modes[$modeId]['total_amount'] += $payment->payment ?? 0;
            $modes[$modeId]['count']++;
        }

        $data = array_values($modes);

        $summary = [
            'total_payments' => $payments->count(),
            'total_collected' => array_sum(array_column($data, 'total_amount')),
        ];

        return [
            'data' => $data,
            'summary' => $summary,
        ];
    }

    public function getTitle(): string
    {
        return 'Payment Collection Report';
    }

    public function getCsvHeaders(): array
    {
        return ['Payment Mode', 'Count', 'Total Amount'];
    }

    public function formatForCsv(array $data): array
    {
        $rows = [];
        foreach ($data as $item) {
            $rows[] = [
                $item['payment_mode'] ?? '',
                $item['count'] ?? 0,
                $item['total_amount'] ?? 0,
            ];
        }
        return $rows;
    }

    public function getFiltersConfig(): array
    {
        return array_merge(parent::getFiltersConfig(), [
            [
                'type' => 'select',
                'name' => 'customer_id',
                'label' => 'Customer',
                'required' => false,
            ],
        ]);
    }
}
