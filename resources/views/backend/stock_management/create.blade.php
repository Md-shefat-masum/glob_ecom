@extends('backend.master')

@section('title', 'Create Stock Adjustment')

@section('content')
<div class="container-fluid" id="stockAdjustmentApp">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">
                    <i class="fas fa-edit text-primary me-2"></i>
                    Create Stock Adjustment
                </h4>
                <div class="page-title-right">
                    <a href="{{ route('stock-adjustment.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Adjustment Form -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-box me-2"></i>
                        Stock Adjustment Details
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form @submit.prevent="submitAdjustment">
                        <div class="row">
                            <!-- Product Selection -->
                            <div class="col-md-12 mb-4">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-search me-1"></i>
                                    Search Product <span class="text-danger">*</span>
                                </label>
                                <select id="productSelect" class="form-control" style="width: 100%;">
                                    <option value="">Search by name, code, SKU, or barcode...</option>
                                </select>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Start typing to search for products
                                </small>
                            </div>
                        </div>

                        <!-- Product Info Card (shown when product selected) -->
                        <div v-if="selectedProduct" class="row mb-4">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-box fa-2x me-3"></i>
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1">@{{ selectedProduct.name }}</h5>
                                            <div class="d-flex gap-3">
                                                <span><strong>Code:</strong> @{{ selectedProduct.code }}</span>
                                                <span v-if="selectedProduct.sku"><strong>SKU:</strong> @{{ selectedProduct.sku }}</span>
                                                <span v-if="!selectedProduct.has_variants">
                                                    <strong>Current Stock:</strong> 
                                                    <span class="badge" :class="selectedProduct.stock > 0 ? 'bg-success' : 'bg-danger'">
                                                        @{{ selectedProduct.stock }}
                                                    </span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Adjustment Type -->
                        <div class="row" v-if="selectedProduct">
                            <div class="col-md-6 mb-4">
                                <div>
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-list me-1"></i>
                                        Adjustment Type <span class="text-danger">*</span>
                                    </label>
                                </div>
                                <div>
                                    <select v-model="adjustmentType" class="form-control" required>
                                        <option value="">Select Type</option>
                                        <option value="purchase">Purchase (Add Stock)</option>
                                        <option value="return">Return (Add Stock)</option>
                                        <option value="initial">Initial Stock (Add Stock)</option>
                                        <option value="manual add">Manual Add (Add Stock)</option>
                                        <option value="sales">Sales (Reduce Stock)</option>
                                        <option value="waste">Waste (Reduce Stock)</option>
                                        <option value="transfer">Transfer (Reduce Stock)</option>
                                    </select>
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Choose the reason for stock adjustment
                                </small>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-comment-alt me-1"></i>
                                    Description
                                </label>
                                <textarea v-model="description" class="form-control" rows="3" 
                                    placeholder="Enter description for this adjustment..."></textarea>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Optional: Add notes about this adjustment
                                </small>
                            </div>
                        </div>

                        <!-- Single Product Stock Adjustment -->
                        <div v-if="selectedProduct && !selectedProduct.has_variants && adjustmentType" class="row">
                            <div class="col-12 mb-4">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h6 class="mb-0">
                                            <i class="fas fa-calculator me-2"></i>
                                            Stock Quantity
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label class="form-label">Current Stock</label>
                                                <input type="text" :value="selectedProduct.stock" class="form-control form-control-lg" 
                                                    readonly style="background-color: #f8f9fa; font-weight: bold;">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Adjustment Quantity <span class="text-danger">*</span></label>
                                                <input type="number" v-model="singleQuantity" class="form-control form-control-lg" 
                                                    min="0" step="1" required placeholder="0">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">New Stock (Preview)</label>
                                                <input type="text" :value="calculateNewStock()" class="form-control form-control-lg" 
                                                    readonly style="background-color: #e7f3ff; font-weight: bold; color: #0066cc;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Variant Stock Adjustment -->
                        <div v-if="selectedProduct && selectedProduct.has_variants && adjustmentType" class="row">
                            <div class="col-12 mb-4">
                                <div class="card">
                                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            <i class="fas fa-th-list me-2"></i>
                                            Variant Stock Adjustment
                                        </h6>
                                        <button type="button" @click="setAllVariants" class="btn btn-sm btn-light">
                                            <i class="fas fa-check-double me-1"></i> Set All
                                        </button>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                            <table class="table table-hover mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th width="40">#</th>
                                                        <th>Variant</th>
                                                        <th width="150">Current Stock</th>
                                                        <th width="200">Adjustment Qty</th>
                                                        <th width="150">New Stock</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(variant, index) in selectedProduct.variants" :key="variant.id">
                                                        <td>@{{ index + 1 }}</td>
                                                        <td>
                                                            <div class="d-flex flex-column">
                                                                <strong class="text-primary">@{{ variant.combination_key }}</strong>
                                                                <div class="d-flex flex-wrap gap-1 mt-1">
                                                                    <span v-for="(value, key) in variant.variant_values" :key="key" 
                                                                        class="badge bg-secondary small">
                                                                        @{{ value }}
                                                                    </span>
                                                                </div>
                                                                <small v-if="variant.sku" class="text-muted">SKU: @{{ variant.sku }}</small>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="badge fs-6" 
                                                                :class="variant.present_stock > 0 ? 'bg-success' : 'bg-danger'">
                                                                @{{ variant.present_stock }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <input type="number" v-model.number="variant.adjustment_qty" 
                                                                class="form-control" min="0" step="1" placeholder="0">
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-info fs-6">
                                                                @{{ calculateVariantNewStock(variant) }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Common Input for Set All -->
                                <div class="card bg-light mt-3">
                                    <div class="card-body">
                                        <div class="row align-items-end">
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Common Quantity</label>
                                                <input type="number" v-model="commonVariantQty" class="form-control" 
                                                    min="0" step="1" placeholder="Enter quantity to apply to all variants">
                                            </div>
                                            <div class="col-md-6">
                                                <small class="text-muted">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    Enter a quantity above and click "Set All" to apply it to all variants
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="row" v-if="selectedProduct && adjustmentType">
                            <div class="col-12">
                                <div class="d-flex gap-2 justify-content-end">
                                    <button type="button" @click="resetForm" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i> Cancel
                                    </button>
                                    <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
                                        <i class="fas fa-save me-1"></i>
                                        <span v-if="!isSubmitting">Save Adjustment</span>
                                        <span v-else>
                                            <span class="spinner-border spinner-border-sm me-1"></span>
                                            Saving...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Help Text -->
                        <div class="row mt-4" v-if="!selectedProduct">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h5><i class="fas fa-info-circle me-2"></i>How to create a stock adjustment:</h5>
                                    <ol class="mb-0">
                                        <li>Search and select a product from the dropdown above</li>
                                        <li>Choose the adjustment type (purchase, sales, return, etc.)</li>
                                        <li>Enter the quantity to adjust</li>
                                        <li>Add a description (optional)</li>
                                        <li>Click "Save Adjustment" to complete</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<link href="{{url('assets')}}/plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />
@endpush

@push('js')
<script src="{{url('assets')}}/plugins/select2/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="{{ asset('assets/js/vue.min.js') }}"></script>
<script src="{{ asset('assets/js/stock_adjustment_vue.js') }}?v={{ env('APP_VERSION', time()) }}"></script>
@endpush

