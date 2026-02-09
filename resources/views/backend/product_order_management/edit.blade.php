@extends('backend.master')

@section('header_css')
    <link href="{{ versioned_url('assets/plugins/dropify/dropify.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ versioned_url('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ versioned_url('assets/css/tagsinput.css') }}" rel="stylesheet" type="text/css" />
    {{-- <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet"> --}}
    <style>
        .select2-selection {
            height: 34px !important;
            border: 1px solid #ced4da !important;
        }

        .select2 {
            width: 100% !important;
        }

        .bootstrap-tagsinput .badge {
            margin: 2px 2px !important;
        }

        #searchResults {
            width: 100%;
            /* Match the search box width */
            top: 100%;
            /* Position the dropdown below the input */
            left: 0;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            background-color: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.7.15/vue.min.js"></script> --}}
    <script src="{{ versioned_asset('assets/js/vue.min.js') }}"></script>
    <script src="{{ versioned_asset('assets/js/product_order_vue.js') }}" defer></script>
@endsection


@section('page_title')
    Product Order
@endsection
@section('page_heading')
    Product Order
@endsection

@section('content')
    <div class="container" style="max-width: 1500px;">
        <div class="row">
            <div class="col-lg-12 col-xl-12">
                <div class="card">
                    <div class="card-body" id="formApp" data-order-slug="{{ $data->slug ?? '' }}" data-order-data="{}">
    
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4>Edit Order</h4>
                            <a href="{{ route('ViewAllProductOrder') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i>
                            </a>
                        </div>
    
                        <form action="{{ url('/update/product-order/manage') }}" @submit.prevent="save_order($event)" method="POST">
                            @csrf
                            <input type="hidden" name="product_order_id" value="{{ $data->id }}">
    
                            <div class="product_order">
                                <div class="order_meta">
                                    <div class="customer_info">
                                        <div>
                                            <label for="order_status">Order Status</label>
                                            <select id="order_status" class="form-control" name="order_status">
                                                <option value="invoiced" {{ $data->order_status == 'invoiced' ? 'selected' : '' }}>Invoiced</option>
                                                <option value="pending" {{ $data->order_status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="delivered" {{ $data->order_status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                            </select>
                                        </div>
                                        <div class="">
                                            <label for="product_warehouse_id">Warehouse</label>
                                            <div>
                                                <select id="product_warehouse_id" data-toggle="select2" class="form-control"
                                                    name="product_warehouse_id" v-model="selectedWarehouse">
                                                    <option value="">Select Warehouse</option>
                                                    @foreach ($productWarehouses as $warehouse)
                                                        <option value="{{ $warehouse->id }}">{{ $warehouse->title }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
    
                                        <div class="">
                                            <div class="d-flex justify-content-between">
                                                <label>Customer</label>
                                                <div>
                                                    <button type="button" class="btn btn-info btn-sm" id="addCustomerBtn"
                                                        data-toggle="modal" data-target="#addCustomerModal">
                                                        <i class="fa fa-user-plus"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-success btn-sm ml-1" id="deliveryInfoBtn"
                                                        data-toggle="modal" data-target="#deliveryInfoModal"
                                                        :disabled="!$('#customer_id').val()"
                                                        title="Delivery Information">
                                                        <i class="fa fa-truck"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div>
                                                <select id="customer_id" class="form-control" name="customer_id"></select>
                                            </div>
                                            <div v-if="customerDue > 0" class="mt-2">
                                                <small class="text-danger">
                                                    <strong>Previous Due: ৳ @{{ customerDue.toFixed(2) }}</strong>
                                                </small>
                                            </div>
                                        </div>
    
                                        <div class="">
                                            <label for="date">Sale Code</label>
                                            <input type="text" id="order_code" class="form-control" value="{{ $data->order_code }}"
                                                name="order_code" readonly required>
                                        </div>
    
                                        <div class="">
                                            <label for="date">Sales Date</label>
                                            <input type="date" id="sale_date" class="form-control" value="{{ $data->sale_date }}"
                                                name="sale_date" required>
                                        </div>
    
                                        <div class="">
                                            <label for="due_date">
                                                Due Date
                                                <span v-if="total_due > 0" class="text-danger">*</span>
                                            </label>
                                            <input type="date" id="due_date" class="form-control" name="due_date" 
                                                ref="due_date_input"
                                                value="{{ $data->due_date }}"
                                                :class="{'border-danger': total_due > 0 && !$refs.due_date_input?.value}">
                                        </div>
    
                                        <div class="">
                                            <label for="reference">Reference</label>
                                            <input type="text" id="reference" class="form-control" name="reference" 
                                                value="{{ $data->reference }}">
                                        </div>
    
                                    </div>
                                    <div class="selected_products">
                                        <div class="d-flex justify-content-center">
                                            <div class="mb-3 w-50">
                                                <div style="position: relative;">
                                                    <input type="text" v-model="searchQuery" @keyup="getData"
                                                        class="form-control" placeholder="Search for a product...">
    
                                                    <div v-if="searchResults && searchResults.length > 0"
                                                        class="dropdown-menu searchResultsClass">
    
                                                        <!-- Displaying products -->
                                                        <ul>
                                                            <li v-for="product in searchResults" :key="product.id"
                                                                @click="addRow(product)">
                                                                @{{ product.name }}
                                                            </li>
                                                        </ul>
    
                                                        <div v-if="loadingMore" class="text-center">Loading...</div>
                                                    </div>
                                                </div>
    
                                            </div>
                                        </div>
    
                                        <!-- Purchase Items Table -->
                                        <table class="table table-bordered selected_products_table">
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Qty</th>
                                                    <th>Unit Price</th>
                                                    <th>Dis(%)</th>
                                                    <th>Tax (%)</th>
                                                    <th>Total</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="purchaseItems">
                                                <tr v-for="(item, index) in purchaseItems" :key="index"
                                                    v-if="item.isVisible">
                                                    <input type="hidden" class="form-control total"
                                                        :name="`product[${index}][id]`" v-model="item.id" readonly>
                                                    <td>
                                                        <div>
                                                            @{{ item.name }}
                                                        </div>
                                                        <!-- Show available stock with variant/unit label -->
                                                        <small v-if="item.has_variant && item.selected_variant" class="text-info">
                                                            @{{ item.selected_variant.name }} (Stock: @{{ item.available_stock }})
                                                        </small>
                                                        <small v-else-if="item.has_unit_price && item.selected_unit_price" class="text-info">
                                                            @{{ item.selected_unit_price.unit_label }}
                                                        </small>
                                                        <small v-else-if="item.available_stock" class="text-muted">
                                                            Stock: @{{ item.available_stock }}
                                                        </small>
                                                        <!-- Show price information -->
                                                        <div class="mt-1">
                                                            <small v-if="item.has_variant && item.selected_variant">
                                                                <span v-if="item.selected_variant.discount_price && item.selected_variant.discount_price > 0">
                                                                    <span class="text-muted" style="text-decoration: line-through;">৳@{{ item.selected_variant.price }}</span>
                                                                    <span class="text-success font-weight-bold"> ৳@{{ item.selected_variant.discount_price }}</span>
                                                                </span>
                                                                <span v-else class="text-primary font-weight-bold">
                                                                    ৳@{{ item.selected_variant.price }}
                                                                </span>
                                                            </small>
                                                            <small v-else-if="item.has_unit_price && item.selected_unit_price">
                                                                <span v-if="item.selected_unit_price.discount_price && item.selected_unit_price.discount_price > 0">
                                                                    <span class="text-muted" style="text-decoration: line-through;">৳@{{ item.selected_unit_price.price }}</span>
                                                                    <span class="text-success font-weight-bold"> ৳@{{ item.selected_unit_price.discount_price }}</span>
                                                                </span>
                                                                <span v-else class="text-primary font-weight-bold">
                                                                    ৳@{{ item.selected_unit_price.price }}
                                                                </span>
                                                            </small>
                                                        </div>
                                                        <input type="hidden" class="form-control total"
                                                            :name="`product[${index}][product_id]`" v-model="item.product_id" readonly>
                                                        <!-- Hidden fields for variant/unit tracking -->
                                                        <input type="hidden" v-if="item.selected_variant_id"
                                                            :name="`product[${index}][variant_id]`" v-model="item.selected_variant_id">
                                                        <input type="hidden" v-if="item.selected_unit_price_id"
                                                            :name="`product[${index}][unit_price_id]`" v-model="item.selected_unit_price_id">
                                                    </td>
                                                    <td>
                                                        <!-- Show variant dropdown if product has variants -->
                                                        <select v-if="item.has_variant" 
                                                            class="form-control form-control-sm mb-1"
                                                            v-model="item.selected_variant_id"
                                                            @change="onVariantChange(item)">
                                                            <option v-for="variant in item.variants" :key="variant.id" :value="variant.id">
                                                                @{{ variant.name }} (Stock: @{{ variant.stock }}) - @{{ variant.discount_price && variant.discount_price > 0 ? '৳' + variant.price + ' → ৳' + variant.discount_price : '৳' + variant.price }}
                                                            </option>
                                                        </select>
                                                        <!-- Show unit price dropdown if product has unit pricing -->
                                                        <select v-else-if="item.has_unit_price" 
                                                            class="form-control form-control-sm mb-1"
                                                            v-model="item.selected_unit_price_id"
                                                            @change="onUnitPriceChange(item)">
                                                            <option v-for="unitPrice in item.unit_prices" :key="unitPrice.id" :value="unitPrice.id">
                                                                @{{ unitPrice.unit_label }} - @{{ unitPrice.discount_price && unitPrice.discount_price > 0 ? '৳' + unitPrice.price + ' → ৳' + unitPrice.discount_price : '৳' + unitPrice.price }}
                                                            </option>
                                                        </select>
                                                        <input type="text" class="form-control text-center quantity"
                                                            :name="`product[${index}][quantities]`" 
                                                            @keyup="validate_number($event, 1, 999999)"
                                                            @keydown="handleArrowKeys($event, item, 'quantity', 1)"
                                                            @click="$event.target.select()"
                                                            v-model="item.quantity"
                                                            value="1" min="1">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control text-center price"
                                                            :name="`product[${index}][prices]`" 
                                                            @keyup="validate_number($event, 1, 999999)"
                                                            @click="$event.target.select()"
                                                            v-model="item.price"
                                                            step="0.01">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control text-center discount"
                                                            :name="`product[${index}][discounts]`" v-model="item.discount"
                                                            @keyup="validate_number($event, 0, 100)"
                                                            @click="$event.target.select()"
                                                            :value="item.discount_parcent" min="0" step="0.01">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control text-center tax"
                                                            :name="`product[${index}][taxes]`" v-model="item.tax"
                                                            @keyup="validate_number($event, 0, 100)"
                                                            @click="$event.target.select()"
                                                            value="0" min="0" step="0.01">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control total"
                                                            :name="`product[${index}][totals]`"
                                                            :value="getItemTotalPrice(item)" readonly hidden>
                                                        <div class="form-control text-right">
                                                            @{{ getItemTotalPrice(item).toFixed(2) }}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger removeRow"
                                                            @click="removeRow(index)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
    
                                        <div class="form-group">
                                            <label for="note" class="control-label">Note</label>
                                            <div class="">
                                                <textarea class="form-control text-left" id="note" name="note">{{ $data->note }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="payments">
                                    <div>
                                        <table class="order_at_a_glance_table">
                                            <tbody>
                                                <tr>
                                                    <th colspan="2"> Total Quantities</th>
                                                    <th>
                                                        @{{ totalQuantity }}
                                                    </th>
                                                </tr>
    
                                                @foreach ($other_charges_types as $key => $item)
                                                    <tr>
                                                        <th>
                                                            {{ $item->title }}
                                                            <input type="hidden" :name="`other_charges[{{ $key }}][title]`" value="{{ $item->title }}">
                                                        </th>
                                                        <td>
                                                            <select
                                                                class="form-control other_charges_type other_charges_type{{ $key }}"
                                                                name="other_charges[{{ $key }}][type]"
                                                                style="width: 100%;" @change="calc_other_charges">
                                                                <option value="percent"
                                                                    {{ $item->type == 'percent' ? 'selected' : '' }}>Per%
                                                                </option>
                                                                <option value="fixed"
                                                                    {{ $item->type == 'fixed' ? 'selected' : '' }}>Fixed
                                                                </option>
                                                            </select>
                                                        </td>
                                                        <th>
                                                            <input type="text"
                                                                class="form-control text-right only_currency other_charges_amount other_charges_amount{{ $key }}"
                                                                name="other_charges[{{ $key }}][amount]"
                                                                value="" @keyup="calc_other_charges" />
                                                        </th>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <th>
                                                        Discount on All
                                                    </th>
                                                    <td>
                                                        <select class="form-control" id="discount_to_all_type"
                                                            name="discount_to_all_type" v-model="discount_on_all_type"
                                                            @change="calculateDiscountOnAll">
                                                            <option value="in_percentage">Per%</option>
                                                            <option value="in_fixed">Fixed</option>
                                                        </select>
                                                    </td>
                                                    <th>
                                                        <input type="text" class="form-control text-right only_currency"
                                                            id="discount_to_all_input" name="discount_on_all"
                                                            v-model="discount_on_all"
                                                            @input="calculateDiscountOnAll" />
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th colspan="2">
                                                        Round Off
                                                    </th>
                                                    <th>
                                                        <input type="text" class="form-control text-right only_currency"
                                                            id="round_off_from_total" name="round_off_from_total"
                                                            v-model="round_off_from_total" />
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th class="text-right" colspan="2">Subtotal</th>
                                                    <th class="text-right">
                                                        <input type="hidden" name="subtotal_amt"
                                                            :value="subtotal.toFixed(2)">
                                                        ৳ <b id="subtotal_amt">@{{ subtotal.toFixed(2) }}</b>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th class="text-right" colspan="2">Other Charges</th>
                                                    <th class="text-right">
                                                        <input type="hidden" name="other_charges_amt"
                                                            :value="other_charges_amt?.toFixed(2)">
                                                        ৳ <b id="other_charges_amt">@{{ other_charges_amt?.toFixed(2) }}</b>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th class="text-right" colspan="2">Discount on All</th>
                                                    <th class="text-right">
                                                        <input type="hidden" name="discount_to_all_amt"
                                                            :value="discount_to_all_amt?.toFixed(2)">
                                                        ৳ <b id="discount_to_all_amt">@{{ discount_to_all_amt?.toFixed(2) }}</b>
                                                    </th>
                                                </tr>
                                                <tr style="">
                                                    <th class="text-right" colspan="2">
                                                        Decimal round Off
                                                        <i class="hover-q " data-container="body" data-toggle="popover"
                                                            data-placement="top"
                                                            data-content="Go to Site Settings-> Site -> Disable the Round Off(Checkbox)."
                                                            data-html="true" data-trigger="hover"
                                                            data-original-title="Do you wants to Disable Round Off ?"
                                                            title="">
                                                            <i class="fa fa-info-circle text-maroon text-black hover-q"></i>
                                                        </i>
    
                                                    </th>
                                                    <th class="text-right" style="">
                                                        <input type="hidden" name="decimal_round_off"
                                                            :value="Number(total_round_off_amt).toFixed(2)">
                                                        ৳ <b id="round_off_amt">@{{ total_round_off_amt }}</b>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th class="text-right" colspan="2">Grand Total</th>
                                                    <th class="text-right">
                                                        <input type="hidden" name="grand_total_amt"
                                                            :value="grand_total_amt.toFixed(2)">
                                                        ৳ <b id="total_amt">@{{ grand_total_amt?.toFixed(2) }}</b>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th colspan="3" class="text-center bg-secondary text-white">
                                                        Payments
                                                    </th>
                                                </tr>
                                                <tr v-for="(mode, index) in payment_modes" :key="index">
                                                    <th class="text-right" colspan="2">
                                                        @{{ index }}
                                                    </th>
                                                    <td>
                                                        <input type="number" class="form-control text-right"
                                                            :name="`payments[${index}]`" 
                                                            @click="setPaymentToDue($event, index)"
                                                            v-model="payment_modes[index]"
                                                            min="0"
                                                            step="0.01" />
                                                    </td>
                                                </tr>
                                                <tr v-if="hasAdvance">
                                                    <th colspan="2" class="text-right">
                                                        <label class="mb-0">
                                                            <input type="checkbox" v-model="useAdvance" @change="toggleAdvanceAdjustment">
                                                            <b class="text-success">
                                                                Adjust from Advance <br/>
                                                                (Available: ৳ @{{ availableAdvance.toFixed(2) }})
                                                            </b>
                                                        </label>
                                                    </th>
                                                    <th>
                                                        <input type="number" 
                                                            class="form-control text-right" 
                                                            name="payments[advance_adjustment]"
                                                            v-model="advanceAdjustmentAmount"
                                                            :max="availableAdvance"
                                                            :disabled="!useAdvance"
                                                            step="0.01"
                                                            min="0" />
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th colspan="2" class="text-right">
                                                        Paid
                                                    </th>
                                                    <th>
                                                        <div>
                                                            <input type="hidden" name="paid_amount"
                                                                :value="total_paid">
                                                            ৳ <b class="text-success">@{{ total_paid?.toFixed(2) }}</b>
                                                        </div>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th colspan="2" class="text-right">
                                                        Due
                                                    </th>
                                                    <th>
                                                        <div>
                                                            <input type="hidden" name="due_amount"
                                                                :value="total_due > 0 ? total_due : 0">
                                                            <span v-if="total_due < 0" class="text-warning">
                                                                ৳ <b>0.00</b> <small>(Overpaid by @{{ Math.abs(total_due).toFixed(2) }})</small>
                                                            </span>
                                                            <span v-else-if="total_due === 0 || total_due < 0.01" class="text-success">
                                                                ৳ <b>0.00</b> <small>(Fully Paid)</small>
                                                            </span>
                                                            <span v-else class="text-danger">
                                                                ৳ <b>@{{ total_due?.toFixed(2) }}</b>
                                                            </span>
                                                        </div>
                                                    </th>
                                                </tr>
                                            </tbody>
                                        </table>
    
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-end">
                                <button type="button" @click="previewOrder" class="btn btn-info m-2">
                                    <i class="fas fa-eye"></i> Preview Order
                                </button> 
                                <button type="submit" class="btn btn-success m-2" :disabled="isSubmitting">
                                    <span v-if="isSubmitting">
                                        <i class="fas fa-spinner fa-spin"></i> Processing Order...
                                    </span>
                                    <span v-else>
                                        <i class="fas fa-check"></i> Update Order
                                    </span>
                                </button>
                            </div>
                        </form>
                        
                        <!-- Add Customer Modal -->
                        <div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <form id="customerForm" class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="addCustomerModalLabel">Add New Customer</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Name</label>
                                            <input type="text" class="form-control" id="customer_name" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Phone</label>
                                            <input type="text" class="form-control" id="customer_phone" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Address</label>
                                            <textarea class="form-control" id="customer_address"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Save Customer</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Delivery Information Modal -->
                        <div class="modal fade" id="deliveryInfoModal" tabindex="-1" aria-labelledby="deliveryInfoModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <form id="deliveryInfoForm" class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deliveryInfoModalLabel">
                                            <i class="fa fa-truck"></i> Delivery Information
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Receiver Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" v-model="deliveryInfo.receiver_name" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Receiver Phone <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" v-model="deliveryInfo.receiver_phone" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Customer Phone</label>
                                                <input type="text" class="form-control" v-model="deliveryInfo.customer_phone">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">District <span class="text-danger">*</span></label>
                                                <select class="form-control" v-model="deliveryInfo.district" @change="loadUpazilas" required>
                                                    <option value="">Select District</option>
                                                    <option v-for="district in districts" :key="district.id" :value="district.id">
                                                        @{{ district.name }}
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Upazila <span class="text-danger">*</span></label>
                                                <select class="form-control" v-model="deliveryInfo.upazila" required>
                                                    <option value="">Select Upazila</option>
                                                    <option v-for="upazila in upazilas" :key="upazila.id" :value="upazila.id">
                                                        @{{ upazila.name }}
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Thana</label>
                                                <input type="text" class="form-control" v-model="deliveryInfo.thana">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Post Office</label>
                                                <input type="text" class="form-control" v-model="deliveryInfo.post_office">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Full Address <span class="text-danger">*</span></label>
                                                <textarea class="form-control" v-model="deliveryInfo.full_address" rows="3" required></textarea>
                                            </div>
                                            
                                            <!-- Delivery Method -->
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Delivery Method <span class="text-danger">*</span></label>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="delivery_method" v-model="deliveryInfo.delivery_method" value="courier" id="methodCourier">
                                                    <label class="form-check-label" for="methodCourier">
                                                        Courier
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="delivery_method" v-model="deliveryInfo.delivery_method" value="pathao" id="methodPathao">
                                                    <label class="form-check-label" for="methodPathao">
                                                        Pathao
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="delivery_method" v-model="deliveryInfo.delivery_method" value="uber" id="methodUber">
                                                    <label class="form-check-label" for="methodUber">
                                                        Uber
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="delivery_method" v-model="deliveryInfo.delivery_method" value="steadfast" id="methodSteadfast">
                                                    <label class="form-check-label" for="methodSteadfast">
                                                        Steadfast
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="delivery_method" v-model="deliveryInfo.delivery_method" value="store_pickup" id="methodStorePickup">
                                                    <label class="form-check-label" for="methodStorePickup">
                                                        Store Pickup
                                                    </label>
                                                </div>
                                            </div>
                                            
                                            <!-- Courier Name (show only if courier is selected) -->
                                            <div class="col-md-12 mb-3" v-if="deliveryInfo.delivery_method == 'courier'">
                                                <label class="form-label">Courier Name <span class="text-danger">*</span></label>
                                                <select class="form-control" v-model="deliveryInfo.courier_name" required>
                                                    <option value="">Select Courier</option>
                                                    <option value="Sundarban Courier">Sundarban Courier</option>
                                                    <option value="SA Paribahan">SA Paribahan</option>
                                                    <option value="Janani Courier">Janani Courier</option>
                                                    <option value="Paperfly">Paperfly</option>
                                                    <option value="Redx">Redx</option>
                                                    <option value="eCourier">eCourier</option>
                                                    <option value="other">Other (Enter manually)</option>
                                                </select>
                                            </div>
                                            
                                            <!-- Manual courier name input -->
                                            <div class="col-md-12 mb-3" v-if="deliveryInfo.delivery_method == 'courier' && deliveryInfo.courier_name == 'other'">
                                                <label class="form-label">Enter Courier Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" v-model="deliveryInfo.courier_name_custom" placeholder="Enter courier name" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-primary" @click="saveDeliveryInfo">Save Delivery Info</button>
                                    </div>
                                </form>
                            </div>
                        </div>
    
                    </div>
                </div>
            </div>
        </div>

    <!-- Order Preview Modal -->
    <div class="modal fade" id="orderPreviewModal" tabindex="-1" aria-labelledby="orderPreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header sticky-top bg-white" style="z-index: 1050;">
                    <h5 class="modal-title" id="orderPreviewModalLabel">
                        <i class="fas fa-file-invoice"></i> Order Preview
                    </h5>
                    <div class="ms-auto d-flex gap-2">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="window.product_order_app.printPreview()">
                            <i class="fas fa-print"></i> Print Preview
                        </button>
                        <button type="button" class="btn btn-success btn-sm" onclick="window.product_order_app.proceedWithOrder()">
                            <i class="fas fa-check"></i> Proceed Order
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="window.product_order_app.closePreviewModal()">
                            <i class="fas fa-times"></i> Close
                        </button>
                    </div>
                </div>
                <div class="modal-body p-0">
                    <div id="invoicePreviewContent" class="p-3">
                        <!-- Invoice content will be rendered here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('js')
    <script src="{{ versioned_url('assets/plugins/select2/select2.min.js') }}"></script>
 
    <script>
        $(document).ready(function() {
            const default_user = 1;
            const user_id = window.user_id || default_user;

            // 🔹 Add Customer Form Submit
            $('#customerForm').on('submit', function(e) {
                e.preventDefault();
                const payload = {
                    name: $('#customer_name').val(),
                    phone: $('#customer_phone').val(),
                    address: $('#customer_address').val(),
                };

                $.ajax({
                    url: '/customers/store',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(payload),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        // Add new option and select it
                        const newOption = new Option(data.name, data.id, true, true);
                        $('#customer_id').append(newOption).trigger('change');

                        // Reset form and close modal
                        $('#customerForm')[0].reset();
                        $('#addCustomerModal').modal('hide');
                    },
                    error: function(err) {
                        console.error('Error saving customer:', err);
                        alert('Failed to save customer.');
                    }
                });
            });
        });

    </script>
     
@endpush
