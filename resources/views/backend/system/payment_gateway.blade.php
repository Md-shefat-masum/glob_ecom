@extends('backend.master')

@section('header_css')
    <link href="{{url('assets')}}/plugins/switchery/switchery.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('page_title')
    Payment Gateways
@endsection

@section('page_heading')
    Payment Gateways Credentials
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-3 col-xl-3">
            <div class="card" style="height: 750px; @if($gateways[0]->status == 1) border: 2px solid green; box-shadow: 2px 2px 5px #b5b5b5; @endif">
                <div class="card-body">
                    <h4 class="card-title mb-3">
                        <div class="row">
                            <div class="col-lg-8">SSL Commerz Gateway</div>
                            <div class="col-lg-4 text-right">
                                <input type="checkbox" class="switchery_checkbox" id="ssl_commerz" @if($gateways[0]->status == 1) checked @endif value="ssl_commerz" onchange="changeGatewayStatus(this.value)" name="has_variant" data-size="small" data-toggle="switchery" data-color="#53c024" data-secondary-color="#df3554"/>
                            </div>
                        </div>
                    </h4>

                    <div class="row" style="height: 120px;">
                        <div class="col-lg-12 text-center pt-4 pb-4">
                            <img src="{{url('/')}}/images/ssl_commerz.png" class="img-fluid" style="max-width: 200px; max-height: 130px; padding-top: 20px">
                        </div>
                    </div>

                    <form class="needs-validation" method="POST" action="{{url('update/payment/gateway/info')}}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="provider_name" value="{{$gateways[0]->provider_name}}">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="api_key">Store ID</label>
                                    <input type="text" id="api_key" name="api_key" value="{{$gateways[0]->api_key}}" class="form-control" placeholder="3423423URYUR">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('api_key')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="secret_key">Store Password</label>
                                    <input type="text" id="secret_key" name="secret_key" value="{{$gateways[0]->secret_key}}" class="form-control" placeholder="7345876UYTUYR856778">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('secret_key')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" id="username" name="username" value="{{$gateways[0]->username}}" class="form-control" placeholder="Username">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('username')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="text" id="password" name="password" value="{{$gateways[0]->password}}" class="form-control" placeholder="*********">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('password')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="live">Payment Mode</label>
                                    <select class="form-control" id="live" name="live">
                                        <option value="">Select One</option>
                                        <option value="0" @if($gateways[0]->live == 0) selected @endif>Test/Sandbox</option>
                                        <option value="1" @if($gateways[0]->live == 1) selected @endif>Production/Live</option>
                                    </select>
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('live')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="">Select One</option>
                                        <option value="0" @if($gateways[0]->status == 0) selected @endif>Inactive</option>
                                        <option value="1" @if($gateways[0]->status == 1) selected @endif>Active</option>
                                    </select>
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('status')
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

        {{-- <div class="col-lg-3 col-xl-3">
            <div class="card" style="height: 750px; @if($gateways[1]->status == 1) border: 2px solid green; box-shadow: 2px 2px 5px #b5b5b5; @endif">
                <div class="card-body">
                    <h4 class="card-title mb-3">
                        <div class="row">
                            <div class="col-lg-8">Stripe Payment Gateway</div>
                            <div class="col-lg-4 text-right">
                                <input type="checkbox" class="switchery_checkbox" id="stripe" value="stripe" @if($gateways[1]->status == 1) checked @endif onchange="changeGatewayStatus(this.value)" name="has_variant" data-size="small" data-toggle="switchery" data-color="#53c024" data-secondary-color="#df3554"/>
                            </div>
                        </div>
                    </h4>

                    <div class="row" style="height: 120px;">
                        <div class="col-lg-12 text-center pt-4 pb-4">
                            <img src="{{url('/')}}/images/stripe_payment_gatway.png" style="max-width: 200px; max-height: 130px;">
                        </div>
                    </div>

                    <form class="needs-validation" method="POST" action="{{url('update/payment/gateway/info')}}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="provider_name" value="{{$gateways[1]->provider_name}}">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="api_key">API Key</label>
                                    <input type="text" id="api_key" name="api_key" value="{{$gateways[1]->api_key}}" class="form-control" placeholder="3423423URYUR">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('api_key')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="secret_key">Secret Key</label>
                                    <input type="text" id="secret_key" name="secret_key" value="{{$gateways[1]->secret_key}}" class="form-control" placeholder="7345876UYTUYR856778">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('secret_key')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" id="username" name="username" value="{{$gateways[1]->username}}" class="form-control" placeholder="Username">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('username')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="text" id="password" name="password" value="{{$gateways[1]->password}}" class="form-control" placeholder="*********">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('password')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="live">Payment Mode</label>
                                    <select class="form-control" id="live" name="live">
                                        <option value="">Select One</option>
                                        <option value="0" @if($gateways[1]->live == 0) selected @endif>Test/Sandbox</option>
                                        <option value="1" @if($gateways[1]->live == 1) selected @endif>Production/Live</option>
                                    </select>
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('live')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="">Select One</option>
                                        <option value="0" @if($gateways[1]->status == 0) selected @endif>Inactive</option>
                                        <option value="1" @if($gateways[1]->status == 1) selected @endif>Active</option>
                                    </select>
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('status')
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
        </div> --}}

        {{-- <div class="col-lg-3 col-xl-3">
            <div class="card" style="height: 750px; @if($gateways[2]->status == 1) border: 2px solid green; box-shadow: 2px 2px 5px #b5b5b5; @endif">
                <div class="card-body">
                    <h4 class="card-title mb-3">
                        <div class="row">
                            <div class="col-lg-8">bKash Payment Gateway</div>
                            <div class="col-lg-4 text-right">
                                <input type="checkbox" class="switchery_checkbox" id="bkash" value="bkash" @if($gateways[2]->status == 1) checked @endif onchange="changeGatewayStatus(this.value)" name="has_variant" data-size="small" data-toggle="switchery" data-color="#53c024" data-secondary-color="#df3554"/>
                            </div>
                        </div>
                    </h4>

                    <div class="row" style="height: 120px;">
                        <div class="col-lg-12 text-center pt-4 pb-4">
                            <img src="{{url('/')}}/images/bkash_payment_gateway.png" style="max-width: 200px; max-height: 130px; height: 90px">
                        </div>
                    </div>

                    <form class="needs-validation" method="POST" action="{{url('update/payment/gateway/info')}}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="provider_name" value="{{$gateways[2]->provider_name}}">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="api_key">API Key</label>
                                    <input type="text" id="api_key" name="api_key" value="{{$gateways[2]->api_key}}" class="form-control" placeholder="3423423URYUR">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('api_key')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="secret_key">Secret Key</label>
                                    <input type="text" id="secret_key" name="secret_key" value="{{$gateways[2]->secret_key}}" class="form-control" placeholder="7345876UYTUYR856778">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('secret_key')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" id="username" name="username" value="{{$gateways[2]->username}}" class="form-control" placeholder="Username">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('username')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="text" id="password" name="password" value="{{$gateways[2]->password}}" class="form-control" placeholder="*********">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('password')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="live">Payment Mode</label>
                                    <select class="form-control" id="live" name="live">
                                        <option value="">Select One</option>
                                        <option value="0" @if($gateways[2]->live == 0) selected @endif>Test/Sandbox</option>
                                        <option value="1" @if($gateways[2]->live == 1) selected @endif>Production/Live</option>
                                    </select>
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('live')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="">Select One</option>
                                        <option value="0" @if($gateways[2]->status == 0) selected @endif>Inactive</option>
                                        <option value="1" @if($gateways[2]->status == 1) selected @endif>Active</option>
                                    </select>
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('status')
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
        </div> --}}

        {{-- <div class="col-lg-3 col-xl-3">
            <div class="card" style="height: 750px; @if($gateways[3]->status == 1) border: 2px solid green; box-shadow: 2px 2px 5px #b5b5b5; @endif">
                <div class="card-body">
                    <h4 class="card-title mb-3">
                        <div class="row">
                            <div class="col-lg-8">Amar Pay Payment Gateway</div>
                            <div class="col-lg-4 text-right">
                                <input type="checkbox" class="switchery_checkbox" id="amar_pay" value="amar_pay" @if($gateways[3]->status == 1) checked @endif onchange="changeGatewayStatus(this.value)" name="has_variant" data-size="small" data-toggle="switchery" data-color="#53c024" data-secondary-color="#df3554"/>
                            </div>
                        </div>
                    </h4>

                    <div class="row" style="height: 120px;">
                        <div class="col-lg-12 text-center pt-4 pb-4">
                            <img src="{{url('/')}}/images/amar_pay.png" style="max-width: 220px; max-height: 130px; height: 65px">
                        </div>
                    </div>

                    <form class="needs-validation" method="POST" action="{{url('update/payment/gateway/info')}}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="provider_name" value="{{$gateways[3]->provider_name}}">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="api_key">API Key</label>
                                    <input type="text" id="api_key" name="api_key" value="{{$gateways[3]->api_key}}" class="form-control" placeholder="3423423URYUR">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('api_key')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="secret_key">Secret Key</label>
                                    <input type="text" id="secret_key" name="secret_key" value="{{$gateways[3]->secret_key}}" class="form-control" placeholder="7345876UYTUYR856778">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('secret_key')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" id="username" name="username" value="{{$gateways[3]->username}}" class="form-control" placeholder="Username">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('username')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="text" id="password" name="password" value="{{$gateways[3]->password}}" class="form-control" placeholder="*********">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('password')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="live">Payment Mode</label>
                                    <select class="form-control" id="live" name="live">
                                        <option value="">Select One</option>
                                        <option value="0" @if($gateways[3]->live == 0) selected @endif>Test/Sandbox</option>
                                        <option value="1" @if($gateways[3]->live == 1) selected @endif>Production/Live</option>
                                    </select>
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('live')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="">Select One</option>
                                        <option value="0" @if($gateways[3]->status == 0) selected @endif>Inactive</option>
                                        <option value="1" @if($gateways[3]->status == 1) selected @endif>Active</option>
                                    </select>
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('status')
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
        </div> --}}
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
                url: "{{ url('change/payment/gateway/status') }}"+'/'+provider,
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
