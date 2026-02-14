@extends('backend.master')

@section('header_css')
    <style>
        .journal-print { font-size: 12px; color: #000; background: #fff; }
        .journal-print .j-headings {
            display: flex;
            padding: 8px 10px;
            font-weight: 700;
            border-bottom: 2px solid #333;
            background: #f5f5f5;
        }
        .journal-print .j-headings .j-col-date { width: 11%; min-width: 90px; }
        .journal-print .j-headings .j-col-pc { width: 14%; min-width: 110px; }
        .journal-print .j-headings .j-col-type { width: 16%; min-width: 120px; }
        .journal-print .j-headings .j-col-part { flex: 1; }
        .journal-print .j-headings .j-col-debit { width: 120px; text-align: right; }
        .journal-print .j-headings .j-col-credit { width: 120px; text-align: right; }
        .journal-print .j-date-block { border-bottom: 1px solid #ddd; }
        .journal-print .j-date-row { font-weight: 700; padding: 6px 10px; background: #f8f8f8; }
        .journal-print .j-pc-block { padding-left: 1.5rem; }
        .journal-print .j-pc-row { font-weight: 600; padding: 4px 10px; background: #f0f0f0; }
        .journal-print .j-type-block { padding-left: 1.5rem; }
        .journal-print .j-type-row { font-weight: 600; padding: 4px 10px; background: #eee; }
        .journal-print .j-row-block { padding-left: 1.5rem; }
        .journal-print .j-transaction-row {
            display: flex;
            align-items: flex-start;
            padding: 4px 10px;
            border-bottom: 1px solid #f0f0f0;
        }
        .journal-print .j-transaction-row.j-has-credit { padding-left: 2.5rem; }
        .journal-print .j-transaction-row .j-col-part { flex: 1; }
        .journal-print .j-transaction-row .j-col-debit { width: 120px; text-align: right; flex-shrink: 0; }
        .journal-print .j-transaction-row .j-col-credit { width: 120px; text-align: right; flex-shrink: 0; }
        .journal-print .j-note { color: #555; font-size: 11px; }
        .journal-print .j-footer {
            display: flex;
            padding: 8px 10px;
            font-weight: 700;
            border-top: 2px solid #333;
            background: #f0f0f0;
        }
        .journal-print .j-footer .j-col-part { flex: 1; }
        .journal-print .j-footer .j-col-debit { width: 120px; text-align: right; }
        .journal-print .j-footer .j-col-credit { width: 120px; text-align: right; }
        @media print {
            .journal-print .j-date-row, .journal-print .j-pc-row, .journal-print .j-type-row {
                -webkit-print-color-adjust: exact; print-color-adjust: exact;
            }
        }
    </style>
@endsection

@section('page_title')
    Journal
@endsection

@section('page_heading')
    Journal
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Journal</h4>

                    <form method="GET" action="{{ route('journal.index') }}">
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="from">From</label>
                                    <input type="date" id="from" name="from" class="form-control"
                                        value="{{ request('from', $from ?? now()->subDays(30)->format('Y-m-d')) }}">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="to">To</label>
                                    <input type="date" id="to" name="to" class="form-control"
                                        value="{{ request('to', $to ?? now()->format('Y-m-d')) }}">
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label for="page">Page</label>
                                    <input type="number" id="page" name="page" class="form-control" min="1"
                                        value="{{ request('page', $page ?? 1) }}">
                                </div>
                            </div>
                            <div class="col-lg-2 d-flex align-items-end">
                                <button class="btn btn-primary" type="submit">Filter</button>
                            </div>
                        </div>
                    </form>

                    <div class="journal-print mt-4">
                        <table>
                            <thead>
                                <tr>
                                    <th style="width:10%">Date</th>
                                    <th style="width:12%">Payment Code</th>
                                    <th style="width:15%">Transaction Type</th>
                                    <th style="width:35%">Particulars</th>
                                    <th style="width:14%; text-align:right">Debit</th>
                                    <th style="width:14%; text-align:right">Credit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $journalData = $journalData ?? []; @endphp
                                @forelse($journalData as $block)
                                    @if (isset($block['dr']) && isset($block['cr']))
                                        {{-- Opening balance --}}
                                        <tr class="j-opening">
                                            <td class="j-date">{{ $block['date'] }}</td>
                                            <td class="j-payment-code">—</td>
                                            <td class="j-type">Opening balance</td>
                                            <td class="j-particulars">—</td>
                                            <td class="j-amount">{{ number_format($block['dr'], 2) }}</td>
                                            <td class="j-amount">{{ number_format($block['cr'], 2) }}</td>
                                        </tr>
                                    @else
                                        {{-- Date block: date -> payment_code -> transaction_type --}}
                                        @php
                                            $dateRowspan = 0;
                                            foreach ($block['payment_codes'] ?? [] as $pcBlock) {
                                                foreach ($pcBlock['transactions'] ?? [] as $tb) {
                                                    $dateRowspan += count($tb['transactions'] ?? []);
                                                }
                                            }
                                            $isFirstRowOfDate = true;
                                        @endphp
                                        @if ($dateRowspan > 0)
                                            @foreach ($block['payment_codes'] ?? [] as $pcBlock)
                                                @php
                                                    $pcRowspan = 0;
                                                    foreach ($pcBlock['transactions'] ?? [] as $tb) {
                                                        $pcRowspan += count($tb['transactions'] ?? []);
                                                    }
                                                    $isFirstRowOfPc = true;
                                                @endphp
                                                @foreach ($pcBlock['transactions'] ?? [] as $typeBlock)
                                                    @php
                                                        $typeRows = $typeBlock['transactions'] ?? [];
                                                        $typeRowspan = count($typeRows);
                                                        $isFirstRowOfType = true;
                                                    @endphp
                                                    @if ($typeRowspan > 0)
                                                        @foreach ($typeRows as $row)
                                                            <tr>
                                                                @if ($isFirstRowOfDate)
                                                                    <td class="j-date" rowspan="{{ $dateRowspan }}">{{ $block['date'] ?? '' }}</td>
                                                                    @php $isFirstRowOfDate = false; @endphp
                                                                @endif
                                                                @if ($isFirstRowOfPc)
                                                                    <td class="j-payment-code" rowspan="{{ $pcRowspan }}">{{ $pcBlock['payment_code'] ?? '' }}</td>
                                                                    @php $isFirstRowOfPc = false; @endphp
                                                                @endif
                                                                @if ($isFirstRowOfType)
                                                                    <td class="j-type" rowspan="{{ $typeRowspan }}">{{ $typeBlock['type'] ?? '' }}</td>
                                                                    @php $isFirstRowOfType = false; @endphp
                                                                @endif
                                                                <td class="j-particulars">
                                                                    <span class="j-account">{{ $row['account_head'] ?? '' }}</span>
                                                                    @if (!empty($row['note']))
                                                                        <span class="j-note"> — {{ $row['note'] }}</span>
                                                                    @endif
                                                                </td>
                                                                <td class="j-amount">{{ ($row['debit_amount'] ?? 0) > 0 ? number_format($row['debit_amount'], 2) : '' }}</td>
                                                                <td class="j-amount">{{ ($row['credit_amount'] ?? 0) > 0 ? number_format($row['credit_amount'], 2) : '' }}</td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                @endforeach
                                            @endforeach
                                        @endif
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">No journal data for the selected period.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="j-particulars">Total (this page)</td>
                                    <td class="j-amount">{{ number_format($totalDebit ?? 0, 2) }}</td>
                                    <td class="j-amount">{{ number_format($totalCredit ?? 0, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="mt-3">
                            {!! $transactions->links() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
