@extends('backend.master')

@section('header_css')
    <style>
        h4.card-title {
            background: linear-gradient(90deg,rgba(0, 179, 68, 1) 0%, rgba(1, 102, 85, 1) 100%);
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

        .nav-tabs .nav-link {
            border: none;
            color: #6c757d;
            margin-right: 1rem;
            margin-bottom: .5rem;
            border-radius: 3rem;
        }

        .nav-tabs .nav-link.active {
            background: teal;
            color: white;
            border-bottom: 2px solid teal;
        }
    </style>
@endsection

@section('page_title')
    Accounting Dashboard
@endsection

@section('page_heading')
    Financial Overview
@endsection

@section('content')
    <!-- Tabs for Last 30 Days and Last 12 Months -->
    <ul class="nav nav-tabs mb-4" id="dashboardTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="last30Days-tab" data-bs-toggle="tab" data-bs-target="#last30Days" type="button" role="tab" aria-controls="last30Days" aria-selected="true">Last 30 Days</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="last12Months-tab" data-bs-toggle="tab" data-bs-target="#last12Months" type="button" role="tab" aria-controls="last12Months" aria-selected="false">Last 12 Months</button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="dashboardTabsContent">
        <!-- Last 30 Days Tab -->
        <div class="tab-pane fade show active" id="last30Days" role="tabpanel" aria-labelledby="last30Days-tab">
            <div class="row">
                <!-- Key Metrics -->
                <div class="col-lg-3 col-md-6">
                    <div class="card graph_card">
                        <div class="card-body">
                            <h6 class="text-uppercase font-size-12 text-muted mb-3">Total Revenue (30 Days)</h6>
                            <span class="h3 mb-0">৳ {{ number_format($totalRevenue30Days, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card graph_card">
                        <div class="card-body">
                            <h6 class="text-uppercase font-size-12 text-muted mb-3">Total Expenses (30 Days)</h6>
                            <span class="h3 mb-0">৳ {{ number_format($totalExpenses30Days, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card graph_card">
                        <div class="card-body">
                            <h6 class="text-uppercase font-size-12 text-muted mb-3">Net Profit (30 Days)</h6>
                            <span class="h3 mb-0">৳ {{ number_format($netProfit30Days, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card graph_card">
                        <div class="card-body">
                            <h6 class="text-uppercase font-size-12 text-muted mb-3">Cash Flow (30 Days)</h6>
                            <span class="h3 mb-0">৳ {{ number_format(end($cashFlowNet30Days), 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Charts -->
                <div class="col-md-6">
                    <div class="card graph_card">
                        <div class="card-body">
                            <h6 class="text-uppercase font-size-12 text-muted mb-3">Income vs Expenses (30 Days)</h6>
                            <div class="chart-container">
                                <canvas id="incomeExpenseChart30Days"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card graph_card">
                        <div class="card-body">
                            <h6 class="text-uppercase font-size-12 text-muted mb-3">Cash Flow (30 Days)</h6>
                            <div class="chart-container">
                                <canvas id="cashFlowChart30Days"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Last 12 Months Tab -->
        <div class="tab-pane fade" id="last12Months" role="tabpanel" aria-labelledby="last12Months-tab">
            <div class="row">
                <!-- Key Metrics -->
                <div class="col-lg-3 col-md-6">
                    <div class="card graph_card">
                        <div class="card-body">
                            <h6 class="text-uppercase font-size-12 text-muted mb-3">Total Revenue (12 Months)</h6>
                            <span class="h3 mb-0">৳ {{ number_format($totalRevenue12Months, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card graph_card">
                        <div class="card-body">
                            <h6 class="text-uppercase font-size-12 text-muted mb-3">Total Expenses (12 Months)</h6>
                            <span class="h3 mb-0">৳ {{ number_format($totalExpenses12Months, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card graph_card">
                        <div class="card-body">
                            <h6 class="text-uppercase font-size-12 text-muted mb-3">Net Profit (12 Months)</h6>
                            <span class="h3 mb-0">৳ {{ number_format($netProfit12Months, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card graph_card">
                        <div class="card-body">
                            <h6 class="text-uppercase font-size-12 text-muted mb-3">Cash Flow (12 Months)</h6>
                            <span class="h3 mb-0">৳ {{ number_format(end($cashFlowNet12Months), 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Charts -->
                <div class="col-md-6">
                    <div class="card graph_card">
                        <div class="card-body">
                            <h6 class="text-uppercase font-size-12 text-muted mb-3">Income vs Expenses (12 Months)</h6>
                            <div class="chart-container">
                                <canvas id="incomeExpenseChart12Months"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card graph_card">
                        <div class="card-body">
                            <h6 class="text-uppercase font-size-12 text-muted mb-3">Cash Flow (12 Months)</h6>
                            <div class="chart-container">
                                <canvas id="cashFlowChart12Months"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Recent Transactions</h4>
                    <div class="table-responsive">
                        <table class="table table-centered table-striped table-nowrap">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Debit Account</th>
                                    <th>Credit Account</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTransactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->transaction_date }}</td>
                                    <td>{{ $transaction->payment_code }}</td>
                                    <td>{{ $transaction->debitAccount->account_name ?? 'N/A' }}</td>
                                    <td>{{ $transaction->creditAccount->account_name ?? 'N/A' }}</td>
                                    <td>৳ {{ number_format($transaction->debit_amt + $transaction->credit_amt, 2) }}</td>
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
    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        $(document).ready(function() {
            // Income vs Expenses Chart (30 Days)
            var incomeExpenseCtx30Days = document.getElementById('incomeExpenseChart30Days').getContext('2d');
            var incomeExpenseChart30Days = new Chart(incomeExpenseCtx30Days, {
                type: 'line',
                data: {
                    labels: @json($cashFlowLabels30Days),
                    datasets: [{
                        label: 'Income',
                        data: @json($cashFlowIncome30Days),
                        borderColor: '#2e7ce4',
                        fill: false
                    }, {
                        label: 'Expenses',
                        data: @json($cashFlowExpenses30Days),
                        borderColor: '#df3554',
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

            // Cash Flow Chart (30 Days)
            var cashFlowCtx30Days = document.getElementById('cashFlowChart30Days').getContext('2d');
            var cashFlowChart30Days = new Chart(cashFlowCtx30Days, {
                type: 'bar',
                data: {
                    labels: @json($cashFlowLabels30Days),
                    datasets: [{
                        label: 'Cash Flow',
                        data: @json($cashFlowNet30Days),
                        backgroundColor: '#00c2b2'
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

            // Income vs Expenses Chart (12 Months)
            var incomeExpenseCtx12Months = document.getElementById('incomeExpenseChart12Months').getContext('2d');
            var incomeExpenseChart12Months = new Chart(incomeExpenseCtx12Months, {
                type: 'line',
                data: {
                    labels: @json($cashFlowLabels12Months),
                    datasets: [{
                        label: 'Income',
                        data: @json($cashFlowIncome12Months),
                        borderColor: '#2e7ce4',
                        fill: false
                    }, {
                        label: 'Expenses',
                        data: @json($cashFlowExpenses12Months),
                        borderColor: '#df3554',
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

            // Cash Flow Chart (12 Months)
            var cashFlowCtx12Months = document.getElementById('cashFlowChart12Months').getContext('2d');
            var cashFlowChart12Months = new Chart(cashFlowCtx12Months, {
                type: 'bar',
                data: {
                    labels: @json($cashFlowLabels12Months),
                    datasets: [{
                        label: 'Cash Flow',
                        data: @json($cashFlowNet12Months),
                        backgroundColor: '#00c2b2'
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
        });
    </script>
@endsection