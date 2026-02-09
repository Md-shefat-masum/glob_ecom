@extends('backend.master')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        
        <!-- Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Product Managements</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Products</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-box"></i> Products List
                            </h5>
                            <a href="{{ route('product-management.create') }}" class="btn btn-success">
                                <i class="fas fa-plus"></i> Create New Product
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="productsDataTable" class="table table-bordered table-hover table-striped nowrap" style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">SL</th>
                                        <th width="50">ID</th>
                                        <th width="80">Image</th>
                                        <th>Product Name</th>
                                        <th width="120">Price</th>
                                        {{-- <th width="100">Unit Price</th> --}}
                                        <th width="100">Stock</th>
                                        <th width="80">Status</th>
                                        <th width="150">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be loaded via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Unit Prices Modal -->
        <div class="modal fade" id="unitPricesModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-list"></i> Unit Prices - <span id="unitPriceProductName"></span>
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div id="unitPricesContent">
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Variant Stocks Modal -->
        <div class="modal fade" id="variantStocksModal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title">
                            <i class="fas fa-boxes"></i> Variant Stocks - <span id="variantStockProductName"></span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div id="variantStocksContent">
                            <div class="text-center py-4">
                                <div class="spinner-border text-warning" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Image Preview Modal -->
        <div class="modal fade" id="imagePreviewModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="imagePreviewTitle">Image Preview</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img id="imagePreviewImg" src="" class="img-fluid" style="max-height: 70vh;">
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@push('css')
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css" rel="stylesheet">
<style>
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 15px;
    }
    .badge-stock-low {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.6; }
    }
</style>
@endpush

@push('js')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>

<script>
// Initialize DataTable with server-side processing
const table = $('#productsDataTable').DataTable({
    processing: true,
    serverSide: true,
    pageLength: 25,
    lengthMenu: [15, 25, 50, 100],
    ajax: "{{ route('product-management.index') }}",
    columns: [
        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
        { data: 'id', name: 'id' },
        { data: 'image', name: 'image', orderable: false, searchable: false },
        { data: 'name', name: 'name' },
        { data: 'price', name: 'price' },
        // { data: 'unit_price', name: 'unit_price', orderable: false, searchable: false },
        { data: 'stock', name: 'stock', orderable: false, searchable: false },
        { data: 'status', name: 'status' },
        { data: 'action', name: 'action', orderable: false, searchable: false }
    ],
    order: [[1, 'desc']], // Order by ID descending
    language: {
        search: "_INPUT_",
        searchPlaceholder: "Search products...",
        lengthMenu: "Show _MENU_ products per page",
        info: "Showing _START_ to _END_ of _TOTAL_ products",
        emptyTable: "No products found",
        processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>'
    },
    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip'
});

// Apply filters
$('#applyFilters').click(function() {
    const formData = $('#filterForm').serialize();
    
    $.ajax({
        url: "{{ route('product-management.apply-filters') }}",
        type: 'POST',
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                table.ajax.reload();
                toastr.success('Filters applied successfully');
            }
        },
        error: function() {
            toastr.error('Error applying filters');
        }
    });
});

// Clear filters
$('#clearFilters').click(function() {
    $.ajax({
        url: "{{ route('product-management.clear-filters') }}",
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                $('#filterForm')[0].reset();
                table.ajax.reload();
                toastr.success('Filters cleared successfully');
            }
        },
        error: function() {
            toastr.error('Error clearing filters');
        }
    });
});


// Show image preview
$(document).on('click', '.product-image-preview', function() {
    const imageUrl = $(this).data('image-url');
    const productName = $(this).data('product-name');
    
    $('#imagePreviewTitle').text(productName);
    $('#imagePreviewImg').attr('src', imageUrl);
    $('#imagePreviewModal').modal('show');
});

