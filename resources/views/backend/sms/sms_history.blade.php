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
    SMS
@endsection
@section('page_heading')
    SMS History
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">View All SMS History</h4>
                    <div class="table-responsive">

                        <label id="customFilter">
                            <a href="{{url('delete/sms/with/range')}}" onclick="return confirm('Are you sure?')"
                                class="btn btn-danger btn-sm" style="margin-left: 5px"><b><i class="fas fa-trash-alt"></i>
                                    Remove All SMS Before 15 days</b></a>
                        </label>

                        <table class="table table-bordered mb-0 data-table">
                            <thead>
                                <tr>
                                    <th class="text-center">SL</th>
                                    <th class="text-center">SMS Template</th>
                                    <th class="text-center">Sending Type</th>
                                    <th class="text-center">Contact</th>
                                    <th class="text-center">SMS Receivers</th>
                                    <th class="text-center">Order Count Range</th>
                                    <th class="text-center">Order Value Range</th>
                                    <th class="text-center">Sent At</th>
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
            pageLength: 15,
            ajax: "{{ url('/view/sms/history') }}",
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                }, //orderable: true, searchable: true
                {
                    data: 'template_title',
                    name: 'template_title'
                },
                {
                    data: 'sending_type',
                    name: 'sending_type'
                },
                {
                    data: 'individual_contact',
                    name: 'individual_contact'
                },
                {
                    data: 'sms_receivers',
                    name: 'sms_receivers'
                },
                { data: 'min_order', name: 'min_order' },
                { data: 'min_order_value', name: 'min_order_value' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
        });
        $(".dataTables_filter").append($("#customFilter"));


        $('body').on('click', '.deleteBtn', function () {
            var id = $(this).data("id");
            if (confirm("Are You sure want to delete !")) {
                if (check_demo_user()) {
                    return false;
                }
                $.ajax({
                    type: "GET",
                    url: "{{ url('delete/sms') }}" + '/' + id,
                    success: function (data) {
                        table.draw(false);
                        toastr.error("SMS History has been Deleted", "Deleted Successfully");
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            }
        });

    </script>

@endsection