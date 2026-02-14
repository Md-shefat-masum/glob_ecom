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
        .journal-print .j-headings .j-col-date { width: 50px; }
        .journal-print .j-headings .j-col-pc { width: 100px; }
        .journal-print .j-headings .j-col-type { width: 130px; }
        .journal-print .j-headings .j-col-part { flex: 1; }
        .journal-print .j-headings .j-col-debit { width: 120px; text-align: right; }
        .journal-print .j-headings .j-col-credit { width: 120px; text-align: right; }
        .journal-print .j-date-block { border-bottom: 1px solid #ddd; }
        .journal-print .j-date-row { font-weight: 700; padding: 6px 10px; background: #f8f8f8; }
        .journal-print .j-pc-block { padding-left: 70px; }
        .journal-print .j-pc-row { font-weight: 600; padding: 4px 10px; background: #f0f0f0; }
        .journal-print .j-type-block { padding-left: 70px; margin-top: 1px;}
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
    <div class="container">
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
                            {{-- Headings --}}
                            <div class="j-headings">
                                <span class="j-col-date">Date</span>
                                <span class="j-col-pc">Payment Code</span>
                                <span class="j-col-type">Transaction Type</span>
                                <span class="j-col-part">Particulars</span>
                                <span class="j-col-debit">Debit</span>
                                <span class="j-col-credit">Credit</span>
                            </div>
    
                            @php $journalData = $journalData ?? []; @endphp
                            @forelse($journalData as $block)
                                @if (isset($block['dr']) && isset($block['cr']))
                                    {{-- Opening balance --}}
                                    <div class="j-date-block">
                                        <div class="j-date-row" style="display: flex; align-items: center;">
                                            <span class="j-col-date" style="width: 11%; min-width: 90px;">{{ $block['date'] }}</span>
                                            <span class="j-col-pc">—</span>
                                            <span class="j-col-type" style="width: 16%; min-width: 120px;">Opening balance</span>
                                            <span class="j-col-part" style="flex: 1;">—</span>
                                            <span class="j-col-debit" style="width: 120px; text-align: right;">{{ number_format($block['dr'], 2) }}</span>
                                            <span class="j-col-credit" style="width: 120px; text-align: right;">{{ number_format($block['cr'], 2) }}</span>
                                        </div>
                                    </div>
                                @else
                                    {{-- Date -> Payment Code -> Transaction Type -> Rows --}}
                                    @if (!empty($block['payment_codes']))
                                        <div class="j-date-block">
                                            <div class="j-date-row">{{ $block['date'] ?? '' }}</div>
                                            @foreach ($block['payment_codes'] as $pcBlock)
                                                <div class="j-pc-block">
                                                    <div class="j-pc-row">{{ $pcBlock['payment_code'] ?? '' }}</div>
                                                    @foreach ($pcBlock['transactions'] ?? [] as $typeBlock)
                                                        @if (count($typeBlock['transactions'] ?? []) > 0)
                                                            <div class="j-type-block">
                                                                <div class="j-type-row">{{ $typeBlock['type'] ?? '' }}</div>
                                                                <div class="j-row-block">
                                                                    @foreach ($typeBlock['transactions'] as $row)
                                                                        <div class="j-transaction-row {{ (($row['credit_amount'] ?? 0) > 0) ? 'j-has-credit' : '' }}">
                                                                            <span class="j-col-part">
                                                                                {{ $row['account_head'] ?? '' }}
                                                                                @if (!empty($row['note']))
                                                                                    <span class="j-note"> — {{ $row['note'] }}</span>
                                                                                @endif
                                                                            </span>
                                                                            @php $d = (float)($row['debit_amount'] ?? 0); $c = (float)($row['credit_amount'] ?? 0); @endphp
                                                                            <span class="j-col-debit">{{ ($d ?? 0) > 0 ? (($d ?? 0) == (int)($d ?? 0) ? number_format($d ?? 0, 0) : number_format($d ?? 0, 2)) : '' }}</span>
                                                                            <span class="j-col-credit">{{ ($c ?? 0) > 0 ? (($c ?? 0) == (int)($c ?? 0) ? number_format($c ?? 0, 0) : number_format($c ?? 0, 2)) : '' }}</span>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                @endif
                            @empty
                                <div class="py-4 text-center">No journal data for the selected period.</div>
                            @endforelse
    
                            {{-- Footer total --}}
                            <div class="j-footer">
                                <span class="j-col-part">Total (this page)</span>
                                <span class="j-col-debit">{{ number_format($totalDebit ?? 0, 2) }}</span>
                                <span class="j-col-credit">{{ number_format($totalCredit ?? 0, 2) }}</span>
                            </div>
    
                            <div class="mt-3">
                                {!! $transactions->links() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