// View Unit Prices
function view_unit_prices(button) {
    const productId = button.dataset.productId;
    const productName = button.dataset.productName;
    
    $('#unitPriceProductName').text(productName);
    $('#unitPricesContent').html(`
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `);
    
    $('#unitPricesModal').modal('show');
    
    // Load unit prices via AJAX
    $.ajax({
        url: `/product-management/${productId}/unit-prices`,
        type: 'GET',
        success: function(response) {
            if (response.success && response.data.length > 0) {
                let html = `
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Unit</th>
                                    <th>Value</th>
                                    <th>Label</th>
                                    <th>Price</th>
                                    <th>Discount Price</th>
                                    <th>Discount %</th>
                                    <th>Reward Points</th>
                                </tr>
                            </thead>
                            <tbody>
                `;
                
                response.data.forEach((item, index) => {
                    const discountPercent = item.discount_percent || 0;
                    html += `
                        <tr>
                            <td>${index + 1}</td>
                            <td><span class="badge bg-primary">${item.unit?.name || 'N/A'}</span></td>
                            <td><strong>${item.unit_value}</strong></td>
                            <td>${item.unit_label || '—'}</td>
                            <td><strong class="text-success">৳${parseFloat(item.price).toFixed(2)}</strong></td>
                            <td>
                                ${item.discount_price > 0 ? 
                                    `<span class="text-danger">৳${parseFloat(item.discount_price).toFixed(2)}</span>` : 
                                    '<span class="text-muted">—</span>'
                                }
                            </td>
                            <td>
                                ${discountPercent > 0 ? 
                                    `<span class="badge bg-warning text-dark">${discountPercent}%</span>` : 
                                    '<span class="text-muted">—</span>'
                                }
                            </td>
                            <td>${item.reward_points || 0}</td>
                        </tr>
                    `;
                });
                
                html += `
                            </tbody>
                        </table>
                    </div>
                    <div class="alert alert-info mb-0 mt-3">
                        <i class="fas fa-info-circle"></i> Total <strong>${response.data.length}</strong> unit price(s) available for this product.
                    </div>
                `;
                
                $('#unitPricesContent').html(html);
            } else {
                $('#unitPricesContent').html(`
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> No unit prices found for this product.
                    </div>
                `);
            }
        },
        error: function() {
            $('#unitPricesContent').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-times-circle"></i> Error loading unit prices. Please try again.
                </div>
            `);
        }
    });
}

// View Variant Stocks
function show_variant_stocks(button) {

    const productId = button.dataset.productId;
    const productName = button.dataset.productName;

    console.log(button, productId, productName);
    
    $('#variantStockProductName').text(productName);
    $('#variantStocksContent').html(`
        <div class="text-center py-4">
            <div class="spinner-border text-warning" role="status">
                <span class="visually-hidden"></span>
            </div>
        </div>
    `);
    
    $('#variantStocksModal').modal('show');
    
    // Load variant stocks via AJAX
    $.ajax({
        url: `/product-management/${productId}/variant-stocks`,
        type: 'GET',
        success: function(response) {
            if (response.success && response.data.length > 0) {
                let html = `
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-sm">
                            <thead class="table-warning">
                                <tr>
                                    <th width="60">Image</th>
                                    <th>Variant</th>
                                    <th>Attributes</th>
                                    <th>SKU</th>
                                    <th>Barcode</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                `;
                
                let totalStock = 0;
                
                response.data.forEach((variant, index) => {
                    totalStock += parseInt(variant.stock || 0);
                    
                    const stockBadge = variant.stock > (variant.low_stock_alert || 10) ? 
                        `<span class="badge bg-success">${variant.stock}</span>` : 
                        `<span class="badge bg-danger badge-stock-low">${variant.stock}</span>`;
                    
                    let attributesBadges = '';
                    if (variant.variant_values) {
                        const values = typeof variant.variant_values === 'string' ? 
                            JSON.parse(variant.variant_values) : variant.variant_values;
                        
                        for (const [key, value] of Object.entries(values)) {
                            attributesBadges += `<span class="badge bg-secondary me-1 mb-1">${value}</span>`;
                        }
                    }
                    
                    // Image URL is already transformed by controller (uses /media/load/{id})
                    const imageHtml = variant.image ? 
                        `<img src="${variant.image}" 
                              class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;" 
                              onerror="this.src='/assets/images/placeholder.png'; this.onerror=null;">` : 
                        `<div style="width: 50px; height: 50px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
                            <i class="fas fa-image text-muted"></i>
                         </div>`;
                    
                    html += `
                        <tr>
                            <td class="text-center">${imageHtml}</td>
                            <td><strong class="text-primary">${variant.combination_key}</strong></td>
                            <td>${attributesBadges}</td>
                            <td><code>${variant.sku || 'N/A'}</code></td>
                            <td><code>${variant.barcode || 'N/A'}</code></td>
                            <td>
                                ${variant.price ? 
                                    `<strong class="text-success">৳${parseFloat(variant.price).toFixed(2)}</strong>` : 
                                    '<span class="text-muted">Base Price</span>'
                                }
                                ${variant.discount_price > 0 ? 
                                    `<br><small class="text-danger">৳${parseFloat(variant.discount_price).toFixed(2)}</small>` : 
                                    ''
                                }
                            </td>
                            <td class="text-center">${stockBadge}</td>
                            <td>
                                ${variant.status == 1 || variant.status === true ? 
                                    '<span class="badge bg-success">Active</span>' : 
                                    '<span class="badge bg-secondary">Inactive</span>'
                                }
                            </td>
                        </tr>
                    `;
                });
                
                html += `
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="6" class="text-end">Total Stock:</th>
                                    <th class="text-center">
                                        <span class="badge bg-primary fs-6">${totalStock}</span>
                                    </th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="alert alert-success mb-0 mt-3">
                        <i class="fas fa-check-circle"></i> 
                        This product has <strong>${response.data.length}</strong> variant combination(s) with total stock of <strong>${totalStock}</strong> units.
                    </div>
                `;
                
                $('#variantStocksContent').html(html);
            } else {
                $('#variantStocksContent').html(`
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> No variant stocks found for this product.
                    </div>
                `);
            }
        },
        error: function() {
            $('#variantStocksContent').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-times-circle"></i> Error loading variant stocks. Please try again.
                </div>
            `);
        }
    });
}

// Delete product
$(document).on('click', '.delete-product', function() {
    const productId = $(this).data('id');
    
    if (confirm('Are you sure you want to delete this product?')) {
        $.ajax({
            url: `/product-management/delete/${productId}`,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    table.ajax.reload(null, false); // Reload without resetting page
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                toastr.error('Error deleting product');
            }
        });
    }
});
</script>
@endpush
@endsection

