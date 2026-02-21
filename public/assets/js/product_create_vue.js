// Product Create Vue.js Component
new Vue({
    el: '#productCreateApp',
    data: {
        // Current active tab
        activeTab: 'basic_info',
        
        // Product basic info
        product: {
            name: '',
            slug: '',
            code: '',
            sku: '',
            barcode: '',
            hsn_code: '',
            category_id: '',
            subcategory_id: '',
            childcategory_id: '',
            brand_id: '',
            model_id: '',
            unit_id: '',
            flag_id: '',
            short_description: '',
            description: '',
            specification: '',
            warranty_policy: '',
            size_chart: '',
            video_url: '',
            tags: '',
            contact_number: '',
            contact_description: '',
            need_contact_during_order: 0,
            availability_status: 'in_stock',
            status: 0,
            is_demo: 0,
            is_package: 0,
            // Media file IDs
            product_image_id: null,
            gallery_image_ids: '',
            // Image URLs
            product_image_url: null,
            gallery_images: Array(6).fill(null).map(() => ({ id: null, url: null, token: null }))
        },

        availabilityOptions: [
            { value: 'in_stock', label: 'In Stock' },
            { value: 'out_of_stock', label: 'Out of Stock' },
            { value: 'pre_order', label: 'Pre Order' },
        ],

        // Pricing data
        pricing: {
            price: 0,
            discount_price: 0,
            discount_percent: 0,
            reward_points: 0,
            low_stock: 10,
            min_order_qty: 1,
            max_order_qty: null,
            has_unit_based_price: 0
        },

        // Unit-based pricing
        unitPricing: [],
        newUnitPrice: {
            unit_id: '',
            unit_title: '',
            unit_value: 1,
            unit_label: '',
            price: 0,
            discount_price: 0,
            discount_percent: 0,
            reward_points: 0,
            is_default: 0
        },

        // Variant management (NEW STRUCTURE)
        // NOTE: Variants are used for Add to Cart and POS Sale
        hasVariants: false,
        variantGroups: [], // All available groups from DB
        
        // Active variant tab
        activeVariantTab: 'colorSize', // 'colorSize', 'otherVariants'
        
        // Color & Size variants (OPTIONAL FEATURE - For Cart & POS Sale)
        // This is a separate feature from Other Variants
        // Used when customers select color and size when adding to cart
        selectedColorSizeColors: [],
        selectedColorSizeSizes: [],
        colorSizeCombinations: [],
        colorImages: {}, // Store images per color: { colorId: { image_id, image_token, image_path, image_url } }
        commonColorSizeValues: {
            price: null,
            discount_price: null,
            additional_price: 0,
            low_stock_alert: 10
        },
        
        // Other variants (For Cart & POS Sale)
        // This is a separate feature from Color & Size
        // Used for variant groups like storage, RAM, processor, etc.
        selectedOtherStockGroupSlugs: [],
        selectedOtherVariantGroups: {},
        otherVariantsCombinations: [],
        commonOtherVariantValues: {
            price: null,
            discount_price: null,
            additional_price: 0,
            low_stock_alert: 10
        },
        
        // Stock-related variants (creates combinations) - Legacy support
        selectedStockGroupSlugs: [], // e.g., ['material', 'weight', 'storage']
        selectedVariantGroups: {}, // Values for stock groups
        // Example: { 'color': [1, 2, 3], 'size': [4, 5], 'material': [10, 11] }
        variantCombinations: [],  // Generated combinations (merged from Color & Size + Other Variants)
        showSingleVariants: true,
        showCombinationVariants: true,
        
        // Common values for bulk setting variants
        commonVariantValues: {
            price: null,
            discount_price: null,
            additional_price: 0,
            low_stock_alert: 10
        },
        
        // Filter-related variants (ONLY for Category Products Page filtering - NOT for Cart/POS)
        // This is a completely separate feature from variants above
        // Used only for filtering products on category products page
        selectedFilterGroupSlugs: [], // e.g., ['pattern', 'fit', 'style']
        selectedFilterGroups: {}, // Values for filter groups
        // Example: { 'pattern': [1, 2], 'fit': [3, 4] }

        // Attributes
        attributes: {
            material: '',
            style: '',
            pattern: '',
            fit: '',
            fabrication: '',
            dimensions: {
                length: '',
                width: '',
                height: '',
                weight: '',
                capacity: ''
            },
            measurements: {
                chest: '',
                waist: '',
                sleeve: ''
            }
        },

        // Shipping info
        shippingInfo: {
            weight: '',
            dimension_unit: 'cm',
            package_type: 'Box',
            is_fragile: 0,
            returnable: 1,
            return_policy_days: 7
        },

        // Tax info
        taxInfo: {
            tax_class_id: null,
            tax_percent: 0
        },

        // Meta SEO
        metaInfo: {
            title: '',
            keywords: '',
            description: '',
            image: null
        },

        // FAQ
        faq: [],

        // Special offer
        specialOffer: {
            is_special: 0,
            offer_end_time: ''
        },

        // Images (old - for backward compatibility)
        productImage: null,
        productImagePreview: null,
        galleryImages: [],
        galleryPreviews: [],

        // Dynamic data
        subcategories: [],
        childCategories: [],
        models: [],
        
        // Available data from backend
        categories: [],
        brands: [],
        units: [],
        colors: [],
        sizes: [],
        flags: [],
        
        // Category-based variant suggestions
        categoryVariantMap: {},
        suggestedVariantGroups: [],
        showColorSizeTab: true, // Default to true, will be updated based on category

        // Validation
        errors: {},
        isSubmitting: false,
        productId: null,
        routes: {
            checkSlug: '',
            productSearch: ''
        },
        slugPrefix: '',
        slugState: {
            manual: false,
            checking: false,
            error: '',
            available: false,
            lastValue: ''
        },
        related: {
            similar: [],
            recommended: [],
            addons: []
        },
        notification: {
            title: '',
            description: '',
            button_text: '',
            button_url: '',
            image_id: null,
            image_url: null,
            image_token: null,
            image_path: null,
            is_show: false,
        },
        suppressRelatedEvents: false,
        
        // Error handling and resend
        lastSubmissionError: null,
        lastSubmissionData: null,
        showResendButton: false,
        submissionCount: 0,

        // LocalStorage
        localStorageKey: 'product_create_form_data',
        hasStoredData: false,
        lastSavedTime: null,

        // Category creation modal
        newCategory: {
            name: '',
            slug: '',
            status: 1
        },
        categoryErrors: {},
        categoryCreating: false,

        // Subcategory creation modal
        newSubcategory: {
            category_id: '',
            name: '',
            slug: '',
            status: 1
        },
        subcategoryErrors: {},
        subcategoryCreating: false,

        // Child Category creation modal
        newChildCategory: {
            category_id: '',
            subcategory_id: '',
            name: '',
            slug: '',
            status: 1
        },
        childCategoryErrors: {},
        childCategoryCreating: false,

        // Brand creation modal
        newBrand: {
            name: '',
            slug: '',
            status: 1
        },
        brandErrors: {},
        brandCreating: false,

        // Model creation modal
        newModel: {
            brand_id: '',
            name: '',
            code: '',
            slug: '',
            status: 1
        },
        modelErrors: {},
        modelCreating: false,

        // Unit creation modal
        newUnit: {
            name: '',
            status: 1
        },
        unitErrors: {},
        unitCreating: false,

        // Flag creation modal
        newFlag: {
            name: '',
            icon: null,
            iconPreview: null,
            status: 1
        },
        flagErrors: {},
        flagCreating: false
    },

    mounted() {
        // Initialize data from blade template
        if (typeof window.productData !== 'undefined') {
            this.categories = window.productData.categories || [];
            this.brands = window.productData.brands || [];
            this.units = window.productData.units || [];
            this.colors = window.productData.colors || [];
            this.sizes = window.productData.sizes || [];
            this.flags = window.productData.flags || [];
            this.variantGroups = window.productData.variantGroups || [];
            this.routes.checkSlug = window.productData.slugCheckRoute || this.routes.checkSlug;
            this.slugPrefix = window.productData.slugBaseUrl || this.slugPrefix;
            this.routes.productSearch = window.productData.productSearchRoute || this.routes.productSearch;
            this.productId = window.productData.productId || null;
            this.categoryVariantMap = window.productData.categoryVariantMap || {};
            
            if (this.variantGroups.length > 0) {
                // Optionally inspect variant groups in dev tools if needed
            }
            
            // Apply category-based variant suggestions on initial load if category is selected
            if (this.product.category_id) {
                this.onCategoryChange();
            }
        }

        // Check for stored data
        this.checkStoredData();

        // Auto-generate SKU on name change
        this.watchNameForSlug();
        this.watchNameForSku();
        this.resetSlugState(this.slugState.manual);

        // Setup auto-save watchers
        this.setupAutoSave();

        // Add beforeunload warning
        this.setupUnloadWarning();

        // Initialize Select2 after DOM is ready
        this.$nextTick(() => {
            this.initializeSelect2();
            this.initializeRelatedSelects();
            this.syncAllRelatedSelects();
        });
        },

    updated() {
        // Reinitialize Select2 when switching to stock or filter tabs
        if (this.activeTab === 'variants' && this.hasVariants) {
            this.$nextTick(() => {
                this.initializeSelect2();
            });
        }

        if (this.activeTab === 'filters') {
            this.$nextTick(() => {
                this.initializeSelect2();
            });
        }
    },

    computed: {
        slugPreviewPrefix() {
            if (!this.slugPrefix) {
                return '';
            }
            return this.slugPrefix.endsWith('/') ? this.slugPrefix : `${this.slugPrefix}/`;
        },
        computedVariants() {
            return this.variantCombinations
                .map((combo, index) => {
                    const groupCount = combo.variant_values ? Object.keys(combo.variant_values).length : 0;
                    return {
                        combo,
                        index,
                        groupCount
                    };
                })
                .filter(item => {
                    if (item.groupCount <= 1) {
                        return this.showSingleVariants;
                    }
                    return this.showCombinationVariants;
                });
        },
        // Safe accessor for showColorSizeTab to prevent undefined errors
        shouldShowColorSizeTab() {
            return this.showColorSizeTab !== false && this.showColorSizeTab !== undefined;
        }
    },

    watch: {
        hasVariants(newValue) {
            if (!newValue) {
                // Reset all variant data when disabled
                this.selectedStockGroupSlugs = [];
                this.selectedVariantGroups = {};
                this.variantCombinations = [];
                
                // Clear Select2 selections
                this.$nextTick(() => {
                    if ($('#colorSelect').length) {
                        $('#colorSelect').val(null).trigger('change.select2');
                    }
                    if ($('#sizeSelect').length) {
                        $('#sizeSelect').val(null).trigger('change.select2');
                    }
                    if ($('#stockVariantGroupSelector').length) {
                        $('#stockVariantGroupSelector').val(null).trigger('change.select2');
                    }
                });
            } else {
                // Reinitialize Select2 when variants are enabled
                this.$nextTick(() => {
                    this.initializeSelect2();
                });
            }
        },

        selectedStockGroupSlugs(newSlugs) {
            // Remove deselected groups from selectedVariantGroups
            for (let slug in this.selectedVariantGroups) {
                if (!newSlugs.includes(slug) && slug !== 'color' && slug !== 'size') {
                    this.$delete(this.selectedVariantGroups, slug);
                }
            }
            
            // Reinitialize Select2 for stock variant value selects
            this.$nextTick(() => {
                this.initializeStockVariantValueSelects();
            });
        },

        selectedFilterGroupSlugs(newSlugs) {
            // Remove deselected groups from selectedFilterGroups
            for (let slug in this.selectedFilterGroups) {
                if (!newSlugs.includes(slug)) {
                    this.$delete(this.selectedFilterGroups, slug);
                }
            }
            
            // Reinitialize Select2 for filter variant value selects
            this.$nextTick(() => {
                this.initializeFilterVariantValueSelects();
            });
        },

        selectedVariantGroups: {
            handler() {
                this.generateVariantCombinations();
            },
            deep: true
        },

        // Watchers for Color & Size tab
        selectedColorSizeColors: {
            handler() {
                this.generateColorSizeCombinations();
            },
            deep: true
        },
        selectedColorSizeSizes: {
            handler() {
                this.generateColorSizeCombinations();
            },
            deep: true
        },

        // Watchers for Other Variants tab
        selectedOtherStockGroupSlugs(newSlugs) {
            // Remove deselected groups from selectedOtherVariantGroups
            for (let slug in this.selectedOtherVariantGroups) {
                if (!newSlugs.includes(slug)) {
                    this.$delete(this.selectedOtherVariantGroups, slug);
                }
            }
            // Ensure each selected group has a key (empty array) so single-group selection is tracked
            if (newSlugs && newSlugs.length > 0) {
                newSlugs.forEach(slug => {
                    if (!this.selectedOtherVariantGroups.hasOwnProperty(slug)) {
                        this.$set(this.selectedOtherVariantGroups, slug, []);
                    }
                });
            }
            // Reinitialize Select2 for other variant value selects
            this.$nextTick(() => {
                this.initializeOtherVariantValueSelects();
            });
        },
        selectedOtherVariantGroups: {
            handler() {
                this.generateOtherVariantCombinations();
            },
            deep: true
        },

        activeTab(newTab) {
            // Reinitialize Select2 when switching to variants tab
            if (newTab === 'variants') {
                this.$nextTick(() => {
                    this.initializeSelect2();
                });
            }

            if (newTab === 'filters') {
                this.$nextTick(() => {
                    this.initializeSelect2();
                });
            }
            
            // Initialize Summernote when switching to content tab
            if (newTab === 'content') {
                this.$nextTick(() => {
                    if (typeof initializeSummernote === 'function') {
                        initializeSummernote();
                    }
                });
            }
        },
        activeVariantTab(newTab) {
            // Reinitialize Select2 when switching variant tabs
            this.$nextTick(() => {
                this.initializeSelect2();
            });
        },
        
        'product.category_id'(newCategoryId, oldCategoryId) {
            if (newCategoryId !== oldCategoryId) {
                this.onCategoryChange();
            }
        }
    },

    beforeDestroy() {
        // Cleanup Select2 instances before Vue instance is destroyed
        this.destroySelect2();
    },

    methods: {
        generateSlug(value) {
            if (!value) {
                return '';
            }
            return value
                .toString()
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                .toLowerCase()
                .replace(/[^a-z0-9\s-]/g, ' ')
                .trim()
                .replace(/[\s_-]+/g, '-')
                .replace(/^-+|-+$/g, '');
        },
        watchNameForSlug() {
            this.$watch('product.name', (newName) => {
                if (this.slugState.manual) {
                    return;
                }
                const generated = this.generateSlug(newName || '');
                if (generated !== this.product.slug) {
                    this.product.slug = generated;
                }
                this.slugState.manual = false;
                this.slugState.error = '';
                this.slugState.available = false;
                this.slugState.lastValue = '';
            });
        },
        syncSlugManualFlag() {
            const generated = this.generateSlug(this.product.name || '');
            this.slugState.manual = !!this.product.slug && this.product.slug !== generated;
        },
        async handleNameBlur() {
            if (!this.product.name) {
                return;
            }
            if (!this.slugState.manual || !this.product.slug) {
                this.product.slug = this.generateSlug(this.product.name || '');
            }
            this.syncSlugManualFlag();
            await this.ensureSlugValid();
        },
        handleSlugInput() {
            const sanitized = this.generateSlug(this.product.slug || '');
            if (sanitized !== this.product.slug) {
                this.product.slug = sanitized;
            }
            this.slugState.manual = true;
            this.slugState.error = '';
            this.slugState.available = false;
            this.slugState.lastValue = '';
        },
        async handleSlugBlur() {
            this.product.slug = this.generateSlug(this.product.slug || '');
            await this.ensureSlugValid(true);
        },
        async ensureSlugValid(force = false) {
            if (!this.product.slug) {
                this.slugState.error = 'Product URL is required.';
                this.slugState.available = false;
                return false;
            }
            if (!this.routes.checkSlug) {
                this.slugState.error = '';
                this.slugState.available = true;
                this.slugState.lastValue = this.product.slug;
                return true;
            }
            if (!force && this.product.slug === this.slugState.lastValue && this.slugState.available && !this.slugState.error) {
                return true;
            }
            return await this.triggerSlugCheck(true);
        },
        async triggerSlugCheck(force = false) {
            const slug = this.product.slug;
            if (!slug) {
                this.slugState.error = 'Product URL is required.';
                this.slugState.available = false;
                return false;
            }
            if (!this.routes.checkSlug) {
                this.slugState.error = '';
                this.slugState.available = true;
                this.slugState.lastValue = slug;
                return true;
            }
            if (!force && slug === this.slugState.lastValue && this.slugState.available && !this.slugState.error) {
                return true;
            }
            this.slugState.checking = true;
            try {
                const response = await axios.post(this.routes.checkSlug, {
                    slug,
                    ignore_id: this.productId || null,
                }, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    }
                });
                const payload = response.data?.data || {};
                if (payload.slug && payload.slug !== this.product.slug) {
                    this.product.slug = payload.slug;
                }
                const available = !!payload.available;
                this.slugState.available = available;
                this.slugState.error = available ? '' : 'This product URL is already in use.';
                this.slugState.lastValue = this.product.slug;
                return available;
            } catch (error) {
                let message = 'Unable to verify product URL.';
                if (error.response?.status === 422) {
                    const errors = error.response.data.errors || {};
                    if (Array.isArray(errors.slug) && errors.slug.length) {
                        message = errors.slug[0];
                    } else if (error.response.data.message) {
                        message = error.response.data.message;
                    }
                } else if (error.response?.data?.message) {
                    message = error.response.data.message;
                }
                this.slugState.error = message;
                this.slugState.available = false;
                return false;
            } finally {
                this.slugState.checking = false;
            }
        },
        resetSlugState(manual = false) {
            this.slugState.manual = manual;
            this.slugState.checking = false;
            this.slugState.error = '';
            this.slugState.available = false;
            this.slugState.lastValue = '';
        },

        // Tab management
        switchTab(tabName) {
            this.activeTab = tabName;
            if (tabName === 'related') {
                this.$nextTick(() => {
                    this.initializeRelatedSelects();
                    this.syncAllRelatedSelects();
                });
            }
        },

        isActiveTab(tabName) {
            return this.activeTab === tabName;
        },

        // FAQ management
        addFaqItem() {
            this.faq.push({
                question: '',
                answer: ''
            });
        },
        removeFaqItem(index) {
            if (this.faq.length > index) {
                this.faq.splice(index, 1);
            }
        },

        // Dynamic data loading
        loadSubcategories() {
            if (!this.product.category_id) {
                this.subcategories = [];
                this.childCategories = [];
                this.onCategoryChange();
                return;
            }

            axios.get(`/product-management/get-subcategories/${this.product.category_id}`)
                .then(response => {
                    this.subcategories = response.data;
                    // Reinitialize subcategory Select2 with new data
                    this.$nextTick(() => {
                        this.reinitializeDynamicSelects();
                    });
                    // Apply category-based variant suggestions
                    this.onCategoryChange();
                })
                .catch(error => {
                    console.error('Error loading subcategories:', error);
                    toastr.error('Error loading subcategories');
                });
        },
        
        onCategoryChange() {
            if (!this.product.category_id) {
                this.showColorSizeTab = true;
                this.suggestedVariantGroups = [];
                return;
            }

            // Find selected category
            const selectedCategory = this.categories.find(cat => cat.id == this.product.category_id);
            if (!selectedCategory) {
                return;
            }

            // Determine category type from name/slug
            const categoryName = (selectedCategory.name || '').toLowerCase();
            const categorySlug = (selectedCategory.slug || '').toLowerCase();
            const combined = categoryName + ' ' + categorySlug;

            // Check category variant map
            let categoryType = null;
            for (let type in this.categoryVariantMap) {
                if (type === 'getCategoryType') continue;
                const config = this.categoryVariantMap[type];
                if (config && config.category_keywords) {
                    for (let keyword of config.category_keywords) {
                        if (combined.indexOf(keyword.toLowerCase()) !== -1) {
                            categoryType = type;
                            break;
                        }
                    }
                    if (categoryType) break;
                }
            }

            if (categoryType && this.categoryVariantMap[categoryType]) {
                const config = this.categoryVariantMap[categoryType];
                this.showColorSizeTab = config.show_color_size_tab || false;
                this.suggestedVariantGroups = config.default_groups || [];
                
                // If Color+Size tab is hidden, switch to Other Variants tab
                if (!this.showColorSizeTab && this.activeVariantTab === 'colorSize') {
                    this.activeVariantTab = 'otherVariants';
                }
            } else {
                // Default behavior - show Color+Size tab
                this.showColorSizeTab = true;
                this.suggestedVariantGroups = [];
            }
        },

        loadChildCategories() {
            if (!this.product.subcategory_id) {
                this.childCategories = [];
                return;
            }

            axios.get(`/product-management/get-child-categories/${this.product.subcategory_id}`)
                .then(response => {
                    this.childCategories = response.data;
                    // Reinitialize child category Select2 with new data
                    this.$nextTick(() => {
                        this.reinitializeDynamicSelects();
                    });
                })
                .catch(error => {
                    console.error('Error loading child categories:', error);
                    toastr.error('Error loading child categories');
                });
        },

        loadModels() {
            if (!this.product.brand_id) {
                this.models = [];
                return;
            }

            axios.get(`/product-management/get-models/${this.product.brand_id}`)
                .then(response => {
                    this.models = response.data;
                    // Reinitialize model Select2 with new data
                    this.$nextTick(() => {
                        this.reinitializeDynamicSelects();
                    });
                })
                .catch(error => {
                    console.error('Error loading models:', error);
                    toastr.error('Error loading models');
                });
        },

        // Image handling
        handleProductImage(event) {
            const file = event.target.files[0];
            if (file) {
                this.productImage = file;
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.productImagePreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },

        handleGalleryImages(event) {
            const files = Array.from(event.target.files);
            files.forEach(file => {
                this.galleryImages.push(file);
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.galleryPreviews.push(e.target.result);
                };
                reader.readAsDataURL(file);
            });
        },

        removeGalleryImage(index) {
            this.galleryImages.splice(index, 1);
            this.galleryPreviews.splice(index, 1);
        },

        // Video URL normalization
        normalizeVideoUrl() {
            const rawValue = (this.product.video_url || '').trim();

            if (!rawValue) {
                return;
            }

            const embedUrl = this.extractEmbedUrl(rawValue);

            if (embedUrl) {
                this.product.video_url = embedUrl;
            } else {
                toastr.warning('Please enter a valid YouTube or Vimeo URL or iframe embed code.');
                this.product.video_url = '';
            }
        },

        extractEmbedUrl(rawInput) {
            if (!rawInput) {
                return null;
            }

            let value = rawInput.trim();

            const iframeMatch = value.match(/<iframe[^>]+src=["']([^"']+)["']/i);
            if (iframeMatch && iframeMatch[1]) {
                value = iframeMatch[1];
            }

            if (value.startsWith('//')) {
                value = `https:${value}`;
            }

            if (!/^https?:\/\//i.test(value)) {
                value = `https://${value}`;
            }

            let parsedUrl;

            try {
                parsedUrl = new URL(value);
            } catch (error) {
                return null;
            }

            const hostname = parsedUrl.hostname.replace(/^www\./i, '').toLowerCase();
            const pathSegments = parsedUrl.pathname.split('/').filter(Boolean);

            if (hostname === 'youtu.be' || hostname.includes('youtube')) {
                return this.buildYouTubeEmbedUrl(hostname, pathSegments, parsedUrl.searchParams);
            }

            if (hostname === 'vimeo.com' || hostname === 'player.vimeo.com') {
                return this.buildVimeoEmbedUrl(pathSegments, parsedUrl.searchParams);
            }

            return null;
        },

        buildYouTubeEmbedUrl(hostname, pathSegments, searchParams) {
            let videoId = '';
            let queryParams = new URLSearchParams();

            if (hostname === 'youtu.be') {
                videoId = pathSegments[0] || '';
                queryParams = this.copyYouTubeEmbedParams(searchParams);
            } else {
                const primarySegment = pathSegments[0] || '';

                if (!primarySegment || primarySegment === 'watch') {
                    videoId = searchParams.get('v') || '';
                    queryParams = this.copyYouTubeEmbedParams(searchParams);
                } else if (primarySegment === 'embed' && pathSegments[1]) {
                    videoId = pathSegments[1];
                    queryParams = new URLSearchParams(searchParams);
                } else if ((primarySegment === 'shorts' || primarySegment === 'live') && pathSegments[1]) {
                    videoId = pathSegments[1];
                    queryParams = this.copyYouTubeEmbedParams(searchParams);
                } else {
                    return null;
                }
            }

            if (!videoId) {
                return null;
            }

            videoId = videoId.split('?')[0].split('&')[0];
            videoId = videoId.replace(/[^0-9a-zA-Z_-]/g, '');

            if (!videoId) {
                return null;
            }

            const queryString = queryParams.toString();
            const baseUrl = `https://www.youtube.com/embed/${videoId}`;

            return queryString ? `${baseUrl}?${queryString}` : baseUrl;
        },

        buildVimeoEmbedUrl(pathSegments, searchParams) {
            if (!pathSegments.length) {
                return null;
            }

            let videoId = '';

            const videoIndex = pathSegments.indexOf('video');
            if (videoIndex !== -1 && pathSegments[videoIndex + 1]) {
                videoId = pathSegments[videoIndex + 1];
            } else {
                videoId = pathSegments[pathSegments.length - 1];
            }

            videoId = videoId.replace(/[^0-9]/g, '');

            if (!videoId) {
                return null;
            }

            const queryString = searchParams.toString();
            const baseUrl = `https://player.vimeo.com/video/${videoId}`;

            return queryString ? `${baseUrl}?${queryString}` : baseUrl;
        },

        copyYouTubeEmbedParams(searchParams) {
            const params = new URLSearchParams();

            const start = this.parseYouTubeStartTime(searchParams.get('start') || searchParams.get('t'));
            if (start) {
                params.set('start', start);
            }

            const end = this.parseYouTubeStartTime(searchParams.get('end'));
            if (end) {
                params.set('end', end);
            }

            ['list', 'playlist', 'autoplay', 'loop', 'mute', 'rel', 'si'].forEach((key) => {
                if (searchParams.has(key)) {
                    params.set(key, searchParams.get(key));
                }
            });

            return params;
        },

        parseYouTubeStartTime(value) {
            if (!value) {
                return null;
            }

            if (/^\d+$/.test(value)) {
                return value;
            }

            const pattern = /^(?:(\d+)h)?(?:(\d+)m)?(?:(\d+)s)?$/i;
            const match = value.match(pattern);

            if (!match) {
                return null;
            }

            const hours = parseInt(match[1] || '0', 10);
            const minutes = parseInt(match[2] || '0', 10);
            const seconds = parseInt(match[3] || '0', 10);
            const totalSeconds = (hours * 3600) + (minutes * 60) + seconds;

            if (totalSeconds <= 0) {
                return null;
            }

            return totalSeconds.toString();
        },

        // Unit pricing management
        addUnitPrice() {
            if (!this.newUnitPrice.unit_id) {
                toastr.error('Please select a unit');
                return;
            }

            if (!this.newUnitPrice.unit_value || this.newUnitPrice.unit_value <= 0) {
                toastr.error('Please enter a valid unit value');
                return;
            }

            if (!this.newUnitPrice.price || this.newUnitPrice.price <= 0) {
                toastr.error('Please enter a valid price');
                return;
            }

            // Calculate discount percent if discount price is provided
            if (this.newUnitPrice.discount_price > 0 && this.newUnitPrice.price > 0) {
                this.newUnitPrice.discount_percent = Math.round(
                    ((this.newUnitPrice.price - this.newUnitPrice.discount_price) / this.newUnitPrice.price) * 100
                );
            } else {
                this.newUnitPrice.discount_percent = 0;
            }

            // Add to pricing list
            this.unitPricing.push({ ...this.newUnitPrice });
            
            toastr.success('Unit price added successfully!');
            
            // Keep the form filled for quick entry of multiple values
            // Only increment unit_value and clear prices to allow easy addition of more entries
            // Example: User can quickly add pc = 10, 20, 30 with different prices
            
            // Increment unit_value by 10 or 1 based on current value
            const incrementBy = this.newUnitPrice.unit_value >= 10 ? 10 : 1;
            this.newUnitPrice.unit_value = parseFloat(this.newUnitPrice.unit_value) + incrementBy;
            
            // Update unit label if it exists
            if (this.newUnitPrice.unit_label) {
                const unitName = this.getUnitName(this.newUnitPrice.unit_id);
                this.newUnitPrice.unit_label = `${this.newUnitPrice.unit_value} ${unitName.toLowerCase()}`;
            }
            
            // Clear price fields for new entry
            this.newUnitPrice.price = 0;
            this.newUnitPrice.discount_price = 0;
            this.newUnitPrice.discount_percent = 0;
            
            // Note: unit_id and unit_value remain filled for quick multiple entries
        },

        clearUnitPriceForm() {
            // Complete form reset
            this.newUnitPrice = {
                unit_id: '',
                unit_title: '',
                unit_value: 1,
                unit_label: '',
                price: 0,
                discount_price: 0,
                discount_percent: 0,
                reward_points: 0,
                is_default: 0
            };
            
            // Reset Select2
            this.$nextTick(() => {
                if ($('#unitPricingSelect').length) {
                    $('#unitPricingSelect').val(null).trigger('change');
                }
            });
            
            toastr.info('Form cleared');
        },

        async removeUnitPrice(index) {
            const result = await Swal.fire({
                title: 'Remove Unit Price?',
                text: 'Are you sure you want to remove this unit price?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, remove it!'
            });

            if (result.isConfirmed) {
                this.unitPricing.splice(index, 1);
                toastr.success('Unit price removed');
            }
        },

        getUnitName(unitId) {
            const unit = this.units.find(u => u.id == unitId);
            return unit ? unit.name : '';
        },

        // Apply common values to all variant combinations
        applyCommonValuesToAllVariants() {
            if (this.variantCombinations.length === 0) {
                toastr.warning('No variant combinations available');
                return;
            }

            let appliedCount = 0;

            this.variantCombinations.forEach((combination, index) => {
                // Apply common values
                if (this.commonVariantValues.price !== null && this.commonVariantValues.price !== '') {
                    combination.price = parseFloat(this.commonVariantValues.price);
                }
                if (this.commonVariantValues.discount_price !== null && this.commonVariantValues.discount_price !== '') {
                    combination.discount_price = parseFloat(this.commonVariantValues.discount_price);
                }
                if (this.commonVariantValues.additional_price !== null && this.commonVariantValues.additional_price !== '') {
                    combination.additional_price = parseFloat(this.commonVariantValues.additional_price) || 0;
                }
                if (this.commonVariantValues.stock !== null && this.commonVariantValues.stock !== '') {
                }
                if (this.commonVariantValues.low_stock_alert !== null && this.commonVariantValues.low_stock_alert !== '') {
                    combination.low_stock_alert = parseInt(this.commonVariantValues.low_stock_alert) || 10;
                }

                // Generate unique SKU
                if (!combination.sku || combination.sku === '') {
                    combination.sku = this.generateVariantSKU(combination, index);
                }

                // Generate unique Barcode
                if (!combination.barcode || combination.barcode === '') {
                    combination.barcode = this.generateVariantBarcode(combination, index);
                }

                appliedCount++;
            });

            toastr.success(`Applied common values to ${appliedCount} variant(s) successfully!`);
        },

        // Generate unique SKU for variant
        generateVariantSKU(combination, index) {
            // Base SKU from product name (if available)
            let baseSKU = '';
            if (this.product.name) {
                baseSKU = this.product.name
                    .toUpperCase()
                    .replace(/[^A-Z0-9]/g, '')
                    .substring(0, 8);
            } else {
                baseSKU = 'PRD';
            }

            // Create variant identifier from combination key
            const variantKey = combination.combination_key
                .replace(/[^A-Z0-9]/g, '')
                .substring(0, 10)
                .toUpperCase();

            // Add timestamp and index for uniqueness
            const timestamp = Date.now().toString().slice(-6);
            const uniqueId = (index + 1).toString().padStart(3, '0');

            return `${baseSKU}-${variantKey}-${timestamp}-${uniqueId}`;
        },

        // Generate unique Barcode for variant
        generateVariantBarcode(combination, index) {
            // Generate 13-digit EAN-13 compatible barcode
            // Format: 2-digit prefix + 10-digit unique + 1 check digit
            
            // Product prefix (use category ID or default)
            const prefix = this.product.category_id ? 
                this.product.category_id.toString().padStart(2, '0').slice(-2) : '01';

            // Combination hash (first 8 chars of combination key as numbers)
            let combinationHash = combination.combination_key
                .replace(/[^A-Z0-9]/g, '')
                .toUpperCase()
                .substring(0, 8);
            
            // Convert letters to numbers (A=10, B=11, etc.)
            combinationHash = combinationHash
                .split('')
                .map(char => {
                    if (isNaN(char)) {
                        return (char.charCodeAt(0) - 55).toString();
                    }
                    return char;
                })
                .join('')
                .substring(0, 8)
                .padStart(8, '0');

            // Index padded to 3 digits
            const indexStr = (index + 1).toString().padStart(3, '0');

            // Create 12-digit number
            const barcode12 = prefix + combinationHash.substring(0, 8) + indexStr.substring(0, 2);

            // Calculate check digit (EAN-13 algorithm)
            let sum = 0;
            for (let i = 0; i < 12; i++) {
                const digit = parseInt(barcode12[i]);
                sum += (i % 2 === 0) ? digit : digit * 3;
            }
            const checkDigit = (10 - (sum % 10)) % 10;

            return barcode12 + checkDigit.toString();
        },

        // Handle variant image upload
        async handleVariantImageUpload(event, variantIndex) {
            const file = event.target.files[0];
            if (!file) return;

            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!allowedTypes.includes(file.type)) {
                toastr.error('Only JPG, JPEG, and PNG images are allowed');
                event.target.value = ''; // Clear input
                return;
            }

            // Validate file size (max 5MB)
            if (file.size > 5 * 1024 * 1024) {
                toastr.error('Image size must be less than 5MB');
                event.target.value = ''; // Clear input
                return;
            }

            // Show uploading notification
            toastr.info('Uploading variant image...', 'Please wait', {
                timeOut: 0,
                extendedTimeOut: 0,
                closeButton: false,
                progressBar: true
            });

            try {
                // Prepare form data for upload
                const formData = new FormData();
                formData.append('file', file);
                formData.append('width', 800);  // Variant image dimensions
                formData.append('height', 800);

                // Upload to server
                const response = await fetch('/media/upload', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                if (!response.ok) {
                    throw new Error('Upload failed');
                }

                const data = await response.json();

                // Clear previous toastr
                toastr.clear();

                // Store the media file information
                this.variantCombinations[variantIndex].image_id = data.id;
                this.variantCombinations[variantIndex].image_token = data.token;
                this.variantCombinations[variantIndex].image_path = data.path; // Store relative path
                this.variantCombinations[variantIndex].image_url = data.url; // Use URL from server
                this.variantCombinations[variantIndex].image = null; // Clear file object as it's now on server

                toastr.success('Variant image uploaded successfully!');

            } catch (error) {
                console.error('Upload error:', error);
                toastr.clear();
                toastr.error('Failed to upload variant image. Please try again.');
                event.target.value = ''; // Clear input
            }
        },

        // Remove variant image
        async removeVariantImage(variantIndex) {
            const result = await Swal.fire({
                title: 'Remove Variant Image?',
                text: 'Are you sure you want to remove this variant image?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, remove it!'
            });

            if (!result.isConfirmed) {
                return;
            }

            const combination = this.variantCombinations[variantIndex];
            
            // If image was uploaded to server, delete it
            if (combination.image_id) {
                try {
                    await this.deleteMediaFile(combination.image_id);
                } catch (error) {
                    console.error('Error deleting variant image from server:', error);
                }
            }

            // Clear image data
            this.variantCombinations[variantIndex].image = null;
            this.variantCombinations[variantIndex].image_id = null;
            this.variantCombinations[variantIndex].image_token = null;
            this.variantCombinations[variantIndex].image_path = '';
            this.variantCombinations[variantIndex].image_url = '';
            
            toastr.success('Variant image removed');
        },

        // Handle product image upload
        async handleProductImageUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                toastr.error('Only JPG, JPEG, PNG, GIF, and WEBP images are allowed');
                event.target.value = '';
                return;
            }

            // Validate file size (max 5MB)
            if (file.size > 5 * 1024 * 1024) {
                toastr.error('Image size must be less than 5MB');
                event.target.value = '';
                return;
            }

            // Show uploading notification
            toastr.info('Uploading product image...', 'Please wait', {
                timeOut: 0,
                extendedTimeOut: 0,
                closeButton: false,
                progressBar: true
            });

            try {
                // If there's an existing image, delete it first
                if (this.product.product_image_id) {
                    await this.deleteMediaFile(this.product.product_image_id);
                }

                // Prepare form data for upload
                const formData = new FormData();
                formData.append('file', file);
                formData.append('width', 800);
                formData.append('height', 800);

                // Upload to server
                const response = await fetch('/media/upload', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                if (!response.ok) {
                    throw new Error('Upload failed');
                }

                const data = await response.json();

                // Clear previous toastr
                toastr.clear();

                // Store the media file information
                this.product.product_image_id = data.id;
                this.product.product_image_url = data.url; // Use URL from server

                toastr.success('Product image uploaded successfully!');

            } catch (error) {
                console.error('Upload error:', error);
                toastr.clear();
                toastr.error('Failed to upload product image. Please try again.');
                event.target.value = '';
            }
        },

        // Remove product image
        async removeProductImage() {
            const result = await Swal.fire({
                title: 'Remove Product Image?',
                text: 'Are you sure you want to remove this product image?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, remove it!'
            });

            if (!result.isConfirmed) {
                return;
            }

            // If image was uploaded to server, delete it
            if (this.product.product_image_id) {
                try {
                    await this.deleteMediaFile(this.product.product_image_id);
                } catch (error) {
                    console.error('Error deleting image from server:', error);
                }
            }

            // Clear image data
            this.product.product_image_id = null;
            this.product.product_image_url = null;
            
            toastr.success('Product image removed');
        },

        // Handle gallery image upload
        async handleGalleryImageUpload(event, galleryIndex) {
            const file = event.target.files[0];
            if (!file) return;

            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                toastr.error('Only JPG, JPEG, PNG, GIF, and WEBP images are allowed');
                event.target.value = '';
                return;
            }

            // Validate file size (max 5MB)
            if (file.size > 5 * 1024 * 1024) {
                toastr.error('Image size must be less than 5MB');
                event.target.value = '';
                return;
            }

            // Show uploading notification
            toastr.info('Uploading gallery image...', 'Please wait', {
                timeOut: 0,
                extendedTimeOut: 0,
                closeButton: false,
                progressBar: true
            });

            try {
                // If there's an existing image at this index, delete it first
                if (this.product.gallery_images[galleryIndex] && this.product.gallery_images[galleryIndex].id) {
                    await this.deleteMediaFile(this.product.gallery_images[galleryIndex].id);
                }

                // Prepare form data for upload
                const formData = new FormData();
                formData.append('file', file);
                formData.append('width', 800);
                formData.append('height', 800);

                // Upload to server
                const response = await fetch('/media/upload', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                if (!response.ok) {
                    throw new Error('Upload failed');
                }

                const data = await response.json();

                // Clear previous toastr
                toastr.clear();

                // Store the media file information
                this.$set(this.product.gallery_images, galleryIndex, {
                    id: data.id,
                    url: data.url, // Use URL from server
                    token: data.token
                });

                // Update the hidden field with comma-separated IDs
                this.updateGalleryImageIds();

                toastr.success('Gallery image uploaded successfully!');

            } catch (error) {
                console.error('Upload error:', error);
                toastr.clear();
                toastr.error('Failed to upload gallery image. Please try again.');
                event.target.value = '';
            }
        },

        // Remove gallery image
        async removeGalleryImage(galleryIndex) {
            const result = await Swal.fire({
                title: 'Remove Gallery Image?',
                text: 'Are you sure you want to remove this gallery image?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, remove it!'
            });

            if (!result.isConfirmed) {
                return;
            }

            // If image was uploaded to server, delete it
            if (this.product.gallery_images[galleryIndex] && this.product.gallery_images[galleryIndex].id) {
                try {
                    await this.deleteMediaFile(this.product.gallery_images[galleryIndex].id);
                } catch (error) {
                    console.error('Error deleting image from server:', error);
                }
            }

            // Clear image data at this index
            this.$set(this.product.gallery_images, galleryIndex, { id: null, url: null, token: null });

            // Update the hidden field with comma-separated IDs
            this.updateGalleryImageIds();
            
            toastr.success('Gallery image removed');
        },

        async handleNotificationImageUpload(event) {
            const input = Array.isArray(this.$refs.notificationImageInput)
                ? this.$refs.notificationImageInput[0]
                : this.$refs.notificationImageInput;

            const file = event.target.files[0];
            if (!file) return;

            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                toastr.error('Only JPG, JPEG, PNG, GIF, and WEBP images are allowed');
                if (input) input.value = '';
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                toastr.error('Image size must be less than 5MB');
                if (input) input.value = '';
                return;
            }

            toastr.info('Uploading notification image...', 'Please wait', {
                timeOut: 0,
                extendedTimeOut: 0,
                closeButton: false,
                progressBar: true
            });

            try {
                if (this.notification.image_id) {
                    await this.deleteMediaFile(this.notification.image_id);
                }

                const formData = new FormData();
                formData.append('file', file);
                formData.append('width', 600);
                formData.append('height', 600);

                const response = await fetch('/media/upload', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                if (!response.ok) {
                    throw new Error('Upload failed');
                }

                const data = await response.json();

                toastr.clear();

                this.notification.image_id = data.id || null;
                this.notification.image_url = data.url || null;
                this.notification.image_token = data.token || null;
                this.notification.image_path = data.path || null;

                toastr.success('Notification image uploaded successfully!');
                this.saveToLocalStorage();
            } catch (error) {
                console.error('Notification image upload failed:', error);
                toastr.clear();
                toastr.error('Failed to upload notification image. Please try again.');
            } finally {
                if (input) {
                    input.value = '';
                }
            }
        },

        async removeNotificationImage(requireConfirm = true) {
            if (requireConfirm) {
                const result = await Swal.fire({
                    title: 'Remove Notification Image?',
                    text: 'Are you sure you want to remove the notification image?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, remove it!'
                });

                if (!result.isConfirmed) {
                    return;
                }
            }

            if (this.notification.image_id) {
                try {
                    await this.deleteMediaFile(this.notification.image_id);
                } catch (error) {
                    console.error('Error deleting notification image:', error);
                }
            }

            this.notification.image_id = null;
            this.notification.image_url = null;
            this.notification.image_token = null;
            this.notification.image_path = null;

            const input = Array.isArray(this.$refs.notificationImageInput)
                ? this.$refs.notificationImageInput[0]
                : this.$refs.notificationImageInput;
            if (input) {
                input.value = '';
            }

            toastr.success('Notification image removed');
            this.saveToLocalStorage();
        },

        // Update gallery_image_ids hidden field
        updateGalleryImageIds() {
            const ids = this.product.gallery_images
                .filter(img => img && img.id)
                .map(img => img.id)
                .join(',');
            this.product.gallery_image_ids = ids;
        },

        resolveOptionName(options, id) {
            if (!Array.isArray(options) || !id) {
                return null;
            }
            const parsedId = parseInt(id);
            if (!parsedId) {
                return null;
            }
            const match = options.find(option => parseInt(option.id) === parsedId);
            if (!match) {
                return null;
            }
            return match.name || match.title || match.label || null;
        },

        getCategoryNameById(id) {
            return this.resolveOptionName(this.categories, id);
        },

        getSubcategoryNameById(id) {
            return this.resolveOptionName(this.subcategories, id);
        },

        getChildCategoryNameById(id) {
            return this.resolveOptionName(this.childCategories, id);
        },

        getBrandNameById(id) {
            return this.resolveOptionName(this.brands, id);
        },

        // Helper method to delete media file
        async deleteMediaFile(mediaId) {
            try {
                await fetch(`/media/revert`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: mediaId })
                });
            } catch (error) {
                console.error('Error deleting media file:', error);
                throw error;
            }
        },

        // NEW: Variant management methods
        getVariantGroup(slug) {
            return this.variantGroups.find(g => g.slug === slug);
        },

        getVariantGroupKeys(groupSlug) {
            const group = this.getVariantGroup(groupSlug);
            return group ? group.active_keys : [];
        },

        // Get stock-related groups (creates combinations). Include color/size so single-variant (e.g. color-only) products can generate combinations.
        getStockRelatedGroups() {
            return this.variantGroups.filter(g => {
                if (g.slug === 'color' || g.slug === 'size') return true;
                // Check for truthy values (true, 1, '1', or undefined/null as default)
                return g.is_stock_related === true ||
                       g.is_stock_related === 1 ||
                       g.is_stock_related === '1' ||
                       g.is_stock_related === undefined ||
                       g.is_stock_related === null;
            });
        },

        // Get filter-related groups (frontend filtering only)
        getFilterRelatedGroups() {
            return this.variantGroups.filter(g => {
                if (g.is_fixed) return false;
                // Check for falsy values (false, 0, '0')
                return g.is_stock_related === false || 
                       g.is_stock_related === 0 || 
                       g.is_stock_related === '0';
            });
        },

        getKeyName(groupSlug, keyId) {
            // Handle fixed groups (color, size) separately
            if (groupSlug === 'color') {
                const color = this.colors.find(c => c.id == keyId);
                return color ? color.name : '';
            }
            
            if (groupSlug === 'size') {
                const size = this.sizes.find(s => s.id == keyId);
                return size ? size.name : '';
            }
            
            // Handle dynamic groups
            const keys = this.getVariantGroupKeys(groupSlug);
            const key = keys.find(k => k.id == keyId);
            return key ? key.key_name : '';
        },

        // Check if any variants have stock entered
        hasVariantsWithStock() {
            return this.variantCombinations.some(combo => combo.stock && combo.stock > 0);
        },

        // Check if variants with specific attribute values have stock
        checkVariantsHaveStock(removedIds, attributeType) {
            // Get the name mapping for removed IDs
            const removedNames = removedIds.map(id => {
                if (attributeType === 'color') {
                    const color = this.colors.find(c => c.id == id);
                    return color ? color.name : null;
                } else if (attributeType === 'size') {
                    const size = this.sizes.find(s => s.id == id);
                    return size ? size.name : null;
                }
                return null;
            }).filter(name => name !== null);

            // Check if any variant with these values has stock
            return this.variantCombinations.some(combo => {
                if (!combo.stock || combo.stock <= 0) return false;
                
                // Check if this combo uses any of the removed values
                if (combo.variant_values && combo.variant_values[attributeType]) {
                    return removedNames.includes(combo.variant_values[attributeType]);
                }
                return false;
            });
        },

        generateVariantCombinations() {
            if (!this.hasVariants || Object.keys(this.selectedVariantGroups).length === 0) {
                this.variantCombinations = [];
                return;
            }

            const existingByKey = new Map((this.variantCombinations || []).map(combo => [combo.combination_key, combo]));
            const finalMap = new Map();
            const groupValuesBySlug = {};
            const groupArrays = [];

            const orderedSlugs = [];
            if (this.selectedVariantGroups['color'] && this.selectedVariantGroups['color'].length > 0) {
                orderedSlugs.push('color');
            }
            if (this.selectedVariantGroups['size'] && this.selectedVariantGroups['size'].length > 0) {
                orderedSlugs.push('size');
            }
            for (let slug in this.selectedVariantGroups) {
                if (slug !== 'color' && slug !== 'size' && this.selectedVariantGroups[slug] && this.selectedVariantGroups[slug].length > 0) {
                    orderedSlugs.push(slug);
                }
            }

            const totalGroups = orderedSlugs.length;

            orderedSlugs.forEach(slug => {
                const keyIds = this.selectedVariantGroups[slug];
                if (keyIds && keyIds.length > 0) {
                    const mappedValues = keyIds.map(id => {
                        let keyName = '';
                        let groupLabel = slug;

                        if (slug === 'color') {
                            const color = this.colors.find(c => c.id == id);
                            keyName = color ? color.name : '';
                            groupLabel = 'Color';
                        } else if (slug === 'size') {
                            const size = this.sizes.find(s => s.id == id);
                            keyName = size ? size.name : '';
                            groupLabel = 'Size';
                        } else {
                            const group = this.variantGroups.find(g => g.slug === slug);
                            if (group) {
                                groupLabel = group.name || slug;
                                if (group.active_keys) {
                                    const key = group.active_keys.find(k => k.id == id);
                                    keyName = key ? key.key_name : '';
                                }
                            }
                        }

                        return {
                            slug,
                            keyId: id,
                            keyName,
                            groupName: groupLabel
                        };
                    });

                    groupValuesBySlug[slug] = mappedValues;
                    groupArrays.push(mappedValues);
                }
            });

            if (groupArrays.length === 0) {
                this.variantCombinations = [];
                return;
            }

            const upsertCombination = (key, variantValues) => {
                const existing = existingByKey.get(key);
                if (existing) {
                    const combo = {
                        ...existing,
                        variant_values: variantValues
                    };
                    finalMap.set(key, combo);
                } else {
                    const combo = {
                        combination_key: key,
                        variant_values: variantValues,
                        price: null,
                        discount_price: null,
                        additional_price: 0,
                        stock: 0,
                        present_stock: 0,
                        low_stock_alert: 10,
                        sku: '',
                        barcode: '',
                        image: null,
                        image_id: null,
                        image_token: null,
                        image_path: '',
                        image_url: '',
                    };
                    finalMap.set(key, combo);
                }
            };

            // Single value combinations per group
            orderedSlugs.forEach(slug => {
                const isFixedGroup = slug === 'color' || slug === 'size';
                const values = groupValuesBySlug[slug] || [];
                values.forEach(value => {
                    if (!value.keyName) {
                        return;
                    }
                    if (isFixedGroup && totalGroups > 1) {
                        return;
                    }
                    const groupLabel = value.groupName || slug;
                    const key = `${groupLabel}: ${value.keyName}`;
                    const variantValues = {
                        [slug]: value.keyName
                    };
                    upsertCombination(key, variantValues);
                });
            });

            // Multi-group combinations (only when more than one group involved)
            if (groupArrays.length > 1) {
                const cartesian = this.cartesianProduct(groupArrays);
                cartesian.forEach(comboValues => {
                    const variantValues = {};
                    const keyParts = [];
                    comboValues.forEach(value => {
                        if (!value.keyName) {
                            return;
                        }
                        variantValues[value.slug] = value.keyName;
                        keyParts.push(value.keyName);
                    });

                    if (keyParts.length === 0) {
                        return;
                    }

                    const key = keyParts.join('-');
                    upsertCombination(key, variantValues);
                });
            }

            this.variantCombinations = Array.from(finalMap.values());
            this.saveToLocalStorage();
        },

        cartesianProduct(arrays) {
            if (arrays.length === 0) return [[]];
            if (arrays.length === 1) return arrays[0].map(item => [item]);
            
            const [first, ...rest] = arrays;
            const restProduct = this.cartesianProduct(rest);
            
            return first.flatMap(item => 
                restProduct.map(items => [item, ...items])
            );
        },

        // Generate Color & Size combinations. Supports: only colors, only sizes, or both (color-size pairs).
        generateColorSizeCombinations() {
            const hasColors = this.selectedColorSizeColors && this.selectedColorSizeColors.length > 0;
            const hasSizes = this.selectedColorSizeSizes && this.selectedColorSizeSizes.length > 0;
            if (!this.hasVariants || (!hasColors && !hasSizes)) {
                this.colorSizeCombinations = [];
                this.mergeAllCombinations();
                return;
            }

            const existingByKey = new Map((this.colorSizeCombinations || []).map(item => [item.combo.combination_key, item]));
            const finalMap = new Map();

            const selectedColors = (this.selectedColorSizeColors || []).map(id => {
                const color = this.colors.find(c => c.id == id);
                return { id, name: color ? color.name : '' };
            }).filter(c => c.name);

            const selectedSizes = (this.selectedColorSizeSizes || []).map(id => {
                const size = this.sizes.find(s => s.id == id);
                return { id, name: size ? size.name : '' };
            }).filter(s => s.name);

            const upsertCombo = (combinationKey, variantValues, colorId, colorImageData) => {
                const existing = existingByKey.get(combinationKey);
                if (existing) {
                    existing.combo.variant_values = variantValues;
                    existing.combo.color_id = colorId != null ? colorId : (existing.combo.color_id || null);
                    if (colorImageData && colorImageData.image_id) {
                        existing.combo.image_id = colorImageData.image_id;
                        existing.combo.image_token = colorImageData.image_token || null;
                        existing.combo.image_path = colorImageData.image_path || '';
                        existing.combo.image_url = colorImageData.image_url || '';
                    }
                    finalMap.set(combinationKey, existing);
                } else {
                    const combo = {
                        combination_key: combinationKey,
                        variant_values: variantValues,
                        color_id: colorId != null ? colorId : null,
                        price: this.pricing.price || null,
                        discount_price: this.pricing.discount_price || null,
                        additional_price: 0,
                        stock: 0,
                        present_stock: 0,
                        low_stock_alert: 10,
                        sku: '',
                        barcode: '',
                        warehouse_room_id: '',
                        warehouse_room_cartoon_id: '',
                        image_id: (colorImageData && colorImageData.image_id) || null,
                        image_token: (colorImageData && colorImageData.image_token) || null,
                        image_path: (colorImageData && colorImageData.image_path) || '',
                        image_url: (colorImageData && colorImageData.image_url) || '',
                    };
                    finalMap.set(combinationKey, { combo, index: finalMap.size });
                }
            };

            if (selectedColors.length > 0 && selectedSizes.length > 0) {
                // Both: color-size pairs (blue-M, blue-L, red-M, ...)
                selectedColors.forEach(color => {
                    const colorImageData = this.colorImages[color.id] || {};
                    selectedSizes.forEach(size => {
                        const combinationKey = `${color.name}-${size.name}`;
                        upsertCombo(
                            combinationKey,
                            { color: color.name, size: size.name },
                            color.id,
                            colorImageData
                        );
                    });
                });
            } else if (selectedColors.length > 0) {
                // Only colors: one combination per color
                selectedColors.forEach(color => {
                    const colorImageData = this.colorImages[color.id] || {};
                    upsertCombo(
                        color.name,
                        { color: color.name },
                        color.id,
                        colorImageData
                    );
                });
            } else {
                // Only sizes: one combination per size
                selectedSizes.forEach(size => {
                    upsertCombo(size.name, { size: size.name }, null, null);
                });
            }

            this.colorSizeCombinations = Array.from(finalMap.values());
            this.mergeAllCombinations();
        },

        // Generate Other Variants combinations
        generateOtherVariantCombinations() {
            if (!this.hasVariants || Object.keys(this.selectedOtherVariantGroups).length === 0) {
                this.otherVariantsCombinations = [];
                this.mergeAllCombinations();
                return;
            }

            const existingByKey = new Map((this.otherVariantsCombinations || []).map(item => [item.combo.combination_key, item]));
            const finalMap = new Map();
            const groupArrays = [];
            const orderedSlugs = [];

            // Get all selected groups (including color/size so single-variant products get combinations)
            for (let slug in this.selectedOtherVariantGroups) {
                if (this.selectedOtherVariantGroups[slug] &&
                    this.selectedOtherVariantGroups[slug].length > 0) {
                    orderedSlugs.push(slug);
                }
            }

            if (orderedSlugs.length === 0) {
                this.otherVariantsCombinations = [];
                this.mergeAllCombinations();
                return;
            }

            orderedSlugs.forEach(slug => {
                const keyIds = this.selectedOtherVariantGroups[slug];
                if (keyIds && keyIds.length > 0) {
                    const group = this.variantGroups.find(g => g.slug === slug);
                    const mappedValues = keyIds.map(id => {
                        let keyName = '';
                        let groupLabel = slug;
                        if (group) {
                            groupLabel = group.name || slug;
                            if (group.active_keys && group.active_keys.length > 0) {
                                const key = group.active_keys.find(k => k.id == id);
                                keyName = key ? key.key_name : '';
                            }
                            // Resolve color/size or any missing name via getKeyName (supports single-group combinations)
                            if (!keyName) {
                                keyName = this.getKeyName(slug, id) || '';
                            }
                        }
                        return {
                            slug,
                            keyId: id,
                            keyName,
                            groupName: groupLabel
                        };
                    }).filter(v => v.keyName);
                    if (mappedValues.length > 0) {
                        groupArrays.push(mappedValues);
                    }
                }
            });

            if (groupArrays.length === 0) {
                this.otherVariantsCombinations = [];
                this.mergeAllCombinations();
                return;
            }

            const upsertCombination = (key, variantValues) => {
                const existing = existingByKey.get(key);
                if (existing) {
                    existing.combo.variant_values = variantValues;
                    finalMap.set(key, existing);
                } else {
                    const combo = {
                        combination_key: key,
                        variant_values: variantValues,
                        price: this.pricing.price || null,
                        discount_price: this.pricing.discount_price || null,
                        additional_price: 0,
                        stock: 0,
                        present_stock: 0,
                        low_stock_alert: 10,
                        sku: '',
                        barcode: '',
                        image: null,
                        image_id: null,
                        image_token: null,
                        image_path: '',
                        image_url: '',
                    };
                    finalMap.set(key, { combo, index: finalMap.size });
                }
            };

            // Generate combinations using cartesian product
            if (groupArrays.length > 0) {
                const cartesian = this.cartesianProduct(groupArrays);
                cartesian.forEach(comboValues => {
                    const variantValues = {};
                    const keyParts = [];
                    comboValues.forEach(value => {
                        if (!value.keyName) return;
                        variantValues[value.slug] = value.keyName;
                        keyParts.push(value.keyName);
                    });

                    if (keyParts.length === 0) return;
                    const key = keyParts.join('-');
                    upsertCombination(key, variantValues);
                });
            }

            this.otherVariantsCombinations = Array.from(finalMap.values());
            this.mergeAllCombinations();

            console.log('otherVariantsCombinations', this.otherVariantsCombinations);
            
        },

        // Merge all combinations from both tabs into variantCombinations
        mergeAllCombinations() {
            const allCombos = [];
            this.colorSizeCombinations.forEach(item => {
                allCombos.push(item.combo);
            });
            this.otherVariantsCombinations.forEach(item => {
                allCombos.push(item.combo);
            });
            this.variantCombinations = allCombos;
            this.saveToLocalStorage();
        },

        // Color & Size Tab Helper Methods
        onCommonColorSizeRoomChange() {
            const roomId = this.commonColorSizeValues.warehouse_room_id;
            if (!roomId) {
                this.commonColorSizeValues.warehouse_room_cartoon_id = '';
            }
            const cartoons = this.getCartoonsForRoom(roomId);
            this.colorSizeCombinations.forEach(item => {
                item.combo.warehouse_room_id = roomId || '';
                if (roomId && cartoons.length === 1) {
                    item.combo.warehouse_room_cartoon_id = String(cartoons[0].id);
                } else {
                    item.combo.warehouse_room_cartoon_id = '';
                }
            });
            this.saveToLocalStorage();
        },
        onCommonColorSizeCartoonChange() {
            const cartoonId = this.commonColorSizeValues.warehouse_room_cartoon_id;
            this.colorSizeCombinations.forEach(item => {
                if (String(item.combo.warehouse_room_id) === String(this.commonColorSizeValues.warehouse_room_id)) {
                    item.combo.warehouse_room_cartoon_id = cartoonId || '';
                }
            });
            this.saveToLocalStorage();
        },
        onColorSizeRoomChange(combination) {
            if (!combination) return;
            combination._warehouseRoomError = false;
            if (!combination.warehouse_room_id) {
                combination.warehouse_room_cartoon_id = '';
            } else {
                const cartoons = this.getCartoonsForRoom(combination.warehouse_room_id);
                if (cartoons.length === 1) {
                    combination.warehouse_room_cartoon_id = String(cartoons[0].id);
                } else {
                    combination.warehouse_room_cartoon_id = '';
                }
            }
            this.saveToLocalStorage();
        },
        onColorSizeCartoonChange(combination) {
            if (!combination) return;
            this.saveToLocalStorage();
        },
        applyCommonValuesToColorSizeVariants() {
            if (this.colorSizeCombinations.length === 0) {
                toastr.warning('No color & size combinations available');
                return;
            }
            let appliedCount = 0;
            this.colorSizeCombinations.forEach((item, index) => {
                const combo = item.combo;
                if (this.commonColorSizeValues.price !== null && this.commonColorSizeValues.price !== '') {
                    combo.price = parseFloat(this.commonColorSizeValues.price);
                }
                if (this.commonColorSizeValues.discount_price !== null && this.commonColorSizeValues.discount_price !== '') {
                    combo.discount_price = parseFloat(this.commonColorSizeValues.discount_price);
                }
                if (this.commonColorSizeValues.additional_price !== null && this.commonColorSizeValues.additional_price !== '') {
                    combo.additional_price = parseFloat(this.commonColorSizeValues.additional_price) || 0;
                }
                if (this.commonColorSizeValues.stock !== null && this.commonColorSizeValues.stock !== '') {
                }
                if (this.commonColorSizeValues.low_stock_alert !== null && this.commonColorSizeValues.low_stock_alert !== '') {
                    combo.low_stock_alert = parseInt(this.commonColorSizeValues.low_stock_alert) || 10;
                }
                if (!combo.sku || combo.sku === '') {
                    combo.sku = this.generateVariantSKU(combo, index);
                }
                if (!combo.barcode || combo.barcode === '') {
                    combo.barcode = this.generateVariantBarcode(combo, index);
                }
                appliedCount++;
            });
            toastr.success(`Applied common values to ${appliedCount} variant(s) successfully!`);
        },
        // Color-based image upload (one image per color, shared by all size combinations)
        async handleColorImageUpload(event, colorId) {
            const file = event.target.files[0];
            if (!file) return;
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!allowedTypes.includes(file.type)) {
                toastr.error('Only JPG, JPEG, and PNG images are allowed');
                event.target.value = '';
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                toastr.error('Image size must be less than 5MB');
                event.target.value = '';
                return;
            }
            toastr.info('Uploading color image...', 'Please wait', { timeOut: 0, extendedTimeOut: 0, closeButton: false, progressBar: true });
            try {
                const formData = new FormData();
                formData.append('file', file);
                formData.append('is_temp', '1');
                const response = await fetch('/media/upload', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: formData
                });
                const data = await response.json();
                if (!data.success) throw new Error(data.message || 'Upload failed');
                toastr.clear();
                
                // Store image for this color
                this.colorImages[colorId] = {
                    image_id: data.id,
                    image_token: data.token,
                    image_path: data.path,
                    image_url: data.url
                };
                
                // Update all combinations with this color to use the new image
                this.colorSizeCombinations.forEach(item => {
                    if (item.combo.color_id == colorId) {
                        // Update image data in combo object
                        item.combo.image_id = data.id;
                        item.combo.image_token = data.token;
                        item.combo.image_path = data.path;
                        item.combo.image_url = data.url;
                    }
                });
                
                // Also update variantCombinations array
                this.variantCombinations.forEach(combo => {
                    if (combo.color_id == colorId) {
                        combo.image_id = data.id;
                        combo.image_token = data.token;
                        combo.image_path = data.path;
                        combo.image_url = data.url;
                    }
                });
                
                toastr.success('Color image uploaded successfully!');
                this.saveToLocalStorage();
            } catch (error) {
                console.error('Upload error:', error);
                toastr.clear();
                toastr.error('Failed to upload color image. Please try again.');
                event.target.value = '';
            }
        },
        async removeColorImage(colorId) {
            const result = await Swal.fire({
                title: 'Remove Color Image?',
                text: 'All combinations with this color will lose their image. Are you sure?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, remove it!'
            });

            if (!result.isConfirmed) return;
            
            const colorImage = this.colorImages[colorId];
            if (colorImage && colorImage.image_id) {
                try {
                    await this.deleteMediaFile(colorImage.image_id);
                } catch (error) {
                    console.error('Error deleting color image from server:', error);
                }
            }
            
            // Remove image from state
            delete this.colorImages[colorId];
            
            // Clear image data from all combinations with this color
            this.colorSizeCombinations.forEach(item => {
                if (item.combo.color_id == colorId) {
                    item.combo.image_id = null;
                    item.combo.image_token = null;
                    item.combo.image_path = '';
                    item.combo.image_url = '';
                }
            });
            
            // Also update variantCombinations array
            this.variantCombinations.forEach(combo => {
                if (combo.color_id == colorId) {
                    combo.image_id = null;
                    combo.image_token = null;
                    combo.image_path = '';
                    combo.image_url = '';
                }
            });
            
            toastr.success('Color image removed');
            this.saveToLocalStorage();
        },
        // Helper method to get image URL for a color
        getColorImageUrl(colorId) {
            if (!colorId || !this.colorImages[colorId]) return null;
            return this.colorImages[colorId].image_url || null;
        },
        // Helper method to get color name from ID
        getColorName(colorId) {
            if (!colorId || !this.colors) return '';
            const color = this.colors.find(c => c.id == colorId);
            return color ? color.name : '';
        },

        // Other Variants Tab Helper Methods
        applyCommonValuesToOtherVariants() {
            if (this.otherVariantsCombinations.length === 0) {
                toastr.warning('No other variant combinations available');
                return;
            }
            let appliedCount = 0;
            this.otherVariantsCombinations.forEach((item, index) => {
                const combo = item.combo;
                if (this.commonOtherVariantValues.price !== null && this.commonOtherVariantValues.price !== '') {
                    combo.price = parseFloat(this.commonOtherVariantValues.price);
                }
                if (this.commonOtherVariantValues.discount_price !== null && this.commonOtherVariantValues.discount_price !== '') {
                    combo.discount_price = parseFloat(this.commonOtherVariantValues.discount_price);
                }
                if (this.commonOtherVariantValues.additional_price !== null && this.commonOtherVariantValues.additional_price !== '') {
                    combo.additional_price = parseFloat(this.commonOtherVariantValues.additional_price) || 0;
                }
                if (this.commonOtherVariantValues.low_stock_alert !== null && this.commonOtherVariantValues.low_stock_alert !== '') {
                    combo.low_stock_alert = parseInt(this.commonOtherVariantValues.low_stock_alert) || 10;
                }
                if (!combo.sku || combo.sku === '') {
                    combo.sku = this.generateVariantSKU(combo, index);
                }
                if (!combo.barcode || combo.barcode === '') {
                    combo.barcode = this.generateVariantBarcode(combo, index);
                }
                appliedCount++;
            });
            toastr.success(`Applied common values to ${appliedCount} variant(s) successfully!`);
        },
        async handleOtherVariantImageUpload(event, variantIndex) {
            const file = event.target.files[0];
            if (!file) return;
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!allowedTypes.includes(file.type)) {
                toastr.error('Only JPG, JPEG, and PNG images are allowed');
                event.target.value = '';
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                toastr.error('Image size must be less than 5MB');
                event.target.value = '';
                return;
            }
            toastr.info('Uploading variant image...', 'Please wait', { timeOut: 0, extendedTimeOut: 0, closeButton: false, progressBar: true });
            try {
                const formData = new FormData();
                formData.append('file', file);
                formData.append('is_temp', '1');
                const response = await fetch('/media/upload', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: formData
                });
                const data = await response.json();
                if (!data.success) throw new Error(data.message || 'Upload failed');
                toastr.clear();
                const combo = this.otherVariantsCombinations[variantIndex].combo;
                combo.image_id = data.id;
                combo.image_token = data.token;
                combo.image_path = data.path;
                combo.image_url = data.url;
                combo.image = null;
                toastr.success('Variant image uploaded successfully!');
            } catch (error) {
                console.error('Upload error:', error);
                toastr.clear();
                toastr.error('Failed to upload variant image. Please try again.');
                event.target.value = '';
            }
        },
        async removeOtherVariantImage(variantIndex) {
            const result = await Swal.fire({
                title: 'Remove Variant Image?',
                text: 'Are you sure you want to remove this variant image?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, remove it!'
            });

            if (!result.isConfirmed) return;
            const combo = this.otherVariantsCombinations[variantIndex].combo;
            if (combo.image_id) {
                try {
                    await this.deleteMediaFile(combo.image_id);
                } catch (error) {
                    console.error('Error deleting variant image from server:', error);
                }
            }
            combo.image = null;
            combo.image_id = null;
            combo.image_token = null;
            combo.image_path = '';
            combo.image_url = '';
            toastr.success('Variant image removed');
        },

        initializeOtherVariantValueSelects() {
            const self = this;
            this.$nextTick(() => {
                if (this.selectedOtherStockGroupSlugs && this.selectedOtherStockGroupSlugs.length > 0) {
                    this.selectedOtherStockGroupSlugs.forEach(groupSlug => {
                        const selectId = `otherVariantGroup_${groupSlug}`;
                        if ($(`#${selectId}`).length && !$(`#${selectId}`).hasClass('select2-hidden-accessible')) {
                            $(`#${selectId}`).select2({
                                placeholder: `Select ${groupSlug} values`,
                                allowClear: true,
                                width: '100%'
                            }).on('change', function() {
                                const values = $(this).val() || [];
                                self.$set(self.selectedOtherVariantGroups, groupSlug, values);
                            });
                        }
                    });
                }
            });
        },

        // Initialize Select2 for all select fields
        initializeSelect2() {
            const self = this;

            // Basic Info Tab - Category
            if ($('#categorySelect').length && !$('#categorySelect').hasClass('select2-hidden-accessible')) {
                $('#categorySelect').select2({
                    placeholder: 'Search and select category',
                    allowClear: true,
                    width: '100%'
                }).on('change', function() {
                    const value = $(this).val();
                    self.product.category_id = value;
                    self.loadSubcategories();
                });
            }

            // Basic Info Tab - Subcategory
            if ($('#subcategorySelect').length && !$('#subcategorySelect').hasClass('select2-hidden-accessible')) {
                $('#subcategorySelect').select2({
                    placeholder: 'Search and select subcategory',
                    allowClear: true,
                    width: '100%'
                }).on('change', function() {
                    const value = $(this).val();
                    self.product.subcategory_id = value;
                    self.loadChildCategories();
                });
            }

            // Basic Info Tab - Child Category
            if ($('#childCategorySelect').length && !$('#childCategorySelect').hasClass('select2-hidden-accessible')) {
                $('#childCategorySelect').select2({
                    placeholder: 'Search and select child category',
                    allowClear: true,
                    width: '100%'
                }).on('change', function() {
                    self.product.childcategory_id = $(this).val();
                });
            }

            // Basic Info Tab - Brand
            if ($('#brandSelect').length && !$('#brandSelect').hasClass('select2-hidden-accessible')) {
                $('#brandSelect').select2({
                    placeholder: 'Search and select brand',
                    allowClear: true,
                    width: '100%'
                }).on('change', function() {
                    const value = $(this).val();
                    self.product.brand_id = value;
                    self.loadModels();
                });
            }

            // Basic Info Tab - Model
            if ($('#modelSelect').length && !$('#modelSelect').hasClass('select2-hidden-accessible')) {
                $('#modelSelect').select2({
                    placeholder: 'Search and select model',
                    allowClear: true,
                    width: '100%'
                }).on('change', function() {
                    self.product.model_id = $(this).val();
                });
            }

            // Basic Info Tab - Unit
            if ($('#unitSelect').length && !$('#unitSelect').hasClass('select2-hidden-accessible')) {
                $('#unitSelect').select2({
                    placeholder: 'Search and select unit',
                    allowClear: true,
                    width: '100%'
                }).on('change', function() {
                    self.product.unit_id = $(this).val();
                });
            }

            // Basic Info Tab - Flag
            if ($('#flagSelect').length && !$('#flagSelect').hasClass('select2-hidden-accessible')) {
                $('#flagSelect').select2({
                    placeholder: 'Search and select flag',
                    allowClear: true,
                    width: '100%'
                }).on('change', function() {
                    self.product.flag_id = $(this).val();
                });
            }

            // Pricing Tab - Unit Pricing
            if ($('#unitPricingSelect').length && !$('#unitPricingSelect').hasClass('select2-hidden-accessible')) {
                $('#unitPricingSelect').select2({
                    placeholder: 'Select unit for pricing',
                    allowClear: true,
                    width: '100%'
                }).on('change', function() {
                    self.newUnitPrice.unit_id = $(this).val();
                });
            }

            // Variants Tab - Color (Multiple)
            if ($('#colorSelect').length && !$('#colorSelect').hasClass('select2-hidden-accessible')) {
                $('#colorSelect').select2({
                    placeholder: 'Select colors',
                    allowClear: true,
                    width: '100%'
                }).on('change', function() {
                    const values = $(this).val() || [];
                    const newValues = values.map(v => parseInt(v));
                    
                    // Check if trying to remove colors with existing stock
                    if (self.hasVariantsWithStock()) {
                        const currentColors = self.selectedVariantGroups.color || [];
                        const removedColors = currentColors.filter(c => !newValues.includes(c));
                        
                        if (removedColors.length > 0) {
                            // Check if any removed color has stock
                            const hasStock = self.checkVariantsHaveStock(removedColors, 'color');
                            if (hasStock) {
                                toastr.error('Cannot remove colors that have stock entered!', 'Stock Protection', {
                                    closeButton: true,
                                    timeOut: 5000
                                });
                                // Revert to previous selection
                                $(this).val(currentColors).trigger('change.select2');
                                return;
                            }
                        }
                    }
                    
                    self.$set(self.selectedVariantGroups, 'color', newValues);
                });
            }

            // Variants Tab - Size (Multiple)
            if ($('#sizeSelect').length && !$('#sizeSelect').hasClass('select2-hidden-accessible')) {
                $('#sizeSelect').select2({
                    placeholder: 'Select sizes',
                    allowClear: true,
                    width: '100%'
                }).on('change', function() {
                    const values = $(this).val() || [];
                    const newValues = values.map(v => parseInt(v));
                    
                    // Check if trying to remove sizes with existing stock
                    if (self.hasVariantsWithStock()) {
                        const currentSizes = self.selectedVariantGroups.size || [];
                        const removedSizes = currentSizes.filter(s => !newValues.includes(s));
                        
                        if (removedSizes.length > 0) {
                            // Check if any removed size has stock
                            const hasStock = self.checkVariantsHaveStock(removedSizes, 'size');
                            if (hasStock) {
                                toastr.error('Cannot remove sizes that have stock entered!', 'Stock Protection', {
                                    closeButton: true,
                                    timeOut: 5000
                                });
                                // Revert to previous selection
                                $(this).val(currentSizes).trigger('change.select2');
                                return;
                            }
                        }
                    }
                    
                    self.$set(self.selectedVariantGroups, 'size', newValues);
                });
            }

            // Stock Variant Group Selector
            if ($('#stockVariantGroupSelector').length && !$('#stockVariantGroupSelector').hasClass('select2-hidden-accessible')) {
                $('#stockVariantGroupSelector').select2({
                    placeholder: 'Search and select stock variant groups (Material, Weight, Storage, RAM, etc.)',
                    allowClear: true,
                    width: '100%'
                }).on('change', function() {
                    const values = $(this).val() || [];
                    self.selectedStockGroupSlugs = values;
                });
            }

            // Filter Variant Group Selector
            if ($('#filterVariantGroupSelector').length && !$('#filterVariantGroupSelector').hasClass('select2-hidden-accessible')) {
                $('#filterVariantGroupSelector').select2({
                    placeholder: 'Search and select filter attributes (Pattern, Fit, Style, etc.)',
                    allowClear: true,
                    width: '100%'
                }).on('change', function() {
                    const values = $(this).val() || [];
                    self.selectedFilterGroupSlugs = values;
                });
            }

            // Initialize variant group value selects
            this.initializeStockVariantValueSelects();
            this.initializeFilterVariantValueSelects();

            // Color & Size Tab - Color Select
            if ($('#colorSizeColorSelect').length && !$('#colorSizeColorSelect').hasClass('select2-hidden-accessible')) {
                $('#colorSizeColorSelect').select2({
                    placeholder: 'Select colors',
                    allowClear: true,
                    width: '100%'
                }).on('change', function() {
                    const values = $(this).val() || [];
                    self.selectedColorSizeColors = values.map(v => parseInt(v));
                });
            }

            // Color & Size Tab - Size Select
            if ($('#colorSizeSizeSelect').length && !$('#colorSizeSizeSelect').hasClass('select2-hidden-accessible')) {
                $('#colorSizeSizeSelect').select2({
                    placeholder: 'Select sizes',
                    allowClear: true,
                    width: '100%'
                }).on('change', function() {
                    const values = $(this).val() || [];
                    self.selectedColorSizeSizes = values.map(v => parseInt(v));
                });
            }

            // Other Variants Tab - Group Selector
            if ($('#otherVariantGroupSelector').length && !$('#otherVariantGroupSelector').hasClass('select2-hidden-accessible')) {
                $('#otherVariantGroupSelector').select2({
                    placeholder: 'Search and select variant groups',
                    allowClear: true,
                    width: '100%'
                }).on('change', function() {
                    const values = $(this).val() || [];
                    self.selectedOtherStockGroupSlugs = values;
                });
            }

            // Initialize other variant value selects
            this.initializeOtherVariantValueSelects();
        },

        // Initialize Select2 for stock variant value selects
        initializeStockVariantValueSelects() {
            const self = this;
            
            $('.select2-stock-variant-values').each(function() {
                const $this = $(this);
                
                // Destroy if already initialized
                if ($this.hasClass('select2-hidden-accessible')) {
                    $this.select2('destroy');
                }
                
                const groupSlug = $this.attr('id').replace('stockVariantGroup_', '');
                
                $this.select2({
                    placeholder: 'Search and select options',
                    allowClear: true,
                    width: '100%'
                }).on('change', function() {
                    const values = $(this).val() || [];
                    self.$set(self.selectedVariantGroups, groupSlug, values.map(v => parseInt(v)));
                });
            });
        },

        // Initialize Select2 for filter variant value selects
        initializeFilterVariantValueSelects() {
            const self = this;
            
            $('.select2-filter-variant-values').each(function() {
                const $this = $(this);
                
                // Destroy if already initialized
                if ($this.hasClass('select2-hidden-accessible')) {
                    $this.select2('destroy');
                }
                
                const groupSlug = $this.attr('id').replace('filterVariantGroup_', '');
                
                $this.select2({
                    placeholder: 'Search and select options',
                    allowClear: true,
                    width: '100%'
                }).on('change', function() {
                    const values = $(this).val() || [];
                    self.$set(self.selectedFilterGroups, groupSlug, values.map(v => parseInt(v)));
                });
            });
        },

        // Update Select2 selections when Vue data changes programmatically
        updateSelect2Values() {
            const select2Fields = [
                { id: '#categorySelect', value: this.product.category_id },
                { id: '#subcategorySelect', value: this.product.subcategory_id },
                { id: '#childCategorySelect', value: this.product.childcategory_id },
                { id: '#brandSelect', value: this.product.brand_id },
                { id: '#modelSelect', value: this.product.model_id },
                { id: '#unitSelect', value: this.product.unit_id },
                { id: '#colorSelect', value: this.selectedVariantGroups.color },
                { id: '#sizeSelect', value: this.selectedVariantGroups.size }
            ];

            select2Fields.forEach(field => {
                if ($(field.id).length && $(field.id).hasClass('select2-hidden-accessible')) {
                    $(field.id).val(field.value).trigger('change.select2');
                }
            });
        },

        initializeRelatedSelects() {
            const configs = [
                { type: 'similar', selector: '#similarProductsSelect' },
                { type: 'recommended', selector: '#recommendedProductsSelect' },
                { type: 'addons', selector: '#addonProductsSelect' },
            ];

            configs.forEach(({ type, selector }) => {
                const $select = $(selector);
                if (!$select.length) {
                    return;
                }

                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.off('select2:select').off('select2:unselect').select2('destroy');
                }

                const options = {
                    placeholder: 'Search products...',
                    width: '100%',
                    multiple: true,
                    allowClear: false,
                };

                if (this.routes.productSearch) {
                    options.ajax = {
                        url: this.routes.productSearch,
                        dataType: 'json',
                        delay: 250,
                        data: (params) => ({
                            term: params.term || '',
                            exclude: this.productId ? [this.productId] : [],
                            limit: 20,
                        }),
                        processResults: (response) => ({
                            results: (response.results || []).map(item => ({
                                id: item.id,
                                text: item.text,
                                price: item.price,
                            })),
                        }),
                    };
                    options.templateResult = (data) => {
                        if (data.loading) {
                            return data.text;
                        }
                        const priceSuffix = data.price != null
                            ? ` · ৳${this.formatCurrency(data.price)}`
                            : '';
                        const element = document.createElement('span');
                        element.textContent = `${data.text || ''}${priceSuffix}`;
                        return element;
                    };
                    options.templateSelection = (data) => data.text || '';
                }

                $select.select2(options);

                $select.on('select2:select', (event) => {
                    if (this.suppressRelatedEvents) {
                        return;
                    }
                    const selected = event.params?.data || {};
                    this.addRelatedItem(type, selected);
                });

                $select.on('select2:unselect', (event) => {
                    if (this.suppressRelatedEvents) {
                        return;
                    }
                    const removed = event.params?.data || {};
                    this.removeRelatedItem(type, removed.id);
                });

                this.syncRelatedSelect(type);
            });
        },

        getRelatedSelect(type) {
            switch (type) {
                case 'similar':
                    return $('#similarProductsSelect');
                case 'recommended':
                    return $('#recommendedProductsSelect');
                case 'addons':
                    return $('#addonProductsSelect');
                default:
                    return $();
            }
        },

        syncRelatedSelect(type) {
            const select = this.getRelatedSelect(type);
            if (!select.length) {
                return;
            }

            // Check if Select2 is initialized
            const isSelect2Initialized = select.hasClass('select2-hidden-accessible');
            
            const ids = this.related[type].map(item => String(item.id));

            // Remove options that are not in the related list
            select.find('option').each((_, option) => {
                if (!ids.includes(option.value)) {
                    $(option).remove();
                }
            });

            // Prepare data for Select2
            const select2Data = [];

            // Add options for items in the related list
            this.related[type].forEach(item => {
                const value = String(item.id);
                const existingOption = select.find(`option[value="${value}"]`);
                
                if (!existingOption.length) {
                    // Create new option and add to select
                    const option = new Option(item.name, value, true, true);
                    option.setAttribute('data-price', item.price ?? 0);
                    select.append(option);
                } else {
                    // Update existing option text if name changed
                    if (existingOption.text() !== item.name) {
                        existingOption.text(item.name);
                    }
                    // Update price attribute
                    existingOption.attr('data-price', item.price ?? 0);
                }
                
                // Add to Select2 data array
                select2Data.push({
                    id: value,
                    text: item.name,
                    price: item.price ?? 0
                });
            });

            // Set the selected values
            this.suppressRelatedEvents = true;
            if (isSelect2Initialized) {
                // For Select2 with AJAX, we need to add data objects
                // Clear existing data and add new data
                select.empty();
                
                // Add options to underlying select
                select2Data.forEach(data => {
                    const option = new Option(data.text, data.id, true, true);
                    option.setAttribute('data-price', data.price);
                    select.append(option);
                });
                
                // Set value and trigger change
                const selectedIds = ids.length > 0 ? ids : null;
                select.val(selectedIds).trigger('change.select2');
            } else {
                // For regular select, just set the value
                select.val(ids.length > 0 ? ids : null);
            }
            this.suppressRelatedEvents = false;
        },

        syncAllRelatedSelects() {
            ['similar', 'recommended', 'addons'].forEach(type => this.syncRelatedSelect(type));
        },

        addRelatedItem(type, data) {
            const id = parseInt(data.id ?? data.value ?? 0, 10);
            if (!id || this.related[type].some(item => item.id === id)) {
                this.syncRelatedSelect(type);
                return;
            }

            const item = {
                id,
                name: data.text || data.name || `Product #${id}`,
                price: data.price != null ? parseFloat(data.price) : 0,
            };

            if (type === 'addons') {
                item.is_default = false;
            }

            this.related[type] = [...this.related[type], item];
            this.syncRelatedSelect(type);
            this.saveToLocalStorage();
        },

        removeRelatedItem(type, id) {
            const numericId = parseInt(id ?? 0, 10);
            if (!numericId) {
                return;
            }

            this.related[type] = this.related[type].filter(item => item.id !== numericId);

            const select = this.getRelatedSelect(type);
            if (select.length) {
                this.suppressRelatedEvents = true;
                select.find(`option[value="${numericId}"]`).remove();
                const remainingIds = this.related[type].map(item => String(item.id));
                select.val(remainingIds).trigger('change.select2');
                this.suppressRelatedEvents = false;
            }

            this.saveToLocalStorage();
        },

        formatCurrency(value) {
            const number = parseFloat(value);
            if (Number.isNaN(number)) {
                return '0.00';
            }
            return number.toFixed(2);
        },

        // Destroy all Select2 instances
        destroySelect2() {
            const select2Selectors = [
                '#categorySelect', '#subcategorySelect', '#childCategorySelect',
                '#brandSelect', '#modelSelect', '#unitSelect', '#unitPricingSelect',
                '#colorSelect', '#sizeSelect',
                '#stockVariantGroupSelector', '#filterVariantGroupSelector',
                '#similarProductsSelect', '#recommendedProductsSelect', '#addonProductsSelect'
            ];

            select2Selectors.forEach(selector => {
                if ($(selector).hasClass('select2-hidden-accessible')) {
                    $(selector).select2('destroy');
                }
            });

            // Destroy stock variant value selects
            $('.select2-stock-variant-values').each(function() {
                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2('destroy');
                }
            });

            // Destroy filter variant value selects
            $('.select2-filter-variant-values').each(function() {
                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2('destroy');
                }
            });
        },

        // Reinitialize Select2 for dynamic selects (subcategory, child category, model)
        reinitializeDynamicSelects() {
            const self = this;

            // Reinitialize Subcategory Select2
            if ($('#subcategorySelect').hasClass('select2-hidden-accessible')) {
                $('#subcategorySelect').select2('destroy');
            }
            if ($('#subcategorySelect').length) {
                this.$nextTick(() => {
                    $('#subcategorySelect').select2({
                        placeholder: 'Search and select subcategory',
                        allowClear: true,
                        width: '100%'
                    }).on('change', function() {
                        const value = $(this).val();
                        self.product.subcategory_id = value;
                        self.loadChildCategories();
                    });
                });
            }

            // Reinitialize Child Category Select2
            if ($('#childCategorySelect').hasClass('select2-hidden-accessible')) {
                $('#childCategorySelect').select2('destroy');
            }
            if ($('#childCategorySelect').length) {
                this.$nextTick(() => {
                    $('#childCategorySelect').select2({
                        placeholder: 'Search and select child category',
                        allowClear: true,
                        width: '100%'
                    }).on('change', function() {
                        self.product.childcategory_id = $(this).val();
                    });
                });
            }

            // Reinitialize Model Select2
            if ($('#modelSelect').hasClass('select2-hidden-accessible')) {
                $('#modelSelect').select2('destroy');
            }
            if ($('#modelSelect').length) {
                this.$nextTick(() => {
                    $('#modelSelect').select2({
                        placeholder: 'Search and select model',
                        allowClear: true,
                        width: '100%'
                    }).on('change', function() {
                        self.product.model_id = $(this).val();
                    });
                });
            }
        },

        // Auto-generate SKU
        watchNameForSku() {
            this.$watch('product.name', (newName) => {
                if (newName && !this.product.sku) {
                    const slug = newName.toLowerCase()
                        .replace(/[^a-z0-9]+/g, '-')
                        .replace(/^-|-$/g, '');
                    this.product.sku = slug.toUpperCase().substring(0, 20) + '-' + Date.now();
                }
            });
        },

        // Calculate discount percent
        calculateDiscountPercent() {
            if (this.pricing.price > 0 && this.pricing.discount_price > 0) {
                this.pricing.discount_percent = Math.round(
                    ((this.pricing.price - this.pricing.discount_price) / this.pricing.price) * 100
                );
            }
        },

        // ===== LocalStorage Methods =====
        
        // Check if there's stored data
        checkStoredData() {
            const stored = localStorage.getItem(this.localStorageKey);
            if (stored) {
                try {
                    const data = JSON.parse(stored);
                    this.hasStoredData = true;
                    this.lastSavedTime = data.savedAt;
                    
                    // Show notification
                    if (typeof toastr !== 'undefined') {
                        toastr.info('Previous form data found. Click "Restore" to recover your work.', 'Data Recovery', {
                            timeOut: 0,
                            extendedTimeOut: 0,
                            closeButton: true,
                            progressBar: true
                        });
                    }
                } catch (e) {
                    console.error('Error parsing stored data:', e);
                    localStorage.removeItem(this.localStorageKey);
                }
            }
        },

        // Save form data to localStorage
        saveToLocalStorage() {
            try {
                const formData = {
                    product: this.product,
                    pricing: this.pricing,
                    unitPricing: this.unitPricing,
                    hasVariants: this.hasVariants,
                    selectedStockGroupSlugs: this.selectedStockGroupSlugs,
                    selectedFilterGroupSlugs: this.selectedFilterGroupSlugs,
                    selectedVariantGroups: this.selectedVariantGroups,
                    selectedFilterGroups: this.selectedFilterGroups,
                    variantCombinations: this.variantCombinations,
                    commonVariantValues: this.commonVariantValues,
                    attributes: this.attributes,
                    shippingInfo: this.shippingInfo,
                    taxInfo: this.taxInfo,
                    metaInfo: this.metaInfo,
                    faq: this.faq,
                    specialOffer: this.specialOffer,
                    selectedWarehouseId: this.selectedWarehouseId,
                    selectedWarehouseRoomId: this.selectedWarehouseRoomId,
                    selectedWarehouseCartoonId: this.selectedWarehouseCartoonId,
                    related: this.related,
                    notification: this.notification,
                    showSingleVariants: this.showSingleVariants,
                    showCombinationVariants: this.showCombinationVariants,
                    savedAt: new Date().toISOString()
                };

                localStorage.setItem(this.localStorageKey, JSON.stringify(formData));
                this.lastSavedTime = formData.savedAt;
                
            } catch (e) {
                console.error('Error saving to localStorage:', e);
            }
        },

        // Restore form data from localStorage
        restoreFromLocalStorage() {
            const stored = localStorage.getItem(this.localStorageKey);
            if (!stored) {
                toastr.warning('No stored data found');
                return;
            }

            try {
                const data = JSON.parse(stored);
                
                // Restore all data
                this.product = data.product || this.product;
                this.product.slug = this.generateSlug(this.product.slug || '');
                this.pricing = data.pricing || this.pricing;
                this.unitPricing = data.unitPricing || [];
                this.hasVariants = data.hasVariants || false;
                this.selectedStockGroupSlugs = data.selectedStockGroupSlugs || [];
                this.selectedFilterGroupSlugs = data.selectedFilterGroupSlugs || [];
                this.selectedVariantGroups = data.selectedVariantGroups || {};
                this.selectedFilterGroups = data.selectedFilterGroups || {};
                this.variantCombinations = data.variantCombinations || [];
                this.commonVariantValues = data.commonVariantValues || this.commonVariantValues;
                this.attributes = data.attributes || this.attributes;
                this.shippingInfo = data.shippingInfo || this.shippingInfo;
                this.taxInfo = data.taxInfo || this.taxInfo;
                this.metaInfo = data.metaInfo || this.metaInfo;
                this.faq = data.faq || [];
                this.specialOffer = data.specialOffer || this.specialOffer;
                this.related = data.related || this.related;
                this.notification = {
                    ...this.notification,
                    ...(data.notification || {})
                };
                this.notification.is_show = this.notification.is_show === true || this.notification.is_show === 1 || this.notification.is_show === '1';

                this.selectedWarehouseId = data.selectedWarehouseId || this.selectedWarehouseId;
                this.selectedWarehouseRoomId = data.selectedWarehouseRoomId || this.selectedWarehouseRoomId;
                this.selectedWarehouseCartoonId = data.selectedWarehouseCartoonId || this.selectedWarehouseCartoonId;
                this.showSingleVariants = data.showSingleVariants !== undefined ? data.showSingleVariants : this.showSingleVariants;
                this.showCombinationVariants = data.showCombinationVariants !== undefined ? data.showCombinationVariants : this.showCombinationVariants;

                this.warehouseSelectionError = false;
                this.simpleWarehouseRoomError = false;

                this.syncSlugManualFlag();
                this.resetSlugState(this.slugState.manual);

                this.hasStoredData = false;

                // Update Select2 values
                this.$nextTick(() => {
                    this.updateSelect2Values();
                    this.initializeRelatedSelects();
                    this.syncAllRelatedSelects();
                });

                toastr.success('Form data restored successfully!');
            } catch (e) {
                console.error('Error restoring data:', e);
                toastr.error('Error restoring form data');
            }
        },

        // Clear localStorage
        clearLocalStorage() {
            localStorage.removeItem(this.localStorageKey);
            this.hasStoredData = false;
            this.lastSavedTime = null;
        },

        // Discard stored data
        async discardStoredData() {
            const result = await Swal.fire({
                title: 'Discard Saved Data?',
                text: 'Are you sure you want to discard the saved data? This cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, discard it!'
            });

            if (result.isConfirmed) {
                this.clearLocalStorage();
                toastr.info('Stored data discarded');
            }
        },

        // Setup auto-save watchers
        setupAutoSave() {
            // Watch product changes
            this.$watch('product', () => {
                this.saveToLocalStorage();
            }, { deep: true });

            // Watch pricing changes
            this.$watch('pricing', () => {
                this.saveToLocalStorage();
            }, { deep: true });

            // Watch unit pricing changes
            this.$watch('unitPricing', () => {
                this.saveToLocalStorage();
            }, { deep: true });

            // Watch variant changes
            this.$watch('selectedVariantGroups', () => {
                this.saveToLocalStorage();
            }, { deep: true });

            this.$watch('selectedFilterGroups', () => {
                this.saveToLocalStorage();
            }, { deep: true });

            this.$watch('variantCombinations', () => {
                this.saveToLocalStorage();
            }, { deep: true });

            // Watch common variant values
            this.$watch('commonVariantValues', () => {
                this.saveToLocalStorage();
            }, { deep: true });

            // Watch other data
            this.$watch('attributes', () => {
                this.saveToLocalStorage();
            }, { deep: true });

            this.$watch('shippingInfo', () => {
                this.saveToLocalStorage();
            }, { deep: true });

            this.$watch('taxInfo', () => {
                this.saveToLocalStorage();
            }, { deep: true });

            this.$watch('metaInfo', () => {
                this.saveToLocalStorage();
            }, { deep: true });

            this.$watch('faq', () => {
                this.saveToLocalStorage();
            }, { deep: true });

            this.$watch('specialOffer', () => {
                this.saveToLocalStorage();
            }, { deep: true });

            this.$watch('selectedWarehouseId', () => {
                this.saveToLocalStorage();
            });

            this.$watch('selectedWarehouseRoomId', () => {
                this.saveToLocalStorage();
            });

            this.$watch('selectedWarehouseCartoonId', () => {
                this.saveToLocalStorage();
            });

            this.$watch('showSingleVariants', () => {
                this.saveToLocalStorage();
            });

            this.$watch('showCombinationVariants', () => {
                this.saveToLocalStorage();
            });

            this.$watch('related', () => {
                this.saveToLocalStorage();
            }, { deep: true });

            this.$watch('notification', () => {
                this.saveToLocalStorage();
            }, { deep: true });
        },

        // Setup beforeunload warning
        setupUnloadWarning() {
            // Check if there's unsaved data
            const hasUnsavedData = () => {
                return this.product.name || this.pricing.price > 0 || this.variantCombinations.length > 0;
            };

            // Browser-level navigation (back button, close tab, etc.) - use native dialog only
            window.addEventListener('beforeunload', (e) => {
                if (hasUnsavedData()) {
                    e.preventDefault();
                    e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
                    return e.returnValue;
                }
            });
        },

        // Get or create unique session ID for duplicate prevention
        getSessionId() {
            let sessionId = sessionStorage.getItem('product_create_session_id');
            if (!sessionId) {
                sessionId = 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                sessionStorage.setItem('product_create_session_id', sessionId);
            }
            return sessionId;
        },

        // Form submission
        async submitProduct() {
            if (this.isSubmitting) {
                toastr.warning('Please wait, submission is in progress...');
                return;
            }

            // Basic validation
            if (!this.product.name) {
                toastr.error('Product name is required');
                this.switchTab('basic_info');
                return;
            }

            if (!this.product.category_id) {
                toastr.error('Category is required');
                this.switchTab('basic_info');
                return;
            }

            if (!this.product.product_image_id) {
                toastr.error('Product image is required');
                this.switchTab('images');
                return;
            }

            if (!this.pricing.price || this.pricing.price <= 0) {
                toastr.error('Price must be greater than 0');
                this.switchTab('pricing');
                return;
            }

            if (!await this.ensureSlugValid(true)) {
                this.switchTab('basic_info');
                return;
            }

            // Validate variants if product has variants
            if (this.hasVariants) {
                if (this.variantCombinations.length === 0) {
                    toastr.error('Please create at least one variant combination');
                    this.switchTab('variants');
                    return;
                }

            // Validate each variant combination
                const invalidVariants = [];
                this.variantCombinations.forEach((combo, index) => {
                    const variantValueCount = combo.variant_values ? Object.keys(combo.variant_values).length : 0;
                    const isSingleVariant = variantValueCount <= 1;
                    const isCombinationVariant = variantValueCount > 1;

                    const isHiddenByFilter = (isSingleVariant && !this.showSingleVariants) ||
                        (isCombinationVariant && !this.showCombinationVariants);

                    if (isHiddenByFilter) {
                        return;
                    }

                    const errors = [];
                    
                    // Check price
                    if (!combo.price || parseFloat(combo.price) <= 0) {
                        errors.push('price');
                    }

                    if (errors.length > 0) {
                        invalidVariants.push({
                            index: index,
                            combination: combo.combination_key,
                            errors: errors
                        });
                    }
                });

                if (invalidVariants.length > 0) {
                    // Show error messages
                    toastr.error(
                        `${invalidVariants.length} variant(s) have missing price. Please fill in all required fields.`,
                        'Variant Validation Error',
                        { timeOut: 8000, closeButton: true }
                    );

                    // Switch to variants tab
                    this.switchTab('variants');

                    // Add red borders to error fields
                    this.$nextTick(() => {
                        invalidVariants.forEach(invalid => {
                            invalid.errors.forEach(errorType => {
                                const inputSelector = `input[data-variant-index="${invalid.index}"][data-field="${errorType}"]`;
                                $(inputSelector).addClass('border-danger');
                                
                                // Remove error class after user starts typing
                                $(inputSelector).one('input', function() {
                                    $(this).removeClass('border-danger');
                                });
                            });
                        });

                        // Scroll to first error
                        const firstErrorInput = $('input.border-danger').first();
                        if (firstErrorInput.length) {
                            firstErrorInput[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                            setTimeout(() => firstErrorInput.focus(), 500);
                        }
                    });

                    return;
                }

            }

            // Increment submission count (duplicate prevention tracking)
            this.submissionCount++;
            this.isSubmitting = true;

            try {
                // Prepare complete JSON data
                const productData = {
                    // Basic product info
                    product: {
                        ...this.product,
                        // Media files (uploaded via FilePond)
                        product_image_id: this.product.product_image_id || null,
                        gallery_image_ids: this.product.gallery_image_ids || '',
                    },

                    // Pricing
                    pricing: {
                        ...this.pricing,
                        has_unit_based_price: this.pricing.has_unit_based_price || 0,
                    },

                    // Unit-based pricing
                    unit_pricing: this.unitPricing || [],

                    // Variants
                    has_variants: this.hasVariants,
                    variant_combinations: this.variantCombinations.map(combo => ({
                        id: combo.id || null,
                        combination_key: combo.combination_key,
                        variant_values: combo.variant_values,
                        price: combo.price,
                        discount_price: combo.discount_price,
                        additional_price: combo.additional_price,
                        stock: 0, // Stock will be managed via Stock Adjustment module
                        low_stock_alert: combo.low_stock_alert,
                        sku: combo.sku,
                        barcode: combo.barcode,
                        // Variant image data
                        image_id: combo.image_id || null,
                        image_token: combo.image_token || null,
                        image_path: combo.image_path || '',
                        image_url: combo.image_url || '',
                    })),

                    // Filter attributes (frontend filtering)
                    filter_attributes: this.selectedFilterGroups || {},

                    // Common variant values (for reference)
                    common_variant_values: this.commonVariantValues || {},

                    // Product attributes
                    attributes: this.attributes || {},

                    // Shipping info
                    shipping_info: this.shippingInfo || {},

                    // Tax info
                    tax_info: this.taxInfo || {},

                    // Meta/SEO info
                    meta_info: {
                        title: this.metaInfo.title || '',
                        keywords: this.metaInfo.keywords || '',
                        description: this.metaInfo.description || '',
                    },

                    // Special offer
                    special_offer: {
                        is_special: this.specialOffer.is_special || 0,
                        offer_end_time: this.specialOffer.offer_end_time || '',
                    },
                    related: {
                        similar: this.related.similar.map(item => item.id),
                        recommended: this.related.recommended.map(item => item.id),
                        addons: this.related.addons.map(item => ({
                            product_id: item.id,
                            is_default: item.is_default ? 1 : 0,
                        })),
                    },
                    notification: {
                        title: this.notification.title || null,
                        description: this.notification.description || null,
                        button_text: this.notification.button_text || null,
                        button_url: this.notification.button_url || null,
                        image_id: this.notification.image_id || null,
                        is_show: this.notification.is_show ? 1 : 0,
                    },
                    faq: this.faq.filter(item => item.question && item.answer),

                    // Additional metadata
                    _metadata: {
                        submitted_at: new Date().toISOString(),
                        has_variants: this.hasVariants,
                        total_variants: this.variantCombinations.length,
                        total_unit_prices: this.unitPricing.length,
                        has_unit_pricing: this.pricing.has_unit_based_price === 1,
                        submission_attempt: this.submissionCount,
                        session_id: this.getSessionId()
                    }
                };


                // Submit via axios as JSON
                const response = await axios.post('/product-management/store', productData, {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.data.success) {
                    // Clear all storage on successful save
                    this.clearLocalStorage();
                    localStorage.removeItem('product_create_error_backup');
                    sessionStorage.removeItem('product_create_session_id');
                    
                    // Reset error states
                    this.lastSubmissionError = null;
                    this.lastSubmissionData = null;
                    this.showResendButton = false;
                    
                    toastr.success(response.data.message || 'Product created successfully!', 'Success', {
                        timeOut: 2000,
                        progressBar: true
                    });
                    
                    // Clear localStorage before redirect
                    this.clearLocalStorage();
                    
                    // Redirect to edit page to continue editing
                    setTimeout(() => {
                        window.location.href = response.data.redirect || '/product-management';
                    }, 1000);
                } else {
                    toastr.error(response.data.message || 'Error creating product');
                }

            } catch (error) {
                console.error('Error submitting product:', error);
                
                // Store error and data for resend
                this.lastSubmissionError = error.response?.data || { message: 'Unknown error' };
                this.lastSubmissionData = productData;
                this.showResendButton = true;
                
                // Save to localStorage for recovery
                const errorBackup = {
                    ...productData,
                    _error_info: {
                        timestamp: new Date().toISOString(),
                        error_message: error.response?.data?.message || error.message,
                        error_details: error.response?.data?.error_details || null
                    }
                };
                localStorage.setItem('product_create_error_backup', JSON.stringify(errorBackup));
                
                // Display errors
                if (error.response && error.response.data) {
                    const responseData = error.response.data;
                    
                    // Show validation errors
                    if (responseData.errors) {
                        this.errors = responseData.errors;
                        Object.values(this.errors).forEach(errArray => {
                            if (Array.isArray(errArray)) {
                                errArray.forEach(err => toastr.error(err, 'Validation Error', {
                                    timeOut: 10000,
                                    closeButton: true
                                }));
                            }
                        });
                    } else {
                        // Show general error
                        toastr.error(
                            responseData.message || 'Error creating product', 
                            'Error',
                            {
                                timeOut: 15000,
                                closeButton: true,
                                progressBar: true
                            }
                        );
                    }
                    
                    // Show detailed error if available
                    if (responseData.error_details) {
                        toastr.warning(
                            `Error in ${responseData.error_details.file} at line ${responseData.error_details.line}`,
                            'Technical Details',
                            {
                                timeOut: 0,
                                closeButton: true,
                                extendedTimeOut: 0
                            }
                        );
                    }
                    
                    // Show backup data saved notification
                    toastr.info(
                        'Your data has been saved. Click "Resend Data" button to try again.',
                        'Data Backed Up',
                        {
                            timeOut: 0,
                            closeButton: true,
                            extendedTimeOut: 0
                        }
                    );
                } else {
                    toastr.error('Network error. Please check your connection.', 'Connection Error');
                }
            } finally {
                this.isSubmitting = false;
            }
        },
        
        // Resend the last failed submission
        async resendSubmission() {
            if (!this.lastSubmissionData) {
                toastr.warning('No previous submission data found');
                return;
            }
            
            this.submissionCount++;
            this.isSubmitting = true;
            this.showResendButton = false;
            
            toastr.info('Resending data...', 'Please wait');
            
            try {
                const response = await axios.post('/product-management/store', this.lastSubmissionData, {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.data.success) {
                    // Clear error backup
                    localStorage.removeItem('product_create_error_backup');
                    this.lastSubmissionError = null;
                    this.lastSubmissionData = null;
                    this.showResendButton = false;
                    
                    // Clear form localStorage
                    this.clearLocalStorage();
                    
                    toastr.success(response.data.message || 'Product created successfully!', 'Success', {
                        timeOut: 2000,
                        progressBar: true
                    });
                    
                    // Redirect to edit page to continue editing
                    setTimeout(() => {
                        window.location.href = response.data.redirect || '/product-management';
                    }, 1000);
                } else {
                    toastr.error(response.data.message || 'Error creating product');
                    this.showResendButton = true;
                }

            } catch (error) {
                console.error('Resend error:', error);
                this.showResendButton = true;
                toastr.error(error.response?.data?.message || 'Resend failed');
            } finally {
                this.isSubmitting = false;
            }
        },

        // Category Modal Methods
        openCategoryModal() {
            // Reset form
            this.newCategory = {
                name: '',
                slug: '',
                status: 1
            };
            this.categoryErrors = {};

            // Show modal
            $('#categoryModal').modal('show');
        },

        async createCategory() {
            this.categoryCreating = true;
            this.categoryErrors = {};

            try {
                const response = await axios.post('/product-management/categories/store', this.newCategory, {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.data.success) {
                    // Add new category to categories list
                    this.categories.push({
                        id: response.data.category.id,
                        name: response.data.category.name
                    });

                    // Set as selected category
                    this.product.category_id = response.data.category.id;

                    // Hide modal
                    $('#categoryModal').modal('hide');

                    // Show success message
                    toastr.success(response.data.message || 'Category created successfully!', 'Success');

                    // Clear subcategories and child categories as they depend on category
                    this.subcategories = [];
                    this.childCategories = [];
                    this.product.subcategory_id = '';
                    this.product.childcategory_id = '';

                } else {
                    toastr.error(response.data.message || 'Failed to create category');
                }

            } catch (error) {
                console.error('Category creation error:', error);

                if (error.response && error.response.status === 422) {
                    // Validation errors
                    this.categoryErrors = error.response.data.errors || {};
                } else {
                    toastr.error(error.response?.data?.message || 'Failed to create category. Please try again.');
                }
            } finally {
                this.categoryCreating = false;
            }
        },

        // Subcategory Modal Methods
        openSubcategoryModal() {
            // Check if category is selected
            if (!this.product.category_id) {
                toastr.warning('Please select a category first before creating a subcategory.', 'Category Required');
                return;
            }

            // Reset form
            this.newSubcategory = {
                category_id: this.product.category_id, // Set from selected category
                name: '',
                slug: '',
                status: 1
            };
            this.subcategoryErrors = {};

            // Show modal
            $('#subcategoryModal').modal('show');
        },

        async createSubcategory() {
            // Double check category is selected
            if (!this.product.category_id) {
                toastr.error('Please select a category first.', 'Category Required');
                return;
            }

            // Ensure category_id is set
            this.newSubcategory.category_id = this.product.category_id;

            this.subcategoryCreating = true;
            this.subcategoryErrors = {};

            try {
                const response = await axios.post('/product-management/subcategories/store', this.newSubcategory, {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.data.success) {
                    // Set as selected subcategory
                    this.product.subcategory_id = response.data.subcategory.id;

                    // Reload subcategories for the selected category
                    if (this.product.category_id) {
                        this.loadSubcategories();
                    }

                    // Hide modal
                    $('#subcategoryModal').modal('hide');

                    // Show success message
                    toastr.success(response.data.message || 'Subcategory created successfully!', 'Success');

                    // Clear child categories as they depend on subcategory
                    this.childCategories = [];
                    this.product.childcategory_id = '';

                } else {
                    toastr.error(response.data.message || 'Failed to create subcategory');
                }

            } catch (error) {
                console.error('Subcategory creation error:', error);

                if (error.response && error.response.status === 422) {
                    // Validation errors
                    this.subcategoryErrors = error.response.data.errors || {};
                } else {
                    toastr.error(error.response?.data?.message || 'Failed to create subcategory. Please try again.');
                }
            } finally {
                this.subcategoryCreating = false;
            }
        },

        // Child Category Modal Methods
        openChildCategoryModal() {
            // Check if category and subcategory are selected
            if (!this.product.category_id) {
                toastr.warning('Please select a category first before creating a child category.', 'Category Required');
                return;
            }
            if (!this.product.subcategory_id) {
                toastr.warning('Please select a subcategory first before creating a child category.', 'Subcategory Required');
                return;
            }

            // Reset form
            this.newChildCategory = {
                category_id: this.product.category_id, // Set from selected category
                subcategory_id: this.product.subcategory_id, // Set from selected subcategory
                name: '',
                slug: '',
                status: 1
            };
            this.childCategoryErrors = {};

            // Show modal
            $('#childCategoryModal').modal('show');
        },

        async createChildCategory() {
            // Double check category and subcategory are selected
            if (!this.product.category_id) {
                toastr.error('Please select a category first.', 'Category Required');
                return;
            }
            if (!this.product.subcategory_id) {
                toastr.error('Please select a subcategory first.', 'Subcategory Required');
                return;
            }

            // Ensure category_id and subcategory_id are set
            this.newChildCategory.category_id = this.product.category_id;
            this.newChildCategory.subcategory_id = this.product.subcategory_id;

            this.childCategoryCreating = true;
            this.childCategoryErrors = {};

            try {
                const response = await axios.post('/product-management/child-categories/store', this.newChildCategory, {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.data.success) {
                    // Set as selected child category
                    this.product.childcategory_id = response.data.childCategory.id;

                    // Reload child categories for the selected subcategory
                    if (this.product.subcategory_id) {
                        this.loadChildCategories();
                    }

                    // Hide modal
                    $('#childCategoryModal').modal('hide');

                    // Show success message
                    toastr.success(response.data.message || 'Child category created successfully!', 'Success');

                } else {
                    toastr.error(response.data.message || 'Failed to create child category');
                }

            } catch (error) {
                console.error('Child category creation error:', error);

                if (error.response && error.response.status === 422) {
                    // Validation errors
                    this.childCategoryErrors = error.response.data.errors || {};
                } else {
                    toastr.error(error.response?.data?.message || 'Failed to create child category. Please try again.');
                }
            } finally {
                this.childCategoryCreating = false;
            }
        },

        // Brand Modal Methods
        openBrandModal() {
            // Reset form
            this.newBrand = {
                name: '',
                slug: '',
                status: 1
            };
            this.brandErrors = {};

            // Show modal
            $('#brandModal').modal('show');
        },

        async createBrand() {
            this.brandCreating = true;
            this.brandErrors = {};

            try {
                const response = await axios.post('/product-management/brands/store', this.newBrand, {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.data.success) {
                    // Add new brand to brands list
                    this.brands.push({
                        id: response.data.brand.id,
                        name: response.data.brand.name
                    });

                    // Set as selected brand
                    this.product.brand_id = response.data.brand.id;

                    // Hide modal
                    $('#brandModal').modal('hide');

                    // Show success message
                    toastr.success(response.data.message || 'Brand created successfully!', 'Success');

                    // Clear models as they depend on brand
                    this.models = [];
                    this.product.model_id = '';

                } else {
                    toastr.error(response.data.message || 'Failed to create brand');
                }

            } catch (error) {
                console.error('Brand creation error:', error);

                if (error.response && error.response.status === 422) {
                    // Validation errors
                    this.brandErrors = error.response.data.errors || {};
                } else {
                    toastr.error(error.response?.data?.message || 'Failed to create brand. Please try again.');
                }
            } finally {
                this.brandCreating = false;
            }
        },

        // Model Modal Methods
        openModelModal() {
            // Check if brand is selected
            if (!this.product.brand_id) {
                toastr.warning('Please select a brand first before creating a model.', 'Brand Required');
                return;
            }

            // Reset form
            this.newModel = {
                brand_id: this.product.brand_id, // Set from selected brand
                name: '',
                code: '',
                slug: '',
                status: 1
            };
            this.modelErrors = {};

            // Show modal
            $('#modelModal').modal('show');
        },

        async createModel() {
            // Double check brand is selected
            if (!this.product.brand_id) {
                toastr.error('Please select a brand first.', 'Brand Required');
                return;
            }

            // Ensure brand_id is set
            this.newModel.brand_id = this.product.brand_id;

            this.modelCreating = true;
            this.modelErrors = {};

            try {
                const response = await axios.post('/product-management/models/store', this.newModel, {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.data.success) {
                    // Set as selected model
                    this.product.model_id = response.data.model.id;

                    // Reload models for the selected brand
                    if (this.product.brand_id) {
                        this.loadModels();
                    }

                    // Hide modal
                    $('#modelModal').modal('hide');

                    // Show success message
                    toastr.success(response.data.message || 'Model created successfully!', 'Success');

                } else {
                    toastr.error(response.data.message || 'Failed to create model');
                }

            } catch (error) {
                console.error('Model creation error:', error);

                if (error.response && error.response.status === 422) {
                    // Validation errors
                    this.modelErrors = error.response.data.errors || {};
                } else {
                    toastr.error(error.response?.data?.message || 'Failed to create model. Please try again.');
                }
            } finally {
                this.modelCreating = false;
            }
        },

        // Unit Modal Methods
        openUnitModal() {
            // Reset form
            this.newUnit = {
                name: '',
                status: 1
            };
            this.unitErrors = {};

            // Show modal
            $('#unitModal').modal('show');
        },

        async createUnit() {
            this.unitCreating = true;
            this.unitErrors = {};

            try {
                const response = await axios.post('/product-management/units/store', this.newUnit, {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.data.success) {
                    // Add new unit to units list
                    this.units.push({
                        id: response.data.unit.id,
                        name: response.data.unit.name
                    });

                    // Set as selected unit
                    this.product.unit_id = response.data.unit.id;

                    // Hide modal
                    $('#unitModal').modal('hide');

                    // Show success message
                    toastr.success(response.data.message || 'Unit created successfully!', 'Success');

                } else {
                    toastr.error(response.data.message || 'Failed to create unit');
                }

            } catch (error) {
                console.error('Unit creation error:', error);

                if (error.response && error.response.status === 422) {
                    // Validation errors
                    this.unitErrors = error.response.data.errors || {};
                } else {
                    toastr.error(error.response?.data?.message || 'Failed to create unit. Please try again.');
                }
            } finally {
                this.unitCreating = false;
            }
        },

        // Flag Modal Methods
        openFlagModal() {
            // Reset form
            this.newFlag = {
                name: '',
                icon: null,
                iconPreview: null,
                status: 1
            };
            this.flagErrors = {};

            // Show modal
            if ($('#flagModal').length) {
                $('#flagModal').modal('show');
            } else {
                console.error('Flag modal not found in DOM');
            }
        },
        handleFlagIconChange(event) {
            const file = event.target.files[0];
            if (!file) {
                this.newFlag.icon = null;
                this.newFlag.iconPreview = null;
                return;
            }
            
            // Validate file type
            if (!file.type.startsWith('image/')) {
                toastr.error('Please select an image file');
                event.target.value = '';
                this.newFlag.icon = null;
                this.newFlag.iconPreview = null;
                return;
            }
            
            // Validate file size (max 2MB)
            if (file.size > 2 * 1024 * 1024) {
                toastr.error('Image size must be less than 2MB');
                event.target.value = '';
                this.newFlag.icon = null;
                this.newFlag.iconPreview = null;
                return;
            }
            
            this.newFlag.icon = file;
            
            // Create preview
            const reader = new FileReader();
            reader.onload = (e) => {
                this.newFlag.iconPreview = e.target.result;
            };
            reader.readAsDataURL(file);
        },
        async createFlag() {
            this.flagCreating = true;
            this.flagErrors = {};

            try {
                const formData = new FormData();
                formData.append('name', this.newFlag.name);
                formData.append('status', this.newFlag.status);
                if (this.newFlag.icon) {
                    formData.append('icon', this.newFlag.icon);
                }

                const response = await axios.post('/config/create/new/flag', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.data.success || response.data.success === undefined) {
                    // Show success message first
                    toastr.success('Flag created successfully!', 'Success');
                    
                    // Reload the page to get updated flags list
                    // This ensures we have the latest data including the new flag
                    setTimeout(async () => {
                        const result = await Swal.fire({
                            title: 'Reload Page?',
                            text: 'Flag created successfully. The page will reload to update the flags list.',
                            icon: 'success',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Yes, reload',
                            cancelButtonText: 'Cancel'
                        });
                        
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    }, 500);
                    return;

                } else {
                    toastr.error(response.data.message || 'Failed to create flag');
                }

            } catch (error) {
                console.error('Flag creation error:', error);

                if (error.response && error.response.status === 422) {
                    // Validation errors
                    this.flagErrors = error.response.data.errors || {};
                } else {
                    toastr.error(error.response?.data?.message || 'Failed to create flag. Please try again.');
                }
            } finally {
                this.flagCreating = false;
            }
        },
        async loadFlags() {
            try {
                // Flags are already loaded from window.productData in mounted()
                // This method can be used to reload if needed
                console.log('Flags loaded from window.productData');
            } catch (error) {
                console.error('Error loading flags:', error);
            }
        }
    }
});
