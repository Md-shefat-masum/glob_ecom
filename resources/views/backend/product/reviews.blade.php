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
            text-align: center !important;
            font-weight: 600;
        }
        table.dataTable tbody td:nth-child(2){
            text-align: center !important;
        }
        table.dataTable tbody td:nth-child(3){
            text-align: center !important;
        }
        table.dataTable tbody td:nth-child(4){
            text-align: center !important;
        }
        table.dataTable tbody td:nth-child(5){
            text-align: center !important;
        }
        table.dataTable tbody td:nth-child(6){
            text-align: center !important;
        }
        table.dataTable tbody td:nth-child(7){
            text-align: center !important;
        }
        table.dataTable tbody td:nth-child(8){
            text-align: center !important;
        }
        table.dataTable tbody td:nth-child(9){
            text-align: center !important;
        }
        table.dataTable tbody td:nth-child(10){
            text-align: center !important;
        }
        tfoot {
            display: table-header-group !important;
        }
        tfoot th{
            text-align: center;
        }

        img.gridProductImage:hover{
            transition: all .2s linear;
            scale: 2;
            cursor: pointer;
        }

    </style>
@endsection

@section('page_title')
    Product Ratings & Review
@endsection
@section('page_heading')
    View All Ratings & Review
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Ratings & Review</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0 data-table">
                            <thead>
                                <tr>
                                    <th class="text-center">SL</th>
                                    <th class="text-center">Image</th>
                                    <th class="text-center">Product</th>
                                    <th class="text-center">Rating</th>
                                    <th class="text-center">Review</th>
                                    <th class="text-center" style="min-width: 135px;">Reply From Admin</th>
                                    <th class="text-center">Customer Img</th>
                                    <th class="text-center">Customer Name</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center" style="min-width: 80px;">Action</th>
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

    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="productForm" name="productForm" class="form-horizontal">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel2">Reply of Product Review</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="review_id" name="review_id" value="">
                        <div class="form-group">
                            <label>Customer Review :</label>
                            <div style="width: 100%" id="customer_review">

                            </div>
                        </div>
                        <div class="form-group">
                            <label>Write Your Reply Here :</label>
                            <textarea class="form-control" id="reply_from_admin" name="reply" required></textarea>
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
            ajax: "{{ url('view/product/reviews') }}",
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'product_image',
                    name: 'product_image',
                    render: function( data, type, full, meta ) {
                        if(data){
                            return "<img class=\"gridProductImage\" src=\"/" + data + "\" width=\"40\"/>";
                        } else {
                            return '';
                        }
                    }
                },
                {
                    data: 'product_name',
                    name: 'product_name'
                }, //orderable: true, searchable: true
                {
                    data: 'rating',
                    name: 'rating'
                },
                {
                    data: 'review',
                    name: 'review'
                },
                {
                    data: 'reply',
                    name: 'reply'
                },
                {
                    data: 'user_image',
                    name: 'user_image',
                    render: function( data, type, full, meta ) {
                        if(data){
                            return "<img class=\"gridProductImage\" src=\"/" + data + "\" width=\"40\"/>";
                        } else {
                            return 'N/A';
                        }
                    }
                },
                {
                    data: 'user_name',
                    name: 'user_name'
                },
                {data: 'status', name: 'status'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
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
            var slug = $(this).data("id");
            if(confirm("Are You sure want to delete !")){
                $.ajax({
                    type: "GET",
                    url: "{{ url('delete/product/review') }}"+'/'+slug,
                    success: function (data) {
                        table.draw(false);
                        toastr.error("Product Review has been Deleted", "Deleted Successfully");
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            }
        });

        $('body').on('click', '.approveBtn', function () {
            var slug = $(this).data("id");
            if(confirm("Are You sure want to Approve !")){
                $.ajax({
                    type: "GET",
                    url: "{{ url('approve/product/review') }}"+'/'+slug,
                    success: function (data) {
                        table.draw(false);
                        toastr.success("Product Review has been Approved", "Approved Successfully");
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            }
        });

        $('body').on('click', '.replyBtn', function () {
            var id = $(this).data('id');
            $.get("{{ url('get/product/review/info') }}" +'/' + id, function (data) {
                $('#review_id').val(data.id);
                $('#customer_review').text(data.review);
                $('#reply_from_admin').text(data.reply);
                $('#productForm').trigger("reset");
                $('#exampleModal').modal('show');
            })
        });

        $('#saveBtn').click(function (e) {
            e.preventDefault();
            $(this).html('Saving..');
            $.ajax({
                data: $('#productForm').serialize(),
                url: "{{ url('submit/reply/product/review') }}",
                type: "POST",
                dataType: 'json',
                success: function (data) {
                    $('#saveBtn').html('Save');
                    $('#productForm').trigger("reset");
                    $('#exampleModal').modal('hide');
                    toastr.success("Reply is Submitted", "Submitted Successfully");
                    table.draw(false);
                },
                error: function (data) {
                    console.log('Error:', data);
                    $('#saveBtn').html('Save');
                }
            });
        });
    </script>
@endsection
