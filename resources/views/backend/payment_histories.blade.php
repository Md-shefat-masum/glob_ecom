@extends('backend.master')

@section('header_css')
    <link href="{{ url('dataTable') }}/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="{{ url('dataTable') }}/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <style>
        .dataTables_wrapper .dataTables_paginate .paginate_button{
            padding: 0px;
            border-radius: 4px;
        }
        table.dataTable tbody td:nth-child(1){
            font-weight: 600;
        }
        table.dataTable tbody td{
            text-align: center !important;
        }
        tfoot {
            display: table-header-group !important;
        }
        tfoot th{
            text-align: center;
        }

    </style>
@endsection

@section('page_title')
    Order Payment Histories
@endsection
@section('page_heading')
    View All Order Payment Histories
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Order Payment History</h4>
                    <div class="table-responsive">

                        <div class="table-responsive">
                            <table class="table table-bordered mb-0 data-table">
                                <thead>
                                    <tr>
                                        <th class="text-center">SL</th>
                                        <th class="text-center">Payment Through</th>
                                        <th class="text-center">Transaction ID</th>
                                        <th class="text-center">Card Type</th>
                                        <th class="text-center">Card Brand</th>
                                        <th class="text-center">Amount</th>
                                        <th class="text-center">Store Amount</th>
                                        <th class="text-center">Currency</th>
                                        <th class="text-center">Bank Tran ID</th>
                                        <th class="text-center">Datetime</th>
                                        <th class="text-center">Status</th>
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
    </div>

@endsection


@section('footer_js')

    {{-- js code for data table --}}
    <script src="{{ url('dataTable') }}/js/jquery.validate.js"></script>
    <script src="{{ url('dataTable') }}/js/jquery.dataTables.min.js"></script>
    <script src="{{ url('dataTable') }}/js/dataTables.bootstrap4.min.js"></script>

    <script type="text/javascript">
        var table = $('.data-table').DataTable({
            processing: true,
            stateSave: true,
            serverSide: true,
            pageLength: 15,
            lengthMenu: [15, 25, 50, 100],
            ajax: "{{ url('view/payment/history') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'payment_through', name: 'payment_through'},
                {data: 'tran_id', name: 'tran_id'},
                {data: 'card_type', name: 'card_type'},
                {data: 'card_brand', name: 'card_brand'},
                {data: 'amount', name: 'amount'},
                {data: 'store_amount', name: 'store_amount'},
                {data: 'currency', name: 'currency'},
                {data: 'bank_tran_id', name: 'bank_tran_id'},
                {data: 'tran_date', name: 'tran_date'},
                {data: 'status', name: 'status'},
            ]
        });
    </script>
@endsection
