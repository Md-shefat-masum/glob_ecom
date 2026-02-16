<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    /**
     * Main report page - returns Vue app view
     */
    public function index(?string $reportPath = null)
    {
        return view('backend.report.index', compact('reportPath'));
    }

    /**
     * Get product list for Select2 (AJAX)
     */
    public function getProductList(Request $request): JsonResponse
    {
        $search = $request->get('q', '');
        
        $products = Product::where(function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            })
            ->select('id', 'name')
            ->limit(10)
            ->get();

        $results = $products->map(function($product) {
            return [
                'id' => $product->id,
                'text' => $product->name,
            ];
        });

        return response()->json([
            'results' => $results,
        ]);
    }

    /**
     * Get report data (AJAX endpoint for Vue)
     */
    public function getData(Request $request): JsonResponse
    {
        // Extract report path from URL (e.g., '/report/sales/data' -> 'sales')
        $path = $request->path();
        $reportPath = null;
        
        // Pattern: /report/{report-path}/data
        if (preg_match('#^report/(.+?)/data$#', $path, $matches)) {
            $reportPath = $matches[1];
        }
        
        // Fallback: try route name (for routes without hyphens)
        if (!$reportPath) {
            $routeName = $request->route()->getName();
            if ($routeName) {
                $reportPath = str_replace(['report.', '.data'], '', $routeName);
                $reportPath = str_replace('.', '-', $reportPath);
            }
        }
        
        // Fallback: try route parameter or request
        if (!$reportPath) {
            $reportPath = $request->route('reportPath') ?? $request->get('report_path');
        }
        
        if (!$reportPath) {
            return response()->json(['success' => false, 'message' => 'Report path is required.'], 400);
        }

        try {
            $action = $this->getAction($reportPath);
            if (!$action) {
                return response()->json(['success' => false, 'message' => 'Report not found.'], 404);
            }

            $filters = $this->extractFilters($request);
            $result = $action->run($filters);

            return response()->json([
                'success' => true,
                'data' => $result['data'] ?? [],
                'summary' => $result['summary'] ?? [],
            ]);
        } catch (\Throwable $e) {
            Log::error('Report getData error', ['report' => $reportPath, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Export report as PDF
     */
    public function exportPdf(Request $request)
    {
        // Extract report path from URL (e.g., '/report/sales/export/pdf' -> 'sales')
        $path = $request->path();
        $reportPath = null;
        
        // Pattern: /report/{report-path}/export/pdf
        if (preg_match('#^report/(.+?)/export/pdf$#', $path, $matches)) {
            $reportPath = $matches[1];
        }
        
        // Fallback: try route name
        if (!$reportPath) {
            $routeName = $request->route()->getName();
            if ($routeName) {
                $reportPath = str_replace(['report.', '.export.pdf'], '', $routeName);
                $reportPath = str_replace('.', '-', $reportPath);
            }
        }
        
        if (!$reportPath) {
            $reportPath = $request->route('reportPath') ?? $request->get('report_path');
        }
        
        if (!$reportPath) {
            abort(400, 'Report path is required.');
        }

        try {
            $action = $this->getAction($reportPath);
            if (!$action) {
                abort(404, 'Report not found.');
            }

            $filters = $this->extractFilters($request);
            $result = $action->run($filters);
            $title = $action->getTitle();
            $filename = date('d-M-Y') . '-' . str_replace(' ', '-', $title) . '.pdf';

            // TODO: Implement PDF generation using DomPDF or Snappy
            // For now, return a placeholder response
            return response()->json([
                'success' => false,
                'message' => 'PDF export not yet implemented. Install PDF library first.',
            ], 501);
        } catch (\Throwable $e) {
            Log::error('Report exportPdf error', ['report' => $reportPath, 'error' => $e->getMessage()]);
            abort(500, $e->getMessage());
        }
    }

    /**
     * Export report as CSV
     */
    public function exportCsv(Request $request)
    {
        // Extract report path from URL (e.g., '/report/sales/export/csv' -> 'sales')
        $path = $request->path();
        $reportPath = null;
        
        // Pattern: /report/{report-path}/export/csv
        if (preg_match('#^report/(.+?)/export/csv$#', $path, $matches)) {
            $reportPath = $matches[1];
        }
        
        // Fallback: try route name
        if (!$reportPath) {
            $routeName = $request->route()->getName();
            if ($routeName) {
                $reportPath = str_replace(['report.', '.export.csv'], '', $routeName);
                $reportPath = str_replace('.', '-', $reportPath);
            }
        }
        
        if (!$reportPath) {
            $reportPath = $request->route('reportPath') ?? $request->get('report_path');
        }
        
        if (!$reportPath) {
            abort(400, 'Report path is required.');
        }

        try {
            $action = $this->getAction($reportPath);
            if (!$action) {
                abort(404, 'Report not found.');
            }

            $filters = $this->extractFilters($request);
            $result = $action->run($filters);
            $title = $action->getTitle();
            $filename = date('d-M-Y') . '-' . str_replace(' ', '-', $title) . '.csv';

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $callback = function() use ($action, $result, $reportPath) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $action->getCsvHeaders());
                
                // Handle single-product-sale report differently
                if ($reportPath === 'single-product-sale') {
                    $dataToFormat = [
                        'sales_data' => $result['sales_data'] ?? [],
                        'purchase_data' => $result['purchase_data'] ?? [],
                    ];
                } else {
                    $dataToFormat = $result['data'] ?? [];
                }
                
                foreach ($action->formatForCsv($dataToFormat) as $row) {
                    fputcsv($file, $row);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Throwable $e) {
            Log::error('Report exportCsv error', ['report' => $reportPath, 'error' => $e->getMessage()]);
            abort(500, $e->getMessage());
        }
    }

    /**
     * Get Action instance for report path
     */
    private function getAction(string $reportPath): ?Actions\ReportAction
    {
        $actionMap = [
            'sales' => Actions\SalesReportAction::class,
            'sales-product-wise' => Actions\SalesProductWiseReportAction::class,
            'single-product-sale' => Actions\SingleProductSaleReportAction::class,
            'ecommerce-order' => Actions\EcommerceOrderReportAction::class,
            'promotional-sales' => Actions\PromotionalSalesReportAction::class,
            'salesman-sales' => Actions\SalesmanSalesReportAction::class,
            'customer-sales' => Actions\CustomerSalesReportAction::class,
            'location' => Actions\LocationReportAction::class,
            'purchase' => Actions\PurchaseReportAction::class,
            'purchase-product-wise' => Actions\PurchaseProductWiseReportAction::class,
            'single-product-purchase' => Actions\SingleProductPurchaseReportAction::class,
            'supplier-due' => Actions\SupplierDueReportAction::class,
            'purchase-returns' => Actions\PurchaseReturnsReportAction::class,
            'in-stock' => Actions\InStockReportAction::class,
            'out-of-stock' => Actions\OutOfStockReportAction::class,
            'low-stock' => Actions\LowStockReportAction::class,
            'product-per-warehouse' => Actions\ProductPerWarehouseReportAction::class,
            'monthly-stock-movement' => Actions\MonthlyStockMovementReportAction::class,
            'profit-loss' => Actions\ProfitLossReportAction::class,
            'account-head-wise' => Actions\AccountHeadWiseReportAction::class,
            'finance-dashboard' => Actions\FinanceDashboardReportAction::class,
            'expense-summary' => Actions\ExpenseSummaryAction::class,
            'payment-collection' => Actions\PaymentCollectionAction::class,
            'due-customer' => Actions\DueCustomerReportAction::class,
            'customers-advance' => Actions\CustomersAdvanceReportAction::class,
            'sales-returns' => Actions\SalesReturnsReportAction::class,
            'sales-target' => Actions\SalesTargetReportAction::class,
        ];

        $actionClass = $actionMap[$reportPath] ?? null;
        return $actionClass ? new $actionClass() : null;
    }

    /**
     * Extract filters from request
     */
    private function extractFilters(Request $request): array
    {
        return [
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
            'warehouse_id' => $request->get('warehouse_id'),
            'supplier_id' => $request->get('supplier_id'),
            'customer_id' => $request->get('customer_id'),
            'product_id' => $request->get('product_id'),
            'order_status' => $request->get('order_status'),
            'order_source' => $request->get('order_source'),
            'coupon_code' => $request->get('coupon_code'),
            'user_id' => $request->get('user_id'),
            'category_id' => $request->get('category_id'),
            'threshold' => $request->get('threshold', 10), // for low stock
            'account_id' => $request->get('account_id'),
            'month' => $request->get('month'),
            'year' => $request->get('year'),
        ];
    }
}
