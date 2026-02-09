@extends('backend.master')

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

                    <!-- Mini Form for Filtering -->
                    <form method="GET" action="{{ route('journal.index') }}">
                        <div class="row">
                            @php
                                $currentDate = now('Asia/Dhaka')->format('Y-m-d'); // Get today's date
                            @endphp

                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="start_date">Start Date</label>
                                    <input type="date" id="start_date" name="start_date" class="form-control"
                                        value="{{ request('start_date', $currentDate) }}">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="end_date">End Date</label>
                                    <input type="date" id="end_date" name="end_date" class="form-control"
                                        value="{{ request('end_date', $currentDate) }}">
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group text-center pt-3">
                                    <button class="btn btn-primary" type="submit">Filter</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Journal Table -->
                    <table class="table table-bordered mt-4">
                        <thead>
                            <tr>
                                <th>SI</th>
                                <th>Transaction Date</th>
                                <th>Code</th>
                                <th>Debit Account</th>
                                <th>Debit Amount</th>
                                <th>Credit Account</th>
                                <th>Credit Amount</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $serial = 1;
                                $colors = [];
                                $colorOptions = ['#7EA01D', '#846F51'];
                                $colorIndex = 0;
                            @endphp

                            @foreach ($transactions as $transaction)
                                @php
                                    // Assign a fixed color for each unique payment_code
                                    if (!isset($colors[$transaction->payment_code])) {
                                        $colors[$transaction->payment_code] = $colorOptions[$colorIndex % 2];
                                        $colorIndex++;
                                    }
                                    $rowColor = $colors[$transaction->payment_code];
                                @endphp
                                <tr style="background-color: {{ $rowColor }}; color: white;">
                                    <td>{{ $serial++ }}</td>
                                    <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d-m-Y') }}</td>
                                    <td>{{ $transaction->payment_code }}</td>
                                    <td>{{ $transaction->debitAccount->account_name ?? '-' }}</td>
                                    <td>{{ number_format($transaction->debit_amt, 2) }}</td>
                                    <td>{{ $transaction->creditAccount->account_name ?? '-' }}</td>
                                    <td>{{ number_format($transaction->credit_amt, 2) }}</td>
                                    <td>{{ Str::limit($transaction->note, 15) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-right">Total:</th>
                                <th class="font-weight-bold">{{ number_format($totalDebit, 2) }}</th>
                                <th class="text-right">Total:</th>
                                <th class="font-weight-bold">{{ number_format($totalCredit, 2) }}</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>

                </div>
            </div>
        </div>
    </div>
@endsection
