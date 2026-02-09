<?php

namespace App\Http\Controllers\Analytics;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductOrder;
use App\Models\ProductOrderProduct;
use App\Models\ProductReview;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Display analytics dashboard
     */
    public function index()
    {
        return view('backend.analytics.dashboard');
    }

    /**
     * Get overview statistics
     */
    public function getOverview(Request $request)
    {
        try {
            $currentMonth = Carbon::now()->startOfMonth();
            $lastMonth = Carbon::now()->subMonth()->startOfMonth();
            $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

            // Current month stats
            $currentSales = ProductOrder::where('created_at', '>=', $currentMonth)
                ->where('order_status', '!=', 'pending')
                ->sum('total');

            $currentOrders = ProductOrder::where('created_at', '>=', $currentMonth)
                ->where('order_status', '!=', 'pending')
                ->count();

            $currentRevenue = ProductOrder::where('created_at', '>=', $currentMonth)
                ->where('order_status', '!=', 'pending')
                ->sum('total');

            // Last month stats
            $lastMonthSales = ProductOrder::whereBetween('created_at', [$lastMonth, $lastMonthEnd])
                ->where('order_status', '!=', 'pending')
                ->sum('total');

            $lastMonthOrders = ProductOrder::whereBetween('created_at', [$lastMonth, $lastMonthEnd])
                ->where('order_status', '!=', 'pending')
                ->count();

            $lastMonthRevenue = ProductOrder::whereBetween('created_at', [$lastMonth, $lastMonthEnd])
                ->where('order_status', '!=', 'pending')
                ->sum('total');

            // Calculate percentage changes
            $salesChange = $lastMonthSales > 0 
                ? (($currentSales - $lastMonthSales) / $lastMonthSales) * 100 
                : ($currentSales > 0 ? 100 : 0);

            $ordersChange = $lastMonthOrders > 0 
                ? (($currentOrders - $lastMonthOrders) / $lastMonthOrders) * 100 
                : ($currentOrders > 0 ? 100 : 0);

            $revenueChange = $lastMonthRevenue > 0 
                ? (($currentRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 
                : ($currentRevenue > 0 ? 100 : 0);

            // Additional stats
            $totalProducts = Product::where('status', 'active')->count();
            $lowStockProducts = Product::where('status', 'active')
                ->where('stock', '<', 10)
                ->count();

            $totalCustomers = DB::table('users')
                ->where('user_type', 2) // Assuming 2 is customer
                ->count();

            $avgOrderValue = $currentOrders > 0 ? $currentSales / $currentOrders : 0;

            return response()->json([
                'success' => true,
                'data' => [
                    'current' => [
                        'sales' => round($currentSales, 2),
                        'orders' => $currentOrders,
                        'revenue' => round($currentRevenue, 2),
                        'avg_order_value' => round($avgOrderValue, 2),
                    ],
                    'last_month' => [
                        'sales' => round($lastMonthSales, 2),
                        'orders' => $lastMonthOrders,
                        'revenue' => round($lastMonthRevenue, 2),
                    ],
                    'changes' => [
                        'sales' => round($salesChange, 2),
                        'orders' => round($ordersChange, 2),
                        'revenue' => round($revenueChange, 2),
                    ],
                    'stats' => [
                        'total_products' => $totalProducts,
                        'low_stock_products' => $lowStockProducts,
                        'total_customers' => $totalCustomers,
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get top rated products
     */
    public function getTopRatedProducts(Request $request)
    {
        try {
            $limit = $request->get('limit', 10);

            $topRated = DB::table('products')
                ->select(
                    'products.id',
                    'products.name',
                    'products.image',
                    'products.price',
                    'products.discount_price',
                    DB::raw('AVG(product_reviews.rating) as avg_rating'),
                    DB::raw('COUNT(product_reviews.id) as review_count')
                )
                ->join('product_reviews', 'products.id', '=', 'product_reviews.product_id')
                ->where('products.status', 'active')
                ->where('product_reviews.status', 1)
                ->groupBy('products.id', 'products.name', 'products.image', 'products.price', 'products.discount_price')
                ->having('review_count', '>', 0)
                ->orderBy('avg_rating', 'desc')
                ->orderBy('review_count', 'desc')
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $topRated
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get top viewed products
     */
    public function getTopViewedProducts(Request $request)
    {
        try {
            $limit = $request->get('limit', 10);
            $days = $request->get('days', 30);

            $startDate = Carbon::now()->subDays($days);

            // Check if product_views table exists
            $tableExists = DB::getSchemaBuilder()->hasTable('product_views');

            if ($tableExists) {
                $topViewed = DB::table('products')
                    ->select(
                        'products.id',
                        'products.name',
                        'products.image',
                        'products.price',
                        'products.discount_price',
                        DB::raw('COUNT(product_views.id) as view_count')
                    )
                    ->join('product_views', 'products.id', '=', 'product_views.product_id')
                    ->where('products.status', 'active')
                    ->where('product_views.viewed_at', '>=', $startDate)
                    ->groupBy('products.id', 'products.name', 'products.image', 'products.price', 'products.discount_price')
                    ->orderBy('view_count', 'desc')
                    ->limit($limit)
                    ->get();
            } else {
                // Fallback: use order data
                $topViewed = DB::table('products')
                    ->select(
                        'products.id',
                        'products.name',
                        'products.image',
                        'products.price',
                        'products.discount_price',
                        DB::raw('SUM(product_order_products.qty) as view_count')
                    )
                    ->join('product_order_products', 'products.id', '=', 'product_order_products.product_id')
                    ->where('products.status', 'active')
                    ->groupBy('products.id', 'products.name', 'products.image', 'products.price', 'products.discount_price')
                    ->orderBy('view_count', 'desc')
                    ->limit($limit)
                    ->get();
            }

            return response()->json([
                'success' => true,
                'data' => $topViewed
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get top categories by sales
     */
    public function getTopCategories(Request $request)
    {
        try {
            $limit = $request->get('limit', 10);
            $days = $request->get('days', 30);

            $startDate = Carbon::now()->subDays($days);

            $topCategories = DB::table('categories')
                ->select(
                    'categories.id',
                    'categories.name',
                    'categories.icon',
                    DB::raw('SUM(product_order_products.qty) as total_sold'),
                    DB::raw('SUM(product_order_products.total_price) as total_revenue'),
                    DB::raw('COUNT(DISTINCT product_order_products.product_id) as product_count')
                )
                ->join('products', 'categories.id', '=', 'products.category_id')
                ->join('product_order_products', 'products.id', '=', 'product_order_products.product_id')
                ->join('product_orders', 'product_order_products.product_order_id', '=', 'product_orders.id')
                ->where('categories.status', 'active')
                ->where('product_orders.order_status', '!=', 'pending')
                ->where('product_orders.created_at', '>=', $startDate)
                ->groupBy('categories.id', 'categories.name', 'categories.icon')
                ->orderBy('total_revenue', 'desc')
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $topCategories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sales chart data (last 12 months)
     */
    public function getSalesChart(Request $request)
    {
        try {
            $months = [];
            $sales = [];
            $orders = [];

            for ($i = 11; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $monthStart = $date->copy()->startOfMonth();
                $monthEnd = $date->copy()->endOfMonth();

                $months[] = $date->format('M Y');

                $monthlySales = ProductOrder::whereBetween('created_at', [$monthStart, $monthEnd])
                    ->where('order_status', '!=', 'pending')
                    ->sum('total');

                $monthlyOrders = ProductOrder::whereBetween('created_at', [$monthStart, $monthEnd])
                    ->where('order_status', '!=', 'pending')
                    ->count();

                $sales[] = round($monthlySales, 2);
                $orders[] = $monthlyOrders;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'labels' => $months,
                    'sales' => $sales,
                    'orders' => $orders
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get category distribution for pie chart
     */
    public function getCategoryDistribution(Request $request)
    {
        try {
            $days = $request->get('days', 30);
            $startDate = Carbon::now()->subDays($days);

            $distribution = DB::table('categories')
                ->select(
                    'categories.name',
                    DB::raw('SUM(product_order_products.total_price) as revenue')
                )
                ->join('products', 'categories.id', '=', 'products.category_id')
                ->join('product_order_products', 'products.id', '=', 'product_order_products.product_id')
                ->join('product_orders', 'product_order_products.product_order_id', '=', 'product_orders.id')
                ->where('categories.status', 'active')
                ->where('product_orders.order_status', '!=', 'pending')
                ->where('product_orders.created_at', '>=', $startDate)
                ->groupBy('categories.id', 'categories.name')
                ->orderBy('revenue', 'desc')
                ->limit(8)
                ->get();

            $labels = [];
            $data = [];
            $colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69', '#2e59d9'];

            foreach ($distribution as $index => $item) {
                $labels[] = $item->name;
                $data[] = round($item->revenue, 2);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'labels' => $labels,
                    'values' => $data,
                    'colors' => array_slice($colors, 0, count($labels))
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get top selling products
     */
    public function getTopSellingProducts(Request $request)
    {
        try {
            $limit = $request->get('limit', 10);
            $days = $request->get('days', 30);

            $startDate = Carbon::now()->subDays($days);

            $topSelling = DB::table('products')
                ->select(
                    'products.id',
                    'products.name',
                    'products.image',
                    'products.price',
                    'products.discount_price',
                    DB::raw('SUM(product_order_products.qty) as total_sold'),
                    DB::raw('SUM(product_order_products.total_price) as total_revenue')
                )
                ->join('product_order_products', 'products.id', '=', 'product_order_products.product_id')
                ->join('product_orders', 'product_order_products.product_order_id', '=', 'product_orders.id')
                ->where('products.status', 'active')
                ->where('product_orders.order_status', '!=', 'pending')
                ->where('product_orders.created_at', '>=', $startDate)
                ->groupBy('products.id', 'products.name', 'products.image', 'products.price', 'products.discount_price')
                ->orderBy('total_sold', 'desc')
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $topSelling
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

