@extends('backend.master')

@section('header_css')
    <link href="{{ url('dataTable') }}/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="{{ url('dataTable') }}/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <style>
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0px;
            border-radius: 4px;
        }

        table.dataTable tbody td:nth-child(1) {
            text-align: center !important;
            font-weight: 600;
        }

        table.dataTable tbody td:nth-child(2) {
            text-align: center !important;
        }

        table.dataTable tbody td:nth-child(3) {
            text-align: center !important;
        }

        table.dataTable tbody td:nth-child(4) {
            text-align: center !important;
        }

        table.dataTable tbody td:nth-child(5) {
            text-align: center !important;
        }

        table.dataTable tbody td:nth-child(6) {
            text-align: center !important;
        }

        table.dataTable tbody td:nth-child(7) {
            text-align: center !important;
        }

        table.dataTable tbody td:nth-child(8) {
            text-align: center !important;
        }

        tfoot {
            display: table-header-group !important;
        }

        tfoot th {
            text-align: center;
        }

        table#DataTables_Table_0 img {
            transition: all .2s linear;
        }

        img.gridProductImage:hover {
            scale: 2;
            cursor: pointer;
        }
    </style>
@endsection

@section('page_title')
    Payment Type
@endsection
@section('page_heading')
    View All Payment Types
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">View All Payment Types</h4>
                    </div>
                    <div class="table-responsive">
                        <label id="customFilter">
                            <a href="{{url('/add/new/expense')}}" class="btn btn-primary btn-sm"
                                style="margin-left: 5px"><b><i class="fas fa-plus"></i> Add New Expense</b></a>
                        </label>
                        <table class="table table-bordered mb-0 data-table">
                            <thead>
                                <tr>
                                    <th class="text-center">SL</th>
                                    <th class="text-center">Expense For</th>
                                    <th class="text-center">From Account</th>
                                    <th class="text-center">To Account</th>
                                    <th class="text-center">Amount</th>
                                    <th class="text-center">Creator</th>
                                    <th class="text-center">Created At</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Your table data goes here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_js')
    {{-- js code for data table --}}
    <script src="{{ url('dataTable') }}/js/jquery.validate.js"></script>
    <script src="{{ url('dataTable') }}/js/jquery.dataTables.min.js"></script>
    <script src="{{ url('dataTable') }}/js/dataTables.bootstrap4.min.js"></script>

    <script type="text/javascript">
        var table = $(".data-table").DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ url('view/all/expense') }}",
            order: [[0, 'desc']],
            columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'expense_for',
                name: 'expense_for'
            },
            {
                data: 'from_account',
                name: 'from_account',
                orderable: false,
                searchable: false
            },
            {
                data: 'to_account',
                name: 'to_account',
                orderable: false,
                searchable: false
            },
            {
                data: 'expense_amt',
                name: 'expense_amt'
            },
            {
                data: 'user',
                name: 'user'
            },
            {
                data: 'created_at',
                name: 'created_at'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }
            ]


        });
         $(".dataTables_filter").append($("#customFilter"));
    </script>


@endsection