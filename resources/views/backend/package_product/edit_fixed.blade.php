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

        .product-card-title .card-title::before {
            top: 13px
        }

        /* Package Item Card Styling */
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
        
        /* Stock Status Styling */
        .stock-issue {
            background-color: #ffebee !important;
            border-left: 4px solid #f44336 !important;
        }
        
        .stock-warning {
            background-color: #fff8e1 !important;
            border-left: 4px solid #ff9800 !important;
        }
        
        .stock-ok {
            background-color: #e8f5e8 !important;
            border-left: 4px solid #4caf50 !important;
        }
        
        /* Form Validation */
        .is-invalid {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
        }
        
        .invalid-feedback {
            display: block !important;
        }
        
        /* Stock Badge Styling */
        #available_stock.badge-danger {
            background-color: #dc3545;
        }
        
        #available_stock.badge-warning {
            background-color: #ffc107;
            color: #212529;
        }
        
        #available_stock.badge-success {
            background-color: #28a745;
        }
        
        /* Add Item Form Styling */
        .add-item-form.stock-error {
            border-color: #dc3545;
            background-color: #fff5f5;
        }
        
        .add-item-form.stock-warning {
            border-color: #ffc107;
            background-color: #fffbf0;
        }
        
        .add-item-form.stock-ok {
            border-color: #28a745;
            background-color: #f8fff9;
        }
        
        .add-item-form.duplicate-error {
            border-color: #6f42c1;
            background-color: #f8f4ff;
            animation: shake 0.5s ease-in-out;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
    </style>
@endsection

@section('page_title')
    Package Product
@endsection
@section('page_heading')
    Edit Package Product
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">

                    <form class="needs-validation" method="POST" action="{{ url('package-products/' . $product->id) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row border-bottom mb-4 pb-2">
                            <div class="col-lg-6 product-card-title">
                                <h4 class="card-title mb-3" style="font-size: 18px; padding-top: 12px;">Edit Package Product
                                </h4>
                            </div>
                            <div class="col-lg-6 text-right">
                                <a href="{{ url('package-products') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to List
                                </a>
                            </div>
                        </div>

                        <div class="row">
                             <div class="col-lg-3">
                                <div class="card">
                                    <div class="card-body">
                                         <h5 class="card-title">Package Image</h5>
                                        @if ($product->image)
                                            <div class="text-center mt-2">
                                                <img src="{{ url($product->image) }}" class="img-fluid"
                                                    style="max-height: 200px;">
                                                <p class="text-muted mt-1">Current Image</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Package Image</h5>
                                        <div class="form-group">
                                            <input type="file" name="image" class="dropify" data-height="200"
                                                data-max-file-size="1M" accept="image/*" />
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('image')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Basic Information</h5>

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label for="name">Package Name <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" id="name" name="name" maxlength="255"
                                                        class="form-control" placeholder="Enter Package Product Name Here"
                                                        value="{{ old('name', $product->name) }}">
                                                    <div class="invalid-feedback" style="display: block;">
                                                        @error('name')
                                                            {{ $message }}
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label for="price">Package Price <span
                                                            class="text-danger">*</span></label>
                                                    <input type="number" id="price" name="price" step="0.01"
                                                        min="0" class="form-control" placeholder="0.00"
                                                        value="{{ old('price', $product->price) }}" required>
                                                    <div class="invalid-feedback" style="display: block;">
                                                        @error('price')
                                                            {{ $message }}
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label for="discount_price">Discount Price</label>
                                                    <input type="number" id="discount_price" name="discount_price"
                                                        step="0.01" min="0" class="form-control"
                                                        placeholder="0.00"
                                                        value="{{ old('discount_price', $product->discount_price) }}">
                                                    <div class="invalid-feedback" style="display: block;">
                                                        @error('discount_price')
                                                            {{ $message }}
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label for="status">Status <span class="text-danger">*</span></label>
                                                    <select name="status" class="form-control" id="status" required>
                                                        <option value="1"
                                                            {{ old('status', $product->status) == '1' ? 'selected' : '' }}>
                                                            Active</option>
                                                        <option value="0"
                                                            {{ old('status', $product->status) == '0' ? 'selected' : '' }}>
                                                            Inactive</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-list"></i> Package Items
                                    <span class="badge badge-info">{{ count($packageItems) }} items</span>
                                </h5>

                                <!-- Add New Item Form -->
                                <div class="add-item-form">
                                    <h5 class="mb-3"><i class="fas fa-plus"></i> Add Item to Package</h5>
                                    <form method="POST"
                                        action="{{ url('package-products/' . $product->id . '/add-item') }}">
                                        @csrf
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <div class="form-group">
                                                    <label for="product_id">Select Product <span
                                                            class="text-danger">*</span></label>
                                                    <select name="product_id" id="product_id"
                                                        class="form-control select2">
                                                        <option value="">Choose Product...</option>
                                                        @foreach (App\Models\Product::where('is_package', false)->where('status', 1)->get() as $availableProduct)
                                                            <option value="{{ $availableProduct->id }}">
                                                                {{ $availableProduct->name }}
                                                                (৳{{ $availableProduct->price }})</option>
                                                        @endforeach
                                                    </select>
                                                    @error('product_id')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label for="color_id">Color</label>
                                                    <select name="color_id" id="color_id" class="form-control select2">
                                                        <option value="">Any Color</option>
                                                        @foreach ($colors as $color)
                                                            <option value="{{ $color->id }}">{{ $color->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('color_id')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label for="size_id">Size</label>
                                                    <select name="size_id" id="size_id" class="form-control select2">
                                                        <option value="">Any Size</option>
                                                        @foreach ($sizes as $size)
                                                            <option value="{{ $size->id }}">{{ $size->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('size_id')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-lg-1">
                                                <div class="form-group">
                                                    <label>Stock</label>
                                                    <div class="form-control-plaintext">
                                                        <span id="available_stock" class="badge badge-secondary">-</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label for="quantity">Quantity <span
                                                            class="text-danger">*</span></label>
                                                    <input type="number" name="quantity" id="quantity"
                                                        class="form-control" min="1" max="0" value="1">
                                                    @error('quantity')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="form-group">
                                                    <label>&nbsp;</label>
                                                    <button type="submit" class="btn btn-primary btn-block" id="add_item_btn" disabled>
                                                        <i class="fas fa-plus"></i> Add Item
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <!-- Package Items List -->
                                <div class="mt-4">
                                    @if (count($packageItems) > 0)
                                        @foreach ($packageItems as $item)
                                            <div class="package-item-card">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="item-info">
                                                        <img src="{{ $item->product->image ? url($item->product->image) : url('demo_products/demo_product.png') }}"
                                                            alt="{{ $item->product->name }}" class="item-image">
                                                        <div class="item-details">
                                                            <h6>{{ $item->product->name }}</h6>
                                                            <p>
                                                                Price: ৳{{ number_format($item->product->price, 2) }}
                                                                @if ($item->color)
                                                                    | Color: {{ $item->color->name }}
                                                                @endif
                                                                @if ($item->size)
                                                                    | Size: {{ $item->size->name }}
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="d-flex align-items-center">
                                                        <span class="quantity-badge mr-2">{{ $item->quantity }}x</span>

                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-sm btn-warning mr-2"
                                                                data-toggle="modal"
                                                                data-target="#editModal{{ $item->id }}">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-danger"
                                                                onclick="removeItem({{ $item->id }})">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Edit Modal -->
                                            <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1"
                                                role="dialog">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <form method="POST"
                                                            action="{{ url('package-products/' . $product->id . '/items/' . $item->id) }}">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Edit Item:
                                                                    {{ $item->product->name }}</h5>
                                                                <button type="button" class="close"
                                                                    data-dismiss="modal">
                                                                    <span>&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label>Product</label>
                                                                    <input type="text" class="form-control"
                                                                        value="{{ $item->product->name }}" readonly>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label
                                                                                for="edit_color_id_{{ $item->id }}">Color</label>
                                                                            <select name="color_id"
                                                                                id="edit_color_id_{{ $item->id }}"
                                                                                class="form-control">
                                                                                <option value="">Any Color</option>
                                                                                @foreach ($colors as $color)
                                                                                    <option value="{{ $color->id }}"
                                                                                        {{ $item->color_id == $color->id ? 'selected' : '' }}>
                                                                                        {{ $color->name }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label
                                                                                for="edit_size_id_{{ $item->id }}">Size</label>
                                                                            <select name="size_id"
                                                                                id="edit_size_id_{{ $item->id }}"
                                                                                class="form-control">
                                                                                <option value="">Any Size</option>
                                                                                @foreach ($sizes as $size)
                                                                                    <option value="{{ $size->id }}"
                                                                                        {{ $item->size_id == $size->id ? 'selected' : '' }}>
                                                                                        {{ $size->name }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label
                                                                        for="edit_quantity_{{ $item->id }}">Quantity</label>
                                                                    <input type="number" name="quantity"
                                                                        id="edit_quantity_{{ $item->id }}"
                                                                        class="form-control" min="1"
                                                                        value="{{ $item->quantity }}" required>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-primary">Update
                                                                    Item</button>
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

                        <div class="card mt-3">
                            <div class="card-body">
                                <h5 class="card-title">Package Description</h5>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="short_description">Short Description</label>
                                            <textarea id="short_description" name="short_description" maxlength="1000" class="form-control" rows="3"
                                                placeholder="Write Package Short Description Here">{{ old('short_description', $product->short_description) }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="description">Description</label>
                                            <textarea id="description" name="description" class="form-control" placeholder="Write Package Description Here">{{ old('description', $product->description) }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="tags">Tags</label>
                                            <input type="text" id="tags" name="tags" class="form-control"
                                                data-role="tagsinput" placeholder="Enter Tags"
                                                value="{{ old('tags', $product->tags) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-body">
                                <h5 class="card-title">SEO Information</h5>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="meta_title">Meta Title</label>
                                            <input type="text" id="meta_title" name="meta_title" maxlength="255"
                                                class="form-control" placeholder="Enter Meta Title"
                                                value="{{ old('meta_title', $product->meta_title) }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="meta_keywords">Meta Keywords</label>
                                            <input type="text" id="meta_keywords" name="meta_keywords"
                                                class="form-control" data-role="tagsinput"
                                                placeholder="Enter Meta Keywords"
                                                value="{{ old('meta_keywords', $product->meta_keywords) }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="meta_description">Meta Description</label>
                                            <textarea id="meta_description" name="meta_description" maxlength="500" class="form-control" rows="3"
                                                placeholder="Enter Meta Description">{{ old('meta_description', $product->meta_description) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group text-center pt-3">
                            <a href="{{ url('package-products') }}" style="width: 130px;"
                                class="btn btn-danger d-inline-block text-white m-2" type="submit"><i
                                    class="mdi mdi-cancel"></i> Cancel</a>
                            <button class="btn btn-primary m-2" style="width: 180px;" type="submit"><i
                                    class="fas fa-save"></i> Update Package</button>
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
        $('.select2').select2({
            placeholder: 'Choose an option',
            allowClear: true
        });

        $('#description').summernote({
            placeholder: 'Write Package Description Here',
            tabsize: 2,
            height: 300
        });

        // ===== COMPREHENSIVE STOCK MANAGEMENT =====
        let currentAvailableStock = 0;
        let selectedProductHasVariants = false;
        let selectedProductData = null;
        let duplicateCheckTimeout = null;

        // Get product variants when product is selected
        $('#product_id').on('change', function() {
            var productId = $(this).val();
            resetAddItemForm();
            
            if (productId) {
                $.ajax({
                    url: "{{ url('get-product-variants') }}/" + productId,
                    type: "GET",
                    success: function(response) {
                        selectedProductData = response;
                        selectedProductHasVariants = response.has_variants;
                        
                        // Update colors
                        $('#color_id').empty().append('<option value="">Any Color</option>');
                        if (response.colors && response.colors.length > 0) {
                            $.each(response.colors, function(key, color) {
                                $('#color_id').append('<option value="' + color.id + '">' + color.name + '</option>');
                            });
                        }

                        // Update sizes
                        $('#size_id').empty().append('<option value="">Any Size</option>');
                        if (response.sizes && response.sizes.length > 0) {
                            $.each(response.sizes, function(key, size) {
                                $('#size_id').append('<option value="' + size.id + '">' + size.name + '</option>');
                            });
                        }

                        // Show total stock if no variants or if product doesn't require variant selection
                        if (!response.has_variants) {
                            updateStockDisplay(response.total_stock);
                            currentAvailableStock = response.total_stock;
                            validateAddItemForm();
                        } else {
                            // For products with variants, show total stock but require variant selection
                            updateStockDisplay(response.total_stock, true);
                            $('#available_stock').text('Select variant');
                            $('#available_stock').removeClass('badge-success badge-warning badge-danger').addClass('badge-info');
                            currentAvailableStock = 0;
                            validateAddItemForm();
                        }
                    },
                    error: function() {
                        console.log('Error getting product variants');
                        toastr.error('Failed to get product information', 'Error');
                        resetAddItemForm();
                    }
                });
            } else {
                resetAddItemForm();
            }
        });

        // Handle color/size selection for stock checking
        $('#color_id, #size_id').on('change', function() {
            if ($('#product_id').val() && selectedProductHasVariants) {
                checkVariantStock();
            }
            // Always validate form when color/size changes (for duplicate checking)
            validateAddItemForm();
        });

        // Check stock for specific variant combination
        function checkVariantStock() {
            const productId = $('#product_id').val();
            const colorId = $('#color_id').val();
            const sizeId = $('#size_id').val();

            if (productId && (colorId || sizeId)) {
                $.ajax({
                    url: "{{ url('get-variant-stock') }}/" + productId,
                    type: "POST",
                    data: {
                        color_id: colorId,
                        size_id: sizeId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        const stock = response.stock || 0;
                        updateStockDisplay(stock);
                        currentAvailableStock = stock;
                        validateAddItemForm();
                    },
                    error: function() {
                        updateStockDisplay(0);
                        currentAvailableStock = 0;
                        validateAddItemForm();
                        toastr.error('Failed to get variant stock information', 'Error');
                    }
                });
            } else if (productId && selectedProductHasVariants && !colorId && !sizeId) {
                // Show total stock when no variant selected
                updateStockDisplay(selectedProductData.total_stock, true);
                $('#available_stock').text('Select variant');
                $('#available_stock').removeClass('badge-success badge-warning badge-danger').addClass('badge-info');
                currentAvailableStock = 0;
                validateAddItemForm();
            }
        }

        // Update stock display and styling
        function updateStockDisplay(stock, showVariantMessage = false) {
            const stockBadge = $('#available_stock');
            const addItemForm = $('.add-item-form');
            
            if (showVariantMessage) {
                stockBadge.text('Total: ' + stock);
                stockBadge.removeClass('badge-success badge-warning badge-danger').addClass('badge-info');
                addItemForm.removeClass('stock-error stock-warning stock-ok');
                return;
            }

            stockBadge.text(stock);
            stockBadge.removeClass('badge-success badge-warning badge-danger badge-info');
            addItemForm.removeClass('stock-error stock-warning stock-ok');

            if (stock === 0) {
                stockBadge.addClass('badge-danger');
                addItemForm.addClass('stock-error');
            } else if (stock <= 5) {
                stockBadge.addClass('badge-warning');
                addItemForm.addClass('stock-warning');
            } else {
                stockBadge.addClass('badge-success');
                addItemForm.addClass('stock-ok');
            }
        }

        // Reset add item form
        function resetAddItemForm() {
            currentAvailableStock = 0;
            selectedProductHasVariants = false;
            selectedProductData = null;
            
            $('#color_id').empty().append('<option value="">Any Color</option>');
            $('#size_id').empty().append('<option value="">Any Size</option>');
            $('#available_stock').text('-').removeClass('badge-success badge-warning badge-danger badge-info').addClass('badge-secondary');
            $('#quantity').val(1).attr('max', 0);
            $('.add-item-form').removeClass('stock-error stock-warning stock-ok duplicate-error');
            $('#product_id, #color_id, #size_id, #quantity').removeClass('is-invalid');
            
            validateAddItemForm();
        }

        // Validate quantity input
        $('#quantity').on('input', function() {
            const quantity = parseInt($(this).val()) || 0;
            const maxStock = currentAvailableStock;

            // Remove previous validation classes
            $(this).removeClass('is-invalid');

            if (quantity > maxStock) {
                $(this).val(maxStock);
                if (maxStock === 0) {
                    toastr.warning('This variant is out of stock', 'Stock Issue');
                } else {
                    toastr.warning(`Maximum available stock is ${maxStock}. Quantity adjusted.`, 'Stock Limited');
                }
            }

            if (quantity <= 0) {
                $(this).val(1);
                toastr.warning('Quantity must be at least 1', 'Invalid Quantity');
            }

            validateAddItemForm();
        });

        // Comprehensive form validation
        function validateAddItemForm() {
            const productId = $('#product_id').val();
            const colorId = $('#color_id').val();
            const sizeId = $('#size_id').val();
            const quantity = parseInt($('#quantity').val()) || 0;
            const addButton = $('#add_item_btn');
            
            let isValid = true;
            let errorMessage = '';

            // Check if product is selected
            if (!productId) {
                isValid = false;
                errorMessage = 'Please select a product';
            }
            // Check if product has variants and requires variant selection
            else if (selectedProductHasVariants && !colorId && !sizeId) {
                isValid = false;
                errorMessage = 'Please select color or size for this product';
            }
            // Check if stock is available
            else if (currentAvailableStock === 0) {
                isValid = false;
                errorMessage = 'This product/variant is out of stock';
            }
            // Check if quantity is valid
            else if (quantity <= 0 || quantity > currentAvailableStock) {
                isValid = false;
                errorMessage = 'Invalid quantity';
            }
            // Check for duplicate variant combination
            else if (checkForDuplicateVariantInExisting(productId, colorId, sizeId)) {
                isValid = false;
                const productName = $('#product_id option:selected').text().split(' (৳')[0];
                const colorName = colorId ? $('#color_id option:selected').text() : 'Any Color';
                const sizeName = sizeId ? $('#size_id option:selected').text() : 'Any Size';
                errorMessage = `This variant already exists: ${productName} (${colorName}, ${sizeName})`;
            }

            // Update button state and visual feedback
            if (isValid) {
                addButton.prop('disabled', false).removeClass('btn-secondary').addClass('btn-primary');
                $('#quantity').attr('max', currentAvailableStock);
                addButton.removeAttr('title');
                
                // Remove error styling from form elements
                $('#product_id, #color_id, #size_id, #quantity').removeClass('is-invalid');
                $('.add-item-form').removeClass('duplicate-error');
            } else {
                addButton.prop('disabled', true).removeClass('btn-primary').addClass('btn-secondary');
                addButton.attr('title', errorMessage);
                
                // Add visual feedback for different error types
                if (errorMessage.includes('variant already exists')) {
                    $('.add-item-form').addClass('duplicate-error');
                    // Show error on the relevant fields
                    if (productId) $('#product_id').addClass('is-invalid');
                    if (colorId) $('#color_id').addClass('is-invalid');
                    if (sizeId) $('#size_id').addClass('is-invalid');
                    
                    // Show toast notification for duplicate (with debounce)
                    clearTimeout(duplicateCheckTimeout);
                    duplicateCheckTimeout = setTimeout(function() {
                        toastr.warning(errorMessage, 'Duplicate Item');
                    }, 500);
                }
            }

            return isValid;
        }

        // Check for duplicate variant combinations in existing items
        function checkForDuplicateVariantInExisting(productId, colorId, sizeId) {
            let duplicateFound = false;
            const selectedProductName = $('#product_id option:selected').text().split(' (৳')[0];
            
            // Get existing package items data from the page
            $('.package-item-card').each(function() {
                const itemDetails = $(this).find('.item-details p').text();
                const existingProductName = $(this).find('.item-details h6').text().trim();
                
                // Check if this is the same product
                if (existingProductName === selectedProductName) {
                    // Extract existing color and size from the details text
                    let existingColorId = null;
                    let existingSizeId = null;
                    
                    // Parse color from "Color: ColorName" pattern
                    const colorMatch = itemDetails.match(/Color:\s*([^|]+)/);
                    if (colorMatch) {
                        const colorName = colorMatch[1].trim();
                        // Find color ID by name
                        $('#color_id option').each(function() {
                            if ($(this).text() === colorName && $(this).val()) {
                                existingColorId = $(this).val();
                            }
                        });
                    }
                    
                    // Parse size from "Size: SizeName" pattern
                    const sizeMatch = itemDetails.match(/Size:\s*([^|]+)/);
                    if (sizeMatch) {
                        const sizeName = sizeMatch[1].trim();
                        // Find size ID by name
                        $('#size_id option').each(function() {
                            if ($(this).text() === sizeName && $(this).val()) {
                                existingSizeId = $(this).val();
                            }
                        });
                    }
                    
                    // Check if this exact variant combination already exists
                    const sameColor = (colorId || '') === (existingColorId || '');
                    const sameSize = (sizeId || '') === (existingSizeId || '');
                    
                    // For products without variants, if the same product exists without color/size, it's duplicate
                    const isPlainProduct = !colorId && !sizeId && !existingColorId && !existingSizeId;
                    
                    if ((sameColor && sameSize) || isPlainProduct) {
                        duplicateFound = true;
                        return false; // Break the loop
                    }
                }
            });
            
            return duplicateFound;
        }

        // Remove item function with enhanced feedback
        function removeItem(itemId) {
            if (confirm('Are you sure you want to remove this item from the package?')) {
                $.ajax({
                    url: "{{ url('package-products/' . $product->id . '/items') }}/" + itemId,
                    type: "DELETE",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        location.reload();
                        toastr.success('Item removed from package successfully', 'Success');
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            toastr.error(xhr.responseJSON.message, 'Error');
                        } else {
                            toastr.error('Failed to remove item', 'Error');
                        }
                    }
                });
            }
        }

        // Enhanced form submission validation
        $('.add-item-form form').on('submit', function(e) {
            if (!validateAddItemForm()) {
                e.preventDefault();
                toastr.error('Please fix validation issues before adding the item', 'Validation Failed');
                return false;
            }

            // Show loading state
            $('#add_item_btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Adding...');
        });

        // Initialize form on page load
        $(document).ready(function() {
            resetAddItemForm();
            
            // Show any flash messages
            @if(session('success'))
                toastr.success('{{ session('success') }}', 'Success');
            @endif
            
            @if(session('error'))
                toastr.error('{{ session('error') }}', 'Error');
            @endif
        });

        // Main form validation (for package update) - only validate basic package info
        $('form.needs-validation').on('submit', function(e) {
            // Skip this validation for add item form
            if ($(this).find('#add_item_btn').length > 0) {
                return; // This is the add item form
            }
            
            // This is the main package update form
            console.log('Submitting main package form');
            toastr.info('Updating package product...', 'Processing');
        });
    </script>
@endsection
