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
    Income Category
@endsection
@section('page_heading')
    Add an Income Category
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-3">Income Category</h4>
                        <a href="{{ route('ViewAllIncomeCategory')}}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>

                    <form class="needs-validation" method="POST" action="{{ url('save/new/income-category') }}"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-lg-12">

                                <div class="row">

                                    <div class="col-lg-6">

                                        <div class="form-group">
                                            <label for="name">Category Name <span class="text-danger">*</span></label>
                                            <input type="text" id="name" name="name" class="form-control"
                                                placeholder="Enter category name Here" required>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('name')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="code">Category Code <span class="text-danger">*</span></label>
                                            <input type="text" id="code" name="code" maxlength="60"
                                                class="form-control" placeholder="Enter category code here" required>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('code')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group" style="margin-bottom: 30px;">
                                            <label for="credit_id">
                                                Received In (Payment Method) <span class="text-danger">*</span>
                                            </label>
                                            <select id="credit_id" name="credit_id" class="form-control" required>
                                                <option value="">Select Income Head</option>
                                            </select>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('credit_id')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="form-group" style="margin-bottom: 30px;">
                                            <label for="debit_id">
                                                Revenue Head (Revenue Account) <span class="text-danger">*</span>
                                            </label>
                                            <select id="debit_id" name="debit_id" class="form-control" required>
                                                <option value="">Select Revenue Head</option>
                                            </select>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('debit_id')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="description">Description</label>
                                            <textarea id="description" name="description" class="form-control"
                                                placeholder="Enter Description Here"></textarea>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('description')
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
                                    <a href="{{ url('view/all/income-category') }}" style="width: 130px;"
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
        $(document).ready(function () {
            // Load From Account (Credit accounts from payment types - asset accounts)
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
                            placeholder: "Select From Account",
                            allowClear: true
                        });
                    } else {
                        $("#credit_id").select2({
                            placeholder: "Select From Account",
                            allowClear: true,
                            width: '100%'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error loading credit accounts:", error);
                    $("#credit_id").select2({
                        placeholder: "Select From Account",
                        allowClear: true,
                        width: '100%'
                    });
                }
            });

            // Load To Account (Revenue accounts)
            $.ajax({
                url: "{{ route('GetJsonAcAccountRevenue') }}",
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
                            placeholder: "Select To Account",
                            allowClear: true
                        });
                    } else {
                        $("#debit_id").select2({
                            placeholder: "Select To Account",
                            allowClear: true,
                            width: '100%'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error loading revenue accounts:", error);
                    $("#debit_id").select2({
                        placeholder: "Select To Account",
                        allowClear: true,
                        width: '100%'
                    });
                }
            });
        });
    </script>
@endsection

