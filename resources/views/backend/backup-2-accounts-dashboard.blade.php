@extends('backend.master')

@section('header_css')
    <style>
        h4.card-title {
            background: linear-gradient(to right, #17263A, #2c3e50, #17263A);
            padding: 8px 15px;
            border-radius: 4px;
            color: white;
        }

        .graph_card {
            position: relative;
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

        .chart-container {
            height: 300px;
        }
    </style>
@endsection

@section('page_title')
    Accounts Dashboard
@endsection

@section('page_heading')
    Overview
@endsection

@section('content')
    <div class="row">
        <!-- Other Cards -->
        <div class="col-lg-6 col-xl-3">
            <div class="card graph_card">
                <div class="card-body">
                    <h6 class="text-uppercase font-size-12 text-muted mb-3">Total Amount</h6>
                    <span class="h3 mb-0">৳ 1,234,567.89</span>
                    <div id="sparkline1" class="mt-3"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-xl-3">
            <div class="card graph_card">
                <div class="card-body">
                    <h6 class="text-uppercase font-size-12 text-muted mb-3">Total Paid</h6>
                    <span class="h3 mb-0">৳ 987,654.32</span>
                    <div id="sparkline2" class="mt-3"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-xl-3">
            <div class="card graph_card">
                <div class="card-body">
                    <h6 class="text-uppercase font-size-12 text-muted mb-3">Total Due</h6>
                    <span class="h3 mb-0">৳ 987,654.32</span>
                    <div id="sparkline3" class="mt-3"></div>
                </div>
            </div>
        </div>


        <div class="col-lg-6 col-xl-3">
            <div class="card graph_card">
                <div class="card-body">
                    <h6 class="text-uppercase font-size-12 text-muted mb-3">Total Sales</h6>
                    <span class="h3 mb-0">৳ 1,234,567.89</span>
                    <div id="sparkline4" class="mt-3"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-xl-3">
            <div class="card graph_card">
                <div class="card-body">
                    <h6 class="text-uppercase font-size-12 text-muted mb-3">Net Profit</h6>
                    <span class="h3 mb-0">৳ 987,654.32</span>
                    <div id="sparkline5" class="mt-3"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-xl-3">
            <div class="card graph_card">
                <div class="card-body">
                    <h6 class="text-uppercase font-size-12 text-muted mb-3">Gross Profit</h6>
                    <span class="h3 mb-0">৳ 987,654.32</span>
                    <div id="sparkline6" class="mt-3"></div>
                </div>
            </div>
        </div>


        <div class="col-lg-6 col-xl-3">
            <div class="card graph_card">
                <div class="card-body">
                    <h6 class="text-uppercase font-size-12 text-muted mb-3">Sales Return</h6>
                    <span class="h3 mb-0">৳ 987,654.32</span>
                    <div id="sparkline7" class="mt-3"></div>
                </div>
            </div>
        </div>


        <div class="col-lg-6 col-xl-3">
            <div class="card graph_card">
                <div class="card-body">
                    <h6 class="text-uppercase font-size-12 text-muted mb-3">Purchase</h6>
                    <span class="h3 mb-0">৳ 987,654.32</span>
                    <div id="sparkline8" class="mt-3"></div>
                </div>
            </div>
        </div>


        <div class="col-lg-6 col-xl-3">
            <div class="card graph_card">
                <div class="card-body">
                    <h6 class="text-uppercase font-size-12 text-muted mb-3">Purchase Due</h6>
                    <span class="h3 mb-0">৳ 987,654.32</span>
                    <div id="sparkline9" class="mt-3"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-xl-3">
            <div class="card graph_card">
                <div class="card-body">
                    <h6 class="text-uppercase font-size-12 text-muted mb-3">Expense</h6>
                    <span class="h3 mb-0">৳ 987,654.32</span>
                    <div id="sparkline10" class="mt-3"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-xl-3">
            <div class="card graph_card">
                <div class="card-body">
                    <h6 class="text-uppercase font-size-12 text-muted mb-3">Salary Cost</h6>
                    <span class="h3 mb-0">৳ 987,654.32</span>
                    <div id="sparkline11" class="mt-3"></div>
                </div>
            </div>
        </div>


    </div>



    <div class="row">
        <!-- Income Overview Card -->
        <div class="col-md-6 col-lg-6 col-xl-6">
            <div class="card graph_card">
                <div class="card-body">
                    <h6 class="text-uppercase font-size-12 text-muted mb-3">Income Overview</h6>
                    <div class="chart-container">
                        <canvas id="incomeOverviewChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Purchase and Sale Flow Card -->
        <div class="col-md-6 col-lg-6 col-xl-6">
            <div class="card graph_card">
                <div class="card-body">
                    <h6 class="text-uppercase font-size-12 text-muted mb-3">Purchase and Sale Flow</h6>
                    <div class="chart-container">
                        <canvas id="purchaseSaleFlowChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Sales Analytics</h4>
                    <div class="chart-container">
                        <canvas id="salesAnalyticsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Expense Distribution</h4>
                    <div class="chart-container">
                        <canvas id="expenseDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6">
            <div class="card">
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
                                <tr>
                                    <td>John Doe</td>
                                    <td>123-456-7890</td>
                                    <td>john@example.com</td>
                                    <td>New York, USA</td>
                                    <td>2023-10-01 14:30:00</td>
                                </tr>
                                <tr>
                                    <td>Jane Smith</td>
                                    <td>987-654-3210</td>
                                    <td>jane@example.com</td>
                                    <td>London, UK</td>
                                    <td>2023-10-02 10:15:00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Account Transactions</h4>
                    <div class="table-responsive">
                        <table class="table table-centered table-striped table-nowrap">
                            <thead>
                                <tr>
                                    <th>TRX ID</th>
                                    <th>Amount</th>
                                    <th>Card Type/Brand</th>
                                    <th>Payment Through</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>TRX123456</td>
                                    <td>৳ 1,000.00</td>
                                    <td>Visa</td>
                                    <td>Online</td>
                                </tr>
                                <tr>
                                    <td>TRX654321</td>
                                    <td>৳ 2,500.00</td>
                                    <td>MasterCard</td>
                                    <td>In-Store</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    <script>
        $(document).ready(function() {

            var DrawSparkline = function() {
                $('#sparkline1').sparkline([{{ $countOrders[8] }}, {{ $countOrders[7] }},
                    {{ $countOrders[6] }}, {{ $countOrders[5] }}, {{ $countOrders[4] }},
                    {{ $countOrders[3] }}, {{ $countOrders[2] }}, {{ $countOrders[1] }},
                    {{ $countOrders[0] }}
                ], {
                    type: 'line',
                    width: "100%",
                    height: '50',
                    chartRangeMax: 35,
                    lineColor: '#2e7ce4',
                    fillColor: 'rgba(46, 124, 228, 0.3)',
                    highlightLineColor: 'rgba(0,0,0,.1)',
                    highlightSpotColor: 'rgba(0,0,0,.2)',
                    maxSpotColor: false,
                    minSpotColor: false,
                    spotColor: false,
                    lineWidth: 1
                });

                $('#sparkline2').sparkline([{{ $totalOrderAmount[8] }}, {{ $totalOrderAmount[7] }},
                    {{ $totalOrderAmount[6] }}, {{ $totalOrderAmount[5] }},
                    {{ $totalOrderAmount[4] }}, {{ $totalOrderAmount[3] }},
                    {{ $totalOrderAmount[2] }}, {{ $totalOrderAmount[1] }},
                    {{ $totalOrderAmount[0] }}
                ], {
                    type: 'line',
                    width: "100%",
                    height: '50',
                    chartRangeMax: 35,
                    lineColor: '#627898',
                    fillColor: 'rgba(98, 120, 152, 0.3)',
                    highlightLineColor: 'rgba(0,0,0,.1)',
                    highlightSpotColor: 'rgba(0,0,0,.2)',
                    maxSpotColor: false,
                    minSpotColor: false,
                    spotColor: false,
                    lineWidth: 1
                });

                $('#sparkline3').sparkline([{{ $todaysOrder[8] }}, {{ $todaysOrder[7] }},
                    {{ $todaysOrder[6] }}, {{ $todaysOrder[5] }}, {{ $todaysOrder[4] }},
                    {{ $todaysOrder[3] }}, {{ $todaysOrder[2] }}, {{ $todaysOrder[1] }},
                    {{ $todaysOrder[0] }}
                ], {
                    type: 'line',
                    width: "100%",
                    height: '50',
                    chartRangeMax: 35,
                    lineColor: '#f1bf43',
                    fillColor: 'rgba(241, 191, 67, 0.3)',
                    highlightLineColor: 'rgba(0,0,0,.1)',
                    highlightSpotColor: 'rgba(0,0,0,.2)',
                    maxSpotColor: false,
                    minSpotColor: false,
                    spotColor: false,
                    lineWidth: 1
                });

                $('#sparkline4').sparkline([{{ $registeredUsers[8] }}, {{ $registeredUsers[7] }},
                    {{ $registeredUsers[6] }}, {{ $registeredUsers[5] }}, {{ $registeredUsers[4] }},
                    {{ $registeredUsers[3] }}, {{ $registeredUsers[2] }}, {{ $registeredUsers[1] }},
                    {{ $registeredUsers[0] }}
                ], {
                    type: 'line',
                    width: "100%",
                    height: '50',
                    chartRangeMax: 35,
                    lineColor: '#df3554',
                    fillColor: 'rgba(223, 53, 84, 0.3)',
                    highlightLineColor: 'rgba(0,0,0,.1)',
                    highlightSpotColor: 'rgba(0,0,0,.2)',
                    maxSpotColor: false,
                    minSpotColor: false,
                    spotColor: false,
                    lineWidth: 1
                });
                $('#sparkline5').sparkline([{{ $countOrders[8] }}, {{ $countOrders[7] }},
                    {{ $countOrders[6] }}, {{ $countOrders[5] }}, {{ $countOrders[4] }},
                    {{ $countOrders[3] }}, {{ $countOrders[2] }}, {{ $countOrders[1] }},
                    {{ $countOrders[0] }}
                ], {
                    type: 'line',
                    width: "100%",
                    height: '50',
                    chartRangeMax: 35,
                    lineColor: '#2e7ce4',
                    fillColor: 'rgba(46, 124, 228, 0.3)',
                    highlightLineColor: 'rgba(0,0,0,.1)',
                    highlightSpotColor: 'rgba(0,0,0,.2)',
                    maxSpotColor: false,
                    minSpotColor: false,
                    spotColor: false,
                    lineWidth: 1
                });

                $('#sparkline6').sparkline([{{ $totalOrderAmount[8] }}, {{ $totalOrderAmount[7] }},
                    {{ $totalOrderAmount[6] }}, {{ $totalOrderAmount[5] }},
                    {{ $totalOrderAmount[4] }}, {{ $totalOrderAmount[3] }},
                    {{ $totalOrderAmount[2] }}, {{ $totalOrderAmount[1] }},
                    {{ $totalOrderAmount[0] }}
                ], {
                    type: 'line',
                    width: "100%",
                    height: '50',
                    chartRangeMax: 35,
                    lineColor: '#627898',
                    fillColor: 'rgba(98, 120, 152, 0.3)',
                    highlightLineColor: 'rgba(0,0,0,.1)',
                    highlightSpotColor: 'rgba(0,0,0,.2)',
                    maxSpotColor: false,
                    minSpotColor: false,
                    spotColor: false,
                    lineWidth: 1
                });

                $('#sparkline7').sparkline([{{ $todaysOrder[8] }}, {{ $todaysOrder[7] }},
                    {{ $todaysOrder[6] }}, {{ $todaysOrder[5] }}, {{ $todaysOrder[4] }},
                    {{ $todaysOrder[3] }}, {{ $todaysOrder[2] }}, {{ $todaysOrder[1] }},
                    {{ $todaysOrder[0] }}
                ], {
                    type: 'line',
                    width: "100%",
                    height: '50',
                    chartRangeMax: 35,
                    lineColor: '#f1bf43',
                    fillColor: 'rgba(241, 191, 67, 0.3)',
                    highlightLineColor: 'rgba(0,0,0,.1)',
                    highlightSpotColor: 'rgba(0,0,0,.2)',
                    maxSpotColor: false,
                    minSpotColor: false,
                    spotColor: false,
                    lineWidth: 1
                });

                $('#sparkline8').sparkline([{{ $registeredUsers[8] }}, {{ $registeredUsers[7] }},
                    {{ $registeredUsers[6] }}, {{ $registeredUsers[5] }}, {{ $registeredUsers[4] }},
                    {{ $registeredUsers[3] }}, {{ $registeredUsers[2] }}, {{ $registeredUsers[1] }},
                    {{ $registeredUsers[0] }}
                ], {
                    type: 'line',
                    width: "100%",
                    height: '50',
                    chartRangeMax: 35,
                    lineColor: '#df3554',
                    fillColor: 'rgba(223, 53, 84, 0.3)',
                    highlightLineColor: 'rgba(0,0,0,.1)',
                    highlightSpotColor: 'rgba(0,0,0,.2)',
                    maxSpotColor: false,
                    minSpotColor: false,
                    spotColor: false,
                    lineWidth: 1
                });

                $('#sparkline9').sparkline([{{ $countOrders[8] }}, {{ $countOrders[7] }},
                    {{ $countOrders[6] }}, {{ $countOrders[5] }}, {{ $countOrders[4] }},
                    {{ $countOrders[3] }}, {{ $countOrders[2] }}, {{ $countOrders[1] }},
                    {{ $countOrders[0] }}
                ], {
                    type: 'line',
                    width: "100%",
                    height: '50',
                    chartRangeMax: 35,
                    lineColor: '#2e7ce4',
                    fillColor: 'rgba(46, 124, 228, 0.3)',
                    highlightLineColor: 'rgba(0,0,0,.1)',
                    highlightSpotColor: 'rgba(0,0,0,.2)',
                    maxSpotColor: false,
                    minSpotColor: false,
                    spotColor: false,
                    lineWidth: 1
                });


                $('#sparkline10').sparkline([{{ $totalOrderAmount[8] }}, {{ $totalOrderAmount[7] }},
                    {{ $totalOrderAmount[6] }}, {{ $totalOrderAmount[5] }},
                    {{ $totalOrderAmount[4] }}, {{ $totalOrderAmount[3] }},
                    {{ $totalOrderAmount[2] }}, {{ $totalOrderAmount[1] }},
                    {{ $totalOrderAmount[0] }}
                ], {
                    type: 'line',
                    width: "100%",
                    height: '50',
                    chartRangeMax: 35,
                    lineColor: '#627898',
                    fillColor: 'rgba(98, 120, 152, 0.3)',
                    highlightLineColor: 'rgba(0,0,0,.1)',
                    highlightSpotColor: 'rgba(0,0,0,.2)',
                    maxSpotColor: false,
                    minSpotColor: false,
                    spotColor: false,
                    lineWidth: 1
                });

                $('#sparkline11').sparkline([{{ $todaysOrder[8] }}, {{ $todaysOrder[7] }},
                    {{ $todaysOrder[6] }}, {{ $todaysOrder[5] }}, {{ $todaysOrder[4] }},
                    {{ $todaysOrder[3] }}, {{ $todaysOrder[2] }}, {{ $todaysOrder[1] }},
                    {{ $todaysOrder[0] }}
                ], {
                    type: 'line',
                    width: "100%",
                    height: '50',
                    chartRangeMax: 35,
                    lineColor: '#f1bf43',
                    fillColor: 'rgba(241, 191, 67, 0.3)',
                    highlightLineColor: 'rgba(0,0,0,.1)',
                    highlightSpotColor: 'rgba(0,0,0,.2)',
                    maxSpotColor: false,
                    minSpotColor: false,
                    spotColor: false,
                    lineWidth: 1
                });
            };

            DrawSparkline();

            var resizeChart;

            $(window).resize(function(e) {
                clearTimeout(resizeChart);
                resizeChart = setTimeout(function() {
                    DrawSparkline();
                }, 300);
            });
        });
    </script>


    <script>
        $(document).ready(function() {
            // Income Overview Chart
            var incomeCtx = document.getElementById('incomeOverviewChart').getContext('2d');
            var incomeChart = new Chart(incomeCtx, {
                type: 'line',
                data: {
                    labels: ['Feb 01', 'Feb 02', 'Feb 03', 'Feb 04', 'Feb 05', 'Feb 06', 'Feb 07', 'Feb 08',
                        'Feb 09', 'Feb 10', 'Feb 11', 'Feb 12', 'Feb 13', 'Feb 14', 'Feb 15', 'Feb 16',
                        'Feb 17', 'Feb 18', 'Feb 19', 'Feb 20', 'Feb 21', 'Feb 22', 'Feb 23', 'Feb 24',
                        'Feb 25', 'Feb 26', 'Feb 27', 'Feb 28'
                    ],
                    datasets: [{
                        label: 'Income',
                        data: [7000000, 6000000, 5000000, 4000000, 3000000, 2000000, 1000000, 0, 0,
                            0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0
                        ],
                        borderColor: '#2e7ce4',
                        fill: false
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

            // Purchase and Sale Flow Chart
            var purchaseSaleCtx = document.getElementById('purchaseSaleFlowChart').getContext('2d');
            var purchaseSaleChart = new Chart(purchaseSaleCtx, {
                type: 'bar',
                data: {
                    labels: ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec',
                        'Jan'
                    ],
                    datasets: [{
                        label: 'Sales',
                        data: [320000, 240000, 160000, 80000, 0, 0, 0, 0, 0, 0, 0, 0],
                        backgroundColor: '#00C2B2'
                    }, {
                        label: 'Purchase',
                        data: [160000, 120000, 80000, 40000, 0, 0, 0, 0, 0, 0, 0, 0],
                        backgroundColor: '#DF3554'
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

            // Sales Analytics Chart
            var salesCtx = document.getElementById('salesAnalyticsChart').getContext('2d');
            var salesChart = new Chart(salesCtx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov',
                        'Dec'
                    ],
                    datasets: [{
                        label: 'Successful',
                        data: [120, 150, 180, 90, 110, 130, 160, 140, 170, 150, 130, 110],
                        backgroundColor: '#00C2B2'
                    }, {
                        label: 'Failed',
                        data: [20, 30, 40, 25, 35, 45, 50, 40, 30, 20, 15, 10],
                        backgroundColor: '#DF3554'
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

            // Expense Distribution Chart
            var expenseCtx = document.getElementById('expenseDistributionChart').getContext('2d');
            var expenseChart = new Chart(expenseCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Marketing', 'Operations', 'Salaries'],
                    datasets: [{
                        data: [30000, 50000, 70000],
                        backgroundColor: ['#2e7ce4', '#00c2b2', '#df3554']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        });
    </script>
@endsection
