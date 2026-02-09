@extends('backend.master')

@section('header_css')
@endsection

@section('page_title')
    Slider
@endsection
@section('page_heading')
    Add New Slider
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-3">Slider Create Form</h4>
                        <a href="{{ route('ViewAllSliders')}}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>

                    <form class="needs-validation" method="POST" action="{{url('save/new/slider')}}">
                        @csrf

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    @include('backend.components.image_upload_v2', [
                                        'inputName' => 'slider_image',
                                        'label' => 'Slider Image',
                                        'required' => true,
                                        'width' => 1920,
                                        'height' => 800,
                                        'maxWidth' => '500px',
                                        'previewHeight' => '300px',
                                        'directory' => 'sliders'
                                    ])
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="sub_title">Sub Title</label>
                                    <input type="text" name="sub_title" id="sub_title" class="form-control" placeholder="Write Sub Title Here"/>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="title">Title</label>
                                    <input type="text" name="title" id="title" class="form-control" placeholder="Write Title Here"/>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <input type="text" name="description" id="description" class="form-control" placeholder="Write Description Here"/>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="text_position">Text Position</label>
                                    <select class="form-control" name="text_position" id="text_position">
                                        <option value="">Select Option</option>
                                        <option value="left">Left</option>
                                        <option value="right">Right</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="link">Slider Link</label>
                                    <input type="text" name="link" class="form-control" id="link" placeholder="https://">
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('link')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="btn_text">Button Text</label>
                                    <input type="text" name="btn_text" id="btn_text" class="form-control" placeholder="ex. New Collection"/>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="btn_link">Button link</label>
                                    <input type="text" name="btn_link" class="form-control" id="btn_link" placeholder="https://">
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-lg-12 text-center">
                                <div class="form-group">
                                    <button class="btn btn-primary" type="submit"><i class="feather-save"></i> Save Slider</button>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('footer_js')
@endsection
