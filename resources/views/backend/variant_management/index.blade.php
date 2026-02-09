@extends('backend.master')

@section('header_css')
    <link href="{{ url('assets') }}/plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />
    <style>
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .stats-card h3 {
            font-size: 32px;
            font-weight: bold;
            margin: 0;
        }
        .stats-card p {
            margin: 5px 0 0 0;
            opacity: 0.9;
        }
        .tab-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .nav-tabs {
            border-bottom: 2px solid #e0e0e0;
            padding: 0 20px;
            background: #f8f9fa;
        }
        .nav-tabs .nav-link {
            border: none;
            color: #666;
            font-weight: 600;
            padding: 15px 25px;
            margin-right: 10px;
        }
        .nav-tabs .nav-link.active {
            color: #5369f8;
            background: white;
            border-bottom: 3px solid #5369f8;
        }
        .content-section {
            display: flex;
            gap: 20px;
            padding: 25px;
        }
        .left-panel {
            flex: 1;
            max-height: 600px;
            overflow-y: auto;
        }
        .right-panel {
            flex: 0 0 400px;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
        }
        .group-item, .key-item {
            background: white;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            transition: all 0.3s;
        }
        .group-item:hover, .key-item:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .badge-custom {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 600;
        }
        .btn-action {
            padding: 5px 10px;
            font-size: 12px;
            margin-left: 5px;
        }
        .form-group label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }
        .products-modal .modal-dialog {
            max-width: 90%;
        }
        .product-table {
            font-size: 13px;
        }
        .product-table th {
            background: #f8f9fa;
            font-weight: 600;
        }
    </style>
@endsection

@section('page_title')
    Variant Management
@endsection

@section('page_heading')
    Variant Management
@endsection

