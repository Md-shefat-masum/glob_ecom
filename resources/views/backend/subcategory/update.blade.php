@extends('backend.master')

@section('page_title')
    Subcategory
@endsection
@section('page_heading')
    Update Subcategory
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card" style="max-width: 768px; margin: 0 auto;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-3">Subcategory Update Form</h4>
                        <a href="{{ route('ViewAllSubcategory')}}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>

                    <form class="needs-validation" method="POST" action="{{url('update/subcategory')}}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" value="{{$subcategory->id}}">

                        <div class="form-group row">
                            <label for="colFormLabe0" class="col-12 col-form-label">Select Category <span class="text-danger">*</span></label>
                            <div class="col-sm-12">
                                <select name="category_id" class="form-control" id="colFormLabe0" required>
                                    @php
                                        echo App\Models\Category::getDropDownList('name', $subcategory->category_id);
                                    @endphp
                                </select>
                                <div class="invalid-feedback" style="display: block;">
                                    @error('category_id')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="colFormLabel" class="col-12 col-form-label">Name <span class="text-danger">*</span></label>
                            <div class="col-sm-12">
                                <input type="text" name="name" value="{{$subcategory->name}}" class="form-control" id="colFormLabel" placeholder="Subcategory Title" required>
                                <div class="invalid-feedback" style="display: block;">
                                    @error('name')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-8">
                                @php
                                    $imageUrl = '';
                                    if ($subcategory->icon) {
                                        $imageUrl = env('FILE_URL').'/'.$subcategory->icon;
                                    }
                                @endphp
                                @include('backend.components.image_upload_v2', [
                                    'inputName' => 'icon',
                                    'label' => 'Subcategory Icon',
                                    'required' => true,
                                    'width' => 100,
                                    'height' => 100,
                                    'maxWidth' => '150px',
                                    'previewHeight' => '100px',
                                    'directory' => 'subcategory_icons',
                                    'value' => $subcategory->icon ?? '',
                                    'imageUrl' => $imageUrl
                                ])
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-8">
                                @php
                                    $imageUrl = '';
                                    if ($subcategory->image) { 
                                        $imageUrl = env('FILE_URL').'/'.$subcategory->image;
                                    }
                                @endphp
                                @include('backend.components.image_upload_v2', [
                                    'inputName' => 'image',
                                    'label' => 'Subcategory Image',
                                    'required' => true,
                                    'width' => 1620,
                                    'height' => 375,
                                    'maxWidth' => '150px',
                                    'previewHeight' => '100px',
                                    'directory' => 'subcategory_images',
                                    'value' => $subcategory->image ?? '',
                                    'imageUrl' => $imageUrl
                                ])
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="slug" class="col-12 col-form-label">Slug <span class="text-danger">*</span></label>
                            <div class="col-sm-12">
                                <input type="text" name="slug" value="{{$subcategory->slug}}" class="form-control" id="slug" placeholder="Subcategory Slug" required>
                                <div class="invalid-feedback" style="display: block;">
                                    @error('slug')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="colFormLabe0" class="col-12 col-form-label">Status <span class="text-danger">*</span></label>
                            <div class="col-sm-12">
                                <select name="status" class="form-control" id="colFormLabe0" required>
                                    <option value="">Select One</option>
                                    <option value="1" @if($subcategory->status == 1) selected @endif>Active</option>
                                    <option value="0" @if($subcategory->status == 0) selected @endif>Inactive</option>
                                </select>
                                <div class="invalid-feedback" style="display: block;">
                                    @error('status')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="colFormLabe0" class="col-12 col-form-label"></label>
                            <div class="col-sm-12">
                                <button class="btn btn-primary" type="submit">Update Subcategory</button>
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
    <script>
        @if($subcategory->icon && file_exists(public_path($subcategory->icon)))
            $(".dropify-preview").eq(0).css("display", "block");
            $(".dropify-clear").eq(0).css("display", "block");
            $(".dropify-filename-inner").eq(0).html("{{$subcategory->icon}}");
            $("span.dropify-render").eq(0).html("<img src='{{url($subcategory->icon)}}'>");
        @endif

        @if($subcategory->image && file_exists(public_path($subcategory->image)))
            $(".dropify-preview").eq(1).css("display", "block");
            $(".dropify-clear").eq(1).css("display", "block");
            $(".dropify-filename-inner").eq(1).html("{{$subcategory->image}}");
            $("span.dropify-render").eq(1).html("<img src='{{url($subcategory->image)}}'>");
        @endif
    </script>
@endsection
