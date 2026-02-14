function debounce(func, wait) {
    let timeout;
    return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            func.apply(this, args);
        }, wait);
    };
}

var customer_list = `
    <div class="customer_list_wrapper">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>
                        <i class="fas fa-square"></i>
                    </th>
                    <th>Name</th>
                    <th>Total Orders</th>
                    <th>Due</th>
                    <th>Bal</th>
                    <th>Phone</th>
                    <th>Addresss</th>
                    <th class="text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="customer in customers.data" :key="customer.id">
                    <td>
                        <input type="radio" name="selected_customer" @click="set_customer(customer)">
                    </td>
                    <td>{{ customer.name }}</td>
                    <td>{{ customer.order_count }}</td>
                    <td>{{ customer.due_amount }}</td>
                    <td>{{ customer.available_advance }}</td>
                    <td>{{ customer.phone }}</td>
                    <td>{{ customer.address }}</td>
                    <td>
                        <div style="width: 180px; text-align: right;">
                            <button type="button" class="btn btn-sm btn-primary" @click="change_type('edit', customer)" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-info" @click="view_customer(customer)" title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" @click="delete_customer(customer)" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div>
        <ul v-if="customers.links" class="pagination">
            <li v-for="(page, index) in customers.links" :key="index" class="page-item" :class="page.active ? 'active' : ''">
                <a :href="page.url?page.url:'#'" @click.prevent="get_page_number(page.url)" class="page-link">
                    <span v-html="page.label"></span>
                    </a>
            </li>
        </ul>
    </div>
`;

var customer_view = `
    <div class="customer_view_wrapper">
        <div class="border p-4 rounded">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Customer Profile</h4>
                <button type="button" class="btn btn-sm btn-secondary" @click="change_type('list')">
                    <i class="fas fa-arrow-left"></i> Back
                </button>
            </div>
            
            <div v-if="viewCustomer" class="customer-profile">
                <!-- Basic Info -->
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-user"></i> Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Name:</strong> {{ viewCustomer.name || 'N/A' }}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Phone:</strong> {{ viewCustomer.phone || 'N/A' }}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Email:</strong> {{ viewCustomer.email || 'N/A' }}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Address:</strong> {{ viewCustomer.address || 'N/A' }}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Due Amount:</strong> <span class="text-danger">{{ formatMoney(viewCustomer.due_amount || 0) }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Available Advance:</strong> <span class="text-success">{{ formatMoney(viewCustomer.advance || 0) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Billing Addresses -->
                <div class="card mb-3">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-file-invoice"></i> Billing Addresses</h5>
                    </div>
                    <div class="card-body">
                        <div v-if="viewCustomer.billing_address && viewCustomer.billing_address.length > 0">
                            <div v-for="(addr, index) in viewCustomer.billing_address" :key="'billing-view-' + index" class="border p-3 mb-2">
                                <div class="row">
                                    <div class="col-md-6"><strong>Full Name:</strong> {{ addr.full_name || 'N/A' }}</div>
                                    <div class="col-md-6"><strong>Phone:</strong> {{ addr.phone || 'N/A' }}</div>
                                    <div class="col-12 mt-2"><strong>Address:</strong> {{ addr.address || 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-muted text-center py-3">
                            No billing addresses found
                        </div>
                    </div>
                </div>
                
                <!-- Shipping Addresses -->
                <div class="card mb-3">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-truck"></i> Shipping Addresses</h5>
                    </div>
                    <div class="card-body">
                        <div v-if="viewCustomer.shipping_address && viewCustomer.shipping_address.length > 0">
                            <div v-for="(addr, index) in viewCustomer.shipping_address" :key="'shipping-view-' + index" class="border p-3 mb-2">
                                <div class="row">
                                    <div class="col-md-6"><strong>Full Name:</strong> {{ addr.full_name || 'N/A' }}</div>
                                    <div class="col-md-6"><strong>Phone:</strong> {{ addr.phone || 'N/A' }}</div>
                                    <div class="col-12 mt-2"><strong>Address:</strong> {{ addr.address || 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-muted text-center py-3">
                            No shipping addresses found
                        </div>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary" @click="change_type('edit', viewCustomer)">
                        <i class="fas fa-edit"></i> Edit Customer
                    </button>
                </div>
            </div>
            
            <div v-else class="text-center p-4">
                <div class="spinner-border" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        </div>
    </div>
`;

