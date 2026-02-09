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
    Product Size
@endsection
@section('page_heading')
    View All Sizes
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Product Sizes</h4>
                    <div class="table-responsive">

                        <label id="customFilter">
                            <button class="btn btn-success btn-sm" id="addNewSize" style="margin-left: 5px"><i
                                    class="feather-plus"></i> Add New Size</button>
                            <a href="{{url('rearrange/size')}}" class="btn btn-success btn-sm"
                                style="margin-left: 5px"><b><i class="fas fa-sort-amount-up"></i> Rearrange Size</b></a>
                        </label>

                        <table class="table table-bordered mb-0 data-table">
                            <thead>
                                <tr>
                                    <th class="text-center">SL</th>
                                    <th class="text-center">Name</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Created At</th>
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

    <div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="productForm2" name="productForm2" class="form-horizontal">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel2">Create New Unit</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Size Name</label>
                            <input type="text" class="form-control" name="name" placeholder="XL/M/S" required>
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
                        <h5 class="modal-title" id="exampleModalLabel">Edit Unit Info</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="flag_slug" id="flag_slug">
                        <div class="form-group">
                            <label>Size Name</label>
                            <input type="text" class="form-control" id="flag_name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label>Unit Status</label>
                            <select class="form-control" id="flag_status" name="flag_status">
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
            ajax: "{{ url('view/all/sizes') }}",
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                }, //orderable: true, searchable: true
                {
                    data: 'name',
                    name: 'name'
                },
                { data: 'status', name: 'status' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            initComplete: function () {
                // this.api().columns([1]).every(function() {
                //     var column = this;
                //     var input = document.createElement("input");
                //     $(input).appendTo($(column.footer()).empty())
                //         .on('change', function() {
                //             var val = $.fn.dataTable.util.escapeRegex($(this).val());
                //             column.search(val ? val : '', true, false).draw();
                //         });
                // });

                // this.api().columns([2]).every(function() {
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

        $('#addNewSize').click(function () {
            $('#productForm2').trigger("reset");
            $('#exampleModal2').modal('show');
        });


        $('#saveBtn').click(function (e) {
            e.preventDefault();
            $(this).html('Saving..');
            $.ajax({
                data: $('#productForm2').serialize(),
                url: "{{ url('create/new/size') }}",
                type: "POST",
                dataType: 'json',
                success: function (data) {
                    $('#saveBtn').html('Save');
                    $('#productForm2').trigger("reset");
                    $('#exampleModal2').modal('hide');
                    toastr.success("Size Created", "Created Successfully");
                    table.draw(false);
                },
                error: function (data) {
                    console.log('Error:', data);
                    $('#saveBtn').html('Save');
                }
            });
        });


        $('body').on('click', '.editBtn', function () {
            var id = $(this).data('id');
            $.get("{{ url('get/size/info') }}" + '/' + id, function (data) {
                $('#exampleModal').modal('show');
                $('#flag_slug').val(id);
                $('#flag_name').val(data.name);
                $('#flag_status').val(data.status);
            })
        });

        $('#updateBtn').click(function (e) {
            e.preventDefault();
            $(this).html('Updating..');
            $.ajax({
                data: $('#productForm').serialize(),
                url: "{{ url('update/size') }}",
                type: "POST",
                dataType: 'json',
                success: function (data) {
                    $('#updateBtn').html('Save Changes');
                    $('#productForm').trigger("reset");
                    $('#exampleModal').modal('hide');
                    toastr.success("Size Info Updated", "Updated Successfully");
                    table.draw(false);
                },
                error: function (data) {
                    console.log('Error:', data);
                    $('#updateBtn').html('Save Changes');
                }
            });
        });

        $('body').on('click', '.deleteBtn', function () {
            var id = $(this).data("id");
            if (confirm("Are You sure want to delete !")) {
                if (check_demo_user()) {
                    return false;
                }
                $.ajax({
                    type: "GET",
                    url: "{{ url('delete/size') }}" + '/' + id,
                    success: function (data) {
                        table.draw(false);
                        toastr.error("Size has been Deleted", "Deleted Successfully");
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            }
        });
    </script>
@endsection