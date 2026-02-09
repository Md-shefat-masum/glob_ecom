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
    Create Side Banner
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-3">Side Banner Create Form</h4>
                        <a href="{{ route('ViewAllSideBanner')}}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>

                    <form class="needs-validation" method="POST" action="{{url('save/new/side-banner')}}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-lg-5">

                                <div class="form-group">
                                    <label for="colFormLabel" class="col-form-label">Side Banner Image (1112px X 400px) <span class="text-danger">*</span></label>
                                    <input type="file" name="banner_img" class="dropify" data-height="250" data-max-file-size="1M" accept="image/*"/>
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <div class="form-group">
                                    <label for="title" class="col-form-label">Title</label>
                                    <input type="text" name="title" class="form-control" id="title" placeholder="Title...">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('title')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="banner_link" class="col-form-label">URL LINK</label>
                                    <input type="text" name="banner_link" class="form-control" id="banner_link" placeholder="Url link">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('banner_link')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="button_title" class="col-form-label">Button Link</label>
                                    <input type="text" name="button_title" class="form-control" id="button_title" placeholder="Click here..">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('button_title')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="button_url" class="col-form-label">Button Url</label>
                                    <input type="text" name="button_url" class="form-control" id="button_url" placeholder="https://tpmart.techparkit.org" >
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
                                <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Save</button>
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
@endsection