var customer_form = `
    <div class="customer_form_wrapper">
        <form @submit.prevent="save_customer" id="customer_form" style="max-width: 768px;" class="border p-3 rounded">
            <h4 v-if="type == 'add'">Add Customer</h4>
            <h4 v-if="type == 'edit'">Update Customer</h4>
            <div class="form-group">
                <label for="name">Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" v-model="form_customer.name" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="phone" v-model="form_customer.phone" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" v-model="form_customer.email">
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <textarea class="form-control" id="address" v-model="form_customer.address" rows="2"></textarea>
            </div>
            <div>
            </div>
            <div class="form-group"">
                <label for="customer_source">Customer Source</label>
                <select class="form-control select2_el" id="customer_source" v-model="form_customer.customer_source_type_id">
                    <option value="">Select Customer Source</option>
                    <option v-for="source in this.customer_sources" :key="source.id" :value="source.id">{{ source.title }}</option>
                </select>
            </div>

            <!-- save as user -->
            <div class="form-group">
                <label>
                    <input type="checkbox" id="save_as_user" v-model="save_as_user">
                    Save as User
                </label>
            </div>
            <div v-if="save_as_user" class="border p-3 mb-3">
                <div class="form-group">
                    <label for="password">Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="password" v-model="password" placeholder="Enter password" required>
                </div>
            </div>

            <div class="border p-3 mb-3">
                <h5>Billing Address</h5>
                <div v-for="(billing_address, index) in billing_address" :key="'billing-' + index" class="border p-2 mb-2">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" class="form-control" v-model="billing_address.full_name" placeholder="Full Name">
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" class="form-control" v-model="billing_address.phone" placeholder="Phone">
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <textarea class="form-control" v-model="billing_address.address" rows="2" placeholder="Address"></textarea>
                    </div>
                    <div class="form-group">
                        <label>District</label>
                        <select class="form-control select2_el" v-model="billing_address.district">
                            <option :value="null">Select District</option>
                            <option v-for="district in districts" :key="district.district_id" :value="district">
                                {{ district.division_name }} > {{ district.district_name }}
                            </option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-sm btn-danger" @click="remove_billing_address(index)">
                        <i class="fas fa-trash"></i> Remove
                    </button>
                </div>
                <button type="button" class="btn btn-sm btn-primary" @click="add_billing_address">
                    <i class="fas fa-plus"></i> Add Billing Address
                </button>
            </div>
            <div class="border p-3 mb-3">
                <h5>Shipping Address</h5>
                <div v-for="(shipping_address, index) in shipping_address" :key="'shipping-' + index" class="border p-2 mb-2">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" class="form-control" v-model="shipping_address.full_name" placeholder="Full Name">
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" class="form-control" v-model="shipping_address.phone" placeholder="Phone">
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <textarea class="form-control" v-model="shipping_address.address" rows="2" placeholder="Address"></textarea>
                    </div>
                    <div class="form-group">
                        <label>District</label>
                        <select class="form-control select2_el" v-model="shipping_address.district">
                            <option :value="null">Select District</option>
                            <option v-for="district in districts" :key="district.district_id" :value="district">
                                {{ district.division_name }} > {{ district.district_name }}
                            </option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-sm btn-danger" @click="remove_shipping_address(index)">
                        <i class="fas fa-trash"></i> Remove
                    </button>
                </div>
                <button type="button" class="btn btn-sm btn-primary" @click="add_shipping_address">
                    <i class="fas fa-plus"></i> Add Shipping Address
                </button>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
`;

