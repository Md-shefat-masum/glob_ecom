@extends('backend.master')

@section('page_title')
    Sales Target Details
@endsection
@section('page_heading')
    Sales Target Details
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">Target Details</h4>
                        <div>
                            <a href="{{ route('sales_targets.edit', $target->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            <a href="{{ route('sales_targets.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
                        </div>
                    </div>
                    <table class="table table-bordered">
                        <tr>
                            <th width="200">Date</th>
                            <td>{{ $target->date->format('Y-m-d') }}</td>
                        </tr>
                        <tr>
                            <th>Employee</th>
                            <td>{{ $target->user ? $target->user->name : '—' }}</td>
                        </tr>
                        <tr>
                            <th>Target</th>
                            <td>{{ number_format($target->target, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Completed</th>
                            <td>{{ number_format($target->completed, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Remains</th>
                            <td>{{ number_format($target->remains, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Target fillup %</th>
                            <td>{{ $target->target > 0 ? round(($target->completed / $target->target) * 100, 1) : 0 }}%</td>
                        </tr>
                        <tr>
                            <th>Is Evaluated</th>
                            <td>{{ $target->is_evaluated ? 'Yes' : 'No' }}</td>
                        </tr>
                        @if($target->note)
                        <tr>
                            <th>Note</th>
                            <td>{{ $target->note }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
