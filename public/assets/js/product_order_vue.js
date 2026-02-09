
function debounce(func, wait) {
    let timeout;
    return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            func.apply(this, args);
        }, wait);
    };
}

window.onload = function () {
    window.product_order_app = new Vue({
        el: "#formApp",
        data: function () {
            return {
                searchQuery: "",
                searchResults: [],
                loadingMore: false,
                allProductsLoaded: false,

                warehouses: [],
                rooms: [],
                cartoons: [],
                selectedWarehouse: "",
                selectedRoom: "",
                selectedCartoon: "",
                purchaseItems: [],
                subtotalAmt: 0,
                finalTotal: 0,
                dropdownVisible: false,

                other_charges_input_amount: '',
                other_charges_type: '',
                discount_on_all: 0.0,
                discount_on_all_type: 'in_percentage',

                other_charges_amt: 0,
                discount_to_all_amt: 0,
                round_off_from_total: 0,

                payment_modes: {
                    cash: 0,
                    bkash: 0,
                    rocket: 0,
                    nogod: 0,
                    credit: 0,
                    cheque: 0,
                    bank: 0,
                    gateway: 0,
                },

                // Customer payment info
                selectedCustomerId: null,
                customerDue: 0,
                customerAdvance: 0,
                availableAdvance: 0,
                hasAdvance: false,
                useAdvance: false,
                advanceAdjustmentAmount: 0,

                // Form submission state
                isSubmitting: false,

                // Delivery Information
                deliveryInfo: {
                    receiver_name: '',
                    receiver_phone: '',
                    full_address: '',
                    delivery_method: '',
                    courier_name: '',
                    courier_name_custom: '',
                    // Pathao fields
                    pathao_city_id: '',
                    pathao_zone_id: '',
                    pathao_area_id: '',
                    pathao_delivery_type: '48',
                    pathao_item_type: '3',
                    pathao_item_weight: '0.5',
                    pathao_delivery_cost: ''
                },
                
                // Pathao data
                pathaoCities: [],
                pathaoZones: [],
                pathaoAreas: [],
                pathaoLoading: {
                    cities: false,
                    zones: false,
                    areas: false,
                    price: false
                },
                pathaoError: '',
                
                // Customer data
                selectedCustomerName: '',
                selectedCustomerPhone: ''
            };
        },
        computed: {
            total_paid: function () {
                const paymentTotal = Object.values(this.payment_modes).reduce((total, value) => total + +value, 0);
                const advanceUsed = this.useAdvance ? parseFloat(this.advanceAdjustmentAmount) || 0 : 0;
                return paymentTotal + advanceUsed;
            },
            total_due: function () {
                return this.grand_total_amt - this.total_paid;
            },
            totalQuantity() {
                return this.purchaseItems.length > 0
                    ? this.purchaseItems.reduce((total, item) => total + Number(item.quantity || 0), 0)
                    : 0;
            },
            subtotal() {
                return this.purchaseItems.reduce((total, item) => {
                    let price = Number(item.price || 0);
                    let quantity = Number(item.quantity || 0);
                    let discount = Number(item.discount || 0) / 100;
                    let tax = Number(item.tax || 0) / 100;

                    let totalBeforeTax = price * quantity * (1 - discount);
                    let totalWithTax = totalBeforeTax * (1 + tax);

                    return total + totalWithTax;
                }, 0);
            },
            pre_grand_total_amt() {
                return +this.subtotal + this.other_charges_amt - this.discount_to_all_amt;
            },
            grand_total_amt() {
                return Math.ceil(this.pre_grand_total_amt - this.total_round_off_amt - this.round_off_from_total);
            },
            // total_round_off_amt() {
            //     let num = Number(this.pre_grand_total_amt);
            //     if (isNaN(num)) return ''; // Handle invalid numbers
            //     let decimal = (num % 1).toFixed(2).substring(1); // Get only decimal part
            //     return decimal === ".00" ? "" : decimal; // Remove .00 if no decimals exist
            // }
            total_round_off_amt() {
                let num = Number(this.pre_grand_total_amt);
                if (isNaN(num)) return 0; // Return 0 instead of an empty string
                let decimal = (num % 1).toFixed(2).substring(1); // Get only decimal part
                return decimal === ".00" ? 0 : Number(decimal); // Ensure it's a number
            }
        },
        methods: {
            getData() {
                if (this.searchQuery.length > 1) {
                    this.fetchProducts();
                    this.dropdownVisible = true;
                } else {
                    this.searchResults = [];
                    this.dropdownVisible = false;
                }
            },

            calc_other_charges() {
                // console.log(this.subtotal);

                const subtotal = this.subtotal;
                let percentTotal = 0;
                let fixedTotal = 0;

                var other_charges_amount = document.querySelectorAll('.other_charges_amount');
                other_charges_amount = [...other_charges_amount];
                other_charges_amount.forEach((element, index) => {
                    var type = document.querySelector('.other_charges_type' + index);
                    var value = +element.value;
                    // console.log("type.value -- ", type.value);

                    if (type.value === "percent") {
                        percentTotal += (subtotal * value) / 100;
                    } else {
                        fixedTotal += value;
                    }

                });
                this.other_charges_amt = percentTotal + fixedTotal;
            },

            hideDropdown(event) {
                // Ensure the dropdown is hidden when clicking anywhere outside
                if (
                    this.$refs.searchDropdown &&
                    !this.$refs.searchDropdown.contains(event.target) &&
                    !this.$refs.searchInput.contains(event.target)
                ) {
                    this.dropdownVisible = false;
                }
            },

            toggleDropdown() {
                // Manually toggle the dropdown visibility
                this.dropdownVisible = !this.dropdownVisible;
            },

            fetchProducts: debounce(function () {
                this.loadingMore = true;
                axios.post(
                    `/api/search/products?query=${this.searchQuery}`,
                    {}, //inputs body
                    {
                        headers: {
                            Authorization: 'GenericCommerceV1-SBW7583837NUDD82'
                        }
                    }
                )
                    .then(response => {
                        try {
                            this.searchResults = response.data.data.data;
                        } catch (e) {

                        }
                        this.allProductsLoaded = true;
                    })
                    .catch(error => {
                        console.log("Error fetching products:", error);
                    })
                    .finally(() => {
                        this.loadingMore = false;
                    });
            }, 500),

            getRooms() {
                // if (this.selectedWarehouse) {
                //     axios.get(`/api/get-rooms/${this.selectedWarehouse}`).then((response) => {
                //         this.rooms = response.data;
                //         this.cartoons = [];
                //     }).catch(error => {
                //         console.error("Error fetching rooms:", error);
                //     });
                // }
            },

            getCartoons() {
                // if (this.selectedRoom) {
                //     axios.get(`/api/get-cartoons/${this.selectedWarehouse}/${this.selectedRoom}`).then((response) => {
                //         this.cartoons = response.data;
                //     }).catch(error => {
                //         console.error("Error fetching cartoons:", error);
                //     });
                // }
            },

            addRow(product) {
                // Check if customer is selected
                const customerId = $('#customer_id').val();
                if (!customerId) {
                    toastr.error('Must add customer first', 'Select Customer');
                    $('#customer_id').select2('open');
                    return;
                }

                // Determine if product has variants or unit pricing
                const hasVariant = product.has_variant == 1 && product.variants && product.variants.length > 0;
                const hasUnitPrice = product.has_unit_price && product.unit_prices && product.unit_prices.length > 0;

                // For products with variants or unit pricing, we need to wait for selection
                // For now, just add the product and let user select from dropdown
                let productPrice = product.price;
                let productStock = product.stock;
                let selectedVariant = null;
                let selectedUnitPrice = null;

                // If has variants, use first variant as default (or prompt user to select)
                if (hasVariant) {
                    selectedVariant = product.variants[0];
                    // Prioritize discount_price over regular price
                    if (selectedVariant.discount_price && selectedVariant.discount_price > 0) {
                        productPrice = selectedVariant.discount_price;
                    } else if (selectedVariant.price) {
                        productPrice = selectedVariant.price;
                    } else if (product.discount_price && product.discount_price > 0) {
                        productPrice = product.discount_price;
                    }
                    productStock = selectedVariant.stock;
                } 
                // If has unit pricing, use first unit price as default
                else if (hasUnitPrice) {
                    selectedUnitPrice = product.unit_prices[0];
                    // Prioritize discount_price over regular price
                    if (selectedUnitPrice.discount_price && selectedUnitPrice.discount_price > 0) {
                        productPrice = selectedUnitPrice.discount_price;
                    } else if (selectedUnitPrice.price) {
                        productPrice = selectedUnitPrice.price;
                    } else if (product.discount_price && product.discount_price > 0) {
                        productPrice = product.discount_price;
                    }
                } else {
                    // For regular products without variants or unit pricing
                    if (product.discount_price && product.discount_price > 0) {
                        productPrice = product.discount_price;
                    }
                }

                // Check if product already exists in cart with same variant/unit
                const existingItem = this.purchaseItems.find(item => {
                    if (item.product_id === product.id && item.isVisible) {
                        // Check variant match
                        if (hasVariant && selectedVariant) {
                            return item.selected_variant_id === selectedVariant.id;
                        }
                        // Check unit price match
                        if (hasUnitPrice && selectedUnitPrice) {
                            return item.selected_unit_price_id === selectedUnitPrice.id;
                        }
                        // No variant or unit price, just match product
                        return !item.selected_variant_id && !item.selected_unit_price_id;
                    }
                    return false;
                });

                if (existingItem) {
                    // Product exists with same variant/unit, increment quantity
                    existingItem.quantity = Number(existingItem.quantity) + 1;
                } else {
                    // Product doesn't exist, add as new item
                    const newItem = {
                        isVisible: true,
                        id: product.id,
                        product_id: product.id,
                        name: product.name,
                        price: productPrice,
                        quantity: 1,
                        discount: product.discount_parcent ? product.discount_parcent : 0,
                        tax: 0,
                        total: productPrice * 1,  // Initial total calculation
                        
                        // Variant/Unit Price support
                        has_variant: hasVariant,
                        has_unit_price: hasUnitPrice,
                        variants: hasVariant ? product.variants : [],
                        unit_prices: hasUnitPrice ? product.unit_prices : [],
                        selected_variant_id: selectedVariant ? selectedVariant.id : null,
                        selected_variant: selectedVariant,
                        selected_unit_price_id: selectedUnitPrice ? selectedUnitPrice.id : null,
                        selected_unit_price: selectedUnitPrice,
                        available_stock: productStock
                    };
                    this.purchaseItems.push(newItem);
                }

                this.searchQuery = '';  // Clear search query after adding item
                this.searchResults = [];  // Clear search results
                this.dropdownVisible = false;  // Hide dropdown
            },

            removeRow(index) {
                this.purchaseItems.splice(index, 1);
            },

            onVariantChange(item) {
                // Update price and stock when variant is selected
                const selectedVariant = item.variants.find(v => v.id == item.selected_variant_id);
                if (selectedVariant) {
                    item.selected_variant = selectedVariant;
                    
                    // Use variant's discount_price if available, otherwise use variant's regular price
                    // If variant price is null, fall back to base product price
                    if (selectedVariant.discount_price && selectedVariant.discount_price > 0) {
                        item.price = selectedVariant.discount_price;
                    } else if (selectedVariant.price) {
                        item.price = selectedVariant.price;
                    }
                    // If variant has no price, keep the original product price
                    
                    item.available_stock = selectedVariant.stock;
                }
            },

            onUnitPriceChange(item) {
                // Update price when unit price is selected
                const selectedUnit = item.unit_prices.find(u => u.id == item.selected_unit_price_id);
                if (selectedUnit) {
                    item.selected_unit_price = selectedUnit;
                    
                    // Use unit's discount_price if available, otherwise use unit's regular price
                    if (selectedUnit.discount_price && selectedUnit.discount_price > 0) {
                        item.price = selectedUnit.discount_price;
                    } else if (selectedUnit.price) {
                        item.price = selectedUnit.price;
                    }
                    // If unit has no price, keep the original product price
                }
            },

            getItemTotalPrice(item) {
                let total = item.quantity * item.price;

                if (item.discount > 0) {
                    let discountAmount = (item.discount / 100) * total;
                    total -= discountAmount;
                }

                if (item.tax > 0) {
                    let taxAmount = (item.tax / 100) * total;
                    total += taxAmount;
                }

                return total;
            },
            validate_number: debounce(function (event, min = 0, max = 999999) {
                var value = event.target.value;
                let num = String(value).replace(/\D/g, '');
                num = parseInt(num || '0', 10);
                if (num > max) num = max;
                if (num < min) num = min;

                event.target.value = num;
                return num;
            }, 2000),
            handleArrowKeys(event, item, field, min = 1) {
                if (event.key === 'ArrowUp') {
                    event.preventDefault();
                    item[field] = Number(item[field] || min) + 1;
                } else if (event.key === 'ArrowDown') {
                    event.preventDefault();
                    const currentValue = Number(item[field] || min);
                    if (currentValue > min) {
                        item[field] = currentValue - 1;
                    }
                }
            },
            calculateDiscountOnAll() {
                let discount_all = 0;
                const discountValue = parseFloat(this.discount_on_all) || 0;

                if (discountValue <= 0) {
                    this.discount_to_all_amt = 0;
                    return;
                }

                if (this.discount_on_all_type == "in_percentage") {
                    discount_all = (discountValue / 100) * this.subtotal;
                } else if (this.discount_on_all_type == "in_fixed") {
                    discount_all = discountValue;
                }

                this.discount_to_all_amt = parseFloat(discount_all);
            },
            fetchCustomerPaymentInfo(customerId) {
                if (!customerId) {
                    this.resetCustomerInfo();
                    return;
                }

                axios.get(`/api/customer-payment-info/${customerId}`)
                    .then(response => {
                        if (response.data.success) {
                            this.customerDue = parseFloat(response.data.total_due);
                            this.customerAdvance = parseFloat(response.data.total_advance);
                            this.availableAdvance = parseFloat(response.data.available_advance);
                            this.hasAdvance = response.data.has_advance;
                            this.selectedCustomerId = customerId;
                            
                            // Load customer delivery info
                            this.loadCustomerDeliveryInfo(customerId);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching customer payment info:', error);
                        this.resetCustomerInfo();
                    });
            },
            resetCustomerInfo() {
                this.customerDue = 0;
                this.customerAdvance = 0;
                this.availableAdvance = 0;
                this.hasAdvance = false;
                this.useAdvance = false;
                this.advanceAdjustmentAmount = 0;
                this.selectedCustomerId = null;
            },
            toggleAdvanceAdjustment() {
                if (this.useAdvance) {
                    this.advanceAdjustmentAmount = Math.min(this.availableAdvance, this.grand_total_amt);
                } else {
                    this.advanceAdjustmentAmount = 0;
                }
            },
            validatePaymentAmount() {
                // Check if total payment exceeds grand total
                if (this.total_paid > this.grand_total_amt) {
                    toastr.error('Payment cannot exceed the due amount!', 'Warning')

                    // Calculate the excess amount
                    const excess = this.total_paid - this.grand_total_amt;

                    // Adjust payments to not exceed grand total
                    // Start from the last non-zero payment and reduce it
                    for (let key in this.payment_modes) {
                        if (this.payment_modes[key] > 0) {
                            const reduction = Math.min(this.payment_modes[key], excess);
                            this.payment_modes[key] = parseFloat((this.payment_modes[key] - reduction).toFixed(2));
                            if (this.total_paid <= this.grand_total_amt) {
                                break;
                            }
                        }
                    }

                    // If still exceeding, adjust advance
                    if (this.total_paid > this.grand_total_amt && this.useAdvance) {
                        const remaining_excess = this.total_paid - this.grand_total_amt;
                        this.advanceAdjustmentAmount = Math.max(0, this.advanceAdjustmentAmount - remaining_excess);
                    }
                }
            },
            setPaymentToDue(event, paymentKey) {
                // Set the payment value to the due amount
                if (this.total_due > 0) {
                    this.payment_modes[paymentKey] = parseFloat(this.total_due.toFixed(2));
                    
                    // Use nextTick to ensure the value is updated in the DOM, then select the text
                    this.$nextTick(() => {
                        event.target.select();
                    });
                } else {
                    // If no due amount, just select the text
                    event.target.select();
                }
            },
            save_order(event) {
                // Check if there's a due amount and due date is not set
                if (this.total_due > 0) {
                    const dueDateInput = document.getElementById('due_date');
                    const dueDateValue = dueDateInput.value;

                    if (!dueDateValue || dueDateValue.trim() === '') {
                        // Set default due date to 1 week from today
                        const today = new Date();
                        const oneWeekLater = new Date(today.setDate(today.getDate() + 7));
                        const formattedDate = oneWeekLater.toISOString().split('T')[0];

                        dueDateInput.value = formattedDate;
                        dueDateInput.focus();

                        toastr.warning('Due date has been set to 1 week from today. Please review or change if needed.', 'Due Date Required');
                        return; // Prevent form submission
                    }
                }

                // Prevent multiple submissions
                if (this.isSubmitting) {
                    return;
                }

                this.isSubmitting = true;

                let formData = new FormData(event.target);
                const isEdit = formData.get('product_order_id') !== null;
                
                // Add delivery_info if available
                if (this.deliveryInfo && this.deliveryInfo.delivery_method) {
                    formData.append('delivery_info', JSON.stringify(this.deliveryInfo));
                }

                axios.post(event.target.action, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    }
                })
                    .then(response => {
                        if (response.data.order && response.data.order.slug) {
                            toastr.success(isEdit ? 'Order has been updated successfully!' : 'Order has been added successfully!', 'Success');
                            // Open invoice in new tab
                            window.open('/order-invoice/' + response.data.order.slug, '_blank');
                            // Redirect to order list or refresh after a short delay
                            
                            // If not in edit state, then reload the page (otherwise do nothing - edit mode will already reload via navigation)
                            if (!isEdit) {
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1000);
                            }
                        }
                        this.isSubmitting = false;
                    })
                    .catch(error => {
                        console.error(error.response);
                        this.isSubmitting = false; // Re-enable button on error
                        if (error.response && error.response.status === 422) {
                            show_errors(error.response.data.errors);
                        } else if (error.response && error.response.data && error.response.data.message) {
                            toastr.error(error.response.data.message, 'Error');
                        }
                    })
            },
            async loadOrderData() {
                const orderDataElement = document.getElementById('formApp');
                const orderSlug = orderDataElement.getAttribute('data-order-slug');
                
                let orderData = null;

                // If slug is present, load data via axios (edit mode with faster initial load)
                if (orderSlug && orderSlug.trim() !== '') {
                    try {
                        toastr.info('Loading order data...', 'Please wait');
                        const response = await axios.get(`/api/edit/product-order/manage/${orderSlug}`);
                        
                        if (response.data.success && response.data.data) {
                            orderData = response.data.data;
                        } else {
                            toastr.error('Failed to load order data', 'Error');
                            return;
                        }
                    } catch (error) {
                        console.error('Error loading order data via axios:', error);
                        toastr.error('Failed to load order data', 'Error');
                        return;
                    }
                } else {
                    // Fallback to inline data (create mode or legacy)
                    const orderDataAttr = orderDataElement.getAttribute('data-order-data');
                    if (orderDataAttr && orderDataAttr !== '{}') {
                        try {
                            orderData = JSON.parse(orderDataAttr);
                        } catch (error) {
                            console.error('Error parsing order data:', error);
                            return;
                        }
                    }
                }

                if (orderData && orderData.id) {
                    // Populate form fields from order data
                    if (orderData.sale_date) {
                        const saleDateInput = document.getElementById('sale_date');
                        if (saleDateInput) saleDateInput.value = orderData.sale_date;
                    }
                    
                    if (orderData.due_date) {
                        const dueDateInput = document.getElementById('due_date');
                        if (dueDateInput) dueDateInput.value = orderData.due_date;
                    }
                    
                    if (orderData.reference) {
                        const referenceInput = document.getElementById('reference');
                        if (referenceInput) referenceInput.value = orderData.reference;
                    }
                    
                    if (orderData.note) {
                        const noteTextarea = document.getElementById('note');
                        if (noteTextarea) noteTextarea.value = orderData.note;
                    }
                    
                    // Set order status
                    if (orderData.order_status) {
                        const orderStatusSelect = document.getElementById('order_status');
                        if (orderStatusSelect) orderStatusSelect.value = orderData.order_status;
                    }
                    
                    // Set warehouse
                    if (orderData.product_warehouse_id) {
                        this.selectedWarehouse = orderData.product_warehouse_id;
                    }

                    // Set customer
                    if (orderData.customer_id) {
                        axios.get('/customers/' + orderData.customer_id)
                            .then(response => {
                                const option = new Option(response.data.name, response.data.id, true, true);
                                $('#customer_id').append(option).trigger('change');
                                $('#customer_id').val(orderData.customer_id).trigger('change');
                                this.fetchCustomerPaymentInfo(orderData.customer_id);
                            })
                            .catch(error => {
                                console.error('Error loading customer:', error);
                            });
                    }

                    // Helper function to capitalize first letter
                    const ucfirst = (str) => {
                        if (!str) return '';
                        return str.charAt(0).toUpperCase() + str.slice(1);
                    };
                    
                    // Helper function to format variant name from variant_values
                    const formatVariantName = (variant) => {
                        const variantValues = variant.variant_values || {};
                        let variantName = variant.combination_key || '';
                        
                        // If variant_values is an object, format it nicely
                        if (typeof variantValues === 'object' && !Array.isArray(variantValues) && Object.keys(variantValues).length > 0) {
                            const formattedValues = Object.entries(variantValues).map(([key, value]) => {
                                return ucfirst(key) + ': ' + value;
                            }).join(' | ');
                            if (formattedValues) {
                                variantName = formattedValues;
                            }
                        }
                        
                        return variantName;
                    };
                    
                    // Load products with variant/unit price support
                    if (orderData.order_products && orderData.order_products.length > 0) {
                        this.purchaseItems = orderData.order_products.map(item => {
                            const product = item.product || {};
                            
                            // Format variants from variant_combinations (API response format)
                            let variants = [];
                            if (product.variant_combinations && product.variant_combinations.length > 0) {
                                variants = product.variant_combinations.map(variant => ({
                                    id: variant.id,
                                    name: formatVariantName(variant),
                                    price: parseFloat(variant.price || 0),
                                    discount_price: variant.discount_price ? parseFloat(variant.discount_price) : null,
                                    stock: parseInt(variant.stock || 0),
                                    sku: variant.sku || '',
                                    combination_key: variant.combination_key || ''
                                }));
                            }
                            
                            // Format unit prices
                            let unitPrices = [];
                            if (product.unit_pricing && product.unit_pricing.length > 0) {
                                unitPrices = product.unit_pricing.map(unit => ({
                                    id: unit.id,
                                    unit_label: unit.unit_label || (unit.unit_title + ' (' + unit.unit_value + ')'),
                                    price: parseFloat(unit.price || 0),
                                    discount_price: unit.discount_price ? parseFloat(unit.discount_price) : null,
                                    unit_title: unit.unit_title || '',
                                    unit_value: unit.unit_value || ''
                                }));
                            }
                            
                            // Determine if product has variants or unit pricing
                            const hasVariant = product.has_variant == 1 && variants.length > 0;
                            const hasUnitPrice = unitPrices.length > 0;
                            
                            // Get selected variant - use variant object from response, or find from variants array
                            let selectedVariant = null;
                            if (item.variant_id) {
                                // First try to use the variant object from response
                                if (item.variant) {
                                    selectedVariant = {
                                        id: item.variant.id,
                                        name: formatVariantName(item.variant),
                                        price: parseFloat(item.variant.price || 0),
                                        discount_price: item.variant.discount_price ? parseFloat(item.variant.discount_price) : null,
                                        stock: parseInt(item.variant.stock || 0),
                                        sku: item.variant.sku || ''
                                    };
                                } else {
                                    // Fallback: find from variants array
                                    selectedVariant = variants.find(v => v.id == item.variant_id);
                                }
                            }
                            
                            // Get selected unit price
                            let selectedUnitPrice = null;
                            if (item.unit_price_id && unitPrices.length > 0) {
                                selectedUnitPrice = unitPrices.find(u => u.id == item.unit_price_id);
                            }

                            // Determine price - use sale_price from order
                            let itemPrice = parseFloat(item.sale_price || product.price || 0);
                            
                            // Calculate discount percentage from discount_amount and product_price
                            let discountPercent = 0;
                            if (item.discount_amount && item.product_price) {
                                const discountAmount = parseFloat(item.discount_amount || 0);
                                const productPrice = parseFloat(item.product_price || itemPrice);
                                if (productPrice > 0) {
                                    discountPercent = (discountAmount / productPrice) * 100;
                                }
                            } else if (item.discount_type === 'in_fixed' && item.discount_amount && item.product_price) {
                                // Handle fixed discount
                                const discountAmount = parseFloat(item.discount_amount || 0);
                                const productPrice = parseFloat(item.product_price || itemPrice);
                                if (productPrice > 0) {
                                    discountPercent = (discountAmount / productPrice) * 100;
                                }
                            } else if (item.discount_type === 'in_percentage') {
                                discountPercent = parseFloat(item.discount_amount || 0);
                            }

                            // Get available stock
                            let availableStock = parseInt(product.stock || 0);
                            if (selectedVariant) {
                                availableStock = selectedVariant.stock;
                            }

                            return {
                                isVisible: true,
                                id: item.id || item.product_id, // Keep order product id for updates
                                product_id: item.product_id,
                                name: item.product_name || product.name,
                                price: itemPrice,
                                quantity: parseInt(item.qty || 1),
                                discount: discountPercent,
                                discount_parcent: discountPercent,
                                tax: parseFloat(item.tax || 0),
                                total: parseFloat(item.total_price || itemPrice * parseInt(item.qty || 1)),
                                
                                // Variant/Unit Price support
                                has_variant: hasVariant,
                                has_unit_price: hasUnitPrice,
                                variants: variants,
                                unit_prices: unitPrices,
                                selected_variant_id: item.variant_id || null,
                                selected_variant: selectedVariant,
                                selected_unit_price_id: item.unit_price_id || null,
                                selected_unit_price: selectedUnitPrice,
                                available_stock: availableStock
                            };
                        });
                    }

                    // Set other charges - populate inputs based on order_charges array
                    if (orderData.other_charges && Array.isArray(orderData.other_charges)) {
                        this.$nextTick(() => {
                            orderData.other_charges.forEach((charge, index) => {
                                // Find matching input by title or index
                                const chargeInputs = document.querySelectorAll('.other_charges_amount');
                                const chargeTypes = document.querySelectorAll('.other_charges_type');
                                
                                // Try to find by matching title first
                                let foundIndex = -1;
                                chargeInputs.forEach((input, idx) => {
                                    // Check if this input's title matches
                                    const hiddenTitle = input.closest('tr')?.querySelector('input[type="hidden"]');
                                    if (hiddenTitle && hiddenTitle.value === charge.title) {
                                        foundIndex = idx;
                                    }
                                });
                                
                                // If found, populate it
                                if (foundIndex >= 0 && chargeInputs[foundIndex] && chargeTypes[foundIndex]) {
                                    chargeInputs[foundIndex].value = parseFloat(charge.amount || 0);
                                    chargeTypes[foundIndex].value = charge.type || 'fixed';
                                } else if (index < chargeInputs.length && chargeInputs[index] && chargeTypes[index]) {
                                    // Fallback to index matching
                                    chargeInputs[index].value = parseFloat(charge.amount || 0);
                                    chargeTypes[index].value = charge.type || 'fixed';
                                }
                            });
                        });
                    }

                    // Set discount on all
                    if (orderData.discount_amount) {
                        this.discount_on_all = parseFloat(orderData.discount_amount);
                        this.discount_on_all_type = orderData.discount_type || 'in_percentage';
                    }

                    // Set round off
                    if (orderData.round_off_from_total) {
                        this.round_off_from_total = parseFloat(orderData.round_off_from_total);
                    }

                    // Set payment modes
                    if (orderData.payments) {
                        const payments = orderData.payments;
                        if (payments.cash) this.payment_modes.cash = parseFloat(payments.cash);
                        if (payments.bkash) this.payment_modes.bkash = parseFloat(payments.bkash);
                        if (payments.rocket) this.payment_modes.rocket = parseFloat(payments.rocket);
                        if (payments.nogod) this.payment_modes.nogod = parseFloat(payments.nogod);
                        if (payments.credit) this.payment_modes.credit = parseFloat(payments.credit);
                        if (payments.cheque) this.payment_modes.cheque = parseFloat(payments.cheque);
                        if (payments.bank) this.payment_modes.bank = parseFloat(payments.bank);
                        if (payments.gateway) this.payment_modes.gateway = parseFloat(payments.gateway);

                        // Handle advance adjustment
                        if (payments.advance_adjustment && parseFloat(payments.advance_adjustment) > 0) {
                            this.useAdvance = true;
                            this.advanceAdjustmentAmount = parseFloat(payments.advance_adjustment);
                        }
                    }

                    // Trigger calculation of other charges
                    this.$nextTick(() => {
                        this.calc_other_charges();
                        if (orderSlug) {
                            toastr.success('Order data loaded successfully', 'Success');
                        }
                    });
                }
            },
            previewOrder() {
                // Validate that there are items in the order
                if (this.purchaseItems.length === 0) {
                    toastr.error('Please add at least one product to preview the order', 'Error');
                    return;
                }

                // Get customer info
                const customerId = $('#customer_id').val();
                if (!customerId) {
                    toastr.error('Please select a customer to preview the order', 'Error');
                    $('#customer_id').select2('open');
                    return;
                }

                // Get customer details from select2
                const customerSelect = $('#customer_id');
                const customerName = customerSelect.select2('data')[0]?.text || 'N/A';

                // Get form data
                const orderCode = $('input[name="order_code"]').val() || 'DRAFT-' + Date.now();
                const saleDate = $('input[name="sale_date"]').val() || new Date().toISOString().split('T')[0];
                const dueDate = $('input[name="due_date"]').val() || '';
                const reference = $('input[name="reference"]').val() || '';
                const note = $('textarea[name="note"]').val() || '';
                const warehouse = this.warehouses.find(w => w.id == this.selectedWarehouse);

                // Build invoice HTML using the compact design
                const invoiceHTML = this.generateInvoicePreview({
                    orderCode,
                    saleDate,
                    dueDate,
                    reference,
                    note,
                    customerName,
                    warehouse: warehouse?.name || 'N/A'
                });

                // Inject into modal and show
                document.getElementById('invoicePreviewContent').innerHTML = invoiceHTML;
                $('#orderPreviewModal').modal('show');
            },
            generateInvoicePreview(orderData) {
                const { orderCode, saleDate, dueDate, reference, note, customerName, warehouse } = orderData;

                // Format date
                const formatDate = (dateStr) => {
                    if (!dateStr) return 'N/A';
                    const date = new Date(dateStr);
                    return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
                };

                // Generate products table rows
                let productsRows = '';
                this.purchaseItems.forEach((item, index) => {
                    // Build product name with variant/unit info
                    let productName = item.name;
                    let variantInfo = '';
                    
                    if (item.selected_variant) {
                        variantInfo = `<br/><small class="text-muted">${item.selected_variant.name}</small>`;
                    }
                    
                    if (item.selected_unit_price) {
                        variantInfo = `<br/><small class="text-muted">Unit: ${item.selected_unit_price.unit_label}</small>`;
                    }
                    
                    productsRows += `
                        <tr>
                            <td>${index + 1}</td>
                            <td><strong>${productName}</strong>${variantInfo}</td>
                            <td>৳${Number(item.price || 0).toFixed(2)}</td>
                            <td>${item.quantity}</td>
                            <td>${item.discount || 0}%</td>
                            <td>${item.tax || 0}%</td>
                            <td>৳${this.getItemTotalPrice(item).toFixed(2)}</td>
                        </tr>
                    `;
                });

                // Generate payment list
                let paymentList = '';
                for (const [method, amount] of Object.entries(this.payment_modes)) {
                    if (amount > 0) {
                        paymentList += `
                            <li>
                                <span><i class="fas fa-check-circle" style="color: #28a745;"></i> ${method.charAt(0).toUpperCase() + method.slice(1)}</span>
                                <strong>৳${Number(amount).toFixed(2)}</strong>
                            </li>
                        `;
                    }
                }

                // Advance payment if used
                if (this.useAdvance && this.advanceAdjustmentAmount > 0) {
                    paymentList += `
                        <li>
                            <span><i class="fas fa-check-circle" style="color: #28a745;"></i> Advance Adjustment</span>
                            <strong>৳${Number(this.advanceAdjustmentAmount).toFixed(2)}</strong>
                        </li>
                    `;
                }

                return `
                    <style>
                        .invoice-preview { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 13px; }
                        .invoice-preview .invoice-header { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; padding-bottom: 10px; border-bottom: 2px solid #333; margin-bottom: 10px; }
                        .invoice-preview .info-section { font-size: 12px; }
                        .invoice-preview .info-section h6 { font-size: 13px; font-weight: 700; color: #333; margin: 0 0 6px 0; padding-bottom: 4px; border-bottom: 1px solid #eee; }
                        .invoice-preview .info-section p { margin: 3px 0; line-height: 1.5; color: #555; }
                        .invoice-preview .products-table { width: 100%; border-collapse: collapse; font-size: 12px; margin: 15px 0; }
                        .invoice-preview .products-table thead { background: #333; color: white; }
                        .invoice-preview .products-table thead th { padding: 6px 8px; text-align: left; font-weight: 600; font-size: 11px; border: 1px solid #222; }
                        .invoice-preview .products-table tbody tr { border-bottom: 1px solid #e0e0e0; }
                        .invoice-preview .products-table tbody tr:nth-child(even) { background: #f9f9f9; }
                        .invoice-preview .products-table tbody td { padding: 5px 8px; color: #555; border: 1px solid #e0e0e0; }
                        .invoice-preview .products-table tbody td:last-child, .invoice-preview .products-table thead th:last-child { text-align: right; }
                        .invoice-preview .products-table thead th:first-child, .invoice-preview .products-table tbody td:first-child { text-align: center; }
                        .invoice-preview .totals-section { display: grid; grid-template-columns: 1fr 300px; gap: 15px; margin-top: 15px; }
                        .invoice-preview .grand-total-text { font-size: 13px; padding: 10px; background: #f8f9fa; border-radius: 4px; }
                        .invoice-preview .totals-box { border: 2px solid #333; padding: 10px; }
                        .invoice-preview .total-row { display: flex; justify-content: space-between; padding: 4px 0; font-size: 12px; }
                        .invoice-preview .total-row.subtotal { border-bottom: 1px solid #ddd; padding-bottom: 5px; margin-bottom: 5px; }
                        .invoice-preview .total-row.grand-total { border-top: 2px solid #333; padding-top: 6px; margin-top: 6px; font-size: 14px; font-weight: 700; color: #333; }
                        .invoice-preview .payment-section { margin-top: 12px; padding: 10px; background: #f8f9fa; border: 1px solid #ddd; border-radius: 4px; }
                        .invoice-preview .payment-section h6 { font-size: 13px; font-weight: 700; color: #333; margin: 0 0 8px 0; }
                        .invoice-preview .payment-list { list-style: none; padding: 0; margin: 0; font-size: 12px; }
                        .invoice-preview .payment-list li { padding: 4px 0; border-bottom: 1px dotted #ddd; display: flex; justify-content: space-between; }
                        .invoice-preview .payment-totals { margin-top: 8px; padding-top: 8px; border-top: 2px solid #ddd; }
                        .invoice-preview .payment-totals div { display: flex; justify-content: space-between; padding: 3px 0; font-size: 12px; font-weight: 600; }
                        .invoice-preview .status-badge { display: inline-block; padding: 2px 8px; border-radius: 3px; font-size: 10px; font-weight: 700; text-transform: uppercase; background: #fff3cd; color: #856404; }
                    </style>
                    
                    <div class="invoice-preview">
                        <div class="invoice-header">
                            <div class="info-section">
                                <h6>INVOICE DETAILS</h6>
                                <p><strong>Invoice No:</strong> ${orderCode}</p>
                                <p><strong>Date:</strong> ${formatDate(saleDate)}</p>
                                ${dueDate ? `<p><strong>Due Date:</strong> ${formatDate(dueDate)}</p>` : ''}
                                <p><strong>Status:</strong> <span class="status-badge">Draft</span></p>
                                ${reference ? `<p><strong>Reference:</strong> ${reference}</p>` : ''}
                                ${warehouse ? `<p><strong>Warehouse:</strong> ${warehouse}</p>` : ''}
                            </div>
                            
                            <div class="info-section">
                                <h6>CUSTOMER INFORMATION</h6>
                                <p><strong>Name:</strong> ${customerName}</p>
                            </div>
                        </div>

                        <table class="products-table">
                            <thead>
                                <tr>
                                    <th style="width: 4%;">#</th>
                                    <th style="width: 38%;">Product Name</th>
                                    <th style="width: 12%;">Unit Price</th>
                                    <th style="width: 8%;">Qty</th>
                                    <th style="width: 10%;">Disc (%)</th>
                                    <th style="width: 10%;">Tax (%)</th>
                                    <th style="width: 18%;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${productsRows}
                            </tbody>
                        </table>

                        <div class="totals-section">
                            <div class="grand-total-text">
                                <strong>Grand Total (in words):</strong>
                                ${this.numberToWords(this.grand_total_amt)} Taka Only
                            </div>
                            
                            <div class="totals-box">
                                <div class="total-row subtotal">
                                    <span>Subtotal:</span>
                                    <span>৳${this.subtotal.toFixed(2)}</span>
                                </div>
                                
                                ${this.other_charges_amt > 0 ? `
                                <div class="total-row">
                                    <span>Other Charges:</span>
                                    <span>৳${this.other_charges_amt.toFixed(2)}</span>
                                </div>
                                ` : ''}
                                
                                ${this.discount_to_all_amt > 0 ? `
                                <div class="total-row" style="color: #dc3545;">
                                    <span>Discount:</span>
                                    <span>- ৳${this.discount_to_all_amt.toFixed(2)}</span>
                                </div>
                                ` : ''}
                                
                                ${this.round_off_from_total != 0 ? `
                                <div class="total-row">
                                    <span>Round Off:</span>
                                    <span>৳${this.round_off_from_total.toFixed(2)}</span>
                                </div>
                                ` : ''}
                                
                                <div class="total-row grand-total">
                                    <span>Grand Total:</span>
                                    <span>৳${this.grand_total_amt.toFixed(2)}</span>
                                </div>
                            </div>
                        </div>

                        ${paymentList ? `
                        <div class="payment-section">
                            <h6><i class="fas fa-money-bill-wave"></i> Payment Information</h6>
                            <ul class="payment-list">
                                ${paymentList}
                            </ul>
                            <div class="payment-totals">
                                <div>
                                    <span>Total Paid:</span>
                                    <span style="color: #28a745;">৳${this.total_paid.toFixed(2)}</span>
                                </div>
                                ${this.total_due > 0 ? `
                                <div>
                                    <span>Amount Due:</span>
                                    <span style="color: #dc3545;">৳${this.total_due.toFixed(2)}</span>
                                </div>
                                ` : ''}
                            </div>
                        </div>
                        ` : ''}

                        ${note ? `
                        <div style="margin-top: 10px; padding: 8px; background: #fff9e6; border-left: 3px solid #ffc107; font-size: 11px;">
                            <p style="margin: 0;"><strong>Note:</strong> ${note}</p>
                        </div>
                        ` : ''}
                    </div>
                `;
            },
            numberToWords(num) {
                // Simple number to words conversion (you can enhance this)
                if (num === 0) return 'Zero';

                const units = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
                const teens = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
                const tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

                const convertLessThanThousand = (n) => {
                    if (n === 0) return '';
                    if (n < 10) return units[n];
                    if (n < 20) return teens[n - 10];
                    if (n < 100) return tens[Math.floor(n / 10)] + (n % 10 !== 0 ? ' ' + units[n % 10] : '');
                    return units[Math.floor(n / 100)] + ' Hundred' + (n % 100 !== 0 ? ' ' + convertLessThanThousand(n % 100) : '');
                };

                num = Math.floor(num);

                if (num < 1000) return convertLessThanThousand(num);
                if (num < 100000) {
                    const thousands = Math.floor(num / 1000);
                    const remainder = num % 1000;
                    return convertLessThanThousand(thousands) + ' Thousand' + (remainder !== 0 ? ' ' + convertLessThanThousand(remainder) : '');
                }
                if (num < 10000000) {
                    const lakhs = Math.floor(num / 100000);
                    const remainder = num % 100000;
                    return convertLessThanThousand(lakhs) + ' Lakh' + (remainder !== 0 ? ' ' + this.numberToWords(remainder) : '');
                }

                const crores = Math.floor(num / 10000000);
                const remainder = num % 10000000;
                return convertLessThanThousand(crores) + ' Crore' + (remainder !== 0 ? ' ' + this.numberToWords(remainder) : '');
            },
            proceedWithOrder() {
                // Close the modal
                $('#orderPreviewModal').modal('hide');

                // Trigger form submission
                const form = document.querySelector('#formApp form');
                if (form) {
                    // Create and dispatch a submit event
                    const submitEvent = new Event('submit', { bubbles: true, cancelable: true });
                    form.dispatchEvent(submitEvent);
                }
            },
            closePreviewModal() {
                // Close the preview modal
                $('#orderPreviewModal').modal('hide')
            },
            
            // Delivery Information Methods
            loadDistricts() {
                axios.get('/api/districts')
                    .then(response => {
                        this.districts = response.data;
                    })
                    .catch(error => {
                        console.error('Error loading districts:', error);
                    });
            },
            
            // Load Pathao cities
            loadPathaoCities() {
                this.pathaoLoading.cities = true;
                this.pathaoError = '';
                
                axios.get('/api/v1/delivery/pathao/cities')
                    .then(response => {
                        if (response.data && response.data.data && response.data.data.data) {
                            this.pathaoCities = response.data.data.data;
                        }
                        this.pathaoLoading.cities = false;
                    })
                    .catch(error => {
                        console.error('Error loading Pathao cities:', error);
                        this.pathaoError = 'Failed to load cities';
                        this.pathaoLoading.cities = false;
                    });
            },
            
            // Handle delivery method change
            onDeliveryMethodChange() {
                // Load Pathao cities when Pathao is selected
                if (this.deliveryInfo.delivery_method === 'pathao' && this.pathaoCities.length === 0) {
                    this.loadPathaoCities();
                }
            },
            
            // Handle Pathao city change
            onPathaoCityChange() {
                this.deliveryInfo.pathao_zone_id = '';
                this.deliveryInfo.pathao_area_id = '';
                this.pathaoZones = [];
                this.pathaoAreas = [];
                
                if (!this.deliveryInfo.pathao_city_id) return;
                
                this.pathaoLoading.zones = true;
                this.pathaoError = '';
                
                axios.get(`/api/v1/delivery/pathao/zones/${this.deliveryInfo.pathao_city_id}`)
                    .then(response => {
                        if (response.data && response.data.data && response.data.data.data) {
                            this.pathaoZones = response.data.data.data;
                        }
                        this.pathaoLoading.zones = false;
                    })
                    .catch(error => {
                        console.error('Error loading Pathao zones:', error);
                        this.pathaoError = 'Failed to load zones';
                        this.pathaoLoading.zones = false;
                    });
            },
            
            // Handle Pathao zone change
            onPathaoZoneChange() {
                this.deliveryInfo.pathao_area_id = '';
                this.pathaoAreas = [];
                
                if (!this.deliveryInfo.pathao_zone_id) return;
                
                this.pathaoLoading.areas = true;
                this.pathaoError = '';
                
                axios.get(`/api/v1/delivery/pathao/areas/${this.deliveryInfo.pathao_zone_id}`)
                    .then(response => {
                        if (response.data && response.data.data && response.data.data.data) {
                            this.pathaoAreas = response.data.data.data;
                        }
                        this.pathaoLoading.areas = false;
                    })
                    .catch(error => {
                        console.error('Error loading Pathao areas:', error);
                        this.pathaoError = 'Failed to load areas';
                        this.pathaoLoading.areas = false;
                    });
            },
            
            // Handle Pathao area change
            onPathaoAreaChange() {
                // Calculate price when area is selected
                this.calculatePathaoPrice();
            },
            
            // Calculate Pathao delivery price
            calculatePathaoPrice() {
                if (!this.deliveryInfo.pathao_city_id || !this.deliveryInfo.pathao_zone_id || 
                    !this.deliveryInfo.pathao_item_weight) {
                    return;
                }
                
                this.pathaoLoading.price = true;
                this.pathaoError = '';
                
                const priceData = {
                    item_type: parseInt(this.deliveryInfo.pathao_item_type),
                    delivery_type: parseInt(this.deliveryInfo.pathao_delivery_type),
                    item_weight: parseFloat(this.deliveryInfo.pathao_item_weight),
                    recipient_city: parseInt(this.deliveryInfo.pathao_city_id),
                    recipient_zone: parseInt(this.deliveryInfo.pathao_zone_id)
                };
                
                axios.post('/api/v1/delivery/pathao/price-plan', priceData, {
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                })
                    .then(response => {
                        if (response.data && response.data.data && response.data.data.price) {
                            this.deliveryInfo.pathao_delivery_cost = response.data.data.price;
                        }
                        this.pathaoLoading.price = false;
                    })
                    .catch(error => {
                        console.error('Error calculating Pathao price:', error);
                        this.pathaoError = 'Failed to calculate delivery cost';
                        this.pathaoLoading.price = false;
                    });
            },
            
            loadCustomerDeliveryInfo(customerId) {
                axios.get(`/api/customer-delivery-info/${customerId}`)
                    .then(response => {
                        if (response.data.success && response.data.delivery_info) {
                            const info = response.data.delivery_info;
                            this.deliveryInfo = {
                                receiver_name: info.receiver_name || '',
                                receiver_phone: info.receiver_phone || '',
                                customer_phone: info.customer_phone || '',
                                district: info.district || '',
                                upazila: info.upazila || '',
                                thana: info.thana || '',
                                post_office: info.post_office || '',
                                full_address: info.full_address || '',
                                delivery_method: info.delivery_method || 'courier',
                                courier_name: info.courier_name || '',
                                courier_name_custom: info.courier_name_custom || ''
                            };
                            
                            // Load upazilas if district is set
                            if (this.deliveryInfo.district) {
                                this.loadUpazilas();
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error loading delivery info:', error);
                    });
            },
            
            saveDeliveryInfo() {
                const customerId = $('#customer_id').val();
                if (!customerId) {
                    toastr.error('Please select a customer first', 'Error');
                    return;
                }
                
                // Validate required fields
                if (!this.deliveryInfo.receiver_name || !this.deliveryInfo.receiver_phone || 
                    !this.deliveryInfo.full_address || !this.deliveryInfo.delivery_method) {
                    toastr.error('Please fill all required fields', 'Error');
                    return;
                }
                
                // Validate Pathao-specific fields if Pathao is selected
                if (this.deliveryInfo.delivery_method === 'pathao') {
                    if (!this.deliveryInfo.pathao_city_id || !this.deliveryInfo.pathao_zone_id || 
                        !this.deliveryInfo.pathao_area_id || !this.deliveryInfo.pathao_item_weight) {
                        toastr.error('Please complete all Pathao delivery details', 'Error');
                        return;
                    }
                }
                
                // Validate courier name if courier is selected
                if (this.deliveryInfo.delivery_method === 'courier') {
                    const finalCourierName = this.deliveryInfo.courier_name === 'other' 
                        ? this.deliveryInfo.courier_name_custom 
                        : this.deliveryInfo.courier_name;
                    
                    if (!finalCourierName) {
                        toastr.error('Please select or enter courier name', 'Error');
                        return;
                    }
                }
                
                // All validations passed
                toastr.success('Delivery information saved successfully', 'Success');
                $('#deliveryInfoModal').modal('hide');
            },
            printPreview() {
                var printContent = document.getElementById('invoicePreviewContent').innerHTML;
                
                // Create a new window for printing
                var printWindow = window.open('', '_blank', 'width=900,height=650');
                
                if (!printWindow) {
                    alert('Please allow pop-ups for this site to use the print preview feature.');
                    return;
                }
                
                // Write content to the new window line by line
                printWindow.document.open();
                printWindow.document.write('<!DOCTYPE html>');
                printWindow.document.write('<html lang="en">');
                printWindow.document.write('<head>');
                printWindow.document.write('<meta charset="UTF-8">');
                printWindow.document.write('<meta name="viewport" content="width=device-width, initial-scale=1.0">');
                printWindow.document.write('<title>Print Invoice Preview</title>');
                printWindow.document.write('<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">');
                printWindow.document.write('<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">');
                printWindow.document.write('<style>');
                printWindow.document.write('@media print { body { margin: 0; padding: 10px; } }');
                printWindow.document.write('@page { size: A4; margin: 10mm; }');
                printWindow.document.write('body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; padding: 20px; font-size: 13px; }');
                printWindow.document.write('.invoice-preview { max-width: 210mm; margin: 0 auto; background: white; }');
                printWindow.document.write('</style>');
                printWindow.document.write('</head>');
                printWindow.document.write('<body>');
                printWindow.document.write(printContent);
                printWindow.document.write('<' + 'script>');
                printWindow.document.write('window.onload = function() { window.print(); };');
                printWindow.document.write('<' + '/script>');
                printWindow.document.write('</body>');
                printWindow.document.write('</html>');
                printWindow.document.close();
            }
        },

        watch: {
            searchQuery: function (newQuery) {
                if (newQuery.trim() === "") {
                    this.searchResults = [];
                    this.allProductsLoaded = false;
                    this.dropdownVisible = false;
                } else {
                    this.getData();
                }
            },

            selectedWarehouse(newWarehouse) {
                this.selectedRoom = null;
                this.getRooms();
            },

            selectedRoom(newRoom) {
                this.getCartoons();
            },

            other_charges_input_amount() {
                let other_charges = (this.other_charges_input_amount / 100) * this.subtotal;
                this.other_charges_amt = other_charges;
            },

            other_charges_type() {
                let other_charges = (this.other_charges_input_amount / 100) * this.subtotal;
                this.other_charges_amt = other_charges;
            },

            discount_on_all() {
                this.calculateDiscountOnAll();
            },

            discount_on_all_type() {
                this.calculateDiscountOnAll();
            },

            advanceAdjustmentAmount() {
                // Ensure advance adjustment doesn't exceed available advance
                if (this.advanceAdjustmentAmount > this.availableAdvance) {
                    this.advanceAdjustmentAmount = this.availableAdvance;
                }
                // Ensure it's not negative
                if (this.advanceAdjustmentAmount < 0) {
                    this.advanceAdjustmentAmount = 0;
                }

                // Validate payment doesn't exceed due
                this.$nextTick(() => {
                    this.validatePaymentAmount();
                });
            },

            payment_modes: {
                handler: function (newValue, oldValue) {
                    // Check if customer is selected before allowing payment
                    const customerId = $('#customer_id').val();
                    if (!customerId) {
                        // Check if any payment value changed from 0 to something
                        for (let key in newValue) {
                            if (newValue[key] > 0 && (!oldValue || oldValue[key] === 0)) {
                                toastr.error('Must add customer first', 'Select Customer');
                                $('#customer_id').select2('open');
                                // Reset the payment value
                                this.$nextTick(() => {
                                    this.payment_modes[key] = 0;
                                });
                                return;
                            }
                        }
                    }

                    // Validate payment doesn't exceed due when any payment mode changes
                    this.$nextTick(() => {
                        this.validatePaymentAmount();
                    });
                },
                deep: true
            }

        },

        mounted() {
            window.addEventListener("click", this.hideDropdown); // Listen for clicks on the window

            const vm = this;

            $('#customer_id').select2({
                ajax: {
                    url: '/customers', // must return JSON: [{id:1, name:'Sajib'}, ...]
                    dataType: 'json',
                    delay: 250,
                    processResults: function (data) {
                        return {
                            results: data.map(function (item) {
                                return {
                                    id: item.id,
                                    text: item.name
                                };
                            })
                        };
                    },
                    cache: true
                },
                placeholder: 'Select Customer',
                allowClear: true,
                width: 'resolve'
            }).on('change', function () {
                const customerId = $(this).val();
                vm.fetchCustomerPaymentInfo(customerId);
            });

            // Load order data if editing
            this.loadOrderData();
            
            // Load districts for delivery info
            this.loadDistricts();
        },
        beforeDestroy() {
            window.removeEventListener("click", this.hideDropdown); // Cleanup event listener
        }
    });
};

