@extends('backend.master')

@section('page_title')
    Orders
@endsection
@section('page_heading')
    Orders Details
@endsection
@section('header_css')
    <style>
        @media print {
            .page-content,
            .main-content{
                padding: 0!important;
                margin: 0!important;
            }
            .footer,
            .vertical-menu,
            #page-topbar{
                display: none!important;
            }
            .hidden-print {
                display: none !important;
            }
            
            .badge {
                border: none !important;
                box-shadow: none !important;
            }
            
            .no_print{
                display: none !important;
            }
            
            *{
                background: transparent !important;
                color: black !important;
                box-sizing: border-box;
            }
            
            .card, .card-body{
                border: unset !important;
                box-shadow: unset !important;
                padding: 0!important;
            }
            
            .row {
                width: 100%;
            }
            
            @page {
                size: 6.8in;
                margin: 0mm; /* optional */
            }
            
            body,
            #printableArea{
                width: 6.8in;
                /*height: 210mm;*/
                /*border: 1px solid gray;*/
                /*margin: 0 auto;*/
                padding: 20px;
            }
        }
        
        table tbody tr td {
            padding: 5px 10px !important
        }

        table thead tr th {
            padding: 5px 10px !important
        }

        address {
            font-size: 15px;
        }

        address h6 {
            font-size: 15px;
        }

        .order_details_text p {
            font-size: 15px;
        }
    </style>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">

        <div class="card" id="printableArea">
            <div class="card-body">
                <div class="row">
                    <div class="col text-left">
                        <h4 class="m-0">{{$generalInfo->company_name}}</h4>
                    </div>
                    <div class="col text-center">
                        @if(file_exists(public_path($generalInfo->logo_dark)))
                            <img src="{{url($generalInfo->logo_dark)}}" alt="" height="50">
                        @endif
                    </div>
                    <div class="col text-right">
                        <h4 class="m-0">Invoice</h4>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-6">

                        @if($shippingInfo)
                            <h6 class="font-weight-bold">Shipping Info :</h6>
                            <address class="line-h-24">
                                <b>Name : {{$shippingInfo->full_name}}</b><br>
                                Phone : {{$shippingInfo->phone}}<br>
                                <span class="no_print">
                                    Email : {{$shippingInfo->email}}<br>
                                </span>
                                District : {{$shippingInfo->city}}<br>
                                @if($shippingInfo->thana)
                                    Thana : {{$shippingInfo->thana}}<br>
                                @endif
                                Street : {{$shippingInfo->address}}<br>
                                <span class="no_print">
                                    Postal code : {{$shippingInfo->post_code}}, {{$shippingInfo->country}}<br>
                                </span>
                            </address>
                        @endif

                    </div><!-- end col -->
                    <div class="col-6">
                        <div class="mt-3 float-right order_details_text">
                            <p class="mb-1"><strong>Order NO: </strong> #{{$order->order_no}}</p>
                            <p class="mb-1"><strong>Tran. ID: </strong> #{{$order->trx_id}}</p>
                            <p class="mb-1"><strong>Order Date: </strong>
                                {{date("jS F, Y", strtotime($order->order_date))}}</p>
                            <p class="mb-1"><strong>Order Status: </strong>
                                @php
                            
                                    if ($order->order_status == 0) {
                                        echo '<span class="badge badge-soft-warning" style="padding: 2px 10px !important;">Pending</span>';
                                    } elseif ($order->order_status == 1) {
                                        echo '<span class="badge badge-soft-info" style="padding: 2px 10px !important;">Approved</span>';
                                    } elseif ($order->order_status == 2) {
                                        echo '<span class="badge badge-soft-primary" style="padding: 2px 10px !important;">Dispatch</span>';
                                    } elseif ($order->order_status == 3) {
                                        echo '<span class="badge badge-soft-info" style="padding: 2px 10px !important;">Intransit</span>';
                                    } elseif ($order->order_status == 4) {
                                        echo '<span class="badge badge-soft-success" style="padding: 2px 10px !important;">Delivered</span>';
                                    } elseif ($order->order_status == 5) {
                                        echo '<span class="badge badge-soft-dark" style="padding: 2px 10px !important;">Return</span>';
                                    } else {
                                        echo '<span class="badge badge-soft-danger" style="padding: 2px 10px !important;">Cancelled</span>';
                                    }
                                @endphp
                            </p>
                            <p class="mb-1"><strong>Delivery Method: </strong>
                                @php
                                    if ($order->delivery_method == 1) {
                                        echo '<span class="badge badge-soft-success" style="padding: 3px 5px !important;">Home Delivery</span>';
                                    }
                                    if ($order->delivery_method == 2) {
                                        echo '<span class="badge badge-soft-success" style="padding: 3px 5px !important;">Store Pickup</span>';
                                    }
                                @endphp
                            </p>
                            <p class="mb-1"><strong>Payment Method: </strong>
                                @php
                                    if ($order->payment_method == NULL) {
                                        echo '<span class="badge badge-soft-danger" style="padding: 2px 10px !important;">Unpaid</span>';
                                    } elseif ($order->payment_method == 1) {
                                        echo '<span class="badge badge-soft-info" style="padding: 2px 10px !important;">COD</span>';
                                    } elseif ($order->payment_method == 2) {
                                        echo '<span class="badge badge-soft-success" style="padding: 2px 10px !important;">bKash</span>';
                                    } elseif ($order->payment_method == 3) {
                                        echo '<span class="badge badge-soft-success" style="padding: 2px 10px !important;">Nagad</span>';
                                    } else {
                                        echo '<span class="badge badge-soft-success" style="padding: 2px 10px !important;">Card</span>';
                                    }
                                @endphp
                            </p>
                            <p class="mb-1 no_print">
                                <strong>Payment Status: </strong>
                                @php
                                    if ($order->payment_status == 0) {
                                        echo '<span class="badge badge-soft-warning" style="padding: 2px 10px !important;">Unpaid</span>';
                                    } elseif ($order->payment_status == 1) {
                                        echo '<span class="badge badge-soft-success" style="padding: 2px 10px !important;">Paid</span>';
                                    } else {
                                        echo '<span class="badge badge-soft-danger" style="padding: 2px 10px !important;">Failed</span>';
                                    }
                                @endphp
                            </p>
                            @if($order->reference_code)
                                <p class="mb-1 no_print">
                                    <strong>Reference: </strong>
                                    {{-- @php
                                    if($order->payment_status == 0){
                                    echo '<span class="badge badge-soft-warning"
                                        style="padding: 2px 10px !important;">Unpaid</span>';
                                    } elseif($order->payment_status == 1) {
                                    echo '<span class="badge badge-soft-success"
                                        style="padding: 2px 10px !important;">Paid</span>';
                                    } else {
                                    echo '<span class="badge badge-soft-danger"
                                        style="padding: 2px 10px !important;">Failed</span>';
                                    }
                                    @endphp --}}
                                    <span class="badge badge-soft-success"
                                        style="padding: 2px 10px !important;">{{ $order->reference_code }}</span>
                                </p>
                            @endif
                            @if($order->customer_src_type_id)
                                <p class="m-b-10 no_print">
                                    <strong>Customer Source Type: </strong>
                                    {{-- @php
                                    if($order->payment_status == 0){
                                    echo '<span class="badge badge-soft-warning"
                                        style="padding: 2px 10px !important;">Unpaid</span>';
                                    } elseif($order->payment_status == 1) {
                                    echo '<span class="badge badge-soft-success"
                                        style="padding: 2px 10px !important;">Paid</span>';
                                    } else {
                                    echo '<span class="badge badge-soft-danger"
                                        style="padding: 2px 10px !important;">Failed</span>';
                                    }
                                    @endphp --}}
                                    <span class="badge badge-soft-success" style="padding: 2px 10px !important;">
                                        {{ $order->customerSourceType->title }}
                                    </span>
                                </p>
                            @endif
                        </div>
                    </div><!-- end col -->
                </div>
                <!-- end row -->

                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mt-4">
                                <thead>
                                    <tr>
                                        <!--<th class="text-center" style="width: 60px;">SL</th>-->
                                        <th>Item</th>
                                        <!--<th class="text-center">Variant</th>-->
                                        <th class="text-center">Qty</th>
                                        <th class="text-center">Unit Cost</th>
                                        <th class="text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $sl = 1;
                                    @endphp
                                    @foreach ($orderDetails as $details)

                                        @php
                                            if ($details->color_id)
                                                $colorInfo = App\Models\Color::where('id', $details->color_id)->first();
                                            if ($details->storage_id)
                                                $storageInfo = App\Models\StorageType::where('id', $details->storage_id)->first();
                                            if ($details->sim_id)
                                                $simInfo = App\Models\Sim::where('id', $details->sim_id)->first();
                                            if ($details->region_id)
                                                $regionInfo = DB::table('country')->where('id', $details->region_id)->first();
                                            if ($details->warrenty_id)
                                                $warrentyInfo = App\Models\ProductWarrenty::where('id', $details->warrenty_id)->first();
                                            if ($details->device_condition_id)
                                                $deviceCondition = App\Models\DeviceCondition::where('id', $details->device_condition_id)->first();
                                            if ($details->size_id)
                                                $productSize = App\Models\ProductSize::where('id', $details->size_id)->first();
                                        @endphp

                                        <tr>
                                            <!--<td class="text-center">-->
                                            <!--    {{$sl++}}-->
                                            <!--</td>-->
                                            <td>
                                                @if($details->is_package == 1)
                                                    <span class="badge badge-soft-info no_print">Package</span>
                                                @else
                                                    <span class="badge badge-soft-success no_print">Product</span>
                                                @endif
                                                <b>{{$details->product_name}}</b>
                                                <br />
                                                <span class="no_print">
                                                    Category : {{$details->category_name}},
                                                    Warehouse : {{$details->warehouse_title}},
                                                    Room : {{$details->warehouse_room_title}},
                                                    Cartoon: {{$details->warehouse_room_cartoon_title}}
                                                </span>
                                                <div>
                                                    @if($details->color_id) Color: {{$colorInfo ? $colorInfo->name : ''}} |
                                                    @endif
                                                    @if($details->storage_id) Storage:
                                                        {{$storageInfo ? $storageInfo->ram : ''}}/{{$storageInfo ? $storageInfo->rom : ''}}
                                                    | @endif
                                                    @if($details->sim_id) SIM: {{$simInfo ? $simInfo->name : ''}} @endif
                                                    @if($details->size_id) Size: {{$productSize ? $productSize->name : ''}}
                                                    @endif
    
                                                    <br>
                                                    @if($details->region_id) Region: {{$regionInfo ? $regionInfo->name : ''}} |
                                                    @endif
                                                    @if($details->warrenty_id) Warrenty:
                                                    {{$warrentyInfo ? $warrentyInfo->name : ''}} | @endif
                                                    @if($details->device_condition_id) Condition:
                                                    {{$deviceCondition ? $deviceCondition->name : ''}} @endif
                                                </div>
                                            </td>
                                            <!--<td class="text-center">-->
                                                
                                            <!--</td>-->
                                            <td class="text-center">{{$details->qty}} {{$details->unit_name}}</td>
                                            <td class="text-center">৳ {{number_format($details->unit_price, 2)}}</td>
                                            <td class="text-right">৳ {{number_format($details->total_price, 2)}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="clearfix pt-3">
                            <h6 class="text-muted">Billing Address:</h6>
                            @if($billingAddress)
                                <address class="line-h-24">
                                    {{$billingAddress->address}},
                                    @if($shippingInfo->thana)
                                        {{$shippingInfo->thana}}<br>
                                    @endif
                                    {{$billingAddress->city}} {{$billingAddress->post_code}},
                                    {{$billingAddress->country}}<br>
                                </address>
                            @endif
                        </div>

                        @if($userInfo)
                            <div class="clearfix pt-2">
                                <h6 class="text-muted">User Account Info:</h6>
                                <address class="line-h-24">
                                    {{$userInfo->name}}<br>
                                    @if($userInfo->email) {{$userInfo->email}}<br> @endif
                                    @if($userInfo->phone) {{$userInfo->phone}}<br> @endif
                                    @if($userInfo->address) {{$userInfo->address}} @endif
                                </address>
                            </div>
                        @endif

                        @if($order->order_note)
                            <div class="clearfix pt-2">
                                <h6 class="text-muted">Order note by Customer:</h6>
                                <p>
                                    {{$order->order_note}}
                                </p>
                            </div>
                        @endif

                    </div>
                    <div class="col-6 text-right">
                        <div class="float-right">
                            <p><b>Sub-total :</b> ৳ {{number_format($order->sub_total, 2)}}</p>
                            <p><b>Discount @if($order->coupon_code)({{$order->coupon_code}})@endif:</b> ৳
                                {{number_format($order->discount, 2)}}
                            </p>
                            <p class="no_print">
                                <b>VAT/TAX :</b> ৳ {{number_format($order->vat + $order->tax, 2)}}</p>
                            <p><b>Delivery Charge :</b> ৳ {{number_format($order->delivery_fee, 2)}}</p>
                            <h3><b>Total Order Amount :</b> ৳ {{number_format($order->total, 2)}}</h3>
                        </div>
                        <div class="clearfix"></div>

                        <div class="hidden-print mt-4 mb-4">
                            <div class="text-right">
                                <a href="javascript:void(0);" onclick="printPageArea('printableArea')"
                                    class="btn btn-primary waves-effect waves-light"><i class="fa fa-print m-r-5"></i>
                                    Print Invoice</a>
                                    
                                <a href="/pos/invoice/print/{{$order->id}}" target="_blank"
                                    class="btn btn-secondary waves-effect waves-light">
                                    <i class="fa fa-print m-r-5"></i>
                                    Print Pos Invoice
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card p-3 no_print">
            <form action="{{url('order/info/update')}}" id="delivery_status_form" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="order_id" value="{{$order->id}}">
                <div class="row">
                    <div class="col-lg-9">
                        <div class="form-group" style="margin-bottom: 0px">
                            <label style="margin-bottom: .2rem; font-weight: 500;">Special Note For Order (Visible by
                                Admin Only) :</label>
                            <textarea name="order_remarks" class="form-control" style="height: 149px !important;"
                                placeholder="Special Note By Admin">{{$order->order_remarks}}</textarea>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="form-group" style="margin-bottom: .5rem;">
                            <label style="margin-bottom: .2rem; font-weight: 500;">Est. Delivery Date :</label>
                            <input type="date" class="form-control" name="estimated_dd" value="{{$order->estimated_dd}}"
                                required>
                        </div>
             
                        <div class="form-group" style="margin-bottom: .5rem;">
                            <label style="margin-bottom: .2rem; font-weight: 500;">Order Status :</label>
                            <select name="order_status" class="form-control" required>
                                <option value="">Change Status</option>
                                <option value="0" @if($order->order_status == 0) selected @endif disabled>
                                    Pending
                                </option>
                                <option value="1" @if($order->order_status == 1) selected @endif
                                    @if(!in_array($order->order_status, [0])) disabled @endif>
                                    Approved
                                </option>
                                <option value="2" @if($order->order_status == 2) selected @endif
                                    @if(!in_array($order->order_status, [1,2])) disabled @endif>
                                    Dispatch
                                </option>
                                <option value="3" @if($order->order_status == 3) selected @endif
                                    @if(in_array($order->order_status, [0,1,2,3,4,5,6])) disabled @endif>
                                    Intransit
                                </option>
                                <option value="4" @if($order->order_status == 4) selected @endif
                                    @if(in_array($order->order_status, [0,1,2,3,4,5,6])) disabled @endif>
                                    Delivered
                                </option>
                                <option value="5" @if($order->order_status == 5) selected @endif
                                    @if(in_array($order->order_status, [0,1,2,3,4,5,6])) disabled @endif>
                                    Return
                                </option>
                                <option value="6" @if($order->order_status == 6) selected @endif
                                    @if(!in_array($order->order_status, [0, 1, 2, 3])) disabled @endif>
                                    Cancel
                                </option>
                            </select>
                        </div>
                        
                        <div class="pathao_form_wrapper border border-1 my-2 p-2">
                            <label for="toggle_pathao_form">
                                <b class="me-3"> Send into pathao </b>
                                <input type="checkbox" id="toggle_pathao_form" v-model="pathaoEnabled" />
                            </label>
                            <div class="form_body">
                                <div v-if="pathaoLoading.cities" class="text-center py-2">
                                    <small class="text-muted">Loading cities...</small>
                                </div>
                                <div v-else>
                                    <div class="form-group mb-2">
                                        <label>City <span class="text-danger">*</span></label>
                                        <div>
                                            <select class="form-control city_select2" v-model="pathaoData.city_id" 
                                                :disabled="pathaoLoading.zones" @change="onCityChange">
                                                <option value="">Select City</option>
                                                <option v-for="(city, index) in cities" :value="city.city_id" :key="index">
                                                    @{{city.city_name}}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label>Zone <span class="text-danger">*</span></label>
                                        <div>
                                            <select class="form-control zone_select2" v-model="pathaoData.zone_id"
                                                :disabled="pathaoLoading.zones || !pathaoData.city_id" @change="onZoneChange">
                                                <option value="">Select Zone</option>
                                                <option v-for="(zone, index) in zones" :value="zone.zone_id" :key="index">
                                                    @{{zone.zone_name}}
                                                </option>
                                            </select>
                                            <small v-if="pathaoLoading.zones" class="text-muted">Loading zones...</small>
                                        </div>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label>Area <span class="text-danger">*</span></label>
                                        <select class="form-control area_select2" v-model="pathaoData.area_id"
                                            :disabled="pathaoLoading.areas || !pathaoData.zone_id" @change="onAreaChange">
                                            <option value="">Select Area</option>
                                            <option v-for="(area, index) in areas" :value="area.area_id" :key="index">
                                                @{{area.area_name}}
                                            </option>
                                        </select>
                                        <small v-if="pathaoLoading.areas" class="text-muted">Loading areas...</small>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label>Delivery Type <span class="text-danger">*</span></label>
                                        <select class="form-control" v-model="pathaoData.delivery_type">
                                            <option value="48">48 hours</option>
                                            <option value="72">72 hours</option>
                                            <option value="120">120 hours</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label>Item Type <span class="text-danger">*</span></label>
                                        <select class="form-control" v-model="pathaoData.item_type">
                                            <option value="2">Document</option>
                                            <option value="3">Parcel</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-2">
                                        <label>Item Weight (kg) <span class="text-danger">*</span></label>
                                        <input class="form-control" type="number" step="0.01" v-model="pathaoData.item_weight" 
                                            @change="calculateDeliveryCost" />
                                    </div>
                                    <div class="form-group mb-2">
                                        <label>Delivery Cost</label>
                                        <input class="form-control" type="number" v-model="pathaoData.delivery_cost" 
                                            :disabled="pathaoLoading.price" readonly />
                                        <small v-if="pathaoLoading.price" class="text-muted">Calculating...</small>
                                        <button v-if="pathaoData.city_id && pathaoData.zone_id && pathaoData.item_weight" 
                                            type="button" class="btn btn-sm btn-link p-0 mt-1" 
                                            @click="calculateDeliveryCost" :disabled="pathaoLoading.price">
                                            Recalculate
                                        </button>
                                    </div>
                                    <div v-if="pathaoError" class="alert alert-danger alert-sm mb-2">
                                        @{{pathaoError}}
                                    </div>
                                    <div v-if="pathaoSuccess" class="alert alert-success alert-sm mb-2">
                                        @{{pathaoSuccess}}
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-info" 
                                            @click="addToPathao" 
                                            :disabled="pathaoLoading.create || !isPathaoFormValid">
                                            <span v-if="pathaoLoading.create">
                                                <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                                Processing...
                                            </span>
                                            <span v-else>Add to Pathao</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
   
                        <div class="form-group" id="delivery-man-group" style="display: none;">
                            <label style="margin-bottom: .2rem; font-weight: 500;">Delivery Man :</label>
                            <select name="delivery_man_id" class="form-control">
                                <option value="">Select Delivery Man</option>
                                @foreach($delivery_man as $deliveryMan)
                                    <option value="{{ $deliveryMan->id }}" 
                                        @if($order->orderDeliveryMen?->delivery_man_id == $deliveryMan->id) selected @endif>
                                        {{ $deliveryMan->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success rounded w-100 mt-1">Save Changes</button>
                    </div>
                </div>
            </form>
        </div>

    </div> <!-- end col -->
</div>
<style>
    .pathao_form_wrapper .form_body{
        display: none;
    }
    .pathao_form_wrapper:has(#toggle_pathao_form:checked) .form_body{
        display: block;
    }
    .select2-container {
        width: 100% !important;
    }
    .alert-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
    }
    .spinner-border-sm {
        width: 0.875rem;
        height: 0.875rem;
        border-width: 0.125em;
    }
</style>
@endsection

@section("footer_js")
    <link href="{{url('assets')}}/plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />
    <script src="{{url('assets')}}/plugins/select2/select2.min.js"></script>
    <script src="/js/vue.js"></script>
    <script src="/js/axios.js"></script>
    <script>
        const pathaoApi = axios.create({
            baseURL: '{{ url("/api/v1/delivery/pathao") }}',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        let del_vue = new Vue({
            el: "#delivery_status_form",
            data: ()=>({
                shipping_info: {!! json_encode($shippingInfo) !!},
                order_id: {{ $order->id }},
                order_total: {{ $order->total }},
                
                // Pathao data
                pathaoEnabled: false,
                cities: [],
                zones: [],
                areas: [],
                pathaoData: {
                    city_id: null,
                    zone_id: null,
                    area_id: null,
                    delivery_type: 48,
                    item_type: 3,
                    item_weight: 1,
                    delivery_cost: 0,
                },
                pathaoLoading: {
                    cities: false,
                    zones: false,
                    areas: false,
                    price: false,
                    create: false,
                },
                pathaoError: null,
                pathaoSuccess: null,
            }),
            computed: {
                isPathaoFormValid() {
                    return this.pathaoData.city_id && 
                           this.pathaoData.zone_id && 
                           this.pathaoData.area_id &&
                           this.pathaoData.item_weight > 0;
                }
            },
            watch: {
                pathaoEnabled(newVal) {
                    if (newVal && this.cities.length === 0) {
                        this.get_cities();
                    }
                }
            },
            mounted: async function(){
                // Initialize Select2 after Vue renders
                this.$nextTick(() => {
                    this.initSelect2();
                });
            },
            methods: {
                initSelect2() {
                    const that = this;
                    
                    $('.city_select2').select2({
                        placeholder: 'Select City',
                        allowClear: true
                    }).on('select2:select', function(e) {
                        const cityId = e.params.data.id;
                        that.pathaoData.city_id = cityId;
                        that.onCityChange();
                    });
                    
                    $('.zone_select2').select2({
                        placeholder: 'Select Zone',
                        allowClear: true
                    }).on('select2:select', function(e) {
                        const zoneId = e.params.data.id;
                        that.pathaoData.zone_id = zoneId;
                        that.onZoneChange();
                    });
                    
                    $('.area_select2').select2({
                        placeholder: 'Select Area',
                        allowClear: true
                    }).on('select2:select', function(e) {
                        const areaId = e.params.data.id;
                        that.pathaoData.area_id = areaId;
                        that.onAreaChange();
                    });
                },
                
                async get_cities(){
                    this.pathaoLoading.cities = true;
                    this.pathaoError = null;
                    try {
                        const res = await pathaoApi.get('cities');
                        if (res.data && res.data.data) {
                            this.cities = Array.isArray(res.data.data) ? res.data.data : [];
                            
                            // Auto-select city if shipping info matches
                            if (this.shipping_info && this.shipping_info.city) {
                                const selectedCity = this.cities.find(c => 
                                    c.city_name && c.city_name.toLowerCase().includes(this.shipping_info.city.toLowerCase())
                                );
                                if (selectedCity) {
                                    this.pathaoData.city_id = selectedCity.city_id;
                                    this.$nextTick(() => {
                                        $('.city_select2').val([selectedCity.city_id]).trigger('change');
                                        this.onCityChange();
                                    });
                                }
                            }
                        }
                    } catch (error) {
                        console.error('Error loading cities:', error);
                        this.pathaoError = 'Failed to load cities. Please try again.';
                    } finally {
                        this.pathaoLoading.cities = false;
                    }
                },
                
                async onCityChange(){
                    if (!this.pathaoData.city_id) {
                        this.zones = [];
                        this.areas = [];
                        this.pathaoData.zone_id = null;
                        this.pathaoData.area_id = null;
                        $('.zone_select2').val(null).trigger('change');
                        $('.area_select2').val(null).trigger('change');
                        return;
                    }
                    
                    this.pathaoLoading.zones = true;
                    this.pathaoError = null;
                    try {
                        const res = await pathaoApi.get(`zones/${this.pathaoData.city_id}`);
                        if (res.data && res.data.data) {
                            this.zones = Array.isArray(res.data.data) ? res.data.data : [];
                            this.$nextTick(() => {
                                $('.zone_select2').val(null).trigger('change');
                            });
                        }
                    } catch (error) {
                        console.error('Error loading zones:', error);
                        this.pathaoError = 'Failed to load zones. Please try again.';
                    } finally {
                        this.pathaoLoading.zones = false;
                    }
                },
                
                async onZoneChange(){
                    if (!this.pathaoData.zone_id) {
                        this.areas = [];
                        this.pathaoData.area_id = null;
                        $('.area_select2').val(null).trigger('change');
                        return;
                    }
                    
                    this.pathaoLoading.areas = true;
                    this.pathaoError = null;
                    try {
                        const res = await pathaoApi.get(`areas/${this.pathaoData.zone_id}`);
                        if (res.data && res.data.data) {
                            this.areas = Array.isArray(res.data.data) ? res.data.data : [];
                            this.$nextTick(() => {
                                $('.area_select2').val(null).trigger('change');
                            });
                        }
                    } catch (error) {
                        console.error('Error loading areas:', error);
                        this.pathaoError = 'Failed to load areas. Please try again.';
                    } finally {
                        this.pathaoLoading.areas = false;
                    }
                },
                
                async onAreaChange(){
                    if (this.pathaoData.area_id && this.pathaoData.item_weight) {
                        await this.calculateDeliveryCost();
                    }
                },
                
                async calculateDeliveryCost(){
                    if (!this.isPathaoFormValid) {
                        return;
                    }
                    
                    this.pathaoLoading.price = true;
                    this.pathaoError = null;
                    try {
                        const res = await pathaoApi.post('price-plan', {
                            item_type: this.pathaoData.item_type,
                            delivery_type: this.pathaoData.delivery_type,
                            item_weight: this.pathaoData.item_weight,
                            recipient_city: this.pathaoData.city_id,
                            recipient_zone: this.pathaoData.zone_id,
                        });
                        
                        if (res.data && res.data.data && res.data.data.standard_price) {
                            this.pathaoData.delivery_cost = parseFloat(res.data.data.standard_price);
                        } else {
                            this.pathaoError = 'Could not calculate delivery cost. Please check your inputs.';
                        }
                    } catch (error) {
                        console.error('Error calculating price:', error);
                        this.pathaoError = error.response?.data?.message || 'Failed to calculate delivery cost.';
                    } finally {
                        this.pathaoLoading.price = false;
                    }
                },
                
                async addToPathao(){
                    if (!this.isPathaoFormValid) {
                        this.pathaoError = 'Please fill all required fields.';
                        return;
                    }
                    
                    if (!this.shipping_info) {
                        this.pathaoError = 'Shipping information is missing.';
                        return;
                    }
                    
                    this.pathaoLoading.create = true;
                    this.pathaoError = null;
                    this.pathaoSuccess = null;
                    
                    try {
                        const orderData = {
                            recipient_name: this.shipping_info.full_name || 'Customer',
                            recipient_phone: this.shipping_info.phone || '',
                            recipient_address: this.shipping_info.address || '',
                            recipient_city: this.pathaoData.city_id,
                            recipient_zone: this.pathaoData.zone_id,
                            recipient_area: this.pathaoData.area_id,
                            delivery_type: this.pathaoData.delivery_type,
                            item_type: this.pathaoData.item_type,
                            item_quantity: 1,
                            item_weight: this.pathaoData.item_weight,
                            item_description: `Order #{{ $order->order_no }}`,
                            amount_to_collect: this.order_total,
                            special_instruction: 'Order ID: {{ $order->id }}',
                        };
                        
                        const res = await pathaoApi.post('create-order', orderData);
                        
                        if (res.data && res.data.type === 'success') {
                            this.pathaoSuccess = `Order added to Pathao successfully! Consignment ID: ${res.data.data?.consignment_id || 'N/A'}`;
                            // Optionally disable form after success
                            setTimeout(() => {
                                this.pathaoEnabled = false;
                            }, 3000);
                        } else {
                            this.pathaoError = res.data?.message || 'Failed to create Pathao order.';
                        }
                    } catch (error) {
                        console.error('Error creating Pathao order:', error);
                        this.pathaoError = error.response?.data?.message || 'Failed to create Pathao order. Please try again.';
                    } finally {
                        this.pathaoLoading.create = false;
                    }
                },
            }
        });
    </script>
    <script>
        function printPageArea(areaID) {
            var printContent = document.getElementById(areaID).innerHTML;
            var originalContent = document.body.innerHTML;
            document.body.innerHTML = printContent;
            window.print();
            document.body.innerHTML = originalContent;
        }
    </script>
    <script>
        document.querySelector('select[name="order_status"]').addEventListener('change', function() {
            const deliveryManGroup = document.getElementById('delivery-man-group');
            if (this.value == '2') { // 2 is the value for "Dispatch"
                deliveryManGroup.style.display = 'block';
            } else {
                deliveryManGroup.style.display = 'none';
            }
        });

        // Show delivery man field on page load if status is already dispatch
        document.addEventListener('DOMContentLoaded', function() {
            const orderStatus = document.querySelector('select[name="order_status"]').value;
            const deliveryManGroup = document.getElementById('delivery-man-group');
            const deliveryManSelect = document.querySelector('select[name="delivery_man_id"]');
            
            if (orderStatus == '2' || orderStatus == '3' || orderStatus == '4' || orderStatus == '5') { // 2 is Dispatch, 3 is Intransit, 4 is Delivered, 5 is Return
            deliveryManGroup.style.display = 'block';
            
            // Disable select for status 3, 4, or 5
            if (orderStatus == '3' || orderStatus == '4' || orderStatus == '5') {
                deliveryManSelect.disabled = true;
            }
            }
        });
        
        
    </script>
@endsection