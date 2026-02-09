<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->order_code }}</title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .invoice-header {
            display: table;
            width: 100%;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #4A90E2;
        }

        .company-info, .invoice-title {
            display: table-cell;
            vertical-align: top;
            width: 50%;
        }

        .company-logo img {
            max-width: 120px;
            height: auto;
        }

        .company-info h3 {
            margin: 10px 0 5px 0;
            color: #333;
        }

        .company-info p {
            margin: 2px 0;
            color: #666;
            font-size: 10pt;
        }

        .invoice-title {
            text-align: right;
        }

        .invoice-title h1 {
            color: #4A90E2;
            font-size: 32pt;
            margin: 0;
        }

        .invoice-title .invoice-number {
            font-size: 14pt;
            color: #666;
            margin-top: 5px;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 10pt;
            font-weight: 600;
            text-transform: uppercase;
            margin-top: 10px;
        }

        .status-invoiced {
            background: #d4edda;
            color: #155724;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-delivered {
            background: #d1ecf1;
            color: #0c5460;
        }

        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }

        .info-box {
            display: table-cell;
            width: 50%;
            padding: 15px;
            background: #f8f9fa;
            vertical-align: top;
        }

        .info-box:first-child {
            border-right: 10px solid white;
        }

        .info-box h4 {
            color: #4A90E2;
            margin-bottom: 10px;
            font-size: 12pt;
        }

        .info-box p {
            margin: 5px 0;
            font-size: 10pt;
        }

        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .products-table thead {
            background: #4A90E2;
            color: white;
        }

        .products-table th {
            padding: 10px;
            text-align: left;
            font-weight: 600;
            font-size: 10pt;
        }

        .products-table td {
            padding: 10px;
            border-bottom: 1px solid #e0e0e0;
            font-size: 10pt;
        }

        .products-table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        .products-table td:last-child,
        .products-table th:last-child {
            text-align: right;
        }

        .totals-section {
            display: table;
            width: 100%;
            margin-top: 20px;
        }

        .qr-section {
            display: table-cell;
            width: 40%;
            text-align: center;
            vertical-align: middle;
            padding: 20px;
        }

        .qr-section img {
            max-width: 150px;
            height: auto;
        }

        .totals-box {
            display: table-cell;
            width: 60%;
            padding: 15px;
            background: #f8f9fa;
        }

        .total-row {
            display: table;
            width: 100%;
            padding: 8px 0;
            font-size: 11pt;
        }

        .total-row span {
            display: table-cell;
        }

        .total-row span:last-child {
            text-align: right;
        }

        .total-row.subtotal {
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .total-row.grand-total {
            border-top: 2px solid #4A90E2;
            padding-top: 10px;
            margin-top: 10px;
            font-size: 14pt;
            font-weight: 700;
            color: #4A90E2;
        }

        .payment-info {
            margin-top: 20px;
            padding: 15px;
            background: #e8f4f8;
            border-left: 4px solid #28a745;
        }

        .payment-info h4 {
            color: #28a745;
            margin-bottom: 10px;
            font-size: 12pt;
        }

        .payment-methods {
            display: table;
            width: 100%;
        }

        .payment-method {
            display: table;
            width: 100%;
            background: white;
            padding: 8px;
            margin: 5px 0;
        }

        .payment-method span {
            display: table-cell;
        }

        .payment-method span:last-child {
            text-align: right;
            font-weight: 700;
        }

        .payment-summary {
            margin-top: 15px;
        }

        .payment-summary-row {
            display: table;
            width: 100%;
            padding: 8px;
            background: white;
            margin: 5px 0;
        }

        .payment-summary-row span {
            display: table-cell;
        }

        .payment-summary-row span:last-child {
            text-align: right;
            font-weight: 700;
        }

        .note-section {
            margin-top: 15px;
            padding: 10px;
            background: #f8f9fa;
        }

        .footer-note {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #e0e0e0;
            text-align: center;
            color: #666;
            font-size: 9pt;
        }

        @media print {
            body {
                padding: 0;
            }
            .invoice-container {
                max-width: 100%;
            }
        }

        @page {
            size: A4;
            margin: 15mm;
        }
    </style>
    <script>
        // Auto-trigger print dialog when page loads
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="invoice-header">
            <div class="company-info">
                <div class="company-logo">
                    <img src="{{ $company['logo'] }}" alt="{{ $company['name'] }}" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22120%22 height=%2250%22%3E%3Crect fill=%22%234A90E2%22 width=%22120%22 height=%2250%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 fill=%22white%22 font-family=%22Arial%22 font-size=%2218%22 font-weight=%22bold%22%3EBME%3C/text%3E%3C/svg%3E'">
                </div>
                <h3>{{ $company['name'] }}</h3>
                <p>{{ $company['address'] }}</p>
                <p>Phone: {{ $company['phone'] }}</p>
                <p>Email: {{ $company['email'] }}</p>
                <p>Website: {{ $company['website'] }}</p>
            </div>
            <div class="invoice-title">
                <h1>INVOICE</h1>
                <div class="invoice-number">#{{ $order->order_code }}</div>
                <div>
                    <span class="status-badge status-{{ strtolower($order->order_status) }}">
                        {{ ucfirst($order->order_status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Information Section -->
        <div class="info-section">
            <div class="info-box">
                <h4>Bill To:</h4>
                <p><strong>Name:</strong> {{ $order->customer->name ?? 'N/A' }}</p>
                <p><strong>Phone:</strong> {{ $order->customer->phone ?? 'N/A' }}</p>
                <p><strong>Email:</strong> {{ $order->customer->email ?? 'N/A' }}</p>
                <p><strong>Address:</strong> {{ $order->customer->address ?? 'N/A' }}</p>
            </div>
            <div class="info-box">
                <h4>Invoice Details:</h4>
                <p><strong>Invoice Date:</strong> {{ \Carbon\Carbon::parse($order->sale_date)->format('d M, Y') }}</p>
                @if($order->due_date)
                <p><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($order->due_date)->format('d M, Y') }}</p>
                @endif
                <p><strong>Reference:</strong> {{ $order->reference ?? 'N/A' }}</p>
                @if($order->warehouse)
                <p><strong>Warehouse:</strong> {{ $order->warehouse->name ?? 'N/A' }}</p>
                @endif
            </div>
        </div>

        <!-- Products Table -->
        <table class="products-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 40%;">Product</th>
                    <th style="width: 13%;">Unit Price</th>
                    <th style="width: 8%;">Qty</th>
                    <th style="width: 10%;">Discount</th>
                    <th style="width: 10%;">Tax</th>
                    <th style="width: 14%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->order_products as $index => $product)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $product->product_name }}</strong>
                        @if($product->variant)
                            @php
                                $variantValues = $product->variant->variant_values ?? [];
                                $variantName = $product->variant->combination_key ?? '';
                                
                                // Format variant_values to readable format
                                if (is_array($variantValues) && !empty($variantValues)) {
                                    $formattedValues = collect($variantValues)->map(function($value, $key) {
                                        return ucfirst(str_replace('_', ' ', $key)) . ': ' . $value;
                                    })->implode(' | ');
                                    
                                    if ($formattedValues) {
                                        $variantName = $formattedValues;
                                    }
                                }
                            @endphp
                            <br><small style="color: #666;">{{ $variantName ?: $product->variant->name }}</small>
                        @elseif($product->unitPrice)
                            <br><small style="color: #666;">Unit: {{ $product->unitPrice->unit_label }}</small>
                        @endif
                    </td>
                    <td>৳{{ number_format($product->sale_price, 2) }}</td>
                    <td>{{ $product->qty }}</td>
                    <td>{{ $product->discount_amount }}%</td>
                    <td>{{ $product->tax }}%</td>
                    <td>৳{{ number_format($product->total_price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals and QR Code Section -->
        <div class="totals-section">
            <div class="qr-section">
                @php
                    // Ensure we have a full absolute URL for QR code
                    $qrUrl = $qrData;
                    if (!filter_var($qrUrl, FILTER_VALIDATE_URL)) {
                        // If not a full URL, make it absolute
                        $qrUrl = url($qrUrl);
                    }
                @endphp
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($qrUrl) }}" alt="QR Code">
                <p style="margin-top: 10px; font-size: 9pt;"><strong>Scan for Quick Reference</strong></p>
            </div>
            <div class="totals-box">
                <div class="total-row subtotal">
                    <span>Subtotal:</span>
                    <span>৳{{ number_format($order->subtotal, 2) }}</span>
                </div>
                
                @if($order->other_charge_amount > 0)
                <div class="total-row">
                    <span>Other Charges:</span>
                    <span>৳{{ number_format($order->other_charge_amount, 2) }}</span>
                </div>
                @endif
                
                @if($order->calculated_discount_amount > 0)
                <div class="total-row" style="color: #dc3545;">
                    <span>Discount:</span>
                    <span>- ৳{{ number_format($order->calculated_discount_amount, 2) }}</span>
                </div>
                @endif
                
                @if($order->decimal_round_off != 0)
                <div class="total-row">
                    <span>Round Off:</span>
                    <span>৳{{ number_format($order->decimal_round_off, 2) }}</span>
                </div>
                @endif
                
                <div class="total-row grand-total">
                    <span>Grand Total:</span>
                    <span>৳{{ number_format($order->total, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Payment Information -->
        @if($order->payments && is_array($order->payments))
        <div class="payment-info">
            <h4>Payment Information</h4>
            <div class="payment-methods">
                @foreach($order->payments as $method => $amount)
                    @if($amount > 0 && !in_array($method, ['total_paid', 'total_due']))
                    <div class="payment-method">
                        <span>{{ ucfirst($method) }}</span>
                        <span>৳{{ number_format($amount, 2) }}</span>
                    </div>
                    @endif
                @endforeach
            </div>
            <div class="payment-summary">
                <div class="payment-summary-row" style="background: #d4edda;">
                    <span><strong>Total Paid:</strong></span>
                    <span style="color: #28a745;">৳{{ number_format($order->paid_amount, 2) }}</span>
                </div>
                @if($order->due_amount > 0)
                <div class="payment-summary-row" style="background: #f8d7da;">
                    <span><strong>Amount Due:</strong></span>
                    <span style="color: #dc3545;">৳{{ number_format($order->due_amount, 2) }}</span>
                </div>
                @endif
            </div>
        </div>
        @endif

        @if($order->note)
        <div class="note-section">
            <p><strong>Note:</strong> {{ $order->note }}</p>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer-note">
            <p style="margin: 8px 0;">Thank you for your business!</p>
            <p style="margin: 8px 0;">This is a computer-generated invoice and does not require a signature.</p>
            <p style="margin: 5px 0; color: #999; font-size: 8pt;">
                Generated on {{ now()->format('d M, Y h:i A') }}
            </p>
        </div>
    </div>
</body>
</html>

