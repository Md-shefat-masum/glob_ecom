<div class="basic-info-form">
    <!-- Product Identity Section -->
    <fieldset class="form-fieldset">
        <legend class="form-legend">
            <i class="fas fa-tag me-2"></i>Product Identity
        </legend>
        <div class="row">
            <div class="col-lg-6 col-md-6 mb-3">
                <label class="form-label">Product Name <span class="text-danger">*</span></label>
                <input type="text" v-model="product.name" class="form-control" placeholder="Enter product name"
                       required @blur="handleNameBlur">
            </div>
            <div class="col-lg-6 col-md-6 mb-3">
                <label class="form-label">Product URL <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text text-muted" v-if="slugPreviewPrefix">@{{ slugPreviewPrefix }}</span>
                    <input type="text"
                           v-model="product.slug"
                           class="form-control"
                           placeholder="unique-product-url"
                           @input="handleSlugInput"
                           @blur="handleSlugBlur"
                           :class="{'is-invalid': slugState.error}">
                </div>
                <small class="text-muted d-block mt-1">Use lowercase letters, numbers, and hyphen only.</small>
                <small class="text-info d-block mt-1" v-if="slugState.checking">
                    <i class="fas fa-spinner fa-spin me-1"></i>Checking availability...
                </small>
                <div class="invalid-feedback d-block" v-if="slugState.error">
                    @{{ slugState.error }}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-3">
                <label class="form-label">Product Code</label>
                <input type="text" v-model="product.code" class="form-control" placeholder="PRD-001">
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <label class="form-label">SKU</label>
                <input type="text" v-model="product.sku" class="form-control" placeholder="Auto-generated">
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <label class="form-label">Barcode</label>
                <input type="text" v-model="product.barcode" class="form-control" placeholder="Barcode">
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <label class="form-label">HSN Code</label>
                <input type="text" v-model="product.hsn_code" class="form-control" placeholder="HSN Code">
            </div>
        </div>
    </fieldset>

    <!-- Product Classification Section -->
    <fieldset class="form-fieldset">
        <legend class="form-legend">
            <i class="fas fa-layer-group me-2"></i>Product Classification
        </legend>
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-3">
                <label class="form-label">Category <span class="text-danger">*</span></label>
                <div class="select-with-add-btn">
                    <select v-model="product.category_id" @change="loadSubcategories" id="categorySelect"
                        class="form-select select2-category" required>
                        <option value="">Select Category</option>
                        <option v-for="category in categories" :key="category.id" :value="category.id">
                            @{{ category.name }}
                        </option>
                    </select>
                    <button type="button" class="btn-add-item" @click="openCategoryModal" title="Add New Category">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-3">
                <label class="form-label">Subcategory</label>
                <div class="select-with-add-btn">
                    <select v-model="product.subcategory_id" @change="loadChildCategories" id="subcategorySelect"
                        class="form-select select2-subcategory">
                        <option value="">Select Subcategory</option>
                        <option v-for="subcategory in subcategories" :key="subcategory.id" :value="subcategory.id">
                            @{{ subcategory.name }}
                        </option>
                    </select>
                    <button type="button" class="btn-add-item" 
                        @click="openSubcategoryModal" 
                        :disabled="!product.category_id"
                        :title="product.category_id ? 'Add New Subcategory' : 'Please select a category first'">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-3">
                <label class="form-label">Child Category</label>
                <div class="select-with-add-btn">
                    <select v-model="product.childcategory_id" id="childCategorySelect" class="form-select select2-child-category">
                        <option value="">Select Child Category</option>
                        <option v-for="child in childCategories" :key="child.id" :value="child.id">
                            @{{ child.name }}
                        </option>
                    </select>
                    <button type="button" class="btn-add-item" 
                        @click="openChildCategoryModal" 
                        :disabled="!product.category_id || !product.subcategory_id"
                        :title="!product.category_id ? 'Please select a category first' : !product.subcategory_id ? 'Please select a subcategory first' : 'Add New Child Category'">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-3">
                <label class="form-label">Brand</label>
                <div class="select-with-add-btn">
                    <select v-model="product.brand_id" @change="loadModels" id="brandSelect" class="form-select select2-brand">
                        <option value="">Select Brand</option>
                        <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                            @{{ brand.name }}
                        </option>
                    </select>
                    <button type="button" class="btn-add-item" @click="openBrandModal" title="Add New Brand">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-3">
                <label class="form-label">Model</label>
                <div class="select-with-add-btn">
                    <select v-model="product.model_id" id="modelSelect" class="form-select select2-model">
                        <option value="">Select Model</option>
                        <option v-for="model in models" :key="model.id" :value="model.id">
                            @{{ model.name }}
                        </option>
                    </select>
                    <button type="button" class="btn-add-item" 
                        @click="openModelModal" 
                        :disabled="!product.brand_id"
                        :title="product.brand_id ? 'Add New Model' : 'Please select a brand first'">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-3">
                <label class="form-label">Unit</label>
                <div class="select-with-add-btn">
                    <select v-model="product.unit_id" id="unitSelect" class="form-select select2-unit">
                        <option value="">Select Unit</option>
                        <option v-for="unit in units" :key="unit.id" :value="unit.id">
                            @{{ unit.name }}
                        </option>
                    </select>
                    <button type="button" class="btn-add-item" @click="openUnitModal" title="Add New Unit">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-3">
                <label class="form-label">Flag</label>
                <div class="select-with-add-btn">
                    <select v-model="product.flag_id" id="flagSelect" class="form-select select2-flag">
                        <option value="">Select Flag</option>
                        <option v-for="flag in flags" :key="flag.id" :value="flag.id">
                            @{{ flag.name }}
                        </option>
                    </select>
                    <button type="button" class="btn-add-item" @click="openFlagModal" title="Add New Flag">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
    </fieldset>

    <!-- Status & Contact Section -->
    <fieldset class="form-fieldset">
        <legend class="form-legend">
            <i class="fas fa-info-circle me-2"></i>Status & Contact Information
        </legend>
        <div class="status-contact-wrapper">
            <div class="status-section">
                <label class="form-label">Status</label>
                <div class="radio-group">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" id="publish_product" name="status" value="1" v-model="product.status">
                        <label class="form-check-label" for="publish_product">Publish</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" id="unpublish_product" name="status" value="0" v-model="product.status">
                        <label class="form-check-label" for="unpublish_product">Unpublish</label>
                    </div>
                </div>
            </div>
            <div class="availability-section">
                <label class="form-label">Availability Status</label>
                <div class="radio-group-vertical">
                    <div class="form-check" v-for="option in availabilityOptions" :key="`availability_${option.value}`">
                        <input class="form-check-input" type="radio"
                               :id="`availability_${option.value}`"
                               name="availability_status"
                               :value="option.value"
                               v-model="product.availability_status">
                        <label class="form-check-label" :for="`availability_${option.value}`">
                            @{{ option.label }}
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="contact-checkbox-wrapper">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="need_contact_during_order" 
                       v-model="product.need_contact_during_order" 
                       :value="1">
                <label class="form-check-label" for="need_contact_during_order">
                    Need to contact during order product
                </label>
            </div>
        </div>
        <div v-if="product.need_contact_during_order" class="contact-fields-wrapper">
            <div class="contact-number-field">
                <label class="form-label">Contact Number</label>
                <input type="text" v-model="product.contact_number" class="form-control" placeholder="Contact number">
            </div>
            <div class="contact-description-field">
                <label class="form-label">Contact Description</label>
                <textarea v-model="product.contact_description" class="form-control" rows="3" placeholder="Contact description"></textarea>
            </div>
        </div>
    </fieldset>
</div>
