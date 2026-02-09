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
    </style>
@endsection

@section('page_title')
    Customer Payments
@endsection

@section('page_heading')
    All Customer Payments
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">All Customer Payments</h4>
                    <div class="table-responsive">
                        <label id="customFilter">
                            <a href="{{ route('CreateCustomerPayment') }}" class="btn btn-success btn-sm" style="margin-left: 5px">
                                <b><i class="fas fa-plus"></i> Add Payment/Advance</b>
                            </a>
                            <a href="{{ route('CreateCustomerPaymentReturn') }}" class="btn btn-warning btn-sm" style="margin-left: 5px">
                                <b><i class="fas fa-undo"></i> Payment Return</b>
                            </a>
                        </label>
                        <table class="table table-bordered mb-0 data-table">
                            <thead>
                                <tr>
                                    <th class="text-center">SL</th>
                                    <th class="text-center">Customer</th>
                                    <th class="text-center">Order/Type</th>
                                    <th class="text-center">Payment Date</th>
                                    <th class="text-center">Type</th>
                                    <th class="text-center">Amount</th>
                                    <th class="text-center">Mode</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_js')
    <script src="{{ url('dataTable') }}/js/jquery.dataTables.min.js"></script>
    <script src="{{ url('dataTable') }}/js/dataTables.bootstrap4.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('ViewAllCustomerPayments') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'customer', name: 'customer'},
                    {data: 'order_code', name: 'order_code'},
                    {data: 'payment_date', name: 'payment_date'},
                    {data: 'payment_type', name: 'payment_type'},
                    {data: 'payment', name: 'payment'},
                    {data: 'payment_mode', name: 'payment_mode'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });
        });
    </script>
@endsection

