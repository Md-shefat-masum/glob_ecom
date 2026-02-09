@extends('backend.master')

@section('header_css')
    <link href="{{ url('dataTable') }}/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="{{ url('dataTable') }}/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="{{url('assets')}}/plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />
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

        .select2-selection {
            height: 34px !important;
            border: 1px solid #ced4da !important;
        }

        .select2 {
            width: 100% !important;
        }
    </style>
@endsection

@section('page_title')
    Upazila & Thana
@endsection
@section('page_heading')
    View All Upazila & Thana
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Upazila & Thana List</h4>
                    <div class="table-responsive">

                        <label id="customFilter">
                            <button class="btn btn-success btn-sm" id="addUpazilaThana" style="margin-left: 5px"><b><i
                                        class="feather-plus"></i> Add Upazila/Thana</b></button>
                        </label>

                        <table class="table table-bordered mb-0 data-table">
                            <thead>
                                <tr>
                                    <th class="text-center">SL</th>
                                    <th class="text-center">District</th>
                                    <th class="text-center">Upazila/Thana (English)</th>
                                    <th class="text-center">Upazila/Thana (Bangla)</th>
                                    <th class="text-center">Website</th>
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


    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="productForm" name="productForm" class="form-horizontal">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Upazila/Thana Name</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="upazila_id" id="upazila_id">

                        <div class="form-group">
                            <label>District Name</label>
                            <input type="text" class="form-control" id="district_name" name="district_name" readonly
                                required>
                        </div>

                        <div class="form-group">
                            <label>Upazila/Thana Name*</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label>pazila/Thana Name (Bangla)</label>
                            <input type="text" class="form-control" id="bn_name" name="bn_name">
                        </div>
                        <div class="form-group">
                            <label>Website</label>
                            <input type="text" class="form-control" id="url" name="url" placeholder="https://">
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


    <div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="productForm2" name="productForm2" class="form-horizontal">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel2">Add New Upazila/Thana</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>District<span class="text-danger">*</span></label>
                            <select data-toggle="select2" class="form-control" name="district_id" id="district_id" required>
                                <option value="">Select One</option>
                                @php
                                    $districts = DB::table('districts')->orderBy('name', 'asc')->get();
                                @endphp
                                @foreach ($districts as $district)
                                    <option value="{{$district->id}}">{{$district->name}} ({{$district->bn_name}})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Upazila/Thana Name<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="form-group">
                            <label>Upazila/Thana Name (Bangla)</label>
                            <input type="text" class="form-control" name="bn_name">
                        </div>
                        <div class="form-group">
                            <label>Website</label>
                            <input type="text" class="form-control" name="url" placeholder="https://">
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" id="saveBtn" class="btn btn-primary">Save Now</button>
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
    <script src="{{url('assets')}}/plugins/select2/select2.min.js"></script>

    <script type="text/javascript">
        $('[data-toggle="select2"]').select2();

        var table = $(".data-table").DataTable({
            processing: true,
            serverSide: true,
            pageLength: 15,
            stateSave: true,
            lengthMenu: [15, 25, 50, 100],

            ajax: "{{ url('view/upazila/thana') }}",
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'district_name',
                    name: 'district_name'
                },
                { data: 'name', name: 'name' },
                { data: 'bn_name', name: 'bn_name' },
                { data: 'url', name: 'url' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
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

        $('#addUpazilaThana').click(function () {
            $('#productForm2').trigger("reset");
            $('#exampleModal2').modal('show');
            $("#district_id").change();
        });

        $('#saveBtn').click(function (e) {
            e.preventDefault();
            $(this).html('Saving..');
            $.ajax({
                data: $('#productForm2').serialize(),
                url: "{{ url('save/new/upazila') }}",
                type: "POST",
                dataType: 'json',
                success: function (data) {
                    $('#saveBtn').html('Save Now');
                    $('#productForm2').trigger("reset");
                    $('#exampleModal2').modal('hide');
                    toastr.success("New Upazila/Thana Added", "Added Successfully");
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
            $.get("{{ url('get/upazila/info') }}" + '/' + id, function (data) {
                console.log(data);
                $('#exampleModal').modal('show');
                $('#upazila_id').val(id);
                $('#district_name').val(data.district_name);
                $('#name').val(data.name);
                $('#bn_name').val(data.bn_name);
                $('#url').val(data.url ? data.url : '');
            })
        });

        $('#updateBtn').click(function (e) {
            e.preventDefault();
            $(this).html('Updating...');
            $.ajax({
                data: $('#productForm').serialize(),
                url: "{{ url('update/upazila/info') }}",
                type: "POST",
                dataType: 'json',
                success: function (data) {
                    $('#updateBtn').html('Save');
                    $('#productForm').trigger("reset");
                    $('#exampleModal').modal('hide');
                    toastr.success("Upazila/Thana Updated", "Updated Successfully");
                    table.draw(false);
                },
                error: function (data) {
                    console.log('Error:', data);
                    toastr.warning("Ops!!", "Something Went Wrong");
                    $('#updateBtn').html('Try Again');
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
                    url: "{{ url('delete/upazila') }}" + '/' + id,
                    success: function (data) {
                        table.draw(false);
                        toastr.error("Upazila/Thana has been Deleted", "Deleted Successfully");
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            }
        });

    </script>
@endsection