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
    Edit Deposit
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Update Form</h4>

                    <form class="needs-validation" method="POST" action="{{ url('update/deposit') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="deposit_id" value="{{ $data->id }}">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-lg-6">

                                        <div class="form-group" style="margin-bottom: 20px;">
                                            <label for="deposit_date">Deposit Date<span class="text-danger">*</span></label>
                                            <input type="date" id="deposit_date" name="deposit_date" class="form-control"
                                                placeholder="Enter deposit date Here" value="{{ $data->deposit_date }}">
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('deposit_date')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Debit Account --}}
                                        <div class="form-group" style="margin-bottom: 30px;">
                                            <label for="debit_id">Debit Account<span class="text-danger">*</span></label>
                                            <select id="debit_id" name="debit_id" class="form-control">
                                                <option></option>
                                                @foreach ($accounts as $account)
                                                    <option value="{{ $account->id }}"
                                                        {{ $account->id == $data->debit_account_id ? 'selected' : '' }}>
                                                        {{ $account->title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('debit_id')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>


                                        {{-- Credit Account --}}
                                        <div class="form-group" style="margin-bottom: 30px;">
                                            <label for="credit_id">Credit Account<span class="text-danger">*</span></label>
                                            <select id="credit_id" name="credit_id" class="form-control">
                                                <option></option>
                                                @foreach ($accounts as $account)
                                                    <option value="{{ $account->id }}"
                                                        {{ $account->id == $data->credit_account_id ? 'selected' : '' }}>
                                                        {{ $account->title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('credit_id')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>


                                        {{-- <div class="form-group" style="margin-bottom: 20px;">
                                            <label for="account_id">Debit Account<span class="text-danger">*</span></label>
                                            <select id="account_id" name="account_id" class="form-control">
                                                <option></option>
                                                @foreach ($accounts as $account)
                                                    <option value="{{ $account->id }}"
                                                        {{ $data->debit_account_id == $account->id ? 'selected' : (old('account_id') == $account->id ? 'selected' : '') }}>
                                                        {{ $account->account_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('account_id')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div> --}}


                                        {{-- <div class="form-group" style="margin-bottom: 20px;">
                                            <label for="account_id">Credit Account<span class="text-danger">*</span></label>
                                            <select id="account_id" name="account_id" class="form-control">
                                                <option></option>
                                                @foreach ($accounts as $account)
                                                    <option value="{{ $account->id }}"
                                                        {{ $data->credit_account_id == $account->id ? 'selected' : (old('account_id') == $account->id ? 'selected' : '') }}>
                                                        {{ $account->account_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('account_id')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div> --}}


                                        {{-- <div class="form-group">
                                            <label for="customer_source_type_id">Customer Source Type<span
                                                    class="text-danger">*</span></label>
                                            <select id="customer_source_type_id" name="customer_source_type_id"
                                                class="form-control">
                                                <option></option>
                                                @foreach ($customer_source_types as $customer_source_type)
                                                    <option value="{{ $customer_source_type->id }}"
                                                        {{ $customer_source_type->id == $data->customer_source_type_id ? 'selected' : '' }}>
                                                        {{ $customer_source_type->title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('customer_source_type_id')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div> --}}


                                        {{-- <div class="form-group">
                                            <label for="payment_type">Payment Type <span class="text-danger">*</span></label>
                                            <input type="text" id="payment_type" name="payment_type"
                                                class="form-control" value="{{ $data->payment_type }}"
                                                placeholder="Enter payment type Here">
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('payment_type')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div> --}}


                                        <div class="form-group" style="margin-bottom: 20px;">
                                            <label for="debit_credit_amount">Amount <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="debit_credit_amount" name="debit_credit_amount"
                                                class="form-control" placeholder="Enter Debit or Credit Amount Here"
                                                value="{{ $data->debit_amt ?? $data->credit_amt }}">
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('debit_credit_amount')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>



                                        <div class="form-group">
                                            <label for="note">Note</label>
                                            <textarea id="note" name="note" class="form-control" placeholder="Enter Description Here">{{ $data->note }}</textarea>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('note')
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

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group text-center pt-3">
                                    <a href="{{ url('view/all/deposit') }}" style="width: 130px;"
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
    <script src="{{ url('assets') }}/plugins/selecttree/select2totree.js" />
    </script>
    <script src="{{ url('assets') }}/js/tagsinput.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // $('#note').summernote({
        //     placeholder: 'Write Description Here',
        //     tabsize: 2,
        //     height: 350
        // });
    </script>

    <script>
        $(document).ready(function() {
            // $('#deposit_type').select2({
            //     placeholder: 'Select Account',
            //     allowClear: true,
            //     width: '100%'
            // });

            // $('#deposit_type_id').select2({
            //     placeholder: 'Select Deposit Type',
            //     allowClear: true,
            //     width: '100%'
            // });

            // $('#debit_id').select2({
            //     placeholder: 'Select Payment Type',
            //     allowClear: true,
            //     width: '100%'
            // });

            // $('#credit_id').select2({
            //     placeholder: 'Select Payment Type',
            //     allowClear: true,
            //     width: '100%'
            // });
        });
    </script>

    <script>
        $(document).ready(function() {
            $.ajax({
                url: "{{ route('GetJsonAcAccount') }}", // Define this route in web.php
                type: "GET",
                success: function(response) {
                    console.log("response.data", response.data);

                    $("#debit_id").select2ToTree({
                        treeData: {
                            dataArr: response
                        },
                        maximumSelectionLength: 3
                    }).select2ToTree({
                        placeholder: "Select Debit Account",
                        allowClear: true
                    });
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching account data:", error);
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $.ajax({
                url: "{{ route('GetJsonAcAccount') }}", // Define this route in web.php
                type: "GET",
                success: function(response) {
                    console.log("response.data", response.data);

                    $("#credit_id").select2ToTree({
                        treeData: {
                            dataArr: response
                        },
                        maximumSelectionLength: 3
                    }).select2ToTree({
                        placeholder: "Select Credit Account",
                        allowClear: true
                    });
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching account data:", error);
                }
            });
        });
    </script>

    {{-- Include Select2ToTree Script --}}
    <script>
        $(document).ready(function() {
            $('#debit_id, #credit_id').select2ToTree({
                treeData: {
                    dataArr: @json($accounts),
                    id: 'id',
                    text: 'title',
                    parentId: 'parent_id'
                },
                multiple: false,
                placeholder: "Select an account",
                allowClear: true
            });
        });
    </script>
@endsection
