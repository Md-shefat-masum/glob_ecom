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
    </style>
@endsection

@section('page_title')
    Edit
@endsection
@section('page_heading')
    Edit Expense Category
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-3">Update Form</h4>
                        <a href="{{ route('ViewAllExpenseCategory')}}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>

                    <form class="needs-validation" method="POST" action="{{ url('update/expense-category') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="expense_category_id" value="{{ $data->id }}">
                        <div class="row">
                            <div class="col-lg-12">

                                <div class="row">

                                    <div class="col-lg-6">

                                        <div class="form-group">
                                            <label for="category_name">Category Name <span class="text-danger">*</span></label>
                                            <input type="text" id="category_name" name="category_name"
                                                class="form-control" value="{{ $data->category_name }}">
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('category_name')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="category_code">Category Code <span class="text-danger">*</span></label>
                                            <input type="text" id="category_code" name="category_code" maxlength="60"
                                                class="form-control" value="{{ $data->category_code }}" required>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('category_code')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group" style="margin-bottom: 30px;">
                                            <label for="credit_id">Paid From (Payment Method) <span class="text-danger">*</span></label>
                                            <select id="credit_id" name="credit_id" class="form-control" required>
                                                <option value="">Select Payment Method</option>
                                            </select>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('credit_id')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group" style="margin-bottom: 30px;">
                                            <label for="debit_id">Paid To (Expense Head) <span class="text-danger">*</span></label>
                                            <select id="debit_id" name="debit_id" class="form-control" required>
                                                <option value="">Select Expense Head</option>
                                            </select>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('debit_id')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- <div class="form-group">
                                            <label for="phone">Phone <span class="text-danger">*</span></label>
                                            <input type="text" id="phone" name="phone" maxlength="60"
                                                class="form-control" value="{{ $data->phone }}"
                                                placeholder="Enter phone number Here">
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('phone')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div> --}}

                                        {{-- <div class="form-group">
                                            <label for="email">Email <span class="text-danger">*</span></label>
                                            <input type="text" id="email" name="email" maxlength="100"
                                                class="form-control" value="{{ $data->email }}"
                                                placeholder="Enter email Here">
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('email')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div> --}}

                                        {{-- <div class="form-group">
                                            <label for="address">Address</label>
                                            <textarea id="address" name="address" class="form-control" placeholder="Enter Address Here">
                                                {{ $data->address }}
                                            </textarea>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('address')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div> --}}

                                        <div class="form-group">
                                            <label for="description">Description</label>
                                            <textarea id="description" name="description" class="form-control" placeholder="Enter Description Here">{{ $data->description }}</textarea>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('description')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select name="status" id="status" class="form-control custom-select">
                                                <option value="active" {{ $data->status == 'active' ? 'selected' : '' }}>
                                                    Active</option>
                                                <option value="inactive"
                                                    {{ $data->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group text-center pt-3">
                                    <a href="{{ url('view/all/expense-category') }}" style="width: 130px;"
                                        class="btn btn-danger d-inline-block text-white m-2" type="submit"><i
                                            class="mdi mdi-cancel"></i> Cancel</a>
                                    <button class="btn btn-primary m-2" style="width: 130px;" type="submit"><i
                                            class="fas fa-save"></i> Update</button>
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




        // $('#description').summernote({
        //     placeholder: 'Write Description Here',
        //     tabsize: 2,
        //     height: 350
        // });

        @if ($data->image && file_exists(public_path($data->image)))
            $(".dropify-preview").eq(0).css("display", "block");
            $(".dropify-clear").eq(0).css("display", "block");
            $(".dropify-filename-inner").eq(0).html("{{ $data->image }}");
            $("span.dropify-render").eq(0).html("<img src='{{ url($data->image) }}'>");
        @endif
    </script>

    <script>
        $(document).ready(function() {
            var existingCreditId = @json($data->credit_id);
            var existingDebitId = @json($data->debit_id);

            // Load Paid From (Credit accounts from payment types)
            $.ajax({
                url: "{{ route('GetJsonAcAccountFromPaymentTypes') }}",
                type: "GET",
                dataType: "json",
                success: function(response) {
                    if (response && response.length > 0) {
                        $("#credit_id").select2ToTree({
                            treeData: {
                                dataArr: response
                            },
                            maximumSelectionLength: 1
                        }).select2ToTree({
                            placeholder: "Select Payment Method",
                            allowClear: true
                        });
                        
                        // Set existing value if available
                        if (existingCreditId) {
                            $("#credit_id").val(existingCreditId).trigger('change');
                        }
                    } else {
                        $("#credit_id").select2({
                            placeholder: "Select Payment Method",
                            allowClear: true,
                            width: '100%'
                        });
                        if (existingCreditId) {
                            $("#credit_id").val(existingCreditId).trigger('change');
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error loading payment method accounts:", error);
                    $("#credit_id").select2({
                        placeholder: "Select Payment Method",
                        allowClear: true,
                        width: '100%'
                    });
                    if (existingCreditId) {
                        $("#credit_id").val(existingCreditId).trigger('change');
                    }
                }
            });

            // Load Paid To (Expense accounts)
            $.ajax({
                url: "{{ route('GetJsonAcAccountExpense') }}",
                type: "GET",
                dataType: "json",
                success: function(response) {
                    if (response && response.length > 0) {
                        $("#debit_id").select2ToTree({
                            treeData: {
                                dataArr: response
                            },
                            maximumSelectionLength: 1
                        }).select2ToTree({
                            placeholder: "Select Expense Head",
                            allowClear: true
                        });
                        
                        // Set existing value if available
                        if (existingDebitId) {
                            $("#debit_id").val(existingDebitId).trigger('change');
                        }
                    } else {
                        $("#debit_id").select2({
                            placeholder: "Select Expense Head",
                            allowClear: true,
                            width: '100%'
                        });
                        if (existingDebitId) {
                            $("#debit_id").val(existingDebitId).trigger('change');
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error loading expense head accounts:", error);
                    $("#debit_id").select2({
                        placeholder: "Select Expense Head",
                        allowClear: true,
                        width: '100%'
                    });
                    if (existingDebitId) {
                        $("#debit_id").val(existingDebitId).trigger('change');
                    }
                }
            });
        });
    </script>

@endsection
