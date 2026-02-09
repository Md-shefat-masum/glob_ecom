@extends('backend.master')

@section('page_title')
    Child Category
@endsection
@section('page_heading')
    Edit Child Category
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-3">Child Category Update Form</h4>
                        <a href="{{ route('ViewAllChildcategory')}}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>

                    <form class="needs-validation" method="POST" action="{{url('update/childcategory')}}" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="slug" value="{{$childcategory->slug}}">
                        <input type="hidden" name="id" value="{{$childcategory->id}}">

                        <div class="form-group row">
                            <label for="colFormLabe0" class="col-sm-2 col-form-label">Select Category <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <select name="category_id" class="form-control" id="colFormLabe0" required>
                                    @php
                                        echo App\Models\Category::getDropDownList('name', $childcategory->category_id);
                                    @endphp
                                </select>
                                <div class="invalid-feedback" style="display: block;">
                                    @error('category_id')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="colFormLabe2" class="col-sm-2 col-form-label">Select Subcategory <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <select name="subcategory_id" class="form-control" id="colFormLabe2" required>
                                    <option value="">Select One</option>
                                    @foreach ($subcategories as $subcategory)
                                    <option value="{{$subcategory->id}}" @if($subcategory->id == $childcategory->subcategory_id) selected @endif>{{$subcategory->name}}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" style="display: block;">
                                    @error('subcategory_id')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="colFormLabel" class="col-sm-2 col-form-label">Name <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" name="name" class="form-control" value="{{$childcategory->name}}" id="colFormLabel" placeholder="Child Category Title" required>
                                <div class="invalid-feedback" style="display: block;">
                                    @error('name')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="colFormLabe02" class="col-sm-2 col-form-label">Status <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <select name="status" class="form-control" id="colFormLabe02" required>
                                    <option value="">Select One</option>
                                    <option value="1" @if($childcategory->status == 1) selected @endif>Active</option>
                                    <option value="0" @if($childcategory->status == 0) selected @endif>Inactive</option>
                                </select>
                                <div class="invalid-feedback" style="display: block;">
                                    @error('status')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button class="btn btn-primary" type="submit">Save Child Category</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('footer_js')
<script>

    $(document).ready(function () {

        $('#colFormLabe0').on('change', function () {
            var categoryId = this.value;
            $("#colFormLabe2").html('');
            $.ajax({
                url: "{{url('/category/wise/subcategory')}}",
                type: "POST",
                data: {
                    category_id: categoryId,
                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function (result) {
                    $('#colFormLabe2').html('<option value="">Select Subcategory</option>');
                    $.each(result, function (key, value) {
                        $("#colFormLabe2").append('<option value="' + value.id + '">' + value.name + '</option>');
                    });
                }
            });
        });

    });
</script>
@endsection
