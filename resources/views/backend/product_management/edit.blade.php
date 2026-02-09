@extends('backend.master')

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <!-- Page Title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Edit Product: {{ $product->name }}</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('product-management.index') }}">Products</a>
                                </li>
                                <li class="breadcrumb-item active">Edit</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vue App -->
            <div id="productEditApp">

                <!-- Loading State -->
                <div class="row" v-if="isLoading">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden"></span>
                                </div>
                                <p class="mt-3 text-muted">Loading product data...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Form (shown after loading) -->
                <div v-if="!isLoading">

                    <!-- Tab Navigation -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <ul class="nav nav-pills nav-justified mb-4" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link" :class="{ active: isActiveTab('basic_info') }"
                                                @click="switchTab('basic_info')" href="javascript:void(0)">
                                                <i class="fas fa-info-circle"></i> Basic Info
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" :class="{ active: isActiveTab('images') }"
                                                @click="switchTab('images')" href="javascript:void(0)">
                                                <i class="fas fa-images"></i> Images
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" :class="{ active: isActiveTab('content') }"
                                                @click="switchTab('content')" href="javascript:void(0)">
                                                <i class="fas fa-file-alt"></i> Content
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" :class="{ active: isActiveTab('pricing') }"
                                                @click="switchTab('pricing')" href="javascript:void(0)">
                                                <i class="fas fa-dollar-sign"></i> Pricing
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" :class="{ active: isActiveTab('variants') }"
                                                @click="switchTab('variants')" href="javascript:void(0)">
                                                <i class="fas fa-boxes"></i> Variants
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" :class="{ active: isActiveTab('filters') }"
                                                @click="switchTab('filters')" href="javascript:void(0)">
                                                <i class="fas fa-filter"></i> Filters
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" :class="{ active: isActiveTab('attributes') }"
                                                @click="switchTab('attributes')" href="javascript:void(0)">
                                                <i class="fas fa-tags"></i> Attributes
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" :class="{ active: isActiveTab('shipping') }"
                                                @click="switchTab('shipping')" href="javascript:void(0)">
                                                <i class="fas fa-shipping-fast"></i> Shipping
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" :class="{ active: isActiveTab('related') }"
                                                @click="switchTab('related')" href="javascript:void(0)">
                                                <i class="fas fa-random"></i> Related
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" :class="{ active: isActiveTab('notification') }"
                                                @click="switchTab('notification')" href="javascript:void(0)">
                                                <i class="fas fa-bell"></i> Notification
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" :class="{ active: isActiveTab('seo') }"
                                                @click="switchTab('seo')" href="javascript:void(0)">
                                                <i class="fas fa-search"></i> SEO
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" :class="{ active: isActiveTab('faq') }"
                                                @click="switchTab('faq')" href="javascript:void(0)">
                                                <i class="fas fa-question-circle"></i> FAQ
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab Content -->
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
                                    <p class="text-muted mb-0 small">Upload and manage product images</p>
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
                                        <small class="text-muted">Manage cross-sell, up-sell, and bundled add-ons</small>
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

                    <!-- Submit Buttons -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="{{ route('product-management.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left"></i> Cancel
                                        </a>

                                        <div class="d-flex gap-2">
                                            <!-- Resend button (shown on error) -->
                                            <button type="button" v-if="showResendButton" @click="resendSubmission"
                                                class="btn btn-warning" :disabled="isSubmitting">
                                                <i class="fas fa-redo"></i> Resend Data
                                            </button>

                                            <!-- Save button -->
                                            <button type="button" @click="updateProduct"
                                                class="btn btn-primary btn-lg px-5" :disabled="isSubmitting">
                                                <span v-if="!isSubmitting">
                                                    <i class="fas fa-save"></i> Update Product
                                                </span>
                                                <span v-else>
                                                    <span class="spinner-border spinner-border-sm me-2"
                                                        role="status"></span>
                                                    Updating...
                                                </span>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Error Summary -->
                                    <div v-if="errors && Object.keys(errors).length > 0 && Object.values(errors).some(err => Array.isArray(err) && err.length > 0)" class="alert alert-danger mt-3 mb-0">
                                        <strong><i class="fas fa-exclamation-triangle"></i> Please fix the following
                                            errors:</strong>
                                        <ul class="mb-0 mt-2">
                                            <li v-for="(error, field) in errors" :key="field" v-if="Array.isArray(error) && error.length > 0">
                                                @{{ error[0] }}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modals -->
                @include('backend.product_management.components.category_modal')
                @include('backend.product_management.components.subcategory_modal')
                @include('backend.product_management.components.child_category_modal')
                @include('backend.product_management.components.brand_modal')
                @include('backend.product_management.components.model_modal')
                @include('backend.product_management.components.unit_modal')
                @include('backend.product_management.components.flag_modal')

            </div>
            <!-- End Vue App -->

        </div>
    </div>

    @push('css')
        <!-- FilePond CSS -->
        <link href="{{ versioned_url('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet">
        <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
        <!-- Summernote CSS -->
        <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    @endpush

    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
        <script src="{{ versioned_url('assets/plugins/select2/select2.min.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="{{ versioned_asset('assets/js/vue.min.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

        <!-- Pass data to JS -->
        <script>
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
                productId: {{ $product->id }}, // Pass product ID for loading
                slugCheckRoute: "{{ route('product-management.check-slug') }}",
                slugBaseUrl: "{{ url('/') }}",
                productSearchRoute: "{{ route('product-management.search-products') }}"
            };
        </script>
        <script src="{{ versioned_asset('assets/js/product_edit_vue.js') }}"></script>
        <script>
            // Make productEditApp available globally
            var productEditApp;

            // Initialize after Vue mounts
            $(document).ready(function() {
                setTimeout(function() {
                    productEditApp = document.getElementById('productEditApp').__vue__;
                    
                    console.log('Edit page initialized, Vue app ready');
                    console.log('productEditApp:', productEditApp);

                    // DON'T initialize Select2 on page load - wait until data is loaded
                    // and elements are guaranteed to exist
                    
                    // Load product data immediately - it will initialize Select2 at the end
                    if (productEditApp && productEditApp.loadProductData) {
                        console.log('Loading product data...');
                        productEditApp.loadProductData();
                    }
                }, 500);
            });

            // Global function to initialize all Summernote editors
            function initializeSummernote() {
                // Short Description
                if ($('#shortDescriptionEditor').length && !$('#shortDescriptionEditor').hasClass('summernote-initialized')) {
                    $('#shortDescriptionEditor').summernote({
                        placeholder: 'Write a short description of your product (2-3 sentences)',
                        tabsize: 2,
                        height: 150,
                        toolbar: [
                            ['style', ['bold', 'italic', 'underline']],
                            ['para', ['ul', 'ol']],
                        ],
                        callbacks: {
                            onChange: function(contents) {
                                if (productEditApp && productEditApp.product) {
                                    productEditApp.product.short_description = contents;
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
                            ['color', ['color']],
                            ['para', ['ul', 'ol', 'paragraph']],
                            ['table', ['table']],
                            ['insert', ['link', 'picture']],
                            ['view', ['codeview']]
                        ],
                        callbacks: {
                            onChange: function(contents) {
                                if (productEditApp && productEditApp.product) {
                                    productEditApp.product.description = contents;
                                }
                            }
                        }
                    }).addClass('summernote-initialized');
                }

                // Specification
                if ($('#specificationEditor').length && !$('#specificationEditor').hasClass('summernote-initialized')) {
                    $('#specificationEditor').summernote({
                        placeholder: 'Write technical specifications here (e.g., dimensions, materials, features)',
                        tabsize: 2,
                        height: 250,
                        toolbar: [
                            ['font', ['bold', 'italic']],
                            ['para', ['ul', 'ol']],
                            ['table', ['table']],
                            ['insert', ['link']],
                            ['view', ['codeview']]
                        ],
                        callbacks: {
                            onChange: function(contents) {
                                if (productEditApp && productEditApp.product) {
                                    productEditApp.product.specification = contents;
                                }
                            }
                        }
                    }).addClass('summernote-initialized');
                }

                // Warranty Policy
                if ($('#warrantyPolicyEditor').length && !$('#warrantyPolicyEditor').hasClass('summernote-initialized')) {
                    $('#warrantyPolicyEditor').summernote({
                        placeholder: 'Write warranty information here',
                        tabsize: 2,
                        height: 200,
                        toolbar: [
                            ['font', ['bold', 'italic']],
                            ['para', ['ul', 'ol']],
                            ['insert', ['link']],
                            ['view', ['codeview']]
                        ],
                        callbacks: {
                            onChange: function(contents) {
                                if (productEditApp && productEditApp.product) {
                                    productEditApp.product.warranty_policy = contents;
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
                                if (productEditApp && productEditApp.product) {
                                    productEditApp.product.size_chart = contents;
                                }
                            }
                        }
                    }).addClass('summernote-initialized');
                }
            }
        </script>
    @endpush
@endsection
