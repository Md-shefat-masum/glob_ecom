@extends('backend.master')

@section('header_css')
    <link href="{{url('assets')}}/plugins/dropify/dropify.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('page_title')
    Testimonial
@endsection
@section('page_heading')
    Edit Testimonial
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-3">Testimonial Update Form</h4>
                        <a href="{{ route('ViewTestimonials')}}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>
                    <form class="needs-validation" method="POST" action="{{url('update/testimonial')}}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="slug" value="{{$data->slug}}">

                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="image">Customer Image <span class="text-danger">*</span></label>
                                    <input type="file" name="image" class="dropify" data-height="200" data-max-file-size="1M" accept="image/*"/>
                                </div>
                            </div>
                            <div class="col-lg-8 border-right">

                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="name">Customer Name <span class="text-danger">*</span></label>
                                            <input type="text" id="name" value="{{$data->customer_name}}" name="name" class="form-control" placeholder="Enter Product Name Here" required>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('name')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="designation">Designation</label>
                                            <input type="text" id="designation" value="{{$data->designation}}"  name="designation" class="form-control" placeholder="Designation">
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('designation')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label for="rating">Rating <span class="text-danger">*</span></label>
                                    <select name="rating" class="form-control" id="rating" required>
                                        <option value="">Select One</option>
                                        <option value="1" style="color: orange;" @if($data->rating == 1) selected @endif> &#9734; </option>
                                        <option value="2" style="color: orange;" @if($data->rating == 2) selected @endif> &#9734; &#9734; </option>
                                        <option value="3" style="color: orange;" @if($data->rating == 3) selected @endif> &#9734; &#9734; &#9734; </option>
                                        <option value="4" style="color: orange;" @if($data->rating == 4) selected @endif> &#9734; &#9734; &#9734; &#9734; </option>
                                        <option value="5" style="color: orange;" @if($data->rating == 5) selected @endif> &#9734; &#9734; &#9734; &#9734; &#9734; </option>
                                    </select>
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('rating')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="description">Description <span class="text-danger">*</span></label>
                                    <textarea id="description" name="description" class="form-control" placeholder="Write Testimonial Here" required>{{$data->description}}</textarea>
                                    <div class="invalid-feedback" style="display: block;">
                                        @error('description')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group text-center pt-3">
                                    <button class="btn btn-primary" type="submit">Update Testimonial</button>
                                </div>
                            </div>
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

    <script type="text/javascript">

        $(document).ready(function () {
            $('#subcategory_id').on('change', function () {
                var subCategoryId = this.value;
                $("#childcategory_id").html('');
                $.ajax({
                    url: "{{url('/subcategory/wise/childcategory')}}",
                    type: "POST",
                    data: {
                        subcategory_id: subCategoryId,
                        _token: '{{csrf_token()}}'
                    },
                    dataType: 'json',
                    success: function (result) {
                        $('#childcategory_id').html('<option value="">Select Child Category</option>');
                        $.each(result, function (key, value) {
                            $("#childcategory_id").append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    }
                });
            });
        });
    </script>

    <script>
        @if($data->customer_image && file_exists(public_path($data->customer_image)))
            $(".dropify-preview").eq(0).css("display", "block");
            $(".dropify-clear").eq(0).css("display", "block");
            $(".dropify-filename-inner").eq(0).html("{{$data->customer_image}}");
            $("span.dropify-render").eq(0).html("<img src='{{url($data->customer_image)}}'>");
        @endif
    </script>
@endsection
