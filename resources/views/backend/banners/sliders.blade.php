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
    Sliders
@endsection
@section('page_heading')
    View All Sliders
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Sliders List</h4>
                    <div class="table-responsive">

                        <label id="customFilter">
                            <a href="{{url('add/new/slider')}}" class="btn btn-success btn-sm" id="addNewFlag"
                                style="margin-left: 5px"><i class="feather-plus"></i> Add New Slider</a>
                            <a href="{{url('rearrange/slider')}}" class="btn btn-info btn-sm" style="margin-left: 5px"><i
                                    class="fas fa-sort-amount-up"></i> Rearrange Slider</a>
                        </label>

                        <table class="table table-bordered mb-0 data-table">
                            <thead>
                                <tr>
                                    <th class="text-center">SL</th>
                                    <th class="text-center">Slider</th>
                                    <th class="text-center">Sub Title</th>
                                    <th class="text-center">Title</th>
                                    <th class="text-center">Slider Link</th>
                                    <th class="text-center">Button Text</th>
                                    <th class="text-center">Button Link</th>
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
@endsection


@section('footer_js')

    {{-- js code for data table --}}
    <script src="{{ url('dataTable') }}/js/jquery.validate.js"></script>
    <script src="{{ url('dataTable') }}/js/jquery.dataTables.min.js"></script>
    <script src="{{ url('dataTable') }}/js/dataTables.bootstrap4.min.js"></script>

    <script type="text/javascript">
        // Get base URL from env
        var baseUrl = '{{ env("FILE_URL", env("APP_URL", url("/"))) }}';
        baseUrl = baseUrl.replace(/\/$/, ''); // Remove trailing slash
        
        var table = $(".data-table").DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ url('/view/all/sliders') }}",
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                }, //orderable: true, searchable: true
                {
                    data: 'image',
                    name: 'image',
                    render: function (data, type, full, meta) {
                        if (data) {
                            // Check if data is already a full URL
                            if (data.startsWith('http://') || data.startsWith('https://')) {
                                return "<img src=\"" + data + "\" width=\"60\"/>";
                            }
                            // Use baseUrl + data directly (data already contains the full path like media/sliders/2025/12/file.jpg)
                            var imagePath = data.replace(/^\//, ''); // Remove leading slash if present
                            return "<img src=\"" + baseUrl + "/" + imagePath + "\" width=\"60\"/>";
                        } else {
                            return '';
                        }
                    }
                },
                { data: 'sub_title', name: 'sub_title' },
                { data: 'title', name: 'title' },
                { data: 'link', name: 'link' },
                { data: 'btn_text', name: 'btn_text' },
                { data: 'btn_link', name: 'btn_link' },
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

        $('body').on('click', '.deleteBtn', function () {
            var slug = $(this).data("id");
            if (confirm("Are You sure want to delete !")) {
                if (check_demo_user()) {
                    return false;
                }
                $.ajax({
                    type: "GET",
                    url: "{{ url('delete/data') }}" + '/' + slug,
                    success: function (data) {
                        table.draw(false);
                        toastr.error("Data has been Deleted", "Deleted Successfully");
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            }
        });
    </script>
@endsection