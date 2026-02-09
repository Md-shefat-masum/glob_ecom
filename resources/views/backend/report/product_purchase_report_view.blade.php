<div class="card" id="printableArea">
    <div class="card-body">

        <div class="row pb-3">
            <div class="col-lg-6">
                <h4 class="card-title">Product Purchase Report From <b style="color: #225ba5">{{date("d M, Y",strtotime($startDate))}}</b> To <b style="color: #225ba5">{{date("d M, Y",strtotime($endDate))}}</b></h4>
            </div>
            <div class="col-lg-6 text-right">
                <a href="javascript:void(0);" onclick="printPageArea('printableArea')" class="hidden-print btn btn-sm btn-success rounded"><i class="fas fa-print"></i> Print Report</a>
            </div>
        </div>

        <table class="table table-striped table-bordered w-100 table-sm">
            <thead>
                <tr>
                    <th class="text-center">SL</th>
                    <th class="text-center">Warehouse</th>
                    <th class="text-center">Supplier</th>
                    <th class="text-center">Qutation No</th>
                    <th class="text-center">Purchase Date</th>
                    <th class="text-center">Subtotal</th>
                    <th class="text-center">Other Fee</th>
                    <th class="text-center">Discount</th>
                    <th class="text-center">Grand Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $sl = 1;
                    $grandSubTotal = 0;
                    $grandDiscount = 0;
                    $grandDeliveryFee = 0;
                    $grandTotal = 0;
                @endphp
                @foreach ($data as $item)
                @php
                    $grandSubTotal += $item->subtotal;
                    $grandDiscount += $item->calculated_discount_amount;
                    $grandDeliveryFee += $item->other_charge_amount;
                    $grandTotal += $item->total;
                @endphp
                <tr>
                    <td class="text-center">{{$sl++}}</td>
                    <td class="text-center">{{$item->warehouse_name}}</td>
                    <td class="text-center">{{$item->supplier_name}}</td>
                    <td class="text-center">{{$item->product_purchase_quotation_id}}</td>
                    <td class="text-center">{{$item->date}}</td>

                    <td class="text-right">{{number_format($item->subtotal, 2)}}</td>
                    <td class="text-right">{{number_format($item->other_charge_amount, 2)}}</td>
                    <td class="text-right">{{number_format($item->calculated_discount_amount, 2)}}</td>
                    <td class="text-right">{{number_format($item->total, 2)}}</td>
                </tr>
                @endforeach

                <tr>
                    <th colspan="7" class="text-right">Total : </th>
                    <th class="text-right">৳ {{number_format($grandSubTotal, 2)}}</th>
                    <th class="text-right">৳ {{number_format($grandDeliveryFee, 2)}}</th>
                    <th class="text-right">৳ {{number_format($grandDiscount, 2)}}</th>
                    <th class="text-right">৳ {{number_format($grandTotal, 2)}}</th>
                </tr>
            </tbody>
        </table>

    </div>
</div>