// Print preview function
function printPreview() {
    var printContent = document.getElementById('invoicePreviewContent').innerHTML;

    // Create a new window for printing
    var printWindow = window.open('', '_blank', 'width=900,height=650');

    if (!printWindow) {
        alert('Please allow pop-ups for this site to use the print preview feature.');
        return;
    }

    // Write content to the new window line by line
    printWindow.document.open();
    printWindow.document.write('<!DOCTYPE html>');
    printWindow.document.write('<html lang="en">');
    printWindow.document.write('<head>');
    printWindow.document.write('<meta charset="UTF-8">');
    printWindow.document.write('<meta name="viewport" content="width=device-width, initial-scale=1.0">');
    printWindow.document.write('<title>Print Invoice Preview</title>');
    printWindow.document.write('<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">');
    printWindow.document.write('<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">');
    printWindow.document.write('<style>');
    printWindow.document.write('@media print { body { margin: 0; padding: 10px; } }');
    printWindow.document.write('@page { size: A4; margin: 10mm; }');
    printWindow.document.write('body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; padding: 20px; font-size: 13px; }');
    printWindow.document.write('.invoice-preview { max-width: 210mm; margin: 0 auto; background: white; }');
    printWindow.document.write('</style>');
    printWindow.document.write('</head>');
    printWindow.document.write('<body>');
    printWindow.document.write(printContent);
    printWindow.document.write('<' + 'script>');
    printWindow.document.write('window.onload = function() { window.print(); };');
    printWindow.document.write('<' + '/script>');
    printWindow.document.write('</body>');
    printWindow.document.write('</html>');
    printWindow.document.close();
}