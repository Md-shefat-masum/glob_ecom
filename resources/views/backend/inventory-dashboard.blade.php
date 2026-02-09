@extends('backend.master')

@section('header_css')
    <link href="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.css" rel="stylesheet">
    <style>
        h4.card-title {
            background: linear-gradient(90deg,rgba(0, 179, 68, 1) 0%, rgba(1, 102, 85, 1) 100%);
            padding: 8px 15px;
            border-radius: 4px;
            color: white;
        }

        .graph_card {
            position: relative;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .graph_card i {
            position: absolute;
            top: 18px;
            right: 18px;
            font-size: 18px;
            height: 35px;
            width: 35px;
            line-height: 33px;
            text-align: center;
            border-radius: 50%;
            font-weight: 300;
        }

        .card-body {
            padding: 1.5rem;
        }

        .chart-container {
            position: relative;
            height: 300px;
        }
    </style>
@endsection

@section('page_title')
    Inventory Dashboard
@endsection

@section('page_heading')
    Overview
@endsection

@section('content')

    <div class="row">
        <div class="col-lg col-md-6">
            <div class="card graph_card">
                <div class="card-body">
                    <h6 class="text-uppercase font-size-12 text-muted mb-3">Pending Quotations</h6>
                    <span class="h3 mb-0">{{ number_format($pending_quotations) }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg col-md-6">
            <div class="card graph_card">
                <div class="card-body">
                    <h6 class="text-uppercase font-size-12 text-muted mb-3">Total Purchase Subtotal</h6>
                    <span class="h3 mb-0">$ {{ number_format($total_subtotal_orders) }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg col-md-6">
            <div class="card graph_card">
                <div class="card-body">
                    <h6 class="text-uppercase font-size-12 text-muted mb-3">Total Purchase Grandtotal</h6>
                    <span class="h3 mb-0">$ {{ number_format($total_purchase_orders) }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg col-md-6">
            <div class="card graph_card">
                <div class="card-body">
                    <h6 class="text-uppercase font-size-12 text-muted mb-3">Total Discount Amount</h6>
                    <span class="h3 mb-0"> $ {{ number_format($total_discount_orders) }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg col-md-6">
            <div class="card graph_card">
                <div class="card-body">
                    <h6 class="text-uppercase font-size-12 text-muted mb-3">Total Other Charges</h6>
                    <span class="h3 mb-0">$ {{ number_format($total_other_charges) }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <h5 class="mb-4 mt-2">Warehouse Details</h5>
        </div>
        <!-- Key Metrics Cards -->
        <div class="col-lg-4 col-md-6">
            <div class="card graph_card">
                <div class="card-body">
                    <h6 class="text-uppercase font-size-12 text-muted mb-3">Total Warehouses</h6>
                    <span class="h3 mb-0">{{ number_format($total_warehouses) }}</span>
                    <i class="feather-home" style="color: #0074E4; background: #0074E42E;"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card graph_card">
                <div class="card-body">
                    <h6 class="text-uppercase font-size-12 text-muted mb-3">Total Active Warehouses</h6>
                    <span class="h3 mb-0">{{ number_format($total_active_warehouses) }}</span>
                    <i class="feather-home" style="color: #0074E4; background: #0074E42E;"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card graph_card">
                <div class="card-body">
                    <h6 class="text-uppercase font-size-12 text-muted mb-3">Total InActive Warehouses</h6>
                    <span class="h3 mb-0">{{ number_format($total_inactive_warehouses) }}</span>
                    <i class="feather-home" style="color: #0074E4; background: #0074E42E;"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card graph_card">
                <div class="card-body">
                    <h6 class="text-uppercase font-size-12 text-muted mb-3">Total Warehouses Room</h6>
                    <span class="h3 mb-0">{{ number_format($total_warehouses_room) }}</span>
                    <i class="feather-home" style="color: #17263A; background: #17263A3D;"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card graph_card">
                <div class="card-body">
                    <h6 class="text-uppercase font-size-12 text-muted mb-3">Total Active Warehouses Room</h6>
                    <span class="h3 mb-0">{{ number_format($total_active_warehouses_room) }}</span>
                    <i class="feather-home" style="color: #17263A; background: #17263A3D;"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card graph_card">
                <div class="card-body">
                    <h6 class="text-uppercase font-size-12 text-muted mb-3">Total InActive Warehouses Room</h6>
                    <span class="h3 mb-0">{{ number_format($total_inactive_warehouses_room) }}</span>
                    <i class="feather-home" style="color: #17263A; background: #17263A3D;"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card graph_card">
                <div class="card-body">
                    <h6 class="text-uppercase font-size-12 text-muted mb-3">Total Warehouses Room Cartoon</h6>
                    <span class="h3 mb-0">{{ number_format($total_warehouses_room_cartoons) }}</span>
                    <i class="feather-box" style="color: #c28a00; background: #daa5202e;"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card graph_card">
                <div class="card-body">
                    <h6 class="text-uppercase font-size-12 text-muted mb-3">Total Active Warehouses Room Cartoon</h6>
                    <span class="h3 mb-0">{{ number_format($total_active_warehouses_room_cartoons) }}</span>
                    <i class="feather-box" style="color: #c28a00; background: #daa5202e;"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card graph_card">
                <div class="card-body">
                    <h6 class="text-uppercase font-size-12 text-muted mb-3">Total InActive Warehouses Room Cartoon</h6>
                    <span class="h3 mb-0">{{ number_format($total_inactive_warehouses_room_cartoons) }}</span>
                    <i class="feather-box" style="color: #c28a00; background: #daa5202e;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <h5 class="mb-4 mt-2">Product Suppliers List</h5>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card graph_card">
                <div class="card-body">
                    <h6 class="text-uppercase font-size-12 text-muted mb-3">Total Supplier Types</h6>
                    <span class="h3 mb-0">{{ number_format($total_product_suppliers) }}</span>
                    <i class="feather-type" style="color: #a60000; background: #a6000026;"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card graph_card">
                <div class="card-body">
                    <h6 class="text-uppercase font-size-12 text-muted mb-3">Total Product Suppliers</h6>
                    <span class="h3 mb-0">{{ number_format($total_product_suppliers) }}</span>
                    <i class="feather-users" style="color: #a60000; background: #a6000026;"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card graph_card">
                <div class="card-body">
                    <h6 class="text-uppercase font-size-12 text-muted mb-3">Total Active Product Suppliers</h6>
                    <span class="h3 mb-0">{{ number_format($total_active_product_suppliers) }}</span>
                    <i class="feather-clock" style="color: #a60000; background: #a6000026;"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card graph_card">
                <div class="card-body">
                    <h6 class="text-uppercase font-size-12 text-muted mb-3">Total InActive Product Suppliers</h6>
                    <span class="h3 mb-0">{{ number_format($total_inactive_product_suppliers) }}</span>
                    <i class="feather-clock" style="color: #a60000; background: #a6000026;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row mt-4">
        <!-- Customer Growth Chart (Last 30 Days) -->
        <div class="col-lg-6">
            <div class="card graph_card">
                <div class="card-body">
                    <h4 class="card-title">Purchase Orders (Last 30 Days)</h4>
                    <div class="chart-container">
                        <canvas id="customerGrowthChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Growth Chart (Last 6 Months) -->
        <div class="col-lg-6">
            <div class="card graph_card">
                <div class="card-body">
                    <h4 class="card-title">Purchase Orders (Last 6 Months)</h4>
                    <div class="chart-container">
                        <canvas id="customerGrowthSixMonthsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Customers Table -->
    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card graph_card">
                <div class="card-body">
                    <h4 class="card-title">Recent Purchase Orders</h4>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th class="text-center">SL</th>
                                    <th class="text-center">Warehouse</th>
                                    <th class="text-center">Room</th>
                                    <th class="text-center">Cartoon</th>
                                    <th class="text-center">Supplier</th>
                                    <th class="text-center">Quotation No</th>
                                    <th class="text-center">Purchase Date</th>
                                    <th class="text-center">Subtotal</th>
                                    <th class="text-center">Other Fee</th>
                                    <th class="text-center">Discount</th>
                                    <th class="text-center">Grand Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $sl = ($recent_purchase_orders->currentPage() - 1) * $recent_purchase_orders->perPage() + 1; @endphp
                                @foreach ($recent_purchase_orders as $item)
                                    <tr>
                                        <td class="text-center">{{ $sl++ }}</td>
                                        <td class="text-center">{{ $item->warehouse_name ?? 'N/A' }}</td>
                                        <td class="text-center">{{ $item->room_name ?? 'N/A' }}</td>
                                        <td class="text-center">{{ $item->cartoon_name ?? 'N/A' }}</td>
                                        <td class="text-center">{{ $item->supplier_name ?? 'N/A' }}</td>
                                        <td class="text-center">{{ $item->product_purchase_quotation_id ?? 'N/A' }}</td>
                                        <td class="text-center">{{ $item->date ?? 'N/A' }}</td>
                                        <td class="text-right">{{ number_format($item->subtotal, 2) }}</td>
                                        <td class="text-right">{{ number_format($item->other_charge_amount, 2) }}</td>
                                        <td class="text-right">{{ number_format($item->calculated_discount_amount, 2) }}</td>
                                        <td class="text-right">{{ number_format($item->total, 2) }}</td>
                                    </tr>
                                @endforeach

                                <tr>
                                    <th colspan="7" class="text-right">Total:</th>
                                    <th class="text-right">৳ {{ number_format($grandSubTotal, 2) }}</th>
                                    <th class="text-right">৳ {{ number_format($grandDeliveryFee, 2) }}</th>
                                    <th class="text-right">৳ {{ number_format($grandDiscount, 2) }}</th>
                                    <th class="text-right">৳ {{ number_format($grandTotal, 2) }}</th>
                                </tr>
                            </tbody>

                        </table>
                    </div>

                    <div class="mt-3 d-flex justify-content-end">
                        {{ $recent_purchase_orders->links() }} {{-- Pagination --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('footer_js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
    <script>
        const customerGrowthCtx = document.getElementById('customerGrowthChart').getContext('2d');
        const customerGrowthChart = new Chart(customerGrowthCtx, {
            type: 'line',
            data: {
                labels: @json($dailyCounts->keys()->map(function ($d) {
                    return \Carbon\Carbon::parse($d)->format('M d');
                })),
                datasets: [{
                    label: 'Customer Orders (Daily)',
                    data: @json($dailyCounts->values()),
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    borderColor: '#007bff',
                    borderWidth: 2,
                    pointRadius: 3,
                    pointBackgroundColor: '#007bff',
                    fill: true,
                    tension: 0.4,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Customer Growth - Last 30 Days',
                        font: {
                            size: 18
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Orders'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    }
                }
            }
        });

        const customerGrowthSixMonthsCtx = document.getElementById('customerGrowthSixMonthsChart').getContext('2d');
        const customerGrowthSixMonthsChart = new Chart(customerGrowthSixMonthsCtx, {
            type: 'bar',
            data: {
                labels: @json($monthlyCounts->keys()->map(function ($m) {
                    return \Carbon\Carbon::parse($m . '-01')->format('M Y');
                })),
                datasets: [{
                    label: 'Customer Orders (Monthly)',
                    data: @json($monthlyCounts->values()),
                    backgroundColor: 'rgba(23, 162, 184, 0.5)',
                    borderColor: '#17a2b8',
                    borderWidth: 1,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Customer Growth - Last 6 Months',
                        font: {
                            size: 18
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Orders'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Month'
                        }
                    }
                }
            }
        });
    </script>

@endsection