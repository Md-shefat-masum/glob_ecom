@extends('backend.master')

@section('title', 'Stock Adjustment Logs')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">
                    <i class="feather-clipboard text-primary me-2"></i>
                    Stock Adjustment Logs
                </h4>
                <div class="page-title-right">
                    <a href="{{ route('stock-adjustment.create') }}" class="btn btn-primary">
                        <i class="feather-plus-circle me-1"></i> New Adjustment
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-success bg-soft">
                                <span class="avatar-title rounded-circle bg-success bg-opacity-10">
                                    <i class="feather-plus-circle fs-4 text-white"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1">Total Additions</p>
                            <h5 class="mb-0">{{ DB::table('product_stock_logs')->whereIn('type', ['purchase', 'return', 'initial', 'manual add'])->count() }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-danger bg-soft">
                                <span class="avatar-title rounded-circle bg-danger bg-opacity-10">
                                    <i class="feather-minus-circle fs-4 text-white"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1">Total Subtractions</p>
                            <h5 class="mb-0">{{ DB::table('product_stock_logs')->whereIn('type', ['sales', 'waste', 'transfer'])->count() }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-info bg-soft">
                                <span class="avatar-title rounded-circle bg-info bg-opacity-10">
                                    <i class="feather-package fs-4 text-white"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1">Total Products</p>
                            <h5 class="mb-0">{{ DB::table('product_stock_logs')->distinct('product_id')->count('product_id') }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm rounded-circle bg-warning bg-soft">
                                <span class="avatar-title rounded-circle bg-warning bg-opacity-10">
                                    <i class="feather-calendar fs-4 text-white"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-muted mb-1">Today's Adjustments</p>
                            <h5 class="mb-0">{{ DB::table('product_stock_logs')->whereDate('created_at', today())->count() }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Logs Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="feather-clock text-primary me-2"></i>
                        Stock Adjustment History
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="80">ID</th>
                                    <th>Product</th>
                                    <th width="150">Type</th>
                                    <th width="120">Quantity</th>
                                    <th>Variant Info</th>
                                    <th>Description</th>
                                    <th width="150">Date</th>
                                    <th width="100" class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary">#{{ $log->id }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <strong class="text-dark">{{ $log->product_name }}</strong>
                                            @if($log->product_code)
                                            <small class="text-muted">Code: {{ $log->product_code }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $typeColors = [
                                                'purchase' => 'success',
                                                'return' => 'info',
                                                'initial' => 'primary',
                                                'manual add' => 'success',
                                                'sales' => 'danger',
                                                'waste' => 'dark',
                                                'transfer' => 'warning'
                                            ];
                                            $color = $typeColors[$log->type] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $color }}">
                                            <i class="feather-{{ in_array($log->type, ['sales', 'waste', 'transfer']) ? 'minus' : 'plus' }}-circle me-1"></i>
                                            {{ ucfirst($log->type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $log->quantity >= 0 ? 'bg-success' : 'bg-danger' }} fs-6">
                                            {{ $log->quantity >= 0 ? '+' : '' }}{{ $log->quantity }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($log->has_variant)
                                            <div class="d-flex flex-column">
                                                <span class="badge bg-info mb-1">{{ $log->variant_combination_key }}</span>
                                                @if($log->variant_sku)
                                                <small class="text-muted">SKU: {{ $log->variant_sku }}</small>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted small">Single Product</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $log->description ?? '-' }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="text-dark">{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y') }}</span>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($log->created_at)->format('h:i A') }}</small>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($log->status == 1)
                                        <span class="badge bg-success">Active</span>
                                        @else
                                        <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="feather-inbox fa-3x text-muted mb-3 d-block"></i>
                                        <p class="text-muted">No stock adjustment logs found.</p>
                                        <a href="{{ route('stock-adjustment.create') }}" class="btn btn-primary">
                                            <i class="feather-plus-circle me-1"></i> Create First Adjustment
                                        </a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($logs->hasPages())
                <div class="card-footer bg-white border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-0">
                                Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }} entries
                            </p>
                        </div>
                        <div>
                            {{ $logs->links() }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .avatar-sm {
        width: 3rem;
        height: 3rem;
    }
    .avatar-title {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
    }
</style>
@endpush

