@extends('backend.master')

@section('header_css')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <style>
        .form-card {
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            border: none;
        }
        .form-header {
            background: #5369f8;
            color: white;
            padding: 20px 25px;
            border-radius: 8px 8px 0 0;
        }
        .form-content {
            padding: 25px;
        }
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }
        .dropify-wrapper {
            border-radius: 6px;
        }
        .required {
            color: #dc3545;
        }
        .radio-group {
            display: flex;
            gap: 20px;
            align-items: center;
        }
        .form-check-label {
            margin-left: 5px;
            cursor: pointer;
        }
    </style>
@endsection

@section('page_title')
    Category
@endsection
@section('page_heading')
    Update Category
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card form-card">
                <div class="form-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="feather-edit"></i> Update Category</h4>
                            <p class="mb-0 opacity-75 small">Edit category information and settings</p>
                        </div>
                        <a href="{{ route('ViewAllCategory') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>

                <div class="form-content">
                    <form method="POST" action="{{ url('update/category') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" value="{{ $category->id }}">

                        <div class="row">
                            <!-- Category Name -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Category Name <span class="required">*</span></label>
                                <input type="text" name="name" class="form-control" 
                                    value="{{ $category->name }}" placeholder="e.g., Electronics, Fashion, Home & Living" required>
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">SEO Name</label>
                                <input type="text" name="seo_name" class="form-control" 
                                    value="{{ $category->seo_name }}" placeholder="e.g., best fashion brands in bangladesh">
                                @error('seo_name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Slug -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Slug</label>
                                <input type="text" name="slug" class="form-control" 
                                    value="{{ $category->slug }}" placeholder="category-slug" required>
                                @error('slug')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                             <!-- Images Side by Side -->
                             <div class="col-md-6 mb-3">
                                {{-- <input type="file" name="icon" class="dropify" data-height="120"
                                    data-max-file-size="1M" accept="image/*" />
                                <small class="text-muted">100x100px recommended</small> --}}
                                <div class="form-group">
                                    @include('backend.components.image_upload_v2', [
                                        'inputName' => 'icon',
                                        'label' => 'Category Icon',
                                        'required' => true,
                                        'width' => 100,
                                        'height' => 100,
                                        'maxWidth' => '150px',
                                        'previewHeight' => '100px',
                                        'directory' => 'category_icons',
                                        'value' => env('FILE_URL').'/'.$category->icon,
                                    ])
                                    <small class="text-muted">100x100px recommended</small>
                                </div>
                            </div>
                            <!-- Settings Side by Side -->
                            <div class="col-md-6 mb-3">
                                <div>
                                    <label class="form-label d-block">Featured Category</label>
                                    <div class="radio-group">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="featured" id="featured_yes" value="1" {{ $category->featured == 1 ? 'checked' : '' }}>
                                            <label class="form-check-label" for="featured_yes">
                                                Yes, Featured
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="featured" id="featured_no" value="0" {{ $category->featured == 0 ? 'checked' : '' }}>
                                            <label class="form-check-label" for="featured_no">
                                                Not Featured
                                            </label>
                                        </div>
                                    </div>
                                    @error('featured')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div>
                                    <label class="form-label d-block">Show on Navbar</label>
                                    <div class="radio-group">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="show_on_navbar" id="navbar_yes" value="1" {{ $category->show_on_navbar == 1 ? 'checked' : '' }}>
                                            <label class="form-check-label" for="navbar_yes">
                                                Yes, Show
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="show_on_navbar" id="navbar_no" value="0" {{ $category->show_on_navbar == 0 ? 'checked' : '' }}>
                                            <label class="form-check-label" for="navbar_no">
                                                Hide
                                            </label>
                                        </div>
                                    </div>
                                    @error('show_on_navbar')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Page Title <span class="required">*</span></label>
                                <input type="text" name="page_title" class="form-control" 
                                    placeholder="Best Fashion Brands in Bangladesh" value="{{ $category->page_title }}">
                                @error('page_title')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">short description</label>
                                <textarea name="short_description" id="short_description" class="form-control" rows="3" placeholder="Best Fashion Brands in Bangladesh">{{ $category->short_description }}</textarea>
                                @error('short_description')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">SEO meta description</label>
                                <textarea name="meta_description" id="meta_description" class="form-control" rows="3" placeholder="SEO optimized description (150-160 characters)" maxlength="160">{{ $category->meta_description }}</textarea>
                                @error('meta_description')
                                    <small class="text-danger">{{ $message }}</small> 
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Full Description</label>
                                <textarea name="description" id="description" class="form-control" rows="3">{{ $category->description }}</textarea>
                                @error('description')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                {{-- <label class="form-label">Category Banner</label>
                                <input type="file" name="banner_image" class="dropify" data-height="120"
                                    data-max-file-size="1M" accept="image/*" /> --}}
                                <div class="form-group">
                                    @include('backend.components.image_upload_v2', [
                                        'inputName' => 'banner_image',
                                        'label' => 'Category Banner',
                                        'required' => true,
                                        'width' => 1620,
                                        'height' => 375,
                                        'maxWidth' => '1620px',
                                        'previewHeight' => '375px',
                                        'directory' => 'category_banners',
                                        'value' => env('FILE_URL').'/'.$category->banner_image,
                                    ])
                                    <small class="text-muted">1620x375px recommended</small>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status <span class="required">*</span></label>
                                <select name="status" class="form-control" required>
                                    <option value="">Select Status</option>
                                    <option value="1" {{ $category->status == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ $category->status == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>   
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <a href="{{ route('ViewAllCategory') }}" class="btn btn-secondary">
                                <i class="feather-x"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="feather-check"></i> Update Category
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_js')
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script>
        $(document).ready(function() {

            // Form submission loading state
            $('form').on('submit', function() {
                $('button[type="submit"]').html('<i class="fas fa-spinner fa-spin"></i> Updating...').prop('disabled', true);
            });

            $('#description').summernote({
                placeholder: 'Write Description Here',
                tabsize: 2,
                height: 400
            });
        });
    </script>
@endsection