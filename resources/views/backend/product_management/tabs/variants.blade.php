<div class="row">

    <!-- Product Variant Selector -->
    <div class="col-12 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="mb-2">
                            <i class="fas fa-layer-group text-primary me-2"></i>
                            Product Variants Management
                        </h5>
                        <p class="text-muted mb-0">
                            Configure product variants and combinations
                        </p>
                    </div>
                    <div class="col-md-4">
                        <div class="text-md-end mt-3 mt-md-0">
                            <small class="text-muted d-block">
                                <span v-if="hasVariants">
                                    <i class="fas fa-info-circle"></i> Managing variants for this product
                                </span>
                                <span v-else>
                                    <i class="fas fa-info-circle"></i> Simple product without variants
                                </span>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Variant mode selector (Yes / No) -->
    <div class="col-12 mb-4">
        <fieldset class="border rounded-3 p-3">
            <legend class="float-none w-auto px-2 small text-muted fw-semibold">
                Variant Configuration
            </legend>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label mb-0">Product has variant</label>
                    <select class="form-control mt-1" v-model="hasVariants">
                        <option :value="false">No</option>
                        <option :value="true">Yes</option>
                    </select>
                    <small class="text-muted d-block">
                        Choose whether this product has variants (color, size, etc.)
                    </small>
                </div>
            </div>
        </fieldset>
    </div>

    <!-- Variants Management Layout -->
    <div class="col-12" v-if="hasVariants">
        <fieldset class="border rounded-3 p-3">
            <legend class="float-none w-auto px-2 small text-muted fw-semibold">
                Variant Management (For Cart & POS Sale)
            </legend>
            <div class="alert alert-info mb-3">
                <i class="fas fa-info-circle"></i> 
                <strong>Note:</strong> These variants are used for <strong>Add to Cart</strong> and <strong>POS Sale</strong>. 
                Color & Size and Other Variants create product combinations that customers can select when purchasing.
            </div>

            <!-- Tab Navigation -->
            <ul class="nav nav-tabs mb-3" role="tablist">
                <li class="nav-item" role="presentation" v-if="shouldShowColorSizeTab">
                    <a href="javascript:void(0)" class="nav-link" :class="{ active: activeVariantTab === 'colorSize' }"
                        @click="activeVariantTab = 'colorSize'">
                        <i class="fas fa-palette me-1"></i> Color & Size 
                        <small class="text-muted ms-1">(Optional Feature)</small>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="javascript:void(0)" class="nav-link" :class="{ active: activeVariantTab === 'otherVariants' }"
                        @click="activeVariantTab = 'otherVariants'">
                        <i class="fas fa-sliders-h me-1"></i> Other Variants
                    </a>
                </li>
            </ul>
            <div class="alert alert-light border mb-3">
                <small class="text-muted">
                    <i class="fas fa-shopping-cart"></i> <strong>Color & Size</strong> and <strong>Other Variants</strong> are used for 
                    <strong>Add to Cart</strong> and <strong>POS Sale</strong>. Customers select these when purchasing products.
                </small>
            </div>

            <!-- Tab Content -->
            <div class="tab-content">
                <!-- Color & Size Tab -->
                <div v-show="shouldShowColorSizeTab && activeVariantTab === 'colorSize'" class="tab-pane fade" :class="{ 'show active': activeVariantTab === 'colorSize' }">
                    <div class="row">
                        <!-- LEFT SIDEBAR: Color & Size Selectors -->
                        <div class="col-md-3">
                            <div class="card border-primary" style="position: sticky; top: 20px;">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-palette"></i> Color & Size Configuration
                                    </h6>
                                </div>
                                <div class="card-body p-3" style="max-height: 70vh; overflow-y: auto;">
                                    <!-- Color -->
                                    <div class="mb-3">
                                        <label class="form-label d-block mb-1"><strong>Color</strong></label>
                                        <select class="form-control form-control-sm select2-color-size" multiple
                                            id="colorSizeColorSelect">
                                            <option v-for="color in colors" :key="color.id"
                                                :value="color.id">
                                                @{{ color.name }}
                                            </option>
                                        </select>
                                        <small v-if="!colors || colors.length === 0" class="text-muted d-block mt-1">
                                            <i class="fas fa-info-circle"></i> No colors available. Please add colors first.
                                        </small>
                                    </div>

                                    <!-- Size -->
                                    <div class="mb-3">
                                        <label class="form-label d-block mb-1"><strong>Size</strong></label>
                                        <select class="form-control form-control-sm select2-color-size" multiple
                                            id="colorSizeSizeSelect">
                                            <option v-for="size in sizes" :key="size.id" :value="size.id">
                                                @{{ size.name }}
                                            </option>
                                        </select>
                                        <small v-if="!sizes || sizes.length === 0" class="text-muted d-block mt-1">
                                            <i class="fas fa-info-circle"></i> No sizes available. Please add sizes first.
                                        </small>
                                    </div>

                                    <!-- Help Text -->
                                    <div class="alert alert-light border p-2 mt-3">
                                        <small class="text-muted">
                                            <i class="fas fa-lightbulb"></i>
                                            <strong>Note:</strong><br>
                                            • Combinations are grouped by color<br>
                                            • Each color will have all selected sizes
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- RIGHT SIDE: Color & Size Combinations -->
                        <div class="col-md-9">
                            <!-- Summary Card -->
                            <div class="card mb-3 bg-light">
                                <div class="card-body p-3">
                                    <div class="row text-center">
                                        <div class="col">
                                            <h6 class="mb-1 text-primary">@{{ colorSizeCombinations.length }}</h6>
                                            <small class="text-muted">Color & Size Combinations</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Combinations Table -->
                            <div class="card" v-if="colorSizeCombinations.length > 0">
                                <div class="card-header bg-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-table"></i> Color & Size Combinations
                                        <span class="badge bg-primary ms-2">@{{ colorSizeCombinations.length }}</span>
                                    </h6>
                                </div>
                                <div class="card-body p-0">
                                    <!-- Set All Common Values Row -->
                                    <div class="card bg-light border-primary mb-3">
                                        <div class="card-body p-3">
                                            <div class="row align-items-end g-3">
                                                <div class="col-md-3">
                                                    <label class="form-label small mb-1"><strong>Common Price</strong></label>
                                                    <input type="number" v-model="commonColorSizeValues.price"
                                                        class="form-control form-control-sm" step="0.01"
                                                        placeholder="0.00">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small mb-1"><strong>Discount
                                                            Price</strong></label>
                                                    <input type="number" v-model="commonColorSizeValues.discount_price"
                                                        class="form-control form-control-sm" step="0.01"
                                                        placeholder="0.00">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small mb-1"><strong>Add. Price</strong></label>
                                                    <input type="number" v-model="commonColorSizeValues.additional_price"
                                                        class="form-control form-control-sm" step="0.01"
                                                        placeholder="0.00">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label small mb-1"><strong>Low Alert</strong></label>
                                                    <input type="number" v-model="commonColorSizeValues.low_stock_alert"
                                                        class="form-control form-control-sm" min="0" placeholder="10">
                                                </div>
                                                <div class="col-md-1">
                                                    <label class="form-label small mb-1">&nbsp;</label>
                                                    <button type="button" @click="applyCommonValuesToColorSizeVariants"
                                                        class="btn btn-primary btn-sm w-100">
                                                        <i class="fas fa-magic"></i> Set All
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Color Image Upload Blocks -->
                                    <div v-if="selectedColorSizeColors && selectedColorSizeColors.length > 0" class="card bg-light border-info mb-3">
                                        <div class="card-header bg-info text-white">
                                            <h6 class="mb-0">
                                                <i class="fas fa-images"></i> Color Images
                                                <small class="ms-2">Upload one image per color (shared by all sizes)</small>
                                            </h6>
                                        </div>
                                        <div class="card-body p-3">
                                            <div class="row g-3">
                                                <div v-for="colorId in selectedColorSizeColors" :key="'color-img-' + colorId" class="col-md-4 col-lg-3">
                                                    <div class="card border">
                                                        <div class="card-body p-2">
                                                            <label class="form-label small mb-2 d-block">
                                                                <strong>@{{ getColorName(colorId) }}</strong>
                                                            </label>
                                                            <div class="color-image-wrapper position-relative">
                                                                <div v-if="getColorImageUrl(colorId)" class="color-image-preview">
                                                                    <img :src="getColorImageUrl(colorId)" alt="Color"
                                                                        class="img-thumbnail"
                                                                        style="width: 100%; height: 120px; object-fit: cover; cursor: pointer;"
                                                                        @click="$refs['colorFileInput' + colorId][0].click()">
                                                                    <button type="button"
                                                                        @click="removeColorImage(colorId)"
                                                                        class="btn btn-danger btn-sm position-absolute top-0 end-0"
                                                                        style="padding: 2px 6px; font-size: 10px; z-index: 10;">
                                                                        <i class="fas fa-times"></i>
                                                                    </button>
                                                                </div>
                                                                <div v-else class="color-image-placeholder"
                                                                    @click="$refs['colorFileInput' + colorId][0].click()"
                                                                    style="width: 100%; height: 120px; border: 2px dashed #dee2e6; 
                                                                       border-radius: 4px; display: flex; align-items: center; 
                                                                       justify-content: center; cursor: pointer; background: #f8f9fa;">
                                                                    <div class="text-center">
                                                                        <i class="fas fa-camera text-muted mb-2" style="font-size: 24px;"></i>
                                                                        <div class="small text-muted">Click to upload</div>
                                                                    </div>
                                                                </div>
                                                                <input type="file" :ref="'colorFileInput' + colorId"
                                                                    @change="handleColorImageUpload($event, colorId)"
                                                                    accept="image/jpeg,image/jpg,image/png"
                                                                    style="display: none;">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-hover table-sm mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="80" class="text-center">Image</th>
                                                    <th class="ps-3">Combination</th>
                                                    <th width="110">Price (৳)</th>
                                                    <th width="110">Discount (৳)</th>
                                                    <th width="100">Add. Price</th>
                                                    <th width="90">Low Alert</th>
                                                    <th width="130">SKU</th>
                                                    <th width="130">Barcode</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-if="colorSizeCombinations.length === 0">
                                                    <td colspan="8" class="text-center text-muted py-4">
                                                        Select colors and sizes to generate combinations.
                                                    </td>
                                                </tr>
                                                <tr v-for="(variant, idx) in colorSizeCombinations"
                                                    :key="variant.combo.combination_key + '-' + idx">
                                                    <!-- Variant Image (from color image) -->
                                                    <td class="text-center align-middle">
                                                        <div class="variant-image-wrapper">
                                                            <div v-if="getColorImageUrl(variant.combo.color_id)" class="variant-image-preview">
                                                                <img :src="getColorImageUrl(variant.combo.color_id)" alt="Variant"
                                                                    class="img-thumbnail"
                                                                    style="width: 60px; height: 60px; object-fit: cover;">
                                                            </div>
                                                            <div v-else class="variant-image-placeholder"
                                                                style="width: 60px; height: 60px; border: 1px solid #dee2e6; 
                                                                   border-radius: 4px; display: flex; align-items: center; 
                                                                   justify-content: center; background: #f8f9fa;">
                                                                <i class="fas fa-image text-muted" style="font-size: 20px;"></i>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td class="ps-3">
                                                        <div class="d-flex flex-column">
                                                            <strong class="text-primary mb-1">@{{ variant.combo.combination_key }}</strong>
                                                            <div class="d-flex flex-wrap gap-1">
                                                                <span v-for="(value, key) in variant.combo.variant_values"
                                                                    :key="key">
                                                                    <span class="badge bg-secondary" style="font-size: 10px;">
                                                                        @{{ value }}
                                                                    </span>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="number" v-model="variant.combo.price"
                                                            class="form-control form-control-sm" step="0.01"
                                                            placeholder="—">
                                                    </td>
                                                    <td>
                                                        <input type="number" v-model="variant.combo.discount_price"
                                                            class="form-control form-control-sm" step="0.01"
                                                            placeholder="0">
                                                    </td>
                                                    <td>
                                                        <input type="number" v-model="variant.combo.additional_price"
                                                            class="form-control form-control-sm" step="0.01"
                                                            placeholder="0">
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <div class="d-flex align-items-center justify-content-center">
                                                            <span class="badge"
                                                                :class="{
                                                                    'bg-success': variant.combo.present_stock > (variant.combo
                                                                        .low_stock_alert || 10),
                                                                    'bg-warning text-dark': variant.combo.present_stock > 0 &&
                                                                        variant.combo.present_stock <= (variant.combo
                                                                            .low_stock_alert || 10),
                                                                    'bg-danger': variant.combo.present_stock == 0
                                                                }"
                                                                style="font-size: 13px; padding: 6px 12px;">
                                                                <i class="fas fa-boxes me-1"></i>
                                                                @{{ variant.combo.present_stock || 0 }}
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="number" v-model="variant.combo.stock"
                                                            class="form-control form-control-sm" min="0"
                                                            step="1" placeholder="0"
                                                            title="Enter quantity to add to existing stock">
                                                    </td>
                                                    <td>
                                                        <input type="number" v-model="variant.combo.low_stock_alert"
                                                            class="form-control form-control-sm" min="0"
                                                            placeholder="10">
                                                    </td>
                                                    <td>
                                                        <input type="text" v-model="variant.combo.sku"
                                                            class="form-control form-control-sm" placeholder="SKU">
                                                    </td>
                                                    <td>
                                                        <input type="text" v-model="variant.combo.barcode"
                                                            class="form-control form-control-sm" placeholder="Barcode">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Other Variants Tab -->
                <div v-show="activeVariantTab === 'otherVariants'" class="tab-pane fade" :class="{ 'show active': activeVariantTab === 'otherVariants' }">
                    <div class="row">
                        <!-- LEFT SIDEBAR: Other Variant Selectors -->
                        <div class="col-md-3">
                            <div class="card border-success" style="position: sticky; top: 20px;">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-sliders-h"></i> Other Variant Configuration
                                    </h6>
                                </div>
                                <div class="card-body p-3" style="max-height: 70vh; overflow-y: auto;">
                                    <!-- Stock-Related Variants -->
                                    <div class="mb-3" v-if="getStockRelatedGroups().length > 0">
                                        <h6 class="text-success border-bottom pb-2">
                                            <i class="fas fa-boxes"></i> Stock Variants
                                            <span class="badge bg-success badge-sm">Combo</span>
                                        </h6>

                                        <!-- Group Selector -->
                                        <div class="mb-2">
                                            <label class="form-label d-block mb-1 small"><strong>Select
                                                    Groups:</strong></label>
                                            <select id="otherVariantGroupSelector"
                                                class="form-control form-control-sm select2-other-variant-selector" multiple
                                                v-model="selectedOtherStockGroupSlugs">
                                                <option v-for="group in getStockRelatedGroups()" :key="group.id"
                                                    :value="group.slug">
                                                    @{{ group.name }}
                                                </option>
                                            </select>
                                        </div>

                                        <!-- Selected Groups Configuration -->
                                        <div v-if="selectedOtherStockGroupSlugs && selectedOtherStockGroupSlugs.length > 0"
                                            class="mt-3">
                                            <div class="mb-3" v-for="groupSlug in selectedOtherStockGroupSlugs"
                                                :key="groupSlug">
                                                <template v-for="group in variantGroups">
                                                    <template v-if="group.slug === groupSlug">
                                                        <label class="form-label d-block mb-1 small">
                                                            <strong>@{{ group.name }}</strong>
                                                        </label>
                                                        <select :id="'otherVariantGroup_' + group.slug"
                                                            class="form-control form-control-sm select2-other-variant-values"
                                                            multiple v-model="selectedOtherVariantGroups[group.slug]">
                                                            <option v-for="key in (group.active_keys || [])" :key="key.id"
                                                                :value="key.id">
                                                                @{{ key.key_name }}
                                                            </option>
                                                        </select>
                                                    </template>
                                                </template>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Help Text -->
                                    <div class="alert alert-light border p-2 mt-3">
                                        <small class="text-muted">
                                            <i class="fas fa-lightbulb"></i>
                                            <strong>Tips:</strong><br>
                                            • Stock variants create combinations<br>
                                            • Select values to generate combinations
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- RIGHT SIDE: Other Variants Combinations -->
                        <div class="col-md-9">
                            <!-- Summary Card -->
                            <div class="card mb-3 bg-light">
                                <div class="card-body p-3">
                                    <div class="row text-center">
                                        <div class="col">
                                            <h6 class="mb-1 text-primary">@{{ otherVariantsCombinations.length }}</h6>
                                            <small class="text-muted">Other Variant Combinations</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Combinations Table -->
                            <div class="card" v-if="otherVariantsCombinations.length > 0">
                                <div class="card-header bg-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-table"></i> Other Variant Combinations
                                        <span class="badge bg-success ms-2">@{{ otherVariantsCombinations.length }}</span>
                                    </h6>
                                </div>
                                <div class="card-body p-0">
                                    <!-- Set All Common Values Row -->
                                    <div class="card bg-light border-success mb-3">
                                        <div class="card-body p-3">
                                            <div class="row align-items-end g-3">
                                                <div class="col-md-3">
                                                    <label class="form-label small mb-1"><strong>Common Price</strong></label>
                                                    <input type="number" v-model="commonOtherVariantValues.price"
                                                        class="form-control form-control-sm" step="0.01"
                                                        placeholder="0.00">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small mb-1"><strong>Discount
                                                            Price</strong></label>
                                                    <input type="number" v-model="commonOtherVariantValues.discount_price"
                                                        class="form-control form-control-sm" step="0.01"
                                                        placeholder="0.00">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small mb-1"><strong>Add. Price</strong></label>
                                                    <input type="number" v-model="commonOtherVariantValues.additional_price"
                                                        class="form-control form-control-sm" step="0.01"
                                                        placeholder="0.00">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label small mb-1"><strong>Low Alert</strong></label>
                                                    <input type="number" v-model="commonOtherVariantValues.low_stock_alert"
                                                        class="form-control form-control-sm" min="0" placeholder="10">
                                                </div>
                                                <div class="col-md-1">
                                                    <label class="form-label small mb-1">&nbsp;</label>
                                                    <button type="button" @click="applyCommonValuesToOtherVariants"
                                                        class="btn btn-success btn-sm w-100">
                                                        <i class="fas fa-magic"></i> Set All
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-hover table-sm mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="80" class="text-center">Image</th>
                                                    <th class="ps-3">Combination</th>
                                                    <th width="110">Price (৳)</th>
                                                    <th width="110">Discount (৳)</th>
                                                    <th width="100">Add. Price</th>
                                                    <th width="90">Low Alert</th>
                                                    <th width="130">SKU</th>
                                                    <th width="130">Barcode</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-if="otherVariantsCombinations.length === 0">
                                                    <td colspan="8" class="text-center text-muted py-4">
                                                        Select variant groups to generate combinations.
                                                    </td>
                                                </tr>
                                                <tr v-for="(variant, idx) in otherVariantsCombinations"
                                                    :key="variant.combo.combination_key + '-' + idx">
                                                    <!-- Variant Image -->
                                                    <td class="text-center align-middle">
                                                        <div class="variant-image-wrapper position-relative">
                                                            <div v-if="variant.combo.image_url" class="variant-image-preview">
                                                                <img :src="variant.combo.image_url" alt="Variant"
                                                                    class="img-thumbnail"
                                                                    style="width: 60px; height: 60px; object-fit: cover; cursor: pointer;"
                                                                    @click="$refs['otherVariantFileInput' + idx][0].click()">
                                                                <button type="button"
                                                                    @click="removeOtherVariantImage(idx)"
                                                                    class="btn btn-danger btn-sm position-absolute top-0 end-0"
                                                                    style="padding: 2px 6px; font-size: 10px; z-index: 10;">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </div>
                                                            <div v-else class="variant-image-placeholder"
                                                                @click="$refs['otherVariantFileInput' + idx][0].click()"
                                                                style="width: 60px; height: 60px; border: 2px dashed #dee2e6; 
                                                                   border-radius: 4px; display: flex; align-items: center; 
                                                                   justify-content: center; cursor: pointer; background: #f8f9fa;">
                                                                <i class="fas fa-camera text-muted"></i>
                                                            </div>
                                                            <input type="file" :ref="'otherVariantFileInput' + idx"
                                                                @change="handleOtherVariantImageUpload($event, idx)"
                                                                accept="image/jpeg,image/jpg,image/png"
                                                                style="display: none;">
                                                        </div>
                                                    </td>

                                                    <td class="ps-3">
                                                        <div class="d-flex flex-column">
                                                            <strong class="text-primary mb-1">@{{ variant.combo.combination_key }}</strong>
                                                            <div class="d-flex flex-wrap gap-1">
                                                                <span v-for="(value, key) in variant.combo.variant_values"
                                                                    :key="key">
                                                                    <span class="badge bg-secondary" style="font-size: 10px;">
                                                                        @{{ value }}
                                                                    </span>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="number" v-model="variant.combo.price"
                                                            class="form-control form-control-sm" step="0.01"
                                                            placeholder="—">
                                                    </td>
                                                    <td>
                                                        <input type="number" v-model="variant.combo.discount_price"
                                                            class="form-control form-control-sm" step="0.01"
                                                            placeholder="0">
                                                    </td>
                                                    <td>
                                                        <input type="number" v-model="variant.combo.additional_price"
                                                            class="form-control form-control-sm" step="0.01"
                                                            placeholder="0">
                                                    </td>
                                                    <td>
                                                        <input type="number" v-model="variant.combo.low_stock_alert"
                                                            class="form-control form-control-sm" min="0"
                                                            placeholder="10">
                                                    </td>
                                                    <td>
                                                        <input type="text" v-model="variant.combo.sku"
                                                            class="form-control form-control-sm" placeholder="SKU">
                                                    </td>
                                                    <td>
                                                        <input type="text" v-model="variant.combo.barcode"
                                                            class="form-control form-control-sm" placeholder="Barcode">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </fieldset>
    </div>
</div>
