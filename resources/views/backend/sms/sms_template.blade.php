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
            width: 180px;
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
    </style>
@endsection

@section('page_title')
    SMS Service
@endsection
@section('page_heading')
    SMS Template
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">View All SMS Templates</h4>
                    <div class="table-responsive">

                        <label id="customFilter">
                            <a href="{{url('create/sms/template')}}" class="btn btn-success btn-sm"
                                style="margin-left: 5px"><b><i class="feather-plus"></i> Create New Template</b></a>
                        </label>

                        <table class="table table-bordered mb-0 data-table">
                            <thead>
                                <tr>
                                    <th class="text-center" style="max-width: 50px;">SL</th>
                                    <th class="text-center" style="max-width: 300px;">Template Title</th>
                                    <th class="text-center" style="min-width: 500px;">Template Description</th>
                                    <th class="text-center" style="max-width: 160px;">Created At</th>
                                    <th class="text-center" style="max-width: 50px;">Action</th>
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
                        <h5 class="modal-title" id="exampleModalLabel">Edit SMS Template</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="template_id" id="template_id">
                        <div class="form-group">
                            <label>Template Title</label>
                            <input type="text" class="form-control" id="template_title" name="template_title" required>
                        </div>
                        <div class="form-group">
                            <label>Template Description</label>
                            <textarea rows="5" id="template_description" class="form-control"
                                name="template_description"></textarea>
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
            ajax: "{{ url('/view/sms/templates') }}",
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                }, //orderable: true, searchable: true
                {
                    data: 'title',
                    name: 'title'
                },
                {
                    data: 'description',
                    name: 'description'
                },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
        });
        $(".dataTables_filter").append($("#customFilter"));

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        $('body').on('click', '.deleteBtn', function () {
            var id = $(this).data("id");
            if (confirm("Are You sure want to delete !")) {
                if (check_demo_user()) {
                    return false;
                }
                $.ajax({
                    type: "GET",
                    url: "{{ url('delete/sms/template') }}" + '/' + id,
                    success: function (data) {
                        table.draw(false);
                        toastr.error("Template has been Deleted", "Deleted Successfully");
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            }
        });

        $('body').on('click', '.editBtn', function () {
            var id = $(this).data('id');
            $.get("{{ url('get/sms/template/info') }}" + '/' + id, function (data) {
                $('#exampleModal').modal('show');
                $('#template_id').val(id);
                $('#template_title').val(data.title);
                $('#template_description').html(data.description);
            })
        });


        $('#updateBtn').click(function (e) {
            e.preventDefault();
            $(this).html('Updating...');
            $.ajax({
                data: $('#productForm').serialize(),
                url: "{{ url('update/sms/template') }}",
                type: "POST",
                dataType: 'json',
                success: function (data) {
                    $('#updateBtn').html('Save Changes');
                    $('#productForm').trigger("reset");
                    $('#exampleModal').modal('hide');
                    toastr.success("Template Updated", "Updated Successfully");
                    table.draw(false);
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
        });

    </script>

@endsection