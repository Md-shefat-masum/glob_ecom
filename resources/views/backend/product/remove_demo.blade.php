@extends('backend.master')

@section('page_title')
    Product
@endsection
@section('page_heading')
    Remove Demo Products
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Remove Demo Products</h4>
                    <div class="row">
                        <div class="col-lg-5" style="border: 1px solid #fff5f5">
                            <img src="{{url('assets')}}/images/remove_demo_products.png" class="img-fluid">
                        </div>
                        <div class="col-lg-7 p-5" style="background: #fff5f5">
                            <span style="font-size: 15px; color: #1e1e1e;">
                                Demo products involves showcasing the features, benefits, and functionality of the products in a way that helps Stack Holders to understand the System in a better way. But Remember you shouldn't take actual order based on these products as these are not actually exists. To Remove all the demo please click on the button below.
                            </span>

                            <a href="{{url('remove/demo/products')}}" class="btn btn-danger rounded w-100 d-block mt-5"><i class="feather-trash-2"></i> Remove Demo Products</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
