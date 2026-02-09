@extends('backend.master')

@section('page_title')
    Orders
@endsection

@section('page_heading')
    Edit Order Information
@endsection

@section('header_css')
    <link href="{{url('assets')}}/plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />
    <style>
        .shipping_input{
            font-size: 13px;
            height: 32px;
            box-shadow: inset 1px 1px 2px #b9b9b9;
            display: inline-block;
        }
        table tbody tr td{
            padding: 5px 10px !important
        }
        table thead tr th{
            padding: 5px 10px !important
        }
        .select2-selection{
            height: 34px !important;
            border: 1px solid #ced4da !important;
        }
        .select2 {
            width: 100% !important;
        }

        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">

            <form action="{{url('order/update')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="order_id" value="{{$order->id}}">
                <div class="card">
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
                                <h4 class="m-0">Voucher</h4>
                            </div>
                        </div>


                        <div class="row mt-4">
                            <div class="col-lg-3">

                                <h6 class="font-weight-bold">Shipping Info :</h6>
                                <address class="line-h-24 shipping_address_fields">
                                    <input type="text" name="shipping_name" value="@if($shippingInfo){{$shippingInfo->full_name}}@endif" @if((isset($shippingInfo) && $shippingInfo->full_name == '') || !isset($shippingInfo)) style="border: 1px solid #b500008c;" @endif placeholder="Customer Name" class="form-control shipping_input mb-2" required>
                                    <input type="text" name="shipping_phone" value="@if($shippingInfo){{$shippingInfo->phone}}@endif" @if((isset($shippingInfo) && $shippingInfo->phone == '') || !isset($shippingInfo)) style="border: 1px solid #b500008c;" @endif placeholder="Customer Phone" class="form-control shipping_input mb-2" required>
                                    <input type="text" name="shipping_email" value="@if($shippingInfo){{$shippingInfo->email}}@endif" @if((isset($shippingInfo) && $shippingInfo->email == '') || !isset($shippingInfo)) style="border: 1px solid #b500008c;" @endif placeholder="Customer Email" class="form-control shipping_input mb-2">
                                    <input type="text" name="shipping_address" value="@if($shippingInfo){{$shippingInfo->address}}@endif" @if((isset($shippingInfo) && $shippingInfo->address == '') || !isset($shippingInfo)) style="border: 1px solid #b500008c;" @endif placeholder="Customer Address" class="form-control shipping_input mb-2" required>


                                    @if((isset($shippingInfo) && $shippingInfo->city == '') || !isset($shippingInfo))
                                    <style>
                                        .shipping_address_fields .select2-container{
                                            border: 1px solid #b500008c !important;
                                            border-radius: 6px;
                                            margin-bottom: 8px;
                                        }
                                    </style>
                                    @else
                                    <style>
                                        .shipping_address_fields .select2-container{
                                            margin-bottom: 8px;
                                        }
                                    </style>
                                    @endif

                                    <select class="form-control" name="shipping_thana" data-toggle="select2" required>
                                        <option value="">Select Shipping thana</option>
                                        @foreach ($upazilas as $upazila)
                                        <option value="{{$upazila->name}}" @if($shippingInfo && $shippingInfo->thana == $upazila->name) selected @endif>{{$upazila->name}}</option>
                                        @endforeach
                                    </select>

                                    <select class="form-control" name="shipping_city" data-toggle="select2" required>
                                        <option value="">Select Shipping City</option>
                                        @foreach ($districts as $district)
                                        <option value="{{$district->name}}" @if($shippingInfo && $shippingInfo->city == $district->name) selected @endif>{{$district->name}}</option>
                                        @endforeach
                                    </select>

                                    <input type="text" name="shipping_post_code" value="@if($shippingInfo){{$shippingInfo->post_code}}@endif" @if((isset($shippingInfo) && $shippingInfo->post_code == '') || !isset($shippingInfo)) style="border: 1px solid #b500008c; width: 38.4%;" @else style="width: 38.4%;" @endif placeholder="Post Code" class="form-control shipping_input mb-2">
                                    <input type="text" name="shipping_country" value="@if($shippingInfo){{$shippingInfo->country}}@endif" @if((isset($shippingInfo) && $shippingInfo->country == '') || !isset($shippingInfo)) style="border: 1px solid #b500008c; width: 60%;" @else style="width: 60%;" @endif placeholder="Country Name" class="form-control shipping_input">

                                </address>

                            </div><!-- end col -->
                            <div class="col-lg-9">
                                <div class="mt-3 float-right">
                                    <p class="mb-2"><strong>Order NO: </strong> #{{$order->order_no}}</p>
                                    <p class="mb-2"><strong>Tran. ID: </strong> #{{$order->trx_id}}</p>
                                    <p class="mb-2"><strong>Order Date: </strong> {{date("jS F, Y",strtotime($order->order_date))}}</p>
                                    <p class="mb-2"><strong>Order Status: </strong>
                                        @php
                                            if($order->order_status == 0){
                                                echo '<span class="badge badge-soft-warning" style="padding: 2px 10px !important;">Pending</span>';
                                            } elseif($order->order_status == 1) {
                                                echo '<span class="badge badge-soft-info" style="padding: 2px 10px !important;">Approved</span>';
                                            } elseif($order->order_status == 2) {
                                                echo '<span class="badge badge-soft-info" style="padding: 2px 10px !important;">Intransit</span>';
                                            } elseif($order->order_status == 3) {
                                                echo '<span class="badge badge-soft-success" style="padding: 2px 10px !important;">Delivered</span>';
                                            } else {
                                                echo '<span class="badge badge-soft-danger" style="padding: 2px 10px !important;">Cancelled</span>';
                                            }
                                        @endphp
                                    </p>
                                    <p class="mb-2"><strong>Delivery Method: </strong>
                                        @php
                                            if($order->delivery_method == 1){
                                                echo '<span class="badge badge-soft-success" style="padding: 3px 5px !important;">Home Delivery</span>';
                                            }
                                            if($order->delivery_method == 2){
                                                echo '<span class="badge badge-soft-success" style="padding: 3px 5px !important;">Store Pickup</span>';
                                            }
                                        @endphp
                                    </p>
                                    <p class="mb-2"><strong>Payment Method: </strong>
                                        @php
                                            if($order->payment_method == NULL){
                                                echo '<span class="badge badge-soft-danger" style="padding: 2px 10px !important;">Unpaid</span>';
                                            } elseif($order->payment_method == 1) {
                                                echo '<span class="badge badge-soft-info" style="padding: 2px 10px !important;">COD</span>';
                                            } elseif($order->payment_method == 2) {
                                                echo '<span class="badge badge-soft-success" style="padding: 2px 10px !important;">bKash</span>';
                                            } elseif($order->payment_method == 3) {
                                                echo '<span class="badge badge-soft-success" style="padding: 2px 10px !important;">Nagad</span>';
                                            } else {
                                                echo '<span class="badge badge-soft-success" style="padding: 2px 10px !important;">Card</span>';
                                            }
                                        @endphp
                                    </p>
                                    <p class="m-b-10"><strong>Payment Status: </strong>
                                        @php
                                            if($order->payment_status == 0){
                                                echo '<span class="badge badge-soft-warning" style="padding: 2px 10px !important;">Unpaid</span>';
                                            } elseif($order->payment_status == 1) {
                                                echo '<span class="badge badge-soft-success" style="padding: 2px 10px !important;">Success</span>';
                                            } else {
                                                echo '<span class="badge badge-soft-danger" style="padding: 2px 10px !important;">Failed</span>';
                                            }
                                        @endphp
                                    </p>

                                    @if($order->payment_method == NULL)
                                    <p class="m-b-10">
                                        <select class="form-control" name="payment_method" style="cursor: pointer; border: 1px solid #a60000b2;">
                                            <option value="">Change Payment Method</option>
                                            <option value="1">Cash On Delivery</option>
                                        </select>
                                    </p>
                                    @endif

                                </div>
                            </div><!-- end col -->
                        </div>
                        <!-- end row -->

                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table mt-4 table-sm table-bordered" id="orderDetailsTable">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width: 60px;">SL</th>
                                                <th>Item</th>
                                                <th class="text-center">Variant</th>
                                                <th class="text-center" style="width: 180px;">Quantity</th>
                                                <th class="text-center" style="width: 150px;">Unit Cost</th>
                                                <th class="text-right" style="width: 150px;">Total</th>
                                                <th class="text-center" style="width: 75px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $sl = 1;
                                            @endphp
                                            @foreach ($orderDetails as $details)
                                            @php
                                                if($details->color_id)
                                                    $colorInfo = App\Models\Color::where('id', $details->color_id)->first();
                                                if($details->size_id)
                                                    $sizeInfo = App\Models\ProductSize::where('id', $details->size_id)->first();
                                                if($details->storage_id)
                                                    $storageInfo = App\Models\StorageType::where('id', $details->storage_id)->first();
                                                if($details->sim_id)
                                                    $simInfo = App\Models\Sim::where('id', $details->sim_id)->first();
                                                if($details->region_id)
                                                    $regionInfo = DB::table('country')->where('id', $details->region_id)->first();
                                                if($details->warrenty_id)
                                                    $warrentyInfo = App\Models\ProductWarrenty::where('id', $details->warrenty_id)->first();
                                                if($details->device_condition_id)
                                                    $deviceCondition = App\Models\DeviceCondition::where('id', $details->device_condition_id)->first();
                                            @endphp
                                            <tr>
                                                <td class="text-center" style="vertical-align: middle;">
                                                    <strong>{{$sl}}</strong>
                                                    <input type="hidden" name="order_details_id[]" value="{{$details->id}}">
                                                </td>
                                                <td style="vertical-align: middle;">
                                                    <b>{{$details->product_name}}</b>
                                                    <input type="hidden" name="product_id[]" value="{{$details->product_id}}">
                                                </td>

                                                <td class="text-center" style="vertical-align: middle;">
                                                    @if($details->color_id) Color: {{$colorInfo?->name}} | @endif
                                                    @if($details->size_id) Size: {{$sizeInfo?->name}} | @endif
                                                    @if($details->storage_id) Storage: {{$storageInfo?->ram}}/{{$storageInfo?->rom}} | @endif
                                                    @if($details->sim_id) SIM: {{$simInfo?->name}} @endif

                                                    <br>
                                                    @if($details->region_id) Region: {{$regionInfo->name}} | @endif
                                                    @if($details->warrenty_id) Warrenty: {{$warrentyInfo->name}} | @endif
                                                    @if($details->device_condition_id) Condition: {{$deviceCondition->name}} @endif

                                                    <input type="hidden" id="color_id_{{$sl}}" name="color_id[]" value="{{$details->color_id}}">
                                                    <input type="hidden" id="size_id_{{$sl}}" name="size_id[]" value="{{$details->size_id}}">
                                                    <input type="hidden" id="storage_id_{{$sl}}" name="storage_id[]" value="{{$details->storage_id}}">
                                                    <input type="hidden" id="sim_id_{{$sl}}" name="sim_id[]" value="{{$details->sim_id}}">
                                                    <input type="hidden" id="region_id_{{$sl}}" name="region_id[]" value="{{$details->region_id}}">
                                                    <input type="hidden" id="warrenty_id_{{$sl}}" name="warrenty_id[]" value="{{$details->warrenty_id}}">
                                                    <input type="hidden" id="device_condition_id_{{$sl}}" name="device_condition_id[]" value="{{$details->device_condition_id}}">
                                                </td>

                                                <td class="text-center" style="vertical-align: middle;">
                                                    <input class="form-control d-inline-block w-50" type="number" onwheel="this.blur()" onkeyup="changeTotalPrice({{$sl}})" min="1" name="qty[]" id="qty_{{$sl}}" value="{{$details->qty}}" required> {{$details->unit_name}}
                                                    <input type="hidden" name="unit_id[]" value="{{$details->unit_id}}">
                                                </td>
                                                <td class="text-center" style="vertical-align: middle;">
                                                    ৳ <input class="form-control d-inline-block w-75" type="text" name="unit_price[]" id="unit_price_{{$sl}}" value="{{$details->unit_price}}" readonly required>
                                                </td>
                                                <td class="text-right" style="vertical-align: middle;">
                                                    ৳ <input class="form-control d-inline-block w-75 orderT" type="text" name="total_price[]" id="total_price_{{$sl}}" value="{{$details->total_price}}" readonly required>
                                                </td>
                                                <td class="text-center" style="vertical-align: middle;">
                                                    <a href="javascript:void(0)" onclick="removeRow(this)" class="btn btn-danger rounded btn-sm d-inline text-white"><i class="feather-trash-2" style="font-size: 14px; line-height: 2"></i></a>
                                                </td>
                                            </tr>
                                            @php
                                                $sl++;
                                            @endphp
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="row pb-3">
                                        <div class="col-lg-12 text-right">
                                            <button type="button" class="btn btn-success btn-sm rounded addAnotherItem" onclick="addAnotherProduct()"><strong>+ Add More Product</strong></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-3">
                                <div class="clearfix pt-3">
                                    <h6 class="text-muted">Billing Address:</h6>
                                    <address class="line-h-24 billing_address_fields">
                                        <input type="text" name="billing_address" value="@if($billingAddress){{$billingAddress->address}}@endif" @if((isset($billingAddress) && $billingAddress->address == '') || !isset($billingAddress)) style="border: 1px solid #b500008c;" @endif placeholder="Billing Address" class="form-control shipping_input mb-2">

                                        @if((isset($billingAddress) && $billingAddress->city == '') || !isset($billingAddress))
                                        <style>
                                            .billing_address_fields .select2-container{
                                                border: 1px solid #b500008c !important;
                                                border-radius: 6px;
                                                margin-bottom: 8px;
                                            }
                                        </style>
                                        @else
                                        <style>
                                            .billing_address_fields .select2-container{
                                                margin-bottom: 8px;
                                            }
                                        </style>
                                        @endif

                                          <select class="form-control" name="billing_thana" data-toggle="select2" required>
                                        <option value="">Select Shipping thana</option>
                                        @foreach ($upazilas as $upazila)
                                        <option value="{{$upazila->name}}" @if($shippingInfo && $shippingInfo->thana == $upazila->name) selected @endif>{{$upazila->name}}</option>
                                        @endforeach
                                    </select>

                                        <select class="form-control" name="billing_city" data-toggle="select2" required>
                                            <option value="">Select Billing City</option>
                                            @foreach ($districts as $district)
                                            <option value="{{$district->name}}" @if($billingAddress && $billingAddress->city == $district->name) selected @endif>{{$district->name}}</option>
                                            @endforeach
                                        </select>


                                        <input type="text" name="billing_post_code" value="@if($billingAddress){{$billingAddress->post_code}}@endif" @if((isset($billingAddress) && $billingAddress->post_code == '') || !isset($billingAddress)) style="border: 1px solid #b500008c; width: 38.4%;" @else style="width: 38.4%;" @endif placeholder="Post Code" class="form-control shipping_input mb-2">
                                        <input type="text" name="billing_country" value="@if($billingAddress){{$billingAddress->country}}@endif" @if((isset($billingAddress) && $billingAddress->country == '') || !isset($billingAddress)) style="border: 1px solid #b500008c;" @else style="width: 60%;" @endif placeholder="Country Name" class="form-control shipping_input">
                                    </address>
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

                            </div>
                            <div class="col-lg-9 pt-3 text-right">
                                <div class="float-right">
                                    <p><b>Sub-total :</b> ৳ <span id="order_sub_total">{{number_format($order->sub_total, 2)}}</span></p>
                                    <p><b>Discount @if($order->coupon_code)({{$order->coupon_code}})@endif:</b> ৳ <span id="order_discount">{{number_format($order->discount, 2)}}</span></p>
                                    <p><b>VAT/TAX :</b> ৳ <span id="order_vat_tax">{{number_format($order->vat+$order->tax, 2)}}</span></p>
                                    <p><b>Delivery Charge :</b> ৳ <span id="order_delivery_charge">{{number_format($order->delivery_fee, 2)}}</span></p>
                                    <h3><b>Total Order Amount :</b> ৳ <span id="order_total">{{number_format($order->total, 2)}}</span></h3>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>

                        <div class="mt-4 mb-4">
                            <div class="text-right">
                                <button type="submit" class="btn btn-primary waves-effect waves-light"><i class="fas fa-save"></i> Update Order</button>
                                <a href="{{url('/order/details')}}/{{$order->slug}}" class="btn btn-danger waves-effect waves-light"><i class="mdi mdi-cancel"></i> Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="card p-3">
                <form action="{{url('order/info/update')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="order_id" value="{{$order->id}}">
                    <div class="row">
                        <div class="col-lg-10">
                            <div class="form-group" style="margin-bottom: 0px">
                                <label style="margin-bottom: .2rem; font-weight: 500;">Special Note For Order (Visible by Admin Only) :</label>
                                <textarea name="order_remarks" class="form-control" style="height: 149px !important;" placeholder="Special Note By Admin">{{$order->order_remarks}}</textarea>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="form-group" style="margin-bottom: .5rem;">
                                <label style="margin-bottom: .2rem; font-weight: 500;">Est. Delivery Date :</label>
                                <input type="date" class="form-control" name="estimated_dd" value="{{$order->estimated_dd}}" required>
                            </div>

                            <div class="form-group" style="margin-bottom: .5rem;">
                                <label style="margin-bottom: .2rem; font-weight: 500;">Order Status :</label>
                                <select name="order_status" class="form-control" required>
                                    <option value="">Change Status</option>
                                    <option value="0" @if($order->order_status == 0) selected @endif @if($order->order_status == 1 || $order->order_status == 2 || $order->order_status == 3 || $order->order_status == 4) disabled @endif>Pending</option>
                                    <option value="1" @if($order->order_status == 1) selected @endif @if($order->order_status == 2 || $order->order_status == 3 || $order->order_status == 4) disabled @endif>Approved</option>
                                    <option value="2" @if($order->order_status == 2) selected @endif @if($order->order_status == 0 || $order->order_status == 3 || $order->order_status == 4) disabled @endif>Intransit</option>
                                    <option value="3" @if($order->order_status == 3) selected @endif @if($order->order_status == 1 || $order->order_status == 0 || $order->order_status == 4) disabled @endif>Delivered</option>
                                    <option value="4" @if($order->order_status == 4) selected @endif @if($order->order_status == 2 || $order->order_status == 3) disabled @endif>Cancel</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success rounded w-100 mt-1">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>

        </div> <!-- end col -->
    </div>
