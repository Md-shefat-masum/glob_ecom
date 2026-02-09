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
                    <h4 class="card-title mb-3">Ledger</h4>

                    <form class="needs-validation" method="POST" action="{{ url() }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-lg-6">

                                        <div class="form-group" style="margin-bottom: 30px;">
                                            <label for="account_id">Debit Account<span class="text-danger">*</span></label>
                                            <select id="account_id" name="account_id" class="form-control">
                                                <option></option>
                                            </select>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('account_id')
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
                                    <a href="{{ url() }}" style="width: 130px;"
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
                url: "{{ route('GetJsonAcAccount') }}", 
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
@endsection
