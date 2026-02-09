@extends('backend.master')

@section('header_css')
    <link href="{{url('assets')}}/plugins/dropify/dropify.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">


    <style>
        .title-btm-seperator{
            position: relative;
        }
        .title-btm-seperator::before{
            position: absolute;
            content: "";
            width: 200px;
            height: 6px;
            background: #0079FF;
            bottom: -10px;
            left: 0;
            border-radius: 6px;
        }
    </style>
@endsection

@section('page_title')
    About Us
@endsection
@section('page_heading')
    Update About Us
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">About Us Update Form</h4>

                    <form class="needs-validation" method="POST" action="{{url('update/about/us')}}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="banner_bg">Banner Background Image (1112px X 400px) <span class="text-danger">*</span></label>
                                    <input type="file" name="banner_bg" class="dropify" data-height="203" data-max-file-size="1M" accept="image/*"/>
                                </div>
                                <div class="form-group">
                                    <label for="image">Side Image <span class="text-danger">*</span></label>
                                    <input type="file" name="image" class="dropify" data-height="232" data-max-file-size="1M" accept="image/*"/>
                                </div>
                            </div>
                            <div class="col-lg-9">

                                <div class="form-group">
                                    <label for="section_sub_title">Sub Title <span class="text-danger">*</span></label>
                                    <input type="text" id="section_sub_title" name="section_sub_title" value="{{$data->section_sub_title ?? ''}}" class="form-control" placeholder="Enter Sub Title Here">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('section_sub_title')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="section_title">Section Title <span class="text-danger">*</span></label>
                                    <input type="text" id="section_title" name="section_title" class="form-control" placeholder="Write Section Title Here" value="{{$data->section_title ?? ''}}" required>
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('section_title')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="section_description">Description</label>
                                    <textarea id="section_description" name="section_description" class="form-control">{!! $data->section_description ?? '' !!}</textarea>
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('section_description')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="btn_icon_class">Button Icon Class</label>
                                            <input type="text" id="btn_icon_class" value="{{$data->btn_icon_class ?? ''}}" name="btn_icon_class" class="form-control" placeholder="fi-rs-download">
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('btn_icon_class')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="btn_text">Button Text</label>
                                            <input type="text" id="btn_text" value="{{$data->btn_text ?? ''}}" name="btn_text" class="form-control" placeholder="Enter Text Here">
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('btn_text')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-5">
                                        <div class="form-group">
                                            <label for="btn_link">Button Link</label>
                                            <input type="text" id="btn_link" value="{{$data->btn_link ?? ''}}" name="btn_link" class="form-control" placeholder="https://">
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('btn_link')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>
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

    </div>
@endsection


@section('footer_js')
    <script src="{{url('assets')}}/plugins/dropify/dropify.min.js"></script>
    <script src="{{url('assets')}}/pages/fileuploads-demo.js"></script>
    <script src="{{url('assets') }}/js/spectrum.min.js"></script>
    {{-- <script src="https://cdn.ckeditor.com/4.12.1/standard/ckeditor.js"></script> --}}
    {{-- <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

    <script type="text/javascript">
        $('#section_description').summernote({
            placeholder: 'Write Description Here',
            tabsize: 2,
            height: 400
        });
    </script>
    
    <script>

        // CKEDITOR.replace('section_description', {
        //     filebrowserUploadUrl: "{{route('ckeditor.upload', ['_token' => csrf_token() ])}}",
        //     filebrowserUploadMethod: 'form',
        //     height: 160,
        // });

        $("#bg_color").spectrum({
            preferredFormat: 'hex',
        });

        @if ($data)
            @if($data->banner_bg && file_exists(public_path($data->banner_bg)))
                $(".dropify-preview").eq(0).css("display", "block");
                $(".dropify-clear").eq(0).css("display", "block");
                $(".dropify-filename-inner").eq(0).html("{{$data->banner_bg}}");
                $("span.dropify-render").eq(0).html("<img src='{{url($data->banner_bg)}}'>");
            @endif

            @if($data->image && file_exists(public_path($data->image)))
                $(".dropify-preview").eq(1).css("display", "block");
                $(".dropify-clear").eq(1).css("display", "block");
                $(".dropify-filename-inner").eq(1).html("{{$data->image}}");
                $("span.dropify-render").eq(1).html("<img src='{{url($data->image)}}'>");
            @endif
        @endif
    </script>
@endsection
