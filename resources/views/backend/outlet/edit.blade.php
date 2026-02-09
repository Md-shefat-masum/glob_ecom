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
    Edit Outlet
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-3">Update Form</h4>
                        <a href="{{ route('ViewAllOutlet')}}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>

                    <form class="needs-validation" method="POST" action="{{url('update/outlet')}}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="outlet_id" value="{{$data->id}}">
                        <div class="row">
                            <div class="col-lg-12">

                                <div class="row">
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="image">Image <span class="text-danger">*</span></label>
                                            <input type="file" name="images[]" class="dropify" data-height="200" data-max-file-size="1M" accept="image/*" multiple>
                                        </div>

                                        <!-- Display Existing Images -->
                                        @if($data->image)
                                            @php
                                                $images = json_decode($data->image, true);
                                            @endphp
                                            {{-- @dd($images) --}}
                                            <div class="mt-3">
                                                <label>Existing Images:</label>
                                                <div class="d-flex flex-wrap">
                                                    @foreach($images as $img)
                                                        <div class="m-2">
                                                            <img src="{{ asset(ltrim($img, '/')) }}" alt="Image" width="100" class="border rounded">
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-lg-9">
                                        <div class="form-group">
                                            <label for="title">Title <span class="text-danger">*</span></label>
                                            <input type="text" id="title" name="title" class="form-control" value="{{$data->title}}" placeholder="Enter Product Title Here" required>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('title')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- <div class="form-group">
                                            <label for="code">Code <span class="text-danger">*</span></label>
                                            <input type="text" id="code" name="code" maxlength="255" class="form-control" placeholder="Enter Customer Source Code Here" value="{{ $data->code }}" >
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('code')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div> --}}

                                        <div class="form-group">
                                            <label for="address">Address</label>
                                            <textarea id="address" name="address" class="form-control" placeholder="Enter Address Here">{{$data->address}}</textarea>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('address')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="opening">Opening <span class="text-danger"></span></label>
                                            <input type="text" id="opening" name="opening" maxlength="255" class="form-control" value="{{ $data->opening }}" placeholder="Enter Opening and Ending Time Here" >
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('opening')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="contact_number_1">Contact Number 1 <span class="text-danger"></span></label>
                                            <input type="text" id="contact_number_1" name="contact_number_1" maxlength="255" class="form-control" value="{{ $data->contact_number_1 }}" placeholder="Enter Contact Number 1" >
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('contact_number_1')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="contact_number_2">Contact Number 2 <span class="text-danger"></span></label>
                                            <input type="text" id="contact_number_2" name="contact_number_2" maxlength="255" class="form-control" value="{{ $data->contact_number_2 }}" placeholder="Enter Contact Number 2" >
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('contact_number_2')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="contact_number_3">Contact Number 3 <span class="text-danger"></span></label>
                                            <input type="text" id="contact_number_3" name="contact_number_3" maxlength="255" class="form-control" value="{{ $data->contact_number_3 }}" placeholder="Enter Contact Number 3" >
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('contact_number_3')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select name="status" id="status" class="form-control custom-select">
                                                <option value="active" {{ $data->status == 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ $data->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            </select>                                         
                                        </div>

                                        <div class="form-group">
                                            <label for="video_link">Video Link</label>
                                            <textarea id="video_link" name="video_link" class="form-control" cols="7" rows="5" placeholder="Enter Video Introduction/Description(If Available)">{{ $data->video_link }}</textarea>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('video_link')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="form-group">
                                            <label for="map">Map</label>
                                            <textarea id="map" name="map" class="form-control" placeholder="Enter Map Here">{{ $data->map }}</textarea>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('map')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="description">Description</label>
                                            <textarea id="description" name="description" class="form-control" placeholder="Enter Description Here">{{ $data->description }}</textarea>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('description')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                {{-- <div class="form-group">
                                    <label for="description">Full Description</label>
                                    <textarea id="description" name="description" class="form-control">{!! $data->description !!}</textarea>
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('short_description')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div> --}}

                                {{-- <div class="row">

                                    <div class="col-lg-9">
                                        <div class="form-group">
                                            <label for="tags">Tags (for search result)</label>
                                            <input type="text" name="tags" value="{{$data->tags}}" class="form-control" data-role="tagsinput">
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('tags')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select name="status" class="form-control" id="status" required>
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

                                </div> --}}


                            </div>
                        </div>


                        <div class="form-group text-center pt-3">
                            <a href="{{url('view/all/outlet')}}" style="width: 130px;" class="btn btn-danger d-inline-block text-white m-2" type="submit"><i class="mdi mdi-cancel"></i> Cancel</a>
                            <button class="btn btn-primary m-2" style="width: 130px;" type="submit"><i class="fas fa-save"></i> Update</button>
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

        @if($data->image && file_exists(public_path($data->image)))
            $(".dropify-preview").eq(0).css("display", "block");
            $(".dropify-clear").eq(0).css("display", "block");
            $(".dropify-filename-inner").eq(0).html("{{$data->image}}");
            $("span.dropify-render").eq(0).html("<img src='{{url($data->image)}}'>");
        @endif

    </script>
@endsection