@extends('backend.master')

@section('header_css')
    <link href="{{url('assets')}}/css/jquery.datetimepicker.css" rel="stylesheet" type="text/css" />
    <link href="{{url('assets')}}/plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />
    <style>
        @media print {
            .hidden-print{
                display: none !important;
            }
        }
        .select2-selection{
            height: 34px !important;
            border: 1px solid #ced4da !important;
        }
        .select2 {
            width: 100% !important;
        }
    </style>
@endsection

@section('page_title')
    Report
@endsection
@section('page_heading')
    Product Purchase Report
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Product Purchase Report Criteria</h4>

                    <form class="needs-validation row" id="sales_report_form">
                        <div class="form-group col">
                            <label for="start_date">Start Date</label>
                            <input type="text" class="form-control date_field" value="{{date("01/m/Y")}}" id="start_date" placeholder="Start Date" required>
                        </div>
                        <div class="form-group col">
                            <label for="end_date">End Date</label>
                            <input type="text" class="form-control date_field" value="{{date("d/m/Y")}}" id="end_date" placeholder="End Date" required>
                        </div>

                        <div class="form-group col-lg-12 text-right">
                            <button type="button" onclick="generateReport()" class="btn btn-primary" id="generate_sales_report_btn"><i class="feather-refresh-ccw "></i> Generate Report</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 col-xl-12" id="report_view_section">

        </div>
    </div>
@endsection


@section('footer_js')
    <script src="{{url('assets')}}/js/jquery.datetimepicker.full.min.js"></script>
    <script src="{{url('assets')}}/plugins/select2/select2.min.js"></script>
    <script>

        $('[data-toggle="select2"]').select2();
        $(".date_field").datetimepicker({
            timepicker: false,

            format: "d/m/Y",
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function generateReport(){

            $("#generate_sales_report_btn").html("Generating...");
            var startDate = $("#start_date").val();
            var endDate = $("#end_date").val();
        
            $.ajax({
                data: {
                    start_date: startDate,
                    end_date: endDate,
                },
                url: "{{ url('generate/product/purchase/report') }}",
                type: "POST",
                dataType: 'json',
                success: function(data) {
                    $("#report_view_section").html(data.variant);
                    $("#generate_sales_report_btn").html("<i class='feather-refresh-ccw'></i> Generate Report");
                },
                error: function(data) {
                    console.log('Error:', data);
                    $(".addAnotherVariant").html("Something Went Wrong");
                }
            });
        }

        function printPageArea(areaID){
            var printContent = document.getElementById(areaID).innerHTML;
            var originalContent = document.body.innerHTML;
            document.body.innerHTML = printContent;
            window.print();
            document.body.innerHTML = originalContent;
        }
    </script>
@endsection
