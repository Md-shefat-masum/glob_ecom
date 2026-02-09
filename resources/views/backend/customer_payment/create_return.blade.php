@extends('backend.master')

@section('header_css')
    <link href="{{ url('assets') }}/plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />
    <style>
        .refund-alert {
            background: #fff3cd;
            padding: 20px;
            border-radius: 5px;
            border-left: 4px solid #ffc107;
            margin-bottom: 20px;
        }
    </style>
@endsection

@section('page_title')
    Customer Payment Return
@endsection

@section('page_heading')
    Customer Payment Return / Refund
@endsection

@section('content')
    <div class="container" style="max-width: 800px;">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4>Process Payment Return / Refund</h4>
                            <a href="{{ route('ViewAllCustomerPayments') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> <strong>Important:</strong> Only available advance balance can be refunded to customers.
                        </div>

                        <form id="refundForm" action="{{ route('ProcessCustomerPaymentReturn') }}" method="POST">
                            @csrf
                            
                            <!-- Customer Selection -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="customer_id">Select Customer <span class="text-danger">*</span></label>
                                    <select id="customer_id" class="form-control select2" name="customer_id" required>
                                        <option value="">-- Select Customer --</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" data-advance="{{ $customer->available_advance }}">
                                                {{ $customer->name }} - {{ $customer->phone }} (Advance: ৳{{ number_format($customer->available_advance, 2) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Available Advance Display -->
                            <div id="advanceDisplay" class="refund-alert" style="display: none;">
                                <h5>Available Advance Balance</h5>
                                <h3 class="text-success mb-0">৳<span id="availableAdvance">0.00</span></h3>
                                <small class="text-muted">Maximum refund amount</small>
                            </div>

                            <!-- Refund Details -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="refund_amount">Refund Amount <span class="text-danger">*</span></label>
                                    <input type="number" id="refund_amount" class="form-control" name="refund_amount" step="0.01" min="0.01" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="payment_date">Refund Date <span class="text-danger">*</span></label>
                                    <input type="date" id="payment_date" class="form-control" name="payment_date" value="{{ now()->toDateString() }}" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="payment_mode">Refund Mode <span class="text-danger">*</span></label>
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
                                    <label for="payment_note">Reason for Refund</label>
                                    <textarea id="payment_note" class="form-control" name="payment_note" rows="3" placeholder="Enter reason for refund" required></textarea>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-12 text-right">
                                    <button type="submit" class="btn btn-warning btn-lg">
                                        <i class="fas fa-undo"></i> Process Refund
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

            let maxRefund = 0;

            // Show available advance when customer selected
            $('#customer_id').on('change', function() {
                const advance = $(this).find(':selected').data('advance');
                maxRefund = parseFloat(advance) || 0;
                
                if (maxRefund > 0) {
                    $('#availableAdvance').text(maxRefund.toFixed(2));
                    $('#advanceDisplay').show();
                    $('#refund_amount').attr('max', maxRefund);
                } else {
                    $('#advanceDisplay').hide();
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Advance',
                        text: 'This customer has no available advance balance to refund!'
                    });
                }
            });

            // Form submission
            $('#refundForm').on('submit', function(e) {
                e.preventDefault();

                const refundAmount = parseFloat($('#refund_amount').val());

                if (refundAmount > maxRefund) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Amount',
                        text: 'Refund amount exceeds available advance balance!'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Confirm Refund',
                    text: `Are you sure you want to refund ৳${refundAmount.toFixed(2)}?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, process refund!'
                }).then((result) => {
                    if (result.isConfirmed) {
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
                    }
                });
            });
        });
    </script>
@endsection

