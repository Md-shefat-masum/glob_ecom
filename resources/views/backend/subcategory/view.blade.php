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
    Subcategory
@endsection
@section('page_heading')
    View All Subcategories
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Subcategory List</h4>
                    <div class="table-responsive">
                        <label id="customFilter">
                            <a href="{{url('/add/new/subcategory')}}" class="btn btn-primary btn-sm"
                                style="margin-left: 5px"><b><i class="fas fa-plus"></i> Add New SubCategory</b></a>
                        </label>
                        <table class="table table-bordered mb-0 data-table">
                            <thead>
                                <tr>
                                    <th class="text-center">SL</th>
                                    <th class="text-center">Category</th>
                                    <th class="text-center">Name</th>
                                    <th class="text-center">Icon</th>
                                    <th class="text-center">Image</th>
                                    <th class="text-center">Slug</th>
                                    <th class="text-center">Featured</th>
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
            ajax: "{{ url('view/all/subcategory') }}",
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'category_name',
                    name: 'category_name'
                }, //orderable: true, searchable: true
                {
                    data: 'name',
                    name: 'name'
                }, //orderable: true, searchable: true
                {
                    data: 'icon',
                    name: 'icon',
                    render: function (data, type, full, meta) {
                        if (data) {
                            return "<img src=\"/" + data + "\" width=\"60\"/>";
                        } else {
                            return '';
                        }
                    }
                },
                {
                    data: 'image',
                    name: 'image',
                    render: function (data, type, full, meta) {
                        if (data) {
                            return "<img src=\"/" + data + "\" width=\"60\"/>";
                        } else {
                            return '';
                        }
                    }
                },
                { data: 'slug', name: 'slug' },
                { data: 'featured', name: 'featured' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            initComplete: function () {
                // this.api().columns([2,5]).every(function() {
                //     var column = this;
                //     var input = document.createElement("input");
                //     $(input).appendTo($(column.footer()).empty())
                //         .on('change', function() {
                //             var val = $.fn.dataTable.util.escapeRegex($(this).val());
                //             column.search(val ? val : '', true, false).draw();
                //         });
                // });

                // this.api().columns([7]).every(function() {
                //     var column = this;
                //     var select = $('<select style="width:100%"><option value="">All</option></select>')
                //         .appendTo($(column.footer()).empty())
                //         .on('change', function() {
                //             var val = $.fn.dataTable.util.escapeRegex(
                //                 $(this).val()
                //             );
                //             column
                //                 .search(val ? '^' + val + '$' : '', true, false)
                //                 .draw();
                //         });
                //     column.each(function() {
                //         select.append('<option value="Active">' + 'Active' + '</option>')
                //         select.append('<option value="Inactive">' + 'Inactive' + '</option>')
                //     });
                // });
            }
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
            var subcategorySlug = $(this).data("id");
            if (confirm("Are You sure want to delete !")) {
                if (check_demo_user()) {
                    return false;
                }
                $.ajax({
                    type: "GET",
                    url: "{{ url('delete/subcategory') }}" + '/' + subcategorySlug,
                    success: function (data) {
                        table.draw(false);
                        toastr.error("Subcategory has been Deleted", "Deleted Successfully");
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            }
        });

        $('body').on('click', '.featureBtn', function () {
            var id = $(this).data("id");
            if (confirm("Are You sure to Change the Feature Status !")) {
                $.ajax({
                    type: "GET",
                    url: "{{ url('feature/subcategory') }}" + '/' + id,
                    success: function (data) {

                        table.draw(false);
                        toastr.success("SubCategory has been Featured", "Featured Successfully");

                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            }
        });
    </script>
@endsection