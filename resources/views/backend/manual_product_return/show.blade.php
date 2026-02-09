@extends('backend.master')

@section('page_title')
    Manual Return Details
@endsection

@section('page_heading')
    Manual Product Return Details
@endsection

@section('content')
    <div class="container" style="max-width: 900px;">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4>Return #{{ $return->return_code }}</h4>
                            <div>
                                <a href="{{ route('ViewAllManualProductReturns') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back
                                </a>
                                <button onclick="window.print()" class="btn btn-primary">
                                    <i class="fas fa-print"></i> Print
                                </button>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6><strong>Customer Information</strong></h6>
                                <p><strong>Name:</strong> {{ $return->customer->name ?? 'N/A' }}<br>
                                <strong>Phone:</strong> {{ $return->customer->phone ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6><strong>Return Information</strong></h6>
                                <p><strong>Return Date:</strong> {{ $return->return_date }}<br>
                                <strong>Status:</strong> 
                                @if($return->return_status == 'approved')
                                    <span class="badge badge-success">Approved</span>
                                @elseif($return->return_status == 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @else
                                    <span class="badge badge-danger">Rejected</span>
                                @endif
                                </p>
                            </div>
                        </div>

                        @if($return->return_reason)
                        <div class="alert alert-warning">
                            <strong>Return Reason:</strong> {{ $return->return_reason }}
                        </div>
                        @endif

                        <h5>Return Items</h5>
                        <table class="table table-bordered">
                            <thead style="background: #495057; color: white;">
                                <tr>
                                    <th>SL</th>
                                    <th>Product Name</th>
                                    <th>Qty</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($return->return_items as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->product_name }}</td>
                                    <td>{{ $item->qty }}</td>
                                    <td>৳{{ number_format($item->unit_price, 2) }}</td>
                                    <td>৳{{ number_format($item->total_price, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-right">Total Refund (Credited to Wallet):</th>
                                    <th>৳{{ number_format($return->total, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>

                        @if($return->note)
                        <div class="mt-3">
                            <strong>Note:</strong> {{ $return->note }}
                        </div>
                        @endif

                        <div class="alert alert-info mt-3">
                            <i class="fas fa-wallet"></i> <strong>Refund Method:</strong> Customer Wallet (Advance Balance)
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

