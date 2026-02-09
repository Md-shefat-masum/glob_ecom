@extends('backend.master')

@section('header_css')
    <link href="{{ url('dataTable') }}/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="{{ url('dataTable') }}/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <style>
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0px;
            border-radius: 4px;
        }

        table.dataTable tbody td {
            text-align: center !important;
        }

        table.dataTable tbody td:nth-child(1) {
            text-align: center !important;
            font-weight: 600;
        }

        table.dataTable tbody td:nth-child(4) {
            text-align: center !important;
            width: 180px;
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
    Contact Request
@endsection
@section('page_heading')
    View All Contact Request
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Contact Requests List</h4>
                    <div class="table-responsive">

                        <table class="table table-bordered mb-0 data-table">
                            <thead>
                                <tr>
                                    <th class="text-center">SL</th>
                                    <th class="text-center">Name</th>
                                    <th class="text-center">Email</th>
                                    <th class="text-center">Phone</th>
                                    <th class="text-center">Message</th>
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

    <!-- Modal for composing email -->
    <div class="modal fade" id="composeEmailModal" tabindex="-1" role="dialog" aria-labelledby="composeEmailModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="composeEmailModalLabel">Send Email</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="composeEmailForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="recipient-email">To</label>
                            <input type="email" class="form-control" id="recipient-email" name="email" readonly required>
                        </div>
                        <div class="form-group">
                            <label for="email-subject">Subject</label>
                            <input type="text" class="form-control" id="email-subject" name="subject" required>
                        </div>
                        <div class="form-group">
                            <label for="email-message">Message</label>
                            <textarea class="form-control" id="email-message" name="message" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Send Email</button>
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
            ajax: "{{ url('/view/all/contact/requests') }}",
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                }, //orderable: true, searchable: true
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                { data: 'phone', name: 'phone' },
                { data: 'message', name: 'message' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
        });

    </script>

    {{-- js code for user crud --}}
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
                    url: "{{ url('delete/contact/request') }}" + '/' + id,
                    success: function (data) {
                        table.draw(false);
                        toastr.error("Request has been Deleted", "Deleted Successfully");
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            }
        });

        $('body').on('click', '.changeStatus', function () {
            var id = $(this).data("id");
            // Get email from the closest row's email cell if not present in data-email
            var email = $(this).data("email");
            if (!email) {
                var row = $(this).closest('tr');
                email = row.find('td').eq(2).text().trim(); // 3rd column is email
            }
            $('#recipient-email').val(email ? email : '');
            $('#composeEmailModal').modal('show');
            $('#composeEmailForm').data('id', id);
        });

        $('#composeEmailForm').on('submit', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var form = $(this);
            var submitBtn = form.find('button[type="submit"]');
            submitBtn.prop('disabled', true).text('Sending...');
            var formData = form.serialize();
            $.ajax({
                type: "GET",
                url: "{{ url('change/request/status') }}" + '/' + id + '?' + formData,
                success: function (data) {
                    $('#composeEmailModal').modal('hide');
                    toastr.success("Email sent and status changed.", "Success");
                    table.draw(false);
                    submitBtn.prop('disabled', false).text('Send Email');
                },
                error: function (data) {
                    toastr.error("Failed to send email.", "Error");
                    submitBtn.prop('disabled', false).text('Send Email');
                }
            });
        });
    </script>
@endsection