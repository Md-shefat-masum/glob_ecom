<tr>
    <td class="text-center">
        @php
            $total = 0;
            if (session('cart') && count(session('cart')) > 0) {
                foreach (session('cart') as $cartIndex => $details) {
                    $total = $total + $details['price'] * $details['quantity'];
                    if (isset($details['discounted_price'])) {
                        $total = $total - $details['discounted_price'];
                    }
                }
            }
        @endphp
        ৳ {{ number_format($total, 2) }}
        <input type="hidden" name="subtotal" id="subtotal" value="{{ $total }}">
    </td>
    {{-- <td class="text-center">৳ 0</td> --}}
    <td class="text-center">
        @php
            if (session('shipping_charge')) {
                $total = $total + session('shipping_charge');
            }
        @endphp
        ৳ <input type="number" class="text-center shipping-charge-input" style="width: 60px; -moz-appearance: textfield; appearance: textfield;"
            data-original-value="{{ session('shipping_charge', 0) }}"
            onblur="updateShippingChargeOnBlur(this)"
            onkeydown="handleShippingKeydown(event, this)"
            oninput="handleShippingInput(this)"
            @if (session('shipping_charge')) value="{{ session('shipping_charge') }}" @else value="0" @endif
            min="0" id="shipping_charge" name="shipping_charge" 
            onwheel="this.blur()" />
    </td>
    <td class="text-center">
        @php
            if (session('pos_discount')) {
                $total -= session('pos_discount');
            }
            if (session('discount')) {
                $total -= session('discount');
            }
        @endphp
        ৳ <input type="number" class="text-center order-discount-input" style="width: 60px; -moz-appearance: textfield; appearance: textfield;"
            data-original-value="{{ session('discount', 0) }}"
            onblur="updateOrderDiscountOnBlur(this)"
            onkeydown="handleOrderDiscountKeydown(event, this)"
            oninput="handleOrderDiscountInput(this)"
            @if (session('discount')) value="{{ session('discount') }}" @else value="0" @endif min="0"
            id="discount" name="discount" 
            onwheel="this.blur()" />
    </td>
    <th class="text-center" id="total_cart_calculation">৳ {{ number_format($total, 2) }}</th>
</tr>

