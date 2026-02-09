<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductDemandPrediction;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductDemandPredictionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::select('id', 'name', 'stock', 'price')
            ->inRandomOrder()
            ->take(8)
            ->get();

        if ($products->isEmpty()) {
            $this->command?->warn('No products found. Skipping demand prediction seed.');
            return;
        }

        $predictedFor = Carbon::now()->addDays(7)->toDateString();
        $predictedAt = Carbon::now();

        $featureImportance = [
            ['feature' => 'rolling_mean_7', 'importance' => 0.32],
            ['feature' => 'conversion_rate', 'importance' => 0.27],
            ['feature' => 'site_visitors', 'importance' => 0.19],
            ['feature' => 'returns_ratio', 'importance' => 0.12],
            ['feature' => 'lag_7', 'importance' => 0.10],
        ];

        ProductDemandPrediction::query()->delete();

        foreach ($products as $product) {
            $baseDemand = max($product->stock, 1) * (1.2 + mt_rand(-10, 25) / 100);
            $growthPct = round(mt_rand(-8, 35) + mt_rand() / mt_getrandmax(), 2);

            $trendDirection = 'flat';
            if ($growthPct > 5) {
                $trendDirection = 'up';
            } elseif ($growthPct < -5) {
                $trendDirection = 'down';
            }

            $predictedDemand = round($baseDemand * (1 + $growthPct / 100), 3);
            $restockRecommended = $predictedDemand > ($product->stock ?? 0);

            $reason = $restockRecommended
                ? 'Predicted demand exceeds current stock.'
                : 'Stable demand with adequate stock.';

            ProductDemandPrediction::create([
                'product_id' => $product->id,
                'predicted_for' => $predictedFor,
                'predicted_demand' => $predictedDemand,
                'predicted_growth_pct' => $growthPct,
                'trend_direction' => $trendDirection,
                'confidence' => rand(60, 95) / 100,
                'restock_recommended' => $restockRecommended,
                'model_version' => 'seeded-' . Str::random(4),
                'recommendation_reason' => $reason,
                'feature_importance' => $featureImportance,
                'raw_payload' => [
                    'meta' => [
                        'current_stock' => $product->stock,
                        'trailing_mean' => round($baseDemand * 0.85, 2),
                        'latest_visitors' => rand(40, 400),
                        'latest_conversion' => rand(2, 5) / 100,
                    ],
                ],
                'predicted_at' => $predictedAt,
            ]);
        }

        $this->command?->info('Seeded product demand predictions for dashboard preview.');
    }
}