Vue.component('pos-customer-manage', {
    props: {
        setSelectedCustomer: {
            type: Function,
            required: true
        },
        customer_sources: {
            type: Array,
            required: true
        }
    },
    data: function () {
        return {
            window: window,
            customers: [],
            customer: {
                id: 1,
                name: 'walking customer',
                phone: '',
                image: null,
            },
            form_customer: {},
            viewCustomer: null,
            type: 'list', // list, add, edit, view
            search_query: '',
            page: 1,
            per_page: 10,
            billing_address: [{ full_name: '', phone: '', address: '', district: null }],
            shipping_address: [{ full_name: '', phone: '', address: '', district: null }],
            save_as_user: false,
            password: '',
            districts: [],
            show_customer_modal: false,
        }
    },
    watch: {
        customer: {
            handler: function (newVal) {
                this.setSelectedCustomer(newVal);
            },
            deep: true
        }
    },
    created: function () {
        this.get_customers();
        this.get_districts();
    },
    methods: {
        get_customers: function () {
            axios.get('/pos/desktop/customers', {
                params: {
                    q: this.search_query,
                    page: this.page,
                    per_page: this.per_page
                }
            })
                .then(response => {
                    this.customers = response.data.data;
                })
                .catch(error => {
                    console.log(error);
                });
        },
        get_page_number: function (url) {
            let query = new URL(url).searchParams;
            let page = query.get('page') || 1;
            this.page = parseInt(page);
            this.get_customers();
        },
        set_customer: function (customer) {
            this.customer = customer;
            this.show_customer_modal = false;
        },
        search_customers: debounce(function () {
            this.page = 1;
            this.get_customers();
        }, 500),
        view_customer: function (customer) {
            this.viewCustomer = null;
            this.type = 'view';
            axios.get('/pos/desktop/customers', {
                params: {
                    customer_id: customer.id,
                    first: true
                }
            })
                .then(response => {
                    if (response.data.success && response.data.data) {
                        this.viewCustomer = response.data.data;
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Customer not found',
                            confirmButtonText: 'OK'
                        });
                        this.change_type('list');
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load customer details',
                        confirmButtonText: 'OK'
                    });
                    console.error(error);
                    this.change_type('list');
                });
        },
        formatMoney: function (amount) {
            if (!amount && amount !== 0) return '0.00';
            return parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        },
        delete_customer: function (customer) {
            Swal.fire({
                title: 'Delete Customer?',
                text: 'Are you sure you want to delete ' + (customer.name || 'this customer') + '? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post('/pos/desktop/customers/delete', {
                        id: customer.id
                    })
                        .then(response => {
                            if (response.data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: response.data.message || 'Customer has been deleted.',
                                    confirmButtonText: 'OK',
                                    allowOutsideClick: false,
                                    allowEscapeKey: false
                                });
                                // Remove from list
                                const index = this.customers.data.findIndex(c => c.id === customer.id);
                                if (index !== -1) {
                                    this.customers.data.splice(index, 1);
                                }
                            }
                        })
                        .catch(error => {
                            const message = error.response?.data?.message || 'Failed to delete customer';
                            Swal.fire({
                                icon: 'error',
                                title: 'Cannot Delete',
                                text: message,
                                confirmButtonText: 'OK',
                                allowOutsideClick: false,
                                allowEscapeKey: false
                            });
                            console.error(error);
                        });
                }
            });
        },
        save_customer: function () {
            if (!this.form_customer.name || !this.form_customer.phone) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Validation Error',
                    text: 'Name and Phone are required',
                    confirmButtonText: 'OK',
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });
                return;
            }
            
            if (this.save_as_user && !this.password) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Validation Error',
                    text: 'Password is required when saving as user',
                    confirmButtonText: 'OK',
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });
                return;
            }
            
            const payload = {
                id: this.form_customer.id || null,
                name: this.form_customer.name,
                mobile: this.form_customer.phone,
                email: this.form_customer.email || null,
                address: this.form_customer.address || null,
                save_as_user: this.save_as_user ? 1 : 0,
                password: this.save_as_user ? this.password : null,
                image: this.form_customer.image || null,
                customer_source_type_id: this.form_customer.customer_source_type_id || null,
            };
            
            // Only include billing_address if it has at least one non-empty address
            const billingAddresses = this.billing_address.filter(addr => addr.full_name || addr.address).map(addr => {
                const addressData = {
                    full_name: addr.full_name || null,
                    phone: addr.phone || null,
                    address: addr.address || null,
                };
                // Add district data if selected
                if (addr.district && addr.district.district_id && addr.district.division_id) {
                    addressData.district_id = addr.district.district_id;
                    addressData.division_id = addr.district.division_id;
                }
                return addressData;
            });
            if (billingAddresses.length > 0) {
                payload.billing_address = billingAddresses;
            }
            
            // Only include shipping_address if it has at least one non-empty address
            const shippingAddresses = this.shipping_address.filter(addr => addr.full_name || addr.address).map(addr => {
                const addressData = {
                    full_name: addr.full_name || null,
                    phone: addr.phone || null,
                    address: addr.address || null,
                };
                // Add district data if selected
                if (addr.district && addr.district.district_id && addr.district.division_id) {
                    addressData.district_id = addr.district.district_id;
                    addressData.division_id = addr.district.division_id;
                }
                return addressData;
            });
            if (shippingAddresses.length > 0) {
                payload.shipping_address = shippingAddresses;
            }
            
            axios.post('/pos/desktop/customers/create', payload)
                .then(response => {
                    if (response.data.success) {
                        if (this.type === 'add') {
                            this.customers.data.unshift(response.data.data);
                            this.customer = response.data.data;
                        } else {
                            const index = this.customers.data.findIndex(c => c.id === this.customer.id);
                            if (index !== -1) {
                                this.$set(this.customers.data, index, response.data.data);
                            }
                        }
                        this.change_type('list');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.data.message || 'Customer saved successfully',
                            confirmButtonText: 'OK',
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        });
                    }
                })
                .catch(error => {
                    const message = error.response?.data?.message || 'Failed to save customer';
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: message,
                        confirmButtonText: 'OK',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    });
                    console.error(error);
                });
        },
        add_billing_address: function () {
            this.billing_address.push({ full_name: '', phone: '', address: '', district: null });
        },
        remove_billing_address: function (index) {
            this.billing_address.splice(index, 1);
            // If all removed, add one empty address for better UX
            if (this.billing_address.length === 0) {
                this.billing_address.push({ full_name: '', phone: '', address: '', district: null });
            }
        },
        add_shipping_address: function () {
            this.shipping_address.push({ full_name: '', phone: '', address: '', district: null });
        },
        remove_shipping_address: function (index) {
            this.shipping_address.splice(index, 1);
            // If all removed, add one empty address for better UX
            if (this.shipping_address.length === 0) {
                this.shipping_address.push({ full_name: '', phone: '', address: '', district: null });
            }
        },
        change_type: function (type = 'list', customerData = null) {
            this.type = type;
            if (type === 'add') {
                this.form_customer = { id: null, name: '', phone: '', email: '', address: '', image: null };
                this.billing_address = [{ full_name: '', phone: '', address: '', district: null }];
                this.shipping_address = [{ full_name: '', phone: '', address: '', district: null }];
                this.save_as_user = false;
                this.password = '';
                this.viewCustomer = null;
            } else if (type === 'edit' && customerData) {
                this.customer = {
                    id: customerData.id,
                    name: customerData.name || '',
                    phone: customerData.phone || customerData.mobile || '',
                    email: customerData.email || '',
                    address: customerData.address || '',
                };
                this.form_customer = customerData;
                let billingAddresses = customerData.billing_address && Array.isArray(customerData.billing_address) 
                    ? customerData.billing_address 
                    : (customerData.billing_address ? JSON.parse(customerData.billing_address) : [{ full_name: '', phone: '', address: '', district: null }]);
                
                // Map district data for billing addresses
                this.billing_address = billingAddresses.map(addr => {
                    const addressObj = {
                        full_name: addr.full_name || '',
                        phone: addr.phone || '',
                        address: addr.address || '',
                        district: null
                    };
                    // Find matching district object if division_id and district_id exist
                    if (addr.division_id && addr.district_id && this.districts.length > 0) {
                        const foundDistrict = this.districts.find(d => 
                            d.division_id == addr.division_id && d.district_id == addr.district_id
                        );
                        if (foundDistrict) {
                            addressObj.district = foundDistrict;
                        }
                    }
                    return addressObj;
                });
                
                let shippingAddresses = customerData.shipping_address && Array.isArray(customerData.shipping_address)
                    ? customerData.shipping_address
                    : (customerData.shipping_address ? JSON.parse(customerData.shipping_address) : [{ full_name: '', phone: '', address: '', district: null }]);
                
                // Map district data for shipping addresses
                this.shipping_address = shippingAddresses.map(addr => {
                    const addressObj = {
                        full_name: addr.full_name || '',
                        phone: addr.phone || '',
                        address: addr.address || '',
                        district: null
                    };
                    // Find matching district object if division_id and district_id exist
                    if (addr.division_id && addr.district_id && this.districts.length > 0) {
                        const foundDistrict = this.districts.find(d => 
                            d.division_id == addr.division_id && d.district_id == addr.district_id
                        );
                        if (foundDistrict) {
                            addressObj.district = foundDistrict;
                        }
                    }
                    return addressObj;
                });
                
                if (this.billing_address.length === 0) {
                    this.billing_address = [{ full_name: '', phone: '', address: '', district: null }];
                }
                if (this.shipping_address.length === 0) {
                    this.shipping_address = [{ full_name: '', phone: '', address: '', district: null }];
                }
            } else {
                this.customer = null;
                this.viewCustomer = null;
                this.billing_address = [{ full_name: '', phone: '', address: '', district: null }];
                this.shipping_address = [{ full_name: '', phone: '', address: '', district: null }];
                this.save_as_user = false;
                this.password = '';
            }
            this.search_query = '';

            if(this.type == 'edit' || this.type == 'add'){
                setTimeout(() => {
                    $('.select2_el').select2();
                }, 100);
            }
        }, 
        get_districts: function () {
            axios.get('/api/get/all/districts')
                .then(response => {
                    this.districts = response.data.data;
                })
                .catch(error => {
                    console.log(error);
                });
        },
        reset_to_walking_customer: function () {
            this.customer = {
                id: 1,
                name: 'walking customer',
                phone: '',
                image: null,
            };
        },
        format_number: function (number) {
            return Intl.NumberFormat('en-US').format(number);
        },
    },
    template: `
        <div>
            <div class="pos_customer_info">
                <div class="customer_avatar_wrapper">
                    <img v-if="customer && customer.image" :src="window.POS_DESKTOP_CONFIG.image_url+'/'+customer.image" alt="Customer" class="customer_avatar">
                </div>
                <div class="customer_info_wrapper">
                    <div class="customer_name">
                        {{ customer?.id ?? '' }} -
                        {{ customer?.name ?? '' }} 
                    </div>
                    <div class="customer_mobile" v-if="customer && customer.due_amount > 0">
                        <b>Due:</b> {{ format_number(customer?.due_amount ?? 0) }} <b>Bal:</b> {{ format_number(customer?.available_advance ?? 0) }}
                    </div>
                    <!-- 
                        <div class="customer_mobile" v-if="customer && customer.id != 1">
                            {{ customer?.phone ?? '' }}
                        </div>
                    -->
                    <div v-if="customer && customer.id">
                        <input v-model="customer.phone" placeholder="Enter phone" />
                    </div>
                </div>
                <div class="action_button">
                    <button type="button" class="btn btn-sm btn-light" @click="reset_to_walking_customer()">
                        <i class="fas fa-redo"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-light" @click="change_type('list');show_customer_modal=true;">
                        <i class="fas fa-search"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-light" @click="change_type('add');show_customer_modal=true;">
                        <i class="fas fa-user-plus"></i>
                    </button>
                    <button type="button" v-if="customer && customer.id != 1" class="btn btn-sm btn-light" @click="change_type('edit', customer);show_customer_modal=true;">
                        <i class="fas fa-user-edit"></i>
                    </button>
                </div>
            </div>
            <div class="customer_manage_modal" v-if="show_customer_modal">
                <div class="customer_manage_modal_content">
                    <div class="customer_manage_modal_content_header">
                        <input type="search" @input="search_customers" class="form-control" placeholder="Search Customer" v-model="search_query">
                        <button type="button" v-if="type == 'list'" @click="change_type('add')" class="btn btn-sm btn-primary">
                            <i class="fas fa-user-plus"></i> Add Customer
                        </button>
                        <button type="button" v-if="type == 'add' || type == 'edit'" @click="change_type('list')" class="btn btn-sm btn-primary">
                            <i class="fas fa-arrow-left"></i> Back
                        </button>
                        <button type="button" @click="show_customer_modal=false" class="btn btn-sm btn-danger">
                            <i class="fas fa-times"></i> Close
                        </button>
                    </div>
                    <div class="customer_manage_modal_content_body">
                        <div v-if="type == 'list'">
                            ${customer_list}
                        </div>
                        <div v-if="type == 'add' || type == 'edit'">
                            ${customer_form}
                        </div>
                        <div v-if="type == 'view'">
                            ${customer_view}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `
});

