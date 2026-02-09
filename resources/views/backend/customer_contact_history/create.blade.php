@extends('backend.master')

@section('header_css')
    <link href="{{ url('assets') }}/plugins/dropify/dropify.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ versioned_url('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ url('assets') }}/css/tagsinput.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <style>
        .select2-selection {
            height: 34px !important;
            border: 1px solid #ced4da !important;
        }

        .select2 {
            width: 100% !important;
        }

        .bootstrap-tagsinput .badge {
            margin: 2px 2px !important;
        }

        .select2-container .select2-selection--single {
            height: 38px;
            padding: 6px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }
    </style>
@endsection

@section('page_title')
    Customer Contact History
@endsection
@section('page_heading')
    Add a Customer Contact History
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                 
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-3">Customer Contact History</h4>
                        <a href="{{ route('ViewAllCustomerContactHistories')}}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>

                    <form class="needs-validation" method="POST" action="{{ url('save/new/customer-contact-history') }}"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-lg-12">

                                <div class="row">


                                    <div class="col-lg-6">

                                        <div class="form-group">
                                            <label for="customer_id">Customer <span
                                                    class="text-danger">*</span></label>
                                            <select id="customer_id" name="customer_id"
                                                class="form-control">
                                                {{-- <option value="" disabled selected>Select Customer Category</option> --}}
                                                <option></option>
                                                @foreach ($customers as $customer)
                                                    <option value="{{ $customer->id }}"
                                                        {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                                        {{ $customer->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('customer_id')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="employee_id">Employee<span
                                                    class="text-danger">*</span></label>
                                            <select id="employee_id" name="employee_id"
                                                class="form-control">
                                                <option></option>
                                                @foreach ($users as $employee)
                                                    <option value="{{ $employee->id }}"
                                                        {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                                        {{ $employee->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('employee_id')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="contact_history_status">Contact History Status <span class="text-danger">*</span></label>
                                            <select id="contact_history_status" name="contact_history_status" class="form-control">
                                                <option value="" disabled selected>Select Contact Status</option>
                                                <option value="planned">Planned</option>                                                
                                                <option value="held">Held</option>                                                
                                                <option value="not_held">Not Held</option>                                                                                                
                                            </select>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('contact_history_status')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="priority">Priority <span class="text-danger">*</span></label>
                                            <select id="priority" name="priority" class="form-control">
                                                <option value="" disabled selected>Select Priority</option>
                                                <option value="low">Low</option>                                                
                                                <option value="normal">Normal</option>                                                
                                                <option value="medium">Medium</option>                                                                                                
                                                <option value="high">High</option>                                                                                                
                                                <option value="urgent">Urgent</option>                                                                                                
                                                <option value="immediate">Immediate</option>                                                                                                
                                            </select>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('priority')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="date">Next Contact Date <span class="text-danger">*</span></label>
                                            <input type="date" id="next_date" name="next_date" maxlength="255"
                                                class="form-control" placeholder="Enter next date Here">
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('next_date')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- 
                                        <div class="form-group">
                                            <label for="date">Date <span class="text-danger">*</span></label>
                                            <input type="date" id="date" name="date" maxlength="255"
                                                class="form-control" placeholder="Enter date Here">
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('date')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div> --}}

                                        <div class="form-group">
                                            <label for="product_id">Product</label>
                                            <select id="product_id" name="product_id" class="form-control">
                                                <option></option>
                                            </select>
                                            <input type="hidden" id="product_name" name="product_name" value="">
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('product_id')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="subject">Subject</label>
                                            <textarea id="subject" name="subject" class="form-control" rows="3" placeholder="Enter Subject Here">{{ old('subject') }}</textarea>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('subject')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="note">Note</label>
                                            <textarea id="note" name="note" class="form-control" placeholder="Enter Note Here"></textarea>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('note')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>


                        <div class="form-group text-center pt-3">
                            <a href="{{ url('view/all/customer') }}" style="width: 130px;"
                                class="btn btn-danger d-inline-block text-white m-2" type="submit"><i
                                    class="mdi mdi-cancel"></i> Cancel</a>
                            <button class="btn btn-primary m-2" style="width: 130px;" type="submit"><i
                                    class="fas fa-save"></i> Save </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('footer_js')
    <script src="{{ url('assets') }}/plugins/dropify/dropify.min.js"></script>
    <script src="{{ url('assets') }}/pages/fileuploads-demo.js"></script>
    <script src="{{ versioned_url('assets/plugins/select2/select2.min.js') }}"></script>
    <script src="{{ url('assets') }}/js/tagsinput.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#customer_id').select2({
                placeholder: 'Select Customer',
                allowClear: true,
                width: '100%'
            });

            // Product Select2 with AJAX
            $('#product_id').select2({
                placeholder: 'Search and select a product (optional)',
                allowClear: true,
                width: '100%',
                ajax: {
                    url: '{{ route("product-management.search-products") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1,
                            limit: 20
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.results || []
                        };
                    },
                    cache: true
                },
                minimumInputLength: 1,
                templateResult: function(item) {
                    if (item.loading) {
                        return item.text;
                    }
                    var price = item.price ? ' · ৳' + parseFloat(item.price).toFixed(2) : '';
                    return $('<span>' + item.text + price + '</span>');
                },
                templateSelection: function(item) {
                    return item.text || item.name;
                }
            });

            // Store product name when product is selected
            $('#product_id').on('select2:select', function (e) {
                var data = e.params.data;
                $('#product_name').val(data.text || data.name || '');
            });

            // Clear product name when product is cleared
            $('#product_id').on('select2:clear', function (e) {
                $('#product_name').val('');
            });

            // $('#employee_id').select2({
            //     placeholder: 'Select Employee',
            //     allowClear: true,
            //     width: '100%'
            // });

        });
    </script>

    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#description').summernote({
            placeholder: 'Write Description Here',
            tabsize: 2,
            height: 350
        });
    </script>
@endsection
