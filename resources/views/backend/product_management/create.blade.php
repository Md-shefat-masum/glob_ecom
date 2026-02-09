@extends('backend.master')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        
        <!-- Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Create New Product</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('product-management.index') }}">Products</a></li>
                            <li class="breadcrumb-item active">Create</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vue App -->
        <div id="productCreateApp">
            
            <!-- Restore Data Banner -->
            <div class="row" v-if="hasStoredData">
                <div class="col-12">
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <i class="fas fa-exclamation-triangle fa-lg me-2"></i>
                                <strong>Unsaved Data Found!</strong>
                                <p class="mb-0 mt-1">
                                    You have unsaved form data from 
                                    <strong v-if="lastSavedTime">@{{ new Date(lastSavedTime).toLocaleString() }}</strong>.
                                    Would you like to restore it?
                                </p>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" @click="restoreFromLocalStorage" class="btn btn-success btn-sm">
                                    <i class="fas fa-undo"></i> Restore Data
                                </button>
                                <button type="button" @click="discardStoredData" class="btn btn-outline-danger btn-sm">
                                    <i class="fas fa-trash"></i> Discard
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form @submit.prevent="submitProduct">
                
                <!-- Tab Navigation -->
                <div class="row">
                    <div class="col-12">
                        <div class="product-tab-navigation">
                            <div class="tab-nav-container">
                                <a class="tab-nav-item" 
                                    :class="{ active: isActiveTab('basic_info') }"
                                    @click="switchTab('basic_info')"
                                    href="javascript:void(0)">
                                    <i class="fas fa-info-circle"></i>
                                    <span class="tab-label">Basic Info</span>
                                </a>
                                <a class="tab-nav-item"
                                    :class="{ active: isActiveTab('images') }"
                                    @click="switchTab('images')"
                                    href="javascript:void(0)">
                                    <i class="fas fa-images"></i>
                                    <span class="tab-label">Images</span>
                                </a>
                                <a class="tab-nav-item"
                                    :class="{ active: isActiveTab('content') }"
                                    @click="switchTab('content')"
                                    href="javascript:void(0)">
                                    <i class="fas fa-file-alt"></i>
                                    <span class="tab-label">Content</span>
                                </a>
                                <a class="tab-nav-item"
                                    :class="{ active: isActiveTab('pricing') }"
                                    @click="switchTab('pricing')"
                                    href="javascript:void(0)">
                                    <i class="fas fa-dollar-sign"></i>
                                    <span class="tab-label">Pricing</span>
                                </a>
                                <a class="tab-nav-item"
                                    :class="{ active: isActiveTab('variants') }"
                                    @click="switchTab('variants')"
                                    href="javascript:void(0)">
                                    <i class="fas fa-boxes"></i>
                                    <span class="tab-label">Variants</span>
                                </a>
                                <a class="tab-nav-item"
                                    :class="{ active: isActiveTab('filters') }"
                                    @click="switchTab('filters')"
                                    href="javascript:void(0)">
                                    <i class="fas fa-filter"></i>
                                    <span class="tab-label">Filters</span>
                                </a>
                                <a class="tab-nav-item"
                                    :class="{ active: isActiveTab('attributes') }"
                                    @click="switchTab('attributes')"
                                    href="javascript:void(0)">
                                    <i class="fas fa-tags"></i>
                                    <span class="tab-label">Attributes</span>
                                </a>
                                <a class="tab-nav-item"
                                    :class="{ active: isActiveTab('shipping') }"
                                    @click="switchTab('shipping')"
                                    href="javascript:void(0)">
                                    <i class="fas fa-shipping-fast"></i>
                                    <span class="tab-label">Shipping</span>
                                </a>
                                <a class="tab-nav-item"
                                    :class="{ active: isActiveTab('related') }"
                                    @click="switchTab('related')"
                                    href="javascript:void(0)">
                                    <i class="fas fa-random"></i>
                                    <span class="tab-label">Related</span>
                                </a>
                                <a class="tab-nav-item"
                                    :class="{ active: isActiveTab('notification') }"
                                    @click="switchTab('notification')"
                                    href="javascript:void(0)">
                                    <i class="fas fa-bell"></i>
                                    <span class="tab-label">Notification</span>
                                </a>
                                <a class="tab-nav-item"
                                    :class="{ active: isActiveTab('seo') }"
                                    @click="switchTab('seo')"
                                    href="javascript:void(0)">
                                    <i class="fas fa-search"></i>
                                    <span class="tab-label">SEO</span>
                                </a>
                                <a class="tab-nav-item"
                                    :class="{ active: isActiveTab('faq') }"
                                    @click="switchTab('faq')"
                                    href="javascript:void(0)">
                                    <i class="fas fa-question-circle"></i>
                                    <span class="tab-label">FAQ</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Contents -->
                <div class="row">
                    <div class="col-12">
                        
                        <!-- Basic Info Tab -->
                        <div class="card" v-show="isActiveTab('basic_info')">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Basic Information</h5>
                            </div>
                            <div class="card-body">
                                @include('backend.product_management.tabs.basic_info')
                            </div>
                        </div>

                        <!-- Images Tab -->
                        <div class="card" v-show="isActiveTab('images')">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-images text-primary"></i> Product Images
                                </h5>
                                <p class="text-muted mb-0 small">Upload product images with FilePond lazy upload</p>
                            </div>
                            <div class="card-body">
                                @include('backend.product_management.tabs.images')
                            </div>
                        </div>

                        <!-- Content Tab -->
                        <div class="card" v-show="isActiveTab('content')">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Product Content & Descriptions</h5>
                            </div>
                            <div class="card-body">
                                @include('backend.product_management.tabs.content')
                            </div>
                        </div>

                        <!-- Pricing Tab -->
                        <div class="card" v-show="isActiveTab('pricing')">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Pricing & Stock</h5>
                            </div>
                            <div class="card-body">
                                @include('backend.product_management.tabs.pricing')
                            </div>
                        </div>

                        <!-- Variants Tab -->
                        <div class="card" v-show="isActiveTab('variants')">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Product Variants</h5>
                            </div>
                            <div class="card-body">
                                @include('backend.product_management.tabs.variants')
                            </div>
                        </div>

                        <!-- Filter Attributes Tab -->
                        <div class="card" v-show="isActiveTab('filters')">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Filter Attributes</h5>
                            </div>
                            <div class="card-body">
                                @include('backend.product_management.tabs.filter_attributes')
                            </div>
                        </div>

                        <!-- Attributes Tab -->
                        <div class="card" v-show="isActiveTab('attributes')">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Product Attributes</h5>
                            </div>
                            <div class="card-body">
                                @include('backend.product_management.tabs.attributes')
                            </div>
                        </div>

                        <!-- Shipping Tab -->
                        <div class="card" v-show="isActiveTab('shipping')">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Shipping & Tax Information</h5>
                            </div>
                            <div class="card-body">
                                @include('backend.product_management.tabs.shipping')
                            </div>
                        </div>

                        <!-- Related Products Tab -->
                        <div class="card" v-show="isActiveTab('related')">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <div>
                                    <h5 class="card-title mb-0">Suggested & Add-on Products</h5>
                                    <small class="text-muted">Configure cross-sell, up-sell, and bundled add-on products</small>
                                </div>
                            </div>
                            <div class="card-body">
                                @include('backend.product_management.tabs.related_products')
                            </div>
                        </div>

                        <!-- Notification Tab -->
                        <div class="card" v-show="isActiveTab('notification')">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <div>
                                    <h5 class="card-title mb-0">Notification Popup</h5>
                                    <small class="text-muted">Display a promotional popup when customers view this product</small>
                                </div>
                            </div>
                            <div class="card-body">
                                @include('backend.product_management.tabs.notification')
                            </div>
                        </div>

                        <!-- SEO Tab -->
                        <div class="card" v-show="isActiveTab('seo')">
                            <div class="card-header">
                                <h5 class="card-title mb-0">SEO & Meta Information</h5>
                            </div>
                            <div class="card-body">
                                @include('backend.product_management.tabs.seo')
                            </div>
                        </div>

                        <!-- FAQ Tab -->
                        <div class="card" v-show="isActiveTab('faq')">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Frequently Asked Questions</h5>
                            </div>
                            <div class="card-body">
                                @include('backend.product_management.tabs.faq')
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Error Banner with Resend Button -->
                <div class="row" v-if="showResendButton && lastSubmissionError">
                    <div class="col-12">
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle fa-2x text-danger me-3"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="alert-heading mb-2">
                                        <i class="fas fa-times-circle"></i> Submission Failed
                                    </h5>
                                    <p class="mb-2">
                                        <strong>Error:</strong> @{{ lastSubmissionError.message || 'Unknown error occurred' }}
                                    </p>
                                    <p class="mb-3 small" v-if="lastSubmissionError.error_details">
                                        <strong>Details:</strong> 
                                        @{{ lastSubmissionError.error_details.message }}
                                        <span class="text-muted">
                                            (in @{{ lastSubmissionError.error_details.file }} at line @{{ lastSubmissionError.error_details.line }})
                                        </span>
                                    </p>
                                    <hr>
                                    <div class="d-flex gap-2 align-items-center">
                                        <button type="button" @click="resendSubmission" 
                                            class="btn btn-warning" :disabled="isSubmitting">
                                            <i class="fas fa-redo"></i> Resend Data
                                        </button>
                                        <button type="button" @click="showResendButton = false" 
                                            class="btn btn-outline-secondary">
                                            <i class="fas fa-times"></i> Dismiss
                                        </button>
                                        <span class="badge bg-info ms-2" v-if="submissionCount > 0">
                                            Attempt @{{ submissionCount + 1 }}
                                        </span>
                                        <small class="text-muted ms-auto">
                                            <i class="fas fa-database"></i> Data backed up in browser storage
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <a href="{{ route('product-management.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left"></i> Back to List
                                        </a>
                                        <button type="button" v-if="hasStoredData" @click="restoreFromLocalStorage" 
                                            class="btn btn-warning ms-2">
                                            <i class="fas fa-history"></i> Restore Saved Data
                                        </button>
                                    </div>
                                    <div>
                                        <span class="badge bg-secondary me-3" v-if="submissionCount > 0" title="Number of submission attempts">
                                            <i class="fas fa-upload"></i> Attempts: @{{ submissionCount }}
                                        </span>
                                        <small class="text-muted me-3" v-if="lastSavedTime">
                                            <i class="fas fa-save"></i> 
                                            Auto-saved: @{{ new Date(lastSavedTime).toLocaleTimeString() }}
                                        </small>
                                        <button type="submit" class="btn btn-primary btn-lg" :disabled="isSubmitting">
                                            <i class="fas fa-save"></i>
                                            <span v-if="!isSubmitting"> Save Product</span>
                                            <span v-else>
                                                <span class="spinner-border spinner-border-sm me-1"></span> Saving...
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </form>

            <!-- Modals -->
            @include('backend.product_management.components.category_modal')
            @include('backend.product_management.components.subcategory_modal')
            @include('backend.product_management.components.child_category_modal')
            @include('backend.product_management.components.brand_modal')
            @include('backend.product_management.components.model_modal')
            @include('backend.product_management.components.unit_modal')
            @include('backend.product_management.components.flag_modal')

        </div>

    </div>
