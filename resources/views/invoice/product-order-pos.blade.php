<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>POS Invoice #{{ $order->order_code }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        @page {
            size: 80mm auto;
            margin: 5mm;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 0;
        }

        .pos-container {
            width: 100%;
            max-width: 80mm;
            margin: 0 auto;
        }

        .pos-header {
            text-align: center;
            margin-bottom: 6px;
        }

        .pos-header h4 {
            margin: 0;
            font-size: 14px;
            font-weight: 700;
        }

        .pos-header p {
            margin: 2px 0;
        }

        .pos-qr {
            text-align: center;
            margin: 6px 0;
        }

        .pos-qr img {
            width: 70px;
            height: 70px;
        }

        .pos-meta,
        .pos-customer {
            margin-bottom: 6px;
        }

        .pos-meta table,
        .pos-customer table {
            width: 100%;
            border-collapse: collapse;
        }

        .pos-meta td,
        .pos-customer td {
            padding: 1px 0;
            vertical-align: top;
        }

        .pos-products {
            margin-top: 4px;
        }

        .pos-products table {
            width: 100%;
            border-collapse: collapse;
        }

        .pos-products th,
        .pos-products td {
            padding: 2px 0;
            border-bottom: 1px dashed #ccc;
        }

        .pos-products th {
            font-weight: 700;
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .pos-totals {
            margin-top: 6px;
        }

        .pos-totals table {
            width: 100%;
            border-collapse: collapse;
        }

        .pos-totals td {
            padding: 2px 0;
        }

        .pos-footer {
            margin-top: 8px;
            text-align: center;
            border-top: 1px dashed #ccc;
            padding-top: 4px;
            font-size: 10px;
        }

        .no-print {
            margin: 5px;
            text-align: center;
        }

        .no-print button {
            padding: 4px 8px;
            font-size: 11px;
        }

        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()">Print</button>
        <a href="{{ route('order.invoice', $order->slug) }}" target="_blank"
           style="display: inline-block; margin-left: 8px; padding: 4px 8px; background:#4a90e2; color:#fff; border-radius:4px; text-decoration:none;">
            View Full Invoice
        </a>
    </div>

    <div class="pos-container">
        <div class="pos-header">
            <h4>{{ $company['name'] }}</h4>
            <p>{{ $company['address'] }}</p>
            <p>{{ $company['phone'] }}</p>
        </div>

        <div class="pos-qr">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=90x90&data={{ urlencode($qrData) }}" alt="QR">
        </div>

        <div class="pos-meta">
            <table>
                <tr>
                    <td>Invoice</td>
                    <td class="text-right">#{{ $order->order_code }}</td>
                </tr>
                <tr>
                    <td>Date</td>
                    <td class="text-right">{{ \Carbon\Carbon::parse($order->sale_date)->format('d M Y H:i') }}</td>
                </tr>
            </table>
        </div>

        <div class="pos-customer">
            <table>
                <tr>
                    <td>Customer</td>
                    <td class="text-right">{{ $order->customer->name ?? 'Walk-in' }}</td>
                </tr>
                @if($order->customer && $order->customer->phone)
                    <tr>
                        <td>Phone</td>
                        <td class="text-right">{{ $order->customer->phone }}</td>
                    </tr>
                @endif
            </table>
        </div>

        <div class="pos-products">
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th class="text-right">Qty</th>
                        <th class="text-right">Price</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->order_products as $item)
                        <tr>
                            <td>
                                {{ $item->product_name }}
                                @if($item->variant)
                                    <br><small>{{ $item->variant->name }}</small>
                                @endif
                            </td>
                            <td class="text-right">{{ $item->qty }}</td>
                            <td class="text-right">{{ number_format($item->sale_price, 2) }}</td>
                            <td class="text-right">{{ number_format($item->total_price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="pos-totals">
            <table>
                <tr>
                    <td>Subtotal</td>
                    <td class="text-right">{{ number_format($order->subtotal, 2) }}</td>
                </tr>
                @if($order->other_charge_amount > 0)
                    <tr>
                        <td>Other Charges</td>
                        <td class="text-right">{{ number_format($order->other_charge_amount, 2) }}</td>
                    </tr>
                @endif
                @if($order->calculated_discount_amount > 0)
                    <tr>
                        <td>Discount</td>
                        <td class="text-right">-{{ number_format($order->calculated_discount_amount, 2) }}</td>
                    </tr>
                @endif
                @if($order->decimal_round_off != 0)
                    <tr>
                        <td>Round Off</td>
                        <td class="text-right">{{ number_format($order->decimal_round_off, 2) }}</td>
                    </tr>
                @endif
                <tr>
                    <td><strong>Grand Total</strong></td>
                    <td class="text-right"><strong>{{ number_format($order->total, 2) }}</strong></td>
                </tr>
                <tr>
                    <td>Paid</td>
                    <td class="text-right">{{ number_format($order->paid_amount, 2) }}</td>
                </tr>
                <tr>
                    <td>Due</td>
                    <td class="text-right">{{ number_format($order->due_amount, 2) }}</td>
                </tr>
            </table>
        </div>

        <div class="pos-footer">
            <p>Scan the QR to view this invoice online.</p>
            <p>Thank you for your purchase.</p>
            <p>
                <a href="{{ route('order.invoice', $order->slug) }}" target="_blank" style="text-decoration: none;">
                    View Full Invoice
                </a>
            </p>
        </div>
    </div>
</body>
</html>


