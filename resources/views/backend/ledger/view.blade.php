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

        table.dataTable tbody td:nth-child(10) {
            text-align: center !important;
        }

        table.dataTable tbody td:nth-child(11) {
            text-align: center !important;
        }

        table.dataTable tbody td:nth-child(12) {
            text-align: center !important;
        }

        tfoot {
            display: table-header-group !important;
        }

        tfoot th {
            text-align: center;
        }

        table#DataTables_Table_0 img {
            transition: all .2s linear;
        }

        img.gridProductImage:hover {
            scale: 2;
            cursor: pointer;
        }
    </style>
@endsection

@section('page_title')
    Transaction
@endsection
@section('page_heading')
    View All Payment Types
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">View All Payment Types</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0 data-table">
                            <thead>
                                <tr>
                                    <th class="text-center">SL</th>
                                    <th class="text-center">Trnx Date</th>
                                    <th class="text-center">Trnx Type</th>
                                    <th class="text-center">Code</th>
                                    <th class="text-center">Debit</th>
                                    <th class="text-center">Debit Amount</th>
                                    <th class="text-center">Credit</th>
                                    <th class="text-center">Credit Amount</th>
                                    {{-- <th class="text-center">Note</th> --}}
                                    <th class="text-center">Creator</th>
                                    <th class="text-center">Created At</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Your table data goes here -->
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
            ajax: "{{ url('view/all/deposit') }}",
            columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex'
            },
            {
                data: 'transactionDate',
                name: 'transactionDate'
            },
            {
                data: 'transactionType',
                name: 'transactionType'
            },
            {
                data: 'payment_code',
                name: 'payment_code'
            },
            {
                data: 'debit',
                name: 'debit'
            },
            {
                data: 'debitAmount',
                name: 'debitAmount'
            },
            {
                data: 'credit',
                name: 'credit'
            },
            {
                data: 'creditAmount',
                name: 'creditAmount'
            },
            // {
            //     data: 'note',
            //     name: 'note',
            //     render: function(data, type, full, meta) {
            //         if (data) {
            //             // Decode HTML entities first, then remove HTML tags
            //             var decodedData = $('<div>').html(data).text();
            //             var cleanText = decodedData.replace(/(<([^>]+)>)/gi, "");

            //             // Limit to 20 characters and append "..." if text is longer
            //             return cleanText.length > 10 ? cleanText.substring(0, 10) + '...' : cleanText;
            //         }
            //         return '';
            //     }
            // },
            {
                data: 'user',
                name: 'user'
            },

            {
                data: 'created_at',
                name: 'created_at'
            },

            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }
            ]


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
            var transactionSlug = $(this).data("id");
            if (confirm("Are You sure want to delete !")) {
                if (check_demo_user()) {
                    return false;
                } $.ajax({
                    type: "GET",
                    url: "{{ url('delete/deposit') }}" + '/' +
                        transactionSlug,
                    success: function (data) {
                        table.draw(false);
                        toastr.error("Deleted",
                            "Deleted Successfully");
                    },
                    error: function (xhr) {
                        console.log('Error 11:', xhr.responseJSON.error);
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            toastr.error(xhr.responseJSON.error, "Error");
                        } else {
                            toastr.error("An unexpected error occurred", "Error");
                        }
                    }
                });
            }
        });
    </script>
@endsection