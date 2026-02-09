<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Invoice #{{ $order->order_code }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                margin: 0;
                padding: 10px;
            }
            .invoice-container {
                box-shadow: none !important;
                margin: 0 !important;
                padding: 15px !important;
            }
            .page-break {
                page-break-inside: avoid;
            }
            thead {
                display: table-header-group;
            }
            tfoot {
                display: table-footer-group;
            }
        }

        @page {
            size: A4;
            margin: 10mm;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            padding: 15px;
            font-size: 13px;
        }

        .invoice-container {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
            padding: 20px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        /* Compact Header */
        .invoice-header {
            display: grid;
            grid-template-columns: 100px 1fr 100px;
            gap: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
            margin-bottom: 10px;
            align-items: start;
        }

        .company-logo img {
            width: 90px;
            height: auto;
        }

        .company-info {
            padding: 0 10px;
        }

        .company-info h4 {
            margin: 0 0 5px 0;
            font-size: 16px;
            color: #333;
            font-weight: 700;
        }

        .company-info p {
            margin: 0;
            font-size: 11px;
            line-height: 1.4;
            color: #555;
        }

        .qr-code-header {
            text-align: center;
        }

        .qr-code-header img {
            width: 90px;
            height: 90px;
        }

        /* Invoice and Customer Info Row */
        .info-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }

        .info-section {
            font-size: 12px;
        }

        .info-section h6 {
            font-size: 13px;
            font-weight: 700;
            color: #333;
            margin: 0 0 6px 0;
            padding-bottom: 4px;
            border-bottom: 1px solid #eee;
        }

        .info-section p {
            margin: 3px 0;
            line-height: 1.5;
            color: #555;
        }

        .info-section strong {
            color: #333;
            font-weight: 600;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .status-invoiced, .status-completed {
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

        /* Products Table Section */
        .products-section {
            margin-bottom: 12px;
        }

        .products-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .products-table thead {
            background: #333;
            color: white;
        }

        .products-table thead th {
            padding: 6px 8px;
            text-align: left;
            font-weight: 600;
            font-size: 11px;
            border: 1px solid #222;
        }

        .products-table tbody tr {
            border-bottom: 1px solid #e0e0e0;
        }

        .products-table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        .products-table tbody td {
            padding: 5px 8px;
            color: #555;
            border: 1px solid #e0e0e0;
        }

        .products-table tbody td:last-child,
        .products-table thead th:last-child {
            text-align: right;
        }

        .products-table thead th:first-child,
        .products-table tbody td:first-child {
            text-align: center;
        }

        /* Totals Section */
        .totals-section {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 15px;
            margin-top: 15px;
        }

        .grand-total-text {
            font-size: 13px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 4px;
        }

        .grand-total-text strong {
            display: block;
            font-size: 11px;
            color: #666;
            margin-bottom: 3px;
        }

        .totals-box {
            border: 2px solid #333;
            padding: 10px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            font-size: 12px;
        }

        .total-row.subtotal {
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 5px;
        }

        .total-row.grand-total {
            border-top: 2px solid #333;
            padding-top: 6px;
            margin-top: 6px;
            font-size: 14px;
            font-weight: 700;
            color: #333;
        }

        /* Payment Information */
        .payment-section {
            margin-top: 12px;
            padding: 10px;
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .payment-section h6 {
            font-size: 13px;
            font-weight: 700;
            color: #333;
            margin: 0 0 8px 0;
        }

        .payment-list {
            list-style: none;
            padding: 0;
            margin: 0;
            font-size: 12px;
        }

        .payment-list li {
            padding: 4px 0;
            border-bottom: 1px dotted #ddd;
            display: flex;
            justify-content: space-between;
        }

        .payment-list li:last-child {
            border-bottom: none;
        }

        .payment-totals {
            margin-top: 8px;
            padding-top: 8px;
            border-top: 2px solid #ddd;
        }

        .payment-totals div {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
            font-size: 12px;
            font-weight: 600;
        }

        /* Footer */
        .footer-note {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 10px;
        }

        .footer-note p {
            margin: 3px 0;
        }

        /* Action Buttons */
        .action-buttons {
            margin-bottom: 15px;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .btn-custom {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
        }

        .btn-print {
            background: #4A90E2;
            color: white;
        }

        .btn-print:hover {
            background: #357ABD;
            color: white;
        }

        .btn-pdf {
            background: #dc3545;
            color: white;
        }

        .btn-pdf:hover {
            background: #c82333;
            color: white;
        }

        .btn-email {
            background: #28a745;
            color: white;
        }

        .btn-email:hover {
            background: #218838;
            color: white;
        }

        .note-section {
            margin-top: 10px;
            padding: 8px;
            background: #fff9e6;
            border-left: 3px solid #ffc107;
            font-size: 11px;
        }

        .note-section p {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="action-buttons no-print">
        <button onclick="window.print()" class="btn-custom btn-print">
            <i class="fas fa-print"></i> Print
        </button>
        <a href="{{ route('order.invoice.pdf', $order->slug) }}" target="_blank" class="btn-custom btn-pdf">
            <i class="fas fa-file-pdf"></i> Download PDF
        </a>
        <button onclick="emailInvoice()" class="btn-custom btn-email">
            <i class="fas fa-envelope"></i> Email Invoice
        </button>
    </div>

    <div class="invoice-container">
        <!-- Compact Header: Logo | Company Address | QR Code -->
        <div class="invoice-header">
            <div class="company-logo">
                <img src="{{ url($company['logo']) }}" alt="{{ $company['name'] }}" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2290%22 height=%2290%22%3E%3Crect fill=%22%234A90E2%22 width=%2290%22 height=%2290%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 fill=%22white%22 font-family=%22Arial%22 font-size=%2218%22 font-weight=%22bold%22%3EBME%3C/text%3E%3C/svg%3E'">
            </div>
            
            <div class="company-info">
                <h4>{{ $company['name'] }}</h4>
                <p>
                    {{ $company['address'] }}<br>
                    <i class="fas fa-phone"></i> {{ $company['phone'] }} | 
                    <i class="fas fa-envelope"></i> {{ $company['email'] }}<br>
                    <i class="fas fa-globe"></i> {{ $company['website'] }}
                </p>
            </div>
            
            <div class="qr-code-header">
                @php
                    // Ensure we have a full absolute URL for QR code
                    $qrUrl = $qrData;
                    if (!filter_var($qrUrl, FILTER_VALIDATE_URL)) {
                        // If not a full URL, make it absolute
                        $qrUrl = url($qrUrl);
                    }
                @endphp
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=90x90&data={{ urlencode($qrUrl) }}" alt="QR Code">
            </div>
        </div>

        <!-- Invoice Info and Customer Info Row -->
        <div class="info-row">
            <div class="info-section">
                <h6>INVOICE DETAILS</h6>
                <p><strong>Invoice No:</strong> {{ $order->order_code }}</p>
                <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($order->sale_date)->format('d M, Y') }}</p>
                @if($order->due_date)
                <p><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($order->due_date)->format('d M, Y') }}</p>
                @endif
                <p><strong>Status:</strong> <span class="status-badge status-{{ strtolower($order->order_status) }}">{{ ucfirst($order->order_status) }}</span></p>
                @if($order->reference)
                <p><strong>Reference:</strong> {{ $order->reference }}</p>
                @endif
                @if($order->warehouse)
                <p><strong>Warehouse:</strong> {{ $order->warehouse->name }}</p>
                @endif
            </div>
            
            <div class="info-section">
                <h6>CUSTOMER INFORMATION</h6>
                <p><strong>Name:</strong> {{ $order->customer->name ?? 'N/A' }}</p>
                <p><strong>Phone:</strong> {{ $order->customer->phone ?? 'N/A' }}</p>
                @if($order->customer->email)
                <p><strong>Email:</strong> {{ $order->customer->email }}</p>
                @endif
                @if($order->customer->address)
                <p><strong>Address:</strong> {{ $order->customer->address }}</p>
                @endif
            </div>
        </div>

        <!-- Products Section -->
        <div class="products-section">
            <table class="products-table">
                <thead>
                    <tr>
                        <th style="width: 4%;">#</th>
                        <th style="width: 38%;">Product Name</th>
                        <th style="width: 12%;">Unit Price</th>
                        <th style="width: 8%;">Qty</th>
                        <th style="width: 10%;">Disc (%)</th>
                        <th style="width: 10%;">Tax (%)</th>
                        <th style="width: 18%;">Total</th>
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
                                <br><small class="text-muted">{{ $variantName ?: $product->variant->name }}</small>
                            @elseif($product->unitPrice)
                                <br><small class="text-muted">Unit: {{ $product->unitPrice->unit_label }}</small>
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
        </div>

        <!-- Totals Section: Grand Total Text Left | Totals Right -->
        <div class="totals-section">
            <div class="grand-total-text">
                <strong>Grand Total (in words):</strong>
                {{ numberToWords($order->total) }} Taka Only
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

        <!-- Payment Information as List -->
        @if($order->payments && is_array($order->payments))
        <div class="payment-section">
            <h6><i class="fas fa-money-bill-wave"></i> Payment Information</h6>
            <ul class="payment-list">
                @foreach($order->payments as $method => $amount)
                    @if($amount > 0 && !in_array($method, ['total_paid', 'total_due']))
                    <li>
                        <span><i class="fas fa-check-circle" style="color: #28a745;"></i> {{ ucfirst($method) }}</span>
                        <strong>৳{{ number_format($amount, 2) }}</strong>
                    </li>
                    @endif
                @endforeach
            </ul>
            <div class="payment-totals">
                <div>
                    <span>Total Paid:</span>
                    <span style="color: #28a745;">৳{{ number_format($order->paid_amount, 2) }}</span>
                </div>
                @if($order->due_amount > 0)
                <div>
                    <span>Amount Due:</span>
                    <span style="color: #dc3545;">৳{{ number_format($order->due_amount, 2) }}</span>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Note Section -->
        @if($order->note)
        <div class="note-section">
            <p><strong>Note:</strong> {{ $order->note }}</p>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer-note">
            <p>Thank you for your business!</p>
            <p>This is a computer-generated invoice and does not require a signature.</p>
            <p>Generated on {{ now()->format('d M, Y h:i A') }}</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function emailInvoice() {
            const email = prompt('Enter email address to send invoice:', '{{ $order->customer->email ?? "" }}');
            if (email) {
                fetch('{{ route("order.invoice.email", $order->slug) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ email: email })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Invoice sent successfully to ' + email);
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('Error sending email: ' + error.message);
                });
            }
        }
    </script>
</body>
</html>

