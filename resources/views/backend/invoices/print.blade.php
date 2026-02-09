<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $order->invoice_no }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 14px;
            line-height: 1.4;
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, .15);
            font-size: 16px;
            line-height: 24px;
            color: #555;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 35px;
            line-height: 35px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
            padding: 10px 5px;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
            padding: 8px 5px;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
            font-size: 18px;
            padding: 10px 5px;
        }

        .text-center {
            text-align: center;
        }

        .mt-4 {
            margin-top: 20px;
        }

        @media print {
            body {
                margin: 0;
                padding: 10px;
            }
            .invoice-box {
                box-shadow: none;
                border: none;
            }
        }
    </style>
</head>
<body onload="window.print();">
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">
                                @if($generalInfo && $generalInfo->logo)
                                    <img src="{{ url('uploads/company_logo/' . $generalInfo->logo) }}" style="width:100%; max-width:180px;">
                                @else
                                    <h2>{{ $generalInfo->company_name ?? 'Company Name' }}</h2>
                                @endif
                            </td>
                            <td>
                                <strong>Invoice #:</strong> {{ $order->invoice_no }}<br>
                                <strong>Order #:</strong> {{ $order->order_no }}<br>
                                <strong>Invoice Date:</strong> {{ date('d M Y', strtotime($order->invoice_date)) }}<br>
                                <strong>Order Date:</strong> {{ date('d M Y', strtotime($order->order_date)) }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                <strong>{{ $generalInfo->company_name ?? 'Company Name' }}</strong><br>
                                {{ $generalInfo->address ?? 'Company Address' }}<br>
                                <strong>Phone:</strong> {{ $generalInfo->phone ?? 'Phone Number' }}<br>
                                <strong>Email:</strong> {{ $generalInfo->email ?? 'Email Address' }}
                            </td>
                            <td>
                                <strong>Bill To:</strong><br>
                                <strong>{{ $order->shippingInfo->full_name ?? 'Customer Name' }}</strong><br>
                                {{ $order->shippingInfo->address ?? '' }}<br>
                                {{ $order->shippingInfo->city ?? '' }}@if($order->shippingInfo->thana), {{ $order->shippingInfo->thana }}@endif<br>
                                <strong>Phone:</strong> {{ $order->shippingInfo->phone ?? '' }}<br>
                                @if($order->shippingInfo->email)<strong>Email:</strong> {{ $order->shippingInfo->email }}<br>@endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="heading">
                <td><strong>Item Description</strong></td>
                <td><strong>Amount</strong></td>
            </tr>

            @foreach($orderDetails as $index => $detail)
            <tr class="item {{ $loop->last ? 'last' : '' }}">
                <td>
                    <strong>{{ $detail->product_name }}</strong>
                    @if($detail->product_code)
                        <br><small>Code: {{ $detail->product_code }}</small>
                    @endif
                    @if($detail->color_name || $detail->size_name)
                        <br><small>
                            @if($detail->color_name) Color: {{ $detail->color_name }} @endif
                            @if($detail->size_name) @if($detail->color_name) | @endif Size: {{ $detail->size_name }} @endif
                        </small>
                    @endif
                    <br><small>Quantity: {{ $detail->qty }} {{ $detail->unit_name ?? 'pcs' }} × ৳{{ number_format($detail->unit_price, 2) }}</small>
                </td>
                <td><strong>৳{{ number_format($detail->total_price, 2) }}</strong></td>
            </tr>
            @endforeach

            <tr class="item">
                <td><strong>Subtotal</strong></td>
                <td><strong>৳{{ number_format($order->sub_total, 2) }}</strong></td>
            </tr>

            @if($order->discount > 0)
            <tr class="item">
                <td>Discount</td>
                <td>-৳{{ number_format($order->discount, 2) }}</td>
            </tr>
            @endif

            @if($order->delivery_fee > 0)
            <tr class="item">
                <td>Delivery Fee</td>
                <td>৳{{ number_format($order->delivery_fee, 2) }}</td>
            </tr>
            @endif

            @if($order->vat > 0)
            <tr class="item">
                <td>VAT</td>
                <td>৳{{ number_format($order->vat, 2) }}</td>
            </tr>
            @endif

            @if($order->tax > 0)
            <tr class="item">
                <td>Tax</td>
                <td>৳{{ number_format($order->tax, 2) }}</td>
            </tr>
            @endif

            <tr class="total">
                <td><strong>Grand Total</strong></td>
                <td><strong>৳{{ number_format($order->total, 2) }}</strong></td>
            </tr>
        </table>

        @if($order->order_note)
        <div class="mt-4">
            <strong>Order Note:</strong><br>
            {{ $order->order_note }}
        </div>
        @endif

        <div class="mt-4 text-center">
            <small><em>Thank you for your business!</em></small>
        </div>
    </div>
</body>
</html>
