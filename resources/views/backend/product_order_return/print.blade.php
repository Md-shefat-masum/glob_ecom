<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Return #{{ $return->return_code }}</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        @media print {
            body {
                margin: 0;
                padding: 10px;
            }
            .invoice-container {
                box-shadow: none !important;
                margin: 0 !important;
                padding: 15px !important;
            }
        }

        @page {
            size: A4;
            margin: 10mm;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: white;
            padding: 10px;
            font-size: 13px;
        }

        .invoice-container {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
            padding: 15px;
        }

        .invoice-header {
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .company-info h4 {
            margin: 0;
            font-size: 20px;
            color: #333;
            font-weight: 700;
        }

        .company-info p {
            margin: 2px 0;
            font-size: 12px;
            color: #555;
        }

        .return-badge {
            background: #dc3545;
            color: white;
            padding: 5px 15px;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            display: inline-block;
        }

        .products-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }

        .products-table th {
            background: #495057;
            color: white;
            padding: 10px;
            text-align: left;
            border: 1px solid #333;
        }

        .products-table td {
            padding: 8px;
            border: 1px solid #dee2e6;
        }

        .totals-section {
            margin-top: 20px;
            text-align: right;
        }

        .total-row {
            display: flex;
            justify-content: flex-end;
            margin: 5px 0;
        }

        .total-label {
            margin-right: 20px;
            min-width: 120px;
            text-align: right;
        }

        .total-amount {
            min-width: 150px;
            text-align: right;
            font-weight: 600;
        }

        .grand-total {
            font-size: 16px;
            font-weight: 700;
            border-top: 2px solid #333;
            padding-top: 8px;
            margin-top: 8px;
        }
    </style>
</head>
<body onload="window.print(); window.onafterprint = function(){ window.close(); }">
    <div class="invoice-container">
        <!-- Header -->
        <div class="invoice-header">
            <div class="row">
                <div class="col-8">
                    <div class="company-info">
                        <h4>{{ $company['name'] }}</h4>
                        <p><i class="fas fa-map-marker-alt"></i> {{ $company['address'] }}</p>
                        <p><i class="fas fa-phone"></i> {{ $company['phone'] }}</p>
                        <p><i class="fas fa-envelope"></i> {{ $company['email'] }}</p>
                    </div>
                </div>
                <div class="col-4 text-end">
                    <div class="return-badge">RETURN</div>
                    <p class="mt-2"><strong>Code:</strong> {{ $return->return_code }}</p>
                    <p><strong>Date:</strong> {{ $return->return_date }}</p>
                </div>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="row mb-3">
            <div class="col-6">
                <h6><strong>Customer:</strong></h6>
                <p>{{ $return->customer->name ?? 'N/A' }}</p>
                @if($return->customer)
                <p>{{ $return->customer->phone ?? '' }}</p>
                @endif
            </div>
            <div class="col-6">
                <h6><strong>Original Order:</strong></h6>
                <p>{{ $return->originalOrder->order_code ?? 'N/A' }}</p>
                <p><strong>Refund:</strong> {{ ucfirst(str_replace('_', ' ', $return->refund_method)) }}</p>
            </div>
        </div>

        <!-- Products Table -->
        <table class="products-table">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>Product Name</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($return->return_products as $index => $product)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $product->product_name }}</td>
                    <td>{{ $product->qty }}</td>
                    <td>৳{{ number_format($product->sale_price, 2) }}</td>
                    <td style="text-align: right;">৳{{ number_format($product->total_price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals-section">
            <div class="total-row">
                <div class="total-label">Subtotal:</div>
                <div class="total-amount">৳{{ number_format($return->subtotal, 2) }}</div>
            </div>
            <div class="total-row grand-total">
                <div class="total-label">Total Refund:</div>
                <div class="total-amount">৳{{ number_format($return->total, 2) }}</div>
            </div>
        </div>

        @if($return->return_reason)
        <div class="mt-3">
            <strong>Reason:</strong> {{ $return->return_reason }}
        </div>
        @endif

        <!-- Footer -->
        <div class="mt-5 pt-3 border-top text-center">
            <p class="small">Thank you for your business!</p>
            <p class="small">Printed on: {{ now()->format('Y-m-d H:i:s') }}</p>
        </div>
    </div>
</body>
</html>

