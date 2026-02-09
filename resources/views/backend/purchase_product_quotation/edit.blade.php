@extends('backend.master')

@section('header_css')
    <link href="{{ url('assets') }}/plugins/dropify/dropify.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('assets') }}/plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('assets') }}/css/tagsinput.css" rel="stylesheet" type="text/css" />
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
    </style>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.7.15/vue.min.js"></script>
    {{-- <script src="{{ asset('assets/js/vue.min.js') }}"></script> --}}
    <script src="{{ asset('assets/js/edit_purchase_vue.js') }}"></script>
@endsection

@section('page_title')
    Purchase Product Quotation
@endsection
@section('page_heading')
    Edit Purchase Product Quotation
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body" id="formApp">
                   
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-3">Purchase Product Quotation</h4>
                         <a href="{{ route('ViewAllPurchaseProductQuotation')}}" class="btn btn-secondary">
                             <i class="fas fa-arrow-left"></i>
                         </a>
                     </div>

                    <form action="{{ url('update/purchase-product/quotation') }}" method="POST">
                        @csrf   

                        <input type="hidden" name="purchase_product_quotation_id" value="{{ $data->id }}">

                        <!-- Warehouse and Supplier Selection -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="purchase_product_warehouse_id">Warehouse</label>
                                <select id="purchase_product_warehouse_id" data-toggle="select2" class="form-control"
                                    name="purchase_product_warehouse_id" v-model="selectedWarehouse" @change="getRooms">
                                    <option value="">Select Warehouse</option>
                                    @foreach ($productWarehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="purchase_product_warehouse_room_id">Warehouse Room</label>
                                <select id="purchase_product_warehouse_room_id" data-toggle="select2" class="form-control"
                                    name="purchase_product_warehouse_room_id" v-model="selectedRoom" @change="getCartoons">
                                    <option value="">Select Warehouse Room</option>
                                    <option v-for="room in rooms" :value="room.id" :key="room.id">
                                        @{{ room.title }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="purchase_product_warehouse_room_cartoon_id">Warehouse Room Cartoon</label>
                                <select id="purchase_product_warehouse_room_cartoon_id" data-toggle="select2"
                                    class="form-control" name="purchase_product_warehouse_room_cartoon_id"
                                    v-model="selectedCartoon">
                                    <option value="">Select Warehouse Room Cartoon</option>
                                    <option v-for="cartoon in cartoons" :value="cartoon.id" :key="cartoon.id">
                                        @{{ cartoon.title }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-4 mt-4">
                                <label for="supplier">Supplier</label>
                                <select id="supplier_id" class="form-control" data-toggle="select2" name="supplier_id"
                                    v-model="selectedSupplier" required>
                                    <option value="">Select Supplier</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mt-4">
                                <label for="date">Purchase Date</label>
                                <input type="date" class="form-control" name="purchase_date" v-model="purchaseDate"
                                    required>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center">
                            <div class="mb-3 w-50">
                                <div style="position: relative;">
                                    <input type="text" v-model="searchQuery" @keyup="getData" class="form-control"
                                        placeholder="Search for a product...">

                                    <div v-if="searchResults.length > 0" class="dropdown-menu searchResultsClass">

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
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Discount (%)</th>
                                    <th>Tax (%)</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="purchaseItems">
                                <tr v-for="(item, index) in purchaseItems" :key="index" v-if="item.isVisible">
                                    <input type="hidden" class="form-control total" :name="`product[${index}][id]`"
                                        v-model="item.id" readonly>
                                    {{-- <td> --}}
                                        <input type="hidden" class="form-control total" :name="`product[${index}][product_id]`"
                                            v-model="item.product_id" readonly> 
                                    {{-- </td>  --}}
                                    <td>
                                        <input type="text" class="form-control total" :name="`product[${index}][name]`"
                                            v-model="item.name" readonly>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control quantity"
                                            :name="`product[${index}][quantities]`" v-model="item.quantity" value="1"
                                            min="1">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control price" :name="`product[${index}][prices]`"
                                            v-model="item.price" step="0.01">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control discount"
                                            :name="`product[${index}][discounts]`" v-model="item.discount" value="0"
                                            min="0" step="0.01">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control tax" :name="`product[${index}][taxes]`"
                                            v-model="item.tax" value="0" min="0" step="0.01">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control total"
                                            :name="`product[${index}][totals]`" :value="getItemTotalPrice(item)" readonly
                                            hidden>
                                        <div class="form-control">
                                            @{{ getItemTotalPrice(item).toFixed(2) }}
                                        </div>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger removeRow"
                                            @click="removeRow(index)">X</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Summary Section -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row form-group">
                                            <label for="" class="col-sm-4 control-label">Total Quantities</label>
                                            <div class="col-sm-4">
                                                <label class="control-label total_quantity text-success"
                                                    style="font-size: 20px;">
                                                    @{{ totalQuantity }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @foreach ($other_charges_types as $key=>$item)
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row form-group">
                                            <label for="other_charges_input" class="col-sm-4 control-label">
                                                {{ $item->title }}
                                            </label>
                                            <div class="col-sm-4">
                                                <input type="hidden" class="other_charges_title" name="other_charges[{{ $key }}][title]" value="{{ $item->title }}">
                                                <input type="text" 
                                                    class="form-control text-right only_currency other_charges_amount other_charges_amount{{ $key }}"
                                                    name="other_charges[{{ $key }}][amount]"
                                                    value="" @keyup="calc_other_charges"/>
                                                
                                            </div>
                                            
                                            <div class="col-sm-4">
                                                <select class="form-control other_charges_type other_charges_type{{ $key }}" 
                                                    name="other_charges[{{ $key }}][type]" 
                                                    style="width: 100%;" @change="calc_other_charges">
                                                    <option value="percent" {{ $item->type == "percent" ? 'selected' : '' }}>Per%</option>
                                                    <option value="fixed" {{ $item->type == "fixed" ? 'selected' : '' }}>Fixed</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                                {{-- <div class="row">
                                    <div class="col-md-12">
                                        <div class="row form-group">
                                            <label for="other_charges_input" class="col-sm-4 control-label">Other
                                                Charges</label>
                                            <div class="col-sm-4">
                                                <input type="text" class="form-control text-right only_currency"
                                                    id="other_charges_input_amount" name="other_charges_input_amount"
                                                    value="" v-model="other_charges_input_amount" />
                                            </div>
                                            <div class="col-sm-4">
                                                <select class="form-control" id="other_charges_type"
                                                    name="other_charges_type" style="width: 100%;" value=""
                                                    v-model="other_charges_type">
                                                    <option value="">-Select-</option>
                                                    <option value="vat">VAT</option>
                                                    <option value="import">Import</option>
                                                    <option value="regular_expense">Regular Expense</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div> --}}

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row form-group">
                                            <label for="discount_to_all_input" class="col-sm-4 control-label">Discount on
                                                All</label>
                                            <div class="col-sm-4">
                                                <input type="text" class="form-control text-right only_currency"
                                                    id="discount_on_all" name="discount_on_all"
                                                    v-model="discount_on_all" />
                                            </div>
                                            <div class="col-sm-4">
                                                <select class="form-control" id="discount_to_all_type"
                                                    name="discount_to_all_type" v-model="discount_on_all_type" :key="discount_on_all_type">
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
                                            <label for="purchase_note" class="col-sm-4 control-label">Note</label>
                                            <div class="col-sm-8">
                                                <textarea class="form-control text-left" id="purchase_note" name="purchase_note" v-model="note"></textarea>
                                                <span id="purchase_note_msg" style="display: none;"
                                                    class="text-danger"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-6 purchase_quotation_table">
                                <table class="table ">
                                    <tbody>
                                        <tr>
                                            <th class="text-right">Subtotal</th>
                                            <th class="text-right">
                                                <input type="hidden" name="subtotal"
                                                    :value="subtotal.toFixed(2)">
                                                ৳ <b id="subtotal">@{{ subtotal?.toFixed(2) }}</b>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="text-right">Other Charges</th>
                                            <th class="text-right">
                                                <input type="hidden" name="other_charges_amt"
                                                    :value="other_charges_amt?.toFixed(2)">
                                                ৳ <b id="other_charges_amt">@{{ other_charges_amt?.toFixed(2) }}</b>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="text-right">Discount on All</th>
                                            <th class="text-right">
                                                <input type="hidden" name="discount_to_all_amt"
                                                    :value="discount_to_all_amt?.toFixed(2)">
                                                ৳ <b id="discount_to_all_amt">@{{ discount_to_all_amt?.toFixed(2) }}</b>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="text-right">Round Off</th>
                                            <th class="text-right">
                                                <input type="hidden" name="total_round_off_amt"
                                                    :value="total_round_off_amt?.toFixed(2)">
                                                ৳ <b id="round_off_amt">@{{ total_round_off_amt?.toFixed(2) }}</b>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="text-right">Grand Total</th>
                                            <th class="text-right">
                                                <input type="hidden" name="grand_total_amt"
                                                    :value="grand_total_amt?.toFixed(2)">
                                                ৳ <b id="grand_total_amt">@{{ grand_total_amt?.toFixed(2) }}</b>
                                            </th>
                                        </tr>
                                    </tbody>

                                </table>
                            </div>

                        </div>

                        <button type="submit" class="btn btn-success mt-3">Submit Purchase</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection


@section('footer_js')
    <script src="{{ url('assets') }}/plugins/dropify/dropify.min.js"></script>
    <script src="{{ url('assets') }}/pages/fileuploads-demo.js"></script>
    <script src="{{ url('assets') }}/plugins/select2/select2.min.js"></script>
    {{-- <script src="{{ url('assets') }}/js/tagsinput.js"></script> --}}

    {{-- <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script> --}}

    <script type="text/javascript">
        // $('[data-toggle="select2"]').select2();
    </script>
@endsection
