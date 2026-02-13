@extends('backend.master')

@section('header_css')
    <style>
        .customer-info-card {
            background: #e8f5e9;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #4CAF50;
        }
        .payment-in {
            color: #28a745;
        }
        .payment-out {
            color: #dc3545;
        }
        .summary-card {
            background: #fff3e0;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
@endsection

@section('page_title')
    Customer Payment History
@endsection

@section('page_heading')
    Payment History for {{ $customer->name }}
@endsection

@section('content')
    <div class="container" style="max-width: 1200px;">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4>Payment History - {{ $customer->name }}</h4>
                            <a href="{{ route('ViewAllCustomerPayments') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>

                        <!-- Customer Information -->
                        <div class="customer-info-card">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Customer Name:</strong><br>
                                    {{ $customer->name }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Phone:</strong><br>
                                    {{ $customer->phone ?? 'N/A' }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Email:</strong><br>
                                    {{ $customer->email ?? 'N/A' }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Available Advance:</strong><br>
                                    <span class="badge badge-success" style="font-size: 16px;">৳{{ number_format($customer->available_advance ?? 0, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Summary -->
                        <div class="summary-card">
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <h6>Total Payments</h6>
                                    <h4 class="payment-in">৳{{ number_format($totalPayments, 2) }}</h4>
                                </div>
                                <div class="col-md-4">
                                    <h6>Total Refunds</h6>
                                    <h4 class="payment-out">৳{{ number_format($totalRefunds, 2) }}</h4>
                                </div>
                                <div class="col-md-4">
                                    <h6>Net Balance</h6>
                                    <h4 class="text-primary">৳{{ number_format($netBalance, 2) }}</h4>
                                </div>
                            </div>
                        </div>

                        <!-- Payment History Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead style="background: #495057; color: white;">
                                    <tr>
                                        <th>SL</th>
                                        <th>Date</th>
                                        <th>Order/Type</th>
                                        <th>Payment Type</th>
                                        <th>Mode</th>
                                        <th>Amount</th>
                                        <th>Note</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($payments as $index => $payment)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ date('Y-m-d', strtotime($payment->payment_date)) }}</td>
                                        <td>
                                            @if($payment->order_id && $payment->order)
                                                <a href="{{ route('order.invoice', $payment->order->slug) }}" target="_blank">
                                                    {{ $payment->order->order_code }}
                                                </a>
                                            @else
                                                <span class="badge badge-info">Advance Payment</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($payment->payment_type == 'received')
                                                <span class="badge badge-success">Received</span>
                                            @elseif($payment->payment_type == 'refund')
                                                <span class="badge badge-danger">Refund</span>
                                            @elseif($payment->payment_type == 'advance')
                                                <span class="badge badge-info">Advance</span>
                                            @else
                                                <span class="badge badge-secondary">{{ ucfirst($payment->payment_type) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-light">{{ ucfirst($payment->payment_mode_title ?? 'N/A') }}</span>
                                        </td>
                                        <td>
                                            @if($payment->payment < 0)
                                                <span class="payment-out"><strong>-৳{{ number_format(abs($payment->payment), 2) }}</strong></span>
                                            @else
                                                <span class="payment-in"><strong>৳{{ number_format($payment->payment, 2) }}</strong></span>
                                            @endif
                                        </td>
                                        <td>{{ $payment->payment_note ?? '-' }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No payment history found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

