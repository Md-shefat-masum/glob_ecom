<tr>
    <td class="text-center" style="vertical-align: middle;">
        <strong>1</strong>
        <input type="hidden" name="order_details_id[]" value="">
    </td>
    <td class="text-center" style="vertical-align: middle;">
        <select name="product_id[]" onchange="getProductVariants(this.value)" data-toggle="select2" class="form-control">
            @php
                echo App\Models\Product::getDropDownList('name');
            @endphp
        </select>
    </td>
    <td class="text-center" style="vertical-align: middle;">
        <select name="product_variant_id[]" onchange="productVariantInfo({{$rowNo}})" id="product_variant_id_{{$rowNo}}" class="form-control">
        </select>

        <input type="hidden" id="color_id_{{$rowNo}}" name="color_id[]">
        <input type="hidden" id="size_id_{{$rowNo}}" name="size_id[]">
        <input type="hidden" id="storage_id_{{$rowNo}}" name="storage_id[]">
        <input type="hidden" id="sim_id_{{$rowNo}}" name="sim_id[]">
        <input type="hidden" id="region_id_{{$rowNo}}" name="region_id[]">
        <input type="hidden" id="warrenty_id_{{$rowNo}}" name="warrenty_id[]">
        <input type="hidden" id="device_condition_id_{{$rowNo}}" name="device_condition_id[]">
    </td>
    <td class="text-center" style="vertical-align: middle;">
        <input class="form-control d-inline-block w-50" type="number" onwheel="this.blur()" onkeyup="changeTotalPrice({{$rowNo}})" min="1" name="qty[]" id="qty_{{$rowNo}}" required> <span id="unit_text_{{$rowNo}}"></span>
        <input type="hidden" name="unit_id[]" id="unit_{{$rowNo}}">
    </td>
    <td class="text-center" style="vertical-align: middle;">
        ৳ <input class="form-control d-inline-block w-75" type="text" name="unit_price[]" id="unit_price_{{$rowNo}}" readonly required>
    </td>
    <td class="text-right" style="vertical-align: middle;">
        ৳ <input class="form-control d-inline-block w-75 orderT" type="text" name="total_price[]" id="total_price_{{$rowNo}}" readonly required>
    </td>
    <td class="text-center" style="vertical-align: middle;">
        <a href="javascript:void(0)" onclick="removeRow(this)" class="btn btn-danger rounded btn-sm d-inline text-white"><i class="feather-trash-2" style="font-size: 14px; line-height: 2"></i></a>
    </td>
</tr>

