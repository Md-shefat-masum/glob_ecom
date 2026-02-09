<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Invoice - Order #{{ $order->order_no }}</title>
    <style>
        /* Thermal Printer CSS - 58mm/80mm width optimized */
        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            @page {
                margin: 0;
            }
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.2;
            margin: 0;
            padding: 10px;
            max-width: 300px;
            /* 80mm thermal printer width */
            background: white;
        }

        .invoice-header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .company-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .company-info {
            font-size: 10px;
            margin-bottom: 2px;
        }

        .invoice-details {
            margin-bottom: 10px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }

        .invoice-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
            font-size: 11px;
        }

        .customer-info {
            margin-bottom: 10px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }

        .items-table {
            width: 100%;
            margin-bottom: 10px;
        }

        .item-row {
            border-bottom: 1px dotted #ccc;
            padding: 5px 0;
        }

        .item-name {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 2px;
        }

        .item-details {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
        }

        .totals {
            border-top: 1px dashed #000;
            padding-top: 10px;
            margin-top: 10px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            font-size: 11px;
        }

        .grand-total {
            font-weight: bold;
            font-size: 13px;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 5px 0;
            margin: 5px 0;
        }

        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 10px;
            border-top: 1px dashed #000;
            padding-top: 10px;
        }

        .center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="invoice-header">
        <div class="company-name">{{ $generalInfo->company_name ?? 'Your Company Name' }}</div>
        @if ($generalInfo && $generalInfo->address)
            <div class="company-info">{{ $generalInfo->address }}</div>
        @endif
        @if ($generalInfo && $generalInfo->contact)
            <div class="company-info">Phone: {{ $generalInfo->contact }}</div>
        @endif
        @if ($generalInfo && $generalInfo->email)
            <div class="company-info">Email: {{ $generalInfo->email }}</div>
        @endif
    </div>

    <div class="invoice-details">
        <div class="invoice-row">
            <span>Invoice No:</span>
            <span class="bold">{{ $order->invoice_no ?? 'INV-' . $order->order_no }}</span>
        </div>
        <div class="invoice-row">
            <span>Order No:</span>
            <span class="bold">{{ $order->order_no }}</span>
        </div>
        <div class="invoice-row">
            <span>Date:</span>
            <span>{{ date('d/m/Y H:i', strtotime($order->order_date)) }}</span>
        </div>
        <div class="invoice-row">
            <span>Payment:</span>
            <span>COD</span>
        </div>
    </div>

    @if ($order->shippingInfo)
        <div class="customer-info">
            <div class="bold center">CUSTOMER DETAILS</div>
            <div class="invoice-row">
                <span>Name:</span>
                <span>{{ $order->shippingInfo->full_name }}</span>
            </div>
            <div class="invoice-row">
                <span>Phone:</span>
                <span>{{ $order->shippingInfo->phone }}</span>
            </div>
            @if ($order->shippingInfo->address)
                <div style="margin-top: 5px; font-size: 10px;">
                    <span class="bold">Address: </span>{{ $order->shippingInfo->address }}
                    @if ($order->shippingInfo->city)
                        , {{ $order->shippingInfo->city }}
                    @endif
                </div>
            @endif
        </div>
    @endif

    <div class="items-table">
        <div class="bold center" style="border-bottom: 1px solid #000; padding-bottom: 5px; margin-bottom: 10px;">
            ITEMS ORDERED
        </div>

        @foreach ($orderDetails as $item)
            <div class="item-row">
                <div class="item-name">{{ $item->product_name }}</div>
                @if ($item->color_name || $item->size_name)
                    <div style="font-size: 9px; color: #666;">
                        @if ($item->color_name)
                            Color: {{ $item->color_name }}
                        @endif
                        @if ($item->color_name && $item->size_name)
                            |
                        @endif
                        @if ($item->size_name)
                            Size: {{ $item->size_name }}
                        @endif
                    </div>
                @endif
                <div class="item-details">
                    <span>{{ $item->qty }} {{ $item->unit_name ?? 'pc' }} ×
                        ৳{{ number_format($item->unit_price, 2) }}</span>
                    <span class="bold">৳{{ number_format($item->total_price, 2) }}</span>
                </div>
            </div>
        @endforeach
    </div>

    <div class="totals">
        <div class="total-row">
            <span>Subtotal:</span>
            <span>৳{{ number_format($order->sub_total, 2) }}</span>
        </div>

        @if ($order->discount > 0)
            <div class="total-row">
                <span>Discount:</span>
                <span>৳{{ number_format($order->discount, 2) }}</span>
            </div>
        @endif

        @if ($order->coupon_price > 0)
            <div class="total-row">
                <span>Coupon:</span>
                <span>
                    {{ $order->coupon_price > 0 ? '৳' . number_format(abs($order->coupon_price), 2) : '' }}
                </span>
            </div>
        @endif

        @if ($order->delivery_fee > 0)
            <div class="total-row">
                <span>Delivery Fee:</span>
                <span>৳{{ number_format($order->delivery_fee, 2) }}</span>
            </div>
        @endif
        @if ($order->round_off)
            <div class="total-row">
                <span>Round Off:</span>
                <span>
                    {{ $order->round_off > 0 ? '৳' . number_format(abs($order->round_off), 2) : '' }}
                </span>
            </div>
        @endif
        <div class="total-row grand-total">
            <span>TOTAL:</span>
            <span>৳{{ number_format($order->total, 2) }}</span>
        </div>
    </div>

    @if ($order->order_note)
        <div style="margin-top: 10px; border-top: 1px dashed #000; padding-top: 10px;">
            <div class="bold">Note:</div>
            <div style="font-size: 10px;">{{ $order->order_note }}</div>
        </div>
    @endif

    <div class="footer">
        <div>Thank you for your purchase!</div>
        <div style="margin-top: 5px;">
            {{ $order->invoice_date ? date('d/m/Y H:i:s', strtotime($order->invoice_date)) : date('d/m/Y H:i:s') }}
        </div>
        {{-- @if ($order->delivery_method == 2)
            <div style="margin-top: 5px; font-size: 9px;">
                Expected Delivery: {{ date('d/m/Y', strtotime($order->estimated_dd)) }}
            </div>
        @endif --}}
    </div>
</body>

</html>
