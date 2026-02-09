@if (session('cart') && count(session('cart')) > 0)
    @php $serial = 1; @endphp
    @foreach (session('cart') as $cartIndex => $details)
        <tr>
            <th class="text-center">
                {{ $serial++ }}
                <input type="hidden" name="product_id[]" value="{{ $details['product_id'] }}">
                <input type="hidden" name="color_id[]" value="{{ $details['color_id'] }}">
                <input type="hidden" name="size_id[]" value="{{ $details['size_id'] }}">
                <input type="hidden" name="purchase_product_warehouse_id[]"
                    value="{{ $details['purchase_product_warehouse_id'] }}">
                <input type="hidden" name="purchase_product_warehouse_room_id[]"
                    value="{{ $details['purchase_product_warehouse_room_id'] }}">
                <input type="hidden" name="purchase_product_warehouse_room_cartoon_id[]"
                    value="{{ $details['purchase_product_warehouse_room_cartoon_id'] }}">
                <input type="hidden" name="price[]" value="{{ $details['price'] }}">
            </th>
            <td class="text-center">
                <img src="{{ url($details['image']) }}" style="width: 30px; height: 30px">
            </td>
            <td class="text-left">
                {{ $details['name'] }} ({{ $details['code'] }}),
                @if ($details['color_id'])
                    <b>Color:</b> {{ $details['color_name'] }},
                @endif
                @if ($details['size_id'])
                    <b>Size:</b> {{ $details['size_name'] }}
                @endif
            </td>
            <td class="text-center">৳{{ $details['price'] }}</td>

            <td class="text-center">
                <input type="number" class="text-center qty-input"
                    style="width: 60px; -moz-appearance: textfield; appearance: textfield;" min="1"
                    data-cart-index="{{ $cartIndex }}" data-original-value="{{ $details['quantity'] }}"
                    onblur="updateCartQtyOnBlur(this, '{{ $cartIndex }}')"
                    onkeydown="handleQtyKeydown(event, this, '{{ $cartIndex }}')" oninput="handleQtyInput(this)"
                    value="{{ $details['quantity'] }}" name="quantity[]" onwheel="this.blur()" />
                <style>
                    /* Hide number input arrows for Chrome, Safari, Edge, Opera */
                    input[type=number]::-webkit-inner-spin-button,
                    input[type=number]::-webkit-outer-spin-button {
                        -webkit-appearance: none;
                        margin: 0;
                    }

                    /* Hide number input arrows for Firefox */
                    input[type=number] {
                        -moz-appearance: textfield;
                    }
                </style>
            </td>

            <td class="text-center">
                <input type="number" class="text-center discount-input"
                    style="width: 60px; -moz-appearance: textfield; appearance: textfield;" min="0"
                    data-cart-index="{{ $cartIndex }}" data-original-value="{{ $details['discounted_price'] }}"
                    onblur="updateCartDiscountOnBlur(this, '{{ $cartIndex }}')"
                    onkeydown="handleDiscountKeydown(event, this, '{{ $cartIndex }}')"
                    oninput="handleDiscountInput(this)" value="{{ $details['discounted_price'] }}"
                    name="discounted_price[]" required onwheel="this.blur()" />
            </td>

            <td class="text-center subtotal-cell">
                ৳{{ number_format($details['price'] * $details['quantity'] - (isset($details['discounted_price']) ? $details['discounted_price'] : 0), 2) }}
            </td>
            <td class="text-center">
                <button type="button" onclick="removeCartItem('{{ $cartIndex }}')"
                    class="btn btn-danger btn-sm m-0">
                    <i class="fa fa-times"></i>
                </button>
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="8" class="text-center">No item is Added</td>
    </tr>
@endif


