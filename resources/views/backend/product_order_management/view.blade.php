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
    Order Management
@endsection
@section('page_heading')
    All Orders
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">All Orders</h4>
                    <div class="table-responsive">
                        <label id="customFilter">
                            <a href="{{ url('/add/new/product-order/manage') }}" class="btn btn-primary btn-sm"
                                style="margin-left: 5px"><b><i class="fas fa-plus"></i> Add Order</b></a>
                        </label>
                        <table class="table table-bordered mb-0 data-table">
                            <thead>
                                <tr>
                                    <th class="text-center">SL</th>
                                    <th class="text-center">Order Date</th>
                                    <th class="text-center">Code</th>
                                    <th class="text-center">Reference</th>
                                    {{-- <th class="text-center">Customer Name</th> --}}
                                    {{-- <th class="text-center">Created By</th> --}}
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
    <script src="{{ url('dataTable') }}/js/jquery.validate.js"></script>
    <script src="{{ url('dataTable') }}/js/jquery.dataTables.min.js"></script>
    <script src="{{ url('dataTable') }}/js/dataTables.bootstrap4.min.js"></script>

    <script type="text/javascript">
        var table = $(".data-table").DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ url('view/all/product-order/manage') }}",
            order: [[0, 'desc']], // Default ordering: ID column (index 0) in descending order
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                // {
                //     data: 'DT_RowIndex',
                //     name: 'DT_RowIndex'
                // },
                {
                    data: 'sale_date',
                    name: 'sale_date'
                },
                {
                    data: 'order_code',
                    name: 'order_code'
                },
                {
                    data: 'reference',
                    name: 'reference'
                },
                // {
                //     data: 'creator',
                //     name: 'creator'
                // },
                {
                    data: 'total',
                    name: 'total'
                },
                {
                    data: 'order_status',
                    name: 'order_status'
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


    {{-- js code for user crud --}}
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('body').on('click', '.deleteBtn', function() {
            console.log('Delete button clicked');
            var productProductQuotationSlug = $(this).data("id");
            if (confirm("Are You sure want to delete !")) {
                if (check_demo_user()) {
                    return false;
                }
                $.ajax({
                    type: "GET",
                    url: "{{ url('delete/product-order/manage') }}" + '/' +
                        productProductQuotationSlug,
                    success: function(data) {
                        table.draw(false);
                        toastr.error("Item has been Deleted",
                            "Deleted Successfully");
                    },
                    error: function(xhr) {
                        // Ensure you're handling the error response properly
                        console.log('Error 11:', xhr.responseJSON.error);
                        // Assuming error message is returned as part of the response JSON
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            toastr.error(xhr.responseJSON.error, "Error");
                        } else {
                            toastr.error("An unexpected error occurred", "Error");
                        }
                    }
                });
            }
        });
    </script>
@endsection
