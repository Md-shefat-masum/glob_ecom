
@if (count($savedAddressed) > 0)
    <div class="single-details-checkout-widget">
        <h5 class="checkout-widget-title">Saved Addresses</h5>
        <div class="row gx-1">
            @foreach ($savedAddressed as $index => $address)
                <div class="col-lg-6"
                    @if ($index == 0) style="padding-right: 5px" @else style="padding-left: 5px" @endif>
                    <div class="address_box">
                        <label for="saved_address_{{ $address->slug }}">
                            <b class="d-block">
                                <input type="radio" id="saved_address_{{ $address->slug }}" name="saved_address" onchange="applySavedAddress('{{ $address->slug }}')">
                                {{ $address->address_type }} Address
                            </b>
                            <address style="margin-bottom: 0px;">{{ $address->address }}, {{ $address->state }}-{{ $address->post_code }}, {{ $address->city }}</address>

                            <input type="hidden" id="saved_address_name_{{ $address->slug }}" value="{{ $address->name }}">
                            <input type="hidden" id="saved_address_phone_{{ $address->slug }}" value="{{ $address->phone }}">
                            <input type="hidden" id="saved_address_line_{{ $address->slug }}" value="{{ $address->address }}">
                            <input type="hidden" id="saved_address_district_{{ $address->slug }}" value="{{ $address->city }}">
                            <input type="hidden" id="saved_address_upazila_{{ $address->slug }}" value="{{ $address->state }}">
                            <input type="hidden" id="saved_address_post_code_{{ $address->slug }}" value="{{ $address->post_code }}">

                        </label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