<script>
    // Improved user-friendly discount update
    function handleDiscountInput(inputElem) {
        // Visual feedback - show pending state
        inputElem.classList.add('pending-update');
        inputElem.classList.remove('updating');

        // Real-time validation
        let discount = parseFloat(inputElem.value) || 0;
        var row = inputElem.closest('tr');
        var price = parseFloat(row.querySelector('input[name="price[]"]').value) || 0;
        var qty = parseFloat(row.querySelector('input[name="quantity[]"]').value) || 1;
        var subtotal = price * qty;

        // Validate input as user types
        if (inputElem.value !== '' && (isNaN(discount) || discount < 0 || discount > subtotal)) {
            inputElem.style.borderColor = '#dc3545';
            inputElem.style.backgroundColor = '#f8d7da';
        } else {
            inputElem.style.borderColor = '';
            inputElem.style.backgroundColor = '';
        }
    }

    function handleDiscountKeydown(event, inputElem, cartIndex) {
        // Enter key to immediately update
        if (event.key === 'Enter') {
            event.preventDefault();
            inputElem.blur(); // This will trigger onblur
            return;
        }

        // Escape key to restore original value
        if (event.key === 'Escape') {
            event.preventDefault();
            let originalValue = inputElem.getAttribute('data-original-value');
            inputElem.value = originalValue;
            inputElem.classList.remove('pending-update');
            inputElem.style.borderColor = '';
            inputElem.style.backgroundColor = '';
            inputElem.blur();
            return;
        }
    }

    function updateCartDiscountOnBlur(inputElem, cartIndex) {
        let discount = parseFloat(inputElem.value) || 0;
        let originalValue = parseFloat(inputElem.getAttribute('data-original-value')) || 0;
        var row = inputElem.closest('tr');
        var price = parseFloat(row.querySelector('input[name="price[]"]').value) || 0;
        var qty = parseFloat(row.querySelector('input[name="quantity[]"]').value) || 1;
        var subtotal = price * qty;

        // If empty, set to 0
        if (inputElem.value === '') {
            discount = 0;
            inputElem.value = 0;
        }

        // Validate discount
        if (isNaN(discount) || discount < 0) {
            inputElem.value = originalValue;
            inputElem.classList.remove('pending-update');
            inputElem.style.borderColor = '';
            inputElem.style.backgroundColor = '';

            toastr.options.positionClass = 'toast-top-right';
            toastr.options.timeOut = 2000;
            toastr.warning('Discount must be 0 or greater. Restored to previous value.');
            return;
        }

        // Prevent discount greater than subtotal
        if (discount > subtotal) {
            inputElem.value = originalValue;
            inputElem.classList.remove('pending-update');
            inputElem.style.borderColor = '';
            inputElem.style.backgroundColor = '';

            toastr.options.positionClass = 'toast-top-right';
            toastr.options.timeOut = 2000;
            toastr.error('Discount cannot be greater than subtotal (৳' + subtotal.toFixed(2) + ')!');
            return;
        }

        // If value hasn't changed, just remove pending state
        if (discount === originalValue) {
            inputElem.classList.remove('pending-update');
            return;
        }

        // Show updating state
        inputElem.classList.remove('pending-update');
        inputElem.classList.add('updating');
        inputElem.disabled = true;

        // Update subtotal cell immediately for smooth UX
        var subtotalCell = row.querySelector('.subtotal-cell');
        var newSubtotal = subtotal - discount;
        if (newSubtotal < 0) newSubtotal = 0;
        subtotalCell.innerHTML = '৳' + newSubtotal.toFixed(2);

        // Update the cart item discount in backend
        $.get("{{ url('update/cart/discount') }}/" + cartIndex + "/" + discount, function(data) {
            // Only update the totals section, not the cart rows
            $('.cart_calculation').html(data.cart_calculation);

            // Update the original value for next comparison
            inputElem.setAttribute('data-original-value', discount);
            inputElem.classList.remove('updating');
            inputElem.disabled = false;

            // Show success feedback
            toastr.options.positionClass = 'toast-top-right';
            toastr.options.timeOut = 1000;
            toastr.success('Discount updated successfully!');

        }).fail(function() {
            // On error, restore original value
            inputElem.value = originalValue;
            inputElem.classList.remove('updating');
            inputElem.disabled = false;

            // Restore subtotal
            var originalSubtotal = subtotal - originalValue;
            subtotalCell.innerHTML = '৳' + originalSubtotal.toFixed(2);

            toastr.options.positionClass = 'toast-top-right';
            toastr.options.timeOut = 2000;
            toastr.error('Failed to update discount. Please try again.');
        });
    }

    // Legacy function for compatibility (keep the old one for backward compatibility)
    function updateCartDiscount(input, cartIndex) {
        updateCartDiscountOnBlur(input, cartIndex);
    }

    // Improved quantity update: more user-friendly with debouncing
    let qtyUpdateTimeout = null;

    function handleQtyInput(inputElem) {
        // Visual feedback - show pending state
        inputElem.classList.add('pending-update');
        inputElem.classList.remove('updating');

        // Clear any existing timeout
        if (qtyUpdateTimeout) {
            clearTimeout(qtyUpdateTimeout);
        }

        // Validate input as user types
        let value = parseFloat(inputElem.value);
        if (inputElem.value !== '' && (isNaN(value) || value < 1)) {
            inputElem.style.borderColor = '#dc3545';
            inputElem.style.backgroundColor = '#f8d7da';
        } else {
            inputElem.style.borderColor = '';
            inputElem.style.backgroundColor = '';
        }
    }

    function handleQtyKeydown(event, inputElem, cartIndex) {
        // Enter key to immediately update
        if (event.key === 'Enter') {
            event.preventDefault();
            inputElem.blur(); // This will trigger onblur which calls updateCartQtyOnBlur
            return;
        }

        // Escape key to restore original value
        if (event.key === 'Escape') {
            event.preventDefault();
            let originalValue = inputElem.getAttribute('data-original-value');
            inputElem.value = originalValue;
            inputElem.classList.remove('pending-update');
            inputElem.style.borderColor = '';
            inputElem.style.backgroundColor = '';
            inputElem.blur();
            return;
        }
    }

    function updateCartQtyOnBlur(inputElem, cartIndex) {
        let value = parseFloat(inputElem.value);
        let originalValue = parseFloat(inputElem.getAttribute('data-original-value'));

        // If empty or invalid, restore original value
        if (inputElem.value === '' || isNaN(value) || value < 1) {
            inputElem.value = originalValue;
            inputElem.classList.remove('pending-update');
            inputElem.style.borderColor = '';
            inputElem.style.backgroundColor = '';

            if (inputElem.value === '') {
                toastr.options.positionClass = 'toast-top-right';
                toastr.options.timeOut = 2000;
                toastr.warning('Quantity cannot be empty. Restored to previous value.');
            } else if (value < 1) {
                toastr.options.positionClass = 'toast-top-right';
                toastr.options.timeOut = 2000;
                toastr.warning('Quantity must be at least 1. Restored to previous value.');
            }
            return;
        }

        // If value hasn't changed, just remove pending state
        if (value === originalValue) {
            inputElem.classList.remove('pending-update');
            return;
        }

        // Show updating state
        inputElem.classList.remove('pending-update');
        inputElem.classList.add('updating');
        inputElem.disabled = true;

        // Update cart
        $.get("{{ url('update/cart/item') }}" + '/' + cartIndex + '/' + value, function(data) {
            $('.cart_items').html(data.rendered_cart);
            $('.cart_calculation').html(data.cart_calculation);

            // Format all subtotal cells to show consistent decimal places
            $('.subtotal-cell').each(function() {
                let text = $(this).text();
                let amount = parseFloat(text.replace('৳', '').replace(',', ''));
                if (!isNaN(amount)) {
                    $(this).text('৳' + amount.toFixed(2));
                }
            });

            // Reset coupon price as cart updates invalidate coupon
            if (typeof couponPrice !== 'undefined') {
                couponPrice = 0;
            }

            // Show success feedback
            toastr.options.positionClass = 'toast-top-right';
            toastr.options.timeOut = 1000;
            toastr.success('Quantity updated successfully!');

        }).fail(function() {
            // On error, restore original value
            inputElem.value = originalValue;
            inputElem.classList.remove('updating');
            inputElem.disabled = false;

            toastr.options.positionClass = 'toast-top-right';
            toastr.options.timeOut = 2000;
            toastr.error('Failed to update quantity. Please try again.');
        });
    }

    // Legacy function for compatibility (in case it's called elsewhere)
    function updateCartQty(inputElem, cartIndex) {
        updateCartQtyOnBlur(inputElem, cartIndex);
    }

    // Function to remove cart item
    function removeCartItem(cartIndex) {
        $.get("{{ url('remove/cart/item') }}" + '/' + cartIndex, function(data) {
            // toastr.error("Item Removed");
            $('.cart_items').html(data.rendered_cart);
            $('.cart_calculation').html(data.cart_calculation);

            // Format all subtotal cells to show consistent decimal places
            $('.subtotal-cell').each(function() {
                let text = $(this).text();
                let amount = parseFloat(text.replace('৳', '').replace(',', ''));
                if (!isNaN(amount)) {
                    $(this).text('৳' + amount.toFixed(2));
                }
            });

            // Reset coupon price as cart updates invalidate coupon
            if (typeof couponPrice !== 'undefined') {
                couponPrice = 0;
            }
        })
    }

    // Format all subtotals on page load for consistency
    $(document).ready(function() {
        formatAllSubtotals();
    });

    function formatAllSubtotals() {
        $('.subtotal-cell').each(function() {
            let text = $(this).text();
            let amount = parseFloat(text.replace('৳', '').replace(',', ''));
            if (!isNaN(amount)) {
                $(this).text('৳' + amount.toFixed(2));
            }
        });
    }
</script>
