@extends('backend.master')

@section('header_css')
    <style>
        h4.card-title{
            background: linear-gradient(to right, #17263A, #2c3e50, #17263A);
            padding: 8px 15px;
            border-radius: 4px;
            color: white;
        }
        .graph_card{
            position: relative
        }
        .graph_card i{
            position: absolute;
            top: 18px;
            right: 18px;
            font-size: 18px;
            height: 35px;
            width: 35px;
            line-height: 33px;
            text-align: center;
            border-radius: 50%;
            font-weight: 300;
        }
    </style>
@endsection

@section('page_title')
    Dashboard
@endsection

@section('page_heading')
    Overview
@endsection

@section('content')

    <div class="row">
        <div class="col-lg-6 col-xl-3">
            <div class="card graph_card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="text-uppercase font-size-12 text-muted mb-3">
                                This month total orders
                            </h6>
                            <span class="h3 mb-0"> {{ number_format($countOrders[0]) }} </span>
                        </div>
                    </div> <!-- end row -->
                </div> <!-- end card-body-->
                <i class="feather-shopping-cart" style="color: #0074E4; background: #0074E42E;"></i>
            </div> <!-- end card-->
        </div> <!-- end col-->

        <div class="col-lg-6 col-xl-3">
            <div class="card graph_card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="text-uppercase font-size-12 text-muted mb-3">
                                this month orders amount
                            </h6>
                            <span class="h3 mb-0"> ৳ {{ number_format($totalOrderAmount[0], 2) }} </span>
                        </div>
                    </div> <!-- end row -->
                </div> <!-- end card-body-->
                <i class="feather-trending-up" style="color: #17263A; background: #17263A3D;"></i>
            </div> <!-- end card-->
        </div> <!-- end col-->

        <div class="col-lg-6 col-xl-3">
            <div class="card graph_card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="text-uppercase font-size-12 text-muted mb-3">
                                Todays orders
                            </h6>
                            <span class="h3 mb-0"> {{ number_format($todaysOrder[0]) }} </span>
                            <span class="h3 mb-0"> <a target="_blank" href="{{url('view/pending/orders')}}" style="height: 20px; line-height: 10px; margin-top: -4px;" class="btn btn-sm btn-success rounded">View All</a> </span>
                        </div>
                    </div> <!-- end row -->
                </div> <!-- end card-body-->
                <i class="feather-package" style="color: #c28a00; background: #daa5202e;"></i>
            </div> <!-- end card-->
        </div> <!-- end col-->

        <div class="col-lg-6 col-xl-3">
            <div class="card graph_card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="text-uppercase font-size-12 text-muted mb-3">
                                This month new customers
                            </h6>
                            <span class="h3 mb-0"> {{ number_format($registeredUsers[0]) }} </span>
                        </div>
                    </div> <!-- end row -->
                </div> <!-- end card-body-->
                <i class="feather-users" style="color: #a60000; background: #a6000026;"></i>
            </div> <!-- end card-->
        </div> <!-- end col-->
    </div>
    <!-- end row-->

@endsection

@section('footer_js')

@endsection