@section('content')
<div id="variantApp">
    <!-- Stats Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="stats-card">
                <h3>{{ $totalGroups }}</h3>
                <p><i class="feather-layers"></i> Variant Groups</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <h3>{{ $totalKeys }}</h3>
                <p><i class="feather-key"></i> Variant Keys</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <h3>{{ $totalProducts }}</h3>
                <p><i class="feather-box"></i> Products with Variants</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <h3>৳ {{ number_format($totalValue, 2) }}</h3>
                <p><i class="feather-dollar-sign"></i> Total Variant Value</p>
            </div>
        </div>
    </div>

    <!-- Tabs Container -->
    <div class="tab-container">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link" @click.prevent="activeTab = 'groups'" :class="{active: activeTab === 'groups'}" href="javascript:void(0);">
                    <i class="feather-layers"></i> Manage Groups
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" @click.prevent="activeTab = 'keys'" :class="{active: activeTab === 'keys'}" href="javascript:void(0);">
                    <i class="feather-key"></i> Manage Keys
                </a>
            </li>
            <li class="nav-item ml-auto">
                <button class="btn btn-info btn-sm mt-2" @click="showVariantProducts">
                    <i class="feather-package"></i> Variant Products
                </button>
            </li>
        </ul>

        <!-- Groups Tab -->
        <div v-show="activeTab === 'groups'" class="content-section">
            <div class="left-panel">
                <h5 class="mb-3">
                    <i class="feather-list"></i> Existing Variant Groups
                    <span class="badge badge-primary ml-2">@{{ groups.length }}</span>
                </h5>
                
                <div v-if="loading" class="text-center py-5">
                    <div class="spinner-border text-primary"></div>
                    <p class="mt-2">Loading groups...</p>
                </div>

                <div v-else>
                    <div v-for="group in groups" :key="group.id" class="group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    <i class="feather-layers text-primary"></i> @{{ group.name }}
                                    <span v-if="group.is_fixed" class="badge badge-success badge-custom ml-2">Fixed</span>
                                    <span v-else class="badge badge-secondary badge-custom ml-2">Dynamic</span>
                                    <span v-if="group.is_stock_related" class="badge badge-info badge-custom">Stock Related</span>
                                </h6>
                                <p class="text-muted mb-2 small">@{{ group.description || 'No description' }}</p>
                                <div class="d-flex gap-2">
                                    <span class="badge badge-light">
                                        <i class="feather-key"></i> @{{ group.keys_count }} Keys
                                    </span>
                                    <span class="badge badge-light">
                                        <i class="feather-box"></i> @{{ group.products_count || 0 }} Products
                                    </span>
                                    <span class="badge badge-light">
                                        Order: @{{ group.sort_order }}
                                    </span>
                                </div>
                            </div>
                            <div>
                                <button v-if="!group.is_fixed" class="btn btn-sm btn-primary btn-action" @click="editGroup(group)">
                                    <i class="feather-edit"></i>
                                </button>
                                <button v-if="!group.is_fixed" class="btn btn-sm btn-danger btn-action" @click="deleteGroup(group.id)" :disabled="group.products_count > 0">
                                    <i class="feather-trash-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div v-if="groups.length === 0" class="text-center py-5">
                        <i class="feather-inbox" style="font-size: 48px; color: #ccc;"></i>
                        <p class="text-muted mt-2">No variant groups found</p>
                    </div>
                </div>
            </div>

            <div class="right-panel">
                <h5 class="mb-4">
                    <i class="feather-plus-circle"></i> @{{ groupForm.id ? 'Edit' : 'Add New' }} Group
                </h5>
                
                <form @submit.prevent="saveGroup">
                    <div class="form-group">
                        <label>Group Name <span class="text-danger">*</span></label>
                        <input type="text" v-model="groupForm.name" class="form-control" placeholder="e.g., Material, Weight" required>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea v-model="groupForm.description" class="form-control" rows="3" placeholder="Brief description of this variant group"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Sort Order</label>
                        <input type="number" v-model.number="groupForm.sort_order" class="form-control" placeholder="0">
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" v-model="groupForm.is_stock_related" class="form-check-input" id="is_stock_related">
                        <label class="form-check-label" for="is_stock_related">
                            Stock Related (Creates Stock Combinations)
                        </label>
                    </div>

                    <div v-if="groupForm.id" class="form-check mb-3">
                        <input type="checkbox" v-model="groupForm.status" class="form-check-input" id="group_status">
                        <label class="form-check-label" for="group_status">
                            Active
                        </label>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" :disabled="submitting">
                            <i class="feather-check"></i> @{{ groupForm.id ? 'Update' : 'Create' }} Group
                        </button>
                        <button v-if="groupForm.id" type="button" class="btn btn-secondary" @click="resetGroupForm">
                            <i class="feather-x"></i> Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Keys Tab -->
        <div v-show="activeTab === 'keys'" class="content-section">
            <div style="width: 100%;">
                <!-- Group Selection -->
                <div class="mb-4 p-3 bg-light rounded">
                    <label class="font-weight-bold mb-2">Select Variant Group <span class="text-danger">*</span></label>
                    <select v-model="selectedGroupForKeys" @change="loadKeys" class="form-control form-control-lg" id="groupSelect" style="font-size: 16px; height: 45px;">
                        <option value="">-- Select a Group --</option>
                        <option v-for="group in groups" :key="group.id" :value="group.id">
                            @{{ group.name }} (@{{ group.keys_count }} keys)
                        </option>
                    </select>
                </div>

                <div v-if="selectedGroupForKeys" class="d-flex gap-3">
                    <div class="left-panel">
                        <h5 class="mb-3">
                            <i class="feather-list"></i> Existing Keys
                            <span class="badge badge-primary ml-2">@{{ keys.length }}</span>
                        </h5>
                        
                        <div v-if="loadingKeys" class="text-center py-5">
                            <div class="spinner-border text-primary"></div>
                            <p class="mt-2">Loading keys...</p>
                        </div>

                        <div v-else>
                            <div v-for="key in keys" :key="key.id" class="key-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center gap-2">
                                            <img v-if="key.image" :src="'/' + key.image" style="width: 40px; height: 40px; border-radius: 5px; object-fit: cover;">
                                            <div>
                                                <h6 class="mb-0">@{{ key.key_name }}</h6>
                                                <small class="text-muted">Value: @{{ key.key_value || 'N/A' }}</small>
                                                <div class="mt-1">
                                                    <span class="badge badge-light">
                                                        <i class="feather-box"></i> @{{ key.products_count || 0 }} Products
                                                    </span>
                                                    <span class="badge badge-light ml-1">Order: @{{ key.sort_order }}</span>
                                                    <span v-if="!key.status" class="badge badge-warning ml-1">Inactive</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <button class="btn btn-sm btn-primary btn-action" @click="editKey(key)">
                                            <i class="feather-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger btn-action" @click="deleteKey(key.id)" :disabled="key.products_count > 0">
                                            <i class="feather-trash-2"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div v-if="keys.length === 0" class="text-center py-5">
                                <i class="feather-inbox" style="font-size: 48px; color: #ccc;"></i>
                                <p class="text-muted mt-2">No keys found for this group</p>
                            </div>
                        </div>
                    </div>

                    <div class="right-panel">
                        <h5 class="mb-4">
                            <i class="feather-plus-circle"></i> @{{ keyForm.id ? 'Edit' : 'Add New' }} Key
                        </h5>
                        
                        <form @submit.prevent="saveKey" enctype="multipart/form-data">
                            <div class="form-group">
                                <label>Key Name <span class="text-danger">*</span></label>
                                <input type="text" v-model="keyForm.key_name" class="form-control" placeholder="e.g., Red, Large, Cotton" required>
                            </div>

                            <div class="form-group">
                                <label>Key Value (Optional)</label>
                                <input type="text" v-model="keyForm.key_value" class="form-control" placeholder="e.g., #FF0000, XL, 100% Cotton">
                            </div>

                            <div class="form-group">
                                <label>Image (Optional)</label>
                                <input type="file" ref="keyImage" @change="handleImageUpload" class="form-control" accept="image/*">
                                <small class="text-muted">Max 2MB (jpeg, png, jpg, webp)</small>
                                <div v-if="keyForm.image && !keyForm.imageFile" class="mt-2">
                                    <img :src="'/' + keyForm.image" style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px;">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Sort Order</label>
                                <input type="number" v-model.number="keyForm.sort_order" class="form-control" placeholder="0">
                            </div>

                            <div v-if="keyForm.id" class="form-check mb-3">
                                <input type="checkbox" v-model="keyForm.status" class="form-check-input" id="key_status">
                                <label class="form-check-label" for="key_status">
                                    Active
                                </label>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary" :disabled="submitting">
                                    <i class="feather-check"></i> @{{ keyForm.id ? 'Update' : 'Create' }} Key
                                </button>
                                <button v-if="keyForm.id" type="button" class="btn btn-secondary" @click="resetKeyForm">
                                    <i class="feather-x"></i> Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div v-else class="text-center py-5">
                    <i class="feather-layers" style="font-size: 64px; color: #ccc;"></i>
                    <p class="text-muted mt-3">Please select a variant group to manage keys</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Variant Products Modal -->
    <div class="modal fade products-modal" id="productsModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="feather-package"></i> Products with Variants
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div v-if="loadingProducts" class="text-center py-5">
                        <div class="spinner-border text-primary"></div>
                    </div>
                    <div v-else>
                        <table class="table table-bordered product-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Variant Combination</th>
                                    <th>SKU</th>
                                    <th>Barcode</th>
                                    <th>Stock</th>
                                    <th>Price</th>
                                    <th>Total Value</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="product in variantProducts" :key="product.id">
                                    <td>
                                        <strong>@{{ product.product_name }}</strong>
                                        <br><small class="text-muted">@{{ product.product_sku }}</small>
                                    </td>
                                    <td>
                                        <div v-if="product.variant_text">@{{ product.variant_text }}</div>
                                        <small class="text-muted">Key: @{{ product.combination_key }}</small>
                                    </td>
                                    <td>@{{ product.sku || '-' }}</td>
                                    <td>@{{ product.barcode || '-' }}</td>
                                    <td>
                                        <span class="badge badge-info">@{{ product.stock }}</span>
                                    </td>
                                    <td>৳ @{{ parseFloat(product.effective_price || 0).toFixed(2) }}</td>
                                    <td>
                                        <strong>৳ @{{ parseFloat(product.total_value || 0).toFixed(2) }}</strong>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-danger" @click="detachVariant(product.id, product.product_name)">
                                            <i class="feather-x"></i> Detach
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div v-if="variantProducts.length === 0" class="text-center py-4">
                            <p class="text-muted">No products with variants found</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer_js')
