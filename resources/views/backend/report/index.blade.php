@extends('backend.master')

@section('header_css')
    <link href="{{ versioned_url('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .report-container {
            display: flex;
            gap: 1rem;
            height: calc(100vh - 200px);
            min-height: 600px;
        }

        .report-sidebar {
            width: 280px;
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 1rem;
            overflow-y: auto;
        }

        .report-content {
            flex: 1;
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 1.5rem;
            overflow-y: auto;
        }

        .report-group {
            margin-bottom: 1.5rem;
        }

        .report-group-title {
            font-weight: 700;
            font-size: 0.875rem;
            text-transform: uppercase;
            color: #6c757d;
            margin-bottom: 0.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e9ecef;
        }

        .report-link {
            display: block;
            padding: 0.625rem 0.75rem;
            margin-bottom: 0.25rem;
            color: #495057;
            text-decoration: none;
            border-radius: 4px;
            transition: all 0.2s;
            font-size: 0.9rem;
        }

        .report-link:hover {
            background: #f8f9fa;
            color: #5369f8;
            text-decoration: none;
        }

        .report-link.active {
            background: #5369f8;
            color: #fff;
        }

        .filter-section {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
        }

        .filter-row {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 0.75rem;
        }

        .filter-row:last-child {
            margin-bottom: 0;
        }

        .filter-group {
            flex: 1;
            min-width: 200px;
        }

        .filter-group label {
            font-weight: 600;
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
            display: block;
        }

        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .summary-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: 1.25rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .summary-card h6 {
            font-size: 0.75rem;
            text-transform: uppercase;
            opacity: 0.9;
            margin-bottom: 0.5rem;
        }

        .summary-card .value {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
        }

        .report-table th {
            background: #f8f9fa;
            padding: 0.75rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.875rem;
            border-bottom: 2px solid #dee2e6;
        }

        .report-table td {
            padding: 0.75rem;
            border-bottom: 1px solid #e9ecef;
        }

        .report-table tr:hover {
            background: #f8f9fa;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .loading-spinner {
            text-align: center;
            padding: 3rem;
        }

        .export-buttons {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .report-table tfoot tr {
            background: #f8f9fa !important;
            font-weight: bold;
            border-top: 2px solid #dee2e6;
        }

        .report-table tfoot td {
            padding: 0.75rem;
            border-top: 2px solid #dee2e6;
        }
    </style>
@endsection

@section('page_title')
    Reports
@endsection

@section('page_heading')
    Reports
@endsection

@section('content')
<div id="reportApp">
    <div class="report-container">
        <!-- Left Sidebar: Report Groups -->
        <div class="report-sidebar">
            <h5 class="mb-3">Report Groups</h5>
            
            <div class="report-group">
                <div class="report-group-title">Sales & Revenue</div>
                <a href="#" class="report-link" :class="{ active: currentReport === 'sales' }" @click.prevent="loadReport('sales')">Sales Report</a>
                <a href="#" class="report-link" :class="{ active: currentReport === 'sales-product-wise' }" @click.prevent="loadReport('sales-product-wise')">Sales Product Wise</a>
                <a href="#" class="report-link" :class="{ active: currentReport === 'single-product-sale' }" @click.prevent="loadReport('single-product-sale')">Single Product Sale</a>
                <a href="#" class="report-link" :class="{ active: currentReport === 'ecommerce-order' }" @click.prevent="loadReport('ecommerce-order')">Ecommerce Order</a>
                <a href="#" class="report-link" :class="{ active: currentReport === 'promotional-sales' }" @click.prevent="loadReport('promotional-sales')">Promotional Sales</a>
                <a href="#" class="report-link" :class="{ active: currentReport === 'salesman-sales' }" @click.prevent="loadReport('salesman-sales')">Salesman Sales</a>
                <a href="#" class="report-link" :class="{ active: currentReport === 'customer-sales' }" @click.prevent="loadReport('customer-sales')">Customer Sales</a>
                <a href="#" class="report-link" :class="{ active: currentReport === 'location' }" @click.prevent="loadReport('location')">Location Report</a>
            </div>

            <div class="report-group">
                <div class="report-group-title">Purchase & Procurement</div>
                <a href="#" class="report-link" :class="{ active: currentReport === 'purchase' }" @click.prevent="loadReport('purchase')">Purchase Report</a>
                <a href="#" class="report-link" :class="{ active: currentReport === 'purchase-product-wise' }" @click.prevent="loadReport('purchase-product-wise')">Purchase Product Wise</a>
                <a href="#" class="report-link" :class="{ active: currentReport === 'single-product-purchase' }" @click.prevent="loadReport('single-product-purchase')">Single Product Purchase</a>
                <a href="#" class="report-link" :class="{ active: currentReport === 'supplier-due' }" @click.prevent="loadReport('supplier-due')">Supplier Due</a>
                <a href="#" class="report-link" :class="{ active: currentReport === 'purchase-returns' }" @click.prevent="loadReport('purchase-returns')">Purchase Returns</a>
            </div>

            <div class="report-group">
                <div class="report-group-title">Inventory & Stock</div>
                <a href="#" class="report-link" :class="{ active: currentReport === 'in-stock' }" @click.prevent="loadReport('in-stock')">In Stock</a>
                <a href="#" class="report-link" :class="{ active: currentReport === 'out-of-stock' }" @click.prevent="loadReport('out-of-stock')">Out of Stock</a>
                <a href="#" class="report-link" :class="{ active: currentReport === 'low-stock' }" @click.prevent="loadReport('low-stock')">Low Stock</a>
                <a href="#" class="report-link" :class="{ active: currentReport === 'product-per-warehouse' }" @click.prevent="loadReport('product-per-warehouse')">Product Per Warehouse</a>
                <a href="#" class="report-link" :class="{ active: currentReport === 'monthly-stock-movement' }" @click.prevent="loadReport('monthly-stock-movement')">Monthly Stock Movement</a>
            </div>

            <div class="report-group">
                <div class="report-group-title">Financial & Accounting</div>
                <a href="#" class="report-link" :class="{ active: currentReport === 'profit-loss' }" @click.prevent="loadReport('profit-loss')">Profit Loss</a>
                <a href="#" class="report-link" :class="{ active: currentReport === 'account-head-wise' }" @click.prevent="loadReport('account-head-wise')">Account Head Wise</a>
                <a href="#" class="report-link" :class="{ active: currentReport === 'finance-dashboard' }" @click.prevent="loadReport('finance-dashboard')">Finance Dashboard</a>
                <a href="#" class="report-link" :class="{ active: currentReport === 'expense-summary' }" @click.prevent="loadReport('expense-summary')">Expense Summary</a>
                <a href="#" class="report-link" :class="{ active: currentReport === 'payment-collection' }" @click.prevent="loadReport('payment-collection')">Payment Collection</a>
            </div>

            <div class="report-group">
                <div class="report-group-title">Customer & Supplier</div>
                <a href="#" class="report-link" :class="{ active: currentReport === 'due-customer' }" @click.prevent="loadReport('due-customer')">Due Customer</a>
                <a href="#" class="report-link" :class="{ active: currentReport === 'customers-advance' }" @click.prevent="loadReport('customers-advance')">Customers Advance</a>
                <a href="#" class="report-link" :class="{ active: currentReport === 'sales-returns' }" @click.prevent="loadReport('sales-returns')">Sales Returns</a>
            </div>

            <div class="report-group">
                <div class="report-group-title">Performance & Target</div>
                <a href="#" class="report-link" :class="{ active: currentReport === 'sales-target' }" @click.prevent="loadReport('sales-target')">Sales Target</a>
            </div>
        </div>

        <!-- Right Content Area -->
        <div class="report-content">
            <!-- Empty State -->
            <div v-if="!currentReport" class="empty-state">
                <i class="feather-file-text"></i>
                <h5>Select a Report</h5>
                <p class="text-muted">Choose a report from the left sidebar to view data.</p>
            </div>

            <!-- Loading State -->
            <div v-else-if="loading" class="loading-spinner">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-2">Loading report data...</p>
            </div>

            <!-- Report Content -->
            <div v-else>
                <!-- Report Title & Export Buttons -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>@{{ reportTitle }}</h4>
                    <div class="export-buttons">
                        <button class="btn btn-sm btn-outline-danger" @click="exportPdf" :disabled="!reportData || (currentReport === 'single-product-sale' ? (!salesData || salesData.length === 0) : (!reportData || reportData.length === 0))">
                            <i class="feather-download"></i> Export PDF
                        </button>
                        <button class="btn btn-sm btn-outline-success" @click="exportCsv" :disabled="!reportData || (currentReport === 'single-product-sale' ? (!salesData || salesData.length === 0) : (!reportData || reportData.length === 0))">
                            <i class="feather-download"></i> Export CSV
                        </button>
                    </div>
                </div>

                <!-- Single Product Sale Report - Product Selector -->
                <div v-if="currentReport === 'single-product-sale'" class="mb-4">
                    <div class="form-group">
                        <label class="font-weight-bold">Select Product</label>
                        <select id="product-select" class="form-control" style="width: 100%;">
                            <option value="">-- Select Product --</option>
                        </select>
                    </div>
                </div>

                <!-- Single Product Sale Report - Summary Metrics -->
                <div v-if="currentReport === 'single-product-sale' && productSummary" class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Product Summary</h5>
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong>Total Sold:</strong> @{{ formatNumber(productSummary.total_sold) }}
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Total Purchased:</strong> @{{ formatNumber(productSummary.total_purchased) }}
                                    </div>
                                    <div class="col-md-2">
                                        <strong>Sold Qty:</strong> @{{ productSummary.sold_qty }}
                                    </div>
                                    <div class="col-md-2">
                                        <strong>Purchase Value:</strong> @{{ formatNumber(productSummary.purchase_value) }}
                                    </div>
                                    <div class="col-md-2" :class="productSummary.is_profit ? 'text-success' : 'text-danger'">
                                        <strong>Profit/Loss:</strong> @{{ formatNumber(productSummary.profit_loss) }}
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-12">
                                        <strong>Total Discounts:</strong> @{{ formatNumber(productSummary.total_discounts) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div v-if="filtersConfig.length > 0" class="filter-section">
                    <h6 class="mb-3">Filters</h6>
                    <div class="filter-row" v-for="(row, rowIndex) in filterRows" :key="rowIndex">
                        <div class="filter-group" v-for="filter in row" :key="filter.name">
                            <label v-if="filter.label">@{{ filter.label }} <span v-if="filter.required" class="text-danger">*</span></label>
                            
                            <!-- Date Input -->
                            <input 
                                v-if="filter.type === 'date'" 
                                type="date" 
                                v-model="filters[filter.name]"
                                class="form-control form-control-sm"
                                :required="filter.required"
                            />
                            
                            <!-- Number Input -->
                            <input 
                                v-else-if="filter.type === 'number'" 
                                type="number" 
                                v-model.number="filters[filter.name]"
                                class="form-control form-control-sm"
                                :placeholder="filter.default"
                                :required="filter.required"
                            />
                            
                            <!-- Text Input -->
                            <input 
                                v-else-if="filter.type === 'text'" 
                                type="text" 
                                v-model="filters[filter.name]"
                                class="form-control form-control-sm"
                                :required="filter.required"
                            />
                            
                            <!-- Select Dropdown -->
                            <select 
                                v-else-if="filter.type === 'select'" 
                                v-model="filters[filter.name]"
                                class="form-control form-control-sm"
                                :required="filter.required"
                            >
                                <option value="">-- Select --</option>
                                <option v-for="opt in filter.options" :key="opt.value" :value="opt.value">@{{ opt.label }}</option>
                            </select>
                            
                            <!-- Checkbox -->
                            <div v-else-if="filter.type === 'checkbox'" class="form-check">
                                <input 
                                    type="checkbox" 
                                    v-model="filters[filter.name]"
                                    class="form-check-input"
                                    :id="'filter-' + filter.name"
                                />
                                <label class="form-check-label" :for="'filter-' + filter.name">@{{ filter.label }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-primary btn-sm" @click="applyFilters">
                            <i class="feather-filter"></i> Apply Filters
                        </button>
                        <button class="btn btn-secondary btn-sm ml-2" @click="resetFilters">
                            <i class="feather-refresh-cw"></i> Reset
                        </button>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div v-if="summary && Object.keys(summary).length > 0" class="summary-cards">
                    <div class="summary-card" v-for="(value, key) in summary" :key="key">
                        <h6>@{{ formatKey(key) }}</h6>
                        <div class="value">@{{ formatValue(value) }}</div>
                    </div>
                </div>

                <!-- Single Product Sale Report - Two Column Layout -->
                <div v-if="currentReport === 'single-product-sale' && (salesData || purchaseData)" class="row">
                    <!-- Sales List -->
                    <div class="col-lg-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Sales List</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="report-table">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Qty</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(row, index) in salesData" :key="index">
                                                <td>@{{ row.date }}</td>
                                                <td>@{{ row.qty }}</td>
                                                <td>@{{ formatNumber(row.total) }}</td>
                                            </tr>
                                        </tbody>
                                        <tfoot v-if="salesData && salesData.length > 0">
                                            <tr style="background: #f8f9fa; font-weight: bold;">
                                                <td style="text-align: right;">Total:</td>
                                                <td>@{{ getTotalQty('sales') }}</td>
                                                <td>@{{ formatNumber(getTotalAmount('sales')) }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Purchase List -->
                    <div class="col-lg-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Purchase List</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="report-table">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Qty</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(row, index) in purchaseData" :key="index">
                                                <td>@{{ row.date }}</td>
                                                <td>@{{ row.qty }}</td>
                                                <td>@{{ formatNumber(row.total) }}</td>
                                            </tr>
                                        </tbody>
                                        <tfoot v-if="purchaseData && purchaseData.length > 0">
                                            <tr style="background: #f8f9fa; font-weight: bold;">
                                                <td style="text-align: right;">Total:</td>
                                                <td>@{{ getTotalQty('purchase') }}</td>
                                                <td>@{{ formatNumber(getTotalAmount('purchase')) }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Regular Report Table (for other reports) -->
                <div v-else-if="reportData && reportData.length > 0" class="table-responsive">
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th v-for="(header, index) in tableHeaders" :key="index">@{{ header }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(row, index) in reportData" :key="index">
                                <td v-for="(cell, cellIndex) in formatRow(row)" :key="cellIndex">@{{ cell }}</td>
                            </tr>
                        </tbody>
                        <!-- Footer Totals (for sales report) -->
                        <tfoot v-if="currentReport === 'sales' && reportData.length > 0">
                            <tr style="background: #f8f9fa; font-weight: bold;">
                                <td colspan="4" style="text-align: right;">Total:</td>
                                <td>@{{ formatNumber(getTotal('grand_total')) }}</td>
                                <td>@{{ formatNumber(getTotal('paid')) }}</td>
                                <td>@{{ formatNumber(getTotal('due')) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Empty Data State -->
                <div v-else-if="currentReport === 'single-product-sale' && (!salesData || salesData.length === 0) && (!purchaseData || purchaseData.length === 0)" class="empty-state">
                    <i class="feather-inbox"></i>
                    <h5>No Data Found</h5>
                    <p class="text-muted" v-if="!filters.product_id">Please select a product to view data.</p>
                    <p class="text-muted" v-else>No records match your filters. Try adjusting your criteria.</p>
                </div>
                <div v-else-if="currentReport !== 'single-product-sale' && reportData && reportData.length === 0" class="empty-state">
                    <i class="feather-inbox"></i>
                    <h5>No Data Found</h5>
                    <p class="text-muted">No records match your filters. Try adjusting your criteria.</p>
                </div>

                <!-- Error State -->
                <div v-if="error" class="alert alert-danger">
                    <i class="feather-alert-circle"></i> @{{ error }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer_js')
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="{{ versioned_url('assets/plugins/select2/select2.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
            }

            new Vue({
                el: '#reportApp',
                data: {
                    currentReport: null,
                    reportTitle: '',
                    reportData: null,
                    salesData: null,
                    purchaseData: null,
                    productSummary: null,
                    summary: null,
                    filters: {},
                    filtersConfig: [],
                    loading: false,
                    error: null
                },
                computed: {
                    filterRows() {
                        // Group filters into rows of 3
                        const rows = [];
                        for (let i = 0; i < this.filtersConfig.length; i += 3) {
                            rows.push(this.filtersConfig.slice(i, i + 3));
                        }
                        return rows;
                    },
                    tableHeaders() {
                        if (!this.reportData || this.reportData.length === 0) return [];
                        
                        // For sales report, use specific column names
                        if (this.currentReport === 'sales') {
                            return ['Date', 'Order Code', 'Customer Name', 'Phone', 'Grand Total', 'Paid', 'Due'];
                        }
                        
                        // For other reports, extract headers from first row keys
                        const keys = Object.keys(this.reportData[0]);
                        return keys.map(key => {
                            // Format key to readable header
                            return key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                        });
                    }
                },
                mounted() {
                    // Expose Vue instance globally for Select2 callbacks
                    window.report_vue = this;
                    
                    // Load report from URL if provided
                    const urlPath = window.location.pathname;
                    const match = urlPath.match(/\/app-report\/(.+)/);
                    if (match && match[1]) {
                        this.loadReport(match[1]);
                    }
                },
                methods: {
                    loadReport(reportPath) {
                        this.currentReport = reportPath;
                        this.reportData = null;
                        this.salesData = null;
                        this.purchaseData = null;
                        this.productSummary = null;
                        this.summary = null;
                        this.error = null;
                        this.filters = {};
                        this.filtersConfig = [];
                        
                        // Update URL without reload
                        window.history.pushState({}, '', `/app-report/${reportPath}`);
                        
                        // Initialize Select2 for single product sale report (with delay)
                        if (reportPath === 'single-product-sale') {
                            setTimeout(() => {
                                this.initProductSelect2();
                            }, 1000);
                        } else {
                            // Destroy Select2 if switching away from single-product-sale
                            this.destroyProductSelect2();
                        }
                        
                        // Load filters config (we'll get it from the first data load)
                        // Don't auto-fetch for single-product-sale until product is selected
                        if (reportPath !== 'single-product-sale') {
                            this.fetchReportData();
                        }
                    },
                    setProductId(productId) {
                        this.filters.product_id = productId;
                    },
                    fetchData() {
                        this.fetchReportData();
                    },
                    destroyProductSelect2() {
                        const selectElement = document.getElementById('product-select');
                        if (selectElement) {
                            try {
                                // Check if Select2 is initialized
                                if ($(selectElement).hasClass('select2-hidden-accessible')) {
                                    $(selectElement).select2('destroy');
                                }
                            } catch (e) {
                                console.log('Select2 destroy error:', e);
                            }
                        }
                    },
                    fetchReportData() {
                        if (!this.currentReport) return;
                        
                        this.loading = true;
                        this.error = null;
                        
                        const routeName = `report.${this.currentReport.replace(/-/g, '.')}.data`;
                        const url = `/report/${this.currentReport}/data`;
                        
                        axios.get(url, { params: this.filters })
                            .then(res => {
                                if (res.data.success) {
                                    // Handle single product sale report differently
                                    if (this.currentReport === 'single-product-sale') {
                                        this.salesData = res.data.data?.sales_data || [];
                                        this.purchaseData = res.data.data?.purchase_data || [];
                                        this.productSummary = res.data.data?.summary || null;
                                        this.reportData = null; // Clear regular report data
                                        
                                        // Ensure Select2 is still initialized after data load (with delay)
                                        setTimeout(() => {
                                            const selectElement = document.getElementById('product-select');
                                            if (selectElement && !$(selectElement).hasClass('select2-hidden-accessible')) {
                                                this.initProductSelect2();
                                            }
                                        }, 1000);
                                    } else {
                                        this.reportData = res.data.data || [];
                                        this.salesData = null;
                                        this.purchaseData = null;
                                        this.productSummary = null;
                                    }
                                    this.summary = res.data.summary || {};
                                    this.reportTitle = this.getReportTitle(this.currentReport);
                                    
                                    // Set default filters config if not set
                                    if (this.filtersConfig.length === 0) {
                                        this.setDefaultFilters();
                                    }
                                } else {
                                    this.error = res.data.message || 'Failed to load report data.';
                                }
                            })
                            .catch(err => {
                                this.error = err.response?.data?.message || 'An error occurred while loading the report.';
                                console.error('Report error:', err);
                            })
                            .finally(() => {
                                this.loading = false;
                            });
                    },
                    setDefaultFilters() {
                        // Default filters for most reports
                        this.filtersConfig = [
                            {
                                type: 'date',
                                name: 'date_from',
                                label: 'From Date',
                                required: true
                            },
                            {
                                type: 'date',
                                name: 'date_to',
                                label: 'To Date',
                                required: true
                            }
                        ];
                        
                        // Set default values
                        const today = new Date();
                        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
                        this.filters.date_from = firstDay.toISOString().split('T')[0];
                        this.filters.date_to = today.toISOString().split('T')[0];
                    },
                    applyFilters() {
                        this.fetchReportData();
                    },
                    resetFilters() {
                        this.filters = {};
                        this.setDefaultFilters();
                        this.fetchReportData();
                    },
                    exportPdf() {
                        if (!this.currentReport) return;
                        const url = `/report/${this.currentReport}/export/pdf?` + new URLSearchParams(this.filters).toString();
                        window.open(url, '_blank');
                    },
                    exportCsv() {
                        if (!this.currentReport) return;
                        const url = `/report/${this.currentReport}/export/csv?` + new URLSearchParams(this.filters).toString();
                        window.open(url, '_blank');
                    },
                    getReportTitle(path) {
                        const titles = {
                            'sales': 'Sales Report',
                            'sales-product-wise': 'Sales Product Wise Report',
                            'single-product-sale': 'Single Product Sale Report',
                            'ecommerce-order': 'Ecommerce Order Report',
                            'promotional-sales': 'Promotional Sales Report',
                            'salesman-sales': 'Salesman Sales Report',
                            'customer-sales': 'Customer Sales Report',
                            'location': 'Location Report',
                            'purchase': 'Purchase Report',
                            'purchase-product-wise': 'Purchase Product Wise Report',
                            'single-product-purchase': 'Single Product Purchase Report',
                            'supplier-due': 'Supplier Due Report',
                            'purchase-returns': 'Purchase Returns Report',
                            'in-stock': 'In Stock Report',
                            'out-of-stock': 'Out of Stock Report',
                            'low-stock': 'Low Stock Report',
                            'product-per-warehouse': 'Product Per Warehouse Report',
                            'monthly-stock-movement': 'Monthly Stock Movement Report',
                            'profit-loss': 'Profit Loss Report',
                            'account-head-wise': 'Account Head Wise Report',
                            'finance-dashboard': 'Finance Dashboard',
                            'expense-summary': 'Expense Summary Report',
                            'payment-collection': 'Payment Collection Report',
                            'due-customer': 'Due Customer Report',
                            'customers-advance': 'Customers Advance Report',
                            'sales-returns': 'Sales Returns Report',
                            'sales-target': 'Sales Target Report'
                        };
                        return titles[path] || path.replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                    },
                    formatKey(key) {
                        return key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                    },
                    formatValue(value) {
                        if (typeof value === 'number') {
                            return value.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        }
                        return value;
                    },
                    formatRow(row) {
                        // For sales report, maintain specific column order
                        if (this.currentReport === 'sales') {
                            const order = ['date', 'order_code', 'customer_name', 'phone', 'grand_total', 'paid', 'due'];
                            return order.map(key => {
                                const val = row[key];
                                if (typeof val === 'number') {
                                    return val.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                }
                                if (val === null || val === undefined) return '';
                                return String(val);
                            });
                        }
                        
                        // For other reports, convert object to array of values
                        return Object.values(row).map(val => {
                            if (typeof val === 'number') {
                                return val.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            }
                            if (val === null || val === undefined) return '';
                            return String(val);
                        });
                    },
                    getTotal(field) {
                        if (!this.reportData || this.reportData.length === 0) return 0;
                        return this.reportData.reduce((sum, row) => {
                            return sum + (parseFloat(row[field]) || 0);
                        }, 0);
                    },
                    getTotalQty(type) {
                        const data = type === 'sales' ? this.salesData : this.purchaseData;
                        if (!data || data.length === 0) return 0;
                        return data.reduce((sum, row) => {
                            return sum + (parseInt(row.qty) || 0);
                        }, 0);
                    },
                    getTotalAmount(type) {
                        const data = type === 'sales' ? this.salesData : this.purchaseData;
                        if (!data || data.length === 0) return 0;
                        return data.reduce((sum, row) => {
                            return sum + (parseFloat(row.total) || 0);
                        }, 0);
                    },
                    formatNumber(value) {
                        if (typeof value === 'number') {
                            return value.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        }
                        return value;
                    },
                    initProductSelect2() {
                        // Wait for DOM to be ready with delay
                        setTimeout(() => {
                            const selectElement = document.getElementById('product-select');
                            if (!selectElement) {
                                console.log('Product select element not found');
                                return;
                            }
                            
                            // Check if Select2 is already initialized
                            if ($(selectElement).hasClass('select2-hidden-accessible')) {
                                console.log('Select2 already initialized, skipping...');
                                return;
                            }
                            
                            // Destroy existing Select2 if any (safety check)
                            try {
                                this.destroyProductSelect2();
                            } catch (e) {
                                console.log('Error destroying Select2:', e);
                            }
                            
                            // Initialize Select2 using vanilla JS approach
                            $(selectElement).select2({
                                placeholder: 'Search and select a product...',
                                allowClear: true,
                                ajax: {
                                    url: '/report-product-list',
                                    dataType: 'json',
                                    delay: 250,
                                    data: function (params) {
                                        return {
                                            q: params.term,
                                            page: params.page || 1
                                        };
                                    },
                                    processResults: function (data) {
                                        return {
                                            results: data.results || []
                                        };
                                    },
                                    cache: true
                                },
                                minimumInputLength: 0
                            });

                            // Track selected event using vanilla JS
                            $(selectElement).off('select2:select select2:clear'); // Remove existing handlers
                            $(selectElement).on('select2:select', function (e) {
                                const data = e.params.data;
                                if (window.report_vue) {
                                    window.report_vue.setProductId(data.id);
                                    window.report_vue.fetchData();
                                }
                            });

                            $(selectElement).on('select2:clear', function () {
                                if (window.report_vue) {
                                    window.report_vue.setProductId(null);
                                    window.report_vue.salesData = null;
                                    window.report_vue.purchaseData = null;
                                    window.report_vue.productSummary = null;
                                }
                            });
                        }, 1000);
                    }
                }
            });
        });
    </script>
@endsection
