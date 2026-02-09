@extends('backend.master')

@section('header_css')
    <style>
        
    </style>
@endsection

@section('page_title')
    Product Other Charge
@endsection
@section('page_heading')
    Add a Product Other Charge
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-3">Product Other Charge</h4>
                         <a href="{{ route('ViewAllPurchaseProductCharge')}}" class="btn btn-secondary">
                             <i class="fas fa-arrow-left"></i>
                         </a>
                     </div>

                    <form class="needs-validation" method="POST" action="{{url('save/new/purchase-product/charge')}}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-lg-12">

                                <div class="row">

                                    <div class="col-lg-9">

                                        <div class="form-group">
                                            <label for="title">Title <span class="text-danger">*</span></label>
                                            <input type="text" id="title" name="title" maxlength="255" class="form-control" placeholder="Other Charge Title" >
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('title')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="type">Type <span class="text-danger">*</span></label>
                                            <select id="type" name="type" class="form-control">
                                                <option value="">Select Type</option>
                                                <option value="percent">Percentage</option>
                                                <option value="fixed">Fixed</option>
                                            </select>
                                            <div class="invalid-feedback" style="display: block;">
                                                @error('type')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </div>
                                        </div>       
                                    </div>
                                </div>

                            </div>
                        </div>


                        <div class="form-group text-center pt-3">
                            <a href="{{url('view/all/purchase-product/charge')}}" style="width: 130px;" class="btn btn-danger d-inline-block text-white m-2" type="submit"><i class="mdi mdi-cancel"></i> Cancel</a>
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

 
@endsection