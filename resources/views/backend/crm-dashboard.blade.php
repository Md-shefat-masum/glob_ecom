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
    CRM Dashboard
@endsection

@section('page_heading')
    Overview
@endsection

@section('content')
    <div class="row">
        <!-- Key Metrics Cards -->
        <div class="col-lg-3 col-md-6">
            <div class="card graph_card">
                <div class="card-body">
                    <h6 class="text-uppercase font-size-12 text-muted mb-3">Total Customers(Our Suppliers)</h6>
                    <span class="h3 mb-0">{{ number_format($totalCustomers) }}</span>
                    <i class="feather-users" style="color: #0074E4; background: #0074E42E;"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card graph_card">
                <div class="card-body">
                    <h6 class="text-uppercase font-size-12 text-muted mb-3">Last 30 Days</h6>
                    <span class="h3 mb-0">{{ number_format($totalLastThirtyDaysCustomers) }}</span>
                    <i class="feather-trending-up" style="color: #17263A; background: #17263A3D;"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card graph_card">
                <div class="card-body">
                    <h6 class="text-uppercase font-size-12 text-muted mb-3">Last 6 Months</h6>
                    <span class="h3 mb-0">{{ number_format($totalLastSixMonthsCustomers) }}</span>
                    <i class="feather-calendar" style="color: #c28a00; background: #daa5202e;"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card graph_card">
                <div class="card-body">
                    <h6 class="text-uppercase font-size-12 text-muted mb-3">This Month</h6>
                    <span class="h3 mb-0">{{ number_format($totalThisMonthCustomers) }}</span>
                    <i class="feather-clock" style="color: #a60000; background: #a6000026;"></i>
                </div>
            </div>
        </div>
        
    </div>


    <div class="row">
        <div class="col-12">
            <h5 class="mb-4 mt-2">Upcoming Schedule</h5>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card graph_card">
                <div class="card-body">
                    <h6 class="text-uppercase font-size-12 text-muted mb-3">Upcoming Contacts</h6>
                    <span class="h3 mb-0">{{ number_format($upcomingContactCustomers) }}</span>
                    <i class="feather-clock" style="color: #a60000; background: #a6000026;"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card graph_card">
                <div class="card-body">
                    <h6 class="text-uppercase font-size-12 text-muted mb-3">Pending Contacts</h6>
                    <span class="h3 mb-0">{{ number_format($pendingContactCustomers) }}</span>
                    <i class="feather-clock" style="color: #a60000; background: #a6000026;"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card graph_card">
                <div class="card-body">
                    <h6 class="text-uppercase font-size-12 text-muted mb-3">Missed Contacts</h6>
                    <span class="h3 mb-0">{{ number_format($missedContactCustomers) }}</span>
                    <i class="feather-clock" style="color: #a60000; background: #a6000026;"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card graph_card">
                <div class="card-body">
                    <h6 class="text-uppercase font-size-12 text-muted mb-3">Completed Contacts</h6>
                    <span class="h3 mb-0">{{ number_format($doneContactCustomers) }}</span>
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
                    <h4 class="card-title">Customer Growth (Last 30 Days)</h4>
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
                    <h4 class="card-title">Customer Growth (Last 6 Months)</h4>
                    <div class="chart-container">
                        <canvas id="customerGrowthSixMonthsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Charts -->
    <div class="row mt-4">

        <!-- Customer Source Distribution Chart -->
        <div class="col-lg-6">
            <div class="card graph_card">
                <div class="card-body">
                    <h4 class="card-title">Customer Source Distribution</h4>
                    <div class="chart-container">
                        <canvas id="customerSourceChart"></canvas>
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
                    <h4 class="card-title">Recent Customers</h4>
                    <div class="table-responsive">
                        <table class="table table-centered table-striped table-nowrap">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Location</th>
                                    <th>Create Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentCustomers as $customer)
                                    <tr>
                                        <td>{{ $customer->name }}</td>
                                        <td>{{ $customer->phone }}</td>
                                        <td>{{ $customer->email }}</td>
                                        <td>{{ $customer->address }}</td>
                                        <td>{{ $customer->created_at ? $customer->created_at->format('M d, Y') : 'N/A' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
    <script>
        // Customer Growth Chart (Last 30 Days)
        const customerGrowthCtx = document.getElementById('customerGrowthChart').getContext('2d');
        const customerGrowthChart = new Chart(customerGrowthCtx, {
            type: 'line',
            data: {
                labels: @json($customerGrowth->pluck('date')),
                datasets: [{
                    label: 'Customers',
                    data: @json($customerGrowth->pluck('count')),
                    backgroundColor: 'rgba(0, 116, 228, 0.2)',
                    borderColor: '#0074E4',
                    borderWidth: 2,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Customer Growth Chart (Last 6 Months)
        // Customer Growth Chart (Last 6 Months)
        const customerGrowthSixMonthsCtx = document.getElementById('customerGrowthSixMonthsChart').getContext('2d');
        const customerGrowthSixMonthsChart = new Chart(customerGrowthSixMonthsCtx, {
            type: 'line',
            data: {
                labels: @json($customerGrowthSixMonths->pluck('month')),
                datasets: [{
                    label: 'Customers',
                    data: @json($customerGrowthSixMonths->pluck('count')),
                    backgroundColor: 'rgba(23, 38, 58, 0.2)',
                    borderColor: '#17263A',
                    borderWidth: 2,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });


        // Customer Source Distribution Chart
        const customerSourceCtx = document.getElementById('customerSourceChart').getContext('2d');
        const customerSourceChart = new Chart(customerSourceCtx, {
            type: 'bar',
            data: {
                labels: @json($customerSourceDistribution->pluck('title')),
                datasets: [{
                    label: 'Customers',
                    data: @json($customerSourceDistribution->pluck('customers_count')),
                    backgroundColor: '#0074E4',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endsection
