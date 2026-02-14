@extends('backend.master')

@section('header_css')
    <style>
        .courier-tabs {
            border-bottom: 2px solid #e9ecef;
            padding: 0;
            margin-bottom: 1.5rem;
        }
        .courier-tabs .tab-btn {
            display: inline-block;
            padding: 12px 24px;
            margin-right: 4px;
            border: none;
            background: #f8f9fa;
            color: #495057;
            font-weight: 600;
            cursor: pointer;
            border-radius: 8px 8px 0 0;
            transition: all 0.2s;
        }
        .courier-tabs .tab-btn:hover {
            background: #e9ecef;
        }
        .courier-tabs .tab-btn.active {
            background: #fff;
            color: #5369f8;
            border-bottom: 2px solid #5369f8;
            margin-bottom: -2px;
        }
        .courier-panel {
            display: none;
            padding: 1.5rem;
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 0 8px 8px 8px;
        }
        .courier-panel.active {
            display: block;
        }
        .config-key {
            text-transform: lowercase;
            font-weight: 600;
            color: #495057;
        }
    </style>
@endsection

@section('page_title')
    Courier Management
@endsection

@section('page_heading')
    Courier Management
@endsection

@section('content')
<div id="courierApp">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <p class="text-muted mb-4">Manage courier methods (Pathao, Steadfast, etc.). Update config and set status to active or inactive.</p>

                    <div v-if="loading" class="text-center py-5">
                        <span class="spinner-border text-primary"></span>
                        <p class="mt-2">Loading...</p>
                    </div>

                    <template v-else>
                        <div class="courier-tabs">
                            <button
                                v-for="method in methods"
                                :key="method.id"
                                type="button"
                                class="tab-btn"
                                :class="{ active: activeTabId === method.id }"
                                @click="activeTabId = method.id"
                            >
                                @{{ method.title }}
                            </button>
                        </div>

                        <div
                            v-for="method in methods"
                            :key="'panel-' + method.id"
                            class="courier-panel"
                            :class="{ active: activeTabId === method.id }"
                        >
                            <form @submit.prevent="save(method)">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Status</label>
                                            <select v-model="method.status" class="form-control" style="max-width: 200px;">
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="font-weight-bold d-block mb-2">Config</label>
                                        <template v-for="(value, key) in getEditedConfig(method)">
                                            <div class="form-group" :key="key">
                                                <label :for="'config-' + method.id + '-' + key" class="config-key">@{{ key }}</label>
                                                <input
                                                    :id="'config-' + method.id + '-' + key"
                                                    v-model="getEditedConfig(method)[key]"
                                                    :type="isSecretKey(key) ? 'password' : 'text'"
                                                    class="form-control"
                                                    :placeholder="key"
                                                />
                                            </div>
                                        </template>
                                        <p v-if="Object.keys(getEditedConfig(method)).length === 0" class="text-muted">No config keys.</p>
                                    </div>
                                    <div class="col-12 mt-3">
                                        <button type="submit" class="btn btn-primary" :disabled="savingId === method.id">
                                            <span v-if="savingId === method.id" class="spinner-border spinner-border-sm mr-1"></span>
                                            Save
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer_js')
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
            }

            new Vue({
                el: '#courierApp',
                data: {
                    methods: [],
                    editedConfig: {},
                    activeTabId: null,
                    loading: true,
                    savingId: null
                },
                mounted() {
                    this.fetchMethods();
                },
                methods: {
                    fetchMethods() {
                        this.loading = true;
                        axios.get('{{ route("courier-management.methods") }}')
                            .then(res => {
                                this.methods = res.data;
                                this.methods.forEach(m => {
                                    this.$set(this.editedConfig, m.id, m.config && typeof m.config === 'object' ? { ...m.config } : {});
                                });
                                if (this.methods.length && this.activeTabId === null) {
                                    this.activeTabId = this.methods[0].id;
                                }
                            })
                            .catch(err => {
                                if (typeof toastr !== 'undefined') {
                                    toastr.error(err.response?.data?.message || 'Failed to load courier methods.');
                                } else {
                                    alert('Failed to load courier methods.');
                                }
                            })
                            .finally(() => { this.loading = false; });
                    },
                    getEditedConfig(method) {
                        if (!this.editedConfig[method.id]) {
                            this.$set(this.editedConfig, method.id, method.config && typeof method.config === 'object' ? { ...method.config } : {});
                        }
                        return this.editedConfig[method.id];
                    },
                    isSecretKey(key) {
                        const k = (key || '').toLowerCase();
                        return k.includes('secret') || k.includes('password');
                    },
                    save(method) {
                        this.savingId = method.id;
                        const config = this.getEditedConfig(method);
                        axios.put('{{ url("/courier-management/methods") }}/' + method.id, {
                            config: config,
                            status: method.status
                        })
                            .then(() => {
                                if (typeof toastr !== 'undefined') {
                                    toastr.success('Saved.');
                                } else {
                                    alert('Saved.');
                                }
                            })
                            .catch(err => {
                                const msg = err.response?.data?.message || (err.response?.data?.errors ? JSON.stringify(err.response.data.errors) : 'Failed to save.');
                                if (typeof toastr !== 'undefined') {
                                    toastr.error(msg);
                                } else {
                                    alert(msg);
                                }
                            })
                            .finally(() => { this.savingId = null; });
                    }
                }
            });
        });
    </script>
@endsection
