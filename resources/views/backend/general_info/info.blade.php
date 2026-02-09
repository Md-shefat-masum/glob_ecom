@extends('backend.master')

@section('header_css')
    <link href="{{url('assets')}}/css/tagsinput.css" rel="stylesheet" type="text/css" />
    <style>
        .bootstrap-tagsinput .badge {
            margin: 2px 2px !important;
        }
    </style>
@endsection

@section('page_title')
    Website Config
@endsection
@section('page_heading')
    Entry General Information
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <form class="needs-validation" method="POST" action="{{url('update/general/info')}}">
                        @csrf

                        <div class="row">
                            <div class="col-lg-8">
                                <h4 class="card-title mb-3">General Information Form</h4>
                            </div>
                            <div class="col-lg-4 text-right">

                                <a href="{{url('/home')}}" style="width: 130px;" class="btn btn-danger d-inline-block text-white m-2" type="submit"><i class="mdi mdi-cancel"></i> Cancel</a>
                                <button class="btn btn-primary m-2" type="submit" style="width: 140px;"><i class="fas fa-save"></i> Update Info</button>

                            </div>
                        </div>

                        <div class="row justify-content-center pt-3">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    @php
                                        $logoUrl = '';
                                        if ($data->logo) {
                                            if (str_starts_with($data->logo, 'http://') || str_starts_with($data->logo, 'https://')) {
                                                $logoUrl = $data->logo;
                                            } else {
                                                $baseUrl = env('FILE_URL', env('APP_URL', url('/')));
                                                $baseUrl = rtrim($baseUrl, '/');
                                                $imagePath = ltrim($data->logo, '/');
                                                $logoUrl = $baseUrl . '/' . $imagePath;
                                            }
                                        }
                                    @endphp
                                    @include('backend.components.image_upload_v2', [
                                        'inputName' => 'logo',
                                        'label' => 'Primary Logo (Light)',
                                        'required' => false,
                                        'width' => 200,
                                        'height' => 60,
                                        'maxWidth' => '300px',
                                        'previewHeight' => '150px',
                                        'directory' => 'company-logos',
                                        'value' => $data->logo ?? '',
                                        'imageUrl' => $logoUrl
                                    ])
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    @php
                                        $logoDarkUrl = '';
                                        if ($data->logo_dark) {
                                            if (str_starts_with($data->logo_dark, 'http://') || str_starts_with($data->logo_dark, 'https://')) {
                                                $logoDarkUrl = $data->logo_dark;
                                            } else {
                                                $baseUrl = env('FILE_URL', env('APP_URL', url('/')));
                                                $baseUrl = rtrim($baseUrl, '/');
                                                $imagePath = ltrim($data->logo_dark, '/');
                                                $logoDarkUrl = $baseUrl . '/' . $imagePath;
                                            }
                                        }
                                    @endphp
                                    @include('backend.components.image_upload_v2', [
                                        'inputName' => 'logo_dark',
                                        'label' => 'Secondary Logo (Dark)',
                                        'required' => false,
                                        'width' => 200,
                                        'height' => 60,
                                        'maxWidth' => '300px',
                                        'previewHeight' => '150px',
                                        'directory' => 'company-logos',
                                        'value' => $data->logo_dark ?? '',
                                        'imageUrl' => $logoDarkUrl
                                    ])
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    @php
                                        $favIconUrl = '';
                                        if ($data->fav_icon) {
                                            if (str_starts_with($data->fav_icon, 'http://') || str_starts_with($data->fav_icon, 'https://')) {
                                                $favIconUrl = $data->fav_icon;
                                            } else {
                                                $baseUrl = env('FILE_URL', env('APP_URL', url('/')));
                                                $baseUrl = rtrim($baseUrl, '/');
                                                $imagePath = ltrim($data->fav_icon, '/');
                                                $favIconUrl = $baseUrl . '/' . $imagePath;
                                            }
                                        }
                                    @endphp
                                    @include('backend.components.image_upload_v2', [
                                        'inputName' => 'fav_icon',
                                        'label' => 'Favicon',
                                        'required' => false,
                                        'width' => 64,
                                        'height' => 64,
                                        'maxWidth' => '150px',
                                        'previewHeight' => '150px',
                                        'directory' => 'company-logos',
                                        'value' => $data->fav_icon ?? '',
                                        'imageUrl' => $favIconUrl
                                    ])
                                </div>
                            </div>
                        </div>


                        <div class="row justify-content-center pt-3">
                            <div class="col-lg-9">

                                <div class="form-group row">
                                    <label for="company_name" class="col-sm-2 col-form-label">Company Name <span class="text-danger">*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" id="company_name" name="company_name" value="{{$data->company_name}}" class="form-control" placeholder="Enter Company Name Here" required>
                                        <div class="invalid-feedback" style="display: block;">
                                            @error('company_name')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row" style="display:none">
                                    <label for="tab_title" class="col-sm-2 col-form-label">Browser Tab Title <span class="text-danger">*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" id="tab_title" name="tab_title" value="{{$data->tab_title}}" class="form-control" placeholder="Tab Title">
                                        <div class="invalid-feedback" style="display: block;">
                                            @error('tab_title')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="contact" class="col-sm-2 col-form-label">Phone No. <span class="text-danger">*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" id="contact" data-role="tagsinput" name="contact" value="{{$data->contact}}" class="form-control" placeholder="01*********">
                                        <div class="invalid-feedback" style="display: block;">
                                            @error('contact')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="email" class="col-sm-2 col-form-label">Company Emails <span class="text-danger">*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" id="email" data-role="tagsinput" name="email" value="{{$data->email}}" class="form-control" placeholder="Write Email Here">
                                        <div class="invalid-feedback" style="display: block;">
                                            @error('email')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="short_description" class="col-sm-2 col-form-label">Short Description</label>
                                    <div class="col-sm-10">
                                        <textarea id="short_description" name="short_description" maxlength="255" rows="3" class="form-control" placeholder="Enter Short Description about Company">{{$data->short_description}}</textarea>
                                        <div class="invalid-feedback" style="display: block;">
                                            @error('short_description')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="address" class="col-sm-2 col-form-label">Company Address</label>
                                    <div class="col-sm-10">
                                        <textarea id="address" name="address" rows="3" class="form-control" placeholder="Enter Company Address Here">{{$data->address}}</textarea>
                                        <div class="invalid-feedback" style="display: block;">
                                            @error('address')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="google_map_link" class="col-sm-2 col-form-label"><i class="fas fa-map-marker-alt"></i> Google Map Link</label>
                                    <div class="col-sm-10">
                                        <textarea name="google_map_link" id="google_map_link" class="form-control">{{ $data->google_map_link }}</textarea>
                                        <div class="invalid-feedback" style="display: block;">
                                            @error('google_map_link')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="play_store_link" class="col-sm-2 col-form-label"><i class="fab fa-google-play"></i> Play Store Link</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="play_store_link" id="play_store_link" value="{{ $data->play_store_link }}" placeholder="https://play.google.com/store" class="form-control"/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="app_store_link" class="col-sm-2 col-form-label"><i class="fab fa-apple" style="font-size: 16px;"></i> App Store Link</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="app_store_link" id="app_store_link" value="{{ $data->app_store_link }}" placeholder="https://www.apple.com/app-store/" class="form-control"/>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="footer_copyright_text" class="col-sm-2 col-form-label"><i class="far fa-copyright"></i> Footer Copyright Text</label>
                                    <div class="col-sm-10">
                                        <textarea name="footer_copyright_text" id="footer_copyright_text" class="form-control">{{ $data->footer_copyright_text }}</textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="payment_banner" class="col-sm-2 col-form-label"><i class="fab fa-cc-visa"></i> Payment Banner</label>
                                    <div class="col-sm-10">
                                        @php
                                            $paymentBannerUrl = '';
                                            if ($data->payment_banner) {
                                                if (str_starts_with($data->payment_banner, 'http://') || str_starts_with($data->payment_banner, 'https://')) {
                                                    $paymentBannerUrl = $data->payment_banner;
                                                } else {
                                                    $baseUrl = env('FILE_URL', env('APP_URL', url('/')));
                                                    $baseUrl = rtrim($baseUrl, '/');
                                                    $imagePath = ltrim($data->payment_banner, '/');
                                                    $paymentBannerUrl = $baseUrl . '/' . $imagePath;
                                                }
                                            }
                                        @endphp
                                        @include('backend.components.image_upload_v2', [
                                            'inputName' => 'payment_banner',
                                            'label' => 'Payment Banner',
                                            'required' => false,
                                            'width' => 400,
                                            'height' => 100,
                                            'maxWidth' => '500px',
                                            'previewHeight' => '150px',
                                            'directory' => 'company-logos',
                                            'value' => $data->payment_banner ?? '',
                                            'imageUrl' => $paymentBannerUrl
                                        ])
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="short_description" class="col-sm-2 col-form-label">Company Description</label>
                                    <div class="col-sm-10">
                                        <textarea id="description" name="description"  rows="3" class="form-control" placeholder="Full Description about Company">{{$data->description}}</textarea>
                                        <div class="invalid-feedback" style="display: block;">
                                            @error('description')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group text-center">
                                    <a href="{{url('/home')}}" style="width: 130px;" class="btn btn-danger d-inline-block text-white m-2" type="submit"><i class="mdi mdi-cancel"></i> Cancel</a>
                                    <button class="btn btn-primary m-2" type="submit" style="width: 140px;"><i class="fas fa-save"></i> Update Info</button>
                                </div>

                            </div>

                        </div>


                        {{-- <div class="form-group text-center pt-3 mt-3">
                            <a href="{{url('/home')}}" style="width: 130px;" class="btn btn-danger d-inline-block text-white m-2" type="submit"><i class="mdi mdi-cancel"></i> Cancel</a>
                            <button class="btn btn-primary m-2" type="submit" style="width: 140px;"><i class="fas fa-save"></i> Update Info</button>
                        </div> --}}
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('footer_js')
    <script src="{{url('assets')}}/js/tagsinput.js"></script>
    <script src="https://cdn.ckeditor.com/4.12.1/standard/ckeditor.js"></script>

    <script>
        // CKEDITOR.replace('footer_copyright_text', {
        //     filebrowserUploadUrl: "{{route('ckeditor.upload', ['_token' => csrf_token() ])}}",
        //     filebrowserUploadMethod: 'form',
        //     height: 120,
        // });
        CKEDITOR.replace('description', {
            height: 120,
        });
    </script>
@endsection
