@extends('backend.master')

@section('header_css')
    <link href="{{ url('dataTable') }}/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="{{ url('dataTable') }}/css/dataTables.bootstrap4.min.css" rel="stylesheet">
@endsection

@section('page_title')
    Manual Product Returns
@endsection

@section('page_heading')
    All Manual Product Returns
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">All Manual Product Returns (Offline/Manual Sales)</h4>
                    <div class="table-responsive">
                        <label id="customFilter">
                            <a href="{{ route('CreateManualProductReturn') }}" class="btn btn-primary btn-sm">
                                <b><i class="fas fa-plus"></i> Add Manual Return</b>
                            </a>
                        </label>
                        <table class="table table-bordered mb-0 data-table">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Return Code</th>
                                    <th>Customer</th>
                                    <th>Return Date</th>
                                    <th>Total (Wallet Credit)</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_js')
    <script src="{{ url('dataTable') }}/js/jquery.dataTables.min.js"></script>
    <script src="{{ url('dataTable') }}/js/dataTables.bootstrap4.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('ViewAllManualProductReturns') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'return_code', name: 'return_code'},
                    {data: 'customer', name: 'customer'},
                    {data: 'return_date', name: 'return_date'},
                    {data: 'total', name: 'total'},
                    {data: 'return_status', name: 'return_status'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });

            // Delete functionality
            $('body').on('click', '.deleteBtn', function() {
                var slug = $(this).data("id");
                var url = '{{ route('DeleteManualProductReturn', ':slug') }}';
                url = url.replace(':slug', slug);

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This will reverse the wallet credit!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "GET",
                            url: url,
                            success: function(data) {
                                table.draw();
                                Swal.fire('Deleted!', 'Return has been deleted.', 'success');
                            },
                            error: function(data) {
                                Swal.fire('Error!', 'Something went wrong.', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection

