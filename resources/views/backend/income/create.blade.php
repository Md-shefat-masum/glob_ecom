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
    Income
@endsection
@section('page_heading')
    Add an Income
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-3">Income</h4>
                        <a href="{{ route('ViewAllIncome')}}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>

                    <form class="needs-validation" method="POST" action="{{ url('save/new/income') }}"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-lg-6">

                                        <div class="form-group">
                                            <label for="date">Income Date<span class="text-danger">*</span></label>
                                            <input type="date" id="date" name="date" maxlength="255"
                                                class="form-control" placeholder="Enter income date Here"
                                                value="{{ date('Y-m-d') }}" required>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('date')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group" style="margin-bottom: 30px;">
                                            <label for="category_id">Income Category<span class="text-danger">*</span></label>
                                            <select id="category_id" name="category_id" class="form-control" required>
                                                <option value="">Select Income Category</option>
                                                @foreach ($income_categories as $income_category)
                                                    <option value="{{ $income_category->id }}"
                                                        {{ old('category_id') == $income_category->id ? 'selected' : '' }}>
                                                        {{ $income_category->name }}
                                                        @if($income_category->debitAccount && $income_category->creditAccount)
                                                            (Income Head: {{ $income_category->creditAccount->account_name }}, Revenue Head: {{ $income_category->debitAccount->account_name }})
                                                        @elseif($income_category->debitAccount)
                                                            (Revenue Head: {{ $income_category->debitAccount->account_name }})
                                                        @elseif($income_category->creditAccount)
                                                            (Income Head: {{ $income_category->creditAccount->account_name }})
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('category_id')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                            <div id="account_info" class="mt-2" style="display: none;">
                                                <div class="alert alert-info">
                                                    <strong>Income Head:</strong> <span id="credit_account_name"></span><br>
                                                    <strong>Available Balance:</strong> <span id="available_balance" class="font-weight-bold"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="amount">Income Amount <span class="text-danger">*</span></label>
                                            <input type="number" step="0.01" id="amount" name="amount" 
                                                class="form-control" placeholder="Enter income amount here" min="0.01" required>
                                            <div id="balance_warning" class="mt-2" style="display: none;">
                                                <div class="alert alert-danger">
                                                    <strong>Insufficient Balance!</strong> Available: <span id="warning_balance"></span>
                                                </div>
                                            </div>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('amount')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="income_for">Income For <span class="text-danger">*</span></label>
                                            <input type="text" id="income_for" name="income_for" class="form-control"
                                                placeholder="Income for What" required>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('income_for')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="reference">Reference</label>
                                            <input type="text" id="reference" name="reference" maxlength="60"
                                                class="form-control" placeholder="Enter reference for income">
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('reference')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="note">Note</label>
                                            <textarea id="note" name="note" class="form-control" placeholder="Enter Note Here"></textarea>
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
                                    <a href="{{ url('view/all/income') }}" style="width: 130px;"
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
    <script src="{{ url('assets') }}/plugins/selecttree/select2totree.js"></script>
    <script src="{{ url('assets') }}/js/tagsinput.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            var availableBalance = 0;

            $('#category_id').select2({
                placeholder: 'Select Income Category',
                allowClear: true,
                width: '100%'
            }).on('change', function() {
                var categoryId = $(this).val();
                if (categoryId) {
                    // Get category details
                    $.ajax({
                        url: "{{ route('GetIncomeCategoryDetails') }}",
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

            $('#amount').on('input change', function() {
                checkBalance();
            });

            function checkBalance() {
                var amount = parseFloat($('#amount').val()) || 0;
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
                var amount = parseFloat($('#amount').val()) || 0;
                if (amount > availableBalance && availableBalance > 0) {
                    e.preventDefault();
                    toastr.error('Insufficient balance! Available: ৳' + availableBalance.toFixed(2));
                    return false;
                }
            });
        });
    </script>
@endsection

