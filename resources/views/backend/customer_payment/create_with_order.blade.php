@extends('backend.master')

@section('header_css')
    <style>
        .order-info-card {
            background: #e3f2fd;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #2196F3;
        }
        .due-orders-table {
            margin-top: 20px;
        }
        .due-orders-table th {
            background: #495057;
            color: white;
        }
    </style>
@endsection

@section('page_title')
    Customer Payment
@endsection

@section('page_heading')
    Record Payment for Order
@endsection

@section('content')
    <div class="container" style="max-width: 1200px;">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4>Record Payment for Order #{{ $order->order_code }}</h4>
                            <a href="{{ route('ViewAllProductOrder') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Orders
                            </a>
                        </div>

                        <!-- Order Information -->
                        <div class="order-info-card">
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Customer:</strong> {{ $order->customer->name ?? 'N/A' }}<br>
                                    <strong>Phone:</strong> {{ $order->customer->phone ?? 'N/A' }}
                                </div>
                                <div class="col-md-4">
                                    <strong>Order Date:</strong> {{ $order->sale_date }}<br>
                                    <strong>Order Total:</strong> ৳{{ number_format($order->total, 2) }}
                                </div>
                                <div class="col-md-4">
                                    <strong>Paid Amount:</strong> ৳{{ number_format($order->paid_amount, 2) }}<br>
                                    <strong class="text-danger">Due Amount:</strong> <span class="text-danger">৳{{ number_format($order->due_amount, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <strong>Customer's Available Advance:</strong> ৳{{ number_format($availableAdvance, 2) }}
                        </div>

                        <form id="paymentForm" action="{{ route('StoreCustomerPayment') }}" method="POST">
                            @csrf
                            <input type="hidden" name="customer_id" value="{{ $order->customer_id }}">
                            <input type="hidden" name="order_id" value="{{ $order->id }}">

                            <!-- Payment Details -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="payment_amount">Payment Amount <span class="text-danger">*</span></label>
                                    <input type="number" id="payment_amount" class="form-control" name="payment_amount" step="0.01" min="0.01" max="{{ $order->due_amount }}" value="{{ $order->due_amount }}" required>
                                    <small class="text-muted">Maximum: ৳{{ number_format($order->due_amount, 2) }}</small>
                                </div>
                                <div class="col-md-4">
                                    <label for="payment_date">Payment Date <span class="text-danger">*</span></label>
                                    <input type="date" id="payment_date" class="form-control" name="payment_date" value="{{ now()->toDateString() }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="payment_mode">Payment Mode <span class="text-danger">*</span></label>
                                    <select id="payment_mode" class="form-control" name="payment_mode" required>
                                        <option value="cash">Cash</option>
                                        <option value="bkash">bKash</option>
                                        <option value="rocket">Rocket</option>
                                        <option value="nogod">Nogod</option>
                                        <option value="bank">Bank Transfer</option>
                                        <option value="cheque">Cheque</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="payment_note">Note</label>
                                    <textarea id="payment_note" class="form-control" name="payment_note" rows="2" placeholder="Optional payment notes">Payment for order {{ $order->order_code }}</textarea>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-12 text-right">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="fas fa-check"></i> Record Payment
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Other Due Orders -->
                        @if($dueOrders->count() > 1)
                        <div class="due-orders-table mt-4">
                            <h5>Other Due Orders for this Customer</h5>
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>Order Code</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Due Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dueOrders as $dueOrder)
                                        @if($dueOrder->id != $order->id)
                                        <tr>
                                            <td>{{ $dueOrder->order_code }}</td>
                                            <td>{{ $dueOrder->sale_date }}</td>
                                            <td>৳{{ number_format($dueOrder->total, 2) }}</td>
                                            <td><span class="badge badge-warning">{{ ucfirst($dueOrder->order_status) }}</span></td>
                                            <td class="text-danger">৳{{ number_format($dueOrder->due_amount, 2) }}</td>
                                            <td>
                                                <a href="{{ route('CreateCustomerPaymentWithOrder', $dueOrder->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-money-bill"></i> Pay
                                                </a>
                                            </td>
                                        </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Form submission
            $('#paymentForm').on('submit', function(e) {
                e.preventDefault();

                const paymentAmount = parseFloat($('#payment_amount').val());
                const dueAmount = {{ $order->due_amount }};

                if (paymentAmount > dueAmount) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Amount',
                        text: 'Payment amount cannot exceed due amount!'
                    });
                    return;
                }

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message
                            }).then(() => {
                                window.location.href = response.redirect;
                            });
                        }
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            let errorMsg = '';
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                errorMsg += value[0] + '\n';
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                text: errorMsg
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.message || 'Something went wrong!'
                            });
                        }
                    }
                });
            });
        });
    </script>
@endsection

