@extends('backend.master')

@section('header_css')
@endsection

@section('page_title')
    Dashboard
@endsection

@section('page_heading')
    Overview
@endsection

@section('content')
    <!-- Dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
    <style>
        .kpi_icon {
            --color: attr(data-bg type(<color>));
            background: color-mix(in srgb, var(--color), transparent 70%);

            i {
                background: var(--color);
                color: white;
            }
        }
    </style>

    <div class="container mt-3 dashboard_container">
        <!-- Topbar -->
        <div class="d-flex align-items-center justify-content-between topbar">
            <h4 class="mb-0">Analytics Dashboard</h4>
            <div class="range-box d-flex align-items-center">
                <label class="mr-2 mb-0 stat-label">From</label>
                <input id="fromDate" type="date" class="form-control form-control-sm mr-2">
                <label class="mr-2 mb-0 stat-label">To</label>
                <input id="toDate" type="date" class="form-control form-control-sm mr-3">
                <button id="applyRange" type="button" class="btn btn-sm btn-primary">Apply</button>
            </div>
        </div>

        <!-- Row 1: Today's Order Analytics (full-width section) -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="mb-0">Today's Order Analytics</h5>
                    <small class="text-muted">Showing analytics for: <span id="rangeText"></span></small>
                </div>

                <div class="top_analytics_wrapper">
                    <div class="analytics_card_container">
                        <!-- Cards for opening stock, sales, returns, waste, purchase, close stock -->

                        <div class="card card-analytics">
                            <div class="kpi">
                                <div class="left">
                                    <div class="kpi_icon" data-bg="green">
                                        <i class="fas fa-box-open"></i>
                                    </div>
                                    <div class="kpi_content">
                                        <div class="stat-label">Opening Stock</div>
                                        <div class="stat-value" id="openingStock">0</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card card-analytics">
                            <div class="kpi">
                                <div class="left">
                                    <div class="kpi_icon" data-bg="blue">
                                        <i class="fas fa-shopping-cart"></i>
                                    </div>
                                    <div class="kpi_content">
                                        <div class="stat-label">Sales (Qty)</div>
                                        <div class="stat-value" id="salesQty">0</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card card-analytics">
                            <div class="kpi">
                                <div class="left">
                                    <div class="kpi_icon" data-bg="red">
                                        <i class="fas fa-undo-alt"></i>
                                    </div>
                                    <div class="kpi_content">
                                        <div class="stat-label">Return Value</div>
                                        <div class="stat-value" id="returns">0</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card card-analytics">
                            <div class="kpi">
                                <div class="left">
                                    <div class="kpi_icon" data-bg="teal">
                                        <i class="fas fa-recycle"></i>
                                    </div>
                                    <div class="kpi_content">
                                        <div class="stat-label">Stock Out</div>
                                        <div class="stat-value" id="stockOut">0</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card card-analytics">
                            <div class="kpi">
                                <div class="left">
                                    <div class="kpi_icon" data-bg="purple">
                                        <i class="fas fa-truck-loading"></i>
                                    </div>
                                    <div class="kpi_content">
                                        <div class="stat-label">Purchase Spend</div>
                                        <div class="stat-value" id="purchaseSpend">0</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card card-analytics">
                            <div class="kpi">
                                <div class="left">
                                    <div class="kpi_icon" data-bg="orange">
                                        <i class="fas fa-warehouse"></i>
                                    </div>
                                    <div class="kpi_content">
                                        <div class="stat-label">Closing Stock</div>
                                        <div class="stat-value" id="closingStock">0</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="financials_container">
                            {{-- <h5 class="mb-3">Financials (Selected Period)</h5> --}}
                            <div class="financials_wrapper">
                                <div class="financials_item">
                                    <div class="stat-label">Gross Income</div>
                                    <div class="stat-value" id="grossIncome">৳0</div>
                                </div>
                                <div class="financials_item">
                                    <div class="stat-label">Purchase Spend</div>
                                    <div class="stat-value" id="financePurchaseSpend">৳0</div>
                                </div>
                                <div class="financials_item">
                                    <div class="stat-label">Operational Expense</div>
                                    <div class="stat-value" id="operationalExpense">৳0</div>
                                </div>
                                <div class="financials_item">
                                    <div class="stat-label">Net Profit / Loss</div>
                                    <div class="stat-value" id="netProfit">৳0</div>
                                    {{-- <small class="text-muted">After purchases & expenses</small> --}}
                                </div>
                                <div class="financials_item">
                                    <div class="stat-label">Receivables Outstanding</div>
                                    <div class="stat-value" id="receivablesDue">৳0</div>
                                    {{-- <small class="text-muted">Customer dues</small> --}}
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="analytics_card_container analytics_card_container_v2">
                        <div class="card card-analytics analytics_card_item">
                            <div class="stat-label">Total Orders</div>
                            <div class="stat-value" id="totalOrders">0</div>
                            <small class="text-muted">POS vs eCommerce: <span id="posVsEcom">0 / 0</span></small>
                        </div>

                        <div class="card card-analytics analytics_card_item">
                            <div class="stat-label">Avg Order Value</div>
                            <div class="stat-value" id="avgOrderValue">৳0</div>
                            <small class="text-muted">(based on sales)</small>
                        </div>

                        <div class="card card-analytics analytics_card_item">
                            <div class="stat-label">Return Rate</div>
                            <div class="stat-value" id="returnRate">0%</div>
                            <small class="text-muted">Sales returns vs orders</small>
                        </div>

                        <div class="card card-analytics analytics_card_item">
                            <div class="stat-label">Repeat Purchase Rate</div>
                            <div class="stat-value" id="repeatRate">0%</div>
                            <small class="text-muted">Customers returning in range</small>
                        </div>

                        <div class="card card-analytics analytics_card_item">
                            <div class="stat-label">Avg Items per Order</div>
                            <div class="stat-value" id="avgItemsPerOrder">0</div>
                            <small class="text-muted">Units per invoice</small>
                        </div>

                        <div class="card card-analytics analytics_card_item">
                            <div class="stat-label">Active Users</div>
                            <div class="stat-value" id="activeUsers">0</div>
                            <small class="text-muted">Unique logins in range</small>
                        </div>

                        <div class="card card-analytics analytics_card_item">
                            <div class="stat-label">Conversion Rate</div>
                            <div class="stat-value" id="conversion">0%</div>
                            <small class="text-muted">Orders / Active users</small>
                        </div>

                        <div class="card card-analytics analytics_card_item">
                            <div class="stat-label">Repeat Customers</div>
                            <div class="stat-value" id="repeatCustomerCount">0</div>
                            <small class="text-muted">Out of distinct customers</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 3: Charts (some full width charts) -->
        <div class="row mt-3">
            <div class="col-lg-8 col-md-12 mb-3">
                <div class="card full-section h-100">
                    <h5>Daily Income vs Sales (Last 14 days)</h5>
                    <canvas id="chartIncomeSales" style="height: 150px;"></canvas>
                </div>
            </div>

            <div class="col-lg-4 col-md-12 mb-3">
                <div class="card full-section h-100">
                    <h5>Daily Visitors (Last 14 days)</h5>
                    <canvas id="chartVisitors" style="height: 150px;"></canvas>
                </div>
            </div>

            <div class="col-6 mb-3">
                <div class="card full-section mb-0">
                    <h5>This Year Sales (Monthly)</h5>
                    <canvas id="chartYearSales" style="height: 150px;"></canvas>
                </div>
            </div>

            <div class="col-6 mb-3">
                <div class="card full-section mb-0">
                    <h5>eCommerce vs POS Orders (Last 12 months)</h5>
                    <canvas id="chartEcomVsPos" style="height: 150px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Row 4: Trending & Low stock -->
        <div class="row">
            <div class="col-lg-6 col-md-12 mb-3">
                <div class="card full-section mb-0">
                    <h5>Trending Products (Top 5)</h5>
                    <div class="table-wrap mt-2">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Product</th>
                                    <th>Sold</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody id="trendingProducts">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-12 mb-3">
                <div class="card full-section mb-0">
                    <h5>Low Stock Products</h5>
                    <div class="table-wrap mt-2">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>SKU</th>
                                    <th>Product</th>
                                    <th>Stock</th>
                                    <th>Reorder Level</th>
                                </tr>
                            </thead>
                            <tbody id="lowStockProducts">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 col-md-12 mb-3">
                <div class="card full-section mb-0">
                    <h5>Order Status Snapshot</h5>
                    <div class="row">
                        <div class="col-sm-6">
                            <h6 class="text-muted">POS Orders</h6>
                            <ul class="list-group list-group-flush" id="orderStatusPos"></ul>
                        </div>
                        <div class="col-sm-6">
                            <h6 class="text-muted">eCommerce Orders</h6>
                            <ul class="list-group list-group-flush" id="orderStatusEcom"></ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 mb-3">
                <div class="card full-section mb-0">
                    <h5>Top Suppliers (by spend)</h5>
                    <div class="table-wrap mt-2">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Supplier</th>
                                    <th>Purchase Orders</th>
                                    <th>Total Spend</th>
                                </tr>
                            </thead>
                            <tbody id="topSuppliers">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- <div class="row mt-3">
            <div class="col-lg-7 col-md-12 mb-3">
                <div class="card full-section">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5>Demand Forecast & Restock Recommendations</h5>
                        <small class="text-muted" id="predictionStatus"></small>
                    </div>
                    <div class="table-wrap mt-2">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th class="text-right">Predicted Demand</th>
                                    <th class="text-right">Growth</th>
                                    <th class="text-right">Confidence</th>
                                    <th>Reason</th>
                                </tr>
                            </thead>
                            <tbody id="demandPredictionTable">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 col-md-12 mb-3">
                <div class="card full-section h-100">
                    <h5>Visitors vs Predicted Demand</h5>
                    <canvas id="chartDemandForecast" height="180"></canvas>
                    <div class="mt-3">
                        <h6 class="text-muted">Top Drivers</h6>
                        <ul id="featureImportanceList" class="list-group list-group-flush"></ul>
                    </div>
                    <div class="mt-3">
                        <h6 class="text-muted">Restock Alerts</h6>
                        <ul id="restockAlertList" class="list-group list-group-flush"></ul>
                    </div>
                </div>
            </div>
        </div> --}}

    </div>


    <script>
        (function() {
            const chartInstances = {
                incomeSales: null,
                visitorTrend: null,
                yearSales: null,
                channelSplit: null,
                demandForecast: null,
            };

            const analyticsUrl = "{{ route('home.analytics') }}";

            function formatNumber(value, decimals = 0) {
                const number = Number(value) || 0;
                return new Intl.NumberFormat(undefined, {
                    minimumFractionDigits: decimals,
                    maximumFractionDigits: decimals,
                }).format(number);
            }

            function formatCurrency(value, decimals = 0) {
                return '৳' + formatNumber(value, decimals);
            }

            function formatDateDisplay(value) {
                if (!value) {
                    return '';
                }
                const date = new Date(value);
                if (Number.isNaN(date.getTime())) {
                    return value;
                }
                return date.toLocaleDateString(undefined, {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
            }

            function formatDateISO(date) {
                return date.toISOString().slice(0, 10);
            }

            function setText(id, value) {
                const el = document.getElementById(id);
                if (el) {
                    el.textContent = value;
                }
            }

            function setRangeText(range) {
                const target = document.getElementById('rangeText');
                if (!target || !range) {
                    return;
                }
                const from = formatDateDisplay(range.from);
                const to = formatDateDisplay(range.to);
                target.textContent = from === to ? from : `${from} — ${to}`;
            }

            function initialiseDefaultDates() {
                const fromInput = document.getElementById('fromDate');
                const toInput = document.getElementById('toDate');
                if (!fromInput || !toInput) {
                    return;
                }
                if (fromInput.value && toInput.value) {
                    return;
                }
                const today = new Date();
                const past = new Date();
                past.setDate(today.getDate() - 6);
                fromInput.value = formatDateISO(past);
                toInput.value = formatDateISO(today);
            }

            function handleError(message) {
                if (window.toastr && typeof window.toastr.error === 'function') {
                    window.toastr.error(message);
                }
            }

            function setLoading(isLoading) {
                const button = document.getElementById('applyRange');
                if (!button) {
                    return;
                }
                if (!button.dataset.defaultText) {
                    button.dataset.defaultText = button.textContent;
                }
                button.disabled = isLoading;
                button.textContent = isLoading ? 'Loading…' : button.dataset.defaultText;
            }

            function updateKpis(payload) {
                const inventory = payload.kpis?.inventory || {};
                const orders = payload.kpis?.orders || {};
                const returns = payload.kpis?.returns || {};
                const finance = payload.kpis?.finance || {};
                const people = payload.kpis?.people || {};

                setText('openingStock', formatNumber(inventory.opening_stock ?? 0, 0));
                setText('salesQty', formatNumber(orders.items_sold ?? 0, 0));
                setText('returns', formatCurrency(returns.total_return_value ?? 0, 0));
                setText('stockOut', formatNumber(inventory.stock_out ?? 0, 0));
                setText('purchaseSpend', formatCurrency(finance.purchase_spend ?? 0, 0));
                setText('closingStock', formatNumber(inventory.closing_stock ?? 0, 0));

                setText('totalOrders', formatNumber(orders.total_orders ?? 0, 0));
                setText('posVsEcom',
                    `${formatNumber(orders.pos_orders ?? 0, 0)} / ${formatNumber(orders.ecommerce_orders ?? 0, 0)}`);
                setText('avgOrderValue', formatCurrency(orders.avg_order_value ?? 0, 0));
                setText('returnRate', `${formatNumber(orders.return_rate ?? 0, 0)}%`);
                setText('repeatRate', `${formatNumber(orders.repeat_customer_rate ?? 0, 0)}%`);
                setText('avgItemsPerOrder', formatNumber(orders.avg_items_per_order ?? 0, 0));

                const activeUsers = Number(people.active_users ?? 0);
                setText('activeUsers', formatNumber(activeUsers, 0));
                const conversionRate = activeUsers > 0 ? ((orders.total_orders ?? 0) / activeUsers) * 100 : 0;
                setText('conversion', `${formatNumber(conversionRate, 0)}%`);
                setText('repeatCustomerCount',
                    `${formatNumber(people.repeat_customers ?? orders.repeat_customer_count ?? 0)} / ${formatNumber(people.distinct_customers ?? orders.distinct_customers ?? 0)}`
                );

                setText('grossIncome', formatCurrency(finance.income ?? 0));
                setText('financePurchaseSpend', formatCurrency(finance.purchase_spend ?? 0));
                setText('operationalExpense', formatCurrency(finance.operational_expense ?? 0));
                setText('netProfit', formatCurrency(finance.net_profit ?? 0));
                setText('receivablesDue', formatCurrency(finance.receivable_due ?? 0));

                setRangeText(payload.meta?.range);
                renderStatusBreakdown(orders.status_breakdown);
            }

            function renderTables(tables) {
                const trendingBody = document.getElementById('trendingProducts');
                const lowStockBody = document.getElementById('lowStockProducts');

                if (trendingBody) {
                    trendingBody.innerHTML = '';
                    const items = tables?.trending_products || [];
                    if (!items.length) {
                        trendingBody.innerHTML =
                            '<tr><td colspan="4" class="text-center text-muted">No sales recorded in this range.</td></tr>';
                    } else {
                        items.forEach(item => {
                            trendingBody.innerHTML +=
                                `<tr><td>${item.rank}</td><td>${item.name}</td><td>${formatNumber(item.sold)}</td><td>${formatCurrency(item.revenue)}</td></tr>`;
                        });
                    }
                }

                if (lowStockBody) {
                    lowStockBody.innerHTML = '';
                    const lowItems = tables?.low_stock_products || [];
                    if (!lowItems.length) {
                        lowStockBody.innerHTML =
                            '<tr><td colspan="4" class="text-center text-muted">All stocks look healthy.</td></tr>';
                    } else {
                        lowItems.forEach(item => {
                            lowStockBody.innerHTML +=
                                `<tr class="low-stock"><td>${item.sku ?? 'N/A'}</td><td>${item.name}</td><td>${formatNumber(item.stock, 2)}</td><td>${formatNumber(item.reorder_level ?? 0)}</td></tr>`;
                        });
                    }
                }

                const supplierBody = document.getElementById('topSuppliers');
                if (supplierBody) {
                    supplierBody.innerHTML = '';
                    const suppliers = tables?.top_suppliers || [];
                    if (!suppliers.length) {
                        supplierBody.innerHTML =
                            '<tr><td colspan="4" class="text-center text-muted">No purchasing activity in this range.</td></tr>';
                    } else {
                        suppliers.forEach(item => {
                            supplierBody.innerHTML +=
                                `<tr><td>${item.rank}</td><td>${item.supplier}</td><td>${formatNumber(item.orders)}</td><td>${formatCurrency(item.spend)}</td></tr>`;
                        });
                    }
                }
            }

            function renderPredictions(predictionData) {
                const enabled = predictionData?.enabled;
                const items = predictionData?.items || [];
                const featureImportance = predictionData?.feature_importance || [];
                const restockAlerts = predictionData?.restock_alerts || [];

                const tableBody = document.getElementById('demandPredictionTable');
                const featureList = document.getElementById('featureImportanceList');
                const alertsList = document.getElementById('restockAlertList');
                const statusLabel = document.getElementById('predictionStatus');

                if (!tableBody || !featureList || !alertsList || !statusLabel) {
                    return;
                }

                if (!enabled) {
                    statusLabel.textContent = 'Prediction engine disabled';
                    tableBody.innerHTML =
                        '<tr><td colspan="5" class="text-center text-muted">Enable analytics predictions to view recommendations.</td></tr>';
                    featureList.innerHTML = '';
                    alertsList.innerHTML = '';
                    if (chartInstances.demandForecast) {
                        chartInstances.demandForecast.destroy();
                        chartInstances.demandForecast = null;
                    }
                    return;
                }

                statusLabel.textContent = items.length ?
                    `Updated ${formatDateDisplay(items[0].predicted_at)}` :
                    'Awaiting prediction run';

                if (!items.length) {
                    tableBody.innerHTML =
                        '<tr><td colspan="5" class="text-center text-muted">No predictions available. Run <code>php artisan analytics:predict-demand</code>.</td></tr>';
                } else {
                    tableBody.innerHTML = '';
                    items.slice(0, 10).forEach(item => {
                        const growth = item.predicted_growth_pct ?? 0;
                        const directionClass = growth > 0 ? 'text-success' : growth < 0 ? 'text-danger' :
                            'text-muted';
                        const arrow = growth > 0 ? '↑' : growth < 0 ? '↓' : '→';
                        const confidence = item.confidence ? `${formatNumber(item.confidence * 100, 1)}%` : '—';
                        tableBody.innerHTML += `
                            <tr>
                                <td>${item.product_name}</td>
                                <td class="text-right">${formatNumber(item.predicted_demand ?? 0, 2)}</td>
                                <td class="text-right ${directionClass}">${arrow} ${formatNumber(growth, 2)}%</td>
                                <td class="text-right">${confidence}</td>
                                <td>${item.reason || ''}</td>
                            </tr>
                        `;
                    });
                }

                featureList.innerHTML = '';
                if (!featureImportance.length) {
                    featureList.innerHTML = '<li class="list-group-item text-muted">No feature attribution data.</li>';
                } else {
                    featureImportance
                        .sort((a, b) => (b.importance ?? 0) - (a.importance ?? 0))
                        .slice(0, 5)
                        .forEach(feature => {
                            const label = feature.feature.replace(/_/g, ' ');
                            featureList.innerHTML += `<li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>${label}</span>
                                <span class="badge badge-info badge-pill">${formatNumber((feature.importance ?? 0) * 100, 1)}%</span>
                            </li>`;
                        });
                }

                alertsList.innerHTML = '';
                if (!restockAlerts.length) {
                    alertsList.innerHTML = '<li class="list-group-item text-muted">No urgent restock alerts.</li>';
                } else {
                    restockAlerts.forEach(item => {
                        alertsList.innerHTML += `<li class="list-group-item">
                            <strong>${item.product_name}</strong>
                            <div class="small text-muted">Predicted demand ${formatNumber(item.predicted_demand ?? 0, 2)} vs stock ${formatNumber(item.current_stock ?? 0, 2)}</div>
                        </li>`;
                    });
                }

                const chartLabels = items.slice(0, 6).map(row => row.product_name);
                const demandSeries = items.slice(0, 6).map(row => row.predicted_demand ?? 0);
                const visitorSeries = items.slice(0, 6).map(row => row.meta?.latest_visitors ?? 0);

                const ctx = document.getElementById('chartDemandForecast')?.getContext('2d');
                if (ctx) {
                    createOrUpdateChart('demandForecast', ctx, () => ({
                        type: 'bar',
                        data: {
                            labels: chartLabels,
                            datasets: [{
                                    label: 'Predicted Demand',
                                    data: demandSeries,
                                    backgroundColor: 'rgba(16,185,129,0.6)',
                                    borderColor: 'rgba(16,185,129,0.9)',
                                    borderWidth: 1,
                                    yAxisID: 'demand',
                                },
                                {
                                    type: 'line',
                                    label: 'Latest Visitors',
                                    data: visitorSeries,
                                    borderColor: '#6366f1',
                                    backgroundColor: 'rgba(99,102,241,0.1)',
                                    fill: true,
                                    yAxisID: 'visitors',
                                },
                            ],
                        },
                        options: {
                            maintainAspectRatio: false,
                            scales: {
                                yAxes: [{
                                        id: 'demand',
                                        position: 'left',
                                        ticks: {
                                            beginAtZero: true
                                        },
                                    },
                                    {
                                        id: 'visitors',
                                        position: 'right',
                                        ticks: {
                                            beginAtZero: true
                                        },
                                        gridLines: {
                                            drawOnChartArea: false
                                        },
                                    },
                                ],
                            },
                            tooltips: {
                                callbacks: {
                                    label(tooltipItem, data) {
                                        const dataset = data.datasets[tooltipItem.datasetIndex];
                                        return `${dataset.label}: ${formatNumber(dataset.data[tooltipItem.index], 2)}`;
                                    },
                                },
                            },
                        },
                    }));
                }
            }

            function createOrUpdateChart(key, ctx, configFactory) {
                if (!ctx) {
                    return;
                }
                if (chartInstances[key]) {
                    chartInstances[key].destroy();
                }
                chartInstances[key] = new Chart(ctx, configFactory());
            }

            function renderStatusBreakdown(breakdown) {
                const posList = document.getElementById('orderStatusPos');
                const ecomList = document.getElementById('orderStatusEcom');

                if (posList) {
                    posList.innerHTML = '';
                    const entries = Object.entries(breakdown?.pos || {});
                    if (!entries.length) {
                        posList.innerHTML = '<li class="list-group-item text-muted">No orders in range.</li>';
                    } else {
                        entries.forEach(([label, count]) => {
                            posList.innerHTML +=
                                `<li class="list-group-item d-flex justify-content-between align-items-center"><span>${label}</span><span class="badge badge-primary badge-pill">${formatNumber(count)}</span></li>`;
                        });
                    }
                }

                if (ecomList) {
                    ecomList.innerHTML = '';
                    const entries = Object.entries(breakdown?.ecommerce || {});
                    if (!entries.length) {
                        ecomList.innerHTML = '<li class="list-group-item text-muted">No orders in range.</li>';
                    } else {
                        entries.forEach(([label, count]) => {
                            ecomList.innerHTML +=
                                `<li class="list-group-item d-flex justify-content-between align-items-center"><span>${label}</span><span class="badge badge-primary badge-pill">${formatNumber(count)}</span></li>`;
                        });
                    }
                }
            }

            function renderCharts(charts) {
                if (!charts) {
                    return;
                }

                const incomeData = charts.income_vs_sales || {
                    labels: [],
                    income: [],
                    sales_qty: []
                };
                const incomeCtx = document.getElementById('chartIncomeSales')?.getContext('2d');
                createOrUpdateChart('incomeSales', incomeCtx, () => ({
                    type: 'bar',
                    data: {
                        labels: incomeData.labels,
                        datasets: [{
                                type: 'bar',
                                label: 'Units Sold',
                                data: incomeData.sales_qty,
                                backgroundColor: 'rgba(79,70,229,0.55)',
                                borderColor: 'rgba(79,70,229,0.9)',
                                borderWidth: 1,
                                yAxisID: 'qty',
                            },
                            {
                                type: 'line',
                                label: 'Income (৳)',
                                data: incomeData.income,
                                borderColor: '#dc2626',
                                backgroundColor: 'rgba(220,38,38,0.15)',
                                fill: false,
                                yAxisID: 'income',
                                lineTension: 0.2,
                            }
                        ]
                    },
                    options: {
                        maintainAspectRatio: false,
                        scales: {
                            yAxes: [{
                                    id: 'qty',
                                    position: 'left',
                                    ticks: {
                                        beginAtZero: true
                                    },
                                },
                                {
                                    id: 'income',
                                    position: 'right',
                                    ticks: {
                                        beginAtZero: true,
                                        callback: value => formatCurrency(value),
                                    },
                                    gridLines: {
                                        drawOnChartArea: false
                                    },
                                }
                            ]
                        },
                        tooltips: {
                            callbacks: {
                                label(tooltipItem, data) {
                                    const dataset = data.datasets[tooltipItem.datasetIndex];
                                    const value = dataset.data[tooltipItem.index];
                                    return dataset.yAxisID === 'income' ?
                                        `${dataset.label}: ${formatCurrency(value)}` :
                                        `${dataset.label}: ${formatNumber(value)}`;
                                }
                            }
                        }
                    }
                }));

                const visitorData = charts.visitor_trend || {
                    labels: [],
                    visitors: []
                };
                const visitorCtx = document.getElementById('chartVisitors')?.getContext('2d');
                createOrUpdateChart('visitorTrend', visitorCtx, () => ({
                    type: 'line',
                    data: {
                        labels: visitorData.labels,
                        datasets: [{
                            label: 'Active Users',
                            data: visitorData.visitors,
                            borderColor: '#4f46e5',
                            backgroundColor: 'rgba(79,70,229,0.1)',
                            fill: true,
                            lineTension: 0.2,
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                }
                            }],
                        },
                    }
                }));

                const yearData = charts.year_to_date_sales || {
                    labels: [],
                    totals: []
                };
                const yearCtx = document.getElementById('chartYearSales')?.getContext('2d');
                createOrUpdateChart('yearSales', yearCtx, () => ({
                    type: 'bar',
                    data: {
                        labels: yearData.labels,
                        datasets: [{
                            label: 'Revenue (৳)',
                            data: yearData.totals,
                            backgroundColor: 'rgba(79,70,229,0.6)',
                            borderColor: 'rgba(79,70,229,0.9)',
                            borderWidth: 1,
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                    callback: value => formatCurrency(value),
                                }
                            }]
                        },
                        tooltips: {
                            callbacks: {
                                label(tooltipItem) {
                                    return `Revenue: ${formatCurrency(tooltipItem.yLabel)}`;
                                }
                            }
                        }
                    }
                }));

                const channelData = charts.channel_sales_split || {
                    labels: [],
                    pos: [],
                    ecommerce: []
                };
                const channelCtx = document.getElementById('chartEcomVsPos')?.getContext('2d');
                createOrUpdateChart('channelSplit', channelCtx, () => ({
                    type: 'line',
                    data: {
                        labels: channelData.labels,
                        datasets: [{
                                label: 'POS Revenue',
                                data: channelData.pos,
                                borderColor: '#0ea5e9',
                                backgroundColor: 'rgba(14,165,233,0.1)',
                                fill: false,
                                lineTension: 0.2,
                            },
                            {
                                label: 'eCommerce Revenue',
                                data: channelData.ecommerce,
                                borderColor: '#f59e0b',
                                backgroundColor: 'rgba(245,158,11,0.1)',
                                fill: false,
                                lineTension: 0.2,
                            }
                        ]
                    },
                    options: {
                        maintainAspectRatio: false,
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                    callback: value => formatCurrency(value),
                                }
                            }]
                        },
                        tooltips: {
                            callbacks: {
                                label(tooltipItem, data) {
                                    const dataset = data.datasets[tooltipItem.datasetIndex];
                                    return `${dataset.label}: ${formatCurrency(dataset.data[tooltipItem.index])}`;
                                }
                            }
                        }
                    }
                }));
            }

            async function fetchAnalytics() {
                const from = document.getElementById('fromDate')?.value;
                const to = document.getElementById('toDate')?.value;

                const params = {};
                if (from) {
                    params.from = from;
                }
                if (to) {
                    params.to = to;
                }

                setLoading(true);
                try {
                    const response = await axios.get(analyticsUrl, {
                        params
                    });
                    if (response.data?.success && response.data.data) {
                        const payload = response.data.data;
                        updateKpis(payload);
                        renderTables(payload.tables);
                        renderCharts(payload.charts);
                        renderPredictions(payload.predictions);
                    } else {
                        handleError('Unable to load analytics data.');
                    }
                } catch (error) {
                    console.error('Analytics fetch error', error);
                    const message = error.response?.data?.message || 'Failed to load analytics data.';
                    handleError(message);
                } finally {
                    setLoading(false);
                }
            }

            document.getElementById('applyRange')?.addEventListener('click', fetchAnalytics);

            window.addEventListener('DOMContentLoaded', () => {
                initialiseDefaultDates();
                fetchAnalytics();
            });
        })();
    </script>
@endsection

@section('footer_js')
@endsection
