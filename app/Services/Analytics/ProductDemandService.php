<?php

namespace App\Services\Analytics;

use App\Models\Product;
use App\Models\ProductDemandPrediction;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ProductDemandService
{
    public const MODEL_VERSION = 'forecast-v1';

    /**
     * Generate and persist product demand predictions.
     *
     * @param Carbon|null $start
     * @param Carbon|null $end
     * @return array
     *
     * @throws \RuntimeException
     */
    public function generate(?Carbon $start = null, ?Carbon $end = null): array
    {
        $end = $end ?? Carbon::now()->endOfDay();
        $start = $start ?? $end->copy()->subDays(120)->startOfDay();

        $payload = $this->buildHistoricalDataset($start, $end);

        if (empty($payload['products'])) {
            throw new \RuntimeException('Insufficient historical data to train predictor.');
        }

        $pythonResponse = $this->runPythonPredictor($payload);

        return $this->persistPredictions($pythonResponse);
    }

    /**
     * Build the historical dataset expected by the Python predictor.
     *
     * @param Carbon $start
     * @param Carbon $end
     * @return array
     */
    protected function buildHistoricalDataset(Carbon $start, Carbon $end): array
    {
        $periodDays = $start->diffInDays($end) + 1;
        $dateCursor = $start->copy();
        $dayKeys = [];
        for ($i = 0; $i < $periodDays; $i++) {
            $dayKeys[] = $dateCursor->toDateString();
            $dateCursor->addDay();
        }

        $posSales = DB::table('product_order_products as pop')
            ->join('product_orders as po', 'po.id', '=', 'pop.product_order_id')
            ->selectRaw('pop.product_id, DATE(po.created_at) as day, SUM(pop.qty) as qty, SUM(pop.total_price) as revenue, COUNT(DISTINCT po.id) as orders')
            ->whereBetween('po.created_at', [$start, $end])
            ->groupBy('pop.product_id', 'day')
            ->get();

        $webSales = DB::table('order_details as od')
            ->join('orders as o', 'o.id', '=', 'od.order_id')
            ->selectRaw('od.product_id, DATE(o.created_at) as day, SUM(od.qty) as qty, SUM(od.total_price) as revenue, COUNT(DISTINCT o.id) as orders')
            ->whereBetween('o.created_at', [$start, $end])
            ->groupBy('od.product_id', 'day')
            ->get();

        $returns = DB::table('product_order_return_products as porp')
            ->join('product_order_returns as por', 'por.id', '=', 'porp.product_order_return_id')
            ->selectRaw('porp.product_id, DATE(por.created_at) as day, SUM(porp.qty) as qty')
            ->whereBetween('por.created_at', [$start, $end])
            ->groupBy('porp.product_id', 'day')
            ->get();

        $siteVisitors = DB::table('user_activities')
            ->selectRaw('DATE(last_seen) as day, COUNT(DISTINCT user_id) as visitors')
            ->whereBetween('last_seen', [$start, $end])
            ->groupBy('day')
            ->pluck('visitors', 'day');

        $productIds = collect($posSales)->pluck('product_id')
            ->merge(collect($webSales)->pluck('product_id'))
            ->unique()
            ->values();

        $products = Product::whereIn('id', $productIds)
            ->get(['id', 'name', 'stock', 'price', 'discount_price'])
            ->keyBy('id');

        $posGrouped = $posSales->groupBy('product_id');
        $webGrouped = $webSales->groupBy('product_id');
        $returnGrouped = $returns->groupBy('product_id');

        $productPayload = [];

        foreach ($productIds as $productId) {
            $product = $products->get($productId);
            if (!$product) {
                continue;
            }

            $history = [];
            foreach ($dayKeys as $day) {
                $posRow = optional($posGrouped->get($productId))->firstWhere('day', $day);
                $webRow = optional($webGrouped->get($productId))->firstWhere('day', $day);
                $returnRow = optional($returnGrouped->get($productId))->firstWhere('day', $day);

                $salesQty = (float) ($posRow->qty ?? 0) + (float) ($webRow->qty ?? 0);
                $salesRevenue = (float) ($posRow->revenue ?? 0) + (float) ($webRow->revenue ?? 0);
                $ordersCount = (int) ($posRow->orders ?? 0) + (int) ($webRow->orders ?? 0);
                $returnsQty = (float) ($returnRow->qty ?? 0);
                $visitors = (int) ($siteVisitors[$day] ?? 0);
                $conversion = $visitors > 0 ? $salesQty / $visitors : 0;

                $history[] = [
                    'date' => $day,
                    'sales_qty' => round($salesQty, 3),
                    'sales_revenue' => round($salesRevenue, 2),
                    'orders_count' => $ordersCount,
                    'returns_qty' => round($returnsQty, 3),
                    'site_visitors' => $visitors,
                    'conversion_rate' => round($conversion, 6),
                ];
            }

            if (collect($history)->sum('sales_qty') <= 0) {
                continue;
            }

            $productPayload[] = [
                'product_id' => $productId,
                'name' => $product->name,
                'current_stock' => (float) ($product->stock ?? 0),
                'price' => (float) ($product->price ?? 0),
                'discount_price' => (float) ($product->discount_price ?? 0),
                'history' => $history,
            ];
        }

        return [
            'generated_at' => Carbon::now()->toIso8601String(),
            'start' => $start->toDateString(),
            'end' => $end->toDateString(),
            'products' => $productPayload,
        ];
    }

    /**
     * Execute the Python predictor script.
     *
     * @param array $payload
     * @return array
     */
    protected function runPythonPredictor(array $payload): array
    {
        $analyticsDir = storage_path('app/analytics');
        if (!File::exists($analyticsDir)) {
            File::makeDirectory($analyticsDir, 0755, true);
        }

        $inputPath = $analyticsDir . '/' . Str::uuid() . '_input.json';
        $outputPath = $analyticsDir . '/' . Str::uuid() . '_output.json';

        File::put($inputPath, json_encode($payload));

        $pythonBinary = config('analytics.python_binary', env('PYTHON_BINARY', 'python3'));
        $scriptPath = base_path('analytics/predictor.py');

        $process = new Process([$pythonBinary, $scriptPath, '--input', $inputPath, '--output', $outputPath]);
        $process->setTimeout(180);
        $process->run();

        if (!$process->isSuccessful()) {
            File::delete($inputPath);
            File::delete($outputPath);
            throw new ProcessFailedException($process);
        }

        if (!File::exists($outputPath)) {
            File::delete($inputPath);
            throw new \RuntimeException('Predictor did not produce output.');
        }

        $result = json_decode(File::get($outputPath), true);

        File::delete($inputPath);
        File::delete($outputPath);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Unable to decode predictor response: ' . json_last_error_msg());
        }

        return $result;
    }

    /**
     * Store predictions and return array representation.
     *
     * @param array $pythonResponse
     * @return array
     */
    protected function persistPredictions(array $pythonResponse): array
    {
        $predictions = $pythonResponse['predictions'] ?? [];
        $featureImportance = $pythonResponse['feature_importance'] ?? [];
        $generatedAt = Carbon::parse($pythonResponse['generated_at'] ?? now());
        $predictedFor = Carbon::parse($pythonResponse['predicted_for'] ?? $generatedAt->copy()->addWeek());

        ProductDemandPrediction::query()->delete();

        $records = [];
        foreach ($predictions as $item) {
            $records[] = ProductDemandPrediction::create([
                'product_id' => $item['product_id'] ?? null,
                'predicted_for' => $predictedFor,
                'predicted_demand' => $item['predicted_demand'] ?? null,
                'predicted_growth_pct' => $item['predicted_growth_pct'] ?? null,
                'trend_direction' => $item['trend_direction'] ?? 'flat',
                'confidence' => $item['confidence'] ?? null,
                'restock_recommended' => $item['restock_recommended'] ?? false,
                'model_version' => $pythonResponse['model_version'] ?? self::MODEL_VERSION,
                'recommendation_reason' => $item['reason'] ?? null,
                'feature_importance' => $featureImportance,
                'raw_payload' => $item,
                'predicted_at' => $generatedAt,
            ])->toArray();
        }

        return [
            'predictions' => $records,
            'anomalies' => $pythonResponse['anomalies'] ?? [],
            'feature_importance' => $featureImportance,
            'model_version' => $pythonResponse['model_version'] ?? self::MODEL_VERSION,
            'generated_at' => $generatedAt->toIso8601String(),
            'predicted_for' => $predictedFor->toDateString(),
        ];
    }

    /**
     * Retrieve latest predictions for UI.
     *
     * @param int $limit
     * @return Collection
     */
    public function latestPredictions(int $limit = 10): Collection
    {
        return ProductDemandPrediction::with('product:id,name,stock')
            ->orderByDesc('predicted_demand')
            ->limit($limit)
            ->get()
            ->map(function (ProductDemandPrediction $prediction) {
                return [
                    'product_id' => $prediction->product_id,
                    'product_name' => $prediction->product->name ?? 'Unknown Product',
                    'predicted_for' => optional($prediction->predicted_for)->toDateString(),
                    'predicted_at' => optional($prediction->predicted_at)->toDateTimeString(),
                    'predicted_demand' => $prediction->predicted_demand,
                    'predicted_growth_pct' => $prediction->predicted_growth_pct,
                    'trend_direction' => $prediction->trend_direction,
                    'confidence' => $prediction->confidence,
                    'restock_recommended' => $prediction->restock_recommended,
                    'reason' => $prediction->recommendation_reason,
                    'current_stock' => $prediction->product->stock ?? null,
                    'meta' => $prediction->raw_payload['meta'] ?? [],
                ];
            });
    }

    /**
     * Retrieve latest feature importance snapshot.
     */
    public function latestFeatureImportance(): array
    {
        $record = ProductDemandPrediction::orderByDesc('predicted_at')->first();
        return $record?->feature_importance ?? [];
    }
}

