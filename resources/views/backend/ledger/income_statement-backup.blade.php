@extends('backend.master')

@section('page_title')
    Income Statement
@endsection

@section('page_heading')
    Income Statement
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Income Statement</h4>

                    <!-- Date Filter Form -->
                    <form method="GET" action="{{ route('ledger.income_statement') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="start_date">Start Date:</label>
                                <input type="date" id="start_date" name="start_date" class="form-control" value="{{ $startDate }}">
                            </div>
                            <div class="col-md-4">
                                <label for="end_date">End Date:</label>
                                <input type="date" id="end_date" name="end_date" class="form-control" value="{{ $endDate }}">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                    </form>

                    <!-- Revenue Section -->
                    <h5 class="text-primary">Revenue</h5>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Account Name</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($incomeStatement['revenueData'] as $revenue)
                                <tr>
                                    <td>{{ $revenue['account_name'] }}</td>
                                    <td class="text-end">৳ {{ number_format($revenue['amount'], 2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="fw-bold">
                                <td><b>Total Revenue</b></td>
                                <td class="text-end">৳ {{ number_format($incomeStatement['totalRevenue'], 2) }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Expenses Section -->
                    <h5 class="text-danger">Expenses</h5>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Account Name</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($incomeStatement['expenseData'] as $expense)
                                <tr>
                                    <td>{{ $expense['account_name'] }}</td>
                                    <td class="text-end">৳ {{ number_format($expense['amount'], 2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="fw-bold">
                                <td><b>Total Expense</b></td>
                                <td class="text-end">৳ {{ number_format($incomeStatement['totalExpense'], 2) }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Net Income Section -->
                    <h5 class="text-success">Net Income</h5>
                    <table class="table">
                        <tbody>
                            <tr class="fw-bold">
                                <td><b>Net Income</b></td>
                                <td class="text-end text-success" style="max-width: 63px; display: table-cell;">
                                    <b>${{ number_format($incomeStatement['netIncome'], 2) }}</b>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
@endsection
