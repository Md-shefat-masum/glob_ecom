@extends('backend.master')

@section('header_css')
    <link href="{{ url('assets') }}/plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />
    <style>
        .order-card {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
            border-left: 4px solid #ffc107;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .order-card:hover {
            background: #e9ecef;
            border-left-color: #17a2b8;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .order-card.selected {
            background: #d1ecf1;
            border-left-color: #0c5460;
            border-left-width: 6px;
        }
        .due-orders-section {
            margin-top: 20px;
            display: none;
        }
        .payment-allocation-row {
            background: #fff;
        }
        .payment-allocation-row.full-payment {
            background: #d4edda;
        }
        .payment-allocation-row.partial-payment {
            background: #fff3cd;
        }
    </style>
@endsection

@section('page_title')
    Customer Payment
@endsection

@section('page_heading')
    Create Customer Payment / Advance
@endsection

@section('content')
    <div class="container" style="max-width: 900px;">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4>Create Payment / Advance</h4>
                            <a href="{{ route('ViewAllCustomerPayments') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>

                        <form id="paymentForm" action="{{ route('StoreCustomerPayment') }}" method="POST">
                            @csrf
                            
                            <!-- Customer Selection -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="customer_id">Select Customer <span class="text-danger">*</span></label>
                                    <select id="customer_id" class="form-control select2" name="customer_id" required>
                                        <option value="">-- Select Customer --</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->name }} - {{ $customer->phone }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Customer Info Display -->
                            <div id="customerInfo" class="alert alert-info" style="display: none;">
                                <strong>Available Advance:</strong> <span id="availableAdvance">৳0.00</span><br>
                                <strong>Total Due:</strong> <span id="totalDue">৳0.00</span>
                            </div>

                            <!-- Due Orders Section -->
                            <div class="due-orders-section" id="dueOrdersSection">
                                <h5>Due Orders <small class="text-muted">(Click on an order to pay it)</small></h5>
                                <div id="dueOrdersList"></div>
                            </div>

                            <!-- Payment Allocation Table -->
                            <div id="paymentAllocationSection" style="display: none;" class="mb-3">
                                <h5>Payment Allocation</h5>
                                <table class="table table-bordered table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Order Code</th>
                                            <th>Due Amount</th>
                                            <th>Paying Now</th>
                                            <th>Remaining</th>
                                        </tr>
                                    </thead>
                                    <tbody id="paymentAllocationBody"></tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="2" class="text-right">Total Payment:</th>
                                            <th colspan="2" id="totalPaymentAllocated">৳0.00</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <!-- Hidden field to track payment allocations -->
                            <input type="hidden" id="payment_allocations" name="payment_allocations" value="">

                            <!-- Payment Details -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="payment_amount">Payment Amount <span class="text-danger">*</span></label>
                                    <input type="number" id="payment_amount" class="form-control" name="payment_amount" step="0.01" min="0.01" required>
                                    <small class="form-text text-muted">Enter amount and it will auto-allocate to orders (FIFO)</small>
                                </div>
                                <div class="col-md-6">
                                    <label for="payment_date">Payment Date <span class="text-danger">*</span></label>
                                    <input type="date" id="payment_date" class="form-control" name="payment_date" value="{{ now()->toDateString() }}" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
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
                                    <textarea id="payment_note" class="form-control" name="payment_note" rows="3" placeholder="Optional payment notes"></textarea>
                                </div>
                            </div>

                            <div id="paymentNote" class="alert alert-warning">
                                <i class="fas fa-info-circle"></i> <strong>Note:</strong> <span id="paymentNoteText">Enter payment amount to see allocation.</span>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-12 text-right">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="fas fa-check"></i> Submit Payment
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_js')
    <script src="{{ url('assets') }}/plugins/select2/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();
            
            let dueOrders = [];
            let paymentAllocations = [];
            let selectedOrderId = null;

            // Load customer due orders when customer selected
            $('#customer_id').on('change', function() {
                const customerId = $(this).val();
                if (customerId) {
                    $.ajax({
                        url: '{{ url("/api/customer-due-orders") }}/' + customerId,
                        method: 'GET',
                        success: function(response) {
                            if (response.success) {
                                $('#availableAdvance').text('৳' + parseFloat(response.available_advance).toFixed(2));
                                $('#totalDue').text('৳' + parseFloat(response.total_due).toFixed(2));
                                $('#customerInfo').show();

                                dueOrders = response.due_orders || [];

                                // Show due orders if any
                                if (dueOrders.length > 0) {
                                    let html = '';
                                    dueOrders.forEach(function(order, index) {
                                        html += '<div class="order-card" data-order-index="' + index + '" data-order-id="' + order.id + '" onclick="selectOrder(' + index + ')">';
                                        html += '<div class="row">';
                                        html += '<div class="col-md-3"><strong>Order:</strong> ' + order.order_code + '</div>';
                                        html += '<div class="col-md-3"><strong>Date:</strong> ' + order.sale_date + '</div>';
                                        html += '<div class="col-md-3"><strong>Total:</strong> ৳' + parseFloat(order.total).toFixed(2) + '</div>';
                                        html += '<div class="col-md-3"><strong class="text-danger">Due:</strong> ৳' + parseFloat(order.due_amount).toFixed(2) + '</div>';
                                        html += '</div></div>';
                                    });
                                    $('#dueOrdersList').html(html);
                                    $('#dueOrdersSection').show();
                                } else {
                                    $('#dueOrdersSection').hide();
                                }
                                
                                // Reset payment fields
                                resetPaymentFields();
                            }
                        }
                    });
                } else {
                    $('#customerInfo').hide();
                    $('#dueOrdersSection').hide();
                    dueOrders = [];
                    resetPaymentFields();
                }
            });

            // Handle payment amount input change for FIFO allocation
            $('#payment_amount').on('input', function() {
                const amount = parseFloat($(this).val()) || 0;
                if (amount > 0 && dueOrders.length > 0) {
                    allocatePaymentFIFO(amount);
                } else {
                    resetPaymentAllocation();
                }
            });

            // Form submission
            $('#paymentForm').on('submit', function(e) {
                e.preventDefault();

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

            // Function to select a specific order for payment
            window.selectOrder = function(orderIndex) {
                const order = dueOrders[orderIndex];
                selectedOrderId = order.id;
                
                // Highlight selected order
                $('.order-card').removeClass('selected');
                $('.order-card[data-order-index="' + orderIndex + '"]').addClass('selected');
                
                // Set payment amount to order's due amount
                $('#payment_amount').val(parseFloat(order.due_amount).toFixed(2));
                
                // Trigger allocation
                allocatePaymentFIFO(parseFloat(order.due_amount));
            };

            // FIFO Payment Allocation
            function allocatePaymentFIFO(paymentAmount) {
                paymentAllocations = [];
                let remainingAmount = paymentAmount;
                
                // Allocate to orders in FIFO order (first order first)
                for (let i = 0; i < dueOrders.length; i++) {
                    if (remainingAmount <= 0) break;
                    
                    const order = dueOrders[i];
                    const orderDue = parseFloat(order.due_amount);
                    const amountForThisOrder = Math.min(remainingAmount, orderDue);
                    
                    if (amountForThisOrder > 0) {
                        paymentAllocations.push({
                            order_id: order.id,
                            order_code: order.order_code,
                            due_amount: orderDue,
                            payment_amount: amountForThisOrder,
                            remaining: orderDue - amountForThisOrder,
                            is_full_payment: amountForThisOrder >= orderDue
                        });
                        
                        remainingAmount -= amountForThisOrder;
                    }
                }
                
                // If there's remaining amount, it will be advance
                if (remainingAmount > 0) {
                    paymentAllocations.push({
                        order_id: null,
                        order_code: 'ADVANCE',
                        due_amount: 0,
                        payment_amount: remainingAmount,
                        remaining: 0,
                        is_full_payment: false,
                        is_advance: true
                    });
                }
                
                renderPaymentAllocation();
            }

            // Render payment allocation table
            function renderPaymentAllocation() {
                if (paymentAllocations.length === 0) {
                    $('#paymentAllocationSection').hide();
                    return;
                }
                
                let html = '';
                let totalPayment = 0;
                
                paymentAllocations.forEach(function(allocation) {
                    const rowClass = allocation.is_advance ? 'table-info' : 
                                    (allocation.is_full_payment ? 'payment-allocation-row full-payment' : 'payment-allocation-row partial-payment');
                    
                    html += '<tr class="' + rowClass + '">';
                    html += '<td>' + (allocation.is_advance ? '<strong>Advance Payment</strong>' : allocation.order_code) + '</td>';
                    html += '<td>৳' + parseFloat(allocation.due_amount).toFixed(2) + '</td>';
                    html += '<td class="text-success"><strong>৳' + parseFloat(allocation.payment_amount).toFixed(2) + '</strong></td>';
                    html += '<td>' + (allocation.is_advance ? '-' : '৳' + parseFloat(allocation.remaining).toFixed(2)) + '</td>';
                    html += '</tr>';
                    
                    totalPayment += parseFloat(allocation.payment_amount);
                });
                
                $('#paymentAllocationBody').html(html);
                $('#totalPaymentAllocated').text('৳' + totalPayment.toFixed(2));
                $('#paymentAllocationSection').show();
                
                // Store allocations in hidden field
                $('#payment_allocations').val(JSON.stringify(paymentAllocations));
                
                // Update note based on allocation
                const orderPayments = paymentAllocations.filter(a => !a.is_advance);
                const advancePayment = paymentAllocations.find(a => a.is_advance);
                
                let noteText = '';
                if (orderPayments.length > 0 && advancePayment) {
                    noteText = 'Payment will be allocated to ' + orderPayments.length + ' order(s). Excess ৳' + advancePayment.payment_amount.toFixed(2) + ' will be saved as advance.';
                    $('#paymentNote').removeClass('alert-warning').addClass('alert-success');
                } else if (orderPayments.length > 0) {
                    noteText = 'Payment will be allocated to ' + orderPayments.length + ' order(s).';
                    $('#paymentNote').removeClass('alert-warning').addClass('alert-success');
                } else if (advancePayment) {
                    noteText = 'This payment will be recorded as an advance payment for the selected customer.';
                    $('#paymentNote').removeClass('alert-success').addClass('alert-warning');
                }
                $('#paymentNoteText').text(noteText);
            }

            // Reset payment allocation
            function resetPaymentAllocation() {
                paymentAllocations = [];
                $('#paymentAllocationSection').hide();
                $('#payment_allocations').val('');
                $('.order-card').removeClass('selected');
                selectedOrderId = null;
            }

            // Reset payment fields
            function resetPaymentFields() {
                $('#payment_amount').val('');
                resetPaymentAllocation();
            }
        });
    </script>
@endsection

