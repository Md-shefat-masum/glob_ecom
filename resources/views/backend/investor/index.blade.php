@extends('backend.master')

@section('header_css')
    <link href="{{ url('dataTable') }}/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="{{ url('dataTable') }}/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <style>
        table.dataTable tbody td {
            vertical-align: middle;
        }
        table.dataTable tbody td img {
            margin: 0 auto;
        }
        .analytics-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .analytics-card h5 {
            color: white;
            margin-bottom: 10px;
            font-size: 14px;
            font-weight: 500;
            opacity: 0.9;
        }
        .analytics-card .amount {
            font-size: 28px;
            font-weight: bold;
            color: white;
        }
        .analytics-card.card-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .analytics-card.card-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        .analytics-card.card-danger {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .analytics-card.card-info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        .analytics-card.card-warning {
            background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
        }
    </style>
@endsection

@section('page_title')
    Investor Management
@endsection
@section('page_heading')
    View All Investors
@endsection

@section('content')
    <!-- Analytics Section -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="analytics-card card-primary">
                <h5><i class="fas fa-users"></i> Total Investors</h5>
                <div class="amount">{{ $totalInvestors }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="analytics-card card-success">
                <h5><i class="fas fa-arrow-down"></i> Total Deposits</h5>
                <div class="amount">৳ {{ number_format($totalDeposits, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="analytics-card card-danger">
                <h5><i class="fas fa-arrow-up"></i> Total Withdraws</h5>
                <div class="amount">৳ {{ number_format($totalWithdraws, 2) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="analytics-card card-info">
                <h5><i class="fas fa-wallet"></i> Net Balance</h5>
                <div class="amount" style="color: {{ $netBalance >= 0 ? '#fff' : '#ffeb3b' }};">
                    ৳ {{ number_format($netBalance, 2) }}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="analytics-card card-warning">
                <h5><i class="fas fa-percentage"></i> Average Profit Ratio</h5>
                <div class="amount">{{ number_format($averageProfitRatio, 2) }}%</div>
                <small style="opacity: 0.8;">Weighted by investment amount</small>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-3">Investor List</h4>
                        <a href="{{ route('CreateInvestor') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Investor
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table id="investorTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Start Date</th>
                                    <th>Profit Ratio</th>
                                    <th>Deposits</th>
                                    <th>Withdraws</th>
                                    <th>Balance</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_js')
    <script src="{{ url('dataTable') }}/js/jquery.dataTables.min.js"></script>
    <script src="{{ url('dataTable') }}/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#investorTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('ViewAllInvestor') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'image', name: 'image', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'phone', name: 'phone' },
                    { data: 'start_date', name: 'start_date', orderable: false, searchable: false },
                    { data: 'profit_ratio', name: 'profit_ratio', orderable: false, searchable: false },
                    { data: 'deposits', name: 'deposits', orderable: false, searchable: false },
                    { data: 'withdraws', name: 'withdraws', orderable: false, searchable: false },
                    { data: 'balance', name: 'balance', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[2, 'asc']]
            });
        });
    </script>
@endsection

