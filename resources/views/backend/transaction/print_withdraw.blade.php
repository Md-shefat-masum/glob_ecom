<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Withdrawal Receipt - #{{ $withdraw->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', serif;
            font-size: 14px;
            line-height: 1.6;
            color: #000;
            background: #fff;
            padding: 20px;
        }

        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #000;
            padding: 30px;
            background: #fff;
        }

        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }

        .header img {
            max-width: 150px;
            max-height: 80px;
            margin-bottom: 10px;
        }

        .header h1 {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .header h2 {
            font-size: 20px;
            font-weight: bold;
            color: #333;
            margin-top: 5px;
        }

        .company-info {
            font-size: 12px;
            margin-top: 10px;
            line-height: 1.8;
        }

        .receipt-title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin: 25px 0;
            text-decoration: underline;
            text-transform: uppercase;
            color: #d32f2f;
        }

        .receipt-details {
            margin: 25px 0;
        }

        .detail-row {
            display: flex;
            margin-bottom: 12px;
            border-bottom: 1px dotted #666;
            padding-bottom: 8px;
        }

        .detail-label {
            font-weight: bold;
            width: 200px;
            flex-shrink: 0;
        }

        .detail-value {
            flex: 1;
            text-align: left;
        }

        .amount-section {
            margin: 30px 0;
            padding: 20px;
            border: 2px solid #d32f2f;
            background: #ffebee;
        }

        .amount-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 18px;
        }

        .amount-label {
            font-weight: bold;
        }

        .amount-value {
            font-weight: bold;
            font-size: 24px;
            color: #d32f2f;
        }

        .amount-in-words {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #d32f2f;
            font-size: 16px;
            font-style: italic;
        }

        .note-section {
            margin: 25px 0;
            padding: 15px;
            border: 1px solid #000;
            background: #fff;
        }

        .note-section h3 {
            font-size: 16px;
            margin-bottom: 10px;
            text-decoration: underline;
        }

        .signature-section {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            width: 250px;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 60px;
            padding-top: 5px;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 11px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 15px;
        }

        @media print {
            body {
                padding: 0;
            }
            .receipt-container {
                border: 2px solid #000;
                box-shadow: none;
            }
            @page {
                margin: 0.5cm;
            }
        }

        .no-print {
            display: none;
        }
    </style>
</head>
<body onload="window.print();">
    <div class="receipt-container">
        <div class="header">
            @if($generalInfo && $generalInfo->logo && file_exists(public_path($generalInfo->logo)))
                <img src="{{ url($generalInfo->logo) }}" alt="Logo">
            @endif
            {{-- <h1>{{ $generalInfo->company_name ?? 'Company Name' }}</h1> --}}
            <div class="company-info">
                @if($generalInfo && $generalInfo->address)
                    {{ $generalInfo->address }}<br>
                @endif
                @if($generalInfo && $generalInfo->contact)
                    Phone: {{ $generalInfo->contact }}
                @endif
                @if($generalInfo && $generalInfo->email)
                    | Email: {{ $generalInfo->email }}
                @endif
            </div>
        </div>

        <div class="receipt-title">WITHDRAWAL RECEIPT</div>

        <div class="receipt-details">
            <div class="detail-row">
                <div class="detail-label">Receipt No:</div>
                <div class="detail-value"><strong>#{{ str_pad($withdraw->id, 6, '0', STR_PAD_LEFT) }}</strong></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Date:</div>
                <div class="detail-value">{{ \Carbon\Carbon::parse($withdraw->withdraw_date)->format('d F Y') }}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Reference No:</div>
                <div class="detail-value">{{ $withdraw->reference_no ?? 'N/A' }}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Investor Name:</div>
                <div class="detail-value"><strong>{{ $withdraw->investor->name ?? 'Owner' }}</strong></div>
            </div>
            @if($withdraw->investor && $withdraw->investor->phone)
            <div class="detail-row">
                <div class="detail-label">Phone:</div>
                <div class="detail-value">{{ $withdraw->investor->phone }}</div>
            </div>
            @endif
            @if($withdraw->investor && $withdraw->investor->email)
            <div class="detail-row">
                <div class="detail-label">Email:</div>
                <div class="detail-value">{{ $withdraw->investor->email }}</div>
            </div>
            @endif
            <div class="detail-row">
                <div class="detail-label">Payment Method:</div>
                <div class="detail-value"><strong>{{ $withdraw->paymentType->payment_type ?? 'N/A' }}</strong></div>
            </div>
        </div>

        <div class="amount-section">
            <div class="amount-row">
                <div class="amount-label">Amount Withdrawn:</div>
                <div class="amount-value">৳ {{ number_format($withdraw->amount, 2) }}</div>
            </div>
            <div class="amount-in-words">
                <strong>In Words:</strong> {{ ucwords(numberToWords($withdraw->amount)) }} Taka Only
            </div>
        </div>

        @if($withdraw->note)
        <div class="note-section">
            <h3>Note:</h3>
            <p>{{ $withdraw->note }}</p>
        </div>
        @endif

        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line">Investor Signature</div>
            </div>
            <div class="signature-box">
                <div class="signature-line">Authorized Signature</div>
            </div>
        </div>

        <div class="footer">
            <p>This is a computer generated receipt. No signature required.</p>
            <p>Generated on: {{ \Carbon\Carbon::now()->format('d F Y, h:i A') }}</p>
            @if($withdraw->creator_info)
            <p>Created by: {{ $withdraw->creator_info->name }}</p>
            @endif
        </div>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; background: #007bff; color: white; border: none; cursor: pointer; border-radius: 5px;">
            Print Receipt
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 16px; background: #dc3545; color: white; border: none; cursor: pointer; border-radius: 5px; margin-left: 10px;">
            Close
        </button>
    </div>
</body>
</html>


