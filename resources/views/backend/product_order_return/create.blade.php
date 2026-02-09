@extends('backend.master')

@section('header_css')
    <style>
        .order-info-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .product-return-table th {
            background: #495057;
            color: white;
        }
        .badge-returned {
            background: #ffc107;
        }
        .badge-available {
            background: #28a745;
        }
    </style>
@endsection

@section('page_title')
    Product Order Return
@endsection

@section('page_heading')
    Create Product Order Return
@endsection

@section('content')
    <div class="container" style="max-width: 1500px;">
        <div class="row">
            <div class="col-lg-12 col-xl-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4>Create Return for Order #{{ $order->order_code }}</h4>
                            <a href="{{ route('ViewAllProductOrder') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Orders
                            </a>
                        </div>

                        <!-- Order Information -->
                        <div class="order-info-card">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Customer:</strong> {{ $order->customer->name ?? 'N/A' }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Order Date:</strong> {{ $order->sale_date }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Order Total:</strong> ৳{{ number_format($order->total, 2) }}
                                </div>
                                <div class="col-md-3">
                                    <div class="text-right">
                                        <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#originalInvoiceModal">
                                            <i class="fas fa-file-invoice"></i> View Original Invoice
                                        </button>
                                        <button type="button" class="btn btn-sm btn-warning" id="viewReturnHistoryBtn">
                                            <i class="fas fa-history"></i> Return History
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('StoreProductOrderReturn') }}" method="POST" id="returnForm">
                            @csrf
                            <input type="hidden" name="product_order_id" value="{{ $order->id }}">
                            <input type="hidden" name="return_code" value="{{ $return_code }}">

                            <!-- Return Details -->
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="return_code_display">Return Code</label>
                                    <input type="text" class="form-control" value="{{ $return_code }}" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="return_date">Return Date <span class="text-danger">*</span></label>
                                    <input type="date" id="return_date" class="form-control" name="return_date" value="{{ now()->toDateString() }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="refund_method">Refund Method <span class="text-danger">*</span></label>
                                    <select id="refund_method" class="form-control" name="refund_method" required>
                                        <option value="advance_payment" selected>Customer Advance Payment</option>
                                        <option value="cash">Cash</option>
                                        <option value="bkash">bKash</option>
                                        <option value="rocket">Rocket</option>
                                        <option value="nogod">Nogod</option>
                                        <option value="bank">Bank Transfer</option>
                                        <option value="cheque">Cheque</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="return_reason">Return Reason</label>
                                    <input type="text" id="return_reason" class="form-control" name="return_reason" placeholder="Optional">
                                </div>
                            </div>

                            <!-- Products Table -->
                            <div class="table-responsive">
                                <table class="table table-bordered product-return-table">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Product Name</th>
                                            <th>Ordered Qty</th>
                                            <th>Already Returned</th>
                                            <th>Available to Return</th>
                                            <th>Return Qty</th>
                                            <th>Unit Price</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->order_products as $index => $product)
                                            @php
                                                $availableQty = $availableQuantities[$product->product_id] ?? 0;
                                                $returnedQty = $product->qty - $availableQty;
                                            @endphp
                                            @if($availableQty > 0)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $product->product_name }}</td>
                                                <td><span class="badge badge-primary">{{ $product->qty }}</span></td>
                                                <td><span class="badge badge-returned">{{ $returnedQty }}</span></td>
                                                <td><span class="badge badge-available">{{ $availableQty }}</span></td>
                                                <td>
                                                    <input type="number" 
                                                           class="form-control return-qty-input" 
                                                           name="return_products[{{ $product->id }}][qty]" 
                                                           data-product-id="{{ $product->product_id }}"
                                                           data-max="{{ $availableQty }}"
                                                           data-price="{{ $product->sale_price }}"
                                                           min="0" 
                                                           max="{{ $availableQty }}" 
                                                           value="0"
                                                           style="width: 100px;">
                                                    <input type="hidden" name="return_products[{{ $product->id }}][product_id]" value="{{ $product->product_id }}">
                                                    <input type="hidden" name="return_products[{{ $product->id }}][order_product_id]" value="{{ $product->id }}">
                                                    <input type="hidden" name="return_products[{{ $product->id }}][sale_price]" value="{{ $product->sale_price }}">
                                                    <input type="hidden" name="return_products[{{ $product->id }}][discount_type]" value="{{ $product->discount_type }}">
                                                    <input type="hidden" name="return_products[{{ $product->id }}][discount_amount]" value="{{ $product->discount_amount }}">
                                                    <input type="hidden" name="return_products[{{ $product->id }}][tax]" value="{{ $product->tax }}">
                                                </td>
                                                <td>৳{{ number_format($product->sale_price, 2) }}</td>
                                                <td class="product-total">৳0.00</td>
                                            </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="7" class="text-right">Subtotal:</th>
                                            <th id="subtotal">৳0.00</th>
                                        </tr>
                                        <tr>
                                            <th colspan="7" class="text-right">Grand Total:</th>
                                            <th id="grandTotal">৳0.00</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <input type="hidden" name="subtotal_amt" id="subtotal_amt" value="0">
                            <input type="hidden" name="grand_total_amt" id="grand_total_amt" value="0">

                            <!-- Note -->
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <label for="note">Note</label>
                                    <textarea id="note" class="form-control" name="note" rows="3" placeholder="Optional notes about this return"></textarea>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="row mt-4">
                                <div class="col-md-12 text-right">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-check"></i> Create Return
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Original Invoice Modal -->
    <div class="modal fade" id="originalInvoiceModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Original Invoice - {{ $order->order_code }}</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Customer:</strong> {{ $order->customer->name ?? 'N/A' }}<br>
                            <strong>Date:</strong> {{ $order->sale_date }}<br>
                            <strong>Status:</strong> <span class="badge badge-success">{{ ucfirst($order->order_status) }}</span>
                        </div>
                        <div class="col-md-6 text-right">
                            <strong>Total:</strong> ৳{{ number_format($order->total, 2) }}<br>
                            <strong>Paid:</strong> ৳{{ number_format($order->paid_amount, 2) }}<br>
                            <strong>Due:</strong> ৳{{ number_format($order->due_amount, 2) }}
                        </div>
                    </div>
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->order_products as $product)
                            <tr>
                                <td>{{ $product->product_name }}</td>
                                <td>{{ $product->qty }}</td>
                                <td>৳{{ number_format($product->sale_price, 2) }}</td>
                                <td>৳{{ number_format($product->total_price, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Return History Modal -->
    <div class="modal fade" id="returnHistoryModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Return History - Order {{ $order->order_code }}</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="returnHistoryContent">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
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
            // Calculate totals when quantity changes
            $('.return-qty-input').on('input', function() {
                calculateTotals();
            });

            function calculateTotals() {
                let subtotal = 0;

                $('.return-qty-input').each(function() {
                    const qty = parseInt($(this).val()) || 0;
                    const price = parseFloat($(this).data('price')) || 0;
                    const total = qty * price;
                    
                    // Update row total
                    $(this).closest('tr').find('.product-total').text('৳' + total.toFixed(2));
                    
                    subtotal += total;
                });

                $('#subtotal').text('৳' + subtotal.toFixed(2));
                $('#grandTotal').text('৳' + subtotal.toFixed(2));
                $('#subtotal_amt').val(subtotal.toFixed(2));
                $('#grand_total_amt').val(subtotal.toFixed(2));
            }

            // Form submission
            $('#returnForm').on('submit', function(e) {
                e.preventDefault();

                // Check if at least one product has return quantity
                let hasReturnQty = false;
                let returnProducts = [];

                $('.return-qty-input').each(function() {
                    const qty = parseInt($(this).val()) || 0;
                    const max = parseInt($(this).data('max')) || 0;
                    
                    if (qty > 0) {
                        hasReturnQty = true;
                        
                        if (qty > max) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Invalid Quantity',
                                text: 'Return quantity exceeds available quantity!'
                            });
                            return false;
                        }

                        const productId = $(this).data('product-id');
                        const row = $(this).closest('tr');
                        returnProducts.push({
                            product_id: productId,
                            qty: qty,
                            sale_price: $(this).data('price'),
                            order_product_id: row.find('input[name$="[order_product_id]"]').val(),
                            discount_type: row.find('input[name$="[discount_type]"]').val(),
                            discount_amount: row.find('input[name$="[discount_amount]"]').val(),
                            tax: row.find('input[name$="[tax]"]').val(),
                            total_price: qty * $(this).data('price')
                        });
                    }
                });

                if (!hasReturnQty) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Products Selected',
                        text: 'Please select at least one product to return!'
                    });
                    return false;
                }

                // Submit via AJAX
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: {
                        _token: $('input[name="_token"]').val(),
                        product_order_id: $('input[name="product_order_id"]').val(),
                        return_code: $('input[name="return_code"]').val(),
                        return_date: $('#return_date').val(),
                        return_reason: $('#return_reason').val(),
                        refund_method: $('#refund_method').val(),
                        return_products: returnProducts,
                        subtotal_amt: $('#subtotal_amt').val(),
                        grand_total_amt: $('#grand_total_amt').val(),
                        note: $('#note').val()
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message
                            }).then(() => {
                                window.location.href = response.redirect || '{{ route("ViewAllProductOrderReturns") }}';
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

            // View return history
            $('#viewReturnHistoryBtn').on('click', function() {
                $.ajax({
                    url: '{{ route("GetReturnHistory", $order->id) }}',
                    method: 'GET',
                    success: function(response) {
                        if (response.success && response.history.length > 0) {
                            let html = '<table class="table table-bordered table-sm">';
                            html += '<thead><tr><th>Return Code</th><th>Date</th><th>Product</th><th>Qty</th><th>Total</th></tr></thead><tbody>';
                            
                            $.each(response.history, function(i, item) {
                                html += '<tr>';
                                html += '<td>' + item.return_code + '</td>';
                                html += '<td>' + item.return_date + '</td>';
                                html += '<td>' + item.product_name + '</td>';
                                html += '<td>' + item.qty + '</td>';
                                html += '<td>৳' + parseFloat(item.total).toFixed(2) + '</td>';
                                html += '</tr>';
                            });
                            
                            html += '</tbody></table>';
                            $('#returnHistoryContent').html(html);
                        } else {
                            $('#returnHistoryContent').html('<p class="text-center">No return history found for this order.</p>');
                        }
                        $('#returnHistoryModal').modal('show');
                    },
                    error: function() {
                        $('#returnHistoryContent').html('<p class="text-center text-danger">Error loading return history.</p>');
                        $('#returnHistoryModal').modal('show');
                    }
                });
            });
        });
    </script>
@endsection

