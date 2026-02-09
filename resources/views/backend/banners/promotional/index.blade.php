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
    Promotional Banners
@endsection
@section('page_heading')
    View All Promotional Banners
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Promotional Banners List</h4>
                    <div class="table-responsive">

                        <label id="customFilter">
                            <a href="{{url('add/new/promotional/banner')}}" class="btn btn-success btn-sm" id="addNewFlag"
                                style="margin-left: 5px"><i class="feather-plus"></i> Add New Promotional Banner</a>
                            <a href="{{url('rearrange/promotional/banners')}}" class="btn btn-info btn-sm" style="margin-left: 5px"><i
                                    class="fas fa-sort-amount-up"></i> Rearrange Promotional Banners</a>
                        </label>

                        <table class="table table-bordered mb-0 data-table">
                            <thead>
                                <tr>
                                    <th class="text-center">SL</th>
                                    <th class="text-center">Image</th>
                                    <th class="text-center">Title</th>
                                    <th class="text-center">Heading</th>
                                    <th class="text-center">Button Text</th>
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
            ajax: "{{ url('/view/all/promotional/banners') }}",
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'product_image',
                    name: 'product_image'
                },
                {
                    data: 'title',
                    name: 'title'
                },
                {
                    data: 'heading',
                    name: 'heading'
                },
                {
                    data: 'btn_text',
                    name: 'btn_text'
                },
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
                    url: "{{ url('delete/promotional/banner') }}" + '/' + slug,
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

