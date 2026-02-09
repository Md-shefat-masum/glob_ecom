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
    Custom Page
@endsection
@section('page_heading')
    Update Custom Page
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-3">Update Custom Page Form</h4>
                        <a href="{{ route('ViewCustomPages')}}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>


                    <form class="needs-validation" method="POST" action="{{url('update/custom/page')}}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="custom_page_id" value="{{$data->id}}">
                        <div class="row">
                            <div class="col-lg-5">

                                <div class="form-group">
                                    <label for="colFormLabel" class="col-form-label">Page Feature Image (1112px X 400px)</label>
                                    <input type="file" name="image" class="dropify" data-height="250" data-max-file-size="1M" accept="image/*"/>
                                </div>

                                <div class="form-group">
                                    <label for="meta_title">Page Meta Title <small class="text-info font-weight-bolder">(SEO)</small></label>
                                    <input type="text" id="meta_title" name="meta_title" value="{{$data->meta_title}}" class="form-control" placeholder="Meta Title">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('meta_title')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="meta_keywords">Page Meta Keywords <small class="text-info font-weight-bolder">(SEO)</small></label>
                                    <input type="text" id="meta_keywords" data-role="tagsinput" value="{{$data->meta_keyword}}" name="meta_keywords" class="form-control">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('meta_keywords')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="meta_description">Page Meta Description <small class="text-info font-weight-bolder">(SEO)</small></label>
                                    <textarea id="meta_description" name="meta_description" class="form-control" placeholder="Meta Description Here" rows="5">{{$data->meta_description}}</textarea>
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('meta_description')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="colFormLabe0" class="col-form-label">Status <span class="text-danger">*</span></label>
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
                            <div class="col-lg-7">

                                <div class="form-group">
                                    <label for="page_title" class="col-form-label">Page Title <span class="text-danger">*</span></label>
                                    <input type="text" name="page_title" class="form-control" value="{{$data->page_title}}" id="page_title" placeholder="Page Title" required>
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('page_title')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="description" class="col-form-label">Page Description</label>
                                    <textarea id="description" name="description" class="form-control">{!! $data->description !!}</textarea>
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('description')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>

                            </div>
                        </div>


                        <div class="form-group row pt-3">
                            <div class="col-sm-12 text-center">
                                <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Update Custom Page</button>
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
        $('#description').summernote({
            placeholder: 'Write Description Here',
            tabsize: 2,
            height: 400
        });

        @if($data->image && file_exists(public_path($data->image)))
            $(".dropify-preview").eq(0).css("display", "block");
            $(".dropify-clear").eq(0).css("display", "block");
            $(".dropify-filename-inner").eq(0).html("{{$data->image}}");
            $("span.dropify-render").eq(0).html("<img src='{{url($data->image)}}'>");
        @endif
    </script>
@endsection