<?php

namespace App\Http\Controllers\Report\Actions;

abstract class ReportAction
{
    /**
     * Run the report and return data
     * @param array $filters
     * @return array ['data' => [...], 'summary' => [...]]
     */
    abstract public function run(array $filters): array;

    /**
     * Get report title for export filename
     */
    abstract public function getTitle(): string;

    /**
     * Get CSV headers
     */
    abstract public function getCsvHeaders(): array;

    /**
     * Format data for CSV export
     */
    abstract public function formatForCsv(array $data): array;

    /**
     * Get filter configuration for dynamic form rendering
     * @return array [['type' => 'date', 'name' => 'date_from', 'label' => 'From Date', ...], ...]
     */
    public function getFiltersConfig(): array
    {
        return [
            [
                'type' => 'date',
                'name' => 'date_from',
                'label' => 'From Date',
                'required' => true,
            ],
            [
                'type' => 'date',
                'name' => 'date_to',
                'label' => 'To Date',
                'required' => true,
            ],
        ];
    }
}
