@extends('backend.master')

@section('header_css')
    <link href="{{ url('assets') }}/plugins/dropify/dropify.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('assets') }}/plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />
    <style>
        .select2-selection {
            height: 34px !important;
            border: 1px solid #ced4da !important;
        }

        .select2 {
            width: 100% !important;
        }

        .bootstrap-tagsinput .badge {
            margin: 2px 2px !important;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background: #1B69D1;
            border-color: #1B69D1;
            color: white;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: white;
        }
    </style>
@endsection

@section('page_title')
    Brand
@endsection
@section('page_heading')
    Add New Brand
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-3">Brand Create Form</h4>
                        <a href="{{ route('ViewAllBrands')}}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>
                    
                    <form class="needs-validation" method="POST" action="{{ url('save/new/brand') }}"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="form-group row">
                            <label for="colFormLabel" class="col-sm-2 col-form-label">Name <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" name="name" class="form-control" id="colFormLabel"
                                    placeholder="Brand Name" required>
                                <div class="invalid-feedback" style="display: block;">
                                    @error('name')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="colFormLabel" class="col-sm-2 col-form-label">Brand Logo</label>
                            <div class="col-sm-10">
                                <input type="file" name="logo" class="dropify" data-height="100" data-max-file-size="1M"
                                    accept="image/*" />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="colFormLabel" class="col-sm-2 col-form-label">Brand Banner (545px*845px)</label>
                            <div class="col-sm-10">
                                <input type="file" name="banner" class="dropify" data-height="200" data-max-file-size="1M"
                                    accept="image/*" />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="categories" class="col-sm-2 col-form-label">Categories</label>
                            <div class="col-sm-10">
                                <select name="categories[]" data-toggle="select2" class="form-control" id="categories"
                                    multiple>
                                    @php
                                        echo App\Models\Category::getDropDownList('name');
                                    @endphp
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="subcategories" class="col-sm-2 col-form-label">Subcategories</label>
                            <div class="col-sm-10">
                                <select name="subcategories[]" data-toggle="select2" class="form-control" id="subcategories"
                                    multiple>
                                    @php
                                        echo App\Models\Subcategory::getDropDownList('name');
                                    @endphp
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="childcategories" class="col-sm-2 col-form-label">Childcategories</label>
                            <div class="col-sm-10">
                                <select name="childcategories[]" data-toggle="select2" class="form-control"
                                    id="childcategories" multiple>
                                    @php
                                        echo App\Models\ChildCategory::getDropDownList('name');
                                    @endphp
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <button class="btn btn-primary" type="submit">Save Brand</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('footer_js')
    <script src="{{ url('assets') }}/plugins/dropify/dropify.min.js"></script>
    <script src="{{ url('assets') }}/pages/fileuploads-demo.js"></script>
    <script src="{{ url('assets') }}/plugins/select2/select2.min.js"></script>
    <script>
        $('[data-toggle="select2"]').select2();
    </script>
@endsection