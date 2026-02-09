@extends('backend.master')

@section('page_title')
    Ledger
@endsection

@section('page_heading')
    Ledger for {{ $account->account_name }}
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Ledger Entries</h4>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Payment Code</th>
                                <th>Debit</th>
                                <th>Credit</th>
                                <th colspan="2">Balance</th>
                            </tr>
                            <tr>
                                <th colspan="4"></th>
                                <th>Debit</th>
                                <th>Credit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $debit_balance = 0;
                                $credit_balance = 0;
                                $totalDebit = 0;
                                $totalCredit = 0;
                            @endphp
                            @foreach ($transactions as $transaction)
                                @php
                                    if ($transaction->debit_account_id == $account->id) {
                                        $debit_balance += $transaction->debit_amt;
                                        $debit = $transaction->debit_amt;
                                        $credit = 0;
                                    } else {
                                        $credit_balance += $transaction->credit_amt;
                                        $debit = 0;
                                        $credit = $transaction->credit_amt;
                                    }
                                    $totalDebit += $debit;
                                    $totalCredit += $credit;
                                @endphp
                                <tr>
                                    <td>{{ $transaction->transaction_date }}</td>
                                    <td>{{ $transaction->payment_code }}</td>
                                    <td>{{ number_format($debit, 2) }}</td>
                                    <td>{{ number_format($credit, 2) }}</td>
                                    <td>{{ $debit_balance > $credit_balance ? number_format($debit_balance - $credit_balance, 2) : '-' }}
                                    </td>
                                    <td>{{ $credit_balance > $debit_balance ? number_format($credit_balance - $debit_balance, 2) : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <th colspan="2">Total</th>
                                <th>{{ number_format($totalDebit, 2) }}</th>
                                <th>{{ number_format($totalCredit, 2) }}</th>
                                <th colspan="2">
                                    @php
                                        $netTotal = $totalDebit - $totalCredit;
                                    @endphp
                                    @if ($netTotal >= 0)
                                        <span>Debit: {{ number_format($netTotal, 2) }}</span>
                                    @else
                                        <span>Credit: {{ number_format(abs($netTotal), 2) }}</span>
                                    @endif
                                </th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
