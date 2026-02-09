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
    Product
@endsection
@section('page_heading')
    View All Products
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Product List</h4>
                    <div class="table-responsive">
                        <label id="customFilter">
                            <a href="{{url('/add/new/product')}}" class="btn btn-primary btn-sm"
                                style="margin-left: 5px"><b><i class="fas fa-plus"></i> Add New Product</b></a>
                        </label>
                        <table class="table table-bordered mb-0 data-table">
                            <thead>
                                <tr>
                                    <th class="text-center">SL</th>
                                    <th class="text-center">Image</th>
                                    <th class="text-center">Category</th>
                                    <th class="text-center">Name</th>
                                    <th class="text-center">Price</th>
                                    <th class="text-center">Offer Price</th>
                                    <th class="text-center">Stock</th>
                                    <th class="text-center">Unit</th>
                                    <th class="text-center">Flag</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            {{-- <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot> --}}
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
            ajax: "{{ url('view/all/product') }}",
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'image',
                    name: 'image',
                    render: function (data, type, full, meta) {
                        if (data) {
                            return "<img class=\"gridProductImage\" src=\"/" + data + "\" width=\"40\"/>";
                        } else {
                            return '';
                        }
                    }
                },
                {
                    data: 'category_name',
                    name: 'category_name'
                }, //orderable: true, searchable: true
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'price',
                    name: 'price'
                },
                {
                    data: 'discount_price',
                    name: 'discount_price'
                },
                {
                    data: 'stock',
                    name: 'stock',
                    render: function (data, type, full, meta) {
                        console.log(full.low_stock);
                        if (data <= full.low_stock ? full.low_stock : 0) {
                            return '<span style="color: red; font-weight: bold;" title="Low Stock: Consider Restocking">' +
                                '<i class="fas fa-exclamation-triangle"></i> ' + data +
                                '</span>';
                        }
                        return data;
                    }
                },
                { data: 'unit_name', name: 'unit_name' },
                { data: 'flag_name', name: 'flag_name' },
                {
                    data: 'status',
                    name: 'status',
                    render: function (data, type, full, meta) {
                        if (data == 1) {
                            return '<span class="btn btn-sm btn-success d-inline-block">Active</span>';
                        } else {
                            return '<span class="btn btn-sm btn-danger d-inline-block">Inactive</span>';
                        }
                    }
                },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
        });
         $(".dataTables_filter").append($("#customFilter"));v
    </script>

    {{-- js code for user crud --}}
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('body').on('click', '.deleteBtn', function () {
            var slug = $(this).data("id");
            if (confirm("Are You sure want to delete !")) {
                if (check_demo_user()) {
                    return false;
                }
                $.ajax({
                    type: "GET",
                    url: "{{ url('delete/product') }}" + '/' + slug,
                    success: function (data) {
                        if (data.data == 1) {
                            table.draw(false);
                            toastr.error("Product has been Deleted", "Deleted Successfully");
                        } else {
                            toastr.warning("Order Available for this Product", "Failed");
                        }
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            }
        });
    </script>
@endsection