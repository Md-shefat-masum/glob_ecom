@extends('backend.master')

@section('header_css')
    <link href="{{url('assets')}}/css/jquery.datetimepicker.css" rel="stylesheet" type="text/css" />
    <style>
        .form-card {
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
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
        .form-group label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 8px;
        }
        .form-control:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
        }
        .datetimepicker {
            cursor: pointer;
        }
    </style>
@endsection

@section('page_title')
    Promotional Banner
@endsection
@section('page_heading')
    Add New Promotional Banner
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-10 col-xl-10 mx-auto">
            <div class="card form-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">
                            <i class="feather-award text-primary"></i> Create Promotional Banner
                        </h4>
                        <a href="{{ route('ViewAllPromotionalBanners')}}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>

                    <form class="needs-validation" method="POST" action="{{url('save/new/promotional/banner')}}">
                        @csrf

                        <!-- Image Section -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="feather-image"></i> Product Image
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        @include('backend.components.image_upload_v2', [
                                            'inputName' => 'product_image',
                                            'label' => 'Product Image',
                                            'required' => true,
                                            'width' => 284,
                                            'height' => 380,
                                            'maxWidth' => '284px',
                                            'previewHeight' => '380px',
                                            'directory' => 'promotional-banners'
                                        ])
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Content Section -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="feather-file-text"></i> Ad Content
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="title">Title <span class="text-danger">*</span></label>
                                        <input type="text" name="title" id="title" class="form-control" placeholder="Enter banner title" required/>
                                        <small class="form-text text-muted">Main heading text for the promotional banner</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Date & Time Section -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="feather-calendar"></i> Schedule
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="started_at">Start Date & Time</label>
                                        <input type="text" name="started_at" id="started_at" class="form-control datetimepicker" placeholder="Select start date & time"/>
                                        <small class="form-text text-muted">When the banner should start displaying</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="end_at">End Date & Time</label>
                                        <input type="text" name="end_at" id="end_at" class="form-control datetimepicker" placeholder="Select end date & time"/>
                                        <small class="form-text text-muted">When the banner should stop displaying</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="row mt-4">
                            <div class="col-12 text-center">
                                <button class="btn btn-primary btn-lg px-5" type="submit">
                                    <i class="feather-save"></i> Save Promotional Banner
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('footer_js')
    <script src="{{url('assets')}}/js/jquery.datetimepicker.full.min.js"></script>
    <script>
        $('.datetimepicker').datetimepicker({
            format: 'Y-m-d H:i:s',
            step: 15,
            theme: 'dark'
        });
    </script>
@endsection

