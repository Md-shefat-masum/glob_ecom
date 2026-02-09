@extends('backend.master')

@section('header_css')
    <link href="{{ url('dataTable') }}/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="{{ url('dataTable') }}/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <style>
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0px;
            border-radius: 4px;
        }

        table.dataTable tbody td {
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
    Withdraw
@endsection
@section('page_heading')
    View All Withdraws
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">View All Withdraws</h4>
                        <a href="{{ route('AddNewWithdraw') }}" class="btn btn-primary btn-sm" style="margin-left: 5px">
                            <b>
                                <i class="fas fa-plus"></i>
                                Add New Withdraw
                            </b>
                        </a>
                    </div>
                    <div class="table-responsive">
                        <label id="customFilter"></label>
                        <table class="table table-bordered mb-0 data-table">
                            <thead>
                                <tr>
                                    <th class="text-center">SL</th>
                                    <th class="text-center">Owner Name</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Note</th>
                                    <th class="text-center">Media</th>
                                    <th class="text-center">Amount</th>
                                    <th class="text-center">Creator</th>
                                    <th class="text-center">Created At</th>
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
    <script src="{{ url('dataTable') }}/js/jquery.validate.js"></script>
    <script src="{{ url('dataTable') }}/js/jquery.dataTables.min.js"></script>
    <script src="{{ url('dataTable') }}/js/dataTables.bootstrap4.min.js"></script>

    <script type="text/javascript">
        var table = $(".data-table").DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('ViewAllWithdraw') }}",
            order: [[0, 'desc']],
            columns: [{
                data: 'id',
                name: 'id',
                // orderable: false,
                // searchable: false
            },
            {
                data: 'owner_name',
                name: 'owner_name'
            },
            {
                data: 'date',
                name: 'withdraw_date'
            },
            {
                data: 'note',
                name: 'note'
            },
            {
                data: 'media',
                name: 'media'
            },
            {
                data: 'amount',
                name: 'amount',
                orderable: true,
                searchable: false
            },
            {
                data: 'creator_name',
                name: 'creator_name'
            },
            {
                data: 'created_at',
                name: 'created_at'
            }
            ]
        });
        $(".dataTables_filter").append($("#customFilter"));
    </script>
@endsection

