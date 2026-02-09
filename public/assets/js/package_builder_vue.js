/* global Vue, axios, toastr */

(function () {
    'use strict';

    const seed = window.packageBuilderSeed || {};
    const initialState = window.packageBuilderState || null;
    const originalState = initialState ? deepClone(initialState) : null;

    function deepClone(source) {
        return source ? JSON.parse(JSON.stringify(source)) : null;
    }

    function cloneGalleryTemplate(size = 4) {
        return Array.from({ length: size }, () => ({
            id: null,
            preview: null,
            token: null,
        }));
    }

    Vue.filter('formatDateTime', function (value) {
        if (!value) return '';
        const date = new Date(value);
        if (Number.isNaN(date.getTime())) return value;
        return date.toLocaleString();
    });

    Vue.filter('formatTime', function (value) {
        if (!value) return '';
        const date = new Date(value);
        if (Number.isNaN(date.getTime())) return value;
        return date.toLocaleTimeString();
    });

    new Vue({
        el: '#packageCreateApp',
        data() {
            return {
                mode: seed.mode || 'create',
                packageId: seed.packageId || null,
                activeTab: 'overview',
                isSubmitting: false,
                hydrating: false,
                formTouched: false,

                overview: {
                    title: '',
                    package_code: '',
                    slug: '',
                    tagline: '',
                    hero_headline: '',
                    hero_subheadline: '',
                    hero_cta_label: '',
                    hero_cta_link: '',
                },

                pricing: {
                    package_price: 0,
                    compare_at_price: null,
                    allow_compare_override: false,
                },

                media: {
                    hero: {
                        id: null,
                        preview: null,
                        token: null,
                    },
                    gallery: cloneGalleryTemplate(),
                },

                items: [],

                info: {
                    status: 'draft',
                    visibility: 'private',
                    publish_at: null,
                    category_id: '',
                    short_description: '',
                    description: '',
                    highlights: [],
                    content_blocks_raw: '',
                },

                seo: {
                    meta_title: '',
                    meta_keywords: '',
                    meta_description: '',
                    meta_image: {
                        id: null,
                        preview: null,
                        token: null,
                    },
                },

                catalog: {
                    searchTerm: '',
                    products: [],
                    loading: false,
                    debounce: null,
                },

                autoSave: {
                    enabled: true,
                    hasStoredData: false,
                    lastSavedTime: null,
                    timer: null,
                    draftKey: seed.draftKey
                        || (seed.mode === 'edit'
                            ? `package_builder_edit_${seed.packageId || 'unknown'}`
                            : 'package_builder_create'),
                },

                masterData: {
                    statuses: seed.statuses || [],
                    visibility: seed.visibility || [],
                    categories: seed.categories || [],
                },

                routes: seed.routes || {},
                csrfToken: seed.csrf || document.querySelector('meta[name="csrf-token"]')?.content || '',
                originalState: originalState,
            };
        },

        computed: {
            itemsTotals() {
                const result = this.items.reduce(
                    (acc, item) => {
                        const unitPrice = parseFloat(item.unit_price) || 0;
                        const comparePrice = parseFloat(item.compare_at_price) || unitPrice;
                        const quantity = parseInt(item.quantity, 10) || 1;

                        acc.itemsTotal += unitPrice * quantity;
                        acc.compareTotal += comparePrice * quantity;
                        return acc;
                    },
                    { itemsTotal: 0, compareTotal: 0 },
                );

                result.itemsTotal = parseFloat(result.itemsTotal.toFixed(2));
                result.compareTotal = parseFloat(result.compareTotal.toFixed(2));
                result.savingsAmount = parseFloat(Math.max(0, result.compareTotal - result.itemsTotal).toFixed(2));
                result.savingsPercent = result.compareTotal > 0
                    ? parseFloat(((result.savingsAmount / result.compareTotal) * 100).toFixed(2))
                    : 0;

                return result;
            },
        },

        watch: {
            overview: {
                handler() {
                    if (this.hydrating) return;
                    this.formTouched = true;
                    this.queueAutoSave();
                },
                deep: true,
            },
            items: {
                handler() {
                    if (this.hydrating) return;
                    this.formTouched = true;
                    this.recalculatePricing();
                    this.queueAutoSave();
                },
                deep: true,
            },
            info: {
                handler() {
                    if (this.hydrating) return;
                    this.formTouched = true;
                    this.queueAutoSave();
                },
                deep: true,
            },
            seo: {
                handler() {
                    if (this.hydrating) return;
                    this.formTouched = true;
                    this.queueAutoSave();
                },
                deep: true,
            },
            pricing: {
                handler() {
                    if (this.hydrating) return;
                    this.formTouched = true;
                    this.recalculatePricing();
                    this.queueAutoSave();
                },
                deep: true,
            },
        },

        created() {
            if (initialState) {
                this.applyInitialState(initialState);
            } else {
                this.resetToBlank();
            }

            this.checkDraft();
            this.loadFeatured();

            window.addEventListener('beforeunload', this.beforeUnloadHandler);
        },

        beforeDestroy() {
            window.removeEventListener('beforeunload', this.beforeUnloadHandler);
            if (this.autoSave.timer) {
                clearTimeout(this.autoSave.timer);
            }
        },

        methods: {
            isActiveTab(tab) {
                return this.activeTab === tab;
            },

            switchTab(tab) {
                this.activeTab = tab;
            },

            generateSlugIfEmpty() {
                if (!this.overview.slug) {
                    this.overview.slug = (this.overview.title || '')
                        .toLowerCase()
                        .replace(/[^a-z0-9]+/g, '-')
                        .replace(/^-+|-+$/g, '');
                }
            },

            triggerHeroImage() {
                this.$refs.heroImageInput?.click();
            },

            async handleHeroImageUpload(event) {
                const file = event.target.files?.[0];
                if (!file) return;

                if (!this.validateImage(file)) {
                    event.target.value = '';
                    return;
                }

                try {
                    toastr.info('Uploading hero image...', 'Please wait');
                    const response = await this.uploadMedia(file);
                    this.media.hero = {
                        id: response.id,
                        preview: response.url,
                        token: response.token,
                    };
                    this.formTouched = true;
                    this.queueAutoSave();
                    toastr.success('Hero image uploaded');
                } catch (error) {
                    console.error(error);
                    toastr.error('Failed to upload hero image');
                } finally {
                    event.target.value = '';
                }
            },

            removeHeroImage() {
                this.media.hero = { id: null, preview: null, token: null };
                this.formTouched = true;
                this.queueAutoSave();
            },

            triggerGallerySlot(index) {
                const ref = this.$refs[`galleryInput${index}`];
                if (Array.isArray(ref) && ref.length) {
                    ref[0].click();
                }
            },

            async handleGalleryUpload(event, index) {
                const file = event.target.files?.[0];
                if (!file) return;

                if (!this.validateImage(file)) {
                    event.target.value = '';
                    return;
                }

                try {
                    toastr.info('Uploading gallery image...', 'Please wait');
                    const response = await this.uploadMedia(file);
                    Vue.set(this.media.gallery, index, {
                        id: response.id,
                        preview: response.url,
                        token: response.token,
                    });
                    this.formTouched = true;
                    this.queueAutoSave();
                    toastr.success('Gallery image uploaded');
                } catch (error) {
                    console.error(error);
                    toastr.error('Failed to upload gallery image');
                } finally {
                    event.target.value = '';
                }
            },

            removeGallerySlot(index) {
                Vue.set(this.media.gallery, index, { id: null, preview: null, token: null });
                this.formTouched = true;
                this.queueAutoSave();
            },

            triggerMetaImage() {
                this.$refs.metaImageInput?.click();
            },

            async handleMetaImageUpload(event) {
                const file = event.target.files?.[0];
                if (!file) return;

                if (!this.validateImage(file)) {
                    event.target.value = '';
                    return;
                }

                try {
                    toastr.info('Uploading meta image...', 'Please wait');
                    const response = await this.uploadMedia(file, { width: 1200, height: 630 });
                    this.seo.meta_image = {
                        id: response.id,
                        preview: response.url,
                        token: response.token,
                    };
                    this.formTouched = true;
                    this.queueAutoSave();
                    toastr.success('Meta image uploaded');
                } catch (error) {
                    console.error(error);
                    toastr.error('Failed to upload meta image');
                } finally {
                    event.target.value = '';
                }
            },

            removeMetaImage() {
                this.seo.meta_image = { id: null, preview: null, token: null };
                this.formTouched = true;
                this.queueAutoSave();
            },

            validateImage(file) {
                const allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif'];
                if (!allowed.includes(file.type)) {
                    toastr.error('Unsupported image format. Use JPG, PNG, WEBP, or GIF.');
                    return false;
                }
                if (file.size > 5 * 1024 * 1024) {
                    toastr.error('Image exceeds 5MB limit.');
                    return false;
                }
                return true;
            },

            async uploadMedia(file, extra = {}) {
                const formData = new FormData();
                formData.append('file', file);
                formData.append('width', extra.width || 800);
                formData.append('height', extra.height || 800);

                const response = await fetch(this.routes.mediaUpload, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                    },
                    body: formData,
                });

                if (!response.ok) {
                    throw new Error('Upload failed');
                }

                return response.json();
            },

            debouncedSearch() {
                if (this.catalog.debounce) {
                    clearTimeout(this.catalog.debounce);
                }
                this.catalog.debounce = setTimeout(() => {
                    this.searchCatalog();
                }, 400);
            },

            async loadFeatured() {
                this.catalog.loading = true;
                try {
                    const response = await axios.get(this.routes.search, { params: { limit: 12 } });
                    this.catalog.products = response.data.results || [];
                } catch (error) {
                    console.error(error);
                    toastr.error('Failed to load catalog products.');
                } finally {
                    this.catalog.loading = false;
                }
            },

            async searchCatalog() {
                this.catalog.loading = true;
                try {
                    const response = await axios.get(this.routes.search, {
                        params: { q: this.catalog.searchTerm, limit: 20 },
                    });
                    this.catalog.products = response.data.results || [];
                } catch (error) {
                    console.error(error);
                    toastr.error('Catalog search failed.');
                } finally {
                    this.catalog.loading = false;
                }
            },

            async selectCatalogProduct(product) {
                if (this.items.some(item => item.product_id === product.id && item.variant_type === 'simple')) {
                    toastr.warning('This product is already added.');
                    return;
                }

                try {
                    const matrixUrl = this.routes.matrix.replace('__ID__', product.id);
                    const response = await axios.get(matrixUrl);
                    this.addCatalogItem(product, response.data);
                    this.formTouched = true;
                    toastr.success(`${product.name} added to package`);
                } catch (error) {
                    console.error(error);
                    toastr.error('Unable to fetch product variants.');
                }
            },

            addCatalogItem(product, matrix) {
                const key = `${product.id}-${Date.now()}-${Math.random().toString(16).slice(2)}`;
                const variantType = matrix.variant_type || 'simple';
                const combinations = (matrix.combinations || []).map(combo => ({
                    id: combo.id,
                    attributes: combo.attributes || {},
                    display: combo.display || this.formatCombinationDisplay(combo.attributes),
                    price: combo.price ?? combo.additional_price ?? product.effective_price,
                    discount_price: combo.discount_price ?? null,
                    stock: combo.stock,
                    sku: combo.sku,
                    barcode: combo.barcode,
                }));

                const item = {
                    key,
                    product_id: product.id,
                    product_name: product.name,
                    sku: product.sku,
                    image_url: product.image_url,
                    variant_type: variantType,
                    variant_options: {
                        combinations,
                        legacy_variants: matrix.legacy_variants || [],
                        colors: matrix.colors || [],
                        sizes: matrix.sizes || [],
                    },
                    variant_combination_id: null,
                    product_variant_id: null,
                    color_id: null,
                    size_id: null,
                    variant_snapshot: {},
                    variant_snapshot_text: '',
                    quantity: 1,
                    unit_price: parseFloat(product.effective_price) || 0,
                    compare_at_price: product.price || product.effective_price || 0,
                };

                if (variantType === 'combination' && combinations.length) {
                    item.variant_combination_id = combinations[0].id;
                    this.applyCombinationPricing(item);
                }

                this.items.push(item);
                this.recalculatePricing();
            },

            formatCombinationDisplay(attributes) {
                if (!attributes || typeof attributes !== 'object') {
                    return 'Variant';
                }
                return Object.values(attributes)
                    .filter(value => value)
                    .join(' • ');
            },

            applyCombinationPricing(item) {
                const selected = item.variant_options.combinations.find(combo => combo.id === item.variant_combination_id);
                if (!selected) {
                    item.variant_snapshot = {};
                    item.variant_snapshot_text = '';
                    return;
                }

                const basePrice = item.unit_price;
                item.unit_price = selected.discount_price ?? selected.price ?? basePrice;
                item.compare_at_price = selected.price ?? item.compare_at_price ?? item.unit_price;
                item.variant_snapshot = selected.attributes || {};
                item.variant_snapshot_text = this.snapshotText(item.variant_snapshot);
            },

            handleVariantCombinationChange(item) {
                this.applyCombinationPricing(item);
                this.formTouched = true;
                this.recalculatePricing();
                this.queueAutoSave();
            },

            syncLegacyVariant(item) {
                let variantId = null;
                const variants = item.variant_options.legacy_variants || [];
                variants.forEach(variant => {
                    const colorMatch = item.color_id ? String(variant.color_id) === String(item.color_id) : true;
                    const sizeMatch = item.size_id ? String(variant.size_id) === String(item.size_id) : true;
                    if (colorMatch && sizeMatch) {
                        variantId = variant.id;
                    }
                });
                item.product_variant_id = variantId;

                item.variant_snapshot = {};
                if (item.color_id) {
                    const color = item.variant_options.colors.find(c => String(c.id) === String(item.color_id));
                    if (color) item.variant_snapshot.Color = color.name;
                }
                if (item.size_id) {
                    const size = item.variant_options.sizes.find(s => String(s.id) === String(item.size_id));
                    if (size) item.variant_snapshot.Size = size.name;
                }
                item.variant_snapshot_text = this.snapshotText(item.variant_snapshot);
                this.formTouched = true;
                this.queueAutoSave();
            },

            snapshotText(snapshot) {
                if (!snapshot || typeof snapshot !== 'object') return '';
                return Object.entries(snapshot)
                    .filter(([, value]) => value)
                    .map(([, value]) => value)
                    .join(' • ');
            },

            enforceItemQuantity(item) {
                if (!item.quantity || item.quantity < 1) {
                    item.quantity = 1;
                }
                this.formTouched = true;
                this.recalculatePricing();
                this.queueAutoSave();
            },

            removeItem(index) {
                this.items.splice(index, 1);
                this.formTouched = true;
                this.recalculatePricing();
                this.queueAutoSave();
            },

            recalculatePricing() {
                const totals = this.itemsTotals;
                this.pricing.package_price = totals.itemsTotal;
                if (!this.pricing.allow_compare_override) {
                    this.pricing.compare_at_price = totals.compareTotal;
                } else if (this.pricing.compare_at_price === null) {
                    this.pricing.compare_at_price = totals.compareTotal;
                }
            },

            addHighlight() {
                if (this.info.highlights.length >= 6) return;
                this.info.highlights.push('');
                this.formTouched = true;
                this.queueAutoSave();
            },

            removeHighlight(index) {
                this.info.highlights.splice(index, 1);
                this.formTouched = true;
                this.queueAutoSave();
            },

            resetToBlank() {
                this.hydrating = true;
                this.overview = {
                    title: '',
                    package_code: '',
                    slug: '',
                    tagline: '',
                    hero_headline: '',
                    hero_subheadline: '',
                    hero_cta_label: '',
                    hero_cta_link: '',
                };
                this.pricing = {
                    package_price: 0,
                    compare_at_price: null,
                    allow_compare_override: false,
                };
                this.media.hero = { id: null, preview: null, token: null };
                this.media.gallery = cloneGalleryTemplate();
                this.items = [];
                this.info = {
                    status: 'draft',
                    visibility: 'private',
                    publish_at: null,
                    category_id: '',
                    short_description: '',
                    description: '',
                    highlights: [],
                    content_blocks_raw: '',
                };
                this.seo = {
                    meta_title: '',
                    meta_keywords: '',
                    meta_description: '',
                    meta_image: { id: null, preview: null, token: null },
                };
                this.hydrating = false;
                this.formTouched = false;
                this.recalculatePricing();
            },

            clearAll() {
                if (this.mode === 'edit' && this.originalState) {
                    if (!confirm('Discard changes and restore the published package?')) return;
                    this.applyInitialState(this.originalState, { skipOriginalUpdate: true });
                    this.removeDraft();
                    toastr.success('Package restored to last saved state.');
                    return;
                }

                if (!confirm('Reset all fields and start over?')) return;
                this.resetToBlank();
                this.removeDraft();
                toastr.success('Form reset.');
            },

            snapshotState() {
                return {
                    overview: deepClone(this.overview),
                    pricing: {
                        package_price: parseFloat(this.pricing.package_price) || 0,
                        compare_at_price: this.pricing.compare_at_price !== null
                            ? parseFloat(this.pricing.compare_at_price)
                            : null,
                        allow_compare_override: this.pricing.allow_compare_override ? 1 : 0,
                    },
                    media: {
                        hero: deepClone(this.media.hero),
                        gallery: deepClone(this.media.gallery),
                    },
                    items: deepClone(this.items),
                    info: {
                        ...deepClone(this.info),
                    },
                    seo: {
                        meta_title: this.seo.meta_title,
                        meta_keywords: this.seo.meta_keywords,
                        meta_description: this.seo.meta_description,
                        meta_image: deepClone(this.seo.meta_image),
                    },
                    timestamp: new Date().toISOString(),
                };
            },

            queueAutoSave() {
                if (!this.autoSave.enabled || this.hydrating) return;
                if (this.autoSave.timer) {
                    clearTimeout(this.autoSave.timer);
                }
                this.autoSave.timer = setTimeout(() => {
                    this.saveDraft();
                }, 2000);
            },

            saveDraft() {
                try {
                    const state = this.snapshotState();
                    localStorage.setItem(this.autoSave.draftKey, JSON.stringify(state));
                    this.autoSave.lastSavedTime = state.timestamp;
                } catch (error) {
                    console.error(error);
                }
            },

            checkDraft() {
                const stored = localStorage.getItem(this.autoSave.draftKey);
                if (!stored) return;

                this.autoSave.hasStoredData = true;
                try {
                    const parsed = JSON.parse(stored);
                    this.autoSave.lastSavedTime = parsed.timestamp || null;
                } catch (error) {
                    console.error(error);
                }
            },

            restoreDraft() {
                const stored = localStorage.getItem(this.autoSave.draftKey);
                if (!stored) return;

                try {
                    const parsed = JSON.parse(stored);
                    this.applyInitialState(parsed, { skipOriginalUpdate: true, skipDraftReset: true });
                    this.autoSave.lastSavedTime = parsed.timestamp || null;
                    this.autoSave.hasStoredData = false;
                    toastr.success('Draft restored.');
                } catch (error) {
                    console.error(error);
                    toastr.error('Failed to restore draft.');
                }
            },

            discardDraft() {
                this.removeDraft();
                this.autoSave.hasStoredData = false;
                toastr.info('Saved draft discarded.');
            },

            removeDraft() {
                localStorage.removeItem(this.autoSave.draftKey);
                this.autoSave.lastSavedTime = null;
            },

            beforeUnloadHandler(event) {
                if (!this.formTouched || this.isSubmitting) return;
                event.preventDefault();
                event.returnValue = '';
            },

            buildPayload() {
                const items = this.items.map(item => ({
                    product_id: item.product_id,
                    quantity: item.quantity,
                    unit_price: parseFloat(item.unit_price) || 0,
                    compare_at_price: parseFloat(item.compare_at_price) || 0,
                    product_variant_id: item.variant_type === 'legacy' ? item.product_variant_id : null,
                    variant_combination_id: item.variant_type === 'combination' ? item.variant_combination_id : null,
                    color_id: item.color_id || null,
                    size_id: item.size_id || null,
                    variant_snapshot: item.variant_snapshot || {},
                }));

                let contentBlocks = [];
                if (this.info.content_blocks_raw) {
                    try {
                        contentBlocks = JSON.parse(this.info.content_blocks_raw);
                    } catch (error) {
                        contentBlocks = { raw: this.info.content_blocks_raw };
                    }
                }

                return {
                    overview: deepClone(this.overview),
                    pricing: {
                        package_price: parseFloat((this.pricing.package_price || 0).toFixed(2)),
                        compare_at_price: this.pricing.allow_compare_override
                            ? parseFloat((this.pricing.compare_at_price || 0).toFixed(2))
                            : parseFloat((this.itemsTotals.compareTotal || 0).toFixed(2)),
                        allow_compare_override: this.pricing.allow_compare_override ? 1 : 0,
                    },
                    media: {
                        hero_image_id: this.media.hero.id,
                        gallery: this.media.gallery.filter(slot => slot.id).map(slot => ({ id: slot.id })),
                    },
                    items,
                    info: {
                        status: this.info.status,
                        visibility: this.info.visibility,
                        publish_at: this.info.publish_at,
                        category_id: this.info.category_id || null,
                        short_description: this.info.short_description,
                        description: this.info.description,
                        highlights: this.info.highlights.filter(Boolean),
                        content_blocks: contentBlocks,
                    },
                    seo: {
                        meta_title: this.seo.meta_title,
                        meta_keywords: this.seo.meta_keywords,
                        meta_description: this.seo.meta_description,
                        meta_image_id: this.seo.meta_image.id,
                    },
                };
            },

            validateBeforeSubmit() {
                if (!this.overview.title) {
                    this.switchTab('overview');
                    toastr.error('Package title is required.');
                    return false;
                }

                if (!this.media.hero.id) {
                    this.switchTab('overview');
                    toastr.error('Hero image is required.');
                    return false;
                }

                if (!this.items.length) {
                    this.switchTab('catalog');
                    toastr.error('Select at least one product for the package.');
                    return false;
                }

                const invalidVariant = this.items.find(item => {
                    if (item.variant_type === 'simple') {
                        return false;
                    }
                    if (item.variant_type === 'combination') {
                        return !item.variant_combination_id;
                    }
                    if (item.variant_type === 'legacy') {
                        return !item.product_variant_id && !(item.color_id || item.size_id);
                    }
                    return false;
                });

                if (invalidVariant) {
                    this.switchTab('catalog');
                    toastr.error('Please configure variants for all selected products.');
                    return false;
                }

                return true;
            },

            async submitPackage() {
                if (this.isSubmitting) {
                    toastr.warning('Submission already in progress.');
                    return;
                }

                if (!this.validateBeforeSubmit()) {
                    return;
                }

                const payload = this.buildPayload();
                const isEdit = this.mode === 'edit';
                const url = isEdit ? this.routes.update : this.routes.store;

                if (!url) {
                    toastr.error('Endpoint not configured.');
                    return;
                }

                this.isSubmitting = true;
                toastr.info(isEdit ? 'Updating package...' : 'Creating package...', 'Please wait');

                try {
                    const response = await axios({
                        method: isEdit ? 'put' : 'post',
                        url,
                        data: payload,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken,
                        },
                    });

                    if (response.data.success) {
                        this.removeDraft();
                        this.formTouched = false;
                        if (isEdit) {
                            this.originalState = deepClone(this.snapshotState());
                        } else {
                            this.resetToBlank();
                        }

                        toastr.success(response.data.message || (isEdit ? 'Package updated successfully.' : 'Package created successfully.'));

                        if (response.data.redirect) {
                            setTimeout(() => {
                                window.location.href = response.data.redirect;
                            }, 800);
                        }
                    } else {
                        toastr.error(response.data.message || 'Request failed.');
                    }
                } catch (error) {
                    console.error(error);
                    const message = error.response?.data?.message || 'An unexpected error occurred.';
                    toastr.error(message);

                    if (error.response?.data?.errors) {
                        Object.values(error.response.data.errors).forEach(errArr => {
                            if (Array.isArray(errArr)) {
                                errArr.forEach(err => toastr.error(err));
                            }
                        });
                    }
                } finally {
                    this.isSubmitting = false;
                }
            },

            applyInitialState(state, options = {}) {
                const snapshot = deepClone(state);
                if (!snapshot) {
                    this.resetToBlank();
                    return;
                }

                this.hydrating = true;

                this.overview = Object.assign({}, this.overview, snapshot.overview || {});

                this.pricing = Object.assign({}, this.pricing, {
                    package_price: snapshot.pricing?.package_price ?? this.pricing.package_price,
                    compare_at_price: snapshot.pricing?.compare_at_price ?? this.pricing.compare_at_price,
                    allow_compare_override: !!snapshot.pricing?.allow_compare_override,
                });

                this.media.hero = Object.assign({ id: null, preview: null, token: null }, snapshot.media?.hero || {});

                const galleryTemplate = cloneGalleryTemplate();
                (snapshot.media?.gallery || []).forEach((slot, index) => {
                    if (index < galleryTemplate.length) {
                        galleryTemplate[index] = Object.assign({}, galleryTemplate[index], slot);
                    }
                });
                this.media.gallery = galleryTemplate;

                this.items = (snapshot.items || []).map(item => ({
                    key: item.key || `existing-${item.product_id}-${Date.now()}-${Math.random().toString(16).slice(2)}`,
                    product_id: item.product_id,
                    product_name: item.product_name,
                    sku: item.sku,
                    image_url: item.image_url,
                    variant_type: item.variant_type || 'simple',
                    variant_options: item.variant_options || {
                        combinations: [],
                        legacy_variants: [],
                        colors: [],
                        sizes: [],
                    },
                    variant_combination_id: item.variant_combination_id || null,
                    product_variant_id: item.product_variant_id || null,
                    color_id: item.color_id || null,
                    size_id: item.size_id || null,
                    variant_snapshot: item.variant_snapshot || {},
                    variant_snapshot_text: item.variant_snapshot_text || this.snapshotText(item.variant_snapshot),
                    quantity: item.quantity || 1,
                    unit_price: parseFloat(item.unit_price) || 0,
                    compare_at_price: parseFloat(item.compare_at_price) || 0,
                }));

                this.info = Object.assign({}, this.info, snapshot.info || {});
                if (!Array.isArray(this.info.highlights)) {
                    this.info.highlights = [];
                }

                this.seo.meta_title = snapshot.seo?.meta_title || '';
                this.seo.meta_keywords = snapshot.seo?.meta_keywords || '';
                this.seo.meta_description = snapshot.seo?.meta_description || '';
                this.seo.meta_image = Object.assign(
                    { id: null, preview: null, token: null },
                    snapshot.seo?.meta_image || {},
                );

                this.hydrating = false;
                this.formTouched = false;
                this.recalculatePricing();

                if (!options.skipDraftReset) {
                    this.autoSave.hasStoredData = false;
                }

                if (!options.skipOriginalUpdate) {
                    this.originalState = deepClone(snapshot);
                }
            },
        },
    });
})();

