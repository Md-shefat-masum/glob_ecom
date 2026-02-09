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
    </style>
@endsection

@section('page_title')
    Brand
@endsection
@section('page_heading')
    View All Brands
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Brand List</h4>
                    <div class="table-responsive">

                        <label id="customFilter">
                            <a href="{{url('add/new/brand')}}" class="btn btn-success btn-sm" id="addNewFlag"
                                style="margin-left: 5px"><i class="feather-plus"></i> Add New Brand</a>
                            <a href="{{url('rearrange/brands')}}" class="btn btn-info btn-sm" style="margin-left: 5px"><b><i
                                        class="fas fa-sort-amount-up"></i> Rearrange Brand</b></a>
                        </label>

                        <table class="table table-bordered mb-0 data-table">
                            <thead>
                                <tr>
                                    <th class="text-center">SL</th>
                                    <th class="text-center">Name</th>
                                    <th class="text-center">Logo</th>
                                    <th class="text-center">Banner</th>
                                    <th class="text-center">Categories</th>
                                    <th class="text-center">Subcategories</th>
                                    <th class="text-center">Childcategories</th>
                                    <th class="text-center">Slug</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Featured</th>
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
            ajax: "{{ url('view/all/brands') }}",
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'name',
                    name: 'name'
                }, //orderable: true, searchable: true
                {
                    data: 'logo',
                    name: 'logo',
                    render: function (data, type, full, meta) {
                        if (data) {
                            return "<img src=\"/" + data + "\" width=\"40\"/>";
                        } else {
                            return '';
                        }
                    }
                },
                {
                    data: 'banner',
                    name: 'banner',
                    render: function (data, type, full, meta) {
                        if (data) {
                            return "<img src=\"/" + data + "\" width=\"40\"/>";
                        } else {
                            return '';
                        }
                    }
                },
                { data: 'categories', name: 'categories' },
                { data: 'subcategories', name: 'subcategories' },
                { data: 'childcategories', name: 'childcategories' },
                { data: 'slug', name: 'slug' },
                { data: 'status', name: 'status' },
                { data: 'featured', name: 'featured' },
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

        $('body').on('click', '.featureBtn', function () {
            var id = $(this).data("id");
            if (confirm("Are You sure to Change the Feature Status !")) {
                $.ajax({
                    type: "GET",
                    url: "{{ url('feature/brand') }}" + '/' + id,
                    success: function (data) {

                        table.draw(false);
                        toastr.success("Feature Status Changed", "Changed Successfully");

                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            }
        });

        $('body').on('click', '.deleteBtn', function () {
            var brandSlug = $(this).data("id");
            if (confirm("All the models of that Brand will also be Deleted !")) {
                if (check_demo_user()) {
                    return false;
                }
                $.ajax({
                    type: "GET",
                    url: "{{ url('delete/brand') }}" + '/' + brandSlug,
                    success: function (data) {
                        table.draw(false);
                        toastr.error("Brand has been Deleted", "Deleted Successfully");
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            }
        });
    </script>
@endsection