@extends('backend.master')

@section('header_css')
    <link href="{{url('assets')}}/plugins/dropify/dropify.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('page_title')
    Customer E-commerce
@endsection
@section('page_heading')
    View All Customer E-commerce
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-3">Customer E-commerce Create Form</h4>
                        <a href="{{ route('ViewAllCustomerEcommerce')}}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>

                    <form class="needs-validation" method="POST" action="{{url('/save/new/customer-ecommerce')}}"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="form-group row">
                            <label for="colFormLabel" class="col-sm-2 col-form-label">Name <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" name="name" class="form-control" id="colFormLabel"
                                    placeholder="Full Name" required>
                                <div class="invalid-feedback" style="display: block;">
                                    @error('name')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="colFormLabel" class="col-sm-2 col-form-label">Image</label>
                            <div class="col-sm-10">
                                <input type="file" name="image" class="dropify" data-height="100" data-max-file-size="1M"
                                    accept="image/*" />
                                <div class="invalid-feedback" style="display: block;">
                                    @error('image')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="colFormLabel" class="col-sm-2 col-form-label">Phone <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" name="phone" class="form-control" id="colFormLabel"
                                    placeholder="Enter your phone number" required>
                                <div class="invalid-feedback" style="display: block;">
                                    @error('phone')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="colFormLabel" class="col-sm-2 col-form-label">Email <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="email" name="email" class="form-control" id="colFormLabel"
                                    placeholder="Enter your email" required>
                                <div class="invalid-feedback" style="display: block;">
                                    @error('email')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="colFormLabel" class="col-sm-2 col-form-label">Address <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" name="address" class="form-control" id="colFormLabel"
                                    placeholder="Enter Your address" required>
                                <div class="invalid-feedback" style="display: block;">
                                    @error('address')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="colFormLabel" class="col-sm-2 col-form-label">Password <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="password" name="password" class="form-control" id="colFormLabel"
                                    placeholder="Enter Your password" required>
                                <div class="invalid-feedback" style="display: block;">
                                    @error('password')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                        </div>


                        <div class="form-group row pt-3">
                            <div class="col-sm-2"></div>
                            <div class="col-sm-10">
                                <button class="btn btn-primary" type="submit">Save</button>
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
        // var route_prefix = "/filemanager";
        // {!! \File::get(base_path('vendor/unisharp/laravel-filemanager/public/js/stand-alone-button.js')) !!}

        // $('#lfm').filemanager('image', {
        //     prefix: route_prefix
        // });

        // var loadFile = function(event) {
        //     var output = document.getElementById('output');
        //     output.src = URL.createObjectURL(event.target.files[0]);
        //     output.onload = function() {
        //         URL.revokeObjectURL(output.src) // free memory
        //     }
        // };
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const forms = document.querySelectorAll('form');

            forms.forEach(form => {
                form.addEventListener('submit', function () {
                    const submitBtn = form.querySelector('[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerText = 'Updating...';
                    }
                });
            });
        });
    </script>
@endsection