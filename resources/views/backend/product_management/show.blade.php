@extends('backend.master')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        
        <!-- Page Title -->
        <div class="row no-print">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Product Details</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('product-management.index') }}">Products</a></li>
                            <li class="breadcrumb-item active">Details</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row mb-3 no-print">
            <div class="col-12">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('product-management.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                    <a href="{{ route('product-management.edit', $product->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit Product
                    </a>
                    <button onclick="window.print()" class="btn btn-info">
                        <i class="fas fa-print"></i> Print
                    </button>
                    <a href="{{ route('product-management.pdf', $product->id) }}" class="btn btn-danger" target="_blank">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </a>
                </div>
            </div>
        </div>

        <!-- Product Details Card -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        
                        <!-- Print Header -->
                        <div class="print-header text-center mb-4 d-none print-show">
                            @php
                                $generalInfo = DB::table('general_infos')->where('id', 1)->first();
                            @endphp
                            @if($generalInfo && $generalInfo->logo)
                                <img src="{{ asset($generalInfo->logo) }}" alt="Logo" style="max-height: 60px; margin-bottom: 10px;">
                            @endif
                            <h2 class="mb-0">{{ $generalInfo->company_name ?? 'Company Name' }}</h2>
                            <p class="text-muted mb-0">Product Information Document</p>
                            <hr>
                        </div>

                        <!-- Basic Information Section -->
                        <div class="product-section">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="section-title">
                                    <i class="fas fa-info-circle text-primary"></i> Basic Information
                                </h4>
                                <span class="badge {{ $product->status == 1 ? 'bg-success' : 'bg-danger' }} fs-6">
                                    {{ $product->status == 1 ? 'Active' : 'Inactive' }}
                                </span>
                            </div>

                            <div class="row">
                                <!-- Product Image -->
                                <div class="col-md-3 text-center mb-3">
                                    @if($product->image)
                                        <img src="{{ asset('/' . $product->image) }}" 
                                            alt="{{ $product->name }}" 
                                            class="img-fluid rounded border"
                                            style="max-height: 250px; object-fit: contain;">
                                    @else
                                        <div class="border rounded d-flex align-items-center justify-content-center" 
                                            style="height: 250px; background: #f8f9fa;">
                                            <i class="fas fa-image fa-4x text-muted"></i>
                                        </div>
                                    @endif
                                    
                                    <!-- Additional Images -->
                                    @if($product->multiple_images)
                                        @php
                                            $multipleImages = is_string($product->multiple_images) 
                                                ? json_decode($product->multiple_images, true) 
                                                : $product->multiple_images;
                                        @endphp
                                        @if(is_array($multipleImages) && count($multipleImages) > 0)
                                            <div class="mt-3 no-print">
                                                <h6 class="text-muted">Additional Images</h6>
                                                <div class="d-flex gap-2 flex-wrap justify-content-center">
                                                    @foreach($multipleImages as $image)
                                                        <img src="{{ asset('/' . $image) }}" 
                                                            alt="Product Image" 
                                                            class="img-thumbnail"
                                                            style="width: 60px; height: 60px; object-fit: cover; cursor: pointer;"
                                                            onclick="showImageModal('{{ asset('uploads/' . $image) }}')">
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                </div>

                                <!-- Product Details -->
                                <div class="col-md-9">
                                    <table class="table table-bordered detail-table">
                                        <tbody>
                                            <tr>
                                                <th width="200">Product Name</th>
                                                <td><strong class="fs-5">{{ $product->name }}</strong></td>
                                            </tr>
                                            <tr>
                                                <th>Product Code</th>
                                                <td><code class="fs-6">{{ $product->code ?? 'N/A' }}</code></td>
                                            </tr>
                                            <tr>
                                                <th>SKU</th>
                                                <td><code class="fs-6">{{ $product->sku ?? 'N/A' }}</code></td>
                                            </tr>
                                            <tr>
                                                <th>Barcode</th>
                                                <td><code class="fs-6">{{ $product->barcode ?? 'N/A' }}</code></td>
                                            </tr>
                                            <tr>
                                                <th>Category</th>
                                                <td>
                                                    <span class="badge bg-primary">{{ $product->category->name ?? 'N/A' }}</span>
                                                    @if($product->subcategory)
                                                        <i class="fas fa-arrow-right mx-1"></i>
                                                        <span class="badge bg-info">{{ $product->subcategory->name }}</span>
                                                    @endif
                                                    @if($product->childCategory)
                                                        <i class="fas fa-arrow-right mx-1"></i>
                                                        <span class="badge bg-secondary">{{ $product->childCategory->name }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Brand</th>
                                                <td>{{ $product->brand->name ?? 'N/A' }}</td>
                                            </tr>
                                            @if($product->model)
                                            <tr>
                                                <th>Model</th>
                                                <td>{{ $product->model->name ?? 'N/A' }}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <th>Unit</th>
                                                <td>{{ $product->unit->name ?? 'N/A' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Pricing & Stock Section -->
                        <div class="product-section">
                            <h4 class="section-title">
                                <i class="fas fa-tags text-success"></i> Pricing & Stock
                            </h4>

                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-bordered detail-table">
                                        <tbody>
                                            <tr>
                                                <th width="200">Regular Price</th>
                                                <td><strong class="text-success fs-5">৳{{ number_format($product->price, 2) }}</strong></td>
                                            </tr>
                                            @if($product->discount_price > 0)
                                            <tr>
                                                <th>Discount Price</th>
                                                <td>
                                                    <strong class="text-danger fs-5">৳{{ number_format($product->discount_price, 2) }}</strong>
                                                    @php
                                                        $discountPercent = (($product->price - $product->discount_price) / $product->price) * 100;
                                                    @endphp
                                                    <span class="badge bg-warning text-dark ms-2">{{ round($discountPercent) }}% OFF</span>
                                                </td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-bordered detail-table">
                                        <tbody>
                                            @if(!$product->has_variant)
                                            <tr>
                                                <th width="200">Stock Quantity</th>
                                                <td>
                                                    <span class="badge {{ $product->stock > ($product->low_stock ?? 10) ? 'bg-success' : 'bg-danger' }} fs-6">
                                                        {{ $product->stock }} {{ $product->unit->name ?? 'Units' }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @if($product->low_stock)
                                            <tr>
                                                <th>Low Stock Alert</th>
                                                <td><span class="badge bg-warning text-dark">{{ $product->low_stock }}</span></td>
                                            </tr>
                                            @endif
                                            @else
                                            <tr>
                                                <th width="200">Product Type</th>
                                                <td>
                                                    <span class="badge bg-primary fs-6">
                                                        <i class="fas fa-layer-group"></i> Variable Product
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Total Variants</th>
                                                <td><span class="badge bg-info fs-6">{{ $variantCombinations->count() }} Combinations</span></td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Product Description -->
                        @if($product->short_description || $product->description)
                        <div class="product-section page-break-before">
                            <h4 class="section-title">
                                <i class="fas fa-align-left text-info"></i> Product Description
                            </h4>
                            
                            @if($product->short_description)
                            <div class="mb-3">
                                <h6 class="text-muted">Short Description</h6>
                                <div class="description-content">
                                    {!! nl2br(e($product->short_description)) !!}
                                </div>
                            </div>
                            @endif
                            
                            @if($product->description)
                            <div>
                                <h6 class="text-muted">Full Description</h6>
                                <div class="description-content">
                                    {!! $product->description !!}
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif

                        <!-- Specifications -->
                        @if($product->specification)
                        <div class="product-section">
                            <h4 class="section-title">
                                <i class="fas fa-list-ul text-warning"></i> Specifications
                            </h4>
                            <div class="description-content">
                                {!! $product->specification !!}
                            </div>
                        </div>
                        @endif

                        <!-- Warranty Policy -->
                        @if($product->warrenty_policy)
                        <div class="product-section">
                            <h4 class="section-title">
                                <i class="fas fa-shield-alt text-danger"></i> Warranty Policy
                            </h4>
                            <div class="description-content">
                                {!! $product->warrenty_policy !!}
                            </div>
                        </div>
                        @endif

                        <!-- Unit Pricing -->
                        @if($unitPricing->count() > 0)
                        <div class="product-section page-break-before">
                            <h4 class="section-title">
                                <i class="fas fa-calculator text-primary"></i> Unit Pricing
                            </h4>
                            
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="50">#</th>
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
                                        @foreach($unitPricing as $index => $pricing)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td><span class="badge bg-primary">{{ $pricing->unit->name ?? 'N/A' }}</span></td>
                                            <td><strong>{{ $pricing->unit_value }}</strong></td>
                                            <td>{{ $pricing->unit_label ?? '—' }}</td>
                                            <td><strong class="text-success">৳{{ number_format($pricing->price, 2) }}</strong></td>
                                            <td>
                                                @if($pricing->discount_price > 0)
                                                    <span class="text-danger">৳{{ number_format($pricing->discount_price, 2) }}</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($pricing->discount_percent > 0)
                                                    <span class="badge bg-warning text-dark">{{ $pricing->discount_percent }}%</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>{{ $pricing->reward_points ?? 0 }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif

                        <!-- Variant Combinations -->
                        @if($product->has_variant && $variantCombinations->count() > 0)
                        <div class="product-section page-break-before">
                            <h4 class="section-title">
                                <i class="fas fa-boxes text-warning"></i> Product Variants
                            </h4>
                            
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover table-sm">
                                    <thead class="table-light">
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
                                        @php $totalStock = 0; @endphp
                                        @foreach($variantCombinations as $variant)
                                        @php $totalStock += $variant->stock; @endphp
                                        <tr>
                                            <td class="text-center">
                                                @if($variant->image)
                                                    <img src="{{ ('/uploads/' . $variant->image) }}" 
                                                        class="img-thumbnail" 
                                                        style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                    <div style="width: 50px; height: 50px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td><strong class="text-primary">{{ $variant->combination_key }}</strong></td>
                                            <td>
                                                @if($variant->variant_values)
                                                    @foreach($variant->variant_values as $key => $value)
                                                        <span class="badge bg-secondary me-1 mb-1">{{ $value }}</span>
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td><code>{{ $variant->sku ?? 'N/A' }}</code></td>
                                            <td><code>{{ $variant->barcode ?? 'N/A' }}</code></td>
                                            <td>
                                                @if($variant->price)
                                                    <strong class="text-success">৳{{ number_format($variant->price, 2) }}</strong>
                                                @else
                                                    <span class="text-muted">Base Price</span>
                                                @endif
                                                @if($variant->discount_price > 0)
                                                    <br><small class="text-danger">৳{{ number_format($variant->discount_price, 2) }}</small>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge {{ $variant->stock > ($variant->low_stock_alert ?? 10) ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $variant->stock }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $variant->status ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $variant->status ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="6" class="text-end">Total Stock:</th>
                                            <th class="text-center">
                                                <span class="badge bg-primary fs-6">{{ $totalStock }}</span>
                                            </th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        @endif

                        <!-- Filter Attributes -->
                        @if($filterAttributes->count() > 0)
                        <div class="product-section">
                            <h4 class="section-title">
                                <i class="fas fa-filter text-secondary"></i> Filter Attributes
                            </h4>
                            
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Attribute Name</th>
                                            <th>Attribute Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($filterAttributes as $index => $attribute)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td><strong>{{ $attribute->attribute_name }}</strong></td>
                                            <td>{{ $attribute->attribute_value }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif

                        <!-- Additional Information -->
                        <div class="product-section">
                            <h4 class="section-title">
                                <i class="fas fa-info text-secondary"></i> Additional Information
                            </h4>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-bordered detail-table">
                                        <tbody>
                                            @if($product->tags)
                                            <tr>
                                                <th width="200">Tags</th>
                                                <td>
                                                    @foreach(explode(',', $product->tags) as $tag)
                                                        <span class="badge bg-secondary me-1">{{ trim($tag) }}</span>
                                                    @endforeach
                                                </td>
                                            </tr>
                                            @endif
                                            @if($product->video_url)
                                            <tr>
                                                <th>Video URL</th>
                                                <td><a href="{{ $product->video_url }}" target="_blank">{{ $product->video_url }}</a></td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <th>Created Date</th>
                                                <td>{{ $product->created_at->format('d M Y, h:i A') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-bordered detail-table">
                                        <tbody>
                                            @if($product->meta_title)
                                            <tr>
                                                <th width="200">Meta Title</th>
                                                <td>{{ $product->meta_title }}</td>
                                            </tr>
                                            @endif
                                            @if($product->meta_keywords)
                                            <tr>
                                                <th>Meta Keywords</th>
                                                <td>{{ $product->meta_keywords }}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <th>Last Updated</th>
                                                <td>{{ $product->updated_at->format('d M Y, h:i A') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Print Footer -->
                        <div class="print-footer text-center mt-5 pt-3 border-top d-none print-show">
                            <p class="text-muted mb-0">
                                <small>
                                    Generated on {{ now()->format('d M Y, h:i A') }} | 
                                    Product ID: {{ $product->id }} | 
                                    {{ $generalInfo->company_name ?? 'Company Name' }}
                                </small>
                            </p>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Image Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid" style="max-height: 70vh;">
            </div>
        </div>
    </div>
</div>

@push('css')
<style>
    /* Print Styles */
    @media print {
        .no-print {
            display: none !important;
        }
        
        .print-show {
            display: block !important;
        }
        
        .card {
            box-shadow: none !important;
            border: none !important;
        }
        
        .page-content {
            margin: 0 !important;
            padding: 20px !important;
        }
        
        .container-fluid {
            padding: 0 !important;
        }
        
        body {
            background: white !important;
        }
        
        .page-break-before {
            page-break-before: always;
        }
        
        table {
            page-break-inside: auto;
        }
        
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        
        thead {
            display: table-header-group;
        }
        
        tfoot {
            display: table-footer-group;
        }
    }
    
    /* Screen Styles */
    .product-section {
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid #f0f0f0;
    }
    
    .product-section:last-child {
        border-bottom: none;
    }
    
    .section-title {
        color: #495057;
        font-weight: 600;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e9ecef;
    }
    
    .detail-table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
    
    .detail-table td {
        vertical-align: middle;
    }
    
    .description-content {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        line-height: 1.8;
    }
    
    .print-header,
    .print-footer {
        display: none;
    }
    
    @page {
        size: A4;
        margin: 2cm;
    }
</style>
@endpush

@push('js')
<script>
function showImageModal(imageUrl) {
    $('#modalImage').attr('src', imageUrl);
    $('#imageModal').modal('show');
}
</script>
@endpush

@endsection

