@extends('backend.master')

@section('header_css')
    <link href="{{ url('assets') }}/plugins/dropify/dropify.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('assets') }}/plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('assets') }}/plugins/selecttree/select2totree.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('assets') }}/css/tagsinput.css" rel="stylesheet" type="text/css" />
    <style>
        .select2-selection {
            height: 34px !important;
            border: 1px solid #ced4da !important;
        }

        .select2 {
            width: 100% !important;
        }

        .bootstrap-tagsinput .badge {
            margin: 2px 2px !important;
        }

        .select2-container .select2-selection--single {
            height: 38px;
            padding: 6px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }

        #balance_info {
            margin-top: 10px;
            padding: 10px;
            border-radius: 4px;
        }

        .balance-warning {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
        }

        .balance-success {
            background-color: #d4edda;
            border: 1px solid #28a745;
            color: #155724;
        }

        #amount_text {
            font-size: 18px;
            font-weight: 600;
            color: #28a745;
            margin-top: 5px;
        }
    </style>
@endsection

@section('page_title')
    Withdraw
@endsection
@section('page_heading')
    Add a withdraw
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                         <h4 class="card-title mb-3">Withdraw</h4>
                         <a href="{{ route('ViewAllWithdraw')}}" class="btn btn-secondary">
                             <i class="fas fa-arrow-left"></i>
                         </a>
                     </div>

                    <form class="needs-validation" method="POST" action="{{ route('StoreWithdraw') }}"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-lg-6">

                                        <div class="form-group" style="margin-bottom: 30px;">
                                            <label for="withdraw_date">Withdraw Date<span class="text-danger">*</span></label>
                                            <input type="date" id="withdraw_date" name="withdraw_date" class="form-control"
                                                placeholder="Enter withdraw date Here" value="{{ date('Y-m-d') }}">
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('withdraw_date')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group" style="margin-bottom: 30px;">
                                            <label for="investor_id">Investor<span class="text-danger">*</span></label>
                                            <select id="investor_id" name="investor_id" class="form-control" required>
                                                <option value="">Select Investor</option>
                                                @foreach($investors as $investor)
                                                    <option value="{{ $investor->id }}">{{ $investor->name }}</option>
                                                @endforeach
                                            </select>
                                            <div id="balance_info" style="display: none;"></div>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('investor_id')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group" style="margin-bottom: 30px;">
                                            <label for="payment_type">From Which Account<span class="text-danger">*</span></label>
                                            <select id="payment_type" name="payment_type" class="form-control" required>
                                                <option value="">Select Payment Type</option>
                                                @foreach($paymentTypes as $paymentType)
                                                    @php
                                                        $balance = $paymentType['total_amount'] ?? 0;
                                                        $formattedBalance = number_format($balance, 2);
                                                        $balanceText = $balance > 0 ? ' (Balance: ৳' . $formattedBalance . ')' : ' (No Balance)';
                                                        $disabled = $balance <= 0 ? 'disabled' : '';
                                                    @endphp
                                                    <option value="{{ $paymentType['id'] }}" data-balance="{{ $balance }}" {{ $disabled }}>
                                                        {{ $paymentType['payment_type'] }}{{ $balanceText }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div id="payment_type_balance_info" style="display: none; margin-top: 10px;"></div>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('payment_type')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group" style="margin-bottom: 30px;">
                                            <label for="amount">Amount <span class="text-danger">*</span></label>
                                            <input type="number" step="0.01" id="amount" name="amount"
                                                class="form-control" placeholder="Enter Amount Here" min="0.01" required>
                                            <div id="amount_text"></div>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('amount')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="note">Note <span class="text-danger">*</span> (Minimum 10 characters)</label>
                                            <textarea id="note" name="note" class="form-control" placeholder="Enter Note Here (Minimum 10 characters)" rows="4" minlength="10" required></textarea>
                                            <small class="form-text text-muted">Minimum 10 characters required.</small>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('note')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-3">
                                <div class="form-group text-center pt-3">
                                    <a href="{{ route('ViewAllWithdraw') }}" style="width: 130px;"
                                        class="btn btn-danger d-inline-block text-white m-2" type="submit"><i
                                            class="mdi mdi-cancel"></i> Cancel</a>
                                    <button class="btn btn-primary m-2" style="width: 130px;" type="submit" id="submitBtn"><i
                                            class="fas fa-save"></i> Save </button>
                                </div>
                            </div>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_js')
    <script src="{{ url('assets') }}/plugins/dropify/dropify.min.js"></script>
    <script src="{{ url('assets') }}/pages/fileuploads-demo.js"></script>
    <script src="{{ url('assets') }}/plugins/select2/select2.min.js"></script>
    <script src="{{ url('assets') }}/plugins/selecttree/select2totree.js" /></script>
    <script src="{{ url('assets') }}/js/tagsinput.js"></script>

    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {
            $('#payment_type').select2({
                placeholder: 'Select Payment Type',
                allowClear: true,
                width: '100%'
            });

            $('#investor_id').select2({
                placeholder: 'Select Investor',
                allowClear: true,
                width: '100%'
            });

            // Load investor balance when investor is selected
            $('#investor_id').on('change', function() {
                var investorId = $(this).val();
                if (investorId) {
                    loadInvestorBalance(investorId);
                } else {
                    $('#balance_info').hide();
                }
            });

            // Check balance when amount changes
            $('#amount').on('input change', function() {
                var investorId = $('#investor_id').val();
                var amount = parseFloat($(this).val()) || 0;
                
                if (investorId && amount > 0) {
                    checkBalance(investorId, amount);
                } else {
                    $('#balance_info').hide();
                }

                // Show amount text
                if (amount > 0) {
                    var formatted = amount.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    $('#amount_text').html('<strong>Amount: ৳ ' + formatted + '</strong>');
                } else {
                    $('#amount_text').html('');
                }
            });
        });

        function loadInvestorBalance(investorId) {
            $.ajax({
                url: "{{ route('GetInvestorBalance') }}",
                type: "GET",
                data: { investor_id: investorId },
                success: function(response) {
                    if (response.success) {
                        var balance = parseFloat(response.balance) || 0;
                        var balanceHtml = '<div class="balance-success"><strong>Available Balance: ৳ ' + response.formatted_balance + '</strong></div>';
                        $('#balance_info').html(balanceHtml).show();
                    } else {
                        var balanceHtml = '<div class="balance-warning"><strong>Warning: ' + response.message + '</strong></div>';
                        $('#balance_info').html(balanceHtml).show();
                    }
                },
                error: function(xhr) {
                    console.error("Error loading balance:", xhr);
                    var balanceHtml = '<div class="balance-warning"><strong>Error loading balance</strong></div>';
                    $('#balance_info').html(balanceHtml).show();
                }
            });
        }

        function checkBalance(investorId, amount) {
            $.ajax({
                url: "{{ route('GetInvestorBalance') }}",
                type: "GET",
                data: { investor_id: investorId },
                success: function(response) {
                    if (response.success) {
                        var balance = parseFloat(response.balance) || 0;
                        var balanceInfo = $('#balance_info');
                        var submitBtn = $('#submitBtn');
                        
                        if (amount > balance) {
                            var warningHtml = '<div class="balance-warning"><strong>Insufficient Investor Balance!</strong><br>Available: ৳ ' + response.formatted_balance + '<br>Required: ৳ ' + amount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</div>';
                            balanceInfo.html(warningHtml).show();
                            submitBtn.prop('disabled', true);
                        } else {
                            var successHtml = '<div class="balance-success"><strong>Investor Balance: ৳ ' + response.formatted_balance + '</strong></div>';
                            balanceInfo.html(successHtml).show();
                            // Don't enable submit button here, let payment type check handle it
                        }
                    } else {
                        var warningHtml = '<div class="balance-warning"><strong>Warning: ' + response.message + '</strong></div>';
                        $('#balance_info').html(warningHtml).show();
                        $('#submitBtn').prop('disabled', true);
                    }
                },
                error: function(xhr) {
                    console.error("Error checking balance:", xhr);
                }
            });
        }

        function checkPaymentTypeBalance(paymentTypeId, amount) {
            $.ajax({
                url: "{{ route('GetPaymentTypeBalance') }}",
                type: "GET",
                data: { payment_type_id: paymentTypeId },
                success: function(response) {
                    if (response.success) {
                        var balance = parseFloat(response.balance) || 0;
                        var balanceInfo = $('#payment_type_balance_info');
                        var submitBtn = $('#submitBtn');
                        
                        if (balance <= 0) {
                            var warningHtml = '<div class="balance-warning"><strong>Warning: This payment type has no available balance!</strong></div>';
                            balanceInfo.html(warningHtml).show();
                            submitBtn.prop('disabled', true);
                        } else if (amount > balance) {
                            var warningHtml = '<div class="balance-warning"><strong>Insufficient Payment Type Balance!</strong><br>Available: ৳ ' + response.formatted_balance + '<br>Required: ৳ ' + amount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</div>';
                            balanceInfo.html(warningHtml).show();
                            submitBtn.prop('disabled', true);
                        } else {
                            var successHtml = '<div class="balance-success"><strong>Available Balance: ৳ ' + response.formatted_balance + '</strong></div>';
                            balanceInfo.html(successHtml).show();
                            // Check investor balance before enabling submit
                            var investorId = $('#investor_id').val();
                            var investorAmount = parseFloat($('#amount').val()) || 0;
                            if (investorId && investorAmount > 0) {
                                $.ajax({
                                    url: "{{ route('GetInvestorBalance') }}",
                                    type: "GET",
                                    data: { investor_id: investorId },
                                    success: function(invResponse) {
                                        if (invResponse.success) {
                                            var invBalance = parseFloat(invResponse.balance) || 0;
                                            if (investorAmount <= invBalance && investorAmount <= balance) {
                                                submitBtn.prop('disabled', false);
                                            } else {
                                                submitBtn.prop('disabled', true);
                                            }
                                        }
                                    }
                                });
                            }
                        }
                    } else {
                        var warningHtml = '<div class="balance-warning"><strong>Warning: ' + response.message + '</strong></div>';
                        $('#payment_type_balance_info').html(warningHtml).show();
                        $('#submitBtn').prop('disabled', true);
                    }
                },
                error: function(xhr) {
                    console.error("Error checking payment type balance:", xhr);
                }
            });
        }

        // Form validation
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>
@endsection

