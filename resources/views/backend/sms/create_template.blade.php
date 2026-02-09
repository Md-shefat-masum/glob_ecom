@extends('backend.master')

@section('page_title')
    SMS Service
@endsection
@section('page_heading')
    SMS Template
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                   
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-3">Create New SMS Template</h4>
                         <a href="{{ route('ViewSmsTemplates')}}" class="btn btn-secondary">
                             <i class="fas fa-arrow-left"></i>
                         </a>
                     </div>

                    <form class="needs-validation" method="POST" action="{{url('save/sms/template')}}" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group row">
                            <label for="title" class="col-sm-2 col-form-label">Template Title : <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" name="title" class="form-control" id="title" placeholder="Coupon Offer SMS for 1st Time Order Customers" required>
                                <div class="invalid-feedback" style="display: block;">
                                    @error('title')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="description" class="col-sm-2 col-form-label">Template Description :</label>
                            <div class="col-sm-10">
                                <textarea name="description" class="form-control" rows="10" id="description" placeholder="Write SMS Template Here"></textarea>
                                <div class="invalid-feedback" style="display: block;">
                                    @error('description')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-save"></i> Save Template</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