</div>

@push('header_css')
<link href="{{ versioned_url('assets/plugins/dropify/dropify.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ versioned_url('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">

<!-- FilePond CSS -->
<link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
<link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet" />
    
<style>
    .nav-pills .nav-link {
        border-radius: 0.25rem;
        transition: all 0.3s;
    }
    .nav-pills .nav-link.active {
        background-color: #0d6efd;
        color: white;
    }
    .variant-value-item {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        padding: 0.5rem;
        margin-bottom: 0.5rem;
    }
    .image-preview {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 0.25rem;
        border: 2px solid #dee2e6;
    }
    .gallery-preview-item {
        position: relative;
        display: inline-block;
        margin-right: 10px;
        margin-bottom: 10px;
    }
    .gallery-preview-item img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 0.25rem;
    }
    .gallery-preview-item .remove-btn {
        position: absolute;
        top: -8px;
        right: -8px;
        background: red;
        color: white;
        border: none;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
    
    /* Variant Sidebar Styles */
    .select2-container--default .select2-selection--multiple {
        min-height: 34px;
        border-color: #dee2e6;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
    }
    .badge-sm {
        font-size: 10px;
        padding: 2px 6px;
    }
    .gap-1 {
        gap: 0.25rem;
    }
    
    /* Sticky sidebar for variants */
    @media (min-width: 768px) {
        .variants-sidebar {
            position: sticky;
            top: 20px;
            max-height: calc(100vh - 100px);
            overflow-y: auto;
        }
    }
    
    /* Compact table styles */
    .table-sm th, .table-sm td {
        padding: 0.5rem 0.4rem;
        vertical-align: middle;
    }
    .form-control-sm {
        font-size: 0.875rem;
    }
    
    /* Auto-save indicator animation */
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    .auto-save-indicator {
        animation: pulse 2s infinite;
    }
    
    /* Images Tab Styles */
    .filepond--root {
        margin-bottom: 0;
    }
    
    .filepond--panel-root {
        background-color: #f8f9fa;
    }
    
    .filepond--drop-label {
        color: #6c757d;
    }
    
    /* Card hover effect for images tab */
    .card.h-100:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        transition: box-shadow 0.3s ease-in-out;
    }
    
    /* Variant Image Styles */
    .variant-image-wrapper {
        display: inline-block;
        position: relative;
    }
    
    .variant-image-preview {
        position: relative;
        display: inline-block;
    }
    
    .variant-image-preview img {
        transition: opacity 0.3s ease;
    }
    
    .variant-image-preview:hover img {
        opacity: 0.8;
    }
    
    .variant-image-preview .btn-danger {
        opacity: 0;
        transition: opacity 0.2s ease;
    }
    
    .variant-image-preview:hover .btn-danger {
        opacity: 1;
    }
    
    .variant-image-placeholder:hover {
        background-color: #e9ecef !important;
        border-color: #0d6efd !important;
    }
    
    .variant-image-placeholder i {
        font-size: 20px;
        transition: color 0.2s ease;
    }
    
    .variant-image-placeholder:hover i {
        color: #0d6efd !important;
    }
