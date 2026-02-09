@extends('backend.master')

@section('header_css')
    <link href="{{url('assets')}}/css/jquery.datetimepicker.css" rel="stylesheet" type="text/css" />
    <link href="{{url('assets')}}/plugins/dropify/dropify.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('page_title')
    Promo Code
@endsection
@section('page_heading')
    Update Promo Code
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-3">Promo Code Update Form</h4>
                         <a href="{{ route('ViewAllPromoCodes')}}" class="btn btn-secondary">
                             <i class="fas fa-arrow-left"></i>
                         </a>
                     </div>

                    <form class="needs-validation" method="POST" action="{{url('update/promo/code')}}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="slug" value="{{$data->slug}}">

                        <div class="row">
                            <div class="col-lg-5">
                                <div class="form-group">
                                    @include('backend.components.image_upload_v2', [
                                        'inputName' => 'icon',
                                        'label' => 'Promo Icon',
                                        'required' => true,
                                        'width' => 250,
                                        'height' => 250,
                                        'maxWidth' => '250px',
                                        'previewHeight' => '250px',
                                        'directory' => 'promo_icons',
                                        'value' => env('FILE_URL').'/'.$data->icon,
                                    ])
                                    <small class="text-muted">250x250px recommended</small>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="title">Title <span class="text-danger">*</span></label>
                                    <input type="text" id="title" name="title" class="form-control" value="{{$data->title}}" placeholder="25% OFF Promo" required>
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('title')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="description">Short Description</label>
                                    <textarea id="description" rows="8" name="description" class="form-control" placeholder="During this sale, we're offering 25% OFF this summary. Make sure you don't miss it.">{{$data->description}}</textarea>
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('description')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="effective_date">Effective Date <span class="text-danger">*</span></label>
                                            <input type="text" id="effective_date" name="effective_date" value="{{date("d/m/Y", strtotime(str_replace("-","/",$data->effective_date)))}}" placeholder="d/m/Y" class="form-control" required>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('effective_date')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="expire_date">Expiry Date <span class="text-danger">*</span></label>
                                            <input type="text" id="expire_date" name="expire_date" value="{{ date("d/m/Y", strtotime(str_replace("-","/",$data->expire_date))) }}" placeholder="d/m/Y" class="form-control" required>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('expire_date')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="type">Type <span class="text-danger">*</span></label>
                                            <select class="form-control" name="type" required>
                                                <option value="">Select One</option>
                                                <option value="1" @if($data->type == 1) selected @endif>Amount (৳) Based</option>
                                                <option value="2" @if($data->type == 2) selected @endif>Percentage (%) Based</option>
                                            </select>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('type')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="value">Value <span class="text-danger">*</span></label>
                                            <input type="number" id="value" value="{{$data->value}}" name="value" class="form-control" required>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('value')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="code">Code <span class="text-danger">*</span></label>
                                            <input type="text" id="code" value="{{$data->code}}" name="code" class="form-control" placeholder="SNNY22" required>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('code')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="minimum_order_amount">Minimum Order Amount</label>
                                            <input type="number" id="minimum_order_amount" name="minimum_order_amount" value="{{$data->minimum_order_amount}}" class="form-control">
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('minimum_order_amount')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select class="form-control" name="status" required>
                                        <option value="">Select One</option>
                                        <option value="1" @if($data->status == 1) selected @endif>Active</option>
                                        <option value="2" @if($data->status == 2) selected @endif>Inactive</option>
                                    </select>
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('type')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="form-group text-center pt-3">
                            <a href="{{url('view/all/promo/codes')}}" style="width: 130px;" class="btn btn-danger d-inline-block text-white m-2" type="submit"><i class="mdi mdi-cancel"></i> Cancel</a>
                            <button class="btn btn-primary" type="submit">Update Promo Code</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_js')
    <script src="{{url('assets')}}/js/jquery.datetimepicker.full.min.js"></script>
    <script src="{{url('assets')}}/plugins/dropify/dropify.min.js"></script>
    <script src="{{url('assets')}}/pages/fileuploads-demo.js"></script>
    <script>
        $("#effective_date").datetimepicker({
            timepicker: false,
            // minDate: "-1970/01/01",
            format: "d/m/Y",
        });

        $("#expire_date").datetimepicker({
            timepicker: false,
            // minDate: "-1970/01/01",
            format: "d/m/Y",
        });

        @if($data->icon && file_exists(public_path($data->icon)))
            $(".dropify-preview").eq(0).css("display", "block");
            $(".dropify-clear").eq(0).css("display", "block");
            $(".dropify-filename-inner").eq(0).html("{{$data->icon}}");
            $("span.dropify-render").eq(0).html("<img src='{{url($data->icon)}}'>");
        @endif
    </script>
@endsection

