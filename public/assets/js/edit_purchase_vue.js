


window.onload = function () {
    new Vue({
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
                selectedSupplier: null,
                purchaseDate: '',
                purchaseItems: [],
                subtotalAmt: 0,
                finalTotal: 0,
                dropdownVisible: false,

                other_charges_input_amount: '',
                other_charges_type: '',
                discount_on_all: 0,
                discount_on_all_type: '',
                discount_to_all_amt: 0,

                other_charges_amt: 0,

                note: '',
                subtotal_amt: 0,

            };
        },
       
        async created() {
            await this.fetchEditData();
            await this.setOtherCharges();
        },
        computed: {
            totalQuantity() {
                return this.purchaseItems?.length > 0
                    ? this.purchaseItems?.reduce((total, item) => total + Number(item.quantity || 0), 0)
                    : 0;
            },
            subtotal() {
                let subtotal = this.purchaseItems?.reduce((total, item) => {
                    let price = Number(item.price || 0);
                    let quantity = Number(item.quantity || 0);
                    let discount = Number(item.discount || 0) / 100;
                    let tax = Number(item.tax || 0) / 100;
        
                    let totalBeforeTax = price * quantity * (1 - discount);
                    let totalWithTax = totalBeforeTax * (1 + tax);
        
                    return total + totalWithTax;
                }, 0);
                console.log("Subtotal: ", subtotal);
                return subtotal;
            },
            // pre_grand_total_amt() {
            //     return +this.subtotal + this.other_charges_amt - this.discount_to_all_amt;
            // },
            // grand_total_amt() {
            //     return Math.ceil(this.pre_grand_total_amt - this.total_round_off_amt);
            // },
            // total_round_off_amt() {
            //     let num = Number(this.pre_grand_total_amt);
            //     if (isNaN(num)) return ''; // Handle invalid numbers
            //     let decimal = (num % 1).toFixed(2).substring(1); // Get only decimal part
            //     return decimal === ".00" ? "" : decimal; // Remove .00 if no decimals exist
            // }
            pre_grand_total_amt() {
                return +this.subtotal + this.other_charges_amt - this.discount_to_all_amt;
            },
            grand_total_amt() {
                return Math.ceil(this.pre_grand_total_amt - this.total_round_off_amt);
            },
            total_round_off_amt() {
                let num = Number(this.pre_grand_total_amt);
                if (isNaN(num)) return 0; // Return 0 if invalid number
                let decimal = (num % 1).toFixed(2).substring(1); // Get only the decimal part
                return decimal === ".00" ? 0 : Number(decimal); // Return as number, not string
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

            async fetchEditData() {
                const path = window.location.pathname;
                const slug = path.split('/').pop();
                console.log("editSlug - ", slug);

                await axios.get(`${location.origin}/api/edit/purchase-product/quotation/${slug}`)
                    .then(response => {
                        this.editData = response.data.data;
                        console.log("this.editData g--- ", this.editData);

                        this.purchaseItems = this.editData.quotation_products.map(item => ({
                            ...item,
                            isVisible: true,
                            id: item.id,
                            product_id: item.product_id,
                            name: item.product_name,
                            price: item.product_price,
                            quantity: item.qty || 1,
                            discount: item.discount_amount || 0,
                            tax: item.tax || 0,
                            total: item.purchase_total
                        }));

                        // Set Warehouse
                        this.selectedWarehouse = this.editData.product_warehouse_id;
                        this.selectedSupplier = this.editData.product_supplier_id;
                        this.purchaseDate = this.editData.date;
                        this.other_charges_input_amount = this.editData.other_charge_percentage;
                        this.other_charges_type = this.editData.other_charge_type;
                        this.discount_on_all = this.editData.discount_amount;
                        this.note = this.editData.note;
                        this.subtotal_amt = parseFloat(this.editData.subtotal) || 0;
                        this.subtotal = parseFloat(this.editData.subtotal) || 0;
                        this.other_charges_amt = parseFloat(this.editData.other_charge_amount) || 0;
                        this.discount_to_all_amt = parseFloat(this.editData.calculated_discount_amount) || 0;
                        this.discount_on_all_type = this.editData.discount_type; 
                        this.total_round_off_amt = parseFloat(this.editData.round_off) || 0;
                        // Vue.set(this, 'total_round_off_amt', 0.86);
                        this.grand_total_amt = parseFloat(this.editData.total) || 0;

                        this.$nextTick(() => {
                            // Ensure Vue re-renders and selects the correct option
                            // console.log('Vue re-rendered, discount_on_all_type:', this.discount_on_all_type);
                        });

                        // console.log("this.subtotal_amt ", this.subtotal_amt );
                        // console.log("this.other_charges_amt ", this.other_charges_amt );

                        // Fetch Rooms based on selected warehouse
                        this.getRooms().then(() => {
                            // Once rooms are fetched, set the selectedRoom
                            this.selectedRoom = this.editData.product_warehouse_room_id;
                           
                            this.getCartoons().then(() => {
                                console.log("this.cartoons after fetch - ", this.cartoons);

                                // Check if cartoons exist before setting selectedCartoon
                                if (this.cartoons && this.cartoons.length > 0) {
                                    // Look for the cartoon that matches the selected ID
                                    const cartoon = this.cartoons.find(c => c.id === this.editData.product_warehouse_room_cartoon_id);
                                    if (cartoon) {
                                        this.selectedCartoon = cartoon.id;
                                        console.log("selectedCartoon after fetching:", this.selectedCartoon);
                                    } else {
                                        console.warn("No matching cartoon found for selected room");
                                    }
                                } else {
                                    console.warn("No cartoons available for selected room");
                                }
                            }).catch(error => {
                                console.error("Error fetching cartoons:", error);
                            });
                        });

                        console.log("this.selectedCartoon dropdown - ", this.selectedCartoon);


                        this.dropdownVisible = false;
                    })
                    .catch(error => {
                        console.error("Error fetching edit data:", error);
                    });
            },

            async setOtherCharges() {
                console.log("this.editData 2222- ", this.editData);
                
                try {
                    var decode_json = JSON.parse(this.editData.other_charge_type);
                    console.log("Decoded JSON: ", decode_json);

                    var other_charges_amount = document.querySelectorAll('.other_charges_title');
                    other_charges_amount = [...other_charges_amount];
                    other_charges_amount.forEach((element, index) => {
                        var type_data = decode_json.find((item) => item.title == element.value);
                        var type = document.querySelector('.other_charges_type' + index);
                        var amount = document.querySelector('.other_charges_amount' + index);
                        amount.value = type_data.amount;
                        type.value = type_data.type;
                        // var value = +element.value;
                        console.log("type_data -- ", type_data);
                    });
                    this.calc_other_charges();

                } catch (error) {
                    console.error("Error parsing JSON:", error);
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
                console.log(" percentTotal - ",  percentTotal);
                console.log(" fixedTotal - ",  fixedTotal);
                console.log(" total  - ",  percentTotal + fixedTotal);
                this.other_charges_amt = percentTotal + fixedTotal;


            },

            getEditIdFromUrl() {
                const urlParams = new URLSearchParams(window.location.search);
                return urlParams.get('slug');
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

            fetchProducts() {
                this.loadingMore = true;
                axios.get(`/api/products/search?query=${this.searchQuery}`)
                    .then(response => {
                        if (response.data.length === 0) {
                            this.allProductsLoaded = true;
                        } else {
                            this.searchResults = response.data;
                            this.allProductsLoaded = true;
                        }
                    })
                    .catch(error => {
                        console.log("Error fetching products:", error);
                    })
                    .finally(() => {
                        this.loadingMore = false;
                    });
            },

            // getRooms() {
            //     console.log("this.selectedWarehouse", this.selectedWarehouse);

            //     if (this.selectedWarehouse) {
            //         axios.get(`/api/get-rooms/${this.selectedWarehouse}`).then((response) => {
            //             this.rooms = response.data;
            //             console.log("this.rooms", this.rooms);

            //             this.cartoons = [];
            //         }).catch(error => {
            //             console.error("Error fetching rooms:", error);
            //         });
            //     }
            // },
            getRooms() {
                if (!this.selectedWarehouse) return Promise.resolve();

                return axios.get(`/api/get-rooms/${this.selectedWarehouse}`)
                    .then(response => {
                        this.rooms = response.data;
                        this.cartoons = [];
                        console.log("this.rooms", this.rooms);
                    })
                    .catch(error => {
                        console.error("Error fetching rooms:", error);
                    });
            },

            // getCartoons() {
            //     console.log("this.selectedRoom", this.selectedRoom);
            //     if (this.selectedRoom) {
            //         axios.get(`/api/get-cartoons/${this.selectedWarehouse}/${this.selectedRoom}`).then((response) => {
            //             this.cartoons = response.data;
            //         }).catch(error => {
            //             console.error("Error fetching cartoons:", error);
            //         });
            //     }
            // },
            getCartoons() {
                if (!this.selectedRoom) {
                    console.warn("No room selected for fetching cartoons");
                    return Promise.resolve();
                }
            
                console.log("Fetching cartoons for warehouse:", this.selectedWarehouse, "and room:", this.selectedRoom);
            
                return axios.get(`/api/get-cartoons/${this.selectedWarehouse}/${this.selectedRoom}`)
                    .then(response => {
                        this.cartoons = response.data;
                        console.log("Fetched cartoons: ", this.cartoons);
                    })
                    .catch(error => {
                        console.error("Error fetching cartoons:", error);
                    });
            },



            addRow(product) {
                const newItem = {
                    isVisible: true,
                    id: product.id,
                    name: product.name,
                    price: product.price,
                    quantity: 1,
                    discount: 0,
                    tax: 0,
                    total: product.price * 1  // Initial total calculation
                };
                this.purchaseItems.push(newItem);
                this.searchQuery = '';  // Clear search query after adding item
                this.dropdownVisible = false;
            },

            removeRow(index) {
                this.purchaseItems.splice(index, 1);
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
                console.log("other_charges - ", other_charges);
                this.other_charges_amt = other_charges;
            },

            other_charges_type() {
                let other_charges = (this.other_charges_input_amount / 100) * this.subtotal;
                console.log("other_charges - ", other_charges);
                this.other_charges_amt = other_charges;
            },

            discount_on_all() {
                let discount_all = 0;
                this.discount_on_all = +this.discount_on_all;

                if (this.discount_on_all <= 0) {
                    this.discount_on_all = 0.0;
                    // this.discount_on_all_type = "in_percentage";
                    this.discount_to_all_amt = 0.00;
                    return 0.00;
                }

                if (this.discount_on_all_type == "in_percentage") {
                    discount_all = (this.discount_on_all / 100) * this.subtotal;
                } else if (this.discount_on_all_type == "in_fixed") {
                    discount_all = +this.discount_on_all;
                }

                this.discount_to_all_amt = parseFloat(discount_all);
            },

            discount_on_all_type() {
                let discount_all = 0;
                if (this.discount_on_all_type == "in_percentage") {
                    discount_all = (this.discount_on_all / 100) * this.subtotal;
                    console.log("discount_all percent - ", discount_all);
                } else if (this.discount_on_all_type == "in_fixed") {
                    discount_all = this.discount_on_all;
                    console.log("discount_all fixed- ", discount_all);
                }
                this.discount_to_all_amt = discount_all;
            }

        },

        mounted() {
            window.addEventListener("click", this.hideDropdown); // Listen for clicks on the window
            console.log("Total Round Off Amt: ", this.total_round_off_amt);
            console.log("Grand Total Amt: ", this.grand_total_amt);
        },
        beforeDestroy() {
            window.removeEventListener("click", this.hideDropdown); // Cleanup event listener
        }
    });
};






