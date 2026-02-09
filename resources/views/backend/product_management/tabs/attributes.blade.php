<div class="row">
    
    <!-- General Attributes -->
    <div class="col-12 mb-4">
        <h5 class="border-bottom pb-2 mb-3">General Attributes</h5>
        <div class="row">
            <div class="col-md-3 mb-3">
                <label class="form-label d-block">Material</label>
                <input type="text" v-model="attributes.material" class="form-control" 
                    placeholder="e.g., Cotton, Polyester">
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label d-block">Style</label>
                <input type="text" v-model="attributes.style" class="form-control" 
                    placeholder="e.g., Casual, Formal">
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label d-block">Pattern</label>
                <input type="text" v-model="attributes.pattern" class="form-control" 
                    placeholder="e.g., Plain, Striped">
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label d-block">Fit</label>
                <input type="text" v-model="attributes.fit" class="form-control" 
                    placeholder="e.g., Regular, Slim">
            </div>
        </div>
    </div>

    <!-- Fabrication -->
    <div class="col-12 mb-4">
        <h5 class="border-bottom pb-2 mb-3">Fabrication</h5>
        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label d-block">Fabrication Details</label>
                <textarea v-model="attributes.fabrication" class="form-control" rows="3" 
                    placeholder="e.g., 100% Cotton, 180 GSM, Knitted"></textarea>
            </div>
        </div>
    </div>

    <!-- Dimensions -->
    <div class="col-12 mb-4">
        <h5 class="border-bottom pb-2 mb-3">Dimensions</h5>
        <div class="row">
            <div class="col-md-2 mb-3">
                <label class="form-label d-block">Length</label>
                <input type="text" v-model="attributes.dimensions.length" class="form-control" 
                    placeholder="e.g., 120cm">
            </div>

            <div class="col-md-2 mb-3">
                <label class="form-label d-block">Width</label>
                <input type="text" v-model="attributes.dimensions.width" class="form-control" 
                    placeholder="e.g., 50cm">
            </div>

            <div class="col-md-2 mb-3">
                <label class="form-label d-block">Height</label>
                <input type="text" v-model="attributes.dimensions.height" class="form-control" 
                    placeholder="e.g., 10cm">
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label d-block">Weight</label>
                <input type="text" v-model="attributes.dimensions.weight" class="form-control" 
                    placeholder="e.g., 2kg">
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label d-block">Capacity</label>
                <input type="text" v-model="attributes.dimensions.capacity" class="form-control" 
                    placeholder="e.g., 1.5L">
            </div>
        </div>
    </div>

    <!-- Measurements (for Garments) -->
    <div class="col-12 mb-4">
        <h5 class="border-bottom pb-2 mb-3">Garment Measurements</h5>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label d-block">Chest</label>
                <input type="text" v-model="attributes.measurements.chest" class="form-control" 
                    placeholder="e.g., 42 inches">
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label d-block">Waist</label>
                <input type="text" v-model="attributes.measurements.waist" class="form-control" 
                    placeholder="e.g., 32 inches">
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label d-block">Sleeve</label>
                <input type="text" v-model="attributes.measurements.sleeve" class="form-control" 
                    placeholder="e.g., 20 inches">
            </div>
        </div>
    </div>

    <!-- Additional Info -->
    <div class="col-12">
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> 
            <strong>Note:</strong> These attributes help customers find products and provide detailed product information. 
            Fill in relevant fields based on your product type.
        </div>
    </div>

</div>

