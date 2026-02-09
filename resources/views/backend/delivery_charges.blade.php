@extends('backend.master')

@section('header_css')
    <link href="{{ url('dataTable') }}/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="{{ url('dataTable') }}/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <style>
        .dataTables_wrapper .dataTables_paginate .paginate_button{
            padding: 0px;
            border-radius: 4px;
        }
        table.dataTable tbody td:nth-child(1){
            font-weight: 600;
        }
        table.dataTable tbody td{
            text-align: center !important;
        }
        tfoot {
            display: table-header-group !important;
        }
        tfoot th{
            text-align: center;
        }

    </style>
@endsection

@section('page_title')
    Delivery Charges
@endsection
@section('page_heading')
    View All Delivery Charges
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Delivery Charge List</h4>
                    <div class="table-responsive">

                        <table class="table table-bordered mb-0 data-table">
                            <thead>
                                <tr>
                                    <th class="text-center">SL</th>
                                    <th class="text-center">Division</th>
                                    <th class="text-center">District</th>
                                    <th class="text-center">District (Bangla)</th>
                                    <th class="text-center">Delivery Charge</th>
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


    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="productForm" name="productForm" class="form-horizontal">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Delivery Charge</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="delivery_charge_id" id="delivery_charge_id">

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>District Name</label>
                                    <input type="text" class="form-control" id="name" name="name" readonly required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>District Bangla Name</label>
                                    <input type="text" class="form-control" id="bn_name" name="bn_name" readonly required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Delivery Charge</label>
                            <input type="number" class="form-control" id="delivery_charge" name="delivery_charge" required>
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
            pageLength: 15,
            stateSave: true,
            lengthMenu: [15, 25, 50, 100],

            ajax: "{{ url('view/delivery/charges') }}",
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'},
                {
                    data: 'division_name',
                    name: 'division_name'
                }, //orderable: true, searchable: true
                {data: 'name', name: 'name'},
                {data: 'bn_name', name: 'bn_name'},
                {data: 'delivery_charge', name: 'delivery_charge'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
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

        $('body').on('click', '.editBtn', function () {
            var id = $(this).data('id');
            $.get("{{ url('get/delivery/charge') }}" +'/' + id, function (data) {
                $('#exampleModal').modal('show');
                $('#delivery_charge_id').val(id);
                $('#name').val(data.name);
                $('#bn_name').val(data.bn_name);
                $('#delivery_charge').val(data.delivery_charge);
            })
        });

        $('#updateBtn').click(function (e) {
            e.preventDefault();
            $(this).html('Updating...');
            $.ajax({
                data: $('#productForm').serialize(),
                url: "{{ url('update/delivery/charge') }}",
                type: "POST",
                dataType: 'json',
                success: function (data) {
                    $('#updateBtn').html('Save');
                    $('#productForm').trigger("reset");
                    $('#exampleModal').modal('hide');
                    toastr.success("Delivery Charge Updated", "Updated Successfully");
                    table.draw(false);
                },
                error: function (data) {
                    console.log('Error:', data);
                    toastr.warning("Ops!!", "Something Went Wrong");
                    $('#updateBtn').html('Try Again');
                }
            });
        });

    </script>
@endsection
