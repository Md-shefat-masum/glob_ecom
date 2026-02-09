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

            /* animation-name: rotate;
            animation-duration: 5s;
            animation-iteration-count: infinite;
            animation-timing-function: linear; */
        }

        /* @keyframes rotate{
            from{ transform: rotate(-360deg); }
            to{ transform: rotate(360deg); }
        } */

    </style>
@endsection

@section('page_title')
    CRM Dashboard
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
                            <h6 class="text-uppercase font-size-12 text-muted mb-3">Customer Source Types</h6>
                            <span class="h3 mb-0"> {{ number_format($totalCustomerSourceTypes) }} </span>
                        </div>
                    </div> <!-- end row -->

                    <div id="sparkline1" class="mt-3"></div>
                </div> <!-- end card-body-->
                <i class="feather-trending-up" style="color: #0074E4; background: #0074E42E;"></i>
            </div> <!-- end card-->
        </div> <!-- end col-->

        <div class="col-lg-6 col-xl-3">
            <div class="card graph_card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="text-uppercase font-size-12 text-muted mb-3">Customer Category</h6>
                            <span class="h3 mb-0"> ৳ {{ number_format($totalCustomerCategory) }} </span>
                        </div>
                    </div> <!-- end row -->
                    <div id="sparkline2" class="mt-3"></div>
                </div> <!-- end card-body-->
                <i class="feather-trending-up" style="color: #17263A; background: #17263A3D;"></i>
            </div> <!-- end card-->
        </div> <!-- end col-->

        <div class="col-lg-6 col-xl-3">
            <div class="card graph_card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="text-uppercase font-size-12 text-muted mb-3">Customer Contact History</h6>
                            <span class="h3 mb-0"> {{ number_format($totalCustomerContactHistory) }} </span>
                        </div>
                    </div> <!-- end row -->

                    <div id="sparkline3" class="mt-3"></div>
                </div> <!-- end card-body-->
                <i class="feather-users" style="color: #c28a00; background: #daa5202e;"></i>
            </div> <!-- end card-->
        </div> <!-- end col-->

        <div class="col-lg-6 col-xl-3">
            <div class="card graph_card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="text-uppercase font-size-12 text-muted mb-3">Pending Next Contact Date</h6>
                            <span class="h3 mb-0"> {{ number_format($totalPendingCustomerNextContactDate) }} </span>
                        </div>
                    </div> <!-- end row -->

                    <div id="sparkline4" class="mt-3"></div>
                </div> <!-- end card-body-->
                <i class="feather-users" style="color: #a60000; background: #a6000026;"></i>
            </div> <!-- end card-->
        </div> <!-- end col-->


        <div class="col-lg-6 col-xl-3">
            <div class="card graph_card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="text-uppercase font-size-12 text-muted mb-3">Customers</h6>
                            <span class="h3 mb-0"> {{ number_format($totalCustomers) }} </span>
                        </div>
                    </div> <!-- end row -->

                    <div id="sparkline3" class="mt-3"></div>
                </div> <!-- end card-body-->
                <i class="feather-users" style="color: #c28a00; background: #daa5202e;"></i>
            </div> <!-- end card-->
        </div> <!-- end col-->


        <div class="col-lg-6 col-xl-3">
            <div class="card graph_card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="text-uppercase font-size-12 text-muted mb-3">Customers of Last 30 Days</h6>
                            <span class="h3 mb-0"> {{ number_format($totalLastThirtyDaysCustomers) }} </span>
                        </div>
                    </div> <!-- end row -->

                    <div id="sparkline4" class="mt-3"></div>
                </div> <!-- end card-body-->
                <i class="feather-users" style="color: #a60000; background: #a6000026;"></i>
            </div> <!-- end card-->
        </div> <!-- end col-->


        <div class="col-lg-6 col-xl-3">
            <div class="card graph_card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="text-uppercase font-size-12 text-muted mb-3">Customers of Last 6 Months</h6>
                            <span class="h3 mb-0"> {{ number_format($totalLastSixMonthsCustomers) }} </span>
                        </div>
                    </div> <!-- end row -->

                    <div id="sparkline3" class="mt-3"></div>
                </div> <!-- end card-body-->
                <i class="feather-users" style="color: #c28a00; background: #daa5202e;"></i>
            </div> <!-- end card-->
        </div> <!-- end col-->


        <div class="col-lg-6 col-xl-3">
            <div class="card graph_card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="text-uppercase font-size-12 text-muted mb-3">Customers of This Month</h6>
                            <span class="h3 mb-0"> {{ number_format($totalThisMonthCustomers) }} </span>
                        </div>
                    </div> <!-- end row -->

                    <div id="sparkline4" class="mt-3"></div>
                </div> <!-- end card-body-->
                <i class="feather-users" style="color: #a60000; background: #a6000026;"></i>
            </div> <!-- end card-->
        </div> <!-- end col-->



    </div>
    <!-- end row-->

 

    <div class="row">
        {{-- Recent Customers --}}
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body" style="min-height: 435px;">

                    <h4 class="card-title">Recent Customers</h4>

                    <div class="table-responsive">
                        <table class="table table-centered table-striped table-nowrap">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Location</th>
                                    <th>Create Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($orderPayments) > 0)
                                @foreach ($recentCustomers as $customer)
                                    <tr>
                                        <td class="table-user">
                                            @if ($customer->image && file_exists(public_path($customer->image)))
                                                <img src="{{ $customer->image }}" alt="table-user" class="mr-2 avatar-xs rounded-circle">
                                            @else
                                                <img src="assets/images/users/avatar-4.jpg" alt="table-user" class="mr-2 avatar-xs rounded-circle">
                                            @endif
                                            <a href="javascript:void(0);" class="text-body font-weight-semibold">{{ $customer->name }}</a>
                                        </td>
                                        <td>
                                            {{ $customer->phone }}
                                        </td>
                                        <td>
                                            {{ $customer->email }}
                                        </td>
                                        <td>
                                            {{ $customer->address }}
                                        </td>
                                        <td>
                                            {{ date('h:i:s A, jS F Y', strtotime($customer->created_at)) }}
                                        </td>
                                    </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="5" class="text-center">No Customers Found</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col -->

        {{-- Thana Orders --}}
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body" style="min-height: 435px;">

                    <h4 class="card-title">Thana Orders</h4>

                    <div class="table-responsive">
                        <table class="table table-centered table-striped table-nowrap">
                            <thead>
                                <tr>
                                    <th>Thana</th>
                                    <th>Order</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($thanaOrders) > 0)
                                @foreach ($thanaOrders as $thanaOrder)
                                    <tr>
                                        <td>{{ $thanaOrder->thana }}</td>
                                        <td>{{ $thanaOrder->order_count }}</td>
                                    </tr>
                                @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center">No Order Found</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col -->

        {{-- City Orders --}}
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body" style="min-height: 435px;">

                    <h4 class="card-title">City Orders</h4>

                    <div class="table-responsive">
                        <table class="table table-centered table-striped table-nowrap">
                            <thead>
                                <tr>
                                    <th>Thana</th>
                                    <th>Order</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($cityOrders ) > 0)
                                @foreach ($cityOrders  as $cityOrder)
                                    <tr>
                                        <td>{{ $cityOrder->city }}</td>
                                        <td>{{ $cityOrder->order_count  }}</td>
                                    </tr>
                                @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center">No Order Found</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col -->

        {{-- Customer Source Type --}}
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body" style="min-height: 435px;">

                    <h4 class="card-title">Customer Source Type</h4>

                    <div class="table-responsive">
                        <table class="table table-centered table-striped table-nowrap">
                            <thead>
                                <tr>
                                    <th>Customer Source</th>
                                    <th>Order Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($ordersBySource  ) > 0)
                                @foreach ($ordersBySource   as $order)
                                    <tr>
                                        <td>{{ $order->customerSourceType->title ?? 'Unknown' }}</td>
                                        <td>{{ $order->order_count  }}</td>
                                    </tr>
                                @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center">No Order Found</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col -->

    </div>
    <!-- end row-->
@endsection

@section('footer_js')
   
@endsection