@endsection

@section('footer_js')
    <script src="{{url('assets')}}/plugins/select2/select2.min.js"></script>
    <script>
        $('[data-toggle="select2"]').select2();

        function fixRowNo(params) {
            var renum = 1;
            $("tr td strong").each(function() {
                $(this).text(renum);
                renum++;
            });
        }

        function removeRow(btndel){
            if (typeof(btndel) == "object") {
                $(btndel).closest("tr").remove();
            } else {
                return false;
            }
            fixRowNo();
            orderAmountCalculation();
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function addAnotherProduct(){
            $(".addAnotherItem").html("Adding...");
            var renum = 1;
            $("tr td strong").each(function() {
                renum++;
            });

            $.ajax({
                data: {
                    rowno: renum,
                    _token: '{{csrf_token()}}'
                },
                url: "{{ url('/add/more/product') }}",
                type: "POST",
                dataType: 'json',
                success: function(data) {
                    $(".addAnotherItem").html("Added");
                    $("#orderDetailsTable tbody").append(data.more);
                    fixRowNo();
                    $(".addAnotherItem").html("<strong>+ Add More Product</strong>");
                    $('[data-toggle="select2"]').select2(); // initiate select2 for newly added row
                },
                error: function(data) {
                    console.log('Error:', data);
                    $(".addAnotherItem").html("Something Went Wrong");
                }
            });
        }

        function getProductVariants(value){
            var productId = value;
            var renum = 0;
            $("tr td strong").each(function() {
                renum++;
            });
            $("#product_variant_id_"+renum).html('');

            $.ajax({
                url: "{{url('/get/product/variants')}}",
                type: "POST",
                data: {
                    product_id: productId,
                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function (result) {

                    $('#product_variant_id_'+renum).html('<option value="">Select Variant</option>');

                    if(Array.isArray(result)){
                        console.log("Has Variant");

                        $.each(result, function (key, value) {
                            $optionStr = "("+value.variant_stock+") ";
                            if(value.color_name)
                                $optionStr += value.color_name;
                            if(value.size_name)
                                $optionStr += " | "+value.size_name;
                            if(value.ram && value.rom)
                                $optionStr += " | "+value.ram+'/'+value.rom;
                            if(value.region_name)
                                $optionStr += " | "+value.region_name;
                            if(value.sim_name)
                                $optionStr += " | "+value.sim_name;
                            if(value.device_condition)
                                $optionStr += " | "+value.device_condition;
                            if(value.warrrenty)
                                $optionStr += " | "+value.warrrenty;

                            var price = 0;
                            if(value.discounted_price > 0)
                                price = Number(value.discounted_price);
                            else
                                price = Number(value.price);

                            $("#product_variant_id_"+renum).append('<option value="' + value.id + '" color_id="'+value.color_id+'" size_id="'+value.size_id+'" storage_id="'+value.storage_type_id+'" region_id="'+value.region_id+'" sim_id="'+value.sim_id+'" warrenty_id="'+value.warrenty_id+'" device_condition_id="'+value.device_condition_id+'" price="'+price+'" unit_name="'+value.unit_name+'" unit_id="'+value.unit_id+'" stock="'+value.variant_stock+'">' + $optionStr + '</option>');
                        });

                        orderAmountCalculation();

                    } else {
                        console.log("Dont Have any Variant");
                        $('#product_variant_id_'+renum).html('<option value="">Dont Have Any Variant</option>');

                        if(result.stock <= 0){
                            toastr.error("Stock Out");
                            $("#unit_text_"+renum).html(result.unit_name);
                            $("#unit_"+renum).val(result.unit_id);
                            $("#qty_"+renum).attr("max", result.stock);
                            $("#qty_"+renum).val("");
                            $("#unit_price_"+renum).val("");
                            $("#total_price_"+renum).val("");
                            return false;
                        }

                        var price = 0;
                        if(result.discount_price > 0)
                            price = Number(result.discount_price);
                        else
                            price = Number(result.price);

                        $("#unit_text_"+renum).html(result.unit_name);
                        $("#unit_"+renum).val(result.unit_id);
                        $("#qty_"+renum).attr("max", result.stock);
                        $("#qty_"+renum).val(1);
                        $("#unit_price_"+renum).val(price);
                        $("#total_price_"+renum).val(price);

                        // variant attributes will be null
                        $("#color_id_"+renum).val("");
                        $("#storage_id_"+renum).val("");
                        $("#region_id_"+renum).val("");
                        $("#sim_id_"+renum).val("");
                        $("#warrenty_id_"+renum).val("");
                        $("#device_condition_id_"+renum).val("");

                        orderAmountCalculation();
                    }
                }
            });
        }

        function productVariantInfo(id){
            var price = $('#product_variant_id_'+id+' option:selected').attr('price');
            var unit_name = $('#product_variant_id_'+id+' option:selected').attr('unit_name');
            var unit_id = $('#product_variant_id_'+id+' option:selected').attr('unit_id');
            var stock = $('#product_variant_id_'+id+' option:selected').attr('stock');

            $("#unit_text_"+id).html(unit_name);
            $("#unit_"+id).val(unit_id);
            $("#qty_"+id).attr("max", stock);
            $("#qty_"+id).val(1);
            $("#unit_price_"+id).val(price);
            $("#total_price_"+id).val(price);

            var color_id = $('#product_variant_id_'+id+' option:selected').attr('color_id');
            var size_id = $('#product_variant_id_'+id+' option:selected').attr('size_id');
            var storage_id = $('#product_variant_id_'+id+' option:selected').attr('storage_id');
            var region_id = $('#product_variant_id_'+id+' option:selected').attr('region_id');
            var sim_id = $('#product_variant_id_'+id+' option:selected').attr('sim_id');
            var warrenty_id = $('#product_variant_id_'+id+' option:selected').attr('warrenty_id');
            var device_condition_id = $('#product_variant_id_'+id+' option:selected').attr('device_condition_id');

            $("#color_id_"+id).val(color_id);
            $("#size_id_"+id).val(size_id);
            $("#storage_id_"+id).val(storage_id);
            $("#region_id_"+id).val(region_id);
            $("#sim_id_"+id).val(sim_id);
            $("#warrenty_id_"+id).val(warrenty_id);
            $("#device_condition_id_"+id).val(device_condition_id);

            orderAmountCalculation();
        }

        function changeTotalPrice(id){
            var unitPrice = $("#unit_price_"+id).val();
            var qty = $("#qty_"+id).val();
            $("#total_price_"+id).val(unitPrice*qty);
            orderAmountCalculation();
        }

        function orderAmountCalculation(){
            var orderSubtotal = 0;
            $(".orderT").each(function() {
                orderSubtotal = Number(orderSubtotal) + Number($(this).val());
            });
            var orderDiscount = Number($("#order_discount").html());
            var orderDeliveryCharge = Number($("#order_delivery_charge").html());
            $("#order_sub_total").html(orderSubtotal.toFixed(2))
            var totalAmount = Number(orderSubtotal-orderDiscount+orderDeliveryCharge);
            $("#order_total").html(totalAmount.toFixed(2))
        }
    </script>
@endsection
