<div class="table-responsive">
    <table class="table mb-0">
        <tbody>
            <tr>
                <th style="width: 30%; line-height: 36px;">Full Name<span class="text-danger">*</span></th>
                <td>
                    <input type="text" name="shipping_name" id="shipping_name" class="form-control"
                     placeholder="Full Name" >
                    <div class="invalid-feedback"><strong></strong></div>
                </td>
            </tr>
            <tr>
                <th style="width: 30%; line-height: 36px;">Customer Email</th>
                <td>
                    <input type="email" name="shipping_email" id="shipping_email" class="form-control" placeholder="Email">
                    <div class="invalid-feedback"><strong></strong></div>
                </td>
            </tr>
            <tr>
                <th style="width: 30%; line-height: 36px;">Customer Phone<span class="text-danger">*</span></th>
                <td>
                    <input type="text" name="shipping_phone" id="shipping_phone" class="form-control" placeholder="Phone No" >
                    <div class="invalid-feedback"><strong></strong></div>
                </td>
            </tr>
            <tr>
                <th style="width: 30%; line-height: 36px;">Customer Address</th>
                <td>
                    <input type="text" name="shipping_address" id="shipping_address" class="form-control" placeholder="Street No/House No/Area">
                    <div class="invalid-feedback"><strong></strong></div>
                </td>
            </tr>
            <tr>
                <th style="width: 30%; line-height: 36px;">Shipping City</th>
                <td>
                    <select class="form-control" name="shipping_district_id" id="shipping_district_id" data-toggle="select2" >
                        <option value="">Select One</option>
                        @foreach($districts as $district)
                        <option value="{{$district->id}}">{{$district->name}}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback"><strong></strong></div>
                </td>
            </tr>
            <tr>
                <th style="width: 30%; line-height: 36px;">Sub-District/State</th>
                <td>
                    <select name="shipping_thana_id" data-toggle="select2" id="shipping_thana_id" >
                        <option value="">Select One</option>
                    </select>
                    <div class="invalid-feedback"><strong></strong></div>
                </td>
            </tr>
            <tr>
                <th style="width: 30%; line-height: 36px;">Post Code</th>
                <td>
                    <input type="text" name="shipping_postal_code" id="shipping_postal_code" class="form-control" placeholder="Post Code">
                    <div class="invalid-feedback"><strong></strong></div>
                </td>
            </tr>

            <tr>
                <th style="width: 30%; line-height: 36px;">Reference Code</th>
                <td>
                    <input type="text" name="reference_code" id="reference_code" class="form-control" placeholder="Reference Code">
                    <div class="invalid-feedback"><strong></strong></div>
                </td>
            </tr>

            {{-- <tr>
                <th style="width: 30%; line-height: 36px;">Warehouse</th>
                <td>
                    <select class="form-control" name="purchase_product_warehouse_id" id="purchase_product_warehouse_id" >
                        <option value="">Select One</option>
                        @foreach($warehouses as $warehouse)
                        <option value="{{$warehouse->id}}">{{$warehouse->title}}</option>
                        @endforeach
                    </select>
                </td>
            </tr>

            <tr>
                <th style="width: 30%; line-height: 36px;">Warehouse Room</th>
                <td>
                    <select class="form-control" name="purchase_product_warehouse_room_id" id="purchase_product_warehouse_room_id"  >
                        <option value="">Select One</option>
                        @foreach($warehouse_rooms as $room)
                        <option value="{{$room->id}}">{{$room->title}}</option>
                        @endforeach
                    </select>
                </td>
            </tr>

            <tr>
                <th style="width: 30%; line-height: 36px;">Warehouse Room Cartoon</th>
                <td>
                    <select class="form-control" name="purchase_product_warehouse_room_cartoon_id" id="purchase_product_warehouse_room_cartoon_id" >
                        <option value="">Select One</option>
                        @foreach($room_cartoons as $cartoon)
                        <option value="{{$cartoon->id}}">{{$cartoon->title}}</option>
                        @endforeach
                    </select>
                </td>
            </tr> --}}

            <tr>
                <th style="width: 30%; line-height: 36px;">Customer Source Type</th>
                <td>
                    <select class="form-control" name="customer_source_type_id" id="customer_source_type_id" >
                        <option value="">Select One</option>
                        @foreach($customer_source_types as $customer_source_type)
                        <option value="{{$customer_source_type->id}}">{{$customer_source_type->title}}</option>
                        @endforeach
                    </select>
                </td>
            </tr>

            <tr>
                <th style="width: 30%; line-height: 36px;">Outlet</th>
                <td>
                    <select class="form-control" name="outlet_id" id="outlet_id" >
                        <option value="">Select One</option>
                        @foreach($outlets as $outlet)
                        <option value="{{$outlet->id}}">{{$outlet->title}}</option>
                        @endforeach
                    </select>
                </td>
            </tr>


        </tbody>
    </table>
</div>
