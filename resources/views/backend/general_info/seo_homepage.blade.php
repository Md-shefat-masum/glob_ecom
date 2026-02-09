@extends('backend.master')

@section('header_css')
    <link href="{{url('assets')}}/css/tagsinput.css" rel="stylesheet" type="text/css" />
    <style>
        .bootstrap-tagsinput .badge {
            margin: 2px 2px !important;
        }
        .nav-tabs .nav-link {
            border: 1px solid #dee2e6;
            border-bottom: none;
            border-radius: 0.25rem 0.25rem 0 0;
            color: #495057;
            font-weight: 500;
        }
        .nav-tabs .nav-link.active {
            background-color: #fff;
            border-color: #dee2e6 #dee2e6 #fff;
            color: #495057;
        }
        .nav-tabs .nav-link:hover {
            border-color: #e9ecef #e9ecef #dee2e6;
        }
        .tab-content {
            border: 1px solid #dee2e6;
            border-top: none;
            border-radius: 0 0 0.25rem 0.25rem;
            padding: 20px;
            background: #fff;
        }
        .form-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .form-section-title {
            font-size: 16px;
            font-weight: 600;
            color: #495057;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #dee2e6;
        }
    </style>
@endsection

@section('page_title')
    Website Config
@endsection
@section('page_heading')
    SEO for HomePage
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">
                            <i class="feather-search text-primary"></i> SEO Configuration
                        </h4>
                        <a href="{{url('/home')}}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>

                    <form class="needs-validation" method="POST" action="{{url('update/seo/homepage')}}">
                        @csrf

                        <!-- Tabs Navigation -->
                        <ul class="nav nav-tabs" id="seoTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="seo-tab" data-bs-toggle="tab" data-bs-target="#seo" type="button" role="tab" aria-controls="seo" aria-selected="true">
                                    <i class="feather-search"></i> SEO Settings
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="og-tab" data-bs-toggle="tab" data-bs-target="#og" type="button" role="tab" aria-controls="og" aria-selected="false">
                                    <i class="feather-share-2"></i> Open Graph
                                </button>
                            </li>
                        </ul>

                        <!-- Tabs Content -->
                        <div class="tab-content" id="seoTabsContent">
                            <!-- SEO Tab -->
                            <div class="tab-pane fade show active" id="seo" role="tabpanel" aria-labelledby="seo-tab">
                                <div class="form-section">
                                    <div class="form-section-title">
                                        <i class="feather-search"></i> Search Engine Optimization
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label for="meta_title">Meta Title <small class="text-muted">(Recommended: 50-60 characters)</small></label>
                                        <input type="text" id="meta_title" name="meta_title" value="{{$data->meta_title}}" class="form-control" placeholder="Enter Meta Title Here" maxlength="60">
                                        <small class="form-text text-muted">The title that appears in search engine results</small>
                                        <div class="invalid-feedback" style="display: block;">
                                            @error('meta_title')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="meta_keywords">Meta Keywords <small class="text-muted">(Comma separated)</small></label>
                                        <input type="text" id="meta_keywords" data-max-tags="30" data-role="tagsinput" name="meta_keywords" value="{{$data->meta_keywords}}" class="form-control" placeholder="keyword1, keyword2, keyword3">
                                        <small class="form-text text-muted">Relevant keywords for your homepage</small>
                                        <div class="invalid-feedback" style="display: block;">
                                            @error('meta_keywords')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="meta_description">Meta Description <small class="text-muted">(Recommended: 150-160 characters)</small></label>
                                        <textarea id="meta_description" name="meta_description" rows="4" class="form-control" placeholder="Write Meta Description Here" maxlength="160">{{$data->meta_description}}</textarea>
                                        <small class="form-text text-muted">A brief description that appears in search engine results</small>
                                        <div class="invalid-feedback" style="display: block;">
                                            @error('meta_description')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Open Graph Tab -->
                            <div class="tab-pane fade" id="og" role="tabpanel" aria-labelledby="og-tab">
                                <div class="form-section">
                                    <div class="form-section-title">
                                        <i class="feather-share-2"></i> Open Graph Settings
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label for="meta_og_title">Open Graph Title</label>
                                        <input type="text" id="meta_og_title" name="meta_og_title" value="{{$data->meta_og_title}}" class="form-control" placeholder="Enter Open Graph Title Here">
                                        <small class="form-text text-muted">Title displayed when sharing on social media</small>
                                        <div class="invalid-feedback" style="display: block;">
                                            @error('meta_og_title')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="meta_og_description">Open Graph Description</label>
                                        <textarea id="meta_og_description" name="meta_og_description" rows="4" class="form-control" placeholder="Write Open Graph Description Here">{{$data->meta_og_description ? $data->meta_og_description : ''}}</textarea>
                                        <small class="form-text text-muted">Description displayed when sharing on social media</small>
                                        <div class="invalid-feedback" style="display: block;">
                                            @error('meta_og_description')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="meta_og_image">Open Graph Image <small class="text-muted">(Recommended: 1200x630px)</small></label>
                                        @php
                                            $ogImageUrl = '';
                                            if ($data->meta_og_image) {
                                                if (str_starts_with($data->meta_og_image, 'http://') || str_starts_with($data->meta_og_image, 'https://')) {
                                                    $ogImageUrl = $data->meta_og_image;
                                                } else {
                                                    $baseUrl = env('FILE_URL', env('APP_URL', url('/')));
                                                    $baseUrl = rtrim($baseUrl, '/');
                                                    $imagePath = ltrim($data->meta_og_image, '/');
                                                    $ogImageUrl = $baseUrl . '/' . $imagePath;
                                                }
                                            }
                                        @endphp
                                        @include('backend.components.image_upload_v2', [
                                            'inputName' => 'meta_og_image',
                                            'label' => 'Open Graph Image',
                                            'required' => false,
                                            'width' => 1200,
                                            'height' => 630,
                                            'maxWidth' => '600px',
                                            'previewHeight' => '350px',
                                            'directory' => 'seo-images',
                                            'value' => $data->meta_og_image ?? '',
                                            'imageUrl' => $ogImageUrl
                                        ])
                                        <small class="form-text text-muted">Image displayed when sharing on Facebook, Twitter, LinkedIn, etc.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="form-group text-center pt-4 mt-4 border-top">
                            <a href="{{url('/home')}}" style="width: 130px;" class="btn btn-danger d-inline-block text-white m-2" type="button">
                                <i class="mdi mdi-cancel"></i> Cancel
                            </a>
                            <button class="btn btn-primary m-2" type="submit" style="width: 140px;">
                                <i class="fas fa-save"></i> Update SEO
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_js')
    <script src="{{url('assets')}}/js/tagsinput.js"></script>
    <script>
        // Bootstrap 5 tabs fallback (if using Bootstrap 4, remove data-bs- attributes)
        $(document).ready(function() {
            // If Bootstrap 5 is not available, use Bootstrap 4 syntax
            if (typeof bootstrap === 'undefined') {
            }
            $('.nav-tabs button').on('click', function(e) {
                e.preventDefault();
                var target = $(this).data('bs-target') || $(this).data('target');
                $('.nav-tabs .nav-link').removeClass('active');
                $('.tab-pane').removeClass('show active');
                $(this).addClass('active');
                $(target).addClass('show active');
            });
        });
    </script>
@endsection
