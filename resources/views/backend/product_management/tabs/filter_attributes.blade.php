<div class="row g-3">
    <div class="col-lg-5">
        <div class="card border-info shadow-sm">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0">
                    <i class="fas fa-filter"></i> Filter Variants (For Category Products Page)
                </h6>
            </div>
            <div class="card-body">
                <div class="alert alert-warning mb-3">
                    <i class="fas fa-exclamation-triangle"></i> 
                    <strong>Important:</strong> These filter variants are <strong>ONLY</strong> used for filtering products on the 
                    <strong>Category Products Page</strong>. They do NOT affect cart, POS sale, or stock management.
                </div>
                <p class="text-muted small">
                    These attributes power storefront filters. They never impact stock or variant combinations and can be
                    used even when the product is simple.
                </p>

                <div v-if="getFilterRelatedGroups().length === 0" class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    No filter-only variant groups are active yet. Configure them from Variant Group settings first.
                </div>

                <div v-else>
                    <div class="mb-3">
                        <label class="form-label d-block mb-1 small">
                            <strong>Select Attribute Groups</strong>
                        </label>
                        <select id="filterVariantGroupSelector"
                            class="form-select form-select-sm select2-filter-variant-selector"
                            multiple
                            v-model="selectedFilterGroupSlugs">
                            <option v-for="group in getFilterRelatedGroups()" :key="group.id"
                                :value="group.slug">
                                @{{ group.name }}
                            </option>
                        </select>
                        <small class="text-muted d-block mt-2">
                            Pick any combination of filter-only groups (Pattern, Fit, Material, etc.).
                        </small>
                    </div>

                    <div v-if="selectedFilterGroupSlugs && selectedFilterGroupSlugs.length > 0" class="pt-2 border-top">
                        <div class="mb-3" v-for="groupSlug in selectedFilterGroupSlugs" :key="groupSlug">
                            <template v-for="group in variantGroups">
                                <template v-if="group.slug === groupSlug">
                                    <label class="form-label d-block mb-1 small">
                                        <strong>@{{ group.name }}</strong>
                                    </label>
                                    <select :id="'filterVariantGroup_' + group.slug"
                                        class="form-select form-select-sm select2-filter-variant-values"
                                        multiple
                                        v-model="selectedFilterGroups[group.slug]">
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
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card mb-3">
            <div class="card-header bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <h6 class="mb-0">
                        <i class="fas fa-list"></i> Selected Filter Values
                    </h6>
                    <span class="badge bg-info">@{{ Object.keys(selectedFilterGroups).length }} groups</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div v-if="Object.keys(selectedFilterGroups).length === 0" class="p-4 text-center text-muted">
                    <i class="fas fa-filter fa-2x mb-2"></i>
                    <p class="mb-0">No filter attributes selected yet. Pick a group on the left.</p>
                </div>
                <div v-else class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="32%">Attribute Name</th>
                                <th>Selected Values</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(valueIds, groupSlug) in selectedFilterGroups" :key="groupSlug">
                                <td>
                                    <strong>@{{ getVariantGroup(groupSlug)?.name || groupSlug }}</strong>
                                </td>
                                <td>
                                    <span v-if="valueIds && valueIds.length > 0">
                                        <span v-for="valueId in valueIds" :key="valueId"
                                            class="badge bg-info me-1 mb-1">
                                            @{{ getKeyName(groupSlug, valueId) }}
                                        </span>
                                    </span>
                                    <span v-else class="text-muted small">No values selected</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-light">
                <small class="text-muted">
                    <i class="fas fa-info-circle"></i>
                    Filter attributes sync to the storefront immediately after saving.
                </small>
            </div>
        </div>

        <div class="card border-info">
            <div class="card-header bg-light d-flex align-items-center">
                <i class="fas fa-project-diagram text-info me-2"></i>
                <div>
                    <h6 class="mb-0">Mapping Preview</h6>
                    <small class="text-muted">Each filter value is stored per scope</small>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Scope</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Category</td>
                                <td>
                                    <template v-if="product.category_id">
                                        <span class="badge bg-success me-2">Linked</span>
                                        <strong>@{{ getCategoryNameById(product.category_id) || ('#' + product.category_id) }}</strong>
                                    </template>
                                    <span v-else class="text-muted">Select a category in Basic Info</span>
                                </td>
                            </tr>
                            <tr>
                                <td>Subcategory</td>
                                <td>
                                    <template v-if="product.subcategory_id">
                                        <span class="badge bg-success me-2">Linked</span>
                                        <strong>@{{ getSubcategoryNameById(product.subcategory_id) || ('#' + product.subcategory_id) }}</strong>
                                    </template>
                                    <span v-else class="text-muted">Optional — choose a subcategory to extend mapping</span>
                                </td>
                            </tr>
                            <tr>
                                <td>Child Category</td>
                                <td>
                                    <template v-if="product.childcategory_id">
                                        <span class="badge bg-success me-2">Linked</span>
                                        <strong>@{{ getChildCategoryNameById(product.childcategory_id) || ('#' + product.childcategory_id) }}</strong>
                                    </template>
                                    <span v-else class="text-muted">Optional — select a child category if available</span>
                                </td>
                            </tr>
                            <tr>
                                <td>Brand</td>
                                <td>
                                    <template v-if="product.brand_id">
                                        <span class="badge bg-success me-2">Linked</span>
                                        <strong>@{{ getBrandNameById(product.brand_id) || ('#' + product.brand_id) }}</strong>
                                    </template>
                                    <span v-else class="text-muted">Optional — brand filters sync when selected</span>
                                </td>
                            </tr>
                            <tr>
                                <td>Product</td>
                                <td>
                                    <span class="badge bg-success me-2">Always</span>
                                    <strong>@{{ product.name || 'New Product' }}</strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="alert alert-info mt-3 mb-0">
                    <i class="fas fa-database me-2"></i>
                    Each saved value writes dedicated rows for category, subcategory, child category, brand, and product
                    scopes, ensuring storefront filters stay in sync without manual tagging.
                </div>
            </div>
        </div>
    </div>
</div>

