<!-- Overview Tab -->
<div class="card tab-card" v-show="isActiveTab('overview')">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h5 class="card-title mb-0">Package Overview</h5>
            <small class="text-muted">Define the hero experience customers will see on the landing page.</small>
        </div>
        <div class="text-end">
            <span class="badge bg-primary me-2" v-if="items.length">
                <i class="fas fa-cubes me-1"></i> @{{ items.length }} items
            </span>
            <span class="badge bg-success" v-if="itemsTotals.savingsAmount > 0">
                <i class="fas fa-percentage me-1"></i> Save ৳@{{ itemsTotals.savingsAmount.toFixed(2) }}
            </span>
        </div>
    </div>
    <div class="card-body">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Package Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-lg" placeholder="E.g., Ultimate Eid Gadget Bundle"
                           v-model.trim="overview.title" @blur="generateSlugIfEmpty">
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Internal Package Code</label>
                        <input type="text" class="form-control" placeholder="Auto-generated if blank"
                               v-model.trim="overview.package_code">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Public Slug</label>
                        <div class="input-group">
                            <span class="input-group-text">{{ url('/packages') }}/</span>
                            <input type="text" class="form-control" placeholder="marketing-landing-slug"
                                   v-model.trim="overview.slug">
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <label class="form-label fw-semibold">Tagline</label>
                    <input type="text" class="form-control" placeholder="Quick value statement"
                           v-model.trim="overview.tagline">
                </div>
                <div class="mt-3">
                    <label class="form-label fw-semibold">Hero Headline</label>
                    <input type="text" class="form-control" placeholder="The main hero headline customers see"
                           v-model.trim="overview.hero_headline">
                </div>
                <div class="mt-3">
                    <label class="form-label fw-semibold">Hero Subheadline</label>
                    <textarea class="form-control" rows="2" placeholder="Support statement elaborating on value"
                              v-model.trim="overview.hero_subheadline"></textarea>
                </div>
                <div class="row g-3 mt-1">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">CTA Label</label>
                        <input type="text" class="form-control" placeholder="Shop The Bundle"
                               v-model.trim="overview.hero_cta_label">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">CTA Link</label>
                        <input type="text" class="form-control" placeholder="/checkout?bundle=pkg-code"
                               v-model.trim="overview.hero_cta_link">
                    </div>
                </div>

                <div class="row mt-4 g-3">
                    <div class="col-md-4">
                        <div class="p-3 border rounded position-relative h-100">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="fw-semibold mb-0">Bundle Price</h6>
                                <i class="fas fa-tag text-primary"></i>
                            </div>
                            <div class="display-6 fw-bold my-2">৳@{{ pricing.package_price || 0 }}</div>
                            <p class="text-muted small mb-0">Customers pay this amount.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 border rounded position-relative h-100">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="fw-semibold mb-0">Compare Value</h6>
                                <i class="fas fa-balance-scale text-secondary"></i>
                            </div>
                            <div class="display-6 fw-bold my-2 text-decoration-line-through text-muted">
                                ৳@{{ (pricing.compare_at_price || itemsTotals.compareTotal).toFixed(2) }}
                            </div>
                            <p class="text-muted small mb-0">Total if purchased separately.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 border rounded position-relative h-100 bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="fw-semibold mb-0">Total Savings</h6>
                                <i class="fas fa-percentage text-success"></i>
                            </div>
                            <div class="display-6 fw-bold my-2 text-success">৳@{{ itemsTotals.savingsAmount.toFixed(2) }}</div>
                            <p class="text-muted small mb-0">(@{{ itemsTotals.savingsPercent.toFixed(2) }}%)</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <label class="form-label fw-semibold">Hero Image <span class="text-danger">*</span></label>
                <div class="hero-image-uploader border rounded d-flex flex-column align-items-center justify-content-center position-relative"
                     :class="{'has-image': media.hero.preview}" @click="triggerHeroImage">
                    <template v-if="media.hero.preview">
                        <img :src="media.hero.preview" alt="Hero preview" class="img-fluid rounded">
                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2"
                                @click.stop="removeHeroImage"><i class="fas fa-times"></i></button>
                    </template>
                    <template v-else>
                        <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                        <p class="text-muted small mb-0 text-center">
                            Drag & drop or click to upload<br>
                            Recommended 1600x900px, max 5MB
                        </p>
                    </template>
                </div>
                <input type="file" ref="heroImageInput" class="d-none" accept="image/*" @change="handleHeroImageUpload">

                <div class="mt-3">
                    <label class="form-label fw-semibold">Gallery Images <span class="text-muted small">(optional)</span></label>
                    <div class="row g-2">
                        <div class="col-6" v-for="slot in 4" :key="'gallery-slot-' + slot">
                            <div class="gallery-slot border rounded position-relative text-center p-3"
                                 @click="triggerGallerySlot(slot - 1)">
                                <template v-if="media.gallery[slot - 1] && media.gallery[slot - 1].preview">
                                    <img :src="media.gallery[slot - 1].preview" class="img-fluid rounded">
                                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1"
                                            @click.stop="removeGallerySlot(slot - 1)"><i class="fas fa-times"></i></button>
                                </template>
                                <template v-else>
                                    <i class="fas fa-plus-circle text-muted mb-2"></i>
                                    <p class="small text-muted mb-0">Gallery @{{ slot }}</p>
                                </template>
                            </div>
                            <input type="file" class="d-none" :ref="'galleryInput' + (slot - 1)" accept="image/*"
                                   @change="handleGalleryUpload($event, slot - 1)">
                        </div>
                    </div>
                </div>

                <div class="alert alert-info mt-3 mb-0">
                    <i class="fas fa-lightbulb me-2"></i>
                    <span class="small">Hero imagery is reused for metadata and social share previews by default.</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Catalog Tab -->
