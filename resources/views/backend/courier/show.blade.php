@extends('backend.master')

@section('page_title')
    Courier Management
@endsection

@section('page_heading')
    Courier Order
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Courier Management</h4>
                    
                    <div class="alert alert-info">
                        <h5>Order Information</h5>
                        <p><strong>Order Code:</strong> {{ $order->order_code }}</p>
                        <p><strong>Order Date:</strong> {{ date('d M Y', strtotime($order->sale_date)) }}</p>
                        <p><strong>Customer:</strong> {{ $order->customer->name ?? 'N/A' }}</p>
                        <p><strong>Total Amount:</strong> ৳ {{ number_format($order->total, 2) }}</p>
                        <p><strong>Status:</strong> 
                            <span class="badge badge-{{ $order->is_couriered == 1 ? 'success' : 'warning' }}">
                                {{ $order->is_couriered == 1 ? 'Couriered' : 'Not Couriered' }}
                            </span>
                        </p>
                    </div>

                    <div class="mt-4">
                        <p class="text-muted">Courier functionality will be implemented here.</p>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('ViewAllProductOrder') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Orders
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

