<?php

namespace App\Console\Commands;

use App\Services\Analytics\ProductDemandService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RunDemandPrediction extends Command
{
    protected $signature = 'analytics:predict-demand {--start=} {--end=}';

    protected $description = 'Train predictive model and generate product demand recommendations';

    protected ProductDemandService $service;

    public function __construct(ProductDemandService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    public function handle(): int
    {
        if (!config('analytics.prediction_enabled')) {
            $this->warn('Predictive analytics is disabled via configuration.');
            return self::SUCCESS;
        }

        $start = $this->option('start') ? Carbon::parse($this->option('start'))->startOfDay() : null;
        $end = $this->option('end') ? Carbon::parse($this->option('end'))->endOfDay() : null;

        try {
            $this->info('Collecting historical data...');
            $result = $this->service->generate($start, $end);
            $predictions = $result['predictions'] ?? [];

            $this->table(
                ['Product ID', 'Predicted Demand', 'Growth %', 'Trend', 'Restock', 'Reason'],
                collect($predictions)->map(function ($row) {
                    return [
                        $row['product_id'] ?? 'N/A',
                        $row['predicted_demand'] ?? 0,
                        $row['predicted_growth_pct'] ?? 0,
                        $row['trend_direction'] ?? '-',
                        !empty($row['restock_recommended']) ? 'Yes' : 'No',
                        $row['recommendation_reason'] ?? '',
                    ];
                })
            );

            if (!empty($result['anomalies'])) {
                $this->info('Detected demand anomalies:');
                $this->table(
                    ['Product ID', 'Date', 'Z-score', 'Direction'],
                    collect($result['anomalies'])->map(function ($row) {
                        return [
                            $row['product_id'] ?? 'N/A',
                            $row['date'] ?? '-',
                            $row['z_score'] ?? '-',
                            $row['direction'] ?? '-',
                        ];
                    })
                );
            }

            $this->info('Demand prediction completed successfully.');
            return self::SUCCESS;
        } catch (\Throwable $e) {
            Log::error('Demand prediction failed', ['exception' => $e]);
            $this->error('Prediction failed: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}

