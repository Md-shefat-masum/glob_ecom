@extends('backend.master')

@section('header_css')
    <link href="{{ url('assets') }}/plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />
    <style>
        .return-alert {
            background: #fff3cd;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #ffc107;
            margin-bottom: 20px;
        }
        .product-row {
            background: #f8f9fa;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }
    </style>
@endsection

@section('page_title')
    Manual Product Return
@endsection

@section('page_heading')
    Create Manual Product Return
@endsection

@section('content')
    <div class="container" style="max-width: 1200px;">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4>Create Manual Product Return</h4>
                            <a href="{{ route('ViewAllManualProductReturns') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>

                        <div class="return-alert">
                            <i class="fas fa-info-circle"></i> <strong>Note:</strong> This is for returns from offline/manual sales NOT in the system. The refund amount will be credited to customer's wallet.
                        </div>

                        <form id="returnForm" action="{{ route('StoreManualProductReturn') }}" method="POST">
                            @csrf
                            <input type="hidden" name="return_code" value="{{ $return_code }}">

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label>Return Code</label>
                                    <input type="text" class="form-control" value="{{ $return_code }}" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label for="customer_id">Customer <span class="text-danger">*</span></label>
                                    <select id="customer_id" class="form-control select2" name="customer_id" required>
                                        <option value="">-- Select Customer --</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->name }} - {{ $customer->phone }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="return_date">Return Date <span class="text-danger">*</span></label>
                                    <input type="date" id="return_date" class="form-control" name="return_date" value="{{ now()->toDateString() }}" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="return_reason">Return Reason <span class="text-danger">*</span></label>
                                    <input type="text" id="return_reason" class="form-control" name="return_reason" placeholder="e.g., Defective product, wrong item, etc." required>
                                </div>
                            </div>

                            <h5 class="mt-4">Return Items</h5>
                            <div id="returnItems">
                                <div class="product-row" data-index="0">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <label>Product Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="return_items[0][product_name]" placeholder="Enter product name" required>
                                            <input type="hidden" name="return_items[0][product_id]" class="product-id-input">
                                        </div>
                                        <div class="col-md-2">
                                            <label>Qty <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control qty-input" name="return_items[0][qty]" min="1" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Unit Price <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control price-input" name="return_items[0][unit_price]" step="0.01" min="0" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Total</label>
                                            <input type="text" class="form-control item-total" readonly>
                                        </div>
                                        <div class="col-md-1">
                                            <label>&nbsp;</label><br>
                                            <button type="button" class="btn btn-danger btn-sm remove-item" style="display:none;"><i class="fas fa-trash"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="button" id="addItem" class="btn btn-success btn-sm mb-3">
                                <i class="fas fa-plus"></i> Add Another Item
                            </button>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h5>Total Refund Amount: <span id="grandTotal" class="text-success">৳0.00</span></h5>
                                            <small class="text-muted">This amount will be credited to customer's wallet</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="note">Note (Optional)</label>
                                    <textarea id="note" class="form-control" name="note" rows="2"></textarea>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-12 text-right">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-check"></i> Process Return
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_js')
    <script src="{{ url('assets') }}/plugins/select2/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();
            let itemIndex = 1;

            // Add item
            $('#addItem').on('click', function() {
                const newItem = `
                    <div class="product-row" data-index="${itemIndex}">
                        <div class="row">
                            <div class="col-md-5">
                                <label>Product Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="return_items[${itemIndex}][product_name]" placeholder="Enter product name" required>
                                <input type="hidden" name="return_items[${itemIndex}][product_id]" class="product-id-input">
                            </div>
                            <div class="col-md-2">
                                <label>Qty <span class="text-danger">*</span></label>
                                <input type="number" class="form-control qty-input" name="return_items[${itemIndex}][qty]" min="1" required>
                            </div>
                            <div class="col-md-2">
                                <label>Unit Price <span class="text-danger">*</span></label>
                                <input type="number" class="form-control price-input" name="return_items[${itemIndex}][unit_price]" step="0.01" min="0" required>
                            </div>
                            <div class="col-md-2">
                                <label>Total</label>
                                <input type="text" class="form-control item-total" readonly>
                            </div>
                            <div class="col-md-1">
                                <label>&nbsp;</label><br>
                                <button type="button" class="btn btn-danger btn-sm remove-item"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </div>
                `;
                $('#returnItems').append(newItem);
                itemIndex++;
                updateRemoveButtons();
            });

            // Remove item
            $(document).on('click', '.remove-item', function() {
                $(this).closest('.product-row').remove();
                calculateTotal();
                updateRemoveButtons();
            });

            // Calculate item total
            $(document).on('input', '.qty-input, .price-input', function() {
                const row = $(this).closest('.product-row');
                const qty = parseFloat(row.find('.qty-input').val()) || 0;
                const price = parseFloat(row.find('.price-input').val()) || 0;
                const total = qty * price;
                row.find('.item-total').val('৳' + total.toFixed(2));
                calculateTotal();
            });

            function calculateTotal() {
                let total = 0;
                $('.product-row').each(function() {
                    const qty = parseFloat($(this).find('.qty-input').val()) || 0;
                    const price = parseFloat($(this).find('.price-input').val()) || 0;
                    total += qty * price;
                });
                $('#grandTotal').text('৳' + total.toFixed(2));
            }

            function updateRemoveButtons() {
                const count = $('.product-row').length;
                if (count > 1) {
                    $('.remove-item').show();
                } else {
                    $('.remove-item').hide();
                }
            }

            // Form submission
            $('#returnForm').on('submit', function(e) {
                e.preventDefault();

                // Validate at least one item
                if ($('.product-row').length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Items',
                        text: 'Please add at least one item to return!'
                    });
                    return;
                }

                // Collect data
                const formData = {
                    _token: $('input[name="_token"]').val(),
                    customer_id: $('#customer_id').val(),
                    return_date: $('#return_date').val(),
                    return_code: $('input[name="return_code"]').val(),
                    return_reason: $('#return_reason').val(),
                    note: $('#note').val(),
                    return_items: []
                };

                $('.product-row').each(function() {
                    const index = $(this).data('index');
                    formData.return_items.push({
                        product_name: $(this).find(`input[name="return_items[${index}][product_name]"]`).val(),
                        product_id: $(this).find(`input[name="return_items[${index}][product_id]"]`).val() || null,
                        qty: parseInt($(this).find(`input[name="return_items[${index}][qty]"]`).val()),
                        unit_price: parseFloat($(this).find(`input[name="return_items[${index}][unit_price]"]`).val())
                    });
                });

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message
                            }).then(() => {
                                window.location.href = response.redirect;
                            });
                        }
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            let errorMsg = '';
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                errorMsg += value[0] + '\n';
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                text: errorMsg
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.message || 'Something went wrong!'
                            });
                        }
                    }
                });
            });
        });
    </script>
@endsection

