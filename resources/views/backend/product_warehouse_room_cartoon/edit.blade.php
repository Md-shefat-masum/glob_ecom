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
    Product Warehouse Room Cartoon
@endsection
@section('page_heading')
    Edit Product Warehouse Room Cartoon
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-3">Update Form</h4>
                        <a href="{{ route('ViewAllProductWarehouseRoomCartoon')}}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>
                    <form class="needs-validation" method="POST"
                        action="{{ url('update/product-warehouse-room-cartoon') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="cartoon_id" value="{{ $data->id }}">
                        <div class="row">
                            <div class="col-lg-12">

                                <div class="row">

                                    <div class="col-lg-9">

                                        <!-- Product Warehouse Dropdown -->
                                        <div class="form-group">
                                            <label for="product_warehouse_id">Product Warehouse</label>
                                            <select id="product_warehouse_id" name="product_warehouse_id"
                                                class="form-control">
                                                <option value="">Select Warehouse</option>
                                                @foreach ($productWarehouses as $warehouse)
                                                    <option value="{{ $warehouse->id }}"
                                                        {{ $warehouse->id == $data->product_warehouse_id ? 'selected' : '' }}>
                                                        {{ $warehouse->title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Product Warehouse Room Dropdown -->
                                        <div class="form-group">
                                            <label for="product_warehouse_room_id">Product Warehouse Room</label>
                                            <select id="product_warehouse_room_id" name="product_warehouse_room_id"
                                                class="form-control">
                                                <option value="">Select Room</option>
                                                @foreach ($productWarehouseRooms as $room)
                                                    <option value="{{ $room->id }}"
                                                        {{ $room->id == $data->product_warehouse_room_id ? 'selected' : '' }}>
                                                        {{ $room->title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>


                                        <div class="form-group">
                                            <label for="title">Title <span class="text-danger">*</span></label>
                                            <input type="text" id="title" name="title" class="form-control"
                                                value="{{ $data->title }}" placeholder="Enter Product Title Here"
                                                required>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('title')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>



                                        {{-- <div class="form-group">
                                            <label for="code">Code <span class="text-danger">*</span></label>
                                            <input type="text" id="code" name="code" maxlength="255" class="form-control" placeholder="Enter Product Warehouse Code Here" value="{{ $data->code }}" >
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('code')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div> --}}


                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select name="status" id="status" class="form-control custom-select">
                                                <option value="active" {{ $data->status == 'active' ? 'selected' : '' }}>
                                                    Active</option>
                                                <option value="inactive"
                                                    {{ $data->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </div>


                                        <div class="form-group">
                                            <label for="description">Description</label>
                                            <textarea id="description" name="description" class="form-control" placeholder="Enter Description Here">{{ $data->description }}</textarea>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('description')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>


                        <div class="form-group text-center pt-3">
                            <a href="{{ route('ViewAllProductWarehouseRoomCartoon')}}" style="width: 130px;"
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

        $('[data-toggle="select2"]').select2();


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
