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

        table.dataTable tbody td:nth-child(1) {
            font-weight: 600;
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
    Product Order Returns
@endsection

@section('page_heading')
    All Product Order Returns
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">All Product Order Returns</h4>
                    <div class="table-responsive">
                        <label id="customFilter">
                            <a href="{{ route('ViewAllProductOrder') }}" class="btn btn-primary btn-sm"
                                style="margin-left: 5px"><b><i class="fas fa-list"></i> View Orders</b></a>
                        </label>
                        <table class="table table-bordered mb-0 data-table">
                            <thead>
                                <tr>
                                    <th class="text-center">SL</th>
                                    <th class="text-center">Return Code</th>
                                    <th class="text-center">Order Code</th>
                                    <th class="text-center">Customer</th>
                                    <th class="text-center">Return Date</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Action</th>
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
    <script src="{{ url('dataTable') }}/js/jquery.dataTables.min.js"></script>
    <script src="{{ url('dataTable') }}/js/dataTables.bootstrap4.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('ViewAllProductOrderReturns') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'return_code',
                        name: 'return_code'
                    },
                    {
                        data: 'original_order',
                        name: 'original_order'
                    },
                    {
                        data: 'customer',
                        name: 'customer'
                    },
                    {
                        data: 'return_date',
                        name: 'return_date'
                    },
                    {
                        data: 'total',
                        name: 'total',
                        render: function(data) {
                            return '৳' + parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: 'return_status',
                        name: 'return_status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            // Delete functionality
            $('body').on('click', '.deleteBtn', function() {
                var slug = $(this).data("id");
                var url = '{{ route('DeleteProductOrderReturn', ':slug') }}';
                url = url.replace(':slug', slug);

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to delete this return?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "GET",
                            url: url,
                            success: function(data) {
                                table.draw();
                                Swal.fire(
                                    'Deleted!',
                                    'Return has been deleted.',
                                    'success'
                                );
                            },
                            error: function(data) {
                                console.log('Error:', data);
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection

