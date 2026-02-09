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
            font-weight: 600;
        }

        tfoot {
            display: table-header-group !important;
        }

        tfoot th {
            text-align: center;
        }

        img.gridProductImage:hover {
            transition: all .2s linear;
            scale: 2;
            cursor: pointer;
        }
    </style>
@endsection

@section('page_title')
    Testimonial
@endsection
@section('page_heading')
    View All Testimonials
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Testimonials</h4>

                    <div class="table-responsive">
                        <label id="customFilter">
                            <a href="{{url('/add/testimonial')}}" class="btn btn-primary btn-sm"
                                style="margin-left: 5px"><b><i class="fas fa-plus"></i> Add New Testimonial</b></a>
                        </label>
                        <table class="table table-bordered mb-0 data-table">
                            <thead>
                                <tr>
                                    <th class="text-center">SL</th>
                                    <th class="text-center">Image</th>
                                    <th class="text-center">Customer</th>
                                    <th class="text-center">Designation</th>
                                    <th class="text-center">Rating</th>
                                    <th class="text-center">Testimonial</th>
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
            ajax: "{{ url('view/testimonials') }}",
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'customer_image',
                    name: 'customer_image',
                    render: function (data, type, full, meta) {
                        if (data) {
                            return "<img class=\"gridProductImage\" src=\"/" + data + "\" width=\"40\"/>";
                        } else {
                            return '';
                        }
                    }
                },
                {
                    data: 'customer_name',
                    name: 'customer_name'
                }, //orderable: true, searchable: true
                {
                    data: 'designation',
                    name: 'designation'
                },
                {
                    data: 'rating',
                    name: 'rating'
                },
                {
                    data: 'description',
                    name: 'description'
                },
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
                    url: "{{ url('delete/testimonial') }}" + '/' + slug,
                    success: function (data) {
                        table.draw(false);
                        toastr.error("Testimonial has been Deleted", "Deleted Successfully");
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            }
        });
    </script>
@endsection