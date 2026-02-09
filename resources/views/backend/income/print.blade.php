<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Income Voucher - #{{ $income->code }}</title>
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

        .voucher-container {
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

        .voucher-title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin: 25px 0;
            text-decoration: underline;
            text-transform: uppercase;
            color: #28a745;
        }

        .voucher-details {
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
            border: 2px solid #000;
            background: #f9f9f9;
        }

        .amount-in-numbers {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 15px;
            color: #28a745;
        }

        .amount-in-words {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            font-style: italic;
            padding: 10px;
            background: #fff;
            border: 1px solid #000;
            margin-bottom: 15px;
        }

        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            width: 250px;
            text-align: center;
        }

        .signature-line {
            border-top: 2px solid #000;
            margin-top: 60px;
            padding-top: 5px;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 15px;
        }

        @media print {
            body {
                padding: 0;
            }
            .voucher-container {
                border: none;
                padding: 20px;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="voucher-container">
        <div class="header">
            @if($generalInfo && $generalInfo->logo)
                <img src="{{ env('FILE_URL') }}/{{ $generalInfo->logo }}" alt="Company Logo">
            @endif
            <h2>INCOME VOUCHER</h2>
            @if($generalInfo)
                <div class="company-info">
                    @if($generalInfo->address) {{ $generalInfo->address }}<br> @endif
                    @if($generalInfo->phone) Phone: {{ $generalInfo->phone }} @endif
                    @if($generalInfo->email) | Email: {{ $generalInfo->email }} @endif
                </div>
            @endif
        </div>

        <div class="voucher-title">Income Voucher</div>

        <div class="voucher-details">
            <div class="detail-row">
                <span class="detail-label">Voucher No:</span>
                <span class="detail-value"><strong>{{ $income->code }}</strong></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Date:</span>
                <span class="detail-value">{{ \Carbon\Carbon::parse($income->date)->format('d M Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Income For:</span>
                <span class="detail-value">{{ $income->income_for }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Category:</span>
                <span class="detail-value">{{ $income->income_category ? $income->income_category->name : 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Income Head:</span>
                <span class="detail-value">
                    @if($income->income_category && $income->income_category->creditAccount)
                        {{ $income->income_category->creditAccount->account_name }}
                    @else
                        N/A
                    @endif
                </span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Revenue Head:</span>
                <span class="detail-value">
                    @if($income->income_category && $income->income_category->debitAccount)
                        {{ $income->income_category->debitAccount->account_name }}
                    @else
                        N/A
                    @endif
                </span>
            </div>
            @if($income->reference)
            <div class="detail-row">
                <span class="detail-label">Reference:</span>
                <span class="detail-value">{{ $income->reference }}</span>
            </div>
            @endif
            @if($income->note)
            <div class="detail-row">
                <span class="detail-label">Note:</span>
                <span class="detail-value">{{ $income->note }}</span>
            </div>
            @endif
        </div>

        <div class="amount-section">
            <div class="amount-in-numbers">
                Amount: ৳ {{ number_format($income->amount, 2) }}
            </div>
            <div class="amount-in-words">
                {{ numberToWords($income->amount) }} Taka Only
            </div>
        </div>

        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line">
                    Prepared By<br>
                    {{ $income->user ? $income->user->name : 'N/A' }}
                </div>
            </div>
            <div class="signature-box">
                <div class="signature-line">
                    Authorized Signature
                </div>
            </div>
        </div>

        <div class="footer">
            <p>This is a computer generated voucher.</p>
            <p>Printed on: {{ \Carbon\Carbon::now()->format('d M Y, h:i A') }}</p>
        </div>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" class="btn btn-primary">Print Voucher</button>
        <button onclick="window.close()" class="btn btn-secondary">Close</button>
    </div>
</body>
</html>

