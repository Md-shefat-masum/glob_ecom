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
            min-width: 100px !important;
        }

        table.dataTable tbody td:nth-child(7) {
            text-align: center !important;
            min-width: 80px !important;
        }

        table.dataTable tbody td:nth-child(8) {
            text-align: center !important;
            min-width: 80px !important;
        }

        table.dataTable tbody td:nth-child(9) {
            text-align: center !important;
            min-width: 80px !important;
        }

        table.dataTable tbody td:nth-child(10) {
            text-align: center !important;
            min-width: 100px !important;
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
    System
@endsection
@section('page_heading')
    View All Email Configurations
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">SMTP Email Configurations</h4>
                    <div class="table-responsive">

                        <label id="customFilter">
                            <button class="btn btn-success btn-sm" id="addNewEmailServer" style="margin-left: 5px"><b><i
                                        class="feather-plus"></i> Add Email Server</b></button>
                        </label>

                        <table class="table table-bordered mb-0 data-table">
                            <thead>
                                <tr>
                                    <th class="text-center">SL</th>
                                    <th class="text-center">Host Server</th>
                                    <th class="text-center">Port</th>
                                    <th class="text-center">Email</th>
                                    <th class="text-center">Password</th>
                                    <th class="text-center" style="min-width: 115px;">Mail From Name</th>
                                    <th class="text-center" style="min-width: 115px;">Mail From Email</th>
                                    <th class="text-center">Encryption</th>
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
                        <h5 class="modal-title" id="exampleModalLabel2">Add New Mail Server</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Host Server<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" placeholder="smpt.gmail.com" name="host" required>
                        </div>
                        <div class="form-group">
                            <label>Server PORT<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" placeholder="20" name="port" required>
                        </div>
                        <div class="form-group">
                            <label>Email/Username<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" placeholder="example@gmail.com" name="email" required>
                        </div>
                        <div class="form-group">
                            <label>Password<span class="text-danger">*</span> <small>(Stored in DB as Encrypted
                                    Format)</small></label>
                            <input type="text" class="form-control" placeholder="*********" name="password" required>
                        </div>
                        <div class="form-group">
                            <label>Mail From Name</label>
                            <input type="text" class="form-control" placeholder="Company Name" name="mail_from_name">
                        </div>
                        <div class="form-group">
                            <label>Mail From Email</label>
                            <input type="email" class="form-control" placeholder="companyemail@company.com"
                                name="mail_from_email">
                        </div>
                        <div class="form-group">
                            <label>Encryption<span class="text-danger">*</span></label>
                            <select class="form-control" name="encryption" required>
                                <option value="">Select One</option>
                                <option value="0" selected>No Encryption</option>
                                <option value="1">TLS Encryption</option>
                                <option value="2">SSL Encryption</option>
                            </select>
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
                        <h5 class="modal-title" id="exampleModalLabel">Edit Mail Server Config</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="email_config_slug" id="email_config_slug">
                        <div class="form-group">
                            <label>Host Server<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" placeholder="smpt.gmail.com" id="host" name="host"
                                required>
                        </div>
                        <div class="form-group">
                            <label>Server PORT<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" placeholder="20" id="port" name="port" required>
                        </div>
                        <div class="form-group">
                            <label>Email/Username<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" placeholder="example@gmail.com" id="email" name="email"
                                required>
                        </div>
                        <div class="form-group">
                            <label>Mail From Name</label>
                            <input type="text" class="form-control" placeholder="Company Name" id="mail_from_name"
                                name="mail_from_name">
                        </div>
                        <div class="form-group">
                            <label>Mail From Email</label>
                            <input type="email" class="form-control" placeholder="companyemail@company.com"
                                id="mail_from_email" name="mail_from_email">
                        </div>
                        <div class="form-group">
                            <label>Encryption<span class="text-danger">*</span></label>
                            <select class="form-control" id="encryption" name="encryption" required>
                                <option value="">Select One</option>
                                <option value="0" selected>No Encryption</option>
                                <option value="1">TLS Encryption</option>
                                <option value="2">SSL Encryption</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control" id="status" name="status">
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
            ajax: "{{ url('view/email/credential') }}",
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'host',
                    name: 'host'
                }, //orderable: true, searchable: true
                {
                    data: 'port',
                    name: 'port'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'password',
                    name: 'password'
                },
                {
                    data: 'mail_from_name',
                    name: 'mail_from_name'
                },
                { data: 'mail_from_email', name: 'mail_from_email' },
                { data: 'encryption', name: 'encryption' },
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

        $('#addNewEmailServer').click(function () {
            $('#productForm2').trigger("reset");
            $('#exampleModal2').modal('show');
        });

        $('#saveBtn').click(function (e) {
            e.preventDefault();
            $(this).html('Saving..');
            $.ajax({
                data: $('#productForm2').serialize(),
                url: "{{ url('save/new/email/configure') }}",
                type: "POST",
                dataType: 'json',
                success: function (data) {
                    $('#saveBtn').html('Save');
                    $('#productForm2').trigger("reset");
                    $('#exampleModal2').modal('hide');
                    toastr.success("New Mail Server Added", "Added Successfully");
                    table.draw(false);
                },
                error: function (data) {
                    console.log('Error:', data);
                    $('#saveBtn').html('Save');
                }
            });
        });

        $('body').on('click', '.deleteBtn', function () {
            var slug = $(this).data("id");
            if (confirm("Are You sure to Delete !")) {
                if (check_demo_user()) {
                    return false;
                }
                $.ajax({
                    type: "GET",
                    url: "{{ url('delete/email/config') }}" + '/' + slug,
                    success: function (data) {
                        table.draw(false);
                        toastr.success("Config has been Deleted", "Deleted Successfully");
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            }
        });


        $('body').on('click', '.editBtn', function () {
            var slug = $(this).data('id');
            $.get("{{ url('get/email/config/info') }}" + '/' + slug, function (data) {
                $('#exampleModal').modal('show');
                $('#email_config_slug').val(slug);
                $('#host').val(data.host);
                $('#port').val(data.port);
                $('#email').val(data.email);
                $('#mail_from_name').val(data.mail_from_name);
                $('#mail_from_email').val(data.mail_from_email);
                $('#encryption').val(data.encryption);
                $('#encryption').val(data.encryption);
                $('#status').val(data.status);
            })
        });


        $('#updateBtn').click(function (e) {
            e.preventDefault();
            $(this).html('Updating..');
            $.ajax({
                data: $('#productForm').serialize(),
                url: "{{ url('update/email/config') }}",
                type: "POST",
                dataType: 'json',
                success: function (data) {
                    $('#updateBtn').html('Save Changes');
                    $('#productForm').trigger("reset");
                    $('#exampleModal').modal('hide');
                    toastr.success("Email Credential Updated", "Updated Successfully");
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