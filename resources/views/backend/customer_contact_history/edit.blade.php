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
    </style>
@endsection

@section('page_title')
    Edit
@endsection
@section('page_heading')
    Edit Customer Contact History
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                  
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-3">Update Form</h4>
                        <a href="{{ route('ViewAllCustomerContactHistories')}}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>

                    <form class="needs-validation" method="POST"
                        action="{{ url('update/customer-contact-history') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="customer_contact_history_id" value="{{ $data->id }}">
                        <div class="row">
                            <div class="col-lg-12">

                                <div class="row">

                                    <div class="col-lg-6">

                                        
                                        <div class="form-group">
                                            <label for="customer_id">Customer<span
                                                    class="text-danger">*</span></label>
                                            <select id="customer_id" name="customer_id"
                                                class="form-control">
                                                <option></option>
                                                @foreach ($customers as $customer)
                                                    <option value="{{ $customer->id }}"
                                                        {{ $customer->id == $data->customer_id ? 'selected' : '' }}>
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
                                                        {{ $employee->id == $data->employee_id ? 'selected' : '' }}>
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
                                            <label for="contact_history_status">Contact History Status</label>
                                            <select name="contact_history_status" id="contact_history_status" class="form-control custom-select">
                                                <option value="planned" {{ $data->contact_history_status == 'planned' ? 'selected' : '' }}>Planned</option>
                                                <option value="held" {{ $data->contact_history_status == 'held' ? 'selected' : '' }}>Held</option>
                                                <option value="not_held" {{ $data->contact_history_status == 'not_held' ? 'selected' : '' }}>Not Held</option>
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

                                                <option value="low" {{ $data->priority == 'low' ? 'selected' : '' }}>Low</option>
                                                <option value="normal" {{ $data->priority == 'normal' ? 'selected' : '' }}>Normal</option>
                                                <option value="medium" {{ $data->priority == 'medium' ? 'selected' : '' }}>Medium</option>
                                                <option value="high" {{ $data->priority == 'high' ? 'selected' : '' }}>High</option>
                                                <option value="urgent" {{ $data->priority == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                                <option value="immediate" {{ $data->priority == 'immediate' ? 'selected' : '' }}>Immediate</option>

                                            </select>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('priority')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="date">Next Date <span class="text-danger">*</span></label>
                                            <input type="date" id="next_date" name="next_date"
                                                class="form-control" value="{{ $next_contact_data->next_date ?? '' }}" placeholder="Enter next date Here">
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('next_date')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- <div class="form-group">
                                            <label for="date">Date <span class="text-danger">*</span></label>
                                            <input type="date" id="date" name="date"
                                                class="form-control" value="{{ $data->date }}" placeholder="Enter date Here">
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('date')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div> --}}

                                        <div class="form-group">
                                            <label for="product_id">Product</label>
                                            <select id="product_id" name="product_id" class="form-control">
                                                @if($data->product_id)
                                                    <option value="{{ $data->product_id }}" selected>{{ $data->product_name }}</option>
                                                @else
                                                    <option></option>
                                                @endif
                                            </select>
                                            <input type="hidden" id="product_name" name="product_name" value="{{ $data->product_name ?? '' }}">
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('product_id')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="subject">Subject</label>
                                            <textarea id="subject" name="subject" class="form-control" rows="3" placeholder="Enter Subject Here">{{ $data->subject ?? old('subject') }}</textarea>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('subject')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="note">Note</label>
                                            <textarea id="note" name="note" class="form-control" placeholder="Enter Note Here">
                                                {{ $data->note }}
                                            </textarea>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('note')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select name="status" id="status" class="form-control custom-select">
                                                <option value="active" {{ $data->status == 'active' ? 'selected' : '' }}>
                                                    Active</option>
                                                <option value="inactive"
                                                    {{ $data->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="form-group text-center pt-3">
                            <a href="{{ url('view/all/customer-contact-history') }}" style="width: 130px;"
                                class="btn btn-danger d-inline-block text-white m-2" type="submit"><i
                                    class="mdi mdi-cancel"></i> Cancel</a>
                            <button class="btn btn-primary m-2" style="width: 130px;" type="submit"><i
                                    class="fas fa-save"></i> Update</button>
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
            var productId = {{ $data->product_id ?? 'null' }};
            var productName = @json($data->product_name ?? '');
            
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

        @if ($data->image && file_exists(public_path($data->image)))
            $(".dropify-preview").eq(0).css("display", "block");
            $(".dropify-clear").eq(0).css("display", "block");
            $(".dropify-filename-inner").eq(0).html("{{ $data->image }}");
            $("span.dropify-render").eq(0).html("<img src='{{ url($data->image) }}'>");
        @endif
    </script>

    <script>
        // Update product warehouse rooms based on selected product warehouse
        document.getElementById('product_warehouse_id').addEventListener('change', function() {
            var warehouseId = this.value;
            if (warehouseId) {
                // AJAX request to fetch related rooms
                fetch(`/get-warehouse-rooms/${warehouseId}`)
                    .then(response => response.json())
                    .then(data => {
                        var roomSelect = document.getElementById('product_warehouse_room_id');
                        roomSelect.innerHTML = '<option value="">Select Room</option>';

                        data.rooms.forEach(room => {
                            var option = document.createElement('option');
                            option.value = room.id;
                            option.textContent = room.title;
                            roomSelect.appendChild(option);
                        });
                    });
            }
        });
    </script>
@endsection