<script>
    // Add CSS styles for visual feedback
    $('<style>')
        .prop('type', 'text/css')
        .html(`
            .shipping-charge-input.pending-update, .order-discount-input.pending-update {
                border: 2px solid #ffc107;
                background-color: #fff3cd;
            }
            .shipping-charge-input.updating, .order-discount-input.updating {
                border: 2px solid #007bff;
                background-color: #e3f2fd;
            }
            /* Hide number input arrows */
            input[type=number]::-webkit-inner-spin-button, 
            input[type=number]::-webkit-outer-spin-button { 
                -webkit-appearance: none; 
                margin: 0; 
            }
            input[type=number] {
                -moz-appearance: textfield;
            }
        `)
        .appendTo('head');

    // Improved Shipping Charge handling
    function handleShippingInput(inputElem) {
        inputElem.classList.add('pending-update');
        inputElem.classList.remove('updating');
        
        let value = parseFloat(inputElem.value);
        if (inputElem.value !== '' && (isNaN(value) || value < 0)) {
            inputElem.style.borderColor = '#dc3545';
            inputElem.style.backgroundColor = '#f8d7da';
        } else {
            inputElem.style.borderColor = '';
            inputElem.style.backgroundColor = '';
        }
    }
    
    function handleShippingKeydown(event, inputElem) {
        if (event.key === 'Enter') {
            event.preventDefault();
            inputElem.blur();
            return;
        }
        
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
    
    function updateShippingChargeOnBlur(inputElem) {
        let value = parseFloat(inputElem.value) || 0;
        let originalValue = parseFloat(inputElem.getAttribute('data-original-value')) || 0;
        
        if (inputElem.value === '') {
            value = 0;
            inputElem.value = 0;
        }
        
        if (isNaN(value) || value < 0) {
            inputElem.value = originalValue;
            inputElem.classList.remove('pending-update');
            inputElem.style.borderColor = '';
            inputElem.style.backgroundColor = '';
            
            toastr.options.positionClass = 'toast-top-right';
            toastr.options.timeOut = 2000;
            toastr.warning('Shipping charge must be 0 or greater. Restored to previous value.');
            return;
        }
        
        if (value === originalValue) {
            inputElem.classList.remove('pending-update');
            return;
        }
        
        inputElem.classList.remove('pending-update');
        inputElem.classList.add('updating');
        inputElem.disabled = true;
        
        updateOrderTotalAmount().then(() => {
            inputElem.setAttribute('data-original-value', value);
            inputElem.classList.remove('updating');
            inputElem.disabled = false;
            
            toastr.options.positionClass = 'toast-top-right';
            toastr.options.timeOut = 1000;
            toastr.success('Shipping charge updated successfully!');
        }).catch(() => {
            inputElem.value = originalValue;
            inputElem.classList.remove('updating');
            inputElem.disabled = false;
            
            toastr.options.positionClass = 'toast-top-right';
            toastr.options.timeOut = 2000;
            toastr.error('Failed to update shipping charge. Please try again.');
        });
    }

    // Improved Order Discount handling
    function handleOrderDiscountInput(inputElem) {
        inputElem.classList.add('pending-update');
        inputElem.classList.remove('updating');
        
        let discount = parseFloat(inputElem.value) || 0;
        var subtotal = parseFloat($("#subtotal").val()) || 0;
        
        if (inputElem.value !== '' && (isNaN(discount) || discount < 0 || discount > subtotal)) {
            inputElem.style.borderColor = '#dc3545';
            inputElem.style.backgroundColor = '#f8d7da';
        } else {
            inputElem.style.borderColor = '';
            inputElem.style.backgroundColor = '';
        }
    }
    
    function handleOrderDiscountKeydown(event, inputElem) {
        if (event.key === 'Enter') {
            event.preventDefault();
            inputElem.blur();
            return;
        }
        
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
    
    function updateOrderDiscountOnBlur(inputElem) {
        let discount = parseFloat(inputElem.value) || 0;
        let originalValue = parseFloat(inputElem.getAttribute('data-original-value')) || 0;
        var subtotal = parseFloat($("#subtotal").val()) || 0;
        
        if (inputElem.value === '') {
            discount = 0;
            inputElem.value = 0;
        }
        
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
        
        if (discount === originalValue) {
            inputElem.classList.remove('pending-update');
            return;
        }
        
        inputElem.classList.remove('pending-update');
        inputElem.classList.add('updating');
        inputElem.disabled = true;
        
        updateOrderTotalAmount().then(() => {
            inputElem.setAttribute('data-original-value', discount);
            inputElem.classList.remove('updating');
            inputElem.disabled = false;
            
            toastr.options.positionClass = 'toast-top-right';
            toastr.options.timeOut = 1000;
            toastr.success('Order discount updated successfully!');
        }).catch(() => {
            inputElem.value = originalValue;
            inputElem.classList.remove('updating');
            inputElem.disabled = false;
            
            toastr.options.positionClass = 'toast-top-right';
            toastr.options.timeOut = 2000;
            toastr.error('Failed to update order discount. Please try again.');
        });
    }

    // Improved main calculation function
    function updateOrderTotalAmount() {
        return new Promise((resolve, reject) => {
            var shippingCharge = parseFloat($("#shipping_charge").val()) || 0;
            var discount = parseFloat($("#discount").val()) || 0;
            var currentPrice = parseFloat($("#subtotal").val()) || 0;

            // Validate discount against subtotal
            if (discount > currentPrice) {
                toastr.options.positionClass = 'toast-top-right';
                toastr.options.timeOut = 2000;
                toastr.error("Discount cannot be greater than Order Amount");
                reject(new Error("Discount too high"));
                return;
            }

            // Use the global dynamic couponPrice variable
            var globalCouponPrice = (typeof couponPrice !== 'undefined') ? couponPrice : 0;

            console.log(
                'currentPrice:', currentPrice,
                'shippingCharge:', shippingCharge,
                'discount:', discount,
                'couponPrice:', globalCouponPrice
            );

            $.get("{{ url('update/order/total') }}" + '/' + shippingCharge + '/' + discount)
                .done(function(data) {
                    var newPrice = (currentPrice + shippingCharge) - (discount + globalCouponPrice);

                    var totalPriceDiv = document.getElementById("total_cart_calculation");
                    totalPriceDiv.innerText = '৳ ' + newPrice.toLocaleString("en-BD", {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    
                    $("input[name='delivery_method']").prop("checked", false);
                    resolve(data);
                })
                .fail(function(xhr, status, error) {
                    console.error('Failed to update order total:', error);
                    reject(new Error("AJAX request failed"));
                });
        });
    }

    // Legacy support for old calls
    function updateOrderTotalAmountLegacy() {
        updateOrderTotalAmount().catch(function(error) {
            console.error('Order total update failed:', error);
        });
    }
</script>
