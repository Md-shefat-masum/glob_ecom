@extends('backend.master')

@section('header_css')
    <link href="{{ url('assets') }}/plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />
    <style>
        .stat-card { min-width: 140px; }
        .select2-container { width: 100% !important; }
    </style>
@endsection

@section('page_title')
    Sales Targets
@endsection
@section('page_heading')
    Sales Targets
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif
            {{-- Filters --}}
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <label>From Date</label>
                            <input type="date" id="filter_from" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>To Date</label>
                            <input type="date" id="filter_to" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>Employee</label>
                            <select id="filter_employee" class="form-control">
                                <option value="">All</option>
                                @foreach($filterEmployees as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="button" id="btn_analytics" class="btn btn-primary">Apply Filter</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Analytics cards --}}
            <div class="row mb-3" id="analytics_cards">
                <div class="col-md-3">
                    <div class="card stat-card border-primary">
                        <div class="card-body">
                            <h6 class="text-muted mb-1">Total Employee</h6>
                            <h4 id="stat_employee">0</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card border-info">
                        <div class="card-body">
                            <h6 class="text-muted mb-1">Total Targets</h6>
                            <h4 id="stat_targets">0</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card border-success">
                        <div class="card-body">
                            <h6 class="text-muted mb-1">Sales</h6>
                            <h4 id="stat_sales">0</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card border-warning">
                        <div class="card-body">
                            <h6 class="text-muted mb-1">Achieve %</h6>
                            <h4 id="stat_achieve">0%</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">All Targets</h4>
                        <a href="{{ route('sales_targets.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Target</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Employee</th>
                                    <th class="text-center">Target fillup %</th>
                                    <th class="text-center">Target</th>
                                    <th class="text-center">Completed</th>
                                    <th class="text-center">Remain</th>
                                    <th class="text-center">Is Evaluated</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($targets as $t)
                                    @php
                                        $fillPercent = $t->target > 0 ? round(($t->completed / $t->target) * 100, 1) : 0;
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $t->date->format('Y-m-d') }}</td>
                                        <td class="text-center">{{ $t->user ? $t->user->name : '—' }}</td>
                                        <td class="text-center">{{ $fillPercent }}%</td>
                                        <td class="text-center">{{ number_format($t->target, 2) }}</td>
                                        <td class="text-center">{{ number_format($t->completed, 2) }}</td>
                                        <td class="text-center">{{ number_format($t->remains, 2) }}</td>
                                        <td class="text-center">{{ $t->is_evaluated ? 'Yes' : 'No' }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('sales_targets.show', $t->id) }}" class="btn btn-sm btn-info">View</a>
                                            <a href="{{ route('sales_targets.edit', $t->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="text-center">No targets found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($targets->hasPages())
                        <div class="mt-3">{{ $targets->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_js')
    <script src="{{ url('assets') }}/plugins/select2/select2.min.js"></script>
    <script>
        $(function() {
            $('#filter_employee').select2({ width: '100%' });

            function loadAnalytics() {
                var from = $('#filter_from').val();
                var to = $('#filter_to').val();
                var userId = $('#filter_employee').val();
                var params = {};
                if (from) params.from_date = from;
                if (to) params.to_date = to;
                if (userId) params.user_id = userId;

                $.get("{{ route('sales_targets.analytics') }}", params)
                    .done(function(r) {
                        if (r.success && r.data) {
                            $('#stat_employee').text(r.data.total_employee);
                            $('#stat_targets').text(parseFloat(r.data.total_targets).toFixed(2));
                            $('#stat_sales').text(parseFloat(r.data.sales).toFixed(2));
                            $('#stat_achieve').text(r.data.achieve_percent + '%');
                        }
                    })
                    .fail(function() {
                        $('#stat_employee, #stat_targets, #stat_sales').text('0');
                        $('#stat_achieve').text('0%');
                    });
            }

            $('#btn_analytics').on('click', loadAnalytics);
            loadAnalytics();
        });
    </script>
@endsection
