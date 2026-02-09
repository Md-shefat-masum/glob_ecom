@extends('backend.master')

@section('page_title')
    Model
@endsection
@section('page_heading')
    Add New Model
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                   
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-3">Model Create Form</h4>
                        <a href="{{ route('ViewAllModels')}}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>

                    <form class="needs-validation" method="POST" action="{{url('save/new/model')}}" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group row">
                            <label for="colFormLabe0" class="col-sm-2 col-form-label">Select Brand <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <select name="brand_id" class="form-control" id="colFormLabe0" required>
                                    @php
                                        echo App\Models\Brand::getDropDownList('name');
                                    @endphp
                                </select>
                                <div class="invalid-feedback" style="display: block;">
                                    @error('brand_id')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="colFormLabel" class="col-sm-2 col-form-label">Model Name <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" name="name" class="form-control" id="colFormLabel" placeholder="Model Name" required>
                                <div class="invalid-feedback" style="display: block;">
                                    @error('name')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="colFormLabel" class="col-sm-2 col-form-label">Model Code </label>
                            <div class="col-sm-10">
                                <input type="text" name="code" class="form-control" id="colFormLabel" placeholder="Model Code">
                                <div class="invalid-feedback" style="display: block;">
                                    @error('code')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="colFormLabel" class="col-sm-2 col-form-label"></label>
                            <div class="col-sm-10">
                                <button class="btn btn-primary" type="submit">Save Model</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
