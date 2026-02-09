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
    Product Questions & Answers
@endsection
@section('page_heading')
    View All Questions & Answers
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Questions & Answers</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0 data-table">
                            <thead>
                                <tr>
                                    <th class="text-center">SL</th>
                                    <th class="text-center">Image</th>
                                    <th class="text-center">Product</th>
                                    <th class="text-center">Customers Name</th>
                                    <th class="text-center">Email</th>
                                    <th class="text-center">Question</th>
                                    <th class="text-center">Answer From Admin</th>
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

    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="productForm" name="productForm" class="form-horizontal">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel2">Answer of Question</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="question_answer_id" name="question_answer_id" value="">
                        <div class="form-group">
                            <label>Customer's Question :</label>
                            <div style="width: 100%" id="customers_question">

                            </div>
                        </div>
                        <div class="form-group">
                            <label>Write Your Answer Here :</label>
                            <textarea class="form-control" id="question_answer" name="answer" required></textarea>
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
            ajax: "{{ url('view/product/question/answer') }}",
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'product_image',
                    name: 'product_image',
                    render: function (data, type, full, meta) {
                        if (data) {
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
                    data: 'full_name',
                    name: 'full_name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                { data: 'question', name: 'question' },
                { data: 'answer', name: 'answer' },
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
            var slug = $(this).data("id");
            if (confirm("Are You sure want to delete !")) {
                if (check_demo_user()) {
                    return false;
                }
                $.ajax({
                    type: "GET",
                    url: "{{ url('delete/question/answer/') }}" + '/' + slug,
                    success: function (data) {
                        table.draw(false);
                        toastr.error("Question has been Deleted", "Deleted Successfully");
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            }
        });


        $('body').on('click', '.replyBtn', function () {
            var id = $(this).data('id');
            $.get("{{ url('get/question/answer/info') }}" + '/' + id, function (data) {
                $('#question_answer_id').val(data.id);
                $('#customers_question').text(data.question);
                $('#question_answer').text(data.answer);
                $('#productForm').trigger("reset");
                $('#exampleModal').modal('show');
            })
        });


        $('#saveBtn').click(function (e) {
            e.preventDefault();
            $(this).html('Saving..');
            $.ajax({
                data: $('#productForm').serialize(),
                url: "{{ url('submit/question/answer') }}",
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