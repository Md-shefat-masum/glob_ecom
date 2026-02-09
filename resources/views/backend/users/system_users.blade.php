@extends('backend.master')

@section('header_css')
    <link href="{{url('assets')}}/plugins/switchery/switchery.min.css" rel="stylesheet" type="text/css" />
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

        table.dataTable tbody td:nth-child(9) {
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
    System Users
@endsection
@section('page_heading')
    View All System Users
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">System Users List</h4>
                    <div class="table-responsive">
                        <label id="customFilter">
                            <select id="userTypeFilter" class="form-control form-control-sm"
                                style="width: 180px; display: inline-block; margin-left: 10px;">
                                <option value="">All Users</option>
                                <option value="system_user">System User</option>
                                <option value="delivery_man">Delivery Man</option>
                            </select>
                            <a href="{{url('add/new/system/user')}}" class="btn btn-success btn-sm" id="addNewFlag"
                                style="margin-left: 5px"><i class="feather-plus"></i> Add New User</a>
                        </label>
                        <table class="table table-bordered mb-0 data-table">
                            <thead>
                                <tr>
                                    <th class="text-center">SL</th>
                                    <th class="text-center">Active Status</th>
                                    <th class="text-center">Name</th>
                                    <th class="text-center">Email</th>
                                    <th class="text-center">Phone</th>
                                    <th class="text-center">Address</th>
                                    <th class="text-center">Account Created</th>
                                    <th class="text-center">User Type</th>
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
    <script src="{{url('assets')}}/plugins/switchery/switchery.min.js"></script>

    <script type="text/javascript">

        var table = $(".data-table").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('view/system/users') }}",
                data: function (d) {
                    d.user_type = $('#userTypeFilter').val(); // send selected user type
                }
            },
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                { data: 'active_status', name: 'active_status', orderable: false, searchable: false },
                {
                    data: 'name',
                    name: 'name'
                }, //orderable: true, searchable: true
                { data: 'email', name: 'email' },
                { data: 'phone', name: 'phone' },
                { data: 'address', name: 'address' },
                { data: 'created_at', name: 'created_at' },
                { data: 'user_type', name: 'user_type' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
            initComplete: function () {
                $('[data-toggle="switchery"]').each(function (idx, obj) {
                    if (!$(this).data('switchery')) {
                        new Switchery($(this)[0], $(this).data());
                    }
                });
            },
            drawCallback: function () {
                $('[data-toggle="switchery"]').each(function (idx, obj) {
                    if (!$(this).data('switchery')) {
                        new Switchery($(this)[0], $(this).data());
                    }
                });
            }
        });

        $(".dataTables_filter").append($("#customFilter"));
        $('#userTypeFilter').on('change', function () {
            table.ajax.reload();
        });

    </script>


    <script>
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
                    url: "{{ url('delete/system/user') }}" + '/' + id,
                    success: function (data) {
                        table.draw(false);
                        toastr.error("User has been Deleted", "Deleted Successfully");
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            }
        });

        $('body').on('click', '.makeSuperAdmin', function () {
            var id = $(this).data("id");
            if (confirm("Are You sure want to Make this SuperAdmin !")) {
                $.ajax({
                    type: "GET",
                    url: "{{ url('make/user/superadmin') }}" + '/' + id,
                    success: function (data) {
                        toastr.success("User is SuperAdmin Now", "Successfull");
                        location.reload(true);
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            }
        });

        $('body').on('click', '.revokeSuperAdmin', function () {
            var id = $(this).data("id");
            if (confirm("Are You sure want to Revoke SuperAdmin !")) {
                $.ajax({
                    type: "GET",
                    url: "{{ url('revoke/user/superadmin') }}" + '/' + id,
                    success: function (data) {
                        toastr.success("User is Not SuperAdmin Now", "Successfull");
                        location.reload(true);
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            }
        });

        function changeUserStatus(id) {
            var userId = id;
            $.ajax({
                type: "GET",
                url: "{{ url('change/user/status') }}" + '/' + userId,
                success: function (data) {
                    toastr.success("User Status has been Changed", "Changed Successfully");
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
        }

    </script>

@endsection