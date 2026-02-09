<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="customerCreateForm" action="{{ url('save/new/customer') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        Add New Customer
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-left">

                    <div class="form-group">
                        <label for="customer_name">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="customer_name" id="customer_name" class="form-control"
                            placeholder="Full Name" />
                        <div class="invalid-feedback">
                            <strong></strong>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="customer_phone">Phone Number <span class="text-danger">*</span></label>
                        <input type="tel" name="customer_phone" id="customer_phone" class="form-control"
                            placeholder="Phone Number" />
                        <div class="invalid-feedback">
                            <strong></strong>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="customer_email">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="customer_email" id="customer_email" class="form-control"
                            placeholder="Email Address" />
                        <div class="invalid-feedback">
                            <strong></strong>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="customer_password">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" id="customer_password" class="form-control"/>
                        <div class="invalid-feedback">
                            <strong></strong>
                        </div>
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
