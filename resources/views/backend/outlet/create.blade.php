@extends('backend.master')

@section('header_css')
    <link href="{{url('assets')}}/plugins/dropify/dropify.min.css" rel="stylesheet" type="text/css" />
    <link href="{{url('assets')}}/plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="{{url('assets')}}/css/tagsinput.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <style>
        .select2-selection{
            height: 34px !important;
            border: 1px solid #ced4da !important;
        }
        .select2 {
            width: 100% !important;
        }
        .bootstrap-tagsinput .badge {
            margin: 2px 2px !important;
        }
    </style>
@endsection

@section('page_title')
    Outlet
@endsection
@section('page_heading')
    Add an Outlet
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-3">Outlet</h4>
                        <a href="{{ route('ViewAllOutlet')}}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>

                    <form class="needs-validation" method="POST" action="{{url('save/new/outlet')}}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-lg-12">

                                <div class="row">

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="image">Image <span class="text-danger">*</span></label>
                                            <input type="file" name="images[]" class="dropify" data-height="200" data-max-file-size="1M" accept="image/*" multiple>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">

                                        <div class="form-group">
                                            <label for="title">Title <span class="text-danger">*</span></label>
                                            <input type="text" id="title" name="title" maxlength="255" class="form-control" placeholder="Enter Outlet Title Here" >
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('title')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="address">Address</label>
                                            <textarea id="address" name="address" class="form-control" placeholder="Enter Address Here"></textarea>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('address')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="opening">Opening <span class="text-danger"></span></label>
                                            <input type="text" id="opening" name="opening" maxlength="255" class="form-control" placeholder="Enter Opening and Ending Time Here" >
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('opening')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="contact_number_1">Contact Number 1 <span class="text-danger"></span></label>
                                            <input type="text" id="contact_number_1" name="contact_number_1" maxlength="255" class="form-control" placeholder="Enter Contact Number 1" >
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('contact_number_1')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="contact_number_2">Contact Number 2 <span class="text-danger"></span></label>
                                            <input type="text" id="contact_number_2" name="contact_number_2" maxlength="255" class="form-control" placeholder="Enter Contact Number 2" >
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('contact_number_2')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="contact_number_3">Contact Number 3 <span class="text-danger"></span></label>
                                            <input type="text" id="contact_number_3" name="contact_number_3" maxlength="255" class="form-control" placeholder="Enter Contact Number 3" >
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('contact_number_3')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="video_link">Video Link</label>
                                            <textarea id="video_link" name="video_link" class="form-control" cols="7" rows="5" placeholder="Enter Video Introduction/Description(If Available)"></textarea>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('video_link')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="map">Map</label>
                                            <textarea id="map" name="map" class="form-control" placeholder="Enter Map Here"></textarea>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('map')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="description">Description</label>
                                            <textarea id="description" name="description" class="form-control" placeholder="Enter Description Here"></textarea>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('description')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>

                             
                            </div>
                        </div>


                        <div class="form-group text-center pt-3">
                            <a href="{{url('view/all/outlet')}}" style="width: 130px;" class="btn btn-danger d-inline-block text-white m-2" type="submit"><i class="mdi mdi-cancel"></i> Cancel</a>
                            <button class="btn btn-primary m-2" style="width: 130px;" type="submit"><i class="fas fa-save"></i> Save </button>
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
    <script src="{{url('assets')}}/plugins/select2/select2.min.js"></script>
    <script src="{{url('assets')}}/js/tagsinput.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script type="text/javascript">

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('[data-toggle="select2"]').select2();

        // $('#description').summernote({
        //     placeholder: 'Write Description Here',
        //     tabsize: 2,
        //     height: 350
        // });

    </script>
@endsection