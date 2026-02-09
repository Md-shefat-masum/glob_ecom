@extends('backend.master')

@section('header_css')
    <link href="{{url('assets')}}/plugins/dropify/dropify.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('assets') }}/css/spectrum.min.css" rel="stylesheet" type="text/css" />

@endsection

@section('page_title')
    Product Color
@endsection
@section('page_heading')
    View All Product Color
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-3">Product Color Create Form</h4>
                        <a href="{{ route('ViewAllProductColor')}}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>

                    <form class="needs-validation p-4 shadow rounded bg-white" method="POST" action="{{ url('/save/new/product-color') }}" enctype="multipart/form-data" novalidate>
                        @csrf
                    
                        <h4 class="mb-4">Add New Product Color</h4>
                    
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}">
                                    @error('name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                    
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code" class="form-label">Color</label>
                                    <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}">
                                    @error('code')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    
                        <div class="d-flex justify-content-end mt-4">
                            <button class="btn btn-primary px-4" type="submit">Save</button>
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
     <script src="{{ url('assets') }}/js/spectrum.min.js"></script>
     <script>
 
         $("#code").spectrum({
             preferredFormat: 'hex',
         });
     </script>
@endsection