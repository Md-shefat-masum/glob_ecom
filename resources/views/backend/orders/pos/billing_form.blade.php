<label style="margin-left: 20px; cursor: pointer">
    <input class="form-check-input" type="checkbox" id="flexCheckChecked" onchange="sameShippingBilling()" value="" />
    Same as Shipping Address
</label>
<div class="table-responsive">
    <table class="table mb-0">
        <tbody>
            <tr>
                <th style="width: 30%; line-height: 36px;">Billing Address</th>
                <td>
                    <input type="text" name="billing_address" id="billing_address" class="form-control"
                        placeholder="Street No/House No/Area">
                    <div class="invalid-feedback"><strong></strong></div>
                </td>
            </tr>
            <tr>
                <th style="width: 30%; line-height: 36px;">Billing City</th>
                <td>
                    <select class="form-control" name="billing_district_id" id="billing_district_id"
                        data-toggle="select2">
                        <option value="">Select One</option>
                        @foreach ($districts as $district)
                            <option value="{{ $district->id }}">{{ $district->name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback"><strong></strong></div>
                </td>
            </tr>
            <tr>
                <th style="width: 30%; line-height: 36px;">Sub-District/State</th>
                <td>
                    <select name="billing_thana_id" data-toggle="select2" id="billing_thana_id">
                        <option value="">Select One</option>
                    </select>
                    <div class="invalid-feedback"><strong></strong></div>
                </td>
            </tr>
            <tr>
                <th style="width: 30%; line-height: 36px;">Post Code</th>
                <td>
                    <input type="text" name="billing_postal_code" id="billing_postal_code" class="form-control"
                        placeholder="Post Code">
                    <div class="invalid-feedback"><strong></strong></div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
