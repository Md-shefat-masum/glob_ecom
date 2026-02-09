@extends('backend.master')

@section('header_css')
    <style>
        .info-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background: #fff;
        }
        .info-card h5 {
            color: #495057;
            border-bottom: 2px solid #28a745;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .info-row {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px dotted #dee2e6;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #6c757d;
            width: 200px;
            flex-shrink: 0;
        }
        .info-value {
            color: #212529;
            flex: 1;
        }
    </style>
@endsection

@section('page_title')
    Income Details
@endsection

@section('page_heading')
    Income Details
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">Income Details</h4>
                        <div>
                            <a href="{{ route('ViewAllIncome') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                            <a href="{{ route('PrintIncome', $income->id) }}" target="_blank" class="btn btn-primary">
                                <i class="fas fa-print"></i> Print Voucher
                            </a>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-card">
                                <h5><i class="fas fa-info-circle"></i> Basic Information</h5>
                                <div class="info-row">
                                    <span class="info-label">Income Code:</span>
                                    <span class="info-value"><strong>{{ $income->code }}</strong></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Income For:</span>
                                    <span class="info-value">{{ $income->income_for }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Income Date:</span>
                                    <span class="info-value">{{ \Carbon\Carbon::parse($income->date)->format('d M Y') }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Amount:</span>
                                    <span class="info-value"><strong style="color: #28a745; font-size: 18px;">৳ {{ number_format($income->amount, 2) }}</strong></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Reference:</span>
                                    <span class="info-value">{{ $income->reference ?: 'N/A' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-card">
                                <h5><i class="fas fa-building"></i> Account Information</h5>
                                <div class="info-row">
                                    <span class="info-label">Income Category:</span>
                                    <span class="info-value">{{ $income->income_category ? $income->income_category->name : 'N/A' }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Income Head:</span>
                                    <span class="info-value">
                                        @if($income->income_category && $income->income_category->creditAccount)
                                            {{ $income->income_category->creditAccount->account_name }}
                                        @else
                                            N/A
                                        @endif
                                    </span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Revenue Head:</span>
                                    <span class="info-value">
                                        @if($income->income_category && $income->income_category->debitAccount)
                                            {{ $income->income_category->debitAccount->account_name }}
                                        @else
                                            N/A
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="info-card">
                                <h5><i class="fas fa-file-alt"></i> Additional Information</h5>
                                <div class="info-row">
                                    <span class="info-label">Note:</span>
                                    <span class="info-value">{{ $income->note ?: 'N/A' }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Created By:</span>
                                    <span class="info-value">{{ $income->user ? $income->user->name : 'N/A' }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Created At:</span>
                                    <span class="info-value">{{ \Carbon\Carbon::parse($income->created_at)->format('d M Y, h:i A') }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Status:</span>
                                    <span class="info-value">
                                        @if($income->status == 'active')
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

