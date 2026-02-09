@extends('backend.master')

@section('header_css')
    <link href="{{url('assets')}}/plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />

    <style>
        .select2-selection{
            border: 1px solid #ced4da !important;
        }

        .select2 {
            width: 100% !important;
        }

        ul li.select2-selection__choice{
            background: #2E7CE4 !important;
            border-color: #2E7CE4 !important;
            color: white !important;
        }
        ul li.select2-selection__choice span{
            color: white !important;
        }
    </style>
@endsection

@section('page_title')
    SMS Service
@endsection
@section('page_heading')
    Send SMS
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Send SMS</h4>

                    <form class="needs-validation" method="POST" action="{{url('send/sms')}}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-lg-8 border-right">

                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="sending_type">Sending Type <span class="text-danger">*</span></label>
                                            <select class="form-control" name="sending_type" id="sending_type" onchange="openIndividualSms(this.value)" required>
                                                <option value="">Select One</option>
                                                <option value="1">Individual Customers</option>
                                                <option value="2" selected>Everyone</option>
                                            </select>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('sending_type')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="individual_contact">Customer Contact No</label>
                                            <select class="form-control" data-toggle="select2" name="individual_contact[]" multiple="multiple" id="individual_contact" readonly>
                                                {{-- <option value="">Select One</option> --}}
                                                @foreach ($data as $info)
                                                <option value="{{$info['contact']}}">{{$info['contact']}}</option>
                                                @endforeach
                                            </select>

                                            <div class="invalid-feedback" style="display: block;">
                                                @error('individual_contact')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <fieldset class="border p-3 mt-3" style="border-radius: 4px;">
                                            <legend class="w-auto" style="font-size: 1.2rem;"> SMS Sending Criteria </legend>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label for="contact">Select Customer Type <small>(Optional)</small></label>
                                                        <select class="form-control" name="sms_receivers" id="sms_receivers" onchange="openSmsCriteria(this.value)">
                                                            <option value="">Select One</option>
                                                            <option value="1">Registred Customers Having No Order</option>
                                                            <option value="2">Registred Customers Having Orders</option>
                                                        </select>
                                                        <div class="invalid-feedback" style="display: block;">
                                                            @error('sms_receivers')
                                                                {{ $message }}
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="contact">Min. Order <small>(Optional)</small></label>
                                                        <input type="number" class="form-control" name="min_order" id="min_order" placeholder="ex. 20" readonly>
                                                        <div class="invalid-feedback" style="display: block;">
                                                            @error('min_order')
                                                                {{ $message }}
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label for="contact">Max. Order <small>(Optional)</small></label>
                                                        <input type="number" class="form-control" name="max_order" id="max_order" placeholder="ex. 100" readonly>
                                                        <div class="invalid-feedback" style="display: block;">
                                                            @error('max_order')
                                                                {{ $message }}
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label for="contact">Minimum Order Value <small>(Optional)</small></label>
                                                        <input type="number" class="form-control" name="min_order_value" id="min_order_value" placeholder="ex. 1000" readonly>
                                                        <div class="invalid-feedback" style="display: block;">
                                                            @error('min_order_value')
                                                                {{ $message }}
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label for="contact">Maximum Order Value <small>(Optional)</small></label>
                                                        <input type="number" class="form-control" name="max_order_value" id="max_order_value" placeholder="ex. 10000" readonly>
                                                        <div class="invalid-feedback" style="display: block;">
                                                            @error('max_order_value')
                                                                {{ $message }}
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>

                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="template_id">Select SMS Template</label>
                                    <select name="template_id" onchange="fetchTemplateDescription(this.value)" class="form-control" id="template_id">
                                        @php
                                            echo App\Models\SmsTemplate::getDropDownList('title');
                                        @endphp
                                    </select>
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('template_id')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="template_description">SMS Template Description <span class="text-danger">*</span></label>
                                    <textarea id="template_description" name="template_description" class="form-control" style="height: 217px;" placeholder="Write SMS Here" required></textarea>
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('template_description')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group text-center pt-3">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-paper-plane"></i> Send SMS</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection



@section('footer_js')

    <script src="{{url('assets')}}/plugins/select2/select2.min.js"></script>
    <script type="text/javascript">

        $('[data-toggle="select2"]').select2();
        $("#individual_contact").val("").trigger("change");
        $("#sending_type").val("2");

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function fetchTemplateDescription(value){
            var templateId = value;

            $.ajax({
                url: "{{url('/get/template/description')}}",
                type: "POST",
                data: {
                    template_id: templateId,
                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function (result) {
                    // console.log(result.description);
                    $("#template_description").html(result.description)
                }
            });
        }

        $("span.select2-selection").css("background", "#ebf0f7");
        $("#individual_contact").prop("disabled", true);
        function openIndividualSms(value){
            if(value == 1){
                $("#individual_contact").attr("readonly", false);
                $("span.select2-selection").css("background", "transparent");
                $("#individual_contact").prop("disabled", false);

                $("#sms_receivers").attr("readonly", true);
                $("#sms_receivers").css("pointer-events", "none");
            } else {
                $("#individual_contact").attr("readonly", true);
                $("span.select2-selection").css("background", "#ebf0f7");
                $("#individual_contact").prop("disabled", true);
                $("#individual_contact").val("").trigger("change");

                $("#sms_receivers").attr("readonly", false);
                $("#sms_receivers").css("pointer-events", "auto");
            }
        }

        function openSmsCriteria(value){
            if(value == 1){
                $("#min_order").attr("readonly", true);
                $("#max_order").attr("readonly", true);
                $("#min_order_value").attr("readonly", true);
                $("#max_order_value").attr("readonly", true);
            } else {
                $("#min_order").attr("readonly", false);
                $("#max_order").attr("readonly", false);
                $("#min_order_value").attr("readonly", false);
                $("#max_order_value").attr("readonly", false);
            }
        }
    </script>
@endsection

