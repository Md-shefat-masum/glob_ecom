const stockAdjustmentApp = new Vue({
    el: '#stockAdjustmentApp',
    data: {
        selectedProduct: null,
        adjustmentType: '',
        description: '',
        singleQuantity: 0,
        commonVariantQty: 0,
        isSubmitting: false
    },
    mounted() {
        this.initializeSelect2();
    },
    methods: {
        /**
         * Initialize Select2 for product search
         */
        initializeSelect2() {
            const self = this;
            
            $('#productSelect').select2({
                theme: 'bootstrap-5',
                placeholder: 'Search by name, code, SKU, or barcode...',
                allowClear: true,
                ajax: {
                    url: '/stock-adjustment/search-products',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.results,
                            pagination: data.pagination
                        };
                    },
                    cache: true
                },
                minimumInputLength: 1,
                templateResult: function(item) {
                    if (item.loading) {
                        return item.text;
                    }
                    
                    return $(`
                        <div class="select2-product-result">
                            <div><strong>${item.name}</strong></div>
                            <div class="text-muted small">
                                Code: ${item.code} | SKU: ${item.sku || 'N/A'} | Stock: ${item.stock}
                            </div>
                        </div>
                    `);
                },
                templateSelection: function(item) {
                    return item.name || item.text;
                }
            }).on('select2:select', function(e) {
                const productId = e.params.data.id;
                self.loadProductDetails(productId);
            }).on('select2:clear', function() {
                self.resetForm();
            });
        },

        /**
         * Load product details including variants
         */
        async loadProductDetails(productId) {
            try {
                const response = await axios.get(`/stock-adjustment/product/${productId}`);
                
                if (response.data.success) {
                    this.selectedProduct = response.data.data;
                    
                    // Initialize adjustment_qty for variants
                    if (this.selectedProduct.has_variants && this.selectedProduct.variants) {
                        this.selectedProduct.variants = this.selectedProduct.variants.map(variant => {
                            return {
                                ...variant,
                                adjustment_qty: 0
                            };
                        });
                    }
                    
                    // Reset form fields
                    this.adjustmentType = '';
                    this.description = '';
                    this.singleQuantity = 0;
                    this.commonVariantQty = 0;
                    
                    toastr.success('Product loaded successfully!', 'Success');
                } else {
                    toastr.error(response.data.message || 'Failed to load product details');
                }
            } catch (error) {
                console.error('Error loading product:', error);
                toastr.error('Error loading product details');
            }
        },

        /**
         * Calculate new stock for single product
         */
        calculateNewStock() {
            if (!this.selectedProduct || !this.adjustmentType || !this.singleQuantity) {
                return this.selectedProduct ? this.selectedProduct.stock : 0;
            }

            const currentStock = parseInt(this.selectedProduct.stock) || 0;
            const qty = parseInt(this.singleQuantity) || 0;
            
            // Determine if type adds or subtracts
            const subtractTypes = ['sales', 'waste', 'transfer'];
            const newStock = subtractTypes.includes(this.adjustmentType) 
                ? currentStock - qty 
                : currentStock + qty;
            
            return Math.max(0, newStock);
        },

        /**
         * Calculate new stock for a variant
         */
        calculateVariantNewStock(variant) {
            if (!this.adjustmentType || !variant.adjustment_qty) {
                return variant.present_stock || 0;
            }

            const currentStock = parseInt(variant.present_stock) || 0;
            const qty = parseInt(variant.adjustment_qty) || 0;
            
            // Determine if type adds or subtracts
            const subtractTypes = ['sales', 'waste', 'transfer'];
            const newStock = subtractTypes.includes(this.adjustmentType) 
                ? currentStock - qty 
                : currentStock + qty;
            
            return Math.max(0, newStock);
        },

        /**
         * Set common quantity to all variants
         */
        setAllVariants() {
            if (!this.selectedProduct || !this.selectedProduct.has_variants) {
                return;
            }

            if (!this.commonVariantQty || this.commonVariantQty <= 0) {
                toastr.warning('Please enter a valid quantity in the "Common Quantity" field');
                return;
            }

            this.selectedProduct.variants.forEach(variant => {
                variant.adjustment_qty = this.commonVariantQty;
            });

            toastr.success(`Set ${this.commonVariantQty} to all variants`, 'Success');
        },

        /**
         * Validate form before submission
         */
        validateForm() {
            if (!this.selectedProduct) {
                toastr.error('Please select a product');
                return false;
            }

            if (!this.adjustmentType) {
                toastr.error('Please select adjustment type');
                return false;
            }

            if (!this.selectedProduct.has_variants) {
                // Single product validation
                if (!this.singleQuantity || this.singleQuantity <= 0) {
                    toastr.error('Please enter a valid quantity');
                    return false;
                }
            } else {
                // Variant product validation
                const hasAnyQuantity = this.selectedProduct.variants.some(v => v.adjustment_qty > 0);
                if (!hasAnyQuantity) {
                    toastr.error('Please enter quantity for at least one variant');
                    return false;
                }
            }

            return true;
        },

        /**
         * Submit stock adjustment
         */
        async submitAdjustment() {
            if (!this.validateForm()) {
                return;
            }

            this.isSubmitting = true;

            try {
                const formData = {
                    product_id: this.selectedProduct.id,
                    type: this.adjustmentType,
                    description: this.description,
                    has_variants: this.selectedProduct.has_variants
                };

                if (this.selectedProduct.has_variants) {
                    // Send variant data
                    formData.variants = this.selectedProduct.variants.filter(v => v.adjustment_qty > 0);
                } else {
                    // Send single quantity
                    formData.quantity = this.singleQuantity;
                }

                const response = await axios.post('/stock-adjustment/store', formData, {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.data.success) {
                    toastr.success(response.data.message || 'Stock adjustment created successfully!', 'Success', {
                        timeOut: 3000,
                        progressBar: true
                    });

                    // Reset form and redirect after delay
                    setTimeout(() => {
                        window.location.href = response.data.redirect || '/stock-adjustment';
                    }, 1500);
                } else {
                    toastr.error(response.data.message || 'Error creating stock adjustment');
                }

            } catch (error) {
                console.error('Error submitting adjustment:', error);
                
                if (error.response && error.response.data) {
                    const errors = error.response.data.errors;
                    if (errors) {
                        Object.keys(errors).forEach(key => {
                            toastr.error(errors[key][0]);
                        });
                    } else {
                        toastr.error(error.response.data.message || 'Error creating stock adjustment');
                    }
                } else {
                    toastr.error('An unexpected error occurred');
                }
            } finally {
                this.isSubmitting = false;
            }
        },

        /**
         * Reset form to initial state
         */
        resetForm() {
            this.selectedProduct = null;
            this.adjustmentType = '';
            this.description = '';
            this.singleQuantity = 0;
            this.commonVariantQty = 0;
            
            // Clear Select2
            $('#productSelect').val(null).trigger('change');
        }
    }
});

