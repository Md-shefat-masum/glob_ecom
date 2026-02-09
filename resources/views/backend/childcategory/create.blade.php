@extends('backend.master')

@section('page_title')
    Child Category
@endsection
@section('page_heading')
    Add New Child Category
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-3">Child Category Create Form</h4>
                        <a href="{{ route('ViewAllChildcategory')}}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>

                    <form class="needs-validation" method="POST" action="{{url('save/new/childcategory')}}" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group row">
                            <label for="colFormLabe0" class="col-sm-2 col-form-label">Select Category <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <select name="category_id" class="form-control" id="colFormLabe0" required>
                                    @php
                                        echo App\Models\Category::getDropDownList('name');
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
                                <input type="text" name="name" class="form-control" id="colFormLabel" placeholder="Child Category Title" required>
                                <div class="invalid-feedback" style="display: block;">
                                    @error('name')
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
