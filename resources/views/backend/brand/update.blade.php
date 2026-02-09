@extends('backend.master')

@section('header_css')
    <link href="{{url('assets')}}/plugins/dropify/dropify.min.css" rel="stylesheet" type="text/css" />
    <link href="{{url('assets')}}/plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />
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

        .select2-container--default .select2-selection--multiple .select2-selection__choice{
            background: #1B69D1;
            border-color: #1B69D1;
            color: white;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove{
            color: white;
        }
    </style>
@endsection

@section('page_title')
    Brand
@endsection
@section('page_heading')
    Update Brand
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-3">Brand Update Form</h4>
                        <a href="{{ route('ViewAllBrands')}}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>

                    <form class="needs-validation" method="POST" action="{{url('update/brand')}}" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="slug" value="{{$data->slug}}">
                        <input type="hidden" name="id" value="{{$data->id}}">

                        <div class="form-group row">
                            <label for="colFormLabel" class="col-sm-2 col-form-label">Name <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" name="name" class="form-control" value="{{$data->name}}" id="colFormLabel" placeholder="Brand Name" required>
                                <div class="invalid-feedback" style="display: block;">
                                    @error('name')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="colFormLabel" class="col-sm-2 col-form-label">Change Logo</label>
                            <div class="col-sm-10">
                                <input type="file" name="logo" class="dropify" data-height="100" data-max-file-size="1M" accept="image/*"/>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="colFormLabel" class="col-sm-2 col-form-label">Change Banner</label>
                            <div class="col-sm-10">
                                <input type="file" name="banner" class="dropify" data-height="200" data-max-file-size="1M" accept="image/*"/>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="categories" class="col-sm-2 col-form-label">Categories</label>
                            <div class="col-sm-10">
                                @php
                                    $categories = App\Models\Category::get();
                                @endphp
                                <select name="categories[]" data-toggle="select2" class="form-control" id="categories" multiple>
                                    @foreach ($categories as $category)
                                    <option value="{{$category->id}}" @if(str_contains($data->categories, $category->id)) selected @endif>{{$category->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="subcategories" class="col-sm-2 col-form-label">Subcategories</label>
                            <div class="col-sm-10">
                                @php
                                    $subcategories = App\Models\Subcategory::get();
                                @endphp
                                <select name="subcategories[]" data-toggle="select2" class="form-control" id="subcategories" multiple>
                                    @foreach ($subcategories as $subcategory)
                                    <option value="{{$subcategory->id}}" @if(str_contains($data->subcategories, $subcategory->id)) selected @endif>{{$subcategory->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="childcategories" class="col-sm-2 col-form-label">Childcategories</label>
                            <div class="col-sm-10">
                                @php
                                    $childcategories = App\Models\ChildCategory::get();
                                @endphp
                                <select name="childcategories[]" data-toggle="select2" class="form-control" id="childcategories" multiple>
                                    @foreach ($childcategories as $childcategory)
                                    <option value="{{$childcategory->id}}" @if(str_contains($data->childcategories, $childcategory->id)) selected @endif>{{$childcategory->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="colFormLabe0" class="col-sm-2 col-form-label">Feature Status</label>
                            <div class="col-sm-3">
                                <select name="featured" class="form-control" id="colFormLabe0" required>
                                    <option value="">Select One</option>
                                    <option value="1" @if($data->featured == 1) selected @endif>Featured</option>
                                    <option value="0" @if($data->featured == 0) selected @endif>Not Featured</option>
                                </select>
                                <div class="invalid-feedback" style="display: block;">
                                    @error('featured')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="colFormLabe0" class="col-sm-2 col-form-label">Status <span class="text-danger">*</span></label>
                            <div class="col-sm-3">
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

                        <div class="form-group row">
                            <label for="colFormLabe0" class="col-sm-2 col-form-label"></label>
                            <div class="col-sm-10">
                                <button class="btn btn-primary" type="submit">Update Brand Info</button>
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
    <script src="{{url('assets')}}/plugins/select2/select2.min.js"></script>
    <script>
        $('[data-toggle="select2"]').select2();

        @if($data->logo && file_exists(public_path($data->logo)))
            $(".dropify-preview").eq(0).css("display", "block");
            $(".dropify-clear").eq(0).css("display", "block");
            $(".dropify-filename-inner").eq(0).html("{{$data->logo}}");
            $("span.dropify-render").eq(0).html("<img src='{{url($data->logo)}}'>");
        @endif

        @if($data->banner && file_exists(public_path($data->banner)))
            $(".dropify-preview").eq(1).css("display", "block");
            $(".dropify-clear").eq(1).css("display", "block");
            $(".dropify-filename-inner").eq(1).html("{{$data->banner}}");
            $("span.dropify-render").eq(1).html("<img src='{{url($data->banner)}}'>");
        @endif
    </script>

@endsection
