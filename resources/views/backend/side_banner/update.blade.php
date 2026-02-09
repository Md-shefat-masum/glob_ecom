@extends('backend.master')

@section('header_css')
    <link href="{{url('assets')}}/plugins/dropify/dropify.min.css" rel="stylesheet" type="text/css" />
    <link href="{{url('assets')}}/css/tagsinput.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <style>
        .bootstrap-tagsinput .badge {
            margin: 2px 2px !important;
        }
    </style>
@endsection

@section('page_title')
    Side Banner
@endsection
@section('page_heading')
Side Banner Page
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-3">Side Banner Form</h4>
                        <a href="{{ route('ViewAllSideBanner')}}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>


                    <form class="needs-validation" method="POST" action="{{url('update/side-banner')}}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="custom_id" value="{{$data->id}}">
                        <div class="row">
                            <div class="col-lg-5">

                                <div class="form-group">
                                    <label for="colFormLabel" class="col-form-label">Side Banner Image (1112px X 400px)</label>
                                    <input type="file" name="banner_img" class="dropify" data-height="250" data-max-file-size="1M" accept="image/*"/>
                                </div>

                                <div class="form-group">
                                    <label for="colFormLabe0" class="col-form-label">Status <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control" id="colFormLabe0" required>
                                        <option value="">Select One</option>
                                        <option value="active" @if($data->status == 'active') selected @endif>Active</option>
                                        <option value="inactive" @if($data->status == 'inactive') selected @endif>Inactive</option>
                                    </select>
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('status')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>

                            </div>
                            <div class="col-lg-7">

                                <div class="form-group">
                                    <label for="title" class="col-form-label">Title</label>
                                    <input type="text" name="title" value="{{$data->title ?? ''}}" class="form-control" id="title" placeholder="Title...">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('title')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="banner_link" class="col-form-label">URL <span class="text-danger">*</span></label>
                                    <input type="text" name="banner_link" class="form-control" value="{{$data->banner_link ?? ''}}" id="banner_link" placeholder="Url Link">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('banner_link')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="button_title" class="col-form-label">Button Link</label>
                                    <input type="text" name="button_title" value="{{$data->button_title ?? ''}}" class="form-control" id="button_title" placeholder="Click here..">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('button_title')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="button_url" class="col-form-label">Button Url</label>
                                    <input type="text" name="button_url" value="{{$data->button_url ?? ''}}" class="form-control" id="button_url"  placeholder="https://tpmart.techparkit.org">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('button_url')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>

                            </div>
                        </div>


                        <div class="form-group row pt-3">
                            <div class="col-sm-12 text-right">
                                <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('footer_js')
    <script src="{{url('assets')}}/plugins/dropify/dropify.min.js"></script>
    <script src="{{url('assets')}}/pages/fileuploads-demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script src="{{url('assets')}}/js/tagsinput.js"></script>
    <script type="text/javascript">

        @if($data->banner_img && file_exists(public_path($data->banner_img)))
            $(".dropify-preview").eq(0).css("display", "block");
            $(".dropify-clear").eq(0).css("display", "block");
            $(".dropify-filename-inner").eq(0).html("{{$data->banner_img}}");
            $("span.dropify-render").eq(0).html("<img src='{{url($data->banner_img)}}'>");
        @endif
    </script>
@endsection