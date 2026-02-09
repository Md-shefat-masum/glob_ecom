@extends('backend.master')

@section('header_css')
    <link href="{{ url('assets') }}/plugins/dropify/dropify.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('assets') }}/plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('assets') }}/css/tagsinput.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
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
    </style>
@endsection

@section('page_title')
    Edit
@endsection
@section('page_heading')
    Edit Customer
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
  
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-3">Customer</h4>
                        <a href="{{ route('ViewAllCustomer')}}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>

                    <form class="needs-validation" method="POST" action="{{ url('update/customers') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="customer_id" value="{{ $data->id }}">
                        <div class="row">
                            <div class="col-lg-12">

                                <div class="row">

                                    <div class="col-lg-6">

                                        <div class="form-group">
                                            <label for="customer_category_id">Customer Category <span
                                                    class="text-danger">*</span></label>
                                            <select id="customer_category_id" name="customer_category_id"
                                                class="form-control">
                                                {{-- <option value="" disabled selected>Select Customer Category</option> --}}
                                                <option></option>
                                                @foreach ($customer_categories as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ $category->id == $data->customer_category_id ? 'selected' : '' }}>
                                                        {{ $category->title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('customer_category_id')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="customer_source_type_id">Customer Source Type<span
                                                    class="text-danger">*</span></label>
                                            <select id="customer_source_type_id" name="customer_source_type_id"
                                                class="form-control">
                                                <option></option>
                                                @foreach ($customer_source_types as $customer_source_type)
                                                    <option value="{{ $customer_source_type->id }}"
                                                        {{ $customer_source_type->id == $data->customer_source_type_id ? 'selected' : '' }}>
                                                        {{ $customer_source_type->title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('customer_source_type_id')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="reference_id">Reference By<span class="text-danger">*</span></label>
                                            <select id="reference_id" name="reference_id" class="form-control">
                                                <option></option>
                                                @foreach ($users as $reference)
                                                    <option value="{{ $reference->id }}"
                                                        {{ $reference->id == $data->reference_by ? 'selected' : '' }}>
                                                        {{ $reference->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('reference_id')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="name">Name <span class="text-danger">*</span></label>
                                            <input type="text" id="name" name="name" maxlength="255"
                                                class="form-control" value="{{ $data->name }}"
                                                placeholder="Enter name Here">
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('name')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="phone">Phone <span class="text-danger">*</span></label>
                                            <input type="text" id="phone" name="phone" maxlength="60"
                                                class="form-control" value="{{ $data->phone }}"
                                                placeholder="Enter phone number Here">
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('phone')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="email">Email <span class="text-danger">*</span></label>
                                            <input type="text" id="email" name="email" maxlength="100"
                                                class="form-control" value="{{ $data->email }}"
                                                placeholder="Enter email Here">
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('email')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="address">Address</label>
                                            <textarea id="address" name="address" class="form-control" placeholder="Enter Address Here">
                                                {{ $data->address }}
                                            </textarea>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('address')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select name="status" id="status" class="form-control custom-select">
                                                <option value="active" {{ $data->status == 'active' ? 'selected' : '' }}>
                                                    Active</option>
                                                <option value="inactive"
                                                    {{ $data->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="form-group text-center pt-3">
                            <a href="{{ url('view/all/customer') }}" style="width: 130px;"
                                class="btn btn-danger d-inline-block text-white m-2" type="submit"><i
                                    class="mdi mdi-cancel"></i> Cancel</a>
                            <button class="btn btn-primary m-2" style="width: 130px;" type="submit"><i
                                    class="fas fa-save"></i> Update</button>
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
    <script src="{{ url('assets') }}/js/tagsinput.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });




        $('#description').summernote({
            placeholder: 'Write Description Here',
            tabsize: 2,
            height: 350
        });

        @if ($data->image && file_exists(public_path($data->image)))
            $(".dropify-preview").eq(0).css("display", "block");
            $(".dropify-clear").eq(0).css("display", "block");
            $(".dropify-filename-inner").eq(0).html("{{ $data->image }}");
            $("span.dropify-render").eq(0).html("<img src='{{ url($data->image) }}'>");
        @endif
    </script>

    <script>
        $(document).ready(function() {
            $('#customer_category_id').select2({
                placeholder: 'Select Customer Category',
                allowClear: true,
                width: '100%'
            });

            $('#customer_source_type_id').select2({
                placeholder: 'Select Customer Source Type',
                allowClear: true,
                width: '100%'
            });

            $('#reference_id').select2({
                placeholder: 'Select Reference',
                allowClear: true,
                width: '100%'
            });
        });
    </script>

    <script>
        // Update product warehouse rooms based on selected product warehouse
        document.getElementById('product_warehouse_id').addEventListener('change', function() {
            var warehouseId = this.value;
            if (warehouseId) {
                // AJAX request to fetch related rooms
                fetch(`/get-warehouse-rooms/${warehouseId}`)
                    .then(response => response.json())
                    .then(data => {
                        var roomSelect = document.getElementById('product_warehouse_room_id');
                        roomSelect.innerHTML = '<option value="">Select Room</option>';

                        data.rooms.forEach(room => {
                            var option = document.createElement('option');
                            option.value = room.id;
                            option.textContent = room.title;
                            roomSelect.appendChild(option);
                        });
                    });
            }
        });
    </script>
@endsection
