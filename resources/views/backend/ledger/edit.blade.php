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

                    <form class="needs-validation" method="POST" action="{{ url('update/deposit') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="deposit_id" value="{{ $data->id }}">

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-lg-6">

                                        <div class="form-group" style="margin-bottom: 20px;">
                                            <label for="deposit_date">Deposit Date<span class="text-danger">*</span></label>
                                            <input type="date" id="deposit_date" name="deposit_date" class="form-control"
                                                placeholder="Enter deposit date Here" value="{{ $data->transaction_date }}">
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('deposit_date')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group" style="margin-bottom: 20px;">
                                            <label for="deposit_date">Payment Code<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control"
                                                placeholder="Enter deposit date Here" value="{{ $data->payment_code }}" disabled>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('deposit_date')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Debit Account --}}
                                        @if ($data->debit_account_id != 0)                                        
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

                                        <div class="form-group" style="margin-bottom: 20px;">
                                            <label for="debit_credit_amount">Amount <span class="text-danger">*</span></label>
                                            <input type="text" id="debit_credit_amount" name="debit_credit_amount"
                                                class="form-control" placeholder="Enter Debit or Credit Amount Here"
                                                value="{{ $data->debit_amt }}">
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('debit_credit_amount')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                            <small class="text-danger">
                                                <p>If you edit the amount, its corresponding debit/credit amount will be changed also</p>
                                            </small>
                                        </div>

                                        @endif
                                        
                                        {{-- Credit Account --}}
                                        @if ($data->credit_account_id != 0)     
                                        <div class="form-group" style="margin-bottom: 30px;">
                                            <label for="credit_id">Credit Account<span class="text-danger">*</span></label>
                                            <select id="credit_id" name="credit_id" class="form-control">
                                                <option></option>
                                            </select>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('credit_id')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group" style="margin-bottom: 20px;">
                                            <label for="debit_credit_amount">Amount <span class="text-danger">*</span></label>
                                            <input type="text" id="debit_credit_amount" name="debit_credit_amount"
                                                class="form-control" placeholder="Enter Debit or Credit Amount Here"
                                                value="{{ $data->credit_amt }}">
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('debit_credit_amount')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                            <small class="text-danger">
                                                <p>If you edit the amount, its corresponding debit/credit amount will be changed also</p>
                                            </small>
                                        </div>

                                        @endif



                                        

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

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group text-center pt-3">
                                    <a href="{{ url('view/all/deposit') }}" style="width: 130px;" class="btn btn-danger d-inline-block text-white m-2">
                                        <i class="mdi mdi-cancel"></i> Cancel</a>
                                    <button class="btn btn-primary m-2" style="width: 130px;" type="submit"><i class="fas fa-save"></i> Update</button>
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

    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

    </script>

    <script>
        $(document).ready(function() {
            // Fetch the nested data from the controller
            var nestedData = @json($nestedData);

            // Apply select2totree plugin to Debit Account field
            $("#debit_id").select2ToTree({
                treeData: {
                    dataArr: nestedData
                },
                maximumSelectionLength: 3,
                placeholder: "Select Debit Account",
                allowClear: true
            });

            // Apply select2totree plugin to Credit Account field
            $("#credit_id").select2ToTree({
                treeData: {
                    dataArr: nestedData
                },
                maximumSelectionLength: 3,
                placeholder: "Select Credit Account",
                allowClear: true
            });

            // Set default selected values for Debit and Credit Accounts
            $("#debit_id").val("{{ $data->debit_account_id }}").trigger('change');
            $("#credit_id").val("{{ $data->credit_account_id }}").trigger('change');
        });
    </script>
@endsection
