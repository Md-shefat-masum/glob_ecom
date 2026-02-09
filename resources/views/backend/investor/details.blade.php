@extends('backend.master')

@section('header_css')
    <style>
        .investor-info-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: 600;
            width: 150px;
            color: #666;
        }
        .info-value {
            flex: 1;
        }
        .balance-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .balance-card h5 {
            color: white;
            margin-bottom: 10px;
        }
        .balance-card .balance-amount {
            font-size: 32px;
            font-weight: bold;
        }
    </style>
@endsection

@section('page_title')
    Investor Details
@endsection
@section('page_heading')
    Investor Details
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-3">Investor Information</h4>
                        <a href="{{ route('ViewAllInvestor') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="investor-info-card">
                                @if($investor->image)
                                    @php
                                        $imageUrl = str_replace(env('FILE_URL') . '/', '', $investor->image);
                                        $fullUrl = env('FILE_URL') . '/' . $imageUrl;
                                    @endphp
                                    <img src="{{ $fullUrl }}" alt="{{ $investor->name }}" 
                                        style="width: 200px; height: 200px; object-fit: cover; border-radius: 8px; margin-bottom: 20px;">
                                @else
                                    <div style="width: 200px; height: 200px; border-radius: 8px; background: #e0e0e0; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                                        <i class="fas fa-user" style="font-size: 64px; color: #999;"></i>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="investor-info-card">
                                <div class="info-row">
                                    <div class="info-label">Name:</div>
                                    <div class="info-value"><strong>{{ $investor->name }}</strong></div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Email:</div>
                                    <div class="info-value">{{ $investor->email ?? 'N/A' }}</div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Phone:</div>
                                    <div class="info-value">{{ $investor->phone }}</div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Address:</div>
                                    <div class="info-value">{{ $investor->address ?? 'N/A' }}</div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Created At:</div>
                                    <div class="info-value">{{ $investor->created_at->format('d M Y, h:i A') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="balance-card">
                                <h5>Total Deposits</h5>
                                <div class="balance-amount">৳ {{ number_format($totalDeposits, 2) }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="balance-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                <h5>Total Withdraws</h5>
                                <div class="balance-amount">৳ {{ number_format($totalWithdraws, 2) }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="balance-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                <h5>Current Balance</h5>
                                <div class="balance-amount" style="color: {{ $balance >= 0 ? '#fff' : '#ffeb3b' }};">
                                    ৳ {{ number_format($balance, 2) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#deposits">Deposits</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#withdraws">Withdraws</a>
                                </li>
                            </ul>

                            <div class="tab-content mt-3">
                                <div id="deposits" class="tab-pane active">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>SL</th>
                                                    <th>Date</th>
                                                    <th>Payment Type</th>
                                                    <th>Amount</th>
                                                    <th>Note</th>
                                                    <th>Created By</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($deposits as $index => $deposit)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($deposit->deposit_date)->format('d M Y') }}</td>
                                                        <td>{{ $deposit->paymentType->payment_type ?? 'N/A' }}</td>
                                                        <td><strong>৳ {{ number_format($deposit->amount, 2) }}</strong></td>
                                                        <td>{{ $deposit->note }}</td>
                                                        <td>{{ $deposit->creator_info->name ?? 'N/A' }}</td>
                                                        <td>
                                                            <a href="{{ route('PrintDeposit', $deposit->id) }}" target="_blank" class="btn btn-sm btn-info">
                                                                <i class="fas fa-print"></i> Print
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center">No deposits found</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div id="withdraws" class="tab-pane">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>SL</th>
                                                    <th>Date</th>
                                                    <th>Payment Type</th>
                                                    <th>Amount</th>
                                                    <th>Note</th>
                                                    <th>Created By</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($withdraws as $index => $withdraw)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($withdraw->withdraw_date)->format('d M Y') }}</td>
                                                        <td>{{ $withdraw->paymentType->payment_type ?? 'N/A' }}</td>
                                                        <td><strong>৳ {{ number_format($withdraw->amount, 2) }}</strong></td>
                                                        <td>{{ $withdraw->note }}</td>
                                                        <td>{{ $withdraw->creator_info->name ?? 'N/A' }}</td>
                                                        <td>
                                                            <a href="{{ route('PrintWithdraw', $withdraw->id) }}" target="_blank" class="btn btn-sm btn-info">
                                                                <i class="fas fa-print"></i> Print
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center">No withdraws found</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


