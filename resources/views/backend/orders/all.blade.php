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
            font-weight: 600;
        }

        table.dataTable tbody td {
            text-align: center !important;
        }

        table.dataTable tbody td:nth-child(6) {
            min-width: 100px !important;
        }

        table.dataTable tbody td:nth-child(7) {
            min-width: 80px !important;
        }

        table.dataTable tbody td:nth-child(8) {
            min-width: 80px !important;
        }

        table.dataTable tbody td:nth-child(9) {
            min-width: 80px !important;
        }

        table.dataTable tbody td:nth-child(10) {
            min-width: 100px !important;
        }

        tfoot {
            display: table-header-group !important;
        }

        tfoot th {
            text-align: center;
        }

        .graph_card {
            position: relative
        }

        .graph_card i {
            position: absolute;
            top: 18px;
            right: 18px;
            font-size: 18px;
            height: 35px;
            width: 35px;
            line-height: 33px;
            text-align: center;
            border-radius: 50%;
            font-weight: 300;
        }
    </style>
@endsection

@section('page_title')
    Orders
@endsection
@section('page_heading')
    View All Orders
@endsection

@section('content')

    <div class="row">
        <div class="col-lg-6 col-xl-3">
            <div class="card graph_card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="text-uppercase font-size-12 text-muted mb-3">Total Pending Orders</h6>
                            <span class="h3 mb-0">
                                ৳ {{ number_format(DB::table('orders')->where('order_status', 0)->sum('total'), 2) }}
                            </span>
                        </div>
                    </div> <!-- end row -->

                    <div id="sparkline1" class="mt-3"></div>
                </div> <!-- end card-body-->
                <i class="feather-shopping-cart" style="color: #c28a00; background: #daa5202e;"></i>
            </div> <!-- end card-->
        </div> <!-- end col-->

        <div class="col-lg-6 col-xl-3">
            <div class="card graph_card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="text-uppercase font-size-12 text-muted mb-3">Total Approved Orders</h6>
                            <span class="h3 mb-0">
                                ৳
                                {{ number_format(DB::table('orders')->where('order_status', 1)->orWhere('order_status', 2)->sum('total'), 2) }}
                            </span>
                        </div>
                    </div> <!-- end row -->
                    <div id="sparkline2" class="mt-3"></div>
                </div> <!-- end card-body-->
                <i class="feather-trending-up" style="color: #0074E4; background: #0074E42E;"></i>
            </div> <!-- end card-->
        </div> <!-- end col-->

        <div class="col-lg-6 col-xl-3">
            <div class="card graph_card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="text-uppercase font-size-12 text-muted mb-3">Total Delivered Orders</h6>
                            <span class="h3 mb-0">
                                ৳ {{ number_format(DB::table('orders')->where('order_status', 4)->sum('total'), 2) }}
                            </span>
                        </div>
                    </div> <!-- end row -->

                    <div id="sparkline3" class="mt-3"></div>
                </div> <!-- end card-body-->
                <i class="feather-package" style="color: #027e02; background: #027e0238;"></i>
            </div> <!-- end card-->
        </div> <!-- end col-->

        <div class="col-lg-6 col-xl-3">
            <div class="card graph_card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="text-uppercase font-size-12 text-muted mb-3">Total Cancelled Orders</h6>
                            <span class="h3 mb-0">
                                ৳ {{ number_format(DB::table('orders')->where('order_status', 6)->sum('total'), 2) }}
                            </span>
                        </div>
                    </div> <!-- end row -->

                    <div id="sparkline4" class="mt-3"></div>
                </div> <!-- end card-body-->
                <i class="feather-trash-2" style="color: #a60000; background: #a6000026;"></i>
            </div> <!-- end card-->
        </div> <!-- end col-->
    </div>


    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">All Orders</h4>
                    <div class="table-responsive">
                        <label id="customFilter">
                            <a href="{{url('/create/new/order')}}" class="btn btn-primary btn-sm"
                                style="margin-left: 5px"><b><i class="fas fa-plus"></i> Create Order</b></a>
                            <a href="{{url('/view/trash/orders')}}" class="btn btn-danger btn-sm"
                                style="margin-left: 5px"><b><i class="fas fa-trash"></i> Trashed Order</b></a>
                        </label>
                        <table class="table table-bordered mb-0 data-table">
                            <thead>
                                <tr>
                                    <th class="text-center">SL</th>
                                    <th class="text-center">Order No</th>
                                    <th class="text-center" style="min-width: 120px !important;">Order Date</th>
                                    <th class="text-center">Name</th>
                                    <th class="text-center">Email</th>
                                    <th class="text-center">Phone</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Payment</th>
                                    <th class="text-center">Total</th>
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
            pageLength: 10,
            ajax: "{{ url('view/orders') }}",
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'order_no',
                    name: 'order_no'
                }, //orderable: true, searchable: true
                {
                    data: 'order_date',
                    name: 'order_date'
                },
                { data: 'customer_name', name: 'customer_name' },
                { data: 'customer_email', name: 'customer_email' },
                { data: 'customer_phone', name: 'customer_phone' },
                {
                    data: 'quantity',
                    name: 'quantity'
                },
                {
                    data: 'order_status',
                    name: 'order_status'
                },
                {
                    data: 'payment_status',
                    name: 'payment_status'
                },
                { data: 'total', name: 'total' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
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

        $('body').on('click', '.cancelBtn', function () {
            var slug = $(this).data("id");
            if (confirm("Are You sure to Cancel !")) {
                $.ajax({
                    type: "GET",
                    url: "{{ url('cancel/order') }}" + '/' + slug,
                    success: function (data) {
                        table.draw(false);
                        toastr.error("Order has been Cancelled", "Cancelled Successfully");
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            }
        });

        $('body').on('click', '.approveBtn', function () {
            var slug = $(this).data("id");
            if (confirm("Are You sure to Approve !")) {
                $.ajax({
                    type: "GET",
                    url: "{{ url('approve/order') }}" + '/' + slug,
                    success: function (data) {
                        table.draw(false);
                        toastr.success("Order has been Approved", "Approved Successfully");
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            }
        });

        $('body').on('click', '.deleteBtn', function () {
            var slug = $(this).data("id");
            if (confirm("Are You sure to Delete Order !")) {
                if (check_demo_user()) {
                    return false;
                }
                $.ajax({
                    type: "GET",
                    url: "{{ url('delete/order') }}" + '/' + slug,
                    success: function (data) {
                        table.draw(false);
                        toastr.error("Order has been Deleted", "Deleted Successfully");
                        location.reload(true);
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            }
        });
    </script>

@endsection