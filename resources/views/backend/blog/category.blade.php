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
    Blog
@endsection
@section('page_heading')
    Blog Categories
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">View All Blog Categories</h4>
                    <div class="table-responsive">

                        <label id="customFilter">
                            <a href="{{url('rearrange/blog/category')}}" class="btn btn-warning btn-sm"
                                style="margin-left: 5px"><b><i class="fas fa-sort-amount-up"></i> Rearrange Category</b></a>
                            <button class="btn btn-success btn-sm" id="addNewBlogCategory" style="margin-left: 5px"><b><i
                                        class="feather-plus"></i> Add New Category</b></button>
                        </label>

                        <table class="table table-bordered mb-0 data-table">
                            <thead>
                                <tr>
                                    <th class="text-center">SL</th>
                                    <th class="text-center">Name</th>
                                    <th class="text-center">Slug</th>
                                    <th class="text-center">Featured</th>
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

    <div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="productForm2" name="productForm2" class="form-horizontal">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel2">Add New Category</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" id="category_name_for_create" class="form-control" name="name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" id="saveBtn" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="productForm" name="productForm" class="form-horizontal">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Category Info</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="category_slug" id="category_slug">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" id="category_name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control" id="category_status" name="category_status" required>
                                <option value="">Select One</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" id="updateBtn" class="btn btn-primary">Save</button>
                    </div>
                </form>
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
            ajax: "{{ url('blog/categories') }}",
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'name',
                    name: 'name'
                }, //orderable: true, searchable: true
                { data: 'slug', name: 'slug' },
                { data: 'featured', name: 'featured' },
                { data: 'status', name: 'status' },
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

        $('#addNewBlogCategory').click(function () {
            $('#productForm2').trigger("reset");
            $('#exampleModal2').modal('show');
        });

        $('#saveBtn').click(function (e) {
            e.preventDefault();

            $(this).html('Saving..');
            $.ajax({
                data: $('#productForm2').serialize(),
                url: "{{ url('save/blog/category') }}",
                type: "POST",
                dataType: 'json',
                success: function (data) {
                    $('#saveBtn').html('Save');
                    $('#productForm2').trigger("reset");
                    $('#exampleModal2').modal('hide');
                    toastr.success("Blog Category Created", "Created Successfully");
                    table.draw(false);
                },
                error: function (data) {
                    console.log('Error:', data);
                    $('#saveBtn').html('Save');
                }
            });

        });

        $('body').on('click', '.deleteBtn', function () {
            var categorySlug = $(this).data("id");
            if (confirm("Are You sure want to delete !")) {
                if (check_demo_user()) {
                    return false;
                }
                $.ajax({
                    type: "GET",
                    url: "{{ url('delete/blog/category') }}" + '/' + categorySlug,
                    success: function (data) {

                        if (data.data == 1) {
                            table.draw(false);
                            toastr.error("Category has been Deleted", "Deleted Successfully");
                        } else {
                            toastr.warning("Blog Available in this Category", "Failed");
                        }

                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            }
        });

        $('body').on('click', '.featureBtn', function () {
            var categorySlug = $(this).data("id");
            if (confirm("Are You sure to Change the Feature Status !")) {
                $.ajax({
                    type: "GET",
                    url: "{{ url('feature/blog/category') }}" + '/' + categorySlug,
                    success: function (data) {

                        table.draw(false);
                        toastr.success("Blog Category has been Featured", "Featured Successfully");

                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            }
        });

        $('body').on('click', '.editBtn', function () {
            var slug = $(this).data('id');
            $.get("{{ url('get/blog/category/info') }}" + '/' + slug, function (data) {
                $('#exampleModal').modal('show');
                $('#category_slug').val(slug);
                $('#category_name').val(data.name);
                $('#category_status').val(data.status);
            })
        });

        $('#updateBtn').click(function (e) {
            e.preventDefault();

            $(this).html('Updating..');
            $.ajax({
                data: $('#productForm').serialize(),
                url: "{{ url('update/blog/category') }}",
                type: "POST",
                dataType: 'json',
                success: function (data) {
                    $('#updateBtn').html('Save Changes');
                    $('#productForm').trigger("reset");
                    $('#exampleModal').modal('hide');
                    toastr.success("Category Info Updated", "Updated Successfully");
                    table.draw(false);
                },
                error: function (data) {
                    console.log('Error:', data);
                    $('#updateBtn').html('Save Changes');
                }
            });
        });
    </script>
@endsection