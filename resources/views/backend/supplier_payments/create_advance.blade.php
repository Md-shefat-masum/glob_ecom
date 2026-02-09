@extends('backend.master')

@section('header_css')
    <link href="{{ url('assets') }}/plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('page_title')
    Supplier Payment - Advance
@endsection

@section('page_heading')
    Pay Supplier Advance Amount
@endsection

@section('content')
    <div class="container" style="max-width: 600px;">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4>Pay Advance Amount</h4>
                            <a href="{{ route('ViewAllSupplierPayments') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>

                        <form id="paymentForm" action="{{ route('StoreSupplierPayment') }}" method="POST">
                            @csrf
                            <input type="hidden" name="payment_type" value="advance">

                            <!-- Supplier Selection -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="supplier_id">Select Supplier <span class="text-danger">*</span></label>
                                    <select id="supplier_id" class="form-control select2" name="supplier_id" required>
                                        <option value="">-- Select Supplier --</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}"
                                                {{ $selectedSupplier && $selectedSupplier->id == $supplier->id ? 'selected' : '' }}>
                                                {{ $supplier->name }} - {{ $supplier->contact_number ?? 'N/A' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Payment Details -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="payment_amount">Payment Amount <span class="text-danger">*</span></label>
                                    <input type="number" id="payment_amount" class="form-control" name="payment_amount"
                                        step="0.01" min="0.01" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="payment_date">Payment Date <span class="text-danger">*</span></label>
                                    <input type="date" id="payment_date" class="form-control" name="payment_date"
                                        value="{{ now()->toDateString() }}" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="payment_mode">Payment Mode <span class="text-danger">*</span></label>
                                    <select id="payment_mode" class="form-control" name="payment_mode" required>
                                        <option value="">-- Select Payment Mode --</option>
                                        @foreach ($paymentTypes as $paymentType)
                                            <option value="{{ $paymentType->id }}"
                                                data-payment-type="{{ $paymentType->payment_type }}">
                                                {{ $paymentType->payment_type }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label>Account Balance</label>
                                    <div id="accountBalanceDisplay" class="form-control" style="background-color: #f8f9fa;height: unset;">
                                        <span id="accountBalanceText">Select payment mode to see balance</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden field for account_id -->
                            <input type="hidden" id="account_id" name="account_id" value="">

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="payment_note">Note</label>
                                    <textarea id="payment_note" class="form-control" name="payment_note" rows="3"
                                        placeholder="Optional payment notes"></textarea>
                                </div>
                            </div>

                            <div id="paymentNote" class="alert alert-info">
                                <i class="fas fa-info-circle"></i> <strong>Note:</strong> <span id="paymentNoteText">This
                                    advance payment can be adjusted against future due amounts. Select payment mode to see
                                    available balance.</span>
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

            let accountBalance = 0;

            // Handle payment mode change to load account balance
            $('#payment_mode').on('change', function() {
                const paymentTypeId = $(this).val();
                if (paymentTypeId) {
                    $.ajax({
                        url: '{{ url('/api/account-balance') }}/' + paymentTypeId,
                        method: 'GET',
                        success: function(response) {
                            if (response.success) {
                                accountBalance = parseFloat(response.balance) || 0;
                                $('#account_id').val(response.account_id);
                                $('#accountBalanceText').html(
                                    '<strong>Account:</strong> ' + response.account_name +
                                    '<br>' +
                                    '<strong>Balance:</strong> <span class="' + (
                                        accountBalance >= 0 ? 'text-success' : 'text-danger'
                                        ) + '">৳' + accountBalance.toFixed(2) + '</span>'
                                );
                                validatePaymentAmount();
                            } else {
                                $('#accountBalanceText').html('<span class="text-danger">' +
                                    response.message + '</span>');
                                $('#account_id').val('');
                                accountBalance = 0;
                            }
                        },
                        error: function() {
                            $('#accountBalanceText').html(
                                '<span class="text-danger">Error loading account balance</span>'
                                );
                            $('#account_id').val('');
                            accountBalance = 0;
                        }
                    });
                } else {
                    $('#accountBalanceText').html('Select payment mode to see balance');
                    $('#account_id').val('');
                    accountBalance = 0;
                }
            });

            // Handle payment amount input change
            $('#payment_amount').on('input', function() {
                validatePaymentAmount();
            });

            // Validate payment amount against account balance
            function validatePaymentAmount() {
                const amount = parseFloat($('#payment_amount').val()) || 0;
                const accountId = $('#account_id').val();

                if (!accountId) {
                    $('#paymentNoteText').html('Please select a payment mode first.');
                    return false;
                }

                if (amount > 0) {
                    if (amount > accountBalance) {
                        $('#paymentNoteText').html('<span class="text-danger">Insufficient balance! Available: ৳' +
                            accountBalance.toFixed(2) + '</span>');
                        return false;
                    } else {
                        $('#paymentNoteText').html(
                            'This advance payment can be adjusted against future due amounts.');
                    }
                }
                return true;
            }

            // Form submission
            $('#paymentForm').on('submit', function(e) {
                e.preventDefault();

                if (!validatePaymentAmount()) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please check payment amount and account balance.'
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
                                text: xhr.responseJSON?.message ||
                                    'Something went wrong!'
                            });
                        }
                    }
                });
            });
        });
    </script>
@endsection