<script src="{{ url('assets') }}/plugins/select2/select2.min.js"></script>
<script src="{{ asset('assets/js/vue.min.js') }}"></script>
<script>
new Vue({
    el: '#variantApp',
    data: {
        activeTab: 'groups',
        loading: false,
        loadingKeys: false,
        loadingProducts: false,
        submitting: false,
        groups: [],
        keys: [],
        variantProducts: [],
        selectedGroupForKeys: '',
        groupForm: {
            id: null,
            name: '',
            description: '',
            is_stock_related: true,
            sort_order: 0,
            status: true
        },
        keyForm: {
            id: null,
            group_id: '',
            key_name: '',
            key_value: '',
            image: '',
            imageFile: null,
            sort_order: 0,
            status: true
        }
    },
    mounted() {
        this.loadGroups();
    },
    watch: {
        selectedGroupForKeys(newVal) {
            console.log('selectedGroupForKeys changed to:', newVal);
        }
    },
    methods: {
        async loadGroups() {
            this.loading = true;
            try {
                const response = await axios.get('/variant-management/groups');
                this.groups = response.data;
            } catch (error) {
                this.showError(error.response?.data?.message || 'Failed to load groups');
            } finally {
                this.loading = false;
            }
        },
        
        async saveGroup() {
            this.submitting = true;
            try {
                const url = this.groupForm.id 
                    ? `/variant-management/groups/${this.groupForm.id}`
                    : '/variant-management/groups';
                const method = this.groupForm.id ? 'put' : 'post';
                
                const response = await axios[method](url, this.groupForm);
                
                this.showSuccess(response.data.message);
                this.resetGroupForm();
                this.loadGroups();
            } catch (error) {
                this.showError(error.response?.data?.message || 'Failed to save group');
            } finally {
                this.submitting = false;
            }
        },

        editGroup(group) {
            this.groupForm = { ...group };
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        resetGroupForm() {
            this.groupForm = {
                id: null,
                name: '',
                description: '',
                is_stock_related: true,
                sort_order: 0,
                status: true
            };
        },

        async deleteGroup(id) {
            if (!confirm('Are you sure you want to delete this group?')) return;
            
            try {
                const response = await axios.delete(`/variant-management/groups/${id}`);
                this.showSuccess(response.data.message);
                this.loadGroups();
            } catch (error) {
                this.showError(error.response?.data?.message || 'Failed to delete group');
            }
        },

        async loadKeys() {
            console.log('loadKeys called, selectedGroupForKeys:', this.selectedGroupForKeys);
            
            if (!this.selectedGroupForKeys) {
                this.keys = [];
                this.resetKeyForm();
                return;
            }

            this.loadingKeys = true;
            try {
                const response = await axios.get(`/variant-management/keys/${this.selectedGroupForKeys}`);
                this.keys = response.data;
                this.keyForm.group_id = this.selectedGroupForKeys;
                console.log('Loaded keys:', this.keys.length);
            } catch (error) {
                console.error('Error loading keys:', error);
                this.showError(error.response?.data?.message || 'Failed to load keys');
                this.keys = [];
            } finally {
                this.loadingKeys = false;
            }
        },

        handleImageUpload(event) {
            this.keyForm.imageFile = event.target.files[0];
        },

        async saveKey() {
            this.submitting = true;
            try {
                const formData = new FormData();
                formData.append('group_id', this.keyForm.group_id);
                formData.append('key_name', this.keyForm.key_name);
                formData.append('key_value', this.keyForm.key_value || '');
                formData.append('sort_order', this.keyForm.sort_order);
                
                if (this.keyForm.id) {
                    formData.append('status', this.keyForm.status ? 1 : 0);
                }
                
                if (this.keyForm.imageFile) {
                    formData.append('image', this.keyForm.imageFile);
                }

                const url = this.keyForm.id 
                    ? `/variant-management/keys/${this.keyForm.id}`
                    : '/variant-management/keys';
                
                const response = await axios.post(url, formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                });
                
                this.showSuccess(response.data.message);
                this.resetKeyForm();
                this.loadKeys();
            } catch (error) {
                this.showError(error.response?.data?.message || 'Failed to save key');
            } finally {
                this.submitting = false;
            }
        },

        editKey(key) {
            this.keyForm = { ...key, imageFile: null };
            window.scrollTo({ top: 200, behavior: 'smooth' });
        },

        resetKeyForm() {
            this.keyForm = {
                id: null,
                group_id: this.selectedGroupForKeys,
                key_name: '',
                key_value: '',
                image: '',
                imageFile: null,
                sort_order: 0,
                status: true
            };
            if (this.$refs.keyImage) {
                this.$refs.keyImage.value = '';
            }
        },

        async deleteKey(id) {
            if (!confirm('Are you sure you want to delete this key?')) return;
            
            try {
                const response = await axios.delete(`/variant-management/keys/${id}`);
                this.showSuccess(response.data.message);
                this.loadKeys();
                this.loadGroups(); // Refresh counts
            } catch (error) {
                this.showError(error.response?.data?.message || 'Failed to delete key');
            }
        },

        async showVariantProducts() {
            this.loadingProducts = true;
            $('#productsModal').modal('show');
            
            try {
                const response = await axios.get('/variant-management/products');
                this.variantProducts = response.data;
            } catch (error) {
                this.showError(error.response?.data?.message || 'Failed to load products');
            } finally {
                this.loadingProducts = false;
            }
        },

        async detachVariant(variantId, productName) {
            if (!confirm(`Are you sure you want to detach this variant from "${productName}"?\n\nThis will:\n- Remove the variant combination\n- Delete variant stock entries\n- Recalculate product total stock`)) return;
            
            try {
                const response = await axios.delete(`/variant-management/variants/${variantId}/detach`);
                this.showSuccess(response.data.message);
                this.showVariantProducts(); // Reload list
                this.loadGroups(); // Refresh counts
            } catch (error) {
                this.showError(error.response?.data?.message || 'Failed to detach variant');
            }
        },

        showSuccess(message) {
            toastr.success(message);
        },

        showError(message) {
            toastr.error(message);
        }
    }
});
</script>
@endsection

