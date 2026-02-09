@extends('backend.master')

@section('header_css')
    <style>
        .invoice-box {
            max-width: 900px;
            margin: 30px auto;
            padding: 40px 30px 30px 30px;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.10);
            font-size: 16px;
            line-height: 1.6;
            font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
            color: #333;
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 18px;
            margin-bottom: 24px;
        }

        .invoice-header .logo {
            max-width: 180px;
        }

        .invoice-header .company-details {
            text-align: right;
        }

        .invoice-title {
            font-size: 2.2rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .invoice-meta {
            font-size: 1rem;
            color: #666;
        }

        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .invoice-info .info-block {
            width: 48%;
            background: #f8fafc;
            border-radius: 8px;
            padding: 16px 18px;
        }

        .invoice-info .info-block strong {
            color: #2d3748;
        }

        .table-invoice {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        .table-invoice th,
        .table-invoice td {
            padding: 10px 8px;
            border-bottom: 1px solid #f0f0f0;
        }

        .table-invoice th {
            background: #f1f5f9;
            color: #2d3748;
            font-weight: 600;
            font-size: 1rem;
        }

        .table-invoice td {
            font-size: 1rem;
        }

        .table-invoice .item-desc {
            color: #444;
            font-size: 0.97rem;
        }

        .table-invoice .item-meta {
            color: #888;
            font-size: 0.92rem;
        }

        .totals-table {
            width: 100%;
            margin-top: 10px;
        }

        .totals-table td {
            padding: 7px 8px;
            font-size: 1rem;
        }

        .totals-table .label {
            color: #666;
        }

        .totals-table .value {
            text-align: right;
            font-weight: 500;
        }

        .totals-table .grand-total-row td {
            font-size: 1.2rem;
            font-weight: 700;
            color: #1a8917;
            border-top: 2px solid #e2e8f0;
            background: #f6fff7;
        }

        .invoice-note {
            margin-top: 24px;
            background: #f8fafc;
            border-left: 4px solid #3182ce;
            padding: 14px 18px;
            border-radius: 6px;
            color: #2d3748;
        }

        .invoice-footer {
            text-align: center;
            margin-top: 32px;
            color: #888;
            font-size: 1rem;
        }

        .invoice-footer .fa {
            color: #1a8917;
            margin-right: 6px;
        }

        @media (max-width: 700px) {
            .invoice-box {
                padding: 10px;
            }

            .invoice-header,
            .invoice-info {
                flex-direction: column;
                align-items: flex-start;
            }

            .invoice-info .info-block {
                width: 100%;
                margin-bottom: 10px;
            }
        }
    </style>
@endsection

@section('page_title')
    Invoice - {{ $order->invoice_no }}
@endsection

@section('page_heading')
    Invoice Details
@endsection

@section('content')
    <div class="invoice-box">
        <div class="invoice-header">
            <div class="logo">
                @if ($generalInfo && $generalInfo->logo)
                    <img src="{{ url($generalInfo->logo) }}" style="max-width: 250px;">
                @else
                    <span class="invoice-title">{{ $generalInfo->company_name ?? 'Company Name' }}</span>
                @endif
            </div>
            <div class="company-details">
                <div class="invoice-title">INVOICE</div>
                <div class="invoice-meta">
                    <span><i class="fa fa-hashtag"></i> Invoice #: <strong>{{ $order->invoice_no }}</strong></span><br>
                    <span><i class="fa fa-file-alt"></i> Order #: <strong>{{ $order->order_no }}</strong></span><br>
                    <span><i class="fa fa-calendar"></i> Invoice Date: {{ date('d M Y', strtotime($order->invoice_date)) }}
                    </span><br>
                    <span><i class="fa fa-calendar-check"></i> Order Date:
                        {{ date('d M Y', strtotime($order->order_date)) }}
                    </span>
                </div>
            </div>
        </div>
        <div class="invoice-info">
            <div class="info-block">
                <strong>From:</strong><br>
                {{ $generalInfo->company_name ?? 'Company Name' }}<br>
                {{ $generalInfo->address ?? 'Company Address' }}<br>
                Phone: {{ $generalInfo->phone ?? 'Phone Number' }}<br>
                Email: {{ $generalInfo->email ?? 'Email Address' }}
            </div>
            <div class="info-block text-right" >
                <strong>Bill To:</strong><br>
                {{ $order->shippingInfo->full_name ?? 'Customer Name' }}<br>
                {{ $order->shippingInfo->address ?? '' }}<br>
                {{ $order->shippingInfo->city ?? '' }}, {{ $order->shippingInfo->thana ?? '' }}<br>
                Phone: {{ $order->shippingInfo->phone ?? '' }}<br>
                Email: {{ $order->shippingInfo->email ?? '' }}
            </div>
        </div>
        <table class="table-invoice">
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="text-right">Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orderDetails as $index => $detail)
                    <tr>
                        <td>
                            <div class="item-desc"><strong>{{ $detail->product_name }}</strong></div>
                            @if ($detail->product_code)
                                <div class="item-meta">Code: {{ $detail->product_code }}</div>
                            @endif
                            @if ($detail->color_name || $detail->size_name)
                                <div class="item-meta">
                                    @if ($detail->color_name)
                                        Color: {{ $detail->color_name }}
                                    @endif
                                    @if ($detail->size_name)
                                        | Size: {{ $detail->size_name }}
                                    @endif
                                </div>
                            @endif
                            <div class="item-meta">Qty: {{ $detail->qty }} {{ $detail->unit_name ?? 'pcs' }} ×
                                ৳{{ number_format($detail->unit_price, 2) }}</div>
                        </td>
                        <td class="text-right">৳{{ number_format($detail->total_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <table class="totals-table">
            <tr>
                <td class="label" style="text-align:right; width:70%">Subtotal</td>
                <td class="value" style="text-align:right; width:30%">৳{{ number_format($order->sub_total, 2) }}</td>
            </tr>
            @if ($order->discount > 0)
            <tr>
                <td class="label" style="text-align:right;">Discount</td>
                <td class="value" style="text-align:right;">৳{{ number_format($order->discount, 2) }}</td>
            </tr>
            @endif
            @if ($order->coupon_price > 0)
            <tr>
                <td class="label" style="text-align:right;">Coupon</td>
                <td class="value" style="text-align:right;">৳{{ number_format(abs($order->coupon_price), 2) }}</td>
            </tr>
            @endif
            @if ($order->delivery_fee > 0)
            <tr>
                <td class="label" style="text-align:right;">Delivery Fee</td>
                <td class="value" style="text-align:right;">৳{{ number_format($order->delivery_fee, 2) }}</td>
            </tr>
            @endif
            @if ($order->vat > 0)
            <tr>
                <td class="label" style="text-align:right;">VAT</td>
                <td class="value" style="text-align:right;">৳{{ number_format($order->vat, 2) }}</td>
            </tr>
            @endif
            @if ($order->round_off)
            <tr>
                <td class="label" style="text-align:right;">Round Off</td>
                <td class="value" style="text-align:right;">{{ $order->round_off > 0 ? '৳' . number_format(abs($order->round_off), 2) : '' }}</td>
            </tr>
            @endif
            @if ($order->tax > 0)
            <tr>
                <td class="label" style="text-align:right;">Tax</td>
                <td class="value" style="text-align:right;">৳{{ number_format($order->tax, 2) }}</td>
            </tr>
            @endif
            <tr class="grand-total-row">
                <td class="label" style="text-align:right;">Total</td>
                <td class="value" style="text-align:right;">৳{{ number_format($order->total, 2) }}</td>
            </tr>
        </table>
        @if ($order->order_note)
            <div class="invoice-note">
                <strong>Order Note:</strong><br>
                {{ $order->order_note }}
            </div>
        @endif
        <div class="invoice-footer">
            <i class="fa fa-smile"></i> Thank you for your business!<br>
            <span style="font-size: 0.95em;">Printed on {{ date('d M Y, h:i A') }}</span>
        </div>
    </div>
@endsection
