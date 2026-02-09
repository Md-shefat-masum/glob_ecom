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
        .filter-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
@endsection

@section('page_title')
    Supplier Payments List
@endsection

@section('page_heading')
    Supplier Payments History
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">All Supplier Payments</h4>
                        <a href="{{ route('ViewAllSupplierPayments') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>

                    <!-- Filter Section -->
                    <div class="filter-section">
                        <form id="filterForm">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="supplier_filter">Supplier</label>
                                    <select id="supplier_filter" class="form-control">
                                        <option value="">All Suppliers</option>
                                        @foreach($suppliers as $sup)
                                            <option value="{{ $sup->id }}" {{ $supplier && $supplier->id == $sup->id ? 'selected' : '' }}>
                                                {{ $sup->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="date_from">Date From</label>
                                    <input type="date" id="date_from" class="form-control" name="date_from">
                                </div>
                                <div class="col-md-3">
                                    <label for="date_to">Date To</label>
                                    <input type="date" id="date_to" class="form-control" name="date_to">
                                </div>
                                <div class="col-md-2">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="button" id="applyFilter" class="btn btn-primary btn-sm w-100">
                                            <i class="fas fa-filter"></i> Filter
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-12 text-right">
                                    <button type="button" id="printBtn" class="btn btn-info btn-sm">
                                        <i class="fas fa-print"></i> Print
                                    </button>
                                    <button type="button" id="resetFilter" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-redo"></i> Reset
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered mb-0 data-table">
                            <thead>
                                <tr>
                                    <th class="text-center">SL</th>
                                    <th class="text-center">Supplier</th>
                                    <th class="text-center">Purchase Code</th>
                                    <th class="text-center">Payment Date</th>
                                    <th class="text-center">Type</th>
                                    <th class="text-center">Amount</th>
                                    <th class="text-center">Note</th>
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
                ajax: {
                    url: "{{ route('ViewSupplierPayments', $supplier ? $supplier->id : 'all') }}",
                    data: function(d) {
                        d.supplier_id = $('#supplier_filter').val();
                        d.date_from = $('#date_from').val();
                        d.date_to = $('#date_to').val();
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'supplier', name: 'supplier'},
                    {data: 'purchase_code', name: 'purchase_code'},
                    {data: 'payment_date', name: 'payment_date'},
                    {data: 'payment_type', name: 'payment_type'},
                    {data: 'payment', name: 'payment'},
                    {data: 'payment_note', name: 'payment_note'},
                ],
                order: [[0, 'desc']]
            });

            // Apply filter
            $('#applyFilter').on('click', function() {
                table.ajax.reload();
            });

            // Reset filter
            $('#resetFilter').on('click', function() {
                $('#supplier_filter').val('');
                $('#date_from').val('');
                $('#date_to').val('');
                table.ajax.reload();
            });

            // Print functionality
            $('#printBtn').on('click', function() {
                window.print();
            });
        });
    </script>

    <style media="print">
        .filter-section, .btn, .dataTables_length, .dataTables_filter, .dataTables_info, .dataTables_paginate {
            display: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
    </style>
@endsection
