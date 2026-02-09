@extends('backend.master')

@section('header_css')
    <link href="{{url('assets')}}/plugins/dropify/dropify.min.css" rel="stylesheet" type="text/css" />

@endsection

@section('page_title')
    Attribute Value
@endsection
@section('page_heading')
    View All Attribute Value
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-3">Attribute Value Create Form</h4>
                        <a href="{{ route('ViewAllProductSizeValue')}}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>

                    <form class="needs-validation p-4 shadow rounded bg-white" method="POST"
                        action="{{ url('/save/new/product-size-value') }}" enctype="multipart/form-data" novalidate>
                        @csrf

                        <h4 class="mb-4">Add New Attribute Value</h4>

                        <div class="row g-3">

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="size" class="form-label">Select Size</label>
                                    <select name="size" id="size" class="form-control @error('size') is-invalid @enderror">
                                        {!! \App\Models\ProductSize::getDropDownList('name', old('size')) !!}
                                    </select>
                                    @error('size')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>


                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="form-label">Name & Value</label>
                                    <div id="name-value-container">
                                        <div class="input-group mb-2">
                                            <input type="text" name="name[]" class="form-control mx-2 @error('name.0') is-invalid @enderror" placeholder="Name" value="{{ old('name.0') }}">
                                            <input type="text" name="value[]" class="form-control mx-2 @error('value.0') is-invalid @enderror" placeholder="Value" value="{{ old('value.0') }}">
                                            <button type="button" class="btn btn-success add-btn">+</button>
                                        </div>
                                    </div>
                                    @error('name.*')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    @error('value.*')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('name-value-container');
    
        function createInputGroup() {
            const inputGroup = document.createElement('div');
            inputGroup.classList.add('input-group', 'mb-2');
            inputGroup.innerHTML = `
                <input type="text" name="name[]" class="form-control mx-2" placeholder="Name" value="">
                <input type="text" name="value[]" class="form-control mx-2" placeholder="Value" value="">
                <button type="button" class="btn btn-danger remove-btn">-</button>
            `;
            return inputGroup;
        }
    
        container.addEventListener('click', function (e) {
            if (e.target.classList.contains('add-btn')) {
                const newGroup = createInputGroup();
                container.appendChild(newGroup);
            }
    
            if (e.target.classList.contains('remove-btn')) {
                e.target.parentElement.remove();
            }
        });
    });
    </script>
    
    
@endsection