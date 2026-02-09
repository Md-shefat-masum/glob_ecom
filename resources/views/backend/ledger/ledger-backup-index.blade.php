@extends('backend.master')

@section('page_title')
    Ledger
@endsection

@section('page_heading')
    View Ledger
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Ledger</h4>

                    <form method="POST" action="{{ route('ledger.show') }}">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="account_id">Account<span class="text-danger">*</span></label>
                                    <select id="account_id" name="account_id" class="form-control" required>
                                        <option value="">Select Account</option>
                                        @foreach($accounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->account_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="start_date">Start Date</label>
                                    <input type="date" id="start_date" name="start_date" class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label for="end_date">End Date</label>
                                    <input type="date" id="end_date" name="end_date" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group text-center pt-3">
                                    <button class="btn btn-primary" type="submit">Show Ledger</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection