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
    Customer
@endsection
@section('page_heading')
    View All Customers
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">View All Customers</h4>
                        <div>
                            <a href="{{ route('AddNewCustomerNextContactDate') }}" class="btn btn-primary mr-2">
                                Add Next Date Contact
                            </a>
                            <a href="{{ route('ViewAllCustomerNextContactDate') }}" class="btn btn-secondary">
                                All Next Date Contacts
                            </a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <label id="customFilter">
                            <a href="{{url('/add/new/customers')}}" class="btn btn-primary btn-sm"
                                style="margin-left: 5px"><b><i class="fas fa-plus"></i> Add Customer</b></a>
                        </label>
                        <table class="table table-bordered mb-0 data-table">
                            <thead>
                                <tr>
                                    <th class="text-center">SL</th>
                                    <th class="text-center">Customer Category Title</th>
                                    <th class="text-center">Customer Source Type</th>
                                    <th class="text-center">Reference By</th>
                                    <th class="text-center">Name</th>
                                    <th class="text-center">Phone</th>
                                    <th class="text-center">Address</th>
                                    <th class="text-center">Action</th>
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
    {{-- js code for data table --}}
    <script src="{{ url('dataTable') }}/js/jquery.validate.js"></script>
    <script src="{{ url('dataTable') }}/js/jquery.dataTables.min.js"></script>
    <script src="{{ url('dataTable') }}/js/dataTables.bootstrap4.min.js"></script>

    <script type="text/javascript">
        var table = $(".data-table").DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ url('view/all/customer') }}",
            columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex'
            },
            {
                data: 'customer_category',
                name: 'customer_category'
            },
            {
                data: 'customer_source_type',
                name: 'customer_source_type'
            },
            {
                data: 'reference_by',
                name: 'reference_by'
            },
            {
                data: 'name',
                name: 'name'
            },
            {
                data: 'phone',
                name: 'name'
            },
            {
                data: 'address',
                name: 'address',
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
            var customerSlug = $(this).data("id");
            if (confirm("Are You sure want to delete !")) {
                if (check_demo_user()) {
                    return false;
                } $.ajax({
                    type: "GET",
                    url: "{{ url('delete/customers') }}" + '/' +
                        customerSlug,
                    success: function (data) {
                        table.draw(false);
                        toastr.error("Customer has been Deleted",
                            "Deleted Successfully");
                    },
                    error: function (xhr) {
                        console.log('Error 11:', xhr.responseJSON.error);
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