@extends('backend.master')

@section('header_css')
    <link href="{{url('assets')}}/plugins/switchery/switchery.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('page_title')
    SMS Gateways
@endsection
@section('page_heading')
    SMS Gateways Credentials
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-4 col-xl-4">
            <div class="card" style="@if($gateways[0]->status == 1) border: 2px solid green; box-shadow: 2px 2px 5px #b5b5b5; @endif">
                <div class="card-body">

                    <h4 class="card-title mb-3">
                        <div class="row">
                            <div class="col-lg-8">ElitBuzz SMS Gateway</div>
                            <div class="col-lg-4 text-right">
                                <input type="checkbox" class="switchery_checkbox" id="elitbuzz" @if($gateways[0]->status == 1) checked @endif value="elitbuzz" onchange="changeGatewayStatus(this.value)" name="has_variant" data-size="small" data-toggle="switchery" data-color="#53c024" data-secondary-color="#df3554"/>
                            </div>
                        </div>
                    </h4>

                    <div class="row" style="height: 120px;">
                        <div class="col-lg-12 text-center pt-4 pb-4">
                            <img src="{{url('images')}}/elitebuzz.png" style="max-width: 200px; max-height: 130px">
                        </div>
                    </div>

                    <form class="needs-validation" method="POST" action="{{url('update/sms/gateway/info')}}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="provider" value="elitbuzz">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="api_endpoint">API Endpoint</label>
                                    <input type="text" id="api_endpoint" name="api_endpoint" value="{{$gateways[0]->api_endpoint}}" class="form-control" placeholder="https://">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('api_endpoint')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="api_key">API Key</label>
                                    <input type="text" id="api_key" name="api_key" value="{{$gateways[0]->api_key}}" class="form-control" placeholder="ex. 7345876UYTUYR856778">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('api_key')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="secret_key">Secret Key</label>
                                    <input type="text" id="secret_key" name="secret_key" value="{{$gateways[0]->secret_key}}" class="form-control" placeholder="Don't Need This for ElitBuzz" readonly>
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('secret_key')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="sender_id">Sender ID</label>
                                    <input type="text" id="sender_id" name="sender_id" value="{{$gateways[0]->sender_id}}" class="form-control" >
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('sender_id')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group text-center pt-3">
                            <button class="btn btn-primary" type="submit">Update Info</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-xl-4">
            <div class="card" style="@if($gateways[1]->status == 1) border: 2px solid green; box-shadow: 2px 2px 5px #b5b5b5; @endif">
                <div class="card-body">
                    <h4 class="card-title mb-3">
                        <div class="row">
                            <div class="col-lg-8">Reve SMS Gateway</div>
                            <div class="col-lg-4 text-right">
                                <input type="checkbox" class="switchery_checkbox" id="revesms" value="revesms" @if($gateways[1]->status == 1) checked @endif onchange="changeGatewayStatus('revesms')" name="has_variant" data-size="small" data-toggle="switchery" data-color="#53c024" data-secondary-color="#df3554"/>
                            </div>
                        </div>
                    </h4>

                    <div class="row" style="height: 120px;">
                        <div class="col-lg-12 text-center pt-4 pb-4">
                            <img src="{{url('images')}}/revesms.png" style="max-width: 200px; max-height: 130px">
                        </div>
                    </div>

                    <form class="needs-validation" method="POST" action="{{url('update/sms/gateway/info')}}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="provider" value="revesms">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="api_endpoint">API Endpoint</label>
                                    <input type="text" id="api_endpoint" name="api_endpoint" value="{{$gateways[1]->api_endpoint}}" class="form-control" placeholder="https://">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('api_endpoint')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="api_key">API Key</label>
                                    <input type="text" id="api_key" name="api_key" value="{{$gateways[1]->api_key}}" class="form-control" placeholder="ex. 7345876UYTUYR856778">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('api_key')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="secret_key">Secret Key</label>
                                    <input type="text" id="secret_key" name="secret_key" value="{{$gateways[1]->secret_key}}" class="form-control" placeholder="ex. 7345876UYTUYR8568">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('secret_key')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="sender_id">Sender ID</label>
                                    <input type="text" id="sender_id" name="sender_id" value="{{$gateways[1]->sender_id}}" class="form-control">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('sender_id')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group text-center pt-3">
                            <button class="btn btn-primary" type="submit">Update Info</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-xl-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">More Coming Soon...</h4>
                    <div class="row" style="height: 509px;">
                        <div class="col-lg-12 text-center" style="padding-top: 200px">
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 75%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('footer_js')
    <script src="{{url('assets')}}/plugins/switchery/switchery.min.js"></script>
    <script>
        $('[data-toggle="switchery"]').each(function (idx, obj) {
            new Switchery($(this)[0], $(this).data());
        });

        function changeGatewayStatus(value){
            var provider = value;
            $.ajax({
                type: "GET",
                url: "{{ url('change/gateway/status') }}"+'/'+provider,
                success: function (data) {
                    toastr.success("Status Changed", "Updated Successfully");
                    setTimeout(function() {
                        console.log("Wait For 1 Sec");
                        location.reload(true);
                    }, 1000);
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
        }
    </script>
@endsection