<div class="card tab-card" v-show="isActiveTab('catalog')">
    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center">
        <div>
            <h5 class="card-title mb-0">Select Products</h5>
            <small class="text-muted">Search catalog, configure variants, set quantities and pricing overrides.</small>
        </div>
        <div class="catalog-summary mt-3 mt-md-0">
            <span class="badge bg-secondary me-2"><i class="fas fa-cubes me-1"></i> @{{ items.length }} selected</span>
            <span class="badge bg-success"><i class="fas fa-calculator me-1"></i> Package Total ৳@{{ itemsTotals.itemsTotal.toFixed(2) }}</span>
        </div>
    </div>
    <div class="card-body">
        <div class="row g-4">
            <div class="col-lg-5">
                <div class="search-panel border rounded p-3 h-100">
                    <div class="d-flex align-items-center mb-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="search" class="form-control" placeholder="Search by name, SKU, code"
                                   v-model="catalog.searchTerm" @input="debouncedSearch">
                        </div>
                        <button class="btn btn-outline-secondary ms-2" @click="loadFeatured"><i class="fas fa-bolt"></i></button>
                    </div>
                    <div class="small text-muted mb-2">
                        <i class="fas fa-info-circle me-1"></i> Click a product to configure & add to the package.
                    </div>
                    <div class="catalog-results">
                        <div v-if="catalog.loading" class="text-center text-muted py-4">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-2 mb-0 small">Fetching catalog...</p>
                        </div>
                        <div v-else-if="catalog.products.length === 0" class="text-center text-muted py-4">
                            <i class="fas fa-box-open fa-2x mb-2"></i>
                            <p class="mb-0">No products match your search.</p>
                        </div>
                        <div v-else class="list-group">
                            <button type="button" class="list-group-item list-group-item-action" v-for="product in catalog.products"
                                    :key="'catalog-product-' + product.id" @click="selectCatalogProduct(product)">
                                <div class="d-flex align-items-start">
                                    <img :src="product.image_url" class="rounded me-3 flex-shrink-0" style="width:50px;height:50px;object-fit:cover;">
                                    <div class="flex-grow-1 text-start">
                                        <div class="fw-semibold">@{{ product.name }}</div>
                                        <div class="small text-muted">
                                            ৳@{{ product.effective_price }} <span v-if="product.price && product.price > product.discount_price" class="text-decoration-line-through">৳@{{ product.price }}</span>
                                        </div>
                                        <div class="small">
                                            <span class="badge bg-light text-dark me-1 text-uppercase">@{{ product.variant_type }}</span>
                                            <span class="text-muted">Stock: @{{ product.total_stock }}</span>
                                        </div>
                                    </div>
                                    <i class="fas fa-plus-circle text-primary fa-lg"></i>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="selected-items border rounded">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 32px;"></th>
                                    <th>Product</th>
                                    <th style="width: 160px;">Variant</th>
                                    <th style="width: 90px;">Quantity</th>
                                    <th style="width: 120px;">Unit Price</th>
                                    <th style="width: 120px;">Compare</th>
                                    <th style="width: 90px;">Subtotal</th>
                                    <th style="width: 32px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="items.length === 0">
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="fas fa-layer-group fa-2x mb-2"></i>
                                        <p class="mb-0">No products selected yet.</p>
                                    </td>
                                </tr>
                                <tr v-for="(item, index) in items" :key="item.key">
                                    <td>
                                        <img :src="item.image_url" class="rounded" style="width:40px;height:40px;object-fit:cover;">
                                    </td>
                                    <td>
                                        <div class="fw-semibold">@{{ item.product_name }}</div>
                                        <div class="small text-muted">SKU: @{{ item.sku || 'N/A' }}</div>
                                    </td>
                                    <td>
                                        <template v-if="item.variant_type === 'combination'">
                                            <select class="form-select form-select-sm" v-model="item.variant_combination_id" @change="handleVariantCombinationChange(item)">
                                                <option value="">Select combination</option>
                                                <option v-for="combo in item.variant_options.combinations" :key="'combo-' + combo.id" :value="combo.id">
                                                    @{{ combo.display }}
                                                </option>
                                            </select>
                                        </template>
                                        <template v-else-if="item.variant_type === 'legacy'">
                                            <select class="form-select form-select-sm mb-1" v-model="item.color_id" @change="syncLegacyVariant(item)">
                                                <option value="">Color</option>
                                                <option v-for="color in item.variant_options.colors" :key="'color-' + color.id" :value="color.id">
                                                    @{{ color.name }}
                                                </option>
                                            </select>
                                            <select class="form-select form-select-sm" v-model="item.size_id" @change="syncLegacyVariant(item)">
                                                <option value="">Size</option>
                                                <option v-for="size in item.variant_options.sizes" :key="'size-' + size.id" :value="size.id">
                                                    @{{ size.name }}
                                                </option>
                                            </select>
                                        </template>
                                        <template v-else>
                                            <span class="badge bg-secondary">Standard</span>
                                        </template>
                                        <div class="small text-muted mt-1" v-if="item.variant_snapshot_text">
                                            <i class="fas fa-tags me-1"></i>@{{ item.variant_snapshot_text }}
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm text-center" min="1"
                                               v-model.number="item.quantity" @change="enforceItemQuantity(item)" />
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">৳</span>
                                            <input type="number" class="form-control text-end" min="0" step="0.01"
                                                   v-model.number="item.unit_price" @change="recalculatePricing">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">৳</span>
                                            <input type="number" class="form-control text-end" min="0" step="0.01"
                                                   v-model.number="item.compare_at_price" @change="recalculatePricing">
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        ৳@{{ (item.unit_price * item.quantity).toFixed(2) }}
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-outline-danger btn-sm" @click="removeItem(index)">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot v-if="items.length">
                                <tr class="table-light">
                                    <th colspan="6" class="text-end">Bundle Total:</th>
                                    <th class="text-end">৳@{{ itemsTotals.itemsTotal.toFixed(2) }}</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="alert alert-info mt-3 mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    <span class="small">Per-item pricing can be overridden to craft unique bundle value. Leave blank to use product pricing.</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Details Tab -->
