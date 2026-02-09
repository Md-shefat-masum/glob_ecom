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

        tfoot {
            display: table-header-group !important;
        }

        tfoot th {
            text-align: center;
        }
    </style>
@endsection

@section('page_title')
    Permission Routes
@endsection
@section('page_heading')
    View All Permission Routes
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Permission Route List</h4>
                    <div class="table-responsive">

                        <label id="customFilter">
                            <a href="{{ url('regenerate/permission/routes') }}"
                                class="btn btn-success btn-sm d-inline-block text-white"
                                style="margin-left: 5px; cursor:pointer"><b><i class="feather-repeat"></i> Regenerate
                                    Routes</b></a>
                        </label>

                        <table class="table table-bordered mb-0 data-table">
                            <thead>
                                <tr>
                                    <th class="text-center">SL</th>
                                    <th class="text-center">Module</th>
                                    <th class="text-center">Group</th>
                                    <th class="text-center">Route</th>
                                    <th class="text-center">Name</th>
                                    <th class="text-center">Method</th>
                                    <th class="text-center">Created At</th>
                                    <th class="text-center">Updated At</th>
                                </tr>
                            </thead>
                            <tbody>

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
            pageLength: 10,
            processing: true,
            serverSide: true,
            ajax: "{{ url('/view/permission/routes') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'route_module_name',
                    name: 'route_module_name'
                },
                {
                    data: 'route_group_name',
                    name: 'route_group_name'
                },

                {
                    data: 'route',
                    name: 'route'
                }, //orderable: true, searchable: true
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'method',
                    name: 'method'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'updated_at',
                    name: 'updated_at'
                }
            ],
        });

        $(".dataTables_filter").append($("#customFilter"));
    </script>
@endsection
