@extends('backend.master')

@section('header_css')
    <link href="{{ url('assets') }}/plugins/dropify/dropify.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('page_title')
    Edit Attribute Value
@endsection

@section('page_heading')
    Edit Attribute Value
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12 col-xl-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-3">Edit Attribute Value</h4>
                    <a href="{{ route('ViewAllProductSizeValue') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>

                <form class="needs-validation p-4 shadow rounded bg-white" method="POST"
                      action="{{ url('update/product-size-value') }}" enctype="multipart/form-data" novalidate>
                    @csrf
                    @method('POST')

                    <h4 class="mb-4">Update Attribute Value</h4>

                    <div class="row g-3">

                        <input type="hidden" name="id" value="{{ $productSize->id }}">

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="size" class="form-label">Select Size</label>
                                <select name="size" id="size" class="form-control @error('size') is-invalid @enderror">
                                    {!! \App\Models\ProductSize::getDropDownList('name', old('size', $productSize->id)) !!}
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
                                    @foreach ($productSizeValues as $index => $sizeValue)
                                        <div class="input-group mb-2">
                                            <input type="text" name="name[]" class="form-control mx-2 @error('name.' . $index) is-invalid @enderror"
                                                   placeholder="Name" value="{{ old('name.' . $index, $sizeValue->name) }}">
                                            {{-- <input type="text" name="value[]" class="form-control mx-2 @error('value.' . $index) is-invalid @enderror"
                                                   placeholder="Value" value="{{ old('value.' . $index, $sizeValue->value) }}"> --}}
                                            @if ($loop->first)
                                                <button type="button" class="btn btn-success add-btn">+</button>
                                            @else
                                                <button type="button" class="btn btn-danger remove-btn">-</button>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                @error('name.*')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                {{-- @error('value.*')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror --}}
                            </div>
                        </div>

                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button class="btn btn-primary px-4" type="submit">Update</button>
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

   

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('name-value-container');

            // Function to create a new input group for name/value pairs
            function createInputGroup() {
                const inputGroup = document.createElement('div');
                inputGroup.classList.add('input-group', 'mb-2');
                inputGroup.innerHTML = `
                    <input type="text" name="name[]" class="form-control mx-2" placeholder="Name" value="">
                    <input type="text" name="value[]" class="form-control mx-2 d-none" placeholder="Value" value="" >
                    <button type="button" class="btn btn-danger remove-btn">-</button>
                `;
                return inputGroup;
            }

            // Event delegation: listen to clicks inside the container
            container.addEventListener('click', function (e) {
                // If the add button is clicked
                if (e.target.classList.contains('add-btn')) {
                    const newGroup = createInputGroup();
                    container.appendChild(newGroup);
                }

                // If the remove button is clicked, remove the group
                if (e.target.classList.contains('remove-btn')) {
                    e.target.parentElement.remove();
                }
            });
        });
    </script>

@endsection
