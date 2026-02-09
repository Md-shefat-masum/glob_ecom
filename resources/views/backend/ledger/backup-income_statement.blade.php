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

                    <!-- Revenue Section -->
                    <h5>Revenues</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Account</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($revenueAccounts as $revenueAccount)
                                @if ($revenueAccount->children->isEmpty())
                                    <!-- Only show child accounts -->
                                    <tr>
                                        <td>{{ $revenueAccount->account_name }}</td>
                                        <td>{{ number_format($revenueAccount->balance, 2) }}</td>
                                    </tr>
                                @endif
                            @endforeach
                            <tr>
                                <th>Total Revenue</th>
                                <th>{{ number_format($totalRevenue, 2) }}</th>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Expenses Section -->
                    <h5>Expenses</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Account</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($expenseAccounts as $expenseAccount)
                                @if ($expenseAccount->children->isEmpty())
                                    <!-- Only show child accounts -->
                                    <tr>
                                        <td>{{ $expenseAccount->account_name }}</td>
                                        <td>{{ number_format($expenseAccount->balance, 2) }}</td>
                                    </tr>
                                @endif
                            @endforeach
                            <tr>
                                <th>Total Expenses</th>
                                <th>{{ number_format($totalExpenses, 2) }}</th>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Net Income Section -->
                    <h5>Net Income</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>Net Income</th>
                            <td>{{ number_format($netIncome, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
