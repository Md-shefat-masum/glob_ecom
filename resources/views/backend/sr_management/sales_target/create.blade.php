@extends('backend.master')

@section('header_css')
    <link href="{{ url('assets') }}/plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />
    <style>.select2-container { width: 100% !important; }</style>
@endsection

@section('page_title')
    Create Sales Target
@endsection
@section('page_heading')
    Create Sales Target
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">Add Sales Target</h4>
                        <a href="{{ route('sales_targets.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
                    </div>
                    <form method="POST" action="{{ route('sales_targets.store') }}">
                        @csrf
                        @include('backend.sr_management.sales_target._form', ['target' => null])
                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary">Save</button>
                            <a href="{{ route('sales_targets.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_js')
    <script src="{{ url('assets') }}/plugins/select2/select2.min.js"></script>
    <script>
        $(function() {
            $('#user_id').select2({
                placeholder: 'Search employee...',
                allowClear: true,
                width: '100%',
                ajax: {
                    url: "{{ route('sales_targets.users_list') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return { q: params.term, limit: 10 };
                    },
                    processResults: function(data) {
                        return { results: data.results || [] };
                    }
                },
                minimumInputLength: 0
            });
        });
    </script>
@endsection
