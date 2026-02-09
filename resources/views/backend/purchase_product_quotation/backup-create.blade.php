@extends('backend.master')

@section('header_css')
    <link href="{{ url('assets') }}/plugins/dropify/dropify.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('assets') }}/plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('assets') }}/css/tagsinput.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
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

    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.7.15/vue.min.js"></script> --}}
    <script src="{{ asset('assets/js/vue.min.js') }}"></script>
    <script src="{{ asset('assets/js/purchase_vue.js') }}"></script>
    
@endsection


@section('page_title')
    Purchase Product Quotation
@endsection
@section('page_heading')
    Add a Purchase Product Quotation
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body" id="formApp">
                    <h4 class="card-title mb-3">Purchase Product Quotation @{{ name }}</h4>

                    {{-- <form class="needs-validation" method="POST" action="{{ url('save/new/purchase-product/quotation') }}"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-lg-12">

                                <div class="row">


                                    <div class="col-lg-9">

                                        <div class="form-group">
                                            <label for="product_warehouse_id">Warehouse <span
                                                    class="text-danger">*</span></label>
                                            <select id="purchase_product_warehouse_id" name="product_warehouse_id"
                                                class="form-control">
                                                <option value="" disabled selected>Select Warehouse</option>
                                                @foreach ($productWarehouses as $warehouse)
                                                    <option value="{{ $warehouse->id }}"
                                                        {{ old('product_warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                                        {{ $warehouse->title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('product_warehouse_id')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="product_warehouse_room_id">Warehouse Room<span
                                                    class="text-danger">*</span></label>
                                            <select id="purchase_product_warehouse_room_id" name="product_warehouse_room_id"
                                                class="form-control">
                                                <option value="" disabled selected>Select Warehouse Room</option>
                                                @foreach ($productWarehouseRooms as $room)
                                                    <option value="{{ $room->id }}"
                                                        {{ old('product_warehouse_room_id') == $room->id ? 'selected' : '' }}>
                                                        {{ $room->title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('product_warehouse_room_id')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="product_warehouse_room_cartoon_id">Warehouse Room Cartoon<span
                                                    class="text-danger">*</span></label>
                                            <select id="purchase_product_warehouse_room_cartoon_id"
                                                name="product_warehouse_room_cartoon_id" class="form-control">
                                                <option value="" disabled selected>Select Warehouse Room Cartoon
                                                </option>
                                            </select>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('product_warehouse_room_cartoon_id')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>



                                        <div class="form-group">
                                            <label for="title">Title <span class="text-danger">*</span></label>
                                            <input type="text" id="title" name="title" maxlength="255"
                                                class="form-control" placeholder="Enter Product Warehouse Title Here">
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('title')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="description">Description</label>
                                            <textarea id="description" name="description" class="form-control" placeholder="Enter Description Here"></textarea>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('description')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>


                        <div class="form-group text-center pt-3">
                            <a href="{{ url('view/all/product-warehouse-room-cartoon') }}" style="width: 130px;"
                                class="btn btn-danger d-inline-block text-white m-2" type="submit"><i
                                    class="mdi mdi-cancel"></i> Cancel</a>
                            <button class="btn btn-primary m-2" style="width: 130px;" type="submit"><i
                                    class="fas fa-save"></i> Save </button>
                        </div>

                    </form> --}}

                    <form action="{{ url('save/new/purchase-product/quotation') }}"  method="POST">
                        @csrf

                        <!-- Warehouse and Supplier Selection -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="purchase_product_warehouse_id">Warehouse</label>
                                <select id="purchase_product_warehouse_id" class="form-control"
                                    name="purchase_product_warehouse_id" required>
                                    <option value="">Select Warehouse</option>
                                    @foreach ($productWarehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="purchase_product_warehouse_room_id">Warehouse Room</label>
                                <select id="purchase_product_warehouse_room_id" class="form-control"
                                    name="purchase_product_warehouse_room_id" required>
                                    <option value="">Select Warehouse Room</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="purchase_product_warehouse_room_cartoon_id">Warehouse Room Cartoon</label>
                                <select id="purchase_product_warehouse_room_cartoon_id" class="form-control"
                                    name="purchase_product_warehouse_room_cartoon_id" required>
                                    <option value="">Select Warehouse Room Cartoon</option>
                                </select>
                            </div>
                            <div class="col-md-4 mt-4">
                                <label for="supplier">Supplier</label>
                                <select class="form-control" name="supplier_id" required>
                                    <option value="">Select Supplier</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mt-4">
                                <label for="date">Purchase Date</label>
                                <input type="date" class="form-control" name="purchase_date" required>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center">
                            <div class="mb-3 w-50">
                                <div style="position: relative;" id="searchContainer">
                                    <input type="text" @keyup="getData" id="searchPurchaseProduct" class="form-control"
                                        placeholder="Search for a product...">
                                    <div id="searchResults" class="dropdown-menu"
                                        style="display: none; position: absolute; z-index: 1000;"></div>
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
                                <tr>
                                    <td>
                                        <select class="form-control product" name="products[]" required>
                                            <option value="">Select Product</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                                    {{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" class="form-control quantity" name="quantities[]"
                                            value="1" min="1" required></td>
                                    <td><input type="number" class="form-control price" name="prices[]" step="0.01">
                                    </td>
                                    <td><input type="number" class="form-control discount" name="discounts[]"
                                            value="0" min="0"></td>
                                    <td><input type="number" class="form-control tax" name="taxes[]" value="0"
                                            min="0"></td>
                                    <td><input type="text" class="form-control total" name="totals[]" readonly></td>
                                    <td><button type="button" class="btn btn-danger removeRow">X</button></td>
                                </tr>
                            </tbody>
                        </table>
                        <div>
                            <button type="button" id="addRow" class="btn btn-primary">+ Add Row</button>
                        </div>

                        <!-- Summary Section -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row form-group">
                                            <label for="" class="col-sm-4 control-label">Total Quantities</label>
                                            <div class="col-sm-4">
                                                <label class="control-label total_quantity text-success" style="font-size: 20px;">
                                                    3.00
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row form-group">
                                            <label for="other_charges_input" class="col-sm-4 control-label">Other Charges</label>
                                            <div class="col-sm-4">
                                                <input type="text" class="form-control text-right only_currency" id="other_charges_input" name="other_charges_input" onkeyup="final_total();" value="" />
                                            </div>
                                            <div class="col-sm-4">
                                                <select class="form-control" id="other_charges_tax_id" name="other_charges_tax_id" onchange="final_total();" style="width: 100%;">
                                                    <option value="">-Select-</option>
                                                    <option data-tax="5.0000" value="149">VAT</option>
                                                    <option data-tax="2.5000" value="150">import</option>
                                                    <option data-tax="7.5000" value="151">regular expense</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row form-group">
                                            <label for="discount_to_all_input" class="col-sm-4 control-label">Discount on All</label>
                                            <div class="col-sm-4">
                                                <input type="text" class="form-control text-right only_currency" id="discount_to_all_input" name="discount_to_all_input" onkeyup="enable_or_disable_item_discount();" value="" />
                                            </div>
                                            <div class="col-sm-4">
                                                <select class="form-control" onchange="final_total();" id="discount_to_all_type" name="discount_to_all_type">
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
                                                <textarea class="form-control text-left" id="purchase_note" name="purchase_note"></textarea>
                                                <span id="purchase_note_msg" style="display: none;" class="text-danger"></span>
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
                                                ৳ <b id="subtotal_amt" name="subtotal_amt">28903.50</b>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="text-right">Other Charges</th>
                                            <th class="text-right">
                                                ৳ <b id="other_charges_amt" name="other_charges_amt">10.50</b>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="text-right">Discount on All</th>
                                            <th class="text-right">
                                                ৳ <b id="discount_to_all_amt" name="discount_to_all_amt">2891.40</b>
                                            </th>
                                        </tr>
                                        <tr style="">
                                            <th class="text-right">Round Off
                                                <i class="hover-q " data-container="body" data-toggle="popover"
                                                    data-placement="top"
                                                    data-content="Go to Site Settings-> Site -> Disable the Round Off(Checkbox)."
                                                    data-html="true" data-trigger="hover"
                                                    data-original-title="Do you wants to Disable Round Off ?" title="">
                                                    <i class="fa fa-info-circle text-maroon text-black hover-q"></i>
                                                </i>
    
                                            </th>
                                            <th class="text-right" style="">
                                                ৳ <b id="round_off_amt" name="tot_round_off_amt">0.40</b>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="text-right">Grand Total</th>
                                            <th class="text-right" style="">
                                                ৳ <b id="total_amt" name="total_amt">26023.00</b>
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
    <script src="{{ url('assets') }}/js/tagsinput.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('[data-toggle="select2"]').select2();

        $('#description').summernote({
            placeholder: 'Write Description Here',
            tabsize: 2,
            height: 350
        });
    </script>



    <script>
        $(document).ready(function() {
            function calculateTotal(row) {
                let qty = parseFloat(row.find('.quantity').val()) || 0;
                let price = parseFloat(row.find('.price').val()) || 0;
                let discount = parseFloat(row.find('.discount').val()) || 0;
                let tax = parseFloat(row.find('.tax').val()) || 0;

                let discountedPrice = price - (price * discount / 100);
                let taxedPrice = discountedPrice + (discountedPrice * tax / 100);
                let total = qty * taxedPrice;

                row.find('.total').val(total.toFixed(2));
                updateSummary();
            }

            function updateSummary() {
                let subtotal = 0;
                let totalDiscount = 0;
                let grandTotal = 0;

                $('#purchaseItems tr').each(function() {
                    let row = $(this);
                    let qty = parseFloat(row.find('.quantity').val()) || 0;
                    let price = parseFloat(row.find('.price').val()) || 0;
                    let discount = parseFloat(row.find('.discount').val()) || 0;
                    let total = parseFloat(row.find('.total').val()) || 0;

                    subtotal += qty * price;
                    totalDiscount += qty * (price * discount / 100);
                    grandTotal += total;
                });

                $('#subtotal').text(`$${subtotal.toFixed(2)}`);
                $('#totalDiscount').text(`$${totalDiscount.toFixed(2)}`);
                $('#grandTotal').text(`$${grandTotal.toFixed(2)}`);
            }

            $('#addRow').click(function() {
                let newRow = $('#purchaseItems tr:first').clone();
                newRow.find('input').val('');
                newRow.find('.total').val('0.00');
                $('#purchaseItems').append(newRow);
            });

            $(document).on('input', '.quantity, .price, .discount, .tax', function() {
                calculateTotal($(this).closest('tr'));
            });

            $(document).on('click', '.removeRow', function() {
                $(this).closest('tr').remove();
                updateSummary();
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            // Hide the first row initially
            $('#purchaseItems tr:first').hide();

            function calculateTotal(row) {
                let qty = parseFloat(row.find('.quantity').val()) || 0;
                let price = parseFloat(row.find('.price').val()) || 0;
                let discount = parseFloat(row.find('.discount').val()) || 0;
                let tax = parseFloat(row.find('.tax').val()) || 0;

                let discountedPrice = price - (price * discount / 100);
                let taxedPrice = discountedPrice + (discountedPrice * tax / 100);
                let total = qty * taxedPrice;

                row.find('.total').val(total.toFixed(2));
                updateSummary();
            }

            function updateSummary() {
                let subtotal = 0;
                let totalDiscount = 0;
                let grandTotal = 0;

                $('#purchaseItems tr').each(function() {
                    let row = $(this);
                    let qty = parseFloat(row.find('.quantity').val()) || 0;
                    let price = parseFloat(row.find('.price').val()) || 0;
                    let discount = parseFloat(row.find('.discount').val()) || 0;
                    let total = parseFloat(row.find('.total').val()) || 0;

                    subtotal += qty * price;
                    totalDiscount += qty * (price * discount / 100);
                    grandTotal += total;
                });

                $('#subtotal').text(`$${subtotal.toFixed(2)}`);
                $('#totalDiscount').text(`$${totalDiscount.toFixed(2)}`);
                $('#grandTotal').text(`$${grandTotal.toFixed(2)}`);
            }

            $('#addRow').click(function() {
                let newRow = $('#purchaseItems tr:first').clone();
                newRow.find('input').val('');
                newRow.find('.total').val('0.00');
                newRow.show(); // Ensure new rows are visible
                $('#purchaseItems').append(newRow);
            });

            $(document).on('input', '.quantity, .price, .discount, .tax', function() {
                calculateTotal($(this).closest('tr'));
            });

            $(document).on('click', '.removeRow', function() {
                $(this).closest('tr').remove();
                updateSummary();
            });
        });
    </script>
@endsection
