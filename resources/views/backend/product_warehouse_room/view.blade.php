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
    Room
@endsection
@section('page_heading')
    View All Rooms
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">View All Product Warehouse Rooms</h4>
                    <div class="table-responsive">
                        <label id="customFilter">
                            <a href="{{url('/add/new/product-warehouse-room')}}" class="btn btn-primary btn-sm"
                                style="margin-left: 5px"><b><i class="fas fa-plus"></i> Add Warehouse Room</b></a>
                        </label>
                        <table class="table table-bordered mb-0 data-table">
                            <thead>
                                <tr>
                                    <th class="text-center">SL</th>
                                    <th class="text-center">Room Title</th>
                                    <th class="text-center">Warehouse Title</th>
                                    <th class="text-center">Room Code</th>
                                    <th class="text-center">Description</th>
                                    <th class="text-center">Image</th>
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
            ajax: "{{ url('view/all/product-warehouse-room') }}",
            columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex'
            },
            {
                data: 'room_title', // This is for the product warehouse room title
                name: 'room_title'
            },
            {
                data: 'warehouse_title', // This is for the warehouse title
                name: 'warehouse_title'
            },
            {
                data: 'code',
                name: 'code'
            },
            {
                data: 'description',
                name: 'description',
                render: function (data, type, full, meta) {
                    if (data) {
                        var decodedData = $('<div>').html(data).text(); // Decode any HTML entities
                        var cleanText = decodedData.replace(/(<([^>]+)>)/gi, ""); // Strip any HTML tags
                        return cleanText.substring(0, 20); // Show only the first 20 characters
                    }
                    return '';
                }
            },
            {
                data: 'image',
                name: 'image',
                render: function (data, type, full, meta) {
                    if (data) {
                        return "<img class=\"gridProductImage\" src=\"/" + data + "\" width=\"60\"/>";
                    } else {
                        return '';
                    }
                }
            },
            {
                data: 'status',
                name: 'status'
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

        $('body').on('click', '.deleteBtn', function () {
            var productWarehouseRoomSlug = $(this).data("id");
            if (confirm("Are You sure want to delete !")) {
                if (check_demo_user()) {
                    return false;
                } $.ajax({
                    type: "GET",
                    url: "{{ url('delete/product-warehouse-room') }}" + '/' + productWarehouseRoomSlug,
                    success: function (data) {
                        table.draw(false);
                        toastr.error("Product Warehouse room has been Deleted", "Deleted Successfully");
                    },
                    error: function (xhr) {
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