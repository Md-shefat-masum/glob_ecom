@extends('backend.master')

@section('header_css')
    <link href="{{ url('assets') }}/plugins/dropify/dropify.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ url('assets') }}/plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />
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
    Customer Next Contact Date
@endsection
@section('page_heading')
    Add a Customer Next Contact Date
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                   
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-3">Customer Next Contact Date</h4>
                        <a href="{{ route('ViewAllCustomerNextContactDate')}}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>

                    <form class="needs-validation" method="POST" action="{{ url('save/new/customer-next-contact-date') }}"
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
                                            <label for="date">Next Date <span class="text-danger">*</span></label>
                                            <input type="date" id="next_date" name="next_date" maxlength="255"
                                                class="form-control" placeholder="Enter next date Here">
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('next_date')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="contact_status">Contact Status <span class="text-danger">*</span></label>
                                            <select id="contact_status" name="contact_status" class="form-control">
                                                <option value="" disabled selected>Select Contact Status</option>
                                                <option value="pending">Pending</option>                                                
                                                <option value="missed">Missed</option>                                                
                                                <option value="done">Done</option>                                                                                                
                                            </select>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('contact_status')
                                                    <strong>{{ $message }}</strong>
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
    <script src="{{ url('assets') }}/plugins/select2/select2.min.js"></script>
    <script src="{{ url('assets') }}/js/tagsinput.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#customer_id').select2({
                placeholder: 'Select Customer',
                allowClear: true,
                width: '100%'
            });

            $('#employee_id').select2({
                placeholder: 'Select Employee',
                allowClear: true,
                width: '100%'
            });

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
