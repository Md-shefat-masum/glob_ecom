@extends('backend.master')

@section('header_css')
    <style>
        .order_management_wrapper {
            --om-brand: #0d9488;
            --om-brand-light: #ccfbf1;
        }
    </style>
@endsection

@section('page_title')
    Order Management
@endsection
@section('page_heading')
    All Orders
@endsection

@section('content')
    <div id="order_management_wrapper" class="order_management_wrapper">
        {{-- Top analytics cards --}}
        <div class="row g-2 mb-3" v-if="analytics">
            <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                <div class="card border-0 shadow-sm om-stat-card" :class="{ 'om-stat-active': activeFilter === 'all' }"
                    @click="setFilter('all')">
                    <div class="card-body p-3 text-center d-flex align-items-center" style="gap: 10px;">
                        <i class="fas fa-shopping-cart fa-lg sales_analytics_icon"></i>
                        <div>
                            <div class="stat-label text-muted text-left">All (@{{ analytics.all ? analytics.all.count : 0 }})</div>
                            <div class="stat-total text-success text-left">৳@{{ formatNumber(analytics.all ? analytics.all.total_value : 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                <div class="card border-0 shadow-sm om-stat-card" :class="{ 'om-stat-active': activeFilter === 'pending' }"
                    @click="setFilter('pending')">
                    <div class="card-body p-3 text-center d-flex align-items-center" style="gap: 10px;">
                        <i class="fas fa-clock fa-lg text-warning sales_analytics_icon"></i>
                        <div>
                            <div class="stat-label text-muted text-left">Pending (@{{ analytics.pending ? analytics.pending.count : 0 }})</div>
                            <div class="stat-total text-warning text-left">৳@{{ formatNumber(analytics.pending ? analytics.pending.total_value : 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                <div class="card border-0 shadow-sm om-stat-card" :class="{ 'om-stat-active': activeFilter === 'invoiced' }"
                    @click="setFilter('invoiced')">
                    <div class="card-body p-3 text-center d-flex align-items-center" style="gap: 10px;">
                        <i class="fas fa-file-invoice fa-lg text-info sales_analytics_icon"></i>
                        <div>
                            <div class="stat-label text-muted text-left">Invoiced (@{{ analytics.invoiced ? analytics.invoiced.count : 0 }})</div>
                            <div class="stat-total text-info text-left">৳@{{ formatNumber(analytics.invoiced ? analytics.invoiced.total_value : 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                <div class="card border-0 shadow-sm om-stat-card"
                    :class="{ 'om-stat-active': activeFilter === 'delivered' }" @click="setFilter('delivered')">
                    <div class="card-body p-3 text-center d-flex align-items-center" style="gap: 10px;">
                        <i class="fas fa-truck fa-lg text-success sales_analytics_icon"></i>
                        <div>
                            <div class="stat-label text-muted text-left">Delivered (@{{ analytics.delivered ? analytics.delivered.count : 0 }})</div>
                            <div class="stat-total text-success text-left">৳@{{ formatNumber(analytics.delivered ? analytics.delivered.total_value : 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                <div class="card border-0 shadow-sm om-stat-card" :class="{ 'om-stat-active': activeFilter === 'canceled' }"
                    @click="setFilter('canceled')">
                    <div class="card-body p-3 text-center d-flex align-items-center" style="gap: 10px;">
                        <i class="fas fa-times-circle fa-lg text-secondary sales_analytics_icon"></i>
                        <div>
                            <div class="stat-label text-muted text-left">Canceled (@{{ analytics.canceled ? analytics.canceled.count : 0 }})</div>
                            <div class="stat-total text-secondary text-left">৳@{{ formatNumber(analytics.canceled ? analytics.canceled.total_value : 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                <div class="card border-0 shadow-sm om-stat-card" :class="{ 'om-stat-active': activeFilter === 'returned' }"
                    @click="setFilter('returned')">
                    <div class="card-body p-3 text-center d-flex align-items-center" style="gap: 10px;">
                        <i class="fas fa-undo fa-lg text-orange sales_analytics_icon"></i>
                        <div>
                            <div class="stat-label text-muted text-left">Returned (@{{ analytics.returned ? analytics.returned.count : 0 }})</div>
                            <div class="stat-total text-orange text-left">৳@{{ formatNumber(analytics.returned ? analytics.returned.total_value : 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                <div class="card border-0 shadow-sm om-stat-card">
                    <div class="card-body p-3 text-center d-flex align-items-center" style="gap: 10px;">
                        <i class="fas fa-shipping-fast fa-lg text-primary sales_analytics_icon"></i>
                        <div>
                            <div class="stat-label text-muted text-left">Couriered (@{{ analytics.couriered ? analytics.couriered.count : 0 }})</div>
                            <div class="stat-total text-primary text-left">৳@{{ formatNumber(analytics.couriered ? analytics.couriered.total_value : 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                <div class="card border-0 shadow-sm om-stat-card"
                    :class="{ 'om-stat-active': paidStatusFilter === 'paid' }" @click="setPaidFilter('paid')">
                    <div class="card-body p-3 text-center d-flex align-items-center" style="gap: 10px;">
                        <i class="fas fa-check-circle fa-lg text-success sales_analytics_icon"></i>
                        <div>
                            <div class="stat-label text-muted text-left">Paid (@{{ analytics.paid ? analytics.paid.count : 0 }})</div>
                            <div class="stat-total text-success text-left">৳@{{ formatNumber(analytics.paid ? analytics.paid.total_value : 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                <div class="card border-0 shadow-sm om-stat-card"
                    :class="{ 'om-stat-active': paidStatusFilter === 'due' }" @click="setPaidFilter('due')">
                    <div class="card-body p-3 text-center d-flex align-items-center" style="gap: 10px;">
                        <i class="fas fa-money-bill-wave fa-lg text-danger sales_analytics_icon"></i>
                        <div>
                            <div class="stat-label text-muted text-left">Due (@{{ analytics.due ? analytics.due.count : 0 }})</div>
                            <div class="stat-total text-danger text-left">৳@{{ formatNumber(analytics.due ? analytics.due.total_value : 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Order filter: search always visible + advanced collapse --}}
        <div class="card border-0 shadow-sm bg-white mb-3">
            <div class="card-body py-3 px-0">
                <div class="d-flex align-items-center gap-2 justify-content-between">
                    <div class="col-12 col-md-6 col-lg-4">
                        <input type="text" class="form-control form-control-sm"
                            placeholder="Customer, phone, ID, order code, creator..." v-model="filters.search"
                            @input="debouncedFetch">
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-toggle="collapse"
                            data-target="#omAdvancedFilter" aria-expanded="false">
                            <i class="fas fa-filter"></i> filter
                        </button>
                    </div>
                </div>
                <div class="collapse p-2" id="omAdvancedFilter">
                    <hr class="my-2">
                    <div class="row g-2">
                        <div class="col-6 col-md-4 col-lg-3 pb-2">
                            <label class="form-label small mb-0 block">Order status</label>
                            <select class="form-control form-select-sm" v-model="filters.order_status"
                                @change="fetchData">
                                <option value="">All</option>
                                <option value="pending">Pending</option>
                                <option value="invoiced">Invoiced</option>
                                <option value="delivered">Delivered</option>
                                <option value="canceled">Canceled</option>
                                <option value="returned">Returned</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-4 col-lg-3 pb-2">
                            <label class="form-label small mb-0 block">Discount type</label>
                            <select class="form-control form-select-sm" v-model="filters.discount_type"
                                @change="fetchData">
                                <option value="">All</option>
                                <option value="percent">Percent</option>
                                <option value="fixed">Fixed</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-4 col-lg-3 pb-2">
                            <label class="form-label small mb-0 block">Payment type</label>
                            <select class="form-control form-select-sm" v-model="filters.payment_type"
                                @change="fetchData">
                                <option value="">All</option>
                                @foreach ($paymentTypes ?? [] as $pt)
                                    <option value="{{ $pt->id }}">{{ $pt->payment_type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-md-4 col-lg-3 pb-2">
                            <label class="form-label small mb-0 block">Customer source</label>
                            <select class="form-control form-select-sm" v-model="filters.customer_source_type"
                                @change="fetchData">
                                <option value="">All</option>
                                @foreach ($customerSources ?? [] as $cs)
                                    <option value="{{ $cs->id }}">{{ $cs->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-md-4 col-lg-3 pb-2">
                            <label class="form-label small mb-0 block">Warehouse</label>
                            <select class="form-control form-select-sm" v-model="filters.warehouse_id"
                                @change="fetchData">
                                <option value="">All</option>
                                @foreach ($warehouses ?? [] as $wh)
                                    <option value="{{ $wh->id }}">{{ $wh->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-md-4 col-lg-3 pb-2">
                            <label class="form-label small mb-0 block">Warehouse status</label>
                            <select class="form-control form-select-sm" v-model="filters.warehouse_status"
                                @change="fetchData">
                                <option value="">All</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-4 col-lg-3 pb-2">
                            <label class="form-label small mb-0 block">Sales date from</label>
                            <input type="date" class="form-control form-control-sm" v-model="filters.sales_date_from"
                                @change="fetchData">
                        </div>
                        <div class="col-6 col-md-4 col-lg-3 pb-2">
                            <label class="form-label small mb-0 block">Sales date to</label>
                            <input type="date" class="form-control form-control-sm" v-model="filters.sales_date_to"
                                @change="fetchData">
                        </div>
                        <div class="col-6 col-md-4 col-lg-3 pb-2">
                            <label class="form-label small mb-0 block">Due date from</label>
                            <input type="date" class="form-control form-control-sm" v-model="filters.due_date_from"
                                @change="fetchData">
                        </div>
                        <div class="col-6 col-md-4 col-lg-3 pb-2">
                            <label class="form-label small mb-0 block">Due date to</label>
                            <input type="date" class="form-control form-control-sm" v-model="filters.due_date_to"
                                @change="fetchData">
                        </div>
                        <div class="col-6 col-md-4 col-lg-3 pb-2">
                            <label class="form-label small mb-0 block">Paid status</label>
                            <select class="form-control form-select-sm" v-model="filters.paid_status"
                                @change="fetchData">
                                <option value="">All</option>
                                <option value="paid">Paid</option>
                                <option value="due">Due</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick actions (selected items) --}}
        <div class="card border-0 shadow-sm bg-white mb-3 order_quick_actions_wrapper">
            <div class="card-body py-2 px-0">
                <div class="button_container">
                    <span class="small text-muted">@{{ selectedIds.length }} orders selected</span>
                    <select class="form-select form-select-sm" style="width: auto;" v-model="quickActionStatus"
                        @change="quickChangeStatus">
                        <option value="">Change status</option>
                        <option value="pending">Pending</option>
                        <option value="invoiced">Invoiced</option>
                        <option value="delivered">Delivered</option>
                        <option value="canceled">Canceled</option>
                    </select>
                    <select class="form-select form-select-sm" style="width: auto;" v-model="quickCourier"
                        @change="quickAddCourier">
                        <option value="">Add to courier</option>
                        <option value="pathao">Pathao</option>
                        <option value="steadfast">Steadfast</option>
                    </select>
                    <button type="button" class="button" @click="quickPrint">
                        <i class="fas fa-print"></i>
                        Print
                    </button>
                    <button type="button" class="button" @click="quickEmail">
                        <i class="fas fa-envelope"></i>
                        Email</button>
                    <button type="button" class="button" @click="quickSms">
                        <i class="fas fa-sms"></i> SMS
                    </button>
                    <a href="{{ url('/add/new/product-order/manage') }}" class="button">
                        <i class="fas fa-plus"></i>
                        Add Order
                    </a>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="card border-0 shadow-sm bg-white">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 40px;">
                                    <input type="checkbox" v-model="selectAll" @change="toggleSelectAll"
                                        title="Select all">
                                </th>
                                <th class="text-center">ID</th>
                                <th>Code</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Warehouse</th>
                                <th class="text-end">Total</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="loading">
                                <td colspan="9" class="text-center py-4 text-muted"><i
                                        class="fas fa-spinner fa-spin"></i>
                                    Loading...</td>
                            </tr>
                            <tr v-else-if="!orders.length">
                                <td colspan="9" class="text-center py-4 text-muted">No orders found.</td>
                            </tr>
                            <tr v-else v-for="row in orders" :key="row.id">
                                <td class="text-center">
                                    <input type="checkbox" :value="row.id" v-model="selectedIds">
                                </td>
                                <td class="text-center">@{{ row.id }}</td>
                                <td>
                                    <span class="text-primary">@{{ row.order_code }}</span>
                                    <a href="#" class="ms-1 text-muted" @click.prevent="copyCode(row.order_code)"
                                        title="Copy code"><i class="fas fa-copy"></i></a>
                                </td>
                                <td>
                                    <div>@{{ row.sale_date }}</div>
                                    <small class="text-muted" v-if="row.due_date">Due: @{{ row.due_date }}</small>
                                </td>
                                <td>
                                    <div>@{{ row.customer_name }}</div>
                                    <small class="text-muted">@{{ row.customer_phone }}</small>
                                    <div class="small" v-if="row.order_source">@{{ row.order_source }}</div>
                                    <div class="mt-1">
                                        <a v-if="row.customer_phone" :href="'tel:' + row.customer_phone"
                                            class="btn btn-xs btn-outline-secondary me-1"><i class="fas fa-phone"></i></a>
                                        <a v-if="row.customer_email" :href="'mailto:' + row.customer_email"
                                            class="btn btn-xs btn-outline-secondary"><i class="fas fa-envelope"></i></a>
                                    </div>
                                </td>
                                <td>@{{ row.warehouse_name || '—' }}</td>
                                <td class="text-end">
                                    <div v-if="row.shipping_charge > 0" class="small text-muted">Ship:
                                        ৳@{{ formatNumber(row.shipping_charge) }}</div>
                                    <div><strong>৳@{{ formatNumber(row.grand_total) }}</strong></div>
                                    <div class="small">Paid: ৳@{{ formatNumber(row.paid_amount) }} <span class="text-danger"
                                            v-if="row.due_amount > 0">Due: ৳@{{ formatNumber(row.due_amount) }}</span></div>
                                </td>
                                <td class="text-center">
                                    <span class="badge"
                                        :class="statusBadgeClass(row.order_status)">@{{ row.order_status }}</span>
                                    <span v-if="row.is_couriered" class="badge bg-primary ms-1">Couriered</span>
                                </td>
                                <td class="text-center">
                                    <a v-if="row.order_status === 'pending'" :href="editUrl(row.slug)"
                                        class="btn btn-sm btn-outline-primary" title="Edit"><i
                                            class="fas fa-edit"></i></a>
                                    <a :href="invoiceUrl(row.slug)" target="_blank" class="btn btn-sm btn-outline-info"
                                        title="Invoice"><i class="fas fa-eye"></i></a>
                                    <a :href="printUrl(row.slug)" target="_blank"
                                        class="btn btn-sm btn-outline-secondary" title="Print"><i
                                            class="fas fa-print"></i></a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Delete"
                                        v-if="row.order_status === 'pending'" @click="confirmDelete(row)"><i
                                            class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 p-3 border-top">
                    <div class="d-flex align-items-center gap-2">
                        <span class="small text-muted">Page @{{ pagination.current_page }} of @{{ pagination.last_page }} (total
                            @{{ pagination.total }})</span>
                        <select class="form-select form-select-sm" style="width: auto;" v-model="perPage"
                            @change="fetchData">
                            <option :value="10">10</option>
                            <option :value="50">50</option>
                            <option :value="100">100</option>
                            <option :value="200">200</option>
                        </select>
                        <span class="small text-muted">per page</span>
                    </div>
                    <div class="d-flex gap-1">
                        <button type="button" class="btn btn-sm btn-outline-secondary"
                            :disabled="pagination.current_page <= 1" @click="goPage(pagination.current_page - 1)"><i
                                class="fas fa-chevron-left"></i></button>
                        <button type="button" class="btn btn-sm btn-outline-secondary"
                            :disabled="pagination.current_page >= pagination.last_page"
                            @click="goPage(pagination.current_page + 1)"><i class="fas fa-chevron-right"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_js')
    <script src="https://cdn.jsdelivr.net/npm/vue@2.7.16/dist/vue.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var orderListUrl = @json(route('OrderListPage'));
            var editBase = @json(url('edit/product-order/manage'));
            var invoiceBase = @json(url('order-invoice'));
            var printBase = @json(url('order-invoice'));
            var deleteBase = @json(url('delete/product-order/manage'));

            new Vue({
                el: '#order_management_wrapper',
                data: {
                    orders: [],
                    analytics: null,
                    pagination: {
                        current_page: 1,
                        last_page: 1,
                        per_page: 10,
                        total: 0,
                        from: 0,
                        to: 0
                    },
                    loading: false,
                    filters: {
                        search: '',
                        order_status: '',
                        discount_type: '',
                        payment_type: '',
                        customer_source_type: '',
                        warehouse_id: '',
                        warehouse_status: '',
                        sales_date_from: '',
                        sales_date_to: '',
                        due_date_from: '',
                        due_date_to: '',
                        paid_status: ''
                    },
                    order_source: @json(request('order_source', 'pos')),
                    activeFilter: '',
                    paidStatusFilter: '',
                    perPage: 10,
                    selectedIds: [],
                    selectAll: false,
                    quickActionStatus: '',
                    quickCourier: '',
                    debounceTimer: null
                },
                mounted: function() {
                    this.fetchData();
                },
                watch: {
                    order_source: function(v) {
                        this.fetchData();
                    }
                },
                methods: {
                    buildParams: function(page) {
                        var p = {
                            page: page || 1,
                            per_page: this.perPage
                        };
                        if (this.order_source) p.order_source = this.order_source;
                        if (this.filters.search) p.search = this.filters.search;
                        if (this.filters.order_status) p.order_status = this.filters.order_status;
                        if (this.filters.discount_type) p.discount_type = this.filters.discount_type;
                        if (this.filters.payment_type) p.payment_type = this.filters.payment_type;
                        if (this.filters.customer_source_type) p.customer_source_type = this.filters
                            .customer_source_type;
                        if (this.filters.warehouse_id) p.warehouse_id = this.filters.warehouse_id;
                        if (this.filters.warehouse_status) p.warehouse_status = this.filters
                            .warehouse_status;
                        if (this.filters.sales_date_from) p.sales_date_from = this.filters
                            .sales_date_from;
                        if (this.filters.sales_date_to) p.sales_date_to = this.filters.sales_date_to;
                        if (this.filters.due_date_from) p.due_date_from = this.filters.due_date_from;
                        if (this.filters.due_date_to) p.due_date_to = this.filters.due_date_to;
                        if (this.filters.paid_status) p.paid_status = this.filters.paid_status;
                        return p;
                    },
                    fetchData: function(page) {
                        var vm = this;
                        vm.loading = true;
                        var params = vm.buildParams(page || vm.pagination.current_page);
                        var qs = new URLSearchParams(params).toString();
                        fetch(orderListUrl + '?' + qs, {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        }).then(function(r) {
                            return r.json();
                        }).then(function(res) {
                            vm.analytics = res.analytics || null;
                            vm.orders = res.data || [];
                            vm.pagination = res.pagination || vm.pagination;
                            vm.loading = false;
                        }).catch(function() {
                            vm.loading = false;
                        });
                    },
                    debouncedFetch: function() {
                        var vm = this;
                        if (vm.debounceTimer) clearTimeout(vm.debounceTimer);
                        vm.debounceTimer = setTimeout(function() {
                            vm.fetchData(1);
                        }, 300);
                    },
                    setFilter: function(status) {
                        this.activeFilter = this.activeFilter === status ? '' : status;
                        this.filters.order_status = this.activeFilter;
                        this.paidStatusFilter = '';
                        this.fetchData(1);
                    },
                    setPaidFilter: function(paid) {
                        this.paidStatusFilter = this.paidStatusFilter === paid ? '' : paid;
                        this.filters.paid_status = this.paidStatusFilter;
                        this.fetchData(1);
                    },
                    formatNumber: function(n) {
                        return Number(n).toLocaleString('en-BD', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                    },
                    toggleSelectAll: function() {
                        if (this.selectAll) this.selectedIds = this.orders.map(function(o) {
                            return o.id;
                        });
                        else this.selectedIds = [];
                    },
                    copyCode: function(code) {
                        if (navigator.clipboard) navigator.clipboard.writeText(code).then(function() {
                            toastr.success('Copied');
                        });
                    },
                    statusBadgeClass: function(s) {
                        var m = {
                            pending: 'bg-warning',
                            invoiced: 'bg-info',
                            delivered: 'bg-success',
                            canceled: 'bg-secondary',
                            returned: 'bg-danger'
                        };
                        return m[s] || 'bg-secondary';
                    },
                    editUrl: function(slug) {
                        return editBase + '/' + slug;
                    },
                    invoiceUrl: function(slug) {
                        return invoiceBase + '/' + slug;
                    },
                    printUrl: function(slug) {
                        return printBase + '/' + slug + '/pdf';
                    },
                    goPage: function(p) {
                        this.fetchData(p);
                    },
                    quickChangeStatus: function() {
                        if (!this.quickActionStatus) return;
                        // TODO: implement bulk status change API
                        toastr.info('Bulk status change: ' + this.quickActionStatus);
                        this.quickActionStatus = '';
                    },
                    quickAddCourier: function() {
                        if (!this.quickCourier) return;
                        // TODO: implement bulk add to courier
                        toastr.info('Add to courier: ' + this.quickCourier);
                        this.quickCourier = '';
                    },
                    quickPrint: function() {
                        if (!this.selectedIds.length) return;
                        this.selectedIds.forEach(function(id) {
                            // open print for first; TODO: batch print
                        });
                        toastr.info('Print selected');
                    },
                    quickEmail: function() {
                        toastr.info('Email selected');
                    },
                    quickSms: function() {
                        toastr.info('SMS selected');
                    },
                    confirmDelete: function(row) {
                        if (!confirm('Delete this order?')) return;
                        var vm = this;
                        window.location.href = deleteBase + '/' + row.slug;
                    }
                }
            });
        });
    </script>
@endsection
