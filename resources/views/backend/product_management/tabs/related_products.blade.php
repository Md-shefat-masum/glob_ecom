<div class="row g-4">

    <!-- Similar Products -->
    <div class="col-12 mb-3">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <div>
                <h6 class="mb-0">Similar Products</h6>
                <small class="text-muted">Shown to customers exploring alternatives to this product.</small>
            </div>
        </div>
        <select id="similarProductsSelect" class="form-select select2-related" multiple></select>

        <div class="mt-3" v-if="related && related.similar && related.similar.length">
            <div class="list-group shadow-sm">
                <div class="list-group-item d-flex align-items-center justify-content-between"
                     v-for="item in related.similar"
                     :key="`similar-${item.id}`">
                    <div>
                        <strong>@{{ item.name }}</strong>
                        <div class="text-muted small">৳@{{ formatCurrency(item.price) }}</div>
                    </div>
                    <button type="button"
                            class="btn btn-outline-danger btn-sm"
                            @click="removeRelatedItem('similar', item.id)">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Recommended Products -->
    <div class="col-12 mb-3">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <div>
                <h6 class="mb-0">Recommended Together</h6>
                <small class="text-muted">Great complements frequently purchased with this product.</small>
            </div>
        </div>
        <select id="recommendedProductsSelect" class="form-select select2-related" multiple></select>

        <div class="mt-3" v-if="related && related.recommended && related.recommended.length">
            <div class="list-group shadow-sm">
                <div class="list-group-item d-flex align-items-center justify-content-between"
                     v-for="item in related.recommended"
                     :key="`recommended-${item.id}`">
                    <div>
                        <strong>@{{ item.name }}</strong>
                        <div class="text-muted small">৳@{{ formatCurrency(item.price) }}</div>
                    </div>
                    <button type="button"
                            class="btn btn-outline-danger btn-sm"
                            @click="removeRelatedItem('recommended', item.id)">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add-on Products -->
    <div class="col-12 mb-3">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <div>
                <h6 class="mb-0">Add-on Products</h6>
                <small class="text-muted">
                    Optional extras whose prices can be bundled with the main product.
                </small>
            </div>
        </div>
        <select id="addonProductsSelect" class="form-select select2-related" multiple></select>

        <div class="mt-3" v-if="related && related.addons && related.addons.length">
            <div class="list-group shadow-sm">
                <div class="list-group-item"
                     v-for="item in related.addons"
                     :key="`addon-${item.id}`">
                    <div class="d-flex align-items-start justify-content-between gap-3">
                        <div>
                            <strong>@{{ item.name }}</strong>
                            <div class="text-muted small">
                                Add-on price: ৳@{{ formatCurrency(item.price) }}
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="checkbox"
                                       :id="`addon-default-${item.id}`"
                                       v-model="item.is_default">
                                <label class="form-check-label small text-nowrap" :for="`addon-default-${item.id}`">
                                    Show by default
                                </label>
                            </div>
                            <button type="button"
                                    class="btn btn-outline-danger btn-sm"
                                    @click="removeRelatedItem('addons', item.id)">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="alert alert-info mt-3 mb-0" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            When an add-on is marked as <strong>Show by default</strong>, the product price displayed to customers will include the add-on total.
        </div>
    </div>

</div>

