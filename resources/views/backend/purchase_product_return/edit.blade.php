@extends('backend.master')

@push('css')
    <link href="{{ versioned_url('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ versioned_url('assets/css/tagsinput.css') }}" rel="stylesheet" type="text/css" />
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.7.15/vue.min.js"></script>
    <script src="{{ versioned_asset('assets/js/edit_purchase_return_vue.js') }}" defer></script>
@endpush

@section('page_title')
    Purchase Return
@endsection

@section('page_heading')
    Edit Purchase Return
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body" id="formApp">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-3">Purchase Return Order</h4>
                        <a href="{{ route('ViewAllPurchaseReturnOrder') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>

                    <form action="{{ url('update/purchase-return/order') }}" method="POST">
                        @csrf
                        <input type="hidden" name="purchase_product_return_id" value="{{ $data->id }}">

                        <div class="row mb-3">
                            <div class="col-md-4 mb-4">
                                <label for="order_status">Status <span class="text-danger">*</span></label>
                                <select id="order_status" class="form-control" name="order_status" required>
                                    <option value="pending" :selected="orderStatus === 'pending'">Draft</option>
                                    <option value="returned" :selected="orderStatus === 'returned'">Returned</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="purchase_product_warehouse_id">Warehouse <span class="text-danger">*</span></label>
                                <select id="purchase_product_warehouse_id" data-toggle="select2" class="form-control"
                                    name="purchase_product_warehouse_id" v-model="selectedWarehouse" @change="onWarehouseChange" required>
                                    <option value="">Select Warehouse</option>
                                    @foreach ($productWarehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="supplier">Supplier <span class="text-danger">*</span></label>
                                <select id="supplier_id" class="form-control" data-toggle="select2" name="supplier_id" v-model="selectedSupplier" required>
                                    <option value="">Select Supplier</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="date">Return Date</label>
                                <input type="date" class="form-control" name="purchase_date" v-model="purchaseDate" required>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="product_purchase_quotation_id">Purchase Code or id</label>
                                <input type="text" id="product_purchase_quotation_id" class="form-control" name="purchase_code" v-model="purchaseCode">
                            </div>
                            <div class="col-md-4 mb-4">
                                <label for="reference">Reference</label>
                                <input type="text" id="reference" class="form-control" v-model="reference" name="reference">
                            </div>
                        </div>

                        <div class="d-flex justify-content-center">
                            <div class="mb-3 w-50">
                                <div style="position: relative;">
                                    <input type="text" v-model="searchQuery" @keyup="getData" class="form-control"
                                        placeholder="Search for a product...">

                                    <div v-if="searchResults.length > 0" class="dropdown-menu searchResultsClass">
                                        <ul>
                                            <li v-for="product in searchResults" :key="product.id" @click="addRow(product)">
                                                @{{ product.name }}
                                            </li>
                                        </ul>
                                        <div v-if="loadingMore" class="text-center">Loading...</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Purchase Items Table -->
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    {{-- <th>Warehouse Room</th> --}}
                                    {{-- <th>Cartoon</th> --}}
                                    <th>Stock</th>
                                    <th>Return Qty</th>
                                    <th>Stock Codes</th>
                                    <th>Unit Price</th>
                                    {{-- <th>Discount (%)</th> --}}
                                    {{-- <th>Tax (%)</th> --}}
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="purchaseItems">
                                <tr v-for="(item, index) in purchaseItems" :key="item.rowKey" v-if="item.isVisible">
                                    <input type="hidden" :name="`product[${index}][id]`" v-model="item.product_id" readonly>
                                    <input type="hidden" :name="`product[${index}][variant_combination_id]`"
                                        :value="item.variant_combination_id">
                                    <input type="hidden" :name="`product[${index}][name]`" :value="item.name">
                                    <input type="hidden" :name="`product[${index}][display_name]`"
                                        :value="item.display_name">
                                    <input type="hidden" :name="`product[${index}][previous_stock]`"
                                        :value="item.previous_stock">
                                    <td class="align-middle">
                                        <div class="form-control-plaintext">@{{ item.display_name }}</div>
                                    </td>
                                    
                                    {{-- <td>
                                        <select class="form-control" :name="`product[${index}][warehouse_room_id]`"
                                            v-model="item.warehouse_room_id" @change="onRowRoomChange(item)"
                                            :disabled="!selectedWarehouse">
                                            <option value="">Select Room</option>
                                            <option v-for="room in rooms" :value="room.id"
                                                :key="`room-${room.id}-${index}`">
                                                @{{ room.title }}
                                            </option>
                                        </select>
                                    </td> --}}
                                    {{-- <td>
                                        <select class="form-control" :name="`product[${index}][warehouse_cartoon_id]`"
                                            v-model="item.warehouse_cartoon_id" :disabled="!item.warehouse_room_id">
                                            <option value="">Select Cartoon</option>
                                            <option v-for="cartoon in item.cartoonOptions" :value="cartoon.id"
                                                :key="`cartoon-${cartoon.id}-${index}`">
                                                @{{ cartoon.title }}
                                            </option>
                                        </select>
                                    </td> --}}
                                    <td class="align-middle">
                                        <div class="form-control-plaintext">@{{ Number(item.previous_stock || 0).toFixed(2) }}</div>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control quantity" style="width: 100px;"
                                            :name="`product[${index}][quantities]`" v-model="item.quantity"
                                            @input="handleItemNumericInput(item, 'quantity', 'Return Quantity')">
                                    </td>
                                    <td class="align-middle">
                                        <div v-for="i in item.quantity" :key="i" class="form-control-plaintext">
                                            <input type="text" class="form-control" style="width: 130px;"
                                                :name="`product[${index}][stock_codes][${i}]`" 
                                                v-model="item.stock_codes[i - 1]" />
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control price"
                                            :name="`product[${index}][prices]`" v-model="item.price"
                                            @input="handleItemNumericInput(item, 'price', 'Unit Price')">
                                    </td>
                                    {{-- <td>
                                        <input type="text" class="form-control discount"
                                            :name="`product[${index}][discounts]`" v-model="item.discount"
                                            @input="handleItemNumericInput(item, 'discount', 'Discount')">
                                    </td> --}}
                                    {{-- <td>
                                        <input type="text" class="form-control tax" :name="`product[${index}][taxes]`"
                                            v-model="item.tax" @input="handleItemNumericInput(item, 'tax', 'Tax')">
                                    </td> --}}
                                    <td>
                                        <input type="text" class="form-control total"
                                            :name="`product[${index}][totals]`" :value="getItemTotalPrice(item)" readonly
                                            hidden>
                                        <div class="form-control">
                                            @{{ getItemTotalPrice(item).toFixed(2) }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger removeRow"
                                            @click="removeRow(index)">X</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row form-group">
                                            <label class="col-sm-4 control-label">Total Quantities</label>
                                            <div class="col-sm-4">
                                                <label class="control-label total_quantity text-success" style="font-size: 20px;">
                                                    @{{ totalQuantity }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @foreach ($other_charges_types as $key => $item)
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row form-group">
                                                <label class="col-sm-4 control-label">{{ $item->title }}</label>
                                                <div class="col-sm-4">
                                                    <input type="hidden" class="other_charges_title" name="other_charges[{{ $key }}][title]" value="{{ $item->title }}">
                                                    <input type="text" class="form-control text-right only_currency other_charges_amount other_charges_amount{{ $key }}" name="other_charges[{{ $key }}][amount]" value="" @keyup="calc_other_charges">
                                                </div>
                                                <div class="col-sm-4">
                                                    <select class="form-control other_charges_type other_charges_type{{ $key }}" name="other_charges[{{ $key }}][type]" style="width: 100%;" @change="calc_other_charges">
                                                        <option value="percent" {{ $item->type == 'percent' ? 'selected' : '' }}>Per%</option>
                                                        <option value="fixed" {{ $item->type == 'fixed' ? 'selected' : '' }}>Fixed</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row form-group">
                                            <label for="discount_to_all_input" class="col-sm-4 control-label">Discount on All</label>
                                            <div class="col-sm-4">
                                                <input type="text" class="form-control text-right only_currency" id="discount_to_all_input" name="discount_on_all" v-model="discount_on_all" @input="handleDiscountOnAllInput">
                                            </div>
                                            <div class="col-sm-4">
                                                <select class="form-control" id="discount_to_all_type" name="discount_to_all_type" v-model="discount_on_all_type">
                                                    <option value="in_percentage">Per%</option>
                                                    <option value="in_fixed">Fixed</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row form-group">
                                            <label for="round_off_input" class="col-sm-4 control-label">Round Off</label>
                                            <div class="col-sm-4">
                                                <input type="text" class="form-control text-right only_currency" id="round_off_input" name="total_round_off_amt" v-model="round_off_input" @input="handleRoundOffInput">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row form-group">
                                            <label for="purchase_note" class="col-sm-4 control-label">Note</label>
                                            <div class="col-sm-8">
                                                <textarea class="form-control text-left" id="purchase_note" name="purchase_note" v-model="note"></textarea>
                                                <span id="purchase_note_msg" style="display: none;" class="text-danger"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 purchase_quotation_table">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <th class="text-right">Subtotal</th>
                                            <th class="text-right">
                                                <input type="hidden" name="subtotal" :value="subtotal.toFixed(2)">
                                                ৳ <b id="subtotal">@{{ subtotal.toFixed(2) }}</b>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="text-right">Other Charges</th>
                                            <th class="text-right">
                                                <input type="hidden" name="other_charges_amt" :value="other_charges_amt.toFixed(2)">
                                                ৳ <b id="other_charges_amt">@{{ other_charges_amt.toFixed(2) }}</b>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="text-right">Discount on All</th>
                                            <th class="text-right">
                                                <input type="hidden" name="discount_to_all_amt" :value="discount_to_all_amt.toFixed(2)">
                                                ৳ <b id="discount_to_all_amt">@{{ discount_to_all_amt.toFixed(2) }}</b>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="text-right">Round Off</th>
                                            <th class="text-right">
                                                ৳ <b id="round_off_amt">@{{ Number(total_round_off_amt).toFixed(2) }}</b>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="text-right">Grand Total</th>
                                            <th class="text-right">
                                                <input type="hidden" name="grand_total_amt" :value="grand_total_amt.toFixed(2)">
                                                ৳ <b id="grand_total_amt">@{{ grand_total_amt.toFixed(2) }}</b>
                                            </th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        @if ($data?->order_status == 'pending')
                            <button type="submit" class="btn btn-success mt-3">Update Return</button>
                        @else
                            <div class="alert alert-info mt-3">
                                Order has already been returned and cannot be modified.
                            </div>
                        @endif
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
