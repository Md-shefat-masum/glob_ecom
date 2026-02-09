@extends('backend.master')

@section('header_css')
    <link href="{{url('assets')}}/plugins/dropify/dropify.min.css" rel="stylesheet" type="text/css" />
    <link href="{{url('assets')}}/plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="{{url('assets')}}/css/tagsinput.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <style>
        .select2-selection{
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
    Product Supplier
@endsection
@section('page_heading')
    Edit Product Supplier
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-3">Update Form</h4>
                         <a href="{{ route('ViewAllProductSupplier')}}" class="btn btn-secondary">
                             <i class="fas fa-arrow-left"></i>
                         </a>
                     </div>

                    <form class="needs-validation" method="POST" action="{{url('update/product-supplier')}}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="product_supplier_id" value="{{$data->id}}">
                        <div class="row">
                            <div class="col-lg-12">

                                <div class="row">
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="image">Image <span class="text-danger">*</span></label>
                                            <input type="file" name="image" class="dropify" data-height="200" data-max-file-size="1M" accept="image/*"/>
                                        </div>
                                    </div>
                                    <div class="col-lg-9">

                                        <div class="form-group">
                                            <label for="supplier_type">Supplier Type <span class="text-danger">*</span></label>
                                            <select id="supplier_type" name="supplier_type" class="form-control" >
                                                <option value="" disabled selected>Select Supplier</option>
                                                @foreach($supplier_type as $type)
                                                    <option value="{{ $type->id }}" 
                                                        {{ old('supplier_type', $data->supplier_type) == $type->id ? 'selected' : '' }}>
                                                        {{ $type->title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('supplier_type')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="name">Name <span class="text-danger">*</span></label>
                                            <input type="text" id="name" name="name" class="form-control" value="{{$data->name}}" placeholder="Enter Product name Here">
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('name')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>



                                        <div class="form-group">
                                            <label for="contact_number">Contact Number <span class="text-danger">*</span></label>
                                            <input type="text" id="contact_number" name="contact_number" maxlength="255" class="form-control" value="{{$data->contact->contact_number}}" placeholder="Enter Product Supplier Contact Number Here" >
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('contact_number')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select name="status" id="status" class="form-control custom-select">
                                                <option value="active" {{ $data->status == 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ $data->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            </select>                                         
                                        </div>


                                        <div class="form-group">
                                            <label for="address">Address</label>
                                            <textarea id="address" name="address" class="form-control" placeholder="Enter Address Here">{{$data->address}}</textarea>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('address')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>

                                    </div>
                                </div>


                            </div>
                        </div>


                        <div class="form-group text-center pt-3">
                            <a href="{{ route('ViewAllProductSupplier')}}" style="width: 130px;" class="btn btn-danger d-inline-block text-white m-2" type="submit"><i class="mdi mdi-cancel"></i> Cancel</a>
                            <button class="btn btn-primary m-2" style="width: 130px;" type="submit"><i class="fas fa-save"></i> Update</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('footer_js')
    <script src="{{url('assets')}}/plugins/dropify/dropify.min.js"></script>
    <script src="{{url('assets')}}/pages/fileuploads-demo.js"></script>
    <script src="{{url('assets')}}/plugins/select2/select2.min.js"></script>
    <script src="{{url('assets')}}/js/tagsinput.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script type="text/javascript">

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('[data-toggle="select2"]').select2();


        $('#description').summernote({
            placeholder: 'Write Description Here',
            tabsize: 2,
            height: 350
        });

        @if($data->image && file_exists(public_path($data->image)))
            $(".dropify-preview").eq(0).css("display", "block");
            $(".dropify-clear").eq(0).css("display", "block");
            $(".dropify-filename-inner").eq(0).html("{{$data->image}}");
            $("span.dropify-render").eq(0).html("<img src='{{url($data->image)}}'>");
        @endif

    </script>
@endsection