@extends('backend.master')

@section('page_title')
    Subcategory
@endsection
@section('page_heading')
    Add New Subcategory
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card" style="max-width: 768px; margin: 0 auto;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-3">Subcategory Create Form</h4>
                        <a href="{{ route('ViewAllSubcategory')}}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>

                    <form class="needs-validation" method="POST" action="{{url('save/new/subcategory')}}" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group row">
                            <label for="colFormLabe0" class="col-12 col-form-label">Select Category <span class="text-danger">*</span></label>
                            <div class="col-sm-12">
                                <select name="category_id" class="form-control" id="colFormLabe0" required>
                                    @php
                                        echo App\Models\Category::getDropDownList('name');
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
                            <label for="colFormLabel" class="col-sm-12 col-form-label">Name <span class="text-danger">*</span></label>
                            <div class="col-sm-12">
                                <input type="text" name="name" class="form-control" id="colFormLabel" placeholder="Subcategory Title" required>
                                <div class="invalid-feedback" style="display: block;">
                                    @error('name')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-8">
                                @include('backend.components.image_upload_v2', [
                                    'inputName' => 'icon',
                                    'label' => 'Subcategory Icon',
                                    'required' => true,
                                    'width' => 100,
                                    'height' => 100,
                                    'maxWidth' => '150px',
                                    'previewHeight' => '100px',
                                    'directory' => 'subcategory_icons'
                                ])
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-8">
                                @include('backend.components.image_upload_v2', [
                                    'inputName' => 'image',
                                    'label' => 'Subcategory Image',
                                    'required' => true,
                                    'width' => 1620,
                                    'height' => 375,
                                    'maxWidth' => '150px',
                                    'previewHeight' => '100px',
                                    'directory' => 'subcategory_images'
                                ])
                            </div>
                        </div>

                        <div class="form-group">
                            <button class="btn btn-primary" type="submit">Save Subcategory</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