</style>
@endpush

@push('js')
<script src="{{ versioned_url('assets/plugins/select2/select2.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ versioned_asset('assets/js/vue.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script>
    // Pass data to Vue
    window.productData = {
        categories: @json($categories),
        brands: @json($brands),
        units: @json($units),
        colors: @json($colors),
        sizes: @json($sizes),
        flags: @json($flags),
        models: @json($models),
        variantGroups: @json($variantGroups),
        categoryVariantMap: @json($categoryVariantMap ?? []),
        slugCheckRoute: "{{ route('product-management.check-slug') }}",
        slugBaseUrl: "{{ env('APP_FRONTEND_URL') }}",
        productSearchRoute: "{{ route('product-management.search-products') }}"
    };
</script>
<script src="{{ versioned_asset('assets/js/product_create_vue.js') }}"></script>
<script>
// Make productCreateApp available globally
var productCreateApp;

// Initialize Summernote for all text editors
$(document).ready(function() {
    // Wait for Vue to mount and render
    setTimeout(function() {
        productCreateApp = document.getElementById('productCreateApp').__vue__;
        initializeSummernote();
    }, 800);
});

// Global function to initialize all Summernote editors
window.initializeSummernote = function() {
    // Short Description
    if ($('#shortDescriptionEditor').length && !$('#shortDescriptionEditor').hasClass('summernote-initialized')) {
        $('#shortDescriptionEditor').summernote({
            placeholder: 'Write a brief product description here (recommended 100-255 characters)',
            tabsize: 2,
            height: 150,
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol']],
                ['insert', ['link']],
                ['view', ['codeview']]
            ],
            callbacks: {
                onChange: function(contents) {
                    if (productCreateApp && productCreateApp.product) {
                        productCreateApp.product.short_description = contents;
                    }
                }
            }
        }).addClass('summernote-initialized');
    }

    // Full Description
    if ($('#descriptionEditor').length && !$('#descriptionEditor').hasClass('summernote-initialized')) {
        $('#descriptionEditor').summernote({
            placeholder: 'Write detailed product description here',
            tabsize: 2,
            height: 300,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            callbacks: {
                onChange: function(contents) {
                    if (productCreateApp && productCreateApp.product) {
                        productCreateApp.product.description = contents;
                    }
                }
            }
        }).addClass('summernote-initialized');
    }

    // Specification
    if ($('#specificationEditor').length && !$('#specificationEditor').hasClass('summernote-initialized')) {
        $('#specificationEditor').summernote({
            placeholder: 'Write product specifications here (use tables for organized data)',
            tabsize: 2,
            height: 300,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link']],
                ['view', ['codeview']]
            ],
            callbacks: {
                onChange: function(contents) {
                    if (productCreateApp && productCreateApp.product) {
                        productCreateApp.product.specification = contents;
                    }
                }
            }
        }).addClass('summernote-initialized');
    }

    // Warranty Policy
    if ($('#warrantyPolicyEditor').length && !$('#warrantyPolicyEditor').hasClass('summernote-initialized')) {
        $('#warrantyPolicyEditor').summernote({
            placeholder: 'Write warranty policy here',
            tabsize: 2,
            height: 250,
            toolbar: [
                ['font', ['bold', 'italic', 'underline']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link']],
                ['view', ['codeview']]
            ],
            callbacks: {
                onChange: function(contents) {
                    if (productCreateApp && productCreateApp.product) {
                        productCreateApp.product.warranty_policy = contents;
                    }
                }
            }
        }).addClass('summernote-initialized');
    }

    // Size Chart
    if ($('#sizeChartEditor').length && !$('#sizeChartEditor').hasClass('summernote-initialized')) {
        $('#sizeChartEditor').summernote({
            placeholder: 'Write size chart information here (use tables for size measurements)',
            tabsize: 2,
            height: 250,
            toolbar: [
                ['font', ['bold', 'italic']],
                ['para', ['ul', 'ol']],
                ['table', ['table']],
                ['view', ['codeview']]
            ],
            callbacks: {
                onChange: function(contents) {
                    if (productCreateApp && productCreateApp.product) {
                        productCreateApp.product.size_chart = contents;
                    }
                }
            }
        }).addClass('summernote-initialized');
    }
}

// ==================== FilePond Initialization (DEPRECATED - Now using custom upload) ====================
// FilePond is no longer used for product and gallery images
// Images are now uploaded using custom Vue.js methods with better UI/UX
// See product_create_vue.js for handleProductImageUpload() and handleGalleryImageUpload() methods

</script>
@endpush
@endsection

