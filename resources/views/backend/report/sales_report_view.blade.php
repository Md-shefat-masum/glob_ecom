<div class="card" id="printableArea">
    <div class="card-body">

        <div class="row pb-3">
            <div class="col-lg-6">
                <h4 class="card-title">Sales Report From <b style="color: #225ba5">{{date("d M, Y",strtotime($startDate))}}</b> To <b style="color: #225ba5">{{date("d M, Y",strtotime($endDate))}}</b></h4>
            </div>
            <div class="col-lg-6 text-right">
                <a href="javascript:void(0);" onclick="printPageArea('printableArea')" class="hidden-print btn btn-sm btn-success rounded"><i class="fas fa-print"></i> Print Report</a>
            </div>
        </div>

        <table class="table table-striped table-bordered w-100 table-sm">
            <thead>
                <tr>
                    <th class="text-center">SL</th>
                    <th class="text-center">Order No</th>
                    <th class="text-center">Order Date</th>
                    <th class="text-center">Order Status</th>
                    <th class="text-center">Payment Status</th>
                    <th class="text-center">Payment Method</th>
                    <th class="text-center">Sub Amount</th>
                    <th class="text-center">Discount</th>
                    <th class="text-center">Delivery Fee</th>
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
                    $grandSubTotal += $item->sub_total;
                    $grandDiscount += $item->discount;
                    $grandDeliveryFee += $item->delivery_fee;
                    $grandTotal += $item->total;
                @endphp
                <tr>
                    <td class="text-center">{{$sl++}}</td>
                    <td class="text-center">{{$item->order_no}}</td>
                    <td class="text-center">{{date("d-m-Y h:i:s a",strtotime($item->order_date))}}</td>
                    <td class="text-center">
                        <?php
                            if($item->order_status == 0){
                                echo '<span class="text-warning" style="padding: 2px 10px !important;">Pending</span>';
                            } elseif($item->order_status == 1) {
                                echo '<span class="text-info" style="padding: 2px 10px !important;">Approved</span>';
                            } elseif($item->order_status == 2) {
                                echo '<span class="text-info" style="padding: 2px 10px !important;">Intransit</span>';
                            } elseif($item->order_status == 3) {
                                echo '<span class="text-success" style="padding: 2px 10px !important;">Delivered</span>';
                            } elseif($item->order_status == 5) {
                                echo '<span class="alert alert-dark" style="padding: 2px 10px !important;">Picked</span>';
                            } else {
                                echo '<span class="text-danger" style="padding: 2px 10px !important;">Cancelled</span>';
                            }

                        ?>
                    </td>
                    <td class="text-center">
                        <?php
                            if($item->payment_status == 0){
                                echo '<span class="text-danger" style="padding: 2px 10px !important;">Unpaid</span>';
                            } elseif($item->payment_status == 1) {
                                echo '<span class="text-success" style="padding: 2px 10px !important;">Success</span>';
                            } else {
                                echo '<span class="text-danger" style="padding: 2px 10px !important;">Failed</span>';
                            }
                        ?>
                    </td>
                    <td class="text-center">
                        <?php
                            if($item->payment_method == NULL){
                                echo '<span class="text-danger" style="padding: 2px 10px !important;">Not Paid</span>';
                            } elseif($item->payment_method == 1) {
                                echo '<span class="text-info" style="padding: 2px 10px !important;">COD</span>';
                            } elseif($item->payment_method == 2) {
                                echo '<span class="text-success" style="padding: 2px 10px !important;">bKash</span>';
                            } elseif($item->payment_method == 3) {
                                echo '<span class="text-success" style="padding: 2px 10px !important;">Nagad</span>';
                            } else {
                                echo '<span class="text-success" style="padding: 2px 10px !important;">Card</span>';
                            }
                        ?>
                    </td>
                    <td class="text-right">{{number_format($item->sub_total, 2)}}</td>
                    <td class="text-right">{{number_format($item->discount, 2)}}</td>
                    <td class="text-right">{{number_format($item->delivery_fee, 2)}}</td>
                    <td class="text-right">{{number_format($item->total, 2)}}</td>
                </tr>
                @endforeach


                <tr>
                    <th colspan="6" class="text-right">Total : </th>
                    <th class="text-right">৳ {{number_format($grandSubTotal, 2)}}</th>
                    <th class="text-right">৳ {{number_format($grandDiscount, 2)}}</th>
                    <th class="text-right">৳ {{number_format($grandDeliveryFee, 2)}}</th>
                    <th class="text-right">৳ {{number_format($grandTotal, 2)}}</th>
                </tr>
            </tbody>
        </table>

    </div>
</div>
