@extends('backend.master')

@section('header_css')
    <link href="{{ url('assets') }}/plugins/dropify/dropify.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('assets') }}/plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('assets') }}/plugins/selecttree/select2totree.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('assets') }}/css/tagsinput.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
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
    </style>
@endsection

@section('page_title')
    Expense
@endsection
@section('page_heading')
    Add an expense
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-3">Expense</h4>
                        <a href="{{ route('ViewAllExpense')}}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>

                    <form class="needs-validation" method="POST" action="{{ url('save/new/expense') }}"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="row">

                                    <div class="col-lg-6">


                                        <div class="form-group">
                                            <label for="expense_date">Expense Date<span class="text-danger">*</span></label>
                                            <input type="date" id="expense_date" name="expense_date" maxlength="255"
                                                class="form-control" placeholder="Enter expense date Here"
                                                value="{{ date('Y-m-d') }}" required>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('expense_date')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group" style="margin-bottom: 30px;">
                                            <label for="expense_category_id">Expense Category<span class="text-danger">*</span></label>
                                            <select id="expense_category_id" name="expense_category_id" class="form-control" required>
                                                <option value="">Select Expense Category</option>
                                                @foreach ($expense_categories as $expense_category)
                                                    <option value="{{ $expense_category->id }}"
                                                        {{ old('expense_category_id') == $expense_category->id ? 'selected' : '' }}>
                                                        {{ $expense_category->category_name }}
                                                        @if($expense_category->debitAccount && $expense_category->creditAccount)
                                                            (From: {{ $expense_category->creditAccount->account_name }}, To: {{ $expense_category->debitAccount->account_name }})
                                                        @elseif($expense_category->debitAccount)
                                                            (To: {{ $expense_category->debitAccount->account_name }})
                                                        @elseif($expense_category->creditAccount)
                                                            (From: {{ $expense_category->creditAccount->account_name }})
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('expense_category_id')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                            <div id="account_info" class="mt-2" style="display: none;">
                                                <div class="alert alert-info">
                                                    <strong>From Account:</strong> <span id="credit_account_name"></span><br>
                                                    <strong>Available Balance:</strong> <span id="available_balance" class="font-weight-bold"></span>
                                                </div>
                                            </div>
                                        </div>             


                                        <div class="form-group">
                                            <label for="expense_amt">Expense Amount <span class="text-danger">*</span></label>
                                            <input type="number" step="0.01" id="expense_amt" name="expense_amt" 
                                                class="form-control" placeholder="Enter expense amount here" min="0.01" required>
                                            <div id="balance_warning" class="mt-2" style="display: none;">
                                                <div class="alert alert-danger">
                                                    <strong>Insufficient Balance!</strong> Available: <span id="warning_balance"></span>
                                                </div>
                                            </div>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('expense_amt')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="expense_for">Expense For <span class="text-danger">*</span></label>
                                            <input type="text" id="expense_for" name="expense_for" class="form-control"
                                                placeholder="Expense for What" required>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('expense_for')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="reference_no">Reference</label>
                                            <input type="text" id="reference_no" name="reference_no" maxlength="60"
                                                class="form-control" placeholder="Enter reference for expense">
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('reference_no')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>


                                        {{-- <div class="form-group">
                                            <label for="email">Email <span class="text-danger">*</span></label>
                                            <input type="text" id="email" name="email" maxlength="100"
                                                class="form-control" placeholder="Enter email Here">
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('email')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div> --}}

                                        <div class="form-group">
                                            <label for="note">Note</label>
                                            <textarea id="note" name="note" class="form-control" placeholder="Enter Description Here"></textarea>
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
                            <div class="col--3">
                                <div class="form-group text-center pt-3">
                                    <a href="{{ url('view/all/expense') }}" style="width: 130px;"
                                        class="btn btn-danger d-inline-block text-white m-2" type="submit"><i
                                            class="mdi mdi-cancel"></i> Cancel</a>
                                    <button class="btn btn-primary m-2" style="width: 130px;" type="submit"><i
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

    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>



    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // $('#description').summernote({
        //     placeholder: 'Write Description Here',
        //     tabsize: 2,
        //     height: 350
        // });
    </script>

    <script>
        $(document).ready(function() {
            var availableBalance = 0;

            $('#expense_category_id').select2({
                placeholder: 'Select Expense Category',
                allowClear: true,
                width: '100%'
            }).on('change', function() {
                var categoryId = $(this).val();
                if (categoryId) {
                    // Get category details
                    $.ajax({
                        url: "{{ route('GetExpenseCategoryDetails') }}",
                        type: "GET",
                        data: { category_id: categoryId },
                        success: function(response) {
                            if (response.success) {
                                $('#credit_account_name').text(response.credit_account.name);
                                $('#available_balance').text('৳' + response.credit_account.formatted_balance);
                                $('#account_info').show();
                                availableBalance = parseFloat(response.credit_account.balance);
                                
                                // Check balance on amount change
                                checkBalance();
                            } else {
                                toastr.error(response.message);
                                $('#account_info').hide();
                                availableBalance = 0;
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching category details:", error);
                            toastr.error('Error loading category details');
                            $('#account_info').hide();
                            availableBalance = 0;
                        }
                    });
                } else {
                    $('#account_info').hide();
                    availableBalance = 0;
                }
            });

            $('#expense_amt').on('input change', function() {
                checkBalance();
            });

            function checkBalance() {
                var amount = parseFloat($('#expense_amt').val()) || 0;
                if (amount > 0 && availableBalance > 0) {
                    if (amount > availableBalance) {
                        $('#balance_warning').show();
                        $('#warning_balance').text('৳' + availableBalance.toFixed(2));
                        $('button[type="submit"]').prop('disabled', true);
                    } else {
                        $('#balance_warning').hide();
                        $('button[type="submit"]').prop('disabled', false);
                    }
                } else {
                    $('#balance_warning').hide();
                    if (amount > 0 && availableBalance === 0) {
                        $('button[type="submit"]').prop('disabled', true);
                    } else {
                        $('button[type="submit"]').prop('disabled', false);
                    }
                }
            }

            $('form').on('submit', function(e) {
                var amount = parseFloat($('#expense_amt').val()) || 0;
                if (amount > availableBalance && availableBalance > 0) {
                    e.preventDefault();
                    toastr.error('Insufficient balance! Available: ৳' + availableBalance.toFixed(2));
                    return false;
                }
            });
        });
    </script>
@endsection
