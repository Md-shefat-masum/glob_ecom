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
    Deposit
@endsection
@section('page_heading')
    Add an deposit
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Deposit</h4>

                    <form class="needs-validation" method="POST" action="{{ url('save/new/deposit') }}"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-lg-12">

                                <div class="row">


                                    <div class="col-lg-6">



                                        {{-- <div class="form-group" style="margin-bottom: 30px;">
                                            <label for="payment_code">Payment Code <span class="text-danger">*</span></label>
                                            <input type="text" id="payment_code" name="payment_code"
                                                class="form-control" placeholder="Enter payment code here">
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('payment_code')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div> --}}

                                        <div class="form-group" style="margin-bottom: 30px;">
                                            <label for="deposit_date">Deposit Date<span class="text-danger">*</span></label>
                                            <input type="date" id="deposit_date" name="deposit_date" class="form-control"
                                                placeholder="Enter deposit date Here" value="{{ date('Y-m-d') }}">
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('deposit_date')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>



                                        {{-- <div class="form-group">
                                            <label class="d-block">Account Type <span class="text-danger">*</span></label>
                                            <div class="btn-group" role="group">
                                                <input type="radio" class="btn-check" name="account_type" id="debit" value="DEBIT" {{ old('account_type') == 'DEBIT' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-danger" for="debit">Debit</label>
                                        
                                                <input type="radio" class="btn-check" name="account_type" id="credit" value="CREDIT" {{ old('account_type') == 'CREDIT' ? 'checked' : '' }}>
                                                <label class="btn btn-outline-success" for="credit">Credit</label>
                                            </div>
                                        
                                            <div class="invalid-feedback d-block">
                                                @error('account_type')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div> --}}

                                        {{-- <div class="form-group">
                                            <label class="d-block mb-2">Account Type <span class="text-danger">*</span></label>
                                        
                                            <div class="d-flex flex-column gap-2">
                                                <div>
                                                    <input type="radio" class="btn-check" name="account_type" id="debit" value="DEBIT" 
                                                           {{ old('account_type') == 'DEBIT' ? 'checked' : '' }}>
                                                    <label class="w-100 p-2 text-danger fw-bold" style="cursor: pointer;" for="debit">Debit</label>
                                                </div>
                                        
                                                <div>
                                                    <input type="radio" class="btn-check" name="account_type" id="credit" value="CREDIT" 
                                                           {{ old('account_type') == 'CREDIT' ? 'checked' : '' }}>
                                                    <label class="w-100 p-2 text-success fw-bold" style="cursor: pointer;" for="credit">Credit</label>
                                                </div>
                                            </div>
                                        
                                            <div class="invalid-feedback d-block">
                                                @error('account_type')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div> --}}



                                        {{-- <div class="form-group" style="margin-bottom: 30px;">
                                            <label class="d-block mb-2">Account Type <span class="text-danger">*</span></label>
                                        
                                            <div class="d-flex align-items-center gap-4">
                                                <!-- Debit Option -->
                                                <div class="custom-radio my_custom_radion">
                                                    <input type="radio" name="account_type" id="debit" value="DEBIT"
                                                           {{ old('account_type') == 'DEBIT' ? 'checked' : '' }}>
                                                    <label for="debit" class="fw-bold text-danger account_type_label">Debit</label>
                                                </div>
                                        
                                                <!-- Credit Option -->
                                                <div class="custom-radio my_custom_radion">
                                                    <input type="radio" name="account_type" id="credit" value="CREDIT"
                                                           {{ old('account_type') == 'CREDIT' ? 'checked' : '' }}>
                                                    <label for="credit" class="fw-bold text-success account_type_label">Credit</label>
                                                </div>
                                            </div>
                                        
                                            <div class="invalid-feedback d-block">
                                                @error('account_type')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <style>
                                            
                                            .my_custom_radion {
                                                display: flex;
                                                align-items: center;
                                                gap: 10px; 
                                                margin-right: 10px;
                                            }
                                        
                                            
                                            input[type="radio"] {
                                                width: 16px;
                                                height: 16px;
                                                cursor: pointer;
                                                margin: 0;  
                                                vertical-align: middle; 
                                            }
                                        
                                            
                                            .account_type_label {
                                                margin: 0;
                                                line-height: 1;
                                            }
                                        </style> --}}

                                        {{-- <select id="sel_1" style="width:16em" multiple>
                                        </select> --}}


                                        {{-- <select id="debit_id" name="debit_id" class="form-control"> --}}
                                        <div class="form-group" style="margin-bottom: 30px;">
                                            <label for="debit_id">Debit Account<span class="text-danger">*</span></label>
                                            <select id="debit_id" name="debit_id" class="form-control">
                                                <option></option>
                                            </select>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('debit_id')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>



                                        {{-- Debit Account --}}
                                        {{-- <div class="form-group" style="margin-bottom: 30px;">
                                            <label for="debit_id">Debit Account<span class="text-danger">*</span></label>
                                            <select id="debit_id" name="debit_id" class="form-control">
                                                <option></option>
                                                @foreach ($accounts as $account)
                                                    <option value="{{ $account->id }}"
                                                        {{ old('debit_id') == $account->id ? 'selected' : '' }}>
                                                        {{ $account->account_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('debit_id')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div> --}}

                                        <div class="form-group" style="margin-bottom: 30px;">
                                            <label for="credit_id">Credit Account<span class="text-danger">*</span></label>
                                            <select id="credit_id" name="credit_id" class="form-control">
                                                <option></option>
                                                @foreach ($accounts as $account)
                                                    <option value="{{ $account->id }}"
                                                        {{ old('credit_id') == $account->id ? 'selected' : '' }}>
                                                        {{ $account->account_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('credit_id')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="form-group" style="margin-bottom: 30px;">
                                            <label for="debit_credit_amount">Amount <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" id="debit_credit_amount" name="debit_credit_amount"
                                                class="form-control" placeholder="Enter Debit or Credit Amount Here">
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('debit_credit_amount')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>





                                        {{-- <div class="form-group">
                                            <label for="customer_source_type_id">Customer Source Type<span
                                                    class="text-danger">*</span></label>
                                            <select id="customer_source_type_id" name="customer_source_type_id"
                                                class="form-control">
                                                <option></option>
                                                @foreach ($customer_source_types as $customer_source_type)
                                                    <option value="{{ $customer_source_type->id }}"
                                                        {{ old('customer_source_type_id') == $customer_source_type->id ? 'selected' : '' }}>
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
                                            <label for="reference_id">Reference By<span class="text-danger">*</span></label>
                                            <select id="reference_id" name="reference_id" class="form-control">
                                                <option></option>
                                                @foreach ($users as $reference)
                                                    <option value="{{ $reference->id }}"
                                                        {{ old('reference_id') == $reference->id ? 'selected' : '' }}>
                                                        {{ $reference->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('reference_id')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div> --}}


                                        {{-- <div class="form-group">
                                            <label for="expense_for">Expense For <span class="text-danger">*</span></label>
                                            <input type="text" id="expense_for" name="expense_for"
                                                class="form-control" placeholder="Expense for What">
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('expense_for')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div> --}}




                                        {{-- <div class="form-group">
                                            <label for="reference_no">Reference No <span class="text-danger">*</span></label>
                                            <input type="text" id="reference_no" name="reference_no" maxlength="60"
                                                class="form-control" placeholder="Enter reference number here">
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('reference_no')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div> --}}


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
                            <div class="col--3">
                                <div class="form-group text-center pt-3">
                                    <a href="{{ url('view/all/deposit') }}" style="width: 130px;"
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
    <script src="{{ url('assets') }}/plugins/selecttree/select2totree.js"/></script>
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
            $('#deposit_type').select2({
                placeholder: 'Select Account',
                allowClear: true,
                width: '100%'
            });

            $('#deposit_type_id').select2({
                placeholder: 'Select Deposit Type',
                allowClear: true,
                width: '100%'
            });

            // $('#debit_id').select2({
            //     placeholder: 'Select Payment Type',
            //     allowClear: true,
            //     width: '100%'
            // });

            $('#credit_id').select2({
                placeholder: 'Select Payment Type',
                allowClear: true,
                width: '100%'
            });
        });
    </script>


    {{-- <script>
        var mydata = [{
                id: 1,
                text: "USA",
                inc: [{
                    text: "west",
                    inc: [{
                            id: 111,
                            text: "California",
                            inc: [{
                                    id: 1111,
                                    text: "Los Angeles",
                                    inc: [{
                                        id: 11111,
                                        text: "Hollywood"
                                    }]
                                },
                                {
                                    id: 1112,
                                    text: "San Diego"
                                }
                            ]
                        },
                        {
                            id: 112,
                            text: "Oregon"
                        }
                    ]
                }]
            },
            {
                id: 2,
                text: "India"
            },
            {
                id: 3,
                text: "中国"
            }
        ];
        $("#sel_1").select2ToTree({
            treeData: {
                dataArr: mydata
            },
            maximumSelectionLength: 3
        });
    </script> --}}

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
                    }).select2ToTree({ // Add select2 placeholder
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
    


@endsection
