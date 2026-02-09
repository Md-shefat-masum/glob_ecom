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
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">Courier Management</h4>
                        <a href="{{ route('ViewAllProductOrder') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Orders
                        </a>
                    </div>

                    <!-- Order Information Card -->
                    <div class="card border-primary mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-info-circle"></i> Order Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Order Code:</th>
                                            <td><strong>{{ $order->order_code }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Order Date:</th>
                                            <td>{{ date('d M Y', strtotime($order->sale_date)) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Reference:</th>
                                            <td>{{ $order->reference ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Order Status:</th>
                                            <td>
                                                <span class="badge badge-{{ $order->order_status == 'invoiced' ? 'success' : ($order->order_status == 'pending' ? 'warning' : 'info') }}">
                                                    {{ ucfirst($order->order_status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Courier Status:</th>
                                            <td>
                                                <span class="badge badge-{{ $order->is_couriered == 1 ? 'success' : 'warning' }}">
                                                    {{ $order->is_couriered == 1 ? 'Couriered' : 'Not Couriered' }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Customer:</th>
                                            <td>{{ $order->customer->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Customer Phone:</th>
                                            <td>{{ $order->customer->phone ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Warehouse:</th>
                                            <td>{{ $order->warehouse->title ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Subtotal:</th>
                                            <td>৳ {{ number_format($order->subtotal, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Total Amount:</th>
                                            <td><strong class="text-primary">৳ {{ number_format($order->total, 2) }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Paid Amount:</th>
                                            <td>৳ {{ number_format($order->paid_amount, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Due Amount:</th>
                                            <td>
                                                <span class="badge badge-{{ $order->due_amount > 0 ? 'danger' : 'success' }}">
                                                    ৳ {{ number_format($order->due_amount, 2) }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Products Card -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-box"></i> Order Products</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Product Name</th>
                                            <th class="text-center">Quantity</th>
                                            <th class="text-right">Unit Price</th>
                                            <th class="text-right">Total Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($order->order_products as $index => $product)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $product->product_name }}</td>
                                                <td class="text-center">{{ $product->qty }}</td>
                                                <td class="text-right">৳ {{ number_format($product->sale_price, 2) }}</td>
                                                <td class="text-right">৳ {{ number_format($product->total_price, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">No products found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="4" class="text-right">Subtotal:</th>
                                            <th class="text-right">৳ {{ number_format($order->subtotal, 2) }}</th>
                                        </tr>
                                        @if($order->calculated_discount_amount > 0)
                                            <tr>
                                                <th colspan="4" class="text-right">Discount:</th>
                                                <th class="text-right text-danger">-৳ {{ number_format($order->calculated_discount_amount, 2) }}</th>
                                            </tr>
                                        @endif
                                        @if($order->other_charge_amount > 0)
                                            <tr>
                                                <th colspan="4" class="text-right">Other Charges:</th>
                                                <th class="text-right">৳ {{ number_format($order->other_charge_amount, 2) }}</th>
                                            </tr>
                                        @endif
                                        <tr>
                                            <th colspan="4" class="text-right">Grand Total:</th>
                                            <th class="text-right text-primary">৳ {{ number_format($order->total, 2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Placeholder for future courier functionality -->
                    <div class="card mt-4 border-info">
                        <div class="card-body text-center">
                            <i class="fas fa-truck fa-3x text-info mb-3"></i>
                            <h5 class="text-muted">Courier functionality will be implemented here</h5>
                            <p class="text-muted">This page is ready for courier management features.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_js')
    <script>
        // Placeholder for future JavaScript functionality
    </script>
@endsection

