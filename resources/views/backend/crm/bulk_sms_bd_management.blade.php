@extends('backend.master')

@section('header_css')
    <link href="{{ url('assets') }}/plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />
    <style>
        .select2-selection {
            height: 38px !important;
            border: 1px solid #ced4da !important;
            overflow: hidden !important;
        }
        .select2 {
            width: 100% !important;
        }
        /* Select2 container overflow fix */
        .select2-container--default .select2-selection--multiple {
            max-height: 200px !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            margin-top: 5px !important;
            margin-bottom: 5px !important;
            margin-right: 5px !important;
            max-width: 100% !important;
            word-break: break-word !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__rendered {
            padding: 0 5px !important;
            max-height: 190px !important;
            overflow-y: auto !important;
        }
        /* Select2 dropdown overflow fix */
        .select2-container--default .select2-results {
            max-height: 300px !important;
            overflow-y: auto !important;
        }
        .select2-container--default .select2-results__options {
            max-height: 300px !important;
            overflow-y: auto !important;
        }
        .sms-tab-nav {
            display: flex;
            gap: 10px;
            border-bottom: 2px solid #e9ecef;
            margin-bottom: 20px;
        }
        .sms-tab-item {
            padding: 12px 24px;
            cursor: pointer;
            border: none;
            background: transparent;
            color: #6c757d;
            font-weight: 500;
            transition: all 0.3s;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
        }
        .sms-tab-item:hover {
            color: #495057;
            background: #f8f9fa;
        }
        .sms-tab-item.active {
            color: #4f46e5;
            border-bottom-color: #4f46e5;
            background: #f8f9fa;
        }
        .sms-tab-content {
            display: none;
        }
        .sms-tab-content.active {
            display: block;
        }
        .character-count {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
            padding: 8px;
            background: #f8f9fa;
            border-radius: 4px;
            border-left: 3px solid #6c757d;
        }
        .character-count.warning {
            color: #ff9800;
            border-left-color: #ff9800;
            background: #fff3e0;
        }
        .character-count.danger {
            color: #dc3545;
            border-left-color: #dc3545;
            background: #ffebee;
        }
        .character-count div {
            margin-bottom: 4px;
        }
        .character-count div:last-child {
            margin-bottom: 0;
        }
        .character-count .badge {
            font-size: 10px;
            padding: 2px 6px;
            margin-left: 5px;
        }
        .message-row {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
            align-items: flex-start;
        }
        .message-row input,
        .message-row textarea {
            flex: 1;
        }
        .remove-row-btn {
            margin-top: 5px;
        }
        .balance-info {
            background: #e7f3ff;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .balance-info .balance-value {
            font-size: 24px;
            font-weight: bold;
            color: #4f46e5;
        }
        .provider-badge {
            display: inline-block;
            padding: 5px 15px;
            background: #4f46e5;
            color: white;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
    </style>
@endsection

@section('page_title')
    Bulk SMS BD Management
@endsection

@section('page_heading')
    Bulk SMS BD Management
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        
        <!-- Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">
                        Bulk SMS BD Management
                        <span class="provider-badge ms-2">Bulk SMS BD</span>
                    </h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ url('/crm-home') }}">CRM</a></li>
                            <li class="breadcrumb-item active">Bulk SMS BD</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vue App -->
        <div id="bulkSmsBdApp">
            
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            
                            <!-- Balance Display -->
                            <div class="row mb-4">
                                <div class="col-md-6 offset-md-6">
                                    <div class="balance-info">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <small class="text-muted d-block">Account Balance</small>
                                                <div class="balance-value" v-if="balance !== null && balance !== 'Error' && balance !== 'N/A'">
                                                    @{{ formatBalance(balance) }} SMS
                                                </div>
                                                <div v-else-if="balance === 'Error' || balance === 'N/A'" class="text-danger">
                                                    @{{ balance }}
                                                </div>
                                                <div v-else class="text-muted">Loading...</div>
                                            </div>
                                            <button @click="loadBalance" class="btn btn-sm btn-outline-primary" :disabled="loadingBalance">
                                                <i class="feather-refresh-cw" :class="{ 'spin': loadingBalance }"></i> Refresh
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab Navigation -->
                            <div class="sms-tab-nav">
                                <button 
                                    class="sms-tab-item"
                                    :class="{ active: activeTab === 'single' }"
                                    @click="switchTab('single')">
                                    <i class="feather-user"></i> Single SMS
                                </button>
                                <button 
                                    class="sms-tab-item"
                                    :class="{ active: activeTab === 'one-to-many' }"
                                    @click="switchTab('one-to-many')">
                                    <i class="feather-users"></i> One to Many
                                </button>
                                <button 
                                    class="sms-tab-item"
                                    :class="{ active: activeTab === 'many-to-many' }"
                                    @click="switchTab('many-to-many')">
                                    <i class="feather-message-square"></i> Many to Many
                                </button>
                            </div>

                            <!-- Single SMS Tab -->
                            <div class="sms-tab-content" :class="{ active: activeTab === 'single' }">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Select Customer</label>
                                        <select v-model="singleForm.customerId" @change="onSingleCustomerSelect" class="form-select" id="singleCustomerSelect">
                                            <option value="">Select Customer</option>
                                            <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                                                @{{ customer.name }} - @{{ customer.phone }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Or Enter Phone Number</label>
                                        <input 
                                            type="text" 
                                            v-model="singleForm.number" 
                                            class="form-control" 
                                            placeholder="8801XXXXXXXXX or 01XXXXXXXXX"
                                            @input="validatePhone">
                                        <small class="text-danger" v-if="singleForm.number && !isValidPhone(singleForm.number)">
                                            Invalid phone number format
                                        </small>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Message</label>
                                        <textarea 
                                            v-model="singleForm.message" 
                                            class="form-control" 
                                            rows="5"
                                            placeholder="Enter your message here..."
                                            maxlength="1000"
                                            @input="updateCharacterCount('single')"></textarea>
                                        <div class="character-count" :class="getCharacterCountClass('single')">
                                            <div>
                                                <strong>Characters:</strong> @{{ singleForm.message.length }} / 1000
                                            </div>
                                            <div>
                                                <strong>SMS Count:</strong> @{{ calculateSmsCount(singleForm.message) }} SMS
                                                <span class="badge" :class="isUnicode(singleForm.message) ? 'bg-warning' : 'bg-info'">
                                                    @{{ isUnicode(singleForm.message) ? 'Unicode' : 'GSM-7' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button 
                                            @click="sendSingleSMS" 
                                            class="btn btn-primary"
                                            :disabled="!canSendSingle || sending">
                                            <i class="feather-send" v-if="!sending"></i>
                                            <span v-if="sending">Sending...</span>
                                            <span v-else>Send SMS</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- One to Many Tab -->
                            <div class="sms-tab-content" :class="{ active: activeTab === 'one-to-many' }">
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="form-label mb-0">Select Customers</label>
                                            <div>
                                                <span class="badge bg-info me-2">Total: @{{ customers.length }} customers</span>
                                                <button 
                                                    @click="selectAllCustomers" 
                                                    class="btn btn-sm btn-outline-primary"
                                                    type="button">
                                                    <i class="feather-check-square"></i> Select All
                                                </button>
                                                <button 
                                                    @click="deselectAllCustomers" 
                                                    class="btn btn-sm btn-outline-secondary"
                                                    type="button">
                                                    <i class="feather-square"></i> Deselect All
                                                </button>
                                            </div>
                                        </div>
                                        <select 
                                            v-model="oneToManyForm.selectedCustomers" 
                                            class="form-select" 
                                            id="oneToManyCustomerSelect"
                                            multiple>
                                            <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                                                @{{ customer.name }} - @{{ customer.phone }}
                                            </option>
                                        </select>
                                        <small class="text-muted">
                                            You can select multiple customers | 
                                            <strong>Selected: @{{ oneToManyForm.selectedCustomers.length }}</strong> of @{{ customers.length }}
                                        </small>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Or Add Custom Numbers</label>
                                        <div class="input-group">
                                            <input 
                                                type="text" 
                                                v-model="oneToManyForm.customNumber" 
                                                class="form-control" 
                                                placeholder="8801XXXXXXXXX or 01XXXXXXXXX">
                                            <button 
                                                @click="addCustomNumber" 
                                                class="btn btn-outline-primary"
                                                :disabled="!isValidPhone(oneToManyForm.customNumber)">
                                                <i class="feather-plus"></i> Add
                                            </button>
                                        </div>
                                        <small class="text-danger" v-if="oneToManyForm.customNumber && !isValidPhone(oneToManyForm.customNumber)">
                                            Invalid phone number format
                                        </small>
                                    </div>
                                    <div class="col-12 mb-3" v-if="oneToManyForm.customNumbers.length > 0">
                                        <label class="form-label">Custom Numbers Added</label>
                                        <div class="d-flex flex-wrap gap-2">
                                            <span 
                                                v-for="(num, index) in oneToManyForm.customNumbers" 
                                                :key="index"
                                                class="badge bg-primary d-flex align-items-center gap-2">
                                                @{{ num }}
                                                <button 
                                                    @click="removeCustomNumber(index)"
                                                    class="btn-close btn-close-white"
                                                    style="font-size: 10px;"></button>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Message</label>
                                        <textarea 
                                            v-model="oneToManyForm.message" 
                                            class="form-control" 
                                            rows="5"
                                            placeholder="Enter your message here..."
                                            maxlength="1000"
                                            @input="updateCharacterCount('one-to-many')"></textarea>
                                        <div class="character-count" :class="getCharacterCountClass('one-to-many')">
                                            <div>
                                                <strong>Characters:</strong> @{{ oneToManyForm.message.length }} / 1000
                                            </div>
                                            <div>
                                                <strong>SMS Count:</strong> @{{ calculateSmsCount(oneToManyForm.message) }} SMS per recipient
                                                <span class="badge" :class="isUnicode(oneToManyForm.message) ? 'bg-warning' : 'bg-info'">
                                                    @{{ isUnicode(oneToManyForm.message) ? 'Unicode' : 'GSM-7' }}
                                                </span>
                                            </div>
                                            <div v-if="getTotalRecipients('one-to-many') > 0" class="mt-2">
                                                <strong>Total SMS:</strong> @{{ calculateSmsCount(oneToManyForm.message) * getTotalRecipients('one-to-many') }} SMS
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="alert alert-info">
                                            <strong>Total Recipients:</strong> @{{ getTotalRecipients('one-to-many') }}
                                        </div>
                                        <button 
                                            @click="sendOneToManySMS" 
                                            class="btn btn-primary"
                                            :disabled="!canSendOneToMany || sending">
                                            <i class="feather-send" v-if="!sending"></i>
                                            <span v-if="sending">Sending...</span>
                                            <span v-else>Send SMS to All</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Many to Many Tab -->
                            <div class="sms-tab-content" :class="{ active: activeTab === 'many-to-many' }">
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Select Customers (Optional)</label>
                                        <select 
                                            v-model="manyToManyForm.selectedCustomers" 
                                            class="form-select" 
                                            id="manyToManyCustomerSelect"
                                            multiple>
                                            <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                                                @{{ customer.name }} - @{{ customer.phone }}
                                            </option>
                                        </select>
                                        <small class="text-muted">Selected customers will be added to the list below</small>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <button @click="addCustomersToManyToMany" class="btn btn-outline-primary btn-sm">
                                            <i class="feather-plus"></i> Add Selected Customers
                                        </button>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Messages</label>
                                        <div v-for="(msg, index) in manyToManyForm.messages" :key="index" class="message-row">
                                            <div style="flex: 1;">
                                                <input 
                                                    type="text" 
                                                    v-model="msg.to" 
                                                    class="form-control mb-2" 
                                                    placeholder="8801XXXXXXXXX or 01XXXXXXXXX">
                                            </div>
                                            <div style="flex: 2;">
                                                <textarea 
                                                    v-model="msg.message" 
                                                    class="form-control" 
                                                    rows="2"
                                                    placeholder="Message for this number"
                                                    maxlength="1000"
                                                    @input="updateManyToManyCharacterCount(index)"></textarea>
                                                <div class="character-count" :class="getManyToManyCharacterCountClass(index)">
                                                    <div>
                                                        <strong>Characters:</strong> @{{ msg.message.length }} / 1000
                                                    </div>
                                                    <div>
                                                        <strong>SMS Count:</strong> @{{ calculateSmsCount(msg.message) }} SMS
                                                        <span class="badge" :class="isUnicode(msg.message) ? 'bg-warning' : 'bg-info'">
                                                            @{{ isUnicode(msg.message) ? 'Unicode' : 'GSM-7' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <button 
                                                    @click="removeMessageRow(index)" 
                                                    class="btn btn-danger btn-sm remove-row-btn"
                                                    :disabled="manyToManyForm.messages.length === 1">
                                                    <i class="feather-trash-2"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <button @click="addMessageRow" class="btn btn-outline-primary btn-sm mt-2">
                                            <i class="feather-plus"></i> Add Another Message
                                        </button>
                                    </div>
                                    <div class="col-12">
                                        <div class="alert alert-info">
                                            <div><strong>Total Messages:</strong> @{{ manyToManyForm.messages.length }}</div>
                                            <div v-if="getTotalSmsCount() > 0" class="mt-2">
                                                <strong>Total SMS:</strong> @{{ getTotalSmsCount() }} SMS
                                            </div>
                                        </div>
                                        <button 
                                            @click="sendManyToManySMS" 
                                            class="btn btn-primary"
                                            :disabled="!canSendManyToMany || sending">
                                            <i class="feather-send" v-if="!sending"></i>
                                            <span v-if="sending">Sending...</span>
                                            <span v-else>Send All Messages</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

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
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Vue === 'undefined') {
                console.error('Vue.js is not loaded. Please check the CDN link.');
                return;
            }
            
            try {
                new Vue({
                el: '#bulkSmsBdApp',
            data: {
                balance: null,
                loadingBalance: false,
                activeTab: 'single',
                sending: false,
                customers: @json($customers),
                
                singleForm: {
                    customerId: '',
                    number: '',
                    message: ''
                },
                
                oneToManyForm: {
                    selectedCustomers: [],
                    customNumber: '',
                    customNumbers: [],
                    message: ''
                },
                
                manyToManyForm: {
                    selectedCustomers: [],
                    messages: [
                        { to: '', message: '' }
                    ]
                }
            },
            mounted() {
                this.initSelect2();
                this.loadBalance();
            },
            computed: {
                canSendSingle() {
                    return (this.singleForm.number || this.singleForm.customerId) && 
                           this.singleForm.message.trim() && 
                           (!this.singleForm.number || this.isValidPhone(this.singleForm.number));
                },
                canSendOneToMany() {
                    const hasRecipients = this.oneToManyForm.selectedCustomers.length > 0 || 
                                         this.oneToManyForm.customNumbers.length > 0;
                    return hasRecipients && this.oneToManyForm.message.trim();
                },
                canSendManyToMany() {
                    if (this.manyToManyForm.messages.length === 0) return false;
                    return this.manyToManyForm.messages.every(msg => 
                        msg.to.trim() && 
                        msg.message.trim() && 
                        this.isValidPhone(msg.to)
                    );
                }
            },
            methods: {
                initSelect2() {
                    const self = this;
                    setTimeout(() => {
                        $('#singleCustomerSelect').select2({
                            placeholder: 'Select Customer',
                            allowClear: true
                        }).on('change', function() {
                            self.singleForm.customerId = $(this).val();
                            self.onSingleCustomerSelect();
                        });
                        
                        $('#oneToManyCustomerSelect').select2({
                            placeholder: 'Select Customers',
                            multiple: true
                        }).on('change', function() {
                            self.oneToManyForm.selectedCustomers = $(this).val() || [];
                        });
                        
                        $('#manyToManyCustomerSelect').select2({
                            placeholder: 'Select Customers',
                            multiple: true
                        }).on('change', function() {
                            self.manyToManyForm.selectedCustomers = $(this).val() || [];
                        });
                    }, 100);
                },
                switchTab(tab) {
                    this.activeTab = tab;
                },
                isValidPhone(number) {
                    if (!number) return false;
                    return /^(?:\+8801\d{9}|8801\d{9}|01\d{9})$/.test(number.trim());
                },
                validatePhone() {
                    // Auto-format phone number if needed
                },
                onSingleCustomerSelect() {
                    if (this.singleForm.customerId) {
                        const customer = this.customers.find(c => c.id == this.singleForm.customerId);
                        if (customer && customer.phone) {
                            this.singleForm.number = customer.phone;
                        }
                    }
                },
                updateCharacterCount(tab) {
                    // Character count is reactive
                },
                getCharacterCountClass(tab) {
                    const length = tab === 'single' ? this.singleForm.message.length : this.oneToManyForm.message.length;
                    if (length > 900) return 'danger';
                    if (length > 800) return 'warning';
                    return '';
                },
                addCustomNumber() {
                    if (this.isValidPhone(this.oneToManyForm.customNumber)) {
                        const normalized = this.normalizePhone(this.oneToManyForm.customNumber);
                        if (!this.oneToManyForm.customNumbers.includes(normalized)) {
                            this.oneToManyForm.customNumbers.push(normalized);
                            this.oneToManyForm.customNumber = '';
                        }
                    }
                },
                removeCustomNumber(index) {
                    this.oneToManyForm.customNumbers.splice(index, 1);
                },
                normalizePhone(number) {
                    if (number.startsWith('+880')) {
                        return number.substring(1);
                    } else if (number.startsWith('01')) {
                        return '88' + number;
                    }
                    return number;
                },
                getTotalRecipients(tab) {
                    if (tab === 'one-to-many') {
                        return this.oneToManyForm.selectedCustomers.length + this.oneToManyForm.customNumbers.length;
                    }
                    return 0;
                },
                selectAllCustomers() {
                    const allCustomerIds = this.customers.map(c => c.id.toString());
                    this.oneToManyForm.selectedCustomers = allCustomerIds;
                    $('#oneToManyCustomerSelect').val(allCustomerIds).trigger('change');
                },
                deselectAllCustomers() {
                    this.oneToManyForm.selectedCustomers = [];
                    $('#oneToManyCustomerSelect').val(null).trigger('change');
                },
                addMessageRow() {
                    this.manyToManyForm.messages.push({ to: '', message: '' });
                },
                removeMessageRow(index) {
                    if (this.manyToManyForm.messages.length > 1) {
                        this.manyToManyForm.messages.splice(index, 1);
                    }
                },
                updateManyToManyCharacterCount(index) {
                    // Character count is reactive
                },
                getManyToManyCharacterCountClass(index) {
                    const length = this.manyToManyForm.messages[index].message.length;
                    if (length > 900) return 'danger';
                    if (length > 800) return 'warning';
                    return '';
                },
                isUnicode(message) {
                    if (!message) return false;
                    // Check for Bangla characters (Unicode range: 0980-09FF)
                    // Check for emojis and other Unicode characters
                    const banglaRegex = /[\u0980-\u09FF]/;
                    const emojiRegex = /[\u{1F300}-\u{1F9FF}]/u;
                    const otherUnicodeRegex = /[^\x00-\x7F]/;
                    
                    return banglaRegex.test(message) || emojiRegex.test(message) || otherUnicodeRegex.test(message);
                },
                calculateSmsCount(message) {
                    if (!message || message.length === 0) return 0;
                    
                    const isUnicodeMsg = this.isUnicode(message);
                    const charCount = message.length;
                    
                    if (isUnicodeMsg) {
                        // Unicode: 70 chars per SMS, 67 chars per SMS in multipart
                        if (charCount <= 70) {
                            return 1;
                        } else {
                            return Math.ceil((charCount - 70) / 67) + 1;
                        }
                    } else {
                        // GSM-7: 160 chars per SMS, 153 chars per SMS in multipart
                        if (charCount <= 160) {
                            return 1;
                        } else {
                            return Math.ceil((charCount - 160) / 153) + 1;
                        }
                    }
                },
                getTotalSmsCount() {
                    let total = 0;
                    this.manyToManyForm.messages.forEach(msg => {
                        if (msg.message && msg.message.trim()) {
                            total += this.calculateSmsCount(msg.message);
                        }
                    });
                    return total;
                },
                addCustomersToManyToMany() {
                    this.manyToManyForm.selectedCustomers.forEach(customerId => {
                        const customer = this.customers.find(c => c.id == customerId);
                        if (customer && customer.phone) {
                            const normalized = this.normalizePhone(customer.phone);
                            // Check if already exists
                            const exists = this.manyToManyForm.messages.some(msg => msg.to === normalized);
                            if (!exists) {
                                this.manyToManyForm.messages.push({
                                    to: normalized,
                                    message: ''
                                });
                            }
                        }
                    });
                    this.manyToManyForm.selectedCustomers = [];
                    $('#manyToManyCustomerSelect').val(null).trigger('change');
                },
                async loadBalance() {
                    this.loadingBalance = true;
                    try {
                        const response = await axios.get('{{ route("bulk-sms-bd.get-balance") }}');
                        if (response.data.status) {
                            this.balance = response.data.balance || 0;
                        } else {
                            this.balance = 'N/A';
                        }
                    } catch (error) {
                        console.error('Error loading balance:', error);
                        this.balance = 'Error';
                    } finally {
                        this.loadingBalance = false;
                    }
                },
                async sendSingleSMS() {
                    if (!this.canSendSingle) return;
                    
                    this.sending = true;
                    try {
                        const number = this.singleForm.number || this.getCustomerPhone(this.singleForm.customerId);
                        const response = await axios.post('{{ route("bulk-sms-bd.send-single") }}', {
                            number: number,
                            message: this.singleForm.message
                        });
                        
                        if (response.data.success) {
                            toastr.success(response.data.message || 'SMS sent successfully');
                            this.singleForm = { customerId: '', number: '', message: '' };
                            $('#singleCustomerSelect').val(null).trigger('change');
                            this.loadBalance();
                        } else {
                            toastr.error(response.data.message || 'Failed to send SMS');
                        }
                    } catch (error) {
                        const message = error.response?.data?.message || 'Error sending SMS';
                        toastr.error(message);
                    } finally {
                        this.sending = false;
                    }
                },
                async sendOneToManySMS() {
                    if (!this.canSendOneToMany) return;
                    
                    this.sending = true;
                    try {
                        const numbers = [];
                        
                        // Add selected customers' phones
                        this.oneToManyForm.selectedCustomers.forEach(customerId => {
                            const phone = this.getCustomerPhone(customerId);
                            if (phone) numbers.push(phone);
                        });
                        
                        // Add custom numbers
                        numbers.push(...this.oneToManyForm.customNumbers);
                        
                        if (numbers.length === 0) {
                            toastr.error('Please select at least one recipient');
                            return;
                        }
                        
                        const response = await axios.post('{{ route("bulk-sms-bd.send-one-to-many") }}', {
                            numbers: numbers,
                            message: this.oneToManyForm.message
                        });
                        
                        if (response.data.success) {
                            toastr.success(response.data.message || 'SMS sent successfully');
                            this.oneToManyForm = {
                                selectedCustomers: [],
                                customNumber: '',
                                customNumbers: [],
                                message: ''
                            };
                            $('#oneToManyCustomerSelect').val(null).trigger('change');
                            this.loadBalance();
                        } else {
                            toastr.error(response.data.message || 'Failed to send SMS');
                        }
                    } catch (error) {
                        const message = error.response?.data?.message || 'Error sending SMS';
                        toastr.error(message);
                    } finally {
                        this.sending = false;
                    }
                },
                async sendManyToManySMS() {
                    if (!this.canSendManyToMany) return;
                    
                    this.sending = true;
                    try {
                        const messages = this.manyToManyForm.messages.map(msg => ({
                            to: this.normalizePhone(msg.to),
                            message: msg.message.trim()
                        }));
                        
                        const response = await axios.post('{{ route("bulk-sms-bd.send-many-to-many") }}', {
                            messages: messages
                        });
                        
                        if (response.data.success) {
                            toastr.success(response.data.message || 'SMS sent successfully');
                            this.manyToManyForm = {
                                selectedCustomers: [],
                                messages: [{ to: '', message: '' }]
                            };
                            $('#manyToManyCustomerSelect').val(null).trigger('change');
                            this.loadBalance();
                        } else {
                            toastr.error(response.data.message || 'Failed to send SMS');
                        }
                    } catch (error) {
                        const message = error.response?.data?.message || 'Error sending SMS';
                        toastr.error(message);
                    } finally {
                        this.sending = false;
                    }
                },
                getCustomerPhone(customerId) {
                    const customer = this.customers.find(c => c.id == customerId);
                    return customer ? this.normalizePhone(customer.phone) : null;
                },
                formatBalance(balance) {
                    if (typeof balance === 'number') {
                        return balance.toLocaleString('en-US', { maximumFractionDigits: 2 });
                    }
                    return balance;
                }
            }
            });
            } catch (error) {
                console.error('Vue initialization error:', error);
            }
        });
    </script>
    <style>
        .spin {
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
@endsection

