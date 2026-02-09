<?php

namespace App\Http\Controllers\Analytics;

use App\Http\Controllers\Controller;
use App\Models\ManualProductReturn;
use App\Models\Product;
use App\Models\ProductOrderReturn;
use App\Models\ProductPurchaseReturn;
use App\Models\ProductStockLog;
use App\Models\User;
use App\Services\Analytics\ProductDemandService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomePageAnalytics extends Controller
{
    protected ProductDemandService $demandService;

    /**
     * Apply authentication middleware
     */
    public function __construct(ProductDemandService $demandService)
    {
        $this->middleware('auth');
        $this->demandService = $demandService;
    }

    /**
     * Render the dashboard view. The heavy analytics data will be populated via AJAX.
     */
    public function index()
    {
        return view('backend.dashboard');
    }

    /**
     * Provide aggregated analytics for the dashboard based on the requested range.
     */
    public function summary(Request $request)
    {
        [$start, $end] = $this->resolveDateRange($request);

        $inventory = $this->inventorySnapshot($start, $end);
        $orders = $this->orderSnapshot($start, $end);
        $returns = $this->returnSnapshot($start, $end);

        $orders['return_rate'] = ($orders['total_orders'] ?? 0) > 0
            ? round((($returns['sales_returns']['count'] ?? 0) / $orders['total_orders']) * 100, 2)
            : 0;

        $finance = $this->financeSnapshot($start, $end, $orders, $returns);
        $people = $this->peopleSnapshot($start, $end, $orders);

        $charts = $this->buildCharts($start, $end);
        $tables = $this->buildTables($start, $end);

        $predictionData = [
            'enabled' => config('analytics.prediction_enabled', false),
            'items' => [],
            'feature_importance' => [],
        ];

        if ($predictionData['enabled']) {
            $predictions = $this->demandService->latestPredictions()->toArray();
            $predictionData['items'] = $predictions;
            $predictionData['feature_importance'] = $this->demandService->latestFeatureImportance();
            $predictionData['restock_alerts'] = collect($predictions)
                ->where('restock_recommended', true)
                ->values()
                ->take(5);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'meta' => [
                    'generated_at' => Carbon::now()->toDateTimeString(),
                    'range' => [
                        'from' => $start->toDateString(),
                        'to' => $end->toDateString(),
                    ],
                ],
                'kpis' => [
                    'inventory' => $inventory,
                    'orders' => $orders,
                    'returns' => $returns,
                    'finance' => $finance,
                    'people' => $people,
                ],
                'charts' => $charts,
                'tables' => $tables,
                'predictions' => $predictionData,
            ],
        ]);
    }

    /**
     * Normalise and guard the requested date range.
     */
    protected function resolveDateRange(Request $request): array
    {
        $defaultEnd = Carbon::now()->endOfDay();
        $defaultStart = (clone $defaultEnd)->subDays(6)->startOfDay();

        $start = $request->filled('from') ? Carbon::parse($request->get('from'))->startOfDay() : $defaultStart;
        $end = $request->filled('to') ? Carbon::parse($request->get('to'))->endOfDay() : $defaultEnd;

        if ($start->greaterThan($end)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        // Hard cap to prevent unbounded analytics queries
        if ($start->diffInDays($end) > 370) {
            $start = (clone $end)->subDays(370)->startOfDay();
        }

        return [$start, $end];
    }

    /**
     * Inventory summary for the range.
     */
    protected function inventorySnapshot(Carbon $start, Carbon $end): array
    {
        $closingStock = Product::sum(DB::raw('COALESCE(stock,0)'));

        $movement = ProductStockLog::select('type', DB::raw('SUM(quantity) as quantity'))
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('type')
            ->get()
            ->mapWithKeys(function ($row) {
                return [$row->type => (float) $row->quantity];
            });

        $direction = [
            'purchase' => 1,
            'initial' => 1,
            'return' => 1,
            'transfer_in' => 1,
            'sales' => -1,
            'purchase_return' => -1,
            'transfer_out' => -1,
            'waste' => -1,
        ];

        $stockIn = 0;
        $stockOut = 0;
        foreach ($movement as $type => $qty) {
            $sign = $direction[$type] ?? 0;
            if ($sign > 0) {
                $stockIn += $qty;
            } elseif ($sign < 0) {
                $stockOut += $qty;
            }
        }

        $netMovement = $stockIn - $stockOut;
        $openingStock = max($closingStock - $netMovement, 0);

        $lowStockThreshold = config('analytics.low_stock_threshold', 5);
        $lowStockCount = Product::where('status', 'active')
            ->where(function ($query) use ($lowStockThreshold) {
                $query->where('stock', '<=', DB::raw('COALESCE(low_stock, ' . (int) $lowStockThreshold . ')'))
                    ->orWhere('stock', '<=', $lowStockThreshold);
            })
            ->count();

        return [
            'opening_stock' => round($openingStock, 3),
            'stock_in' => round($stockIn, 3),
            'stock_out' => round($stockOut, 3),
            'closing_stock' => round($closingStock, 3),
            'low_stock_products' => $lowStockCount,
        ];
    }

    /**
     * Order summary for POS & eCommerce channels.
     */
    protected function orderSnapshot(Carbon $start, Carbon $end): array
    {
        // POS orders
        $posOrders = DB::table('product_orders')
            ->selectRaw('COUNT(*) as orders, COALESCE(SUM(total),0) as revenue')
            ->where('order_status', '!=', 'pending')
            ->whereBetween('created_at', [$start, $end])
            ->first();

        $posItems = DB::table('product_order_products as pop')
            ->join('product_orders as po', 'po.id', '=', 'pop.product_order_id')
            ->where('po.order_status', '!=', 'pending')
            ->whereBetween('po.created_at', [$start, $end])
            ->selectRaw('COALESCE(SUM(pop.qty),0) as qty, COALESCE(SUM(pop.total_price),0) as amount')
            ->first();

        // eCommerce orders
        $ecomOrders = DB::table('orders')
            ->selectRaw('COUNT(*) as orders, COALESCE(SUM(total),0) as revenue')
            ->whereNotIn('order_status', [5, 6])
            ->whereBetween('created_at', [$start, $end])
            ->first();

        $ecomItems = DB::table('order_details as od')
            ->join('orders as o', 'o.id', '=', 'od.order_id')
            ->whereNotIn('o.order_status', [5, 6])
            ->whereBetween('o.created_at', [$start, $end])
            ->selectRaw('COALESCE(SUM(od.qty),0) as qty, COALESCE(SUM(od.total_price),0) as amount')
            ->first();

        $totalOrders = ($posOrders->orders ?? 0) + ($ecomOrders->orders ?? 0);
        $grossRevenue = ($posOrders->revenue ?? 0) + ($ecomOrders->revenue ?? 0);
        $totalQty = ($posItems->qty ?? 0) + ($ecomItems->qty ?? 0);

        $averageOrderValue = $totalOrders > 0 ? $grossRevenue / $totalOrders : 0;
        $averageItemsPerOrder = $totalOrders > 0 ? $totalQty / $totalOrders : 0;

        $customerBuckets = [];

        $posCustomerCounts = DB::table('product_orders')
            ->select('customer_id', DB::raw('COUNT(*) as total'))
            ->where('order_status', '!=', 'pending')
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('customer_id')
            ->groupBy('customer_id')
            ->get();

        foreach ($posCustomerCounts as $row) {
            $customerBuckets['pos-' . $row->customer_id] = $row->total;
        }

        $ecomCustomerCounts = DB::table('orders')
            ->select('user_id', DB::raw('COUNT(*) as total'))
            ->whereNotIn('order_status', [5, 6])
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->get();

        foreach ($ecomCustomerCounts as $row) {
            $customerBuckets['web-' . $row->user_id] = ($customerBuckets['web-' . $row->user_id] ?? 0) + $row->total;
        }

        $distinctCustomers = count($customerBuckets);
        $repeatCustomers = collect($customerBuckets)->filter(function ($count) {
            return $count > 1;
        })->count();

        $repeatRate = $distinctCustomers > 0 ? ($repeatCustomers / $distinctCustomers) * 100 : 0;

        $posStatusRaw = DB::table('product_orders')
            ->select('order_status', DB::raw('COUNT(*) as total'))
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('order_status')
            ->get();

        $posStatusLabels = [
            'pending' => 'Pending',
            'invoiced' => 'Invoiced',
            'delivered' => 'Delivered',
        ];

        $posStatus = [];
        foreach ($posStatusRaw as $row) {
            $statusKey = $row->order_status ?? 'unknown';
            $posStatus[$posStatusLabels[$statusKey] ?? ucfirst($statusKey)] = (int) $row->total;
        }

        $ecomStatusRaw = DB::table('orders')
            ->select('order_status', DB::raw('COUNT(*) as total'))
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('order_status')
            ->get();

        $ecomStatusLabels = [
            0 => 'Pending',
            1 => 'Approved',
            2 => 'Dispatched',
            3 => 'In Transit',
            4 => 'Delivered',
            5 => 'Returned',
            6 => 'Cancelled',
        ];

        $ecomStatus = [];
        foreach ($ecomStatusRaw as $row) {
            $statusKey = $row->order_status;
            $label = $ecomStatusLabels[$statusKey] ?? 'Status ' . $statusKey;
            $ecomStatus[$label] = (int) $row->total;
        }

        return [
            'total_orders' => (int) $totalOrders,
            'pos_orders' => (int) ($posOrders->orders ?? 0),
            'ecommerce_orders' => (int) ($ecomOrders->orders ?? 0),
            'items_sold' => (int) $totalQty,
            'gross_revenue' => round($grossRevenue, 2),
            'avg_order_value' => round($averageOrderValue, 2),
            'pos_revenue' => round((float) ($posItems->amount ?? 0), 2),
            'ecommerce_revenue' => round((float) ($ecomItems->amount ?? 0), 2),
            'avg_items_per_order' => round($averageItemsPerOrder, 2),
            'distinct_customers' => $distinctCustomers,
            'repeat_customer_count' => $repeatCustomers,
            'repeat_customer_rate' => round($repeatRate, 2),
            'status_breakdown' => [
                'pos' => $posStatus,
                'ecommerce' => $ecomStatus,
            ],
        ];
    }

    /**
     * Purchase, sales and wastage returns
     */
    protected function returnSnapshot(Carbon $start, Carbon $end): array
    {
        $purchaseRet = ProductPurchaseReturn::whereBetween('created_at', [$start, $end])
            ->selectRaw('COALESCE(SUM(total),0) as total, COUNT(*) as count')
            ->first();

        $salesRet = ProductOrderReturn::whereBetween('created_at', [$start, $end])
            ->selectRaw('COALESCE(SUM(total),0) as total, COUNT(*) as count')
            ->first();

        $manualRet = ManualProductReturn::whereBetween('created_at', [$start, $end])
            ->selectRaw('COALESCE(SUM(total),0) as total, COUNT(*) as count')
            ->first();

        $totalValue = ($purchaseRet->total ?? 0) + ($salesRet->total ?? 0) + ($manualRet->total ?? 0);

        return [
            'purchase_returns' => [
                'count' => (int) ($purchaseRet->count ?? 0),
                'value' => round((float) ($purchaseRet->total ?? 0), 2),
            ],
            'sales_returns' => [
                'count' => (int) ($salesRet->count ?? 0) + (int) ($manualRet->count ?? 0),
                'value' => round((float) (($salesRet->total ?? 0) + ($manualRet->total ?? 0)), 2),
            ],
            'total_return_value' => round($totalValue, 2),
        ];
    }

    /**
     * Financial KPIs.
     */
    protected function financeSnapshot(Carbon $start, Carbon $end, array $orders, array $returns): array
    {
        $purchaseSpend = DB::table('product_purchase_orders')
            ->whereBetween('created_at', [$start, $end])
            ->sum(DB::raw('COALESCE(total, 0)'));

        $operationalExpense = DB::table('db_expenses')
            ->whereBetween('created_at', [$start, $end])
            ->sum(DB::raw('COALESCE(expense_amt, 0)'));

        $income = $orders['gross_revenue'] ?? 0;
        $returnAdjustments = $returns['total_return_value'] ?? 0;
        $receivables = DB::table('product_orders')
            ->where('order_status', '!=', 'pending')
            ->whereBetween('created_at', [$start, $end])
            ->sum(DB::raw('COALESCE(due_amount, 0)'));

        $netRevenue = $income - $returnAdjustments;
        $netProfit = $netRevenue - ($purchaseSpend + $operationalExpense);

        return [
            'income' => round($income, 2),
            'return_adjustment' => round($returnAdjustments, 2),
            'net_revenue' => round($netRevenue, 2),
            'purchase_spend' => round($purchaseSpend, 2),
            'operational_expense' => round($operationalExpense, 2),
            'net_profit' => round($netProfit, 2),
            'receivable_due' => round($receivables, 2),
        ];
    }

    /**
     * People/customer snapshot.
     */
    protected function peopleSnapshot(Carbon $start, Carbon $end, array $orders): array
    {
        $newCustomers = DB::table('customers')
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $newUsers = User::where('user_type', 3)
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $activeUsers = DB::table('user_activities')
            ->whereBetween('last_seen', [$start, $end])
            ->selectRaw('COUNT(DISTINCT user_id) as total')
            ->value('total') ?? 0;

        return [
            'new_crm_customers' => (int) $newCustomers,
            'new_portal_users' => (int) $newUsers,
            'active_users' => (int) $activeUsers,
            'repeat_customers' => (int) ($orders['repeat_customer_count'] ?? 0),
            'distinct_customers' => (int) ($orders['distinct_customers'] ?? 0),
        ];
    }

    /**
     * Assemble chart payloads (1) 14 day trend (2) visitor trend (3) YTD sales (4) channel split.
     */
    protected function buildCharts(Carbon $start, Carbon $end): array
    {
        $trendEnd = $end->copy()->endOfDay();
        $trendStart = $trendEnd->copy()->subDays(13)->startOfDay();

        $days = [];
        for ($cursor = $trendStart->copy(); $cursor <= $trendEnd; $cursor->addDay()) {
            $days[] = $cursor->copy();
        }

        $posRevenueByDay = DB::table('product_orders')
            ->selectRaw('DATE(created_at) as day, COALESCE(SUM(total),0) as revenue')
            ->where('order_status', '!=', 'pending')
            ->whereBetween('created_at', [$trendStart, $trendEnd])
            ->groupBy('day')
            ->pluck('revenue', 'day');

        $ecomRevenueByDay = DB::table('orders')
            ->selectRaw('DATE(created_at) as day, COALESCE(SUM(total),0) as revenue')
            ->whereNotIn('order_status', [5, 6])
            ->whereBetween('created_at', [$trendStart, $trendEnd])
            ->groupBy('day')
            ->pluck('revenue', 'day');

        $posQtyByDay = DB::table('product_order_products as pop')
            ->join('product_orders as po', 'po.id', '=', 'pop.product_order_id')
            ->selectRaw('DATE(po.created_at) as day, COALESCE(SUM(pop.qty),0) as qty')
            ->where('po.order_status', '!=', 'pending')
            ->whereBetween('po.created_at', [$trendStart, $trendEnd])
            ->groupBy('day')
            ->pluck('qty', 'day');

        $ecomQtyByDay = DB::table('order_details as od')
            ->join('orders as o', 'o.id', '=', 'od.order_id')
            ->selectRaw('DATE(o.created_at) as day, COALESCE(SUM(od.qty),0) as qty')
            ->whereNotIn('o.order_status', [5, 6])
            ->whereBetween('o.created_at', [$trendStart, $trendEnd])
            ->groupBy('day')
            ->pluck('qty', 'day');

        $incomeLabels = [];
        $incomeSeries = [];
        $quantitySeries = [];
        foreach ($days as $day) {
            $key = $day->toDateString();
            $incomeLabels[] = $day->format('d M');
            $dailyRevenue = ($posRevenueByDay[$key] ?? 0) + ($ecomRevenueByDay[$key] ?? 0);
            $incomeSeries[] = round($dailyRevenue, 2);
            $quantitySeries[] = (int) (($posQtyByDay[$key] ?? 0) + ($ecomQtyByDay[$key] ?? 0));
        }

        // Visitor trend (unique active users per day)
        $visitorSeries = DB::table('user_activities')
            ->selectRaw('DATE(last_seen) as day, COUNT(DISTINCT user_id) as visitors')
            ->whereBetween('last_seen', [$trendStart, $trendEnd])
            ->groupBy('day')
            ->pluck('visitors', 'day');

        $visitorLabels = [];
        $visitorData = [];
        foreach ($days as $day) {
            $key = $day->toDateString();
            $visitorLabels[] = $day->format('d M');
            $visitorData[] = (int) ($visitorSeries[$key] ?? 0);
        }

        // Year to date sales totals
        $yearStart = Carbon::now()->startOfYear();
        $months = [];
        for ($monthCursor = $yearStart->copy(); $monthCursor->year === $yearStart->year && $monthCursor <= $end; $monthCursor->addMonth()) {
            $months[] = $monthCursor->copy();
        }
        if (empty($months)) {
            $months[] = $yearStart;
        }

        $posMonthly = DB::table('product_orders')
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COALESCE(SUM(total),0) as total')
            ->where('order_status', '!=', 'pending')
            ->whereBetween('created_at', [$yearStart, $end])
            ->groupBy('month')
            ->pluck('total', 'month');

        $ecomMonthly = DB::table('orders')
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COALESCE(SUM(total),0) as total')
            ->whereNotIn('order_status', [5, 6])
            ->whereBetween('created_at', [$yearStart, $end])
            ->groupBy('month')
            ->pluck('total', 'month');

        $monthlyLabels = [];
        $monthlyTotals = [];
        foreach ($months as $month) {
            $key = $month->format('Y-m');
            $monthlyLabels[] = $month->format('M');
            $monthlyTotals[] = round(($posMonthly[$key] ?? 0) + ($ecomMonthly[$key] ?? 0), 2);
        }

        $channelMonthlyPos = [];
        $channelMonthlyEcom = [];
        foreach ($months as $month) {
            $key = $month->format('Y-m');
            $channelMonthlyPos[] = round($posMonthly[$key] ?? 0, 2);
            $channelMonthlyEcom[] = round($ecomMonthly[$key] ?? 0, 2);
        }

        return [
            'income_vs_sales' => [
                'labels' => $incomeLabels,
                'income' => $incomeSeries,
                'sales_qty' => $quantitySeries,
            ],
            'visitor_trend' => [
                'labels' => $visitorLabels,
                'visitors' => $visitorData,
            ],
            'year_to_date_sales' => [
                'labels' => $monthlyLabels,
                'totals' => $monthlyTotals,
            ],
            'channel_sales_split' => [
                'labels' => $monthlyLabels,
                'pos' => $channelMonthlyPos,
                'ecommerce' => $channelMonthlyEcom,
            ],
        ];
    }

    /**
     * Build table datasets for trending and low-stock products.
     */
    protected function buildTables(Carbon $start, Carbon $end): array
    {
        $posProductSales = DB::table('product_order_products as pop')
            ->join('product_orders as po', 'po.id', '=', 'pop.product_order_id')
            ->where('po.order_status', '!=', 'pending')
            ->whereBetween('po.created_at', [$start, $end])
            ->groupBy('pop.product_id')
            ->selectRaw('pop.product_id, COALESCE(SUM(pop.qty),0) as qty, COALESCE(SUM(pop.total_price),0) as revenue')
            ->get();

        $ecomProductSales = DB::table('order_details as od')
            ->join('orders as o', 'o.id', '=', 'od.order_id')
            ->whereNotIn('o.order_status', [5, 6])
            ->whereBetween('o.created_at', [$start, $end])
            ->groupBy('od.product_id')
            ->selectRaw('od.product_id, COALESCE(SUM(od.qty),0) as qty, COALESCE(SUM(od.total_price),0) as revenue')
            ->get();

        $aggregated = [];
        foreach ($posProductSales as $row) {
            if (!$row->product_id) {
                continue;
            }
            $aggregated[$row->product_id] = [
                'qty' => (float) $row->qty,
                'revenue' => (float) $row->revenue,
            ];
        }

        foreach ($ecomProductSales as $row) {
            if (!$row->product_id) {
                continue;
            }
            if (!isset($aggregated[$row->product_id])) {
                $aggregated[$row->product_id] = ['qty' => 0, 'revenue' => 0];
            }
            $aggregated[$row->product_id]['qty'] += (float) $row->qty;
            $aggregated[$row->product_id]['revenue'] += (float) $row->revenue;
        }

        $productIds = array_keys($aggregated);
        $products = Product::whereIn('id', $productIds)
            ->get(['id', 'name', 'sku'])
            ->keyBy('id');

        $topProducts = collect($aggregated)
            ->map(function ($totals, $productId) use ($products) {
                $product = $products->get($productId);
                if (!$product) {
                    return null;
                }
                return [
                    'product_id' => $productId,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'sold' => (float) $totals['qty'],
                    'revenue' => round($totals['revenue'], 2),
                ];
            })
            ->filter()
            ->sortByDesc('sold')
            ->values()
            ->take(5)
            ->map(function ($item, $index) {
                return [
                    'rank' => $index + 1,
                    'name' => $item['name'],
                    'sku' => $item['sku'],
                    'sold' => (int) $item['sold'],
                    'revenue' => $item['revenue'],
                ];
            });

        $lowStockThreshold = config('analytics.low_stock_threshold', 5);
        $lowStockProducts = Product::where('status', 'active')
            ->where(function ($query) use ($lowStockThreshold) {
                $query->where('stock', '<=', DB::raw('COALESCE(low_stock, ' . (int) $lowStockThreshold . ')'))
                    ->orWhere('stock', '<=', $lowStockThreshold);
            })
            ->orderBy('stock')
            ->limit(10)
            ->get(['id', 'name', 'sku', 'stock', 'low_stock'])
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'stock' => (float) $product->stock,
                    'reorder_level' => $product->low_stock ?? config('analytics.low_stock_threshold', 5),
                ];
            });

        $topSuppliers = DB::table('product_purchase_orders as ppo')
            ->leftJoin('product_suppliers as ps', 'ps.id', '=', 'ppo.product_supplier_id')
            ->whereBetween('ppo.created_at', [$start, $end])
            ->groupBy('ppo.product_supplier_id', 'ps.name')
            ->selectRaw('ps.name as supplier_name, COUNT(*) as purchase_orders, COALESCE(SUM(ppo.total),0) as total_spend')
            ->orderByDesc('total_spend')
            ->limit(5)
            ->get()
            ->map(function ($row, $index) {
                return [
                    'rank' => $index + 1,
                    'supplier' => $row->supplier_name ?? 'Unknown Supplier',
                    'orders' => (int) $row->purchase_orders,
                    'spend' => round((float) $row->total_spend, 2),
                ];
            });

        return [
            'trending_products' => $topProducts,
            'low_stock_products' => $lowStockProducts,
            'top_suppliers' => $topSuppliers,
        ];
    }
}
