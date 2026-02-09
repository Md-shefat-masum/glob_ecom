<div class="row">
    
    <!-- Shipping Information -->
    <div class="col-12 mb-4">
        <h5 class="border-bottom pb-2 mb-3">Shipping Information</h5>
        <div class="row">
            <div class="col-md-3 mb-3">
                <label class="form-label d-block">Product Weight</label>
                <input type="number" v-model="shippingInfo.weight" class="form-control" 
                    step="0.01" min="0" placeholder="Weight in kg">
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label d-block">Dimension Unit</label>
                <select v-model="shippingInfo.dimension_unit" class="form-control">
                    <option value="cm">Centimeters (cm)</option>
                    <option value="in">Inches (in)</option>
                    <option value="m">Meters (m)</option>
                </select>
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label d-block">Package Type</label>
                <select v-model="shippingInfo.package_type" class="form-control">
                    <option value="Box">Box</option>
                    <option value="Envelope">Envelope</option>
                    <option value="Bag">Bag</option>
                    <option value="Parcel">Parcel</option>
                    <option value="Custom">Custom</option>
                </select>
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label d-block">Return Policy (Days)</label>
                <input type="number" v-model="shippingInfo.return_policy_days" 
                    class="form-control" min="0" placeholder="e.g., 7">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="form-check form-switch">
                    <input type="checkbox" v-model="shippingInfo.is_fragile" 
                        class="form-check-input" id="isFragileCheck" :true-value="1" :false-value="0">
                    <label class="form-check-label" for="isFragileCheck">
                        This is a fragile item
                    </label>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="form-check form-switch">
                    <input type="checkbox" v-model="shippingInfo.returnable" 
                        class="form-check-input" id="returnableCheck" :true-value="1" :false-value="0">
                    <label class="form-check-label" for="returnableCheck">
                        This product is returnable
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Tax Information -->
    <div class="col-12 mb-4">
        <h5 class="border-bottom pb-2 mb-3">Tax Information</h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label d-block">Tax Class ID</label>
                <input type="number" v-model="taxInfo.tax_class_id" class="form-control" 
                    min="0" placeholder="Tax class identifier">
                <small class="text-muted d-block">Leave empty if no tax applicable</small>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label d-block">Tax Percentage (%)</label>
                <input type="number" v-model="taxInfo.tax_percent" class="form-control" 
                    step="0.01" min="0" max="100" placeholder="e.g., 5, 15">
            </div>
        </div>
    </div>

    <!-- Additional Info -->
    <div class="col-12">
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> 
            <strong>Note:</strong> Shipping and tax information helps calculate accurate delivery charges 
            and final prices for customers. Fill in these details carefully.
        </div>
    </div>

</div>

