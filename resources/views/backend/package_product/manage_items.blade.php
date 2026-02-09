@extends('backend.master')

@section('header_css')
    <link href="{{url('assets')}}/plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />
    <style>
        .select2-selection{
            height: 34px !important;
            border: 1px solid #ced4da !important;
        }
        .select2 {
            width: 100% !important;
        }
        .package-item-card {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            background: #f8f9fa;
        }
        .item-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }
        .item-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .item-details h6 {
            margin: 0;
            font-weight: 600;
        }
        .item-details p {
            margin: 5px 0 0 0;
            color: #6c757d;
            font-size: 0.9em;
        }
        .quantity-badge {
            background: #007bff;
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: 600;
        }
        .add-item-form {
            background: #ffffff;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
    </style>
@endsection

@section('page_title')
    Package Items
@endsection

@section('page_heading')
    Manage Package Items
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h4 class="card-title mb-1">{{ $package->name }}</h4>
                            <p class="text-muted">Package Price: ৳{{ number_format($package->price, 2) }}</p>
                        </div>
                        <div>
                            <a href="{{ url('package-products') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                            <a href="{{ url('package-products/' . $package->id . '/edit') }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit Package
                            </a>
                        </div>

                    </div>

                    <!-- Add New Item Form -->
                    <div class="add-item-form">
                        <h5 class="mb-3"><i class="fas fa-plus"></i> Add Item to Package</h5>
                        <form method="POST" action="{{ url('package-products/' . $package->id . '/add-item') }}">
                            @csrf
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="product_id">Select Product <span class="text-danger">*</span></label>
                                        <select name="product_id" id="product_id" class="form-control select2">
                                            <option value="">Choose Product...</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }} (৳{{ $product->price }})</option>
                                            @endforeach
                                        </select>
                                        @error('product_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label for="color_id">Color <span class="text-danger">*</span></label>
                                        <select name="color_id" id="color_id" class="form-control select2">
                                            <option value="">Any Color</option>
                                            @foreach($colors as $color)
                                                <option value="{{ $color->id }}">{{ $color->name }}</option>
                                            @endforeach
                                        </select>
                                         @error('color_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label for="size_id">Size <span class="text-danger">*</span></label>
                                        <select name="size_id" id="size_id" class="form-control select2">
                                            <option value="">Any Size</option>
                                            @foreach($sizes as $size)
                                                <option value="{{ $size->id }}">{{ $size->name }}</option>
                                            @endforeach
                                        </select>
                                         @error('size_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label for="quantity">Quantity <span class="text-danger">*</span></label>
                                        <input type="number" name="quantity" id="quantity" class="form-control" min="1" value="1">
                                        @error('quantity')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="fas fa-plus"></i> Add Item
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Package Items List -->
                    <div class="mt-4">
                        <h5 class="mb-3">
                            <i class="fas fa-list"></i> Package Items 
                            <span class="badge badge-info">{{ count($packageItems) }} items</span>
                        </h5>

                        @if(count($packageItems) > 0)
                            @foreach($packageItems as $item)
                                <div class="package-item-card">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="item-info">
                                            <img src="{{ $item->product->image ? url( $item->product->image) : url('demo_products/demo_product.png') }}" 
                                                 alt="{{ $item->product->name }}" class="item-image">
                                            <div class="item-details">
                                                <h6>{{ $item->product->name }}</h6>
                                                <p>
                                                    Price: ৳{{ number_format($item->product->price, 2) }}
                                                    @if($item->color)
                                                        | Color: {{ $item->color->name }}
                                                    @endif
                                                    @if($item->size)
                                                        | Size: {{ $item->size->name }}
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <div class="d-flex align-items-center">
                                            <span class="quantity-badge mr-2">{{ $item->quantity }}x</span>
                                            
                                            <div class="btn-group ">
                                                <button type="button" class="btn btn-sm btn-warning mr-2" data-toggle="modal" data-target="#editModal{{ $item->id }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="removeItem({{ $item->id }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1" role="dialog">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form method="POST" action="{{ url('package-products/' . $package->id . '/items/' . $item->id) }}">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Item: {{ $item->product->name }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal">
                                                        <span>&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Product</label>
                                                        <input type="text" class="form-control" value="{{ $item->product->name }}" readonly>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="edit_color_id_{{ $item->id }}">Color</label>
                                                                <select name="color_id" id="edit_color_id_{{ $item->id }}" class="form-control">
                                                                    <option value="">Any Color</option>
                                                                    @foreach($colors as $color)
                                                                        <option value="{{ $color->id }}" {{ $item->color_id == $color->id ? 'selected' : '' }}>
                                                                            {{ $color->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="edit_size_id_{{ $item->id }}">Size</label>
                                                                <select name="size_id" id="edit_size_id_{{ $item->id }}" class="form-control">
                                                                    <option value="">Any Size</option>
                                                                    @foreach($sizes as $size)
                                                                        <option value="{{ $size->id }}" {{ $item->size_id == $size->id ? 'selected' : '' }}>
                                                                            {{ $size->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="edit_quantity_{{ $item->id }}">Quantity</label>
                                                        <input type="number" name="quantity" id="edit_quantity_{{ $item->id }}" 
                                                               class="form-control" min="1" value="{{ $item->quantity }}" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Update Item</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No items added to this package yet</h5>
                                <p class="text-muted">Use the form above to add products to this package</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_js')
    <script src="{{url('assets')}}/plugins/select2/select2.min.js"></script>

    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.select2').select2({
            placeholder: 'Choose an option',
            allowClear: true
        });

        // Get product variants when product is selected
        $('#product_id').on('change', function() {
            var productId = $(this).val();
            if (productId) {
                $.ajax({
                    url: "{{ url('get-product-variants') }}/" + productId,
                    type: "GET",
                    success: function(response) {
                        // Update colors
                        $('#color_id').empty().append('<option value="">Any Color</option>');
                        $.each(response.colors, function(key, color) {
                            $('#color_id').append('<option value="' + color.id + '">' + color.name + '</option>');
                        });

                        // Update sizes
                        $('#size_id').empty().append('<option value="">Any Size</option>');
                        $.each(response.sizes, function(key, size) {
                            $('#size_id').append('<option value="' + size.id + '">' + size.name + '</option>');
                        });
                    }
                });
            } else {
                $('#color_id').empty().append('<option value="">Any Color</option>');
                $('#size_id').empty().append('<option value="">Any Size</option>');
            }
        });

        function removeItem(itemId) {
            if (confirm('Are you sure you want to remove this item from the package?')) {
                $.ajax({
                    url: "{{ url('package-products/' . $package->id . '/items') }}/" + itemId,
                    type: "DELETE",
                    success: function(response) {
                        location.reload();
                        toastr.success('Item removed from package successfully');
                    },
                    error: function() {
                        toastr.error('Failed to remove item');
                    }
                });
            }
        }
    </script>
@endsection
