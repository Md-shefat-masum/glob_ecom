<div class="row">
    
    <!-- Basic Pricing Section -->
    <div class="col-12 mb-4">
        <h5 class="border-bottom pb-2 mb-3">Basic Pricing</h5>
        <div class="row">
            <div class="col-md-3 mb-3">
                <label class="form-label d-block">Regular Price <span class="text-danger">*</span></label>
                <input type="number" v-model="pricing.price" @input="calculateDiscountPercent" 
                    class="form-control" step="0.01" min="0" required>
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label d-block">Discount Price</label>
                <input type="number" v-model="pricing.discount_price" @input="calculateDiscountPercent" 
                    class="form-control" step="0.01" min="0">
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label d-block">Discount %</label>
                <input type="number" v-model="pricing.discount_percent" class="form-control" 
                    step="0.01" min="0" max="100" readonly>
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label d-block">Reward Points</label>
                <input type="number" v-model="pricing.reward_points" class="form-control" 
                    step="0.01" min="0">
            </div>
        </div>
    </div>

    <!-- Stock Management Note -->
    {{-- <div class="col-12 mb-4">
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Stock Management:</strong> Stock is now managed in the <strong>Stock</strong> tab. 
            <a href="#" @click.prevent="switchTab('variants')" class="alert-link">Go to Stock tab →</a>
        </div>
    </div> --}}

    <!-- Unit-Based Pricing Section -->
    {{-- <div class="col-12">
        <!-- Unit Pricing Toggle Card -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-3">
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-grow-1">
                        <h5 class="mb-1">
                            <i class="fa fa-boxes text-primary me-1"></i>
                            Unit-Based Pricing
                        </h5>
                        <p class="text-muted small mb-0">
                            <i class="fas fa-info-circle me-1"></i>
                            Create different pricing for different units (e.g., 1 pc, 12 pc box, 144 pc carton)
                        </p>
                    </div>
                </div>
                
                <!-- Toggle Switch - Directly Below Title -->
                <div class="border-top pt-3 mt-2">
                    <div class="d-flex align-items-center">
                        <label class="form-label mb-0 me-3 fw-semibold" for="hasUnitBasedPriceCheck" style="cursor: pointer; font-size: 1rem;">
                            <span v-if="pricing.has_unit_based_price" class="text-success">
                                <i class="fas fa-toggle-on me-2"></i>Enabled
                            </span>
                            <span v-else class="text-muted">
                                <i class="fas fa-toggle-off me-2"></i>Disabled
                            </span>
                        </label>
                        <div class="form-check form-switch mb-0" style="transform: scale(1.4); transform-origin: left center;">
                            <input type="checkbox" v-model="pricing.has_unit_based_price" 
                                class="form-check-input d-none" id="hasUnitBasedPriceCheck" 
                                :true-value="1" :false-value="0"
                                style="cursor: pointer;">
                        </div>
                        <small class="text-muted ms-3" style="font-size: 0.875rem;">
                            <i class="fas fa-info-circle me-1"></i>
                            Toggle to enable/disable unit-based pricing
                        </small>
                    </div>
                </div>
                
                <!-- Quick Info Badge -->
                <div class="mt-3" v-if="pricing.has_unit_based_price">
                    <div class="alert alert-success mb-0 py-2">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>Unit pricing is active!</strong> You can now define multiple price tiers based on different unit quantities.
                    </div>
                </div>
            </div>
        </div>
        
        <div v-if="pricing.has_unit_based_price">
            <!-- Add Unit Price Form -->
            <div class="card bg-light mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">
                            <i class="fas fa-plus-circle text-primary"></i> Add Unit Price
                        </h6>
                        <button type="button" @click="clearUnitPriceForm" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-eraser"></i> Clear Form
                        </button>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <label class="form-label d-block">Unit <span class="text-danger">*</span></label>
                            <select v-model="newUnitPrice.unit_id" id="unitPricingSelect" class="form-select form-control">
                                <option value="">Select Unit</option>
                                <option v-for="unit in units" :key="unit.id" :value="unit.id">
                                    @{{ unit.name }}
                                </option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label d-block">Unit Value <span class="text-danger">*</span></label>
                            <input type="number" v-model="newUnitPrice.unit_value" 
                                class="form-control" step="0.01" min="0" placeholder="e.g., 10">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label d-block">Unit Label</label>
                            <input type="text" v-model="newUnitPrice.unit_label" 
                                class="form-control" placeholder="e.g., 10 pcs">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label d-block">Price <span class="text-danger">*</span></label>
                            <input type="number" v-model="newUnitPrice.price" 
                                class="form-control" step="0.01" min="0" placeholder="100.00">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label d-block">Discount Price</label>
                            <input type="number" v-model="newUnitPrice.discount_price" 
                                class="form-control" step="0.01" min="0" placeholder="90.00">
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" @click="addUnitPrice" class="btn btn-primary w-100">
                                <i class="fas fa-plus"></i> Add
                            </button>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> 
                            <strong>Tip:</strong> Form will stay filled after adding so you can quickly add multiple entries for the same unit (e.g., pc = 10, 20, 30, etc.)
                        </small>
                    </div>
                </div>
            </div>

            <!-- Unit Pricing List -->
            <div v-if="unitPricing.length > 0" class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="60">#</th>
                            <th>Unit</th>
                            <th>Value</th>
                            <th>Label</th>
                            <th>Price</th>
                            <th>Discount Price</th>
                            <th>Discount %</th>
                            <th width="100" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(unitPrice, index) in unitPricing" :key="index">
                            <td>@{{ index + 1 }}</td>
                            <td>
                                <span class="badge bg-primary">@{{ getUnitName(unitPrice.unit_id) }}</span>
                            </td>
                            <td><strong>@{{ unitPrice.unit_value }}</strong></td>
                            <td>@{{ unitPrice.unit_label || '-' }}</td>
                            <td><strong class="text-success">৳@{{ parseFloat(unitPrice.price).toFixed(2) }}</strong></td>
                            <td>
                                <span v-if="unitPrice.discount_price" class="text-danger">
                                    ৳@{{ parseFloat(unitPrice.discount_price).toFixed(2) }}
                                </span>
                                <span v-else class="text-muted">-</span>
                            </td>
                            <td>
                                <span v-if="unitPrice.discount_percent > 0" class="badge bg-warning text-dark">
                                    @{{ unitPrice.discount_percent }}%
                                </span>
                                <span v-else class="text-muted">-</span>
                            </td>
                            <td class="text-center">
                                <button type="button" @click="removeUnitPrice(index)" 
                                    class="btn btn-sm btn-danger" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="alert alert-success mb-0">
                    <i class="fas fa-check-circle"></i> 
                    <strong>@{{ unitPricing.length }}</strong> unit price(s) added successfully.
                </div>
            </div>
            <div v-else class="alert alert-info">
                <i class="fas fa-info-circle"></i> 
                No unit-based pricing added yet. Use the form above to add unit prices.
            </div>
        </div>

        <div v-else class="alert alert-secondary">
            <i class="fas fa-toggle-off"></i> 
            Unit-based pricing is disabled. Enable it using the toggle above to add different pricing for different units.
        </div>
    </div> --}}

    <!-- Special Offer Section -->
    <div class="col-12 mt-4">
        <h5 class="border-bottom pb-2 mb-3">Special Offer</h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="form-check form-switch">
                    <input type="checkbox" v-model="specialOffer.is_special" 
                        class="form-check-input" id="specialOfferCheck" :true-value="1" :false-value="0">
                    <label class="form-check-label" for="specialOfferCheck">
                        Enable Special Offer
                    </label>
                </div>
            </div>

            <div class="col-md-6 mb-3" v-if="specialOffer.is_special">
                <label class="form-label d-block">Offer End Date & Time</label>
                <input type="datetime-local" v-model="specialOffer.offer_end_time" 
                    class="form-control">
            </div>
        </div>
    </div>

</div>