<div class="card tab-card" v-show="isActiveTab('details')">
    <div class="card-header">
        <h5 class="card-title mb-0">Package Information & Landing Content</h5>
        <small class="text-muted">Control merchandising details, highlights, and publishing state.</small>
    </div>
    <div class="card-body">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Short Description</label>
                    <textarea class="form-control" rows="2" placeholder="Appears in cards and previews" v-model.trim="info.short_description"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Landing Page Description</label>
                    <textarea class="form-control" rows="6" placeholder="Hero narrative, usage scenarios, etc."
                              v-model.trim="info.description"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Key Highlights</label>
                    <div class="highlight-list">
                        <div class="input-group mb-2" v-for="(highlight, index) in info.highlights" :key="'highlight-' + index">
                            <span class="input-group-text"><i class="fas fa-check text-success"></i></span>
                            <input type="text" class="form-control" v-model.trim="info.highlights[index]" placeholder="Value proposition bullet">
                            <button class="btn btn-outline-danger" @click="removeHighlight(index)"><i class="fas fa-times"></i></button>
                        </div>
                        <button class="btn btn-outline-primary btn-sm" @click="addHighlight" :disabled="info.highlights.length >= 6">
                            <i class="fas fa-plus"></i> Add Highlight
                        </button>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Custom Landing Blocks</label>
                    <textarea class="form-control" rows="4" placeholder="Use markdown or JSON to describe custom sections (optional)"
                              v-model="info.content_blocks_raw"></textarea>
                    <small class="text-muted">Optional advanced layout JSON or markdown for developer customization.</small>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="border rounded p-3">
                    <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                    <select class="form-select" v-model="info.status">
                        <option v-for="status in masterData.statuses" :key="'status-' + status.value" :value="status.value">@{{ status.label }}</option>
                    </select>

                    <label class="form-label fw-semibold mt-3">Visibility</label>
                    <select class="form-select" v-model="info.visibility">
                        <option v-for="option in masterData.visibility" :key="'visibility-' + option.value" :value="option.value">@{{ option.label }}</option>
                    </select>

                    <div class="mt-3">
                        <label class="form-label fw-semibold">Publish At</label>
                        <input type="datetime-local" class="form-control" v-model="info.publish_at">
                        <small class="text-muted">Leave empty to publish immediately.</small>
                    </div>

                    <div class="mt-3">
                        <label class="form-label fw-semibold">Display Category</label>
                        <select class="form-select" v-model="info.category_id">
                            <option value="">Unassigned</option>
                            <option v-for="category in masterData.categories" :key="'category-' + category.id" :value="category.id">
                                @{{ category.name }}
                            </option>
                        </select>
                    </div>

                    <div class="mt-4 p-3 bg-light rounded">
                        <h6 class="fw-semibold mb-2">Auto-Save</h6>
                        <p class="small text-muted mb-2">
                            Drafts are auto-saved locally every 20 seconds and restored on revisit.
                        </p>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-secondary me-2" v-if="autoSave.lastSavedTime">
                                <i class="fas fa-clock me-1"></i> @{{ autoSave.lastSavedTime | formatTime }}
                            </span>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" v-model="autoSave.enabled" id="autoSaveToggle">
                                <label class="form-check-label small" for="autoSaveToggle">Enable autosave</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SEO Tab -->
