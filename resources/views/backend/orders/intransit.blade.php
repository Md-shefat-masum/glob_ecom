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
    </style>
@endsection

@section('page_title')
    Orders
@endsection
@section('page_heading')
    View All Intransit Orders
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Intransit Orders</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0 data-table">
                            <thead>
                                <tr>
                                    <th class="text-center">SL</th>
                                    <th class="text-center">Order No</th>
                                    <th class="text-center">Order Date</th>
                                    <th class="text-center">Payment</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Sub Total</th>
                                    <th class="text-center">Discount</th>
                                    <th class="text-center">Delivery</th>
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
            stateSave: true,
            pageLength: 15,
            lengthMenu: [15, 25, 50, 100],
            ajax: "{{ url('view/intransit/orders') }}",
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
                {
                    data: 'payment_status',
                    name: 'payment_status'
                },
                {
                    data: 'order_status',
                    name: 'order_status'
                },
                {
                    data: 'sub_total',
                    name: 'sub_total'
                },
                { data: 'discount', name: 'discount' },
                { data: 'delivery_fee', name: 'delivery_fee' },
                { data: 'total', name: 'total' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            }
        });
    </script>
@endsection