@extends('backend.master')

@section('header_css')
<style>
    * {
        box-sizing: border-box;
    }

    .analytics-dashboard {
        padding: 20px;
        background: #f5f7fa;
        min-height: 100vh;
    }

    .dashboard-header {
        margin-bottom: 30px;
    }

    .dashboard-header h2 {
        color: #2c3e50;
        font-size: 28px;
        margin: 0 0 10px 0;
        font-weight: 600;
    }

    .dashboard-header p {
        color: #7f8c8d;
        font-size: 14px;
        margin: 0;
    }

    /* Stats Cards */
    .stats-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        flex: 1;
        min-width: 240px;
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: transform 0.3s, box-shadow 0.3s;
        position: relative;
        overflow: hidden;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.12);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(135deg, var(--card-color-1), var(--card-color-2));
    }

    .stat-card.sales-card {
        --card-color-1: #4e73df;
        --card-color-2: #224abe;
    }

    .stat-card.orders-card {
        --card-color-1: #1cc88a;
        --card-color-2: #13855c;
    }

    .stat-card.revenue-card {
        --card-color-1: #36b9cc;
        --card-color-2: #258391;
    }

    .stat-card.avg-card {
        --card-color-1: #f6c23e;
        --card-color-2: #dda20a;
    }

    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 15px;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        background: linear-gradient(135deg, var(--card-color-1), var(--card-color-2));
        color: white;
    }

    .stat-label {
        color: #7f8c8d;
        font-size: 13px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-value {
        font-size: 32px;
        font-weight: 700;
        color: #2c3e50;
        margin: 10px 0;
        line-height: 1;
    }

    .stat-change {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 13px;
        font-weight: 600;
    }

    .stat-change.positive {
        color: #1cc88a;
    }

    .stat-change.negative {
        color: #e74a3b;
    }

    .stat-change i {
        font-size: 12px;
    }

    /* Charts Section */
    .charts-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 30px;
    }

    .chart-card {
        flex: 1;
        min-width: 300px;
        background: white;
        border-radius: 10px;
        padding: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .chart-card.large {
        flex: 2;
        min-width: 600px;
    }

    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f0f2f5;
    }

    .chart-title {
        font-size: 18px;
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
    }

    .chart-subtitle {
        font-size: 13px;
        color: #7f8c8d;
        margin-top: 5px;
    }

    .chart-container {
        position: relative;
        height: 300px;
    }

    .chart-container.small {
        height: 250px;
    }

    /* Products Grid */
    .products-section {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 30px;
    }

    .products-card {
        flex: 1;
        min-width: 350px;
        background: white;
        border-radius: 10px;
        padding: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .product-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .product-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px;
        border-bottom: 1px solid #f0f2f5;
        transition: background 0.2s;
    }

    .product-item:hover {
        background: #f8f9fa;
        border-radius: 8px;
    }

    .product-item:last-child {
        border-bottom: none;
    }

    .product-image {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        object-fit: cover;
        background: #f0f2f5;
    }

    .product-info {
        flex: 1;
    }

    .product-name {
        font-size: 14px;
        font-weight: 600;
        color: #2c3e50;
        margin: 0 0 5px 0;
        line-height: 1.4;
    }

    .product-meta {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 12px;
        color: #7f8c8d;
    }

    .product-price {
        font-weight: 600;
        color: #1cc88a;
    }

    .product-stats {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 14px;
        font-weight: 600;
        color: #4e73df;
    }

    .rating-stars {
        color: #f6c23e;
        font-size: 12px;
    }

    /* Loading State */
    .loading-skeleton {
        background: linear-gradient(90deg, #f0f2f5 25%, #e4e7ea 50%, #f0f2f5 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
        border-radius: 4px;
    }

    @keyframes loading {
        0% {
            background-position: 200% 0;
        }
        100% {
            background-position: -200% 0;
        }
    }

    .skeleton-text {
        height: 16px;
        margin: 8px 0;
    }

    .skeleton-title {
        height: 24px;
        width: 60%;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #7f8c8d;
    }

    .empty-state i {
        font-size: 48px;
        color: #d4d9df;
        margin-bottom: 15px;
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .chart-card.large {
            min-width: 100%;
        }
    }

    @media (max-width: 768px) {
        .analytics-dashboard {
            padding: 15px;
        }

        .stats-grid {
            gap: 15px;
        }

        .stat-card {
            min-width: 100%;
        }

        .charts-grid {
            gap: 15px;
        }

        .chart-card {
            min-width: 100%;
        }

        .products-section {
            gap: 15px;
        }

        .products-card {
            min-width: 100%;
        }

        .dashboard-header h2 {
            font-size: 24px;
        }

        .stat-value {
            font-size: 28px;
        }
    }

    @media (max-width: 480px) {
        .stat-header {
            flex-direction: column;
            gap: 10px;
        }

        .product-item {
            flex-direction: column;
            text-align: center;
        }

        .product-meta {
            flex-direction: column;
            gap: 5px;
        }
    }
</style>
@endsection

@section('page_title')
    Analytics Dashboard
@endsection

@section('page_heading')
    Business Analytics
@endsection

@section('content')
<div class="analytics-dashboard">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <h2>🍃 Organic Analytics</h2>
        <p>Real-time insights for your business performance</p>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card sales-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Sales</div>
                    <div class="stat-value" id="stat-sales">
                        <div class="loading-skeleton skeleton-text" style="width: 120px; height: 32px;"></div>
                    </div>
                    <div class="stat-change" id="stat-sales-change">
                        <div class="loading-skeleton skeleton-text" style="width: 80px; height: 16px;"></div>
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
        </div>

        <div class="stat-card orders-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Total Orders</div>
                    <div class="stat-value" id="stat-orders">
                        <div class="loading-skeleton skeleton-text" style="width: 80px; height: 32px;"></div>
                    </div>
                    <div class="stat-change" id="stat-orders-change">
                        <div class="loading-skeleton skeleton-text" style="width: 80px; height: 16px;"></div>
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
        </div>

        <div class="stat-card revenue-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Revenue</div>
                    <div class="stat-value" id="stat-revenue">
                        <div class="loading-skeleton skeleton-text" style="width: 120px; height: 32px;"></div>
                    </div>
                    <div class="stat-change" id="stat-revenue-change">
                        <div class="loading-skeleton skeleton-text" style="width: 80px; height: 16px;"></div>
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>

        <div class="stat-card avg-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Avg Order Value</div>
                    <div class="stat-value" id="stat-avg">
                        <div class="loading-skeleton skeleton-text" style="width: 100px; height: 32px;"></div>
                    </div>
                    <div class="stat-change" id="stat-products">
                        <div class="loading-skeleton skeleton-text" style="width: 80px; height: 16px;"></div>
                    </div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-receipt"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-grid">
        <!-- Sales Chart -->
        <div class="chart-card large">
            <div class="chart-header">
                <div>
                    <h3 class="chart-title">Sales Overview</h3>
                    <p class="chart-subtitle">Last 12 months performance</p>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- Category Distribution -->
        <div class="chart-card">
            <div class="chart-header">
                <div>
                    <h3 class="chart-title">Category Distribution</h3>
                    <p class="chart-subtitle">Revenue by category</p>
                </div>
            </div>
            <div class="chart-container small">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Products Section -->
    <div class="products-section">
        <!-- Top Selling Products -->
        <div class="products-card">
            <div class="chart-header">
                <h3 class="chart-title">🔥 Top Selling Products</h3>
            </div>
            <ul class="product-list" id="top-selling-list">
                <!-- Loading skeleton -->
                <li class="product-item">
                    <div class="loading-skeleton" style="width: 60px; height: 60px; border-radius: 8px;"></div>
                    <div class="product-info" style="flex: 1;">
                        <div class="loading-skeleton skeleton-text" style="width: 80%; height: 16px;"></div>
                        <div class="loading-skeleton skeleton-text" style="width: 50%; height: 12px;"></div>
                    </div>
                </li>
                <li class="product-item">
                    <div class="loading-skeleton" style="width: 60px; height: 60px; border-radius: 8px;"></div>
                    <div class="product-info" style="flex: 1;">
                        <div class="loading-skeleton skeleton-text" style="width: 70%; height: 16px;"></div>
                        <div class="loading-skeleton skeleton-text" style="width: 45%; height: 12px;"></div>
                    </div>
                </li>
            </ul>
        </div>

        <!-- Top Rated Products -->
        <div class="products-card">
            <div class="chart-header">
                <h3 class="chart-title">⭐ Top Rated Products</h3>
            </div>
            <ul class="product-list" id="top-rated-list">
                <!-- Loading skeleton -->
                <li class="product-item">
                    <div class="loading-skeleton" style="width: 60px; height: 60px; border-radius: 8px;"></div>
                    <div class="product-info" style="flex: 1;">
                        <div class="loading-skeleton skeleton-text" style="width: 80%; height: 16px;"></div>
                        <div class="loading-skeleton skeleton-text" style="width: 50%; height: 12px;"></div>
                    </div>
                </li>
                <li class="product-item">
                    <div class="loading-skeleton" style="width: 60px; height: 60px; border-radius: 8px;"></div>
                    <div class="product-info" style="flex: 1;">
                        <div class="loading-skeleton skeleton-text" style="width: 70%; height: 16px;"></div>
                        <div class="loading-skeleton skeleton-text" style="width: 45%; height: 12px;"></div>
                    </div>
                </li>
            </ul>
        </div>

        <!-- Top Viewed Products -->
        <div class="products-card">
            <div class="chart-header">
                <h3 class="chart-title">👁️ Top Viewed Products</h3>
            </div>
            <ul class="product-list" id="top-viewed-list">
                <!-- Loading skeleton -->
                <li class="product-item">
                    <div class="loading-skeleton" style="width: 60px; height: 60px; border-radius: 8px;"></div>
                    <div class="product-info" style="flex: 1;">
                        <div class="loading-skeleton skeleton-text" style="width: 80%; height: 16px;"></div>
                        <div class="loading-skeleton skeleton-text" style="width: 50%; height: 12px;"></div>
                    </div>
                </li>
                <li class="product-item">
                    <div class="loading-skeleton" style="width: 60px; height: 60px; border-radius: 8px;"></div>
                    <div class="product-info" style="flex: 1;">
                        <div class="loading-skeleton skeleton-text" style="width: 70%; height: 16px;"></div>
                        <div class="loading-skeleton skeleton-text" style="width: 45%; height: 12px;"></div>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    <!-- Top Categories -->
    <div class="chart-card">
        <div class="chart-header">
            <h3 class="chart-title">📊 Top Performing Categories</h3>
        </div>
        <ul class="product-list" id="top-categories-list">
            <!-- Loading skeleton -->
            <li class="product-item">
                <div class="loading-skeleton" style="width: 60px; height: 60px; border-radius: 8px;"></div>
                <div class="product-info" style="flex: 1;">
                    <div class="loading-skeleton skeleton-text" style="width: 80%; height: 16px;"></div>
                    <div class="loading-skeleton skeleton-text" style="width: 50%; height: 12px;"></div>
                </div>
            </li>
        </ul>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let salesChart = null;
    let categoryChart = null;

    // Format currency
    function formatCurrency(amount) {
        return '৳' + parseFloat(amount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    // Format number
    function formatNumber(num) {
        return parseInt(num).toLocaleString();
    }

    // Load Overview Stats
    function loadOverview() {
        axios.get('/api/analytics/overview')
            .then(response => {
                if (response.data.success) {
                    const data = response.data.data;
                    
                    // Update sales
                    document.getElementById('stat-sales').innerHTML = formatCurrency(data.current.sales);
                    updateChangeIndicator('stat-sales-change', data.changes.sales, 'vs last month');
                    
                    // Update orders
                    document.getElementById('stat-orders').innerHTML = formatNumber(data.current.orders);
                    updateChangeIndicator('stat-orders-change', data.changes.orders, 'vs last month');
                    
                    // Update revenue
                    document.getElementById('stat-revenue').innerHTML = formatCurrency(data.current.revenue);
                    updateChangeIndicator('stat-revenue-change', data.changes.revenue, 'vs last month');
                    
                    // Update avg order value
                    document.getElementById('stat-avg').innerHTML = formatCurrency(data.current.avg_order_value);
                    document.getElementById('stat-products').innerHTML = `<i class="fas fa-box"></i> ${data.stats.total_products} Products`;
                }
            })
            .catch(error => {
                console.error('Error loading overview:', error);
            });
    }

    function updateChangeIndicator(elementId, change, label) {
        const element = document.getElementById(elementId);
        const isPositive = change >= 0;
        const icon = isPositive ? 'fa-arrow-up' : 'fa-arrow-down';
        const className = isPositive ? 'positive' : 'negative';
        
        element.innerHTML = `
            <i class="fas ${icon}"></i>
            ${Math.abs(change).toFixed(1)}% ${label}
        `;
        element.className = `stat-change ${className}`;
    }

    // Load Sales Chart
    function loadSalesChart() {
        axios.get('/api/analytics/sales-chart')
            .then(response => {
                if (response.data.success) {
                    const data = response.data.data;
                    
                    const ctx = document.getElementById('salesChart').getContext('2d');
                    if (salesChart) salesChart.destroy();
                    
                    salesChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Sales (৳)',
                                data: data.sales,
                                borderColor: '#4e73df',
                                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                                tension: 0.4,
                                fill: true,
                                pointRadius: 4,
                                pointBackgroundColor: '#4e73df'
                            }, {
                                label: 'Orders',
                                data: data.orders,
                                borderColor: '#1cc88a',
                                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                                tension: 0.4,
                                fill: true,
                                pointRadius: 4,
                                pointBackgroundColor: '#1cc88a',
                                yAxisID: 'y1'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                mode: 'index',
                                intersect: false,
                            },
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top',
                                }
                            },
                            scales: {
                                y: {
                                    type: 'linear',
                                    display: true,
                                    position: 'left',
                                    ticks: {
                                        callback: function(value) {
                                            return '৳' + value.toLocaleString();
                                        }
                                    }
                                },
                                y1: {
                                    type: 'linear',
                                    display: true,
                                    position: 'right',
                                    grid: {
                                        drawOnChartArea: false,
                                    },
                                }
                            }
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error loading sales chart:', error);
            });
    }

    // Load Category Distribution
    function loadCategoryChart() {
        axios.get('/api/analytics/category-distribution')
            .then(response => {
                if (response.data.success) {
                    const data = response.data.data;
                    
                    const ctx = document.getElementById('categoryChart').getContext('2d');
                    if (categoryChart) categoryChart.destroy();
                    
                    categoryChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                data: data.values,
                                backgroundColor: data.colors,
                                borderWidth: 2,
                                borderColor: '#fff'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'bottom',
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return context.label + ': ৳' + context.parsed.toLocaleString();
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error loading category chart:', error);
            });
    }

    // Load Top Selling Products
    function loadTopSelling() {
        axios.get('/api/analytics/top-selling-products?limit=5')
            .then(response => {
                if (response.data.success) {
                    const list = document.getElementById('top-selling-list');
                    list.innerHTML = response.data.data.map((product, index) => `
                        <li class="product-item">
                            <img src="/uploads/products/${product.image || 'placeholder.jpg'}" 
                                 alt="${product.name}" 
                                 class="product-image"
                                 onerror="this.src='/uploads/products/placeholder.jpg'">
                            <div class="product-info">
                                <h4 class="product-name">${product.name}</h4>
                                <div class="product-meta">
                                    <span class="product-price">${formatCurrency(product.discount_price || product.price)}</span>
                                    <span>•</span>
                                    <span>${formatNumber(product.total_sold)} sold</span>
                                </div>
                            </div>
                            <div class="product-stats">
                                <i class="fas fa-fire"></i> ${formatCurrency(product.total_revenue)}
                            </div>
                        </li>
                    `).join('');
                }
            })
            .catch(error => {
                console.error('Error loading top selling:', error);
            });
    }

    // Load Top Rated Products
    function loadTopRated() {
        axios.get('/api/analytics/top-rated-products?limit=5')
            .then(response => {
                if (response.data.success) {
                    const list = document.getElementById('top-rated-list');
                    if (response.data.data.length === 0) {
                        list.innerHTML = `
                            <div class="empty-state">
                                <i class="fas fa-star"></i>
                                <p>No rated products yet</p>
                            </div>
                        `;
                        return;
                    }
                    list.innerHTML = response.data.data.map(product => {
                        const stars = '★'.repeat(Math.round(product.avg_rating)) + '☆'.repeat(5 - Math.round(product.avg_rating));
                        return `
                            <li class="product-item">
                                <img src="/uploads/products/${product.image || 'placeholder.jpg'}" 
                                     alt="${product.name}" 
                                     class="product-image"
                                     onerror="this.src='/uploads/products/placeholder.jpg'">
                                <div class="product-info">
                                    <h4 class="product-name">${product.name}</h4>
                                    <div class="product-meta">
                                        <span class="rating-stars">${stars}</span>
                                        <span>${parseFloat(product.avg_rating).toFixed(1)}</span>
                                        <span>•</span>
                                        <span>${product.review_count} reviews</span>
                                    </div>
                                </div>
                                <div class="product-stats">
                                    ⭐ ${parseFloat(product.avg_rating).toFixed(1)}
                                </div>
                            </li>
                        `;
                    }).join('');
                }
            })
            .catch(error => {
                console.error('Error loading top rated:', error);
            });
    }

    // Load Top Viewed Products
    function loadTopViewed() {
        axios.get('/api/analytics/top-viewed-products?limit=5')
            .then(response => {
                if (response.data.success) {
                    const list = document.getElementById('top-viewed-list');
                    if (response.data.data.length === 0) {
                        list.innerHTML = `
                            <div class="empty-state">
                                <i class="fas fa-eye"></i>
                                <p>No view data available</p>
                            </div>
                        `;
                        return;
                    }
                    list.innerHTML = response.data.data.map(product => `
                        <li class="product-item">
                            <img src="/uploads/products/${product.image || 'placeholder.jpg'}" 
                                 alt="${product.name}" 
                                 class="product-image"
                                 onerror="this.src='/uploads/products/placeholder.jpg'">
                            <div class="product-info">
                                <h4 class="product-name">${product.name}</h4>
                                <div class="product-meta">
                                    <span class="product-price">${formatCurrency(product.discount_price || product.price)}</span>
                                    <span>•</span>
                                    <span>${formatNumber(product.view_count)} views</span>
                                </div>
                            </div>
                            <div class="product-stats">
                                <i class="fas fa-eye"></i> ${formatNumber(product.view_count)}
                            </div>
                        </li>
                    `).join('');
                }
            })
            .catch(error => {
                console.error('Error loading top viewed:', error);
            });
    }

    // Load Top Categories
    function loadTopCategories() {
        axios.get('/api/analytics/top-categories?limit=5')
            .then(response => {
                if (response.data.success) {
                    const list = document.getElementById('top-categories-list');
                    list.innerHTML = response.data.data.map(category => `
                        <li class="product-item">
                            <img src="/uploads/categories/${category.image || 'placeholder.jpg'}" 
                                 alt="${category.name}" 
                                 class="product-image"
                                 onerror="this.src='/uploads/categories/placeholder.jpg'">
                            <div class="product-info">
                                <h4 class="product-name">${category.name}</h4>
                                <div class="product-meta">
                                    <span>${category.product_count} products</span>
                                    <span>•</span>
                                    <span>${formatNumber(category.total_sold)} sold</span>
                                </div>
                            </div>
                            <div class="product-stats">
                                ${formatCurrency(category.total_revenue)}
                            </div>
                        </li>
                    `).join('');
                }
            })
            .catch(error => {
                console.error('Error loading top categories:', error);
            });
    }

    // Initialize all
    loadOverview();
    loadSalesChart();
    loadCategoryChart();
    loadTopSelling();
    loadTopRated();
    loadTopViewed();
    loadTopCategories();
});
</script>
@endpush

