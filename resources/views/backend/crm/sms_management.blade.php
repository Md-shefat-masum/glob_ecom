@extends('backend.master')

@section('header_css')
    <link href="{{ url('assets') }}/plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />
    <style>
        .select2-selection {
            height: 38px !important;
            border: 1px solid #ced4da !important;
        }
        .select2 {
            width: 100% !important;
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
        }
        .character-count.warning {
            color: #ff9800;
        }
        .character-count.danger {
            color: #dc3545;
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
    </style>
@endsection

@section('page_title')
    SMS Management
@endsection

@section('page_heading')
    SMS Management
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        
        <!-- Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">SMS Management</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ url('/crm-home') }}">CRM</a></li>
                            <li class="breadcrumb-item active">SMS Management</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vue App -->
        <div id="smsManagementApp">
            
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            
                            <!-- Provider Selection & Balance -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">SMS Provider</label>
                                    <select v-model="provider" @change="loadBalance" class="form-select">
                                        <option value="bulksmsbd">Bulk SMS BD</option>
                                        <option value="twilio">Twilio</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <div class="balance-info">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <small class="text-muted d-block">Account Balance</small>
                                                <div class="balance-value" v-if="balance !== null">
                                                    @{{ balance }} SMS
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
                                            @{{ singleForm.message.length }} / 1000 characters
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
                                        <label class="form-label">Select Customers</label>
                                        <select 
                                            v-model="oneToManyForm.selectedCustomers" 
                                            class="form-select" 
                                            id="oneToManyCustomerSelect"
                                            multiple>
                                            <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                                                @{{ customer.name }} - @{{ customer.phone }}
                                            </option>
                                        </select>
                                        <small class="text-muted">You can select multiple customers</small>
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
                                            @{{ oneToManyForm.message.length }} / 1000 characters
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
                                                    @{{ msg.message.length }} / 1000 characters
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
                                            <strong>Total Messages:</strong> @{{ manyToManyForm.messages.length }}
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
        new Vue({
            el: '#smsManagementApp',
            data: {
                provider: 'bulksmsbd',
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
                        const response = await axios.post('{{ route("SmsManagementGetBalance") }}', {
                            provider: this.provider
                        });
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
                        const response = await axios.post('{{ route("SmsManagementSendSingle") }}', {
                            provider: this.provider,
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
                        
                        const response = await axios.post('{{ route("SmsManagementSendOneToMany") }}', {
                            provider: this.provider,
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
                        
                        const response = await axios.post('{{ route("SmsManagementSendManyToMany") }}', {
                            provider: this.provider,
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
                }
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

