@extends('backend.master')

@section('page_title')
    Product
@endsection
@section('page_heading')
    Generate Demo Products
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Generate Demo Products</h4>
                    <div class="row">
                        <div class="col-lg-5" style="border: 1px solid #f7f7f7">
                            <img src="{{url('assets')}}/images/demo_products.png" class="img-fluid">
                        </div>
                        <div class="col-lg-7 p-5" style="background: #f7f7f7">
                            <span style="font-size: 15px; color: #1e1e1e;">
                            Demo products involves showcasing the features, benefits, and functionality of the products in a way that helps Stack Holders to understand the system in a better way. These are just dummies so please don't rely on them. But Remember you shouldn't take actual order based on these products as these are not actually exists. Upload your own products by following these demo. To generate demo products mention the number of products on inside the field below and hit the generate button.
                            </span>

                            <form action="{{url('save/generated/demo/products')}}" method="POST" class="mt-4">
                                @csrf

                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>Demo Products Type</label>
                                            <select class="form-control" name="product_type">
                                                <option value="1">Fashion</option>
                                                <option value="2">Tech</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="form-group">
                                            <label>No of Demo Products to be Generated</label>
                                            <input type="number" class="form-control" name="products" value="100" placeholder="100" required>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-success rounded w-100 d-block"><i class="feather-upload"></i> Generate Demo Products</button>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
