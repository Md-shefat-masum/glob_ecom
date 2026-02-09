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
    Customer Category
@endsection
@section('page_heading')
    Add a Customer Category
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-3">Customer Category</h4>
                        <a href="{{ route('ViewAllCustomerCategory')}}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>

                    <form class="needs-validation" method="POST" action="{{url('save/new/customer-category')}}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-lg-12">

                                <div class="row">

                                    {{-- <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="image">Image <span class="text-danger">*</span></label>
                                            <input type="file" name="image" class="dropify" data-height="200" data-max-file-size="1M" accept="image/*">
                                        </div>
                                    </div> --}}
                                    <div class="col-lg-6">


                                        <div class="form-group">
                                            <label for="title">Title <span class="text-danger">*</span></label>
                                            <input type="text" id="title" name="title" maxlength="255" class="form-control" placeholder="Enter Customer Category Here" >
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('title')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- <div class="form-group">
                                            <label for="code">Code <span class="text-danger">*</span></label>
                                            <input type="text" id="code" name="code" maxlength="255" class="form-control" placeholder="Enter Customer Category Code Here" >
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('code')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div> --}}

                                        <div class="form-group">
                                            <label for="description">Description</label>
                                            <textarea id="description" name="description" class="form-control" placeholder="Enter Description Here"></textarea>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('description')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>


                        <div class="form-group text-center pt-3">
                            <a href="{{url('view/all/customer-category')}}" style="width: 130px;" class="btn btn-danger d-inline-block text-white m-2" type="submit"><i class="mdi mdi-cancel"></i> Cancel</a>
                            <button class="btn btn-primary m-2" style="width: 130px;" type="submit"><i class="fas fa-save"></i> Save </button>
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

        // $('#description').summernote({
        //     placeholder: 'Write Description Here',
        //     tabsize: 2,
        //     height: 350
        // });

    </script>
@endsection