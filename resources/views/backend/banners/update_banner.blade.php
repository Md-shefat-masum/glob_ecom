@extends('backend.master')

@section('header_css')
@endsection

@section('page_title')
    Banner
@endsection
@section('page_heading')
    Update Banner
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-3">Banner Update Form</h4>
                        <a href="{{ route('ViewAllBanners')}}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>

                    <form class="needs-validation" method="POST" action="{{url('update/banner')}}">
                        @csrf

                        <input type="hidden" name="slug" value="{{$data->slug}}">

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    @php
                                        // Get full URL for existing image
                                        $imageUrl = '';
                                        if ($data->image) {
                                            // Check if it's already a full URL
                                            if (str_starts_with($data->image, 'http://') || str_starts_with($data->image, 'https://')) {
                                                $imageUrl = $data->image;
                                            } else {
                                                // Construct URL using FILE_URL or APP_URL
                                                $baseUrl = env('FILE_URL', env('APP_URL', url('/')));
                                                $baseUrl = rtrim($baseUrl, '/');
                                                // Use baseUrl + data directly (data already contains the full path like media/banners/2025/12/file.jpg)
                                                $imagePath = ltrim($data->image, '/');
                                                $imageUrl = $baseUrl . '/' . $imagePath;
                                            }
                                        }
                                    @endphp
                                    @include('backend.components.image_upload_v2', [
                                        'inputName' => 'banner_image',
                                        'label' => 'Banner Image',
                                        'required' => false,
                                        'width' => 284,
                                        'height' => 380,
                                        'maxWidth' => '300px',
                                        'previewHeight' => '400px',
                                        'directory' => 'banners',
                                        'value' => $data->image ?? '',
                                        'imageUrl' => $imageUrl
                                    ])
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="colFormLabe0">Status <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control" id="colFormLabe0" required>
                                        <option value="">Select One</option>
                                        <option value="1" @if($data->status == 1) selected @endif>Active</option>
                                        <option value="0" @if($data->status == 0) selected @endif>Inactive</option>
                                    </select>
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('status')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="position">Banner Position</label>
                                    <select class="form-control" name="position" id="position" required>
                                        <option value="">Select Option</option>
                                        <option value="top" @if($data->position == 'top') selected @endif>Top (Homepage)</option>
                                        <option value="middle" @if($data->position == 'middle') selected @endif>Middle (Homepage)</option>
                                        <option value="bottom" @if($data->position == 'bottom') selected @endif>Bottom (Homepage)</option>
                                        <option value="popup" @if($data->position == 'popup') selected @endif>PopUp</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="sub_title">Sub Title</label>
                                    <input type="text" name="sub_title" id="sub_title" value="{{$data->sub_title}}" class="form-control" placeholder="Write Sub Title Here"/>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="title">Title</label>
                                    <input type="text" name="title" id="title" value="{{$data->title}}" class="form-control" placeholder="Write Title Here"/>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <input type="text" name="description" id="description" value="{{$data->description}}" class="form-control" placeholder="Write Description Here"/>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="text_position">Text Position</label>
                                    <select class="form-control" name="text_position" id="text_position">
                                        <option value="">Select Option</option>
                                        <option value="left" @if($data->text_position == 'left') selected @endif>Left</option>
                                        <option value="right" @if($data->text_position == 'right') selected @endif>Right</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="link">Banner Link</label>
                                    <input type="text" name="link" value="{{$data->link}}" class="form-control" id="link" placeholder="https://">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('link')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="btn_text">Button Text</label>
                                    <input type="text" name="btn_text" value="{{$data->btn_text}}" id="btn_text" class="form-control" placeholder="ex. New Collection"/>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="btn_link">Button link</label>
                                    <input type="text" name="btn_link" value="{{$data->btn_link}}" class="form-control" id="btn_link" placeholder="https://">
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-lg-12 text-center">
                                <div class="form-group">
                                    <button class="btn btn-primary" type="submit"><i class="feather-save"></i> Update Banner</button>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('footer_js')
@endsection
