<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Report\ReportController;

Route::group(['middleware' => ['auth', 'CheckUserType', 'DemoMode']], function () {

    // Main report page route
    Route::get('/app-report/{reportPath?}', [ReportController::class, 'index'])->name('report.index');

    // Product list for Select2 (AJAX)
    Route::get('/report-product-list', [ReportController::class, 'getProductList'])->name('report.product-list');

    // Data endpoints (AJAX for Vue)
    Route::get('/report/sales/data', [ReportController::class, 'getData'])->name('report.sales.data');
    Route::get('/report/sales-product-wise/data', [ReportController::class, 'getData'])->name('report.sales-product-wise.data');
    Route::get('/report/single-product-sale/data', [ReportController::class, 'getData'])->name('report.single-product-sale.data');
    Route::get('/report/ecommerce-order/data', [ReportController::class, 'getData'])->name('report.ecommerce-order.data');
    Route::get('/report/promotional-sales/data', [ReportController::class, 'getData'])->name('report.promotional-sales.data');
    Route::get('/report/salesman-sales/data', [ReportController::class, 'getData'])->name('report.salesman-sales.data');
    Route::get('/report/customer-sales/data', [ReportController::class, 'getData'])->name('report.customer-sales.data');
    Route::get('/report/location/data', [ReportController::class, 'getData'])->name('report.location.data');

    Route::get('/report/purchase/data', [ReportController::class, 'getData'])->name('report.purchase.data');
    Route::get('/report/purchase-product-wise/data', [ReportController::class, 'getData'])->name('report.purchase-product-wise.data');
    Route::get('/report/single-product-purchase/data', [ReportController::class, 'getData'])->name('report.single-product-purchase.data');
    Route::get('/report/supplier-due/data', [ReportController::class, 'getData'])->name('report.supplier-due.data');
    Route::get('/report/purchase-returns/data', [ReportController::class, 'getData'])->name('report.purchase-returns.data');

    Route::get('/report/in-stock/data', [ReportController::class, 'getData'])->name('report.in-stock.data');
    Route::get('/report/out-of-stock/data', [ReportController::class, 'getData'])->name('report.out-of-stock.data');
    Route::get('/report/low-stock/data', [ReportController::class, 'getData'])->name('report.low-stock.data');
    Route::get('/report/product-per-warehouse/data', [ReportController::class, 'getData'])->name('report.product-per-warehouse.data');
    Route::get('/report/monthly-stock-movement/data', [ReportController::class, 'getData'])->name('report.monthly-stock-movement.data');

    Route::get('/report/profit-loss/data', [ReportController::class, 'getData'])->name('report.profit-loss.data');
    Route::get('/report/account-head-wise/data', [ReportController::class, 'getData'])->name('report.account-head-wise.data');
    Route::get('/report/finance-dashboard/data', [ReportController::class, 'getData'])->name('report.finance-dashboard.data');
    Route::get('/report/expense-summary/data', [ReportController::class, 'getData'])->name('report.expense-summary.data');
    Route::get('/report/payment-collection/data', [ReportController::class, 'getData'])->name('report.payment-collection.data');

    Route::get('/report/due-customer/data', [ReportController::class, 'getData'])->name('report.due-customer.data');
    Route::get('/report/customers-advance/data', [ReportController::class, 'getData'])->name('report.customers-advance.data');
    Route::get('/report/sales-returns/data', [ReportController::class, 'getData'])->name('report.sales-returns.data');

    Route::get('/report/sales-target/data', [ReportController::class, 'getData'])->name('report.sales-target.data');

    // PDF Export routes
    Route::get('/report/sales/export/pdf', [ReportController::class, 'exportPdf'])->name('report.sales.export.pdf');
    Route::get('/report/sales-product-wise/export/pdf', [ReportController::class, 'exportPdf'])->name('report.sales-product-wise.export.pdf');
    Route::get('/report/single-product-sale/export/pdf', [ReportController::class, 'exportPdf'])->name('report.single-product-sale.export.pdf');
    Route::get('/report/ecommerce-order/export/pdf', [ReportController::class, 'exportPdf'])->name('report.ecommerce-order.export.pdf');
    Route::get('/report/promotional-sales/export/pdf', [ReportController::class, 'exportPdf'])->name('report.promotional-sales.export.pdf');
    Route::get('/report/salesman-sales/export/pdf', [ReportController::class, 'exportPdf'])->name('report.salesman-sales.export.pdf');
    Route::get('/report/customer-sales/export/pdf', [ReportController::class, 'exportPdf'])->name('report.customer-sales.export.pdf');
    Route::get('/report/location/export/pdf', [ReportController::class, 'exportPdf'])->name('report.location.export.pdf');

    Route::get('/report/purchase/export/pdf', [ReportController::class, 'exportPdf'])->name('report.purchase.export.pdf');
    Route::get('/report/purchase-product-wise/export/pdf', [ReportController::class, 'exportPdf'])->name('report.purchase-product-wise.export.pdf');
    Route::get('/report/single-product-purchase/export/pdf', [ReportController::class, 'exportPdf'])->name('report.single-product-purchase.export.pdf');
    Route::get('/report/supplier-due/export/pdf', [ReportController::class, 'exportPdf'])->name('report.supplier-due.export.pdf');
    Route::get('/report/purchase-returns/export/pdf', [ReportController::class, 'exportPdf'])->name('report.purchase-returns.export.pdf');

    Route::get('/report/in-stock/export/pdf', [ReportController::class, 'exportPdf'])->name('report.in-stock.export.pdf');
    Route::get('/report/out-of-stock/export/pdf', [ReportController::class, 'exportPdf'])->name('report.out-of-stock.export.pdf');
    Route::get('/report/low-stock/export/pdf', [ReportController::class, 'exportPdf'])->name('report.low-stock.export.pdf');
    Route::get('/report/product-per-warehouse/export/pdf', [ReportController::class, 'exportPdf'])->name('report.product-per-warehouse.export.pdf');
    Route::get('/report/monthly-stock-movement/export/pdf', [ReportController::class, 'exportPdf'])->name('report.monthly-stock-movement.export.pdf');

    Route::get('/report/profit-loss/export/pdf', [ReportController::class, 'exportPdf'])->name('report.profit-loss.export.pdf');
    Route::get('/report/account-head-wise/export/pdf', [ReportController::class, 'exportPdf'])->name('report.account-head-wise.export.pdf');
    Route::get('/report/finance-dashboard/export/pdf', [ReportController::class, 'exportPdf'])->name('report.finance-dashboard.export.pdf');
    Route::get('/report/expense-summary/export/pdf', [ReportController::class, 'exportPdf'])->name('report.expense-summary.export.pdf');
    Route::get('/report/payment-collection/export/pdf', [ReportController::class, 'exportPdf'])->name('report.payment-collection.export.pdf');

    Route::get('/report/due-customer/export/pdf', [ReportController::class, 'exportPdf'])->name('report.due-customer.export.pdf');
    Route::get('/report/customers-advance/export/pdf', [ReportController::class, 'exportPdf'])->name('report.customers-advance.export.pdf');
    Route::get('/report/sales-returns/export/pdf', [ReportController::class, 'exportPdf'])->name('report.sales-returns.export.pdf');

    Route::get('/report/sales-target/export/pdf', [ReportController::class, 'exportPdf'])->name('report.sales-target.export.pdf');

    // CSV Export routes
    Route::get('/report/sales/export/csv', [ReportController::class, 'exportCsv'])->name('report.sales.export.csv');
    Route::get('/report/sales-product-wise/export/csv', [ReportController::class, 'exportCsv'])->name('report.sales-product-wise.export.csv');
    Route::get('/report/single-product-sale/export/csv', [ReportController::class, 'exportCsv'])->name('report.single-product-sale.export.csv');
    Route::get('/report/ecommerce-order/export/csv', [ReportController::class, 'exportCsv'])->name('report.ecommerce-order.export.csv');
    Route::get('/report/promotional-sales/export/csv', [ReportController::class, 'exportCsv'])->name('report.promotional-sales.export.csv');
    Route::get('/report/salesman-sales/export/csv', [ReportController::class, 'exportCsv'])->name('report.salesman-sales.export.csv');
    Route::get('/report/customer-sales/export/csv', [ReportController::class, 'exportCsv'])->name('report.customer-sales.export.csv');
    Route::get('/report/location/export/csv', [ReportController::class, 'exportCsv'])->name('report.location.export.csv');

    Route::get('/report/purchase/export/csv', [ReportController::class, 'exportCsv'])->name('report.purchase.export.csv');
    Route::get('/report/purchase-product-wise/export/csv', [ReportController::class, 'exportCsv'])->name('report.purchase-product-wise.export.csv');
    Route::get('/report/single-product-purchase/export/csv', [ReportController::class, 'exportCsv'])->name('report.single-product-purchase.export.csv');
    Route::get('/report/supplier-due/export/csv', [ReportController::class, 'exportCsv'])->name('report.supplier-due.export.csv');
    Route::get('/report/purchase-returns/export/csv', [ReportController::class, 'exportCsv'])->name('report.purchase-returns.export.csv');

    Route::get('/report/in-stock/export/csv', [ReportController::class, 'exportCsv'])->name('report.in-stock.export.csv');
    Route::get('/report/out-of-stock/export/csv', [ReportController::class, 'exportCsv'])->name('report.out-of-stock.export.csv');
    Route::get('/report/low-stock/export/csv', [ReportController::class, 'exportCsv'])->name('report.low-stock.export.csv');
    Route::get('/report/product-per-warehouse/export/csv', [ReportController::class, 'exportCsv'])->name('report.product-per-warehouse.export.csv');
    Route::get('/report/monthly-stock-movement/export/csv', [ReportController::class, 'exportCsv'])->name('report.monthly-stock-movement.export.csv');

    Route::get('/report/profit-loss/export/csv', [ReportController::class, 'exportCsv'])->name('report.profit-loss.export.csv');
    Route::get('/report/account-head-wise/export/csv', [ReportController::class, 'exportCsv'])->name('report.account-head-wise.export.csv');
    Route::get('/report/expense-summary/export/csv', [ReportController::class, 'exportCsv'])->name('report.expense-summary.export.csv');
    Route::get('/report/payment-collection/export/csv', [ReportController::class, 'exportCsv'])->name('report.payment-collection.export.csv');

    Route::get('/report/due-customer/export/csv', [ReportController::class, 'exportCsv'])->name('report.due-customer.export.csv');
    Route::get('/report/customers-advance/export/csv', [ReportController::class, 'exportCsv'])->name('report.customers-advance.export.csv');
    Route::get('/report/sales-returns/export/csv', [ReportController::class, 'exportCsv'])->name('report.sales-returns.export.csv');

    Route::get('/report/sales-target/export/csv', [ReportController::class, 'exportCsv'])->name('report.sales-target.export.csv');

});
