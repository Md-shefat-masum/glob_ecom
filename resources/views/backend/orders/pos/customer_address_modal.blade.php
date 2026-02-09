<div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{url('save/pos/customer/address')}}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        Add Address
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-left">

                    <div class="form-group">
                        <label>Select Customer</label>
                        <select class="form-control w-100" name="customer_id" data-toggle="select2">
                            <option value="">Select One</option>
                            @foreach($customers as $customer)
                            <option value="{{$customer->id}}">{{$customer->name}} (@if($customer->email){{$customer->email}}@else{{$customer->phone}}@endif)</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"><strong></strong></div>
                    </div>

                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Full Name"/>
                        <div class="invalid-feedback"><strong></strong></div>
                    </div>

                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="tel" class="form-control" name="phone" placeholder="Phone Number"/>
                        <div class="invalid-feedback"><strong></strong></div>
                    </div>

                    <div class="form-group">
                        <label>Address</label>
                        <input type="text" name="address" class="form-control" placeholder="Address"/>
                        <div class="invalid-feedback"><strong></strong></div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="customer_address_district_id">City</label>
                                <select class="form-control" name="customer_address_district_id" id="customer_address_district_id" data-toggle="select2">
                                    <option value="">Select One</option>
                                    @foreach($districts as $district)
                                    <option value="{{$district->name}}">{{$district->name}}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"><strong></strong></div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="customer_address_thana_id">Sub-District/State</label>
                                <select name="customer_address_thana_id" data-toggle="select2" id="customer_address_thana_id">
                                    <option value="">Select One</option>
                                </select>
                                <div class="invalid-feedback"><strong></strong></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Post Code</label>
                        <input type="text" name="post_code" class="form-control" placeholder="ex. 25663"/>
                        <div class="invalid-feedback"><strong></strong></div>
                    </div>

                    <div class="form-group">
                        <label>Set address for</label>
                        <div class="set-address">
                            <div class="single-set-address">
                                <input type="radio" class="btn-check" id="btncheck1" name="address_type" value="Home" autocomplete="off" />
                                <label class="btn btn-outline-primary" for="btncheck1">Home Address</label>
                            </div>
                            <div class="single-set-address">
                                <input type="radio" class="btn-check" id="btncheck2" name="address_type" value="Office" autocomplete="off" />
                                <label class="btn btn-outline-primary" for="btncheck2">Office Address</label>
                            </div>
                        </div>
                        <div class="invalid-feedback"><strong></strong></div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