<div class="card tab-card" v-show="isActiveTab('seo')">
    <div class="card-header">
        <h5 class="card-title mb-0">SEO & Meta Information</h5>
        <small class="text-muted">Optimize packaging for organic search and social sharing.</small>
    </div>
    <div class="card-body">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Meta Title</label>
                    <input type="text" class="form-control" maxlength="255" placeholder="Title for SERP & social sharing"
                           v-model.trim="seo.meta_title">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Meta Keywords</label>
                    <input type="text" class="form-control" placeholder="Comma-separated keywords" v-model.trim="seo.meta_keywords">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Meta Description</label>
                    <textarea class="form-control" rows="4" maxlength="300" placeholder="150-160 characters ideal"
                              v-model.trim="seo.meta_description"></textarea>
                </div>
            </div>
            <div class="col-lg-6">
                <label class="form-label fw-semibold">Meta Image</label>
                <div class="meta-image-uploader border rounded d-flex flex-column align-items-center justify-content-center position-relative"
                     :class="{'has-image': seo.meta_image.preview}" @click="triggerMetaImage">
                    <template v-if="seo.meta_image.preview">
                        <img :src="seo.meta_image.preview" alt="Meta preview" class="img-fluid rounded">
                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2"
                                @click.stop="removeMetaImage"><i class="fas fa-times"></i></button>
                    </template>
                    <template v-else>
                        <i class="fas fa-image-polaroid fa-2x text-muted mb-2"></i>
                        <p class="text-muted small mb-0 text-center">
                            Optional custom preview image.<br>Recommended 1200x630px.
                        </p>
                    </template>
                </div>
                <input type="file" ref="metaImageInput" class="d-none" accept="image/*" @change="handleMetaImageUpload">
                <div class="alert alert-light border mt-3 mb-0">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-share-alt text-primary me-2 mt-1"></i>
                        <div>
                            <h6 class="fw-semibold mb-1">Preview</h6>
                            <p class="small mb-1">@{{ seo.meta_title || overview.title || 'Package Title' }}</p>
                            <p class="small text-muted mb-0">@{{ seo.meta_description || info.short_description || 'Meta description will appear here.' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

