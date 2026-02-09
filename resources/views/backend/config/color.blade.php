@extends('backend.master')

@section('header_css')
    <link href="{{ url('assets') }}/css/spectrum.min.css" rel="stylesheet" type="text/css" />
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
            width: 180px;
        }

        table.dataTable tbody td:nth-child(5) {
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
    Color
@endsection
@section('page_heading')
    View All Colors
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Color List</h4>
                    <div class="table-responsive">

                        <label id="customFilter">
                            <button class="btn btn-success btn-sm" id="addNewColor" style="margin-left: 5px"><b><i
                                        class="feather-plus"></i> Add New Color</b></button>
                        </label>

                        <table class="table table-bordered mb-0 data-table">
                            <thead>
                                <tr>
                                    <th class="text-center">SL</th>
                                    <th class="text-center">Color</th>
                                    <th class="text-center">Name</th>
                                    <th class="text-center">Code</th>
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
                        <h5 class="modal-title" id="exampleModalLabel2">Create New Color</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Color Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="form-group">
                            <label>Color Code</label>
                            <input type="text" class="form-control" id="colorpicker-default" name="code">
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
                        <h5 class="modal-title" id="exampleModalLabel">Edit Color Info</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="color_id" id="color_id">
                        <div class="form-group">
                            <label>Color Name</label>
                            <input type="text" class="form-control" id="color_name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label>Color Code</label>
                            <input type="text" class="form-control colorpicker-default-update" id="color_code" name="code"
                                required>
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
    <script src="{{ url('assets') }}/js/spectrum.min.js"></script>

    <script type="text/javascript">
        var table = $(".data-table").DataTable({
            processing: true,
            serverSide: true,
            pageLength: 15,
            lengthMenu: [15, 25, 50, 100],
            ajax: "{{ url('view/all/colors') }}",
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                }, //orderable: true, searchable: true
                {
                    data: 'color',
                    name: 'color'
                },
                { data: 'name', name: 'name' },
                { data: 'code', name: 'code' },
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

        $('#addNewColor').click(function () {
            $('#productForm2').trigger("reset");
            $('#exampleModal2').modal('show');
        });


        $('#saveBtn').click(function (e) {
            e.preventDefault();
            $(this).html('Saving..');
            $.ajax({
                data: $('#productForm2').serialize(),
                url: "{{ url('create/new/color') }}",
                type: "POST",
                dataType: 'json',
                success: function (data) {
                    $('#saveBtn').html('Save');
                    $('#productForm2').trigger("reset");
                    $('#exampleModal2').modal('hide');
                    toastr.success("New Color Added", "Added Successfully");
                    table.draw(false);
                },
                error: function (data) {
                    console.log('Error:', data);
                    toastr.warning("Duplicate Color Exists", "Duplicate Color Exists");
                    $('#saveBtn').html('Save');
                }
            });
        });


        $('body').on('click', '.editBtn', function () {
            var id = $(this).data('id');
            $.get("{{ url('get/color/info') }}" + '/' + id, function (data) {
                $('#exampleModal').modal('show');
                $('#color_id').val(id);
                $('#color_name').val(data.name);
                $('#color_code').val(data.code).change();
            })
        });

        $('#updateBtn').click(function (e) {
            e.preventDefault();
            $(this).html('Updating...');
            $.ajax({
                data: $('#productForm').serialize(),
                url: "{{ url('update/color') }}",
                type: "POST",
                dataType: 'json',
                success: function (data) {
                    $('#updateBtn').html('Save Changes');
                    $('#productForm').trigger("reset");
                    $('#exampleModal').modal('hide');
                    toastr.success("Color Info Updated", "Updated Successfully");
                    table.draw(false);
                },
                error: function (data) {
                    console.log('Error:', data);
                    toastr.warning("Duplicate Color Exists", "Duplicate Color Exists");
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
                    url: "{{ url('delete/color') }}" + '/' + id,
                    success: function (data) {
                        table.draw(false);
                        toastr.error("Color has been Deleted", "Deleted Successfully");
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            }
        });



        $("#colorpicker-default").spectrum({
            preferredFormat: 'hex',
        });

        $(".colorpicker-default-update").spectrum({
            preferredFormat: 'hex',
        });

        // $("#colorpicker-default").spectrum({
        //     showAlpha: true
        // });

        // $("#colorpicker-showpaletteonly").spectrum({
        //     showPaletteOnly: true,
        //     showPalette: true,
        //     palette: [
        //         ['#3bafda', 'white', '#675aa9',
        //         'rgb(255, 128, 0);', '#f672a7'],
        //         ['red', 'yellow', 'green', 'blue', 'violet']
        //     ]
        // });

        // $("#colorpicker-togglepaletteonly").spectrum({
        //     showPaletteOnly: true,
        //     togglePaletteOnly: true,
        //     togglePaletteMoreText: 'more',
        //     togglePaletteLessText: 'less',
        //     palette: [
        //         ["#000","#444","#666","#999","#ccc","#eee","#f3f3f3","#fff"],
        //         ["#f00","#f90","#ff0","#0f0","#0ff","#00f","#90f","#f0f"],
        //         ["#f4cccc","#fce5cd","#fff2cc","#d9ead3","#d0e0e3","#cfe2f3","#d9d2e9","#ead1dc"],
        //         ["#ea9999","#f9cb9c","#ffe599","#b6d7a8","#a2c4c9","#9fc5e8","#b4a7d6","#d5a6bd"],
        //         ["#e06666","#f6b26b","#ffd966","#93c47d","#76a5af","#6fa8dc","#8e7cc3","#c27ba0"],
        //         ["#c00","#e69138","#f1c232","#6aa84f","#45818e","#3d85c6","#674ea7","#a64d79"],
        //         ["#900","#b45f06","#bf9000","#38761d","#134f5c","#0b5394","#351c75","#741b47"],
        //         ["#600","#783f04","#7f6000","#274e13","#0c343d","#073763","#20124d","#4c1130"]
        //     ]
        // });

        // $("#colorpicker-showintial").spectrum({
        //     showInitial: true
        // });

        // $("#colorpicker-showinput-intial").spectrum({
        //     showInitial: true,
        //     showInput: true
        // });

    </script>
@endsection