@extends('backend.master')

@section('header_css')
    <link href="{{ url('assets') }}/plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('assets') }}/plugins/selecttree/select2totree.css" rel="stylesheet" type="text/css" />
    <style>
        .select2-selection {
            height: 34px !important;
            border: 1px solid #ced4da !important;
        }

        .select2 {
            width: 100% !important;
        }

        .select2-container .select2-selection--single {
            height: 38px;
            padding: 6px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
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
    Account Adjustment
@endsection
@section('page_heading')
    Create Adjustment
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">Create Adjustment</h4>
                        <a href="{{ route('ViewAllAdjustment') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>

                    <form class="needs-validation" method="POST" action="{{ route('StoreAdjustment') }}" novalidate>
                        @csrf

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-lg-6">

                                        <div class="form-group" style="margin-bottom: 30px;">
                                            <label for="date">Date<span class="text-danger">*</span></label>
                                            <input type="date" id="date" name="date" class="form-control"
                                                placeholder="Enter date Here" value="{{ date('Y-m-d') }}" required>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('date')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group" style="margin-bottom: 30px;">
                                            <label for="debit_account">Debit Account<span class="text-danger">*</span></label>
                                            <select id="debit_account" name="debit_account" class="form-control" required>
                                                <option value="">Select Debit Account</option>
                                            </select>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('debit_account')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group" style="margin-bottom: 30px;">
                                            <label for="credit_account">Credit Account<span class="text-danger">*</span></label>
                                            <select id="credit_account" name="credit_account" class="form-control" required>
                                                <option value="">Select Credit Account</option>
                                            </select>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('credit_account')
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
                                                    <strong>{{ $message }}</strong>
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
                                    <a href="{{ route('ViewAllAdjustment') }}" style="width: 130px;"
                                        class="btn btn-danger d-inline-block text-white m-2" type="button"><i
                                            class="mdi mdi-cancel"></i> Cancel</a>
                                    <button class="btn btn-primary m-2" style="width: 130px;" type="submit"><i
                                            class="fas fa-save"></i> Save</button>
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
    <script src="{{ url('assets') }}/plugins/select2/select2.min.js"></script>
    <script src="{{ url('assets') }}/plugins/selecttree/select2totree.js"></script>

    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Load accounts for both debit and credit account
        $(document).ready(function() {
            $.ajax({
                url: "{{ route('GetJsonAcAccount') }}",
                type: "GET",
                dataType: 'json',
                success: function(response) {
                    console.log("Account data received:", response);
                    
                    if (!response || response.length === 0) {
                        console.warn("No accounts found. Please ensure accounts are created in the system.");
                        // Initialize empty select2
                        $("#debit_account").select2({
                            placeholder: "No accounts available",
                            allowClear: true
                        });
                        $("#credit_account").select2({
                            placeholder: "No accounts available",
                            allowClear: true
                        });
                        return;
                    }

                    // Initialize debit account
                    $("#debit_account").select2ToTree({
                        treeData: {
                            dataArr: response
                        },
                        maximumSelectionLength: 1
                    }).select2ToTree({
                        placeholder: "Select Debit Account",
                        allowClear: true
                    });

                    // Initialize credit account
                    $("#credit_account").select2ToTree({
                        treeData: {
                            dataArr: response
                        },
                        maximumSelectionLength: 1
                    }).select2ToTree({
                        placeholder: "Select Credit Account",
                        allowClear: true
                    });
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching account data:", error);
                    console.error("Response:", xhr.responseText);
                    alert("Error loading accounts. Please refresh the page or contact administrator.");
                    // Initialize empty select2 on error
                    $("#debit_account").select2({
                        placeholder: "Error loading accounts",
                        allowClear: true
                    });
                    $("#credit_account").select2({
                        placeholder: "Error loading accounts",
                        allowClear: true
                    });
                }
            });
        });

        // Show amount in text on change
        $(document).ready(function() {
            $('#amount').on('input change', function() {
                var amount = $(this).val();
                if (amount && !isNaN(amount) && parseFloat(amount) > 0) {
                    var formatted = parseFloat(amount).toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    $('#amount_text').html('<strong>Amount: ৳ ' + formatted + '</strong>');
                } else {
                    $('#amount_text').html('');
                }
            });
        });

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

