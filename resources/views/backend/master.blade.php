<!DOCTYPE html>
<html lang="en">

@php
    $generalInfo = DB::table('general_infos')
        ->where('id', 1)
        ->select('logo', 'company_name', 'fav_icon', 'guest_checkout')
        ->first();
@endphp

<head>
    <meta charset="utf-8" />
    <title>Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#4f46e5">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">
    <link rel="manifest" href="{{ url('manifest.json') }}">
    
    @if ($generalInfo->fav_icon != '' && $generalInfo->fav_icon != null && file_exists(public_path($generalInfo->fav_icon)))
        <link rel="shortcut icon" href="{{ versioned_url($generalInfo->fav_icon) }}">
    @else
        <link rel="shortcut icon" href="{{ versioned_url('assets/images/favicon.ico') }}">
    @endif

    <!-- App css -->
    <link href="{{ versioned_url('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ versioned_url('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ versioned_url('assets/css/theme.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ versioned_url('assets/css/toastr.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ versioned_asset('assets/plugins/axios/axios.js') }}"></script>

    @yield('header_css')
    @yield('header_js')

    @stack('header_css')
    @stack('css')

    <link href="{{ versioned_url('assets/css/custom.css') }}&v2={{ time() }}" rel="stylesheet" type="text/css" />

</head>

<body>
    <!-- Begin page -->
    <div id="layout-wrapper">

        <!-- ========== Left Sidebar Start ========== -->
        <div class="vertical-menu">
            <div data-simplebar class="h-100">

                <!-- LOGO -->
                <div class="navbar-brand-box">
                    <a href="{{ url('/home') }}" class="logo mt-2" style="display: inline-block;">
                        @if ($generalInfo->logo != '' && $generalInfo->logo != null && file_exists(public_path($generalInfo->logo)))
                            <span>
                                <img src="{{ url($generalInfo->logo) }}" alt="" class="img-fluid"
                                    style="max-height: 100px; max-width: 150px;">
                            </span>
                        @else
                            <h3 style="color: white; margin-top: 20px">{{ $generalInfo->company_name }}</h3>
                        @endif
                    </a>
                </div>

                <!--- Sidemenu -->
                <div id="sidebar-menu">

                    @if (Auth::user()->user_type == 1)
                        @include('backend.sidebar')
                    @else
                        @include('backend.sidebarWithAssignedMenu')
                    @endif

                </div>
                <!-- Sidebar -->
            </div>
        </div>
        <!-- Left Sidebar End -->

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            <header id="page-topbar">
                <div class="navbar-header">
                    <div class="d-flex align-items-center">
                        <button type="button" class="btn btn-sm mr-2 d-lg-none header-item" id="vertical-menu-btn">
                            <i class="fa fa-fw fa-bars"></i>
                        </button>

                        <button type="button" class="btn btn-sm mr-2 d-none d-lg-block header-item" onclick="$('body').toggleClass('lg_hide_menu')" id="lg_menu_toggler">
                            <i class="fa fa-fw fa-bars"></i>
                        </button>

                        <div class="header-breadcumb">
                            <h6 class="header-pretitle d-none d-md-block">Pages <i
                                    class="dripicons-arrow-thin-right"></i> @yield('page_title')</h6>
                            <h2 class="header-title">@yield('page_heading')</h2>
                        </div>
                        <div class="dropdown d-inline-block ml-2">
                            <a href="{{ url('/pos/desktop') }}" 
                            data-active-paths="{{ url('/pos/desktop') }}, {{ url('/pos/desktop/create') }}" 
                            data-active-paths="{{ url('/pos/desktop') }}, {{ url('/pos/desktop/create') }}"  
                            class="btn text-white rounded" style = "background-color: teal;">
                            
                            <i class="fas fa-store"></i>
                                POS
                            </a>
                        </div>
                        <div class="dropdown d-inline-block ml-2">
                            <a href="{{url('/add/new/expense')}}" class="btn text-white" style = "background-color: #931920;">
                                <i class="fas fa-wallet"></i>
                                Daily Expense
                            </a>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="dropdown d-inline-block ml-2">
                            <a href="{{ env('APP_FRONTEND_URL') }}" target="_blank" class="btn text-white visit_website rounded">
                                <i class="fas fa-globe"></i>
                                Visit Website
                            </a>
                        </div>

                        <div class="dropdown d-inline-block ml-2">
                            <button type="button" class="btn header-item" id="page-header-user-dropdown"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img class="rounded-circle header-profile-user"
                                    src="{{ versioned_url('assets/images/users/avatar-1.jpg') }}" alt="Header Avatar">
                                <span class="d-none d-sm-inline-block ml-1">@auth {{ Auth::user()->name }}
                                    @endauth
                                </span>
                                <i class="mdi mdi-chevron-down d-none d-sm-inline-block"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                {{-- <a class="dropdown-item d-flex align-items-center justify-content-between"
                                    href="javascript:void(0)">
                                    Profile
                                </a> --}}
                                <a class="dropdown-item d-flex align-items-center justify-content-between"
                                    href="{{ url('/change/password/page') }}">
                                    <span class="d-none d-sm-inline-block"><i class="fas fa-key"></i>
                                        Change Password
                                    </span>
                                </a>
                                <a href="{{ route('logout') }}"
                                    class="dropdown-item d-flex align-items-center justify-content-between logout"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <span class="d-none d-sm-inline-block">
                                        <i class="fas fa-sign-out-alt"></i>
                                        Logout
                                    </span>
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </header>

            <div class="page-content">
                <div class="container-fluid">

                    @yield('content')

                </div> <!-- container-fluid -->
            </div>
            <!-- End Page-content -->
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->

    <!-- Overlay-->
    <div class="menu-overlay"></div>

    <!-- jQuery  -->
    <script src="{{ versioned_url('assets/js/jquery.min.js') }}"></script>
    <script src="{{ versioned_url('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ versioned_url('assets/js/metismenu.min.js') }}"></script>
    <script src="{{ versioned_url('assets/js/waves.js') }}"></script>
    <script src="{{ versioned_url('assets/js/simplebar.min.js') }}"></script>
    <script src="{{ versioned_url('assets/plugins/jquery-sparkline/jquery.sparkline.min.js') }}"></script>
    <script src="{{ versioned_url('assets/plugins/morris-js/morris.min.js') }}"></script>
    <script src="{{ versioned_url('assets/plugins/raphael/raphael.min.js') }}"></script>
    <script src="{{ versioned_url('assets/pages/dashboard-demo.js') }}"></script>
    <script src="{{ versioned_url('assets/js/theme.js') }}"></script>
    <script src="{{ versioned_url('assets/js/ajax.js') }}"></script>
    <script src="{{ versioned_url('assets/js/ajax_two.js') }}"></script>
    <script src="{{ versioned_url('assets/js/search_product_ajax.js') }}"></script>

    <script>
        const handleScroll = () => {
            var Sidebar = document.querySelector('.simplebar-content-wrapper')
            var scrollPosition = Sidebar.scrollTop;
            localStorage.setItem('scroll_nav', scrollPosition);
        }
        document.addEventListener('DOMContentLoaded', function() {
            var Sidebar = document.querySelector('.simplebar-content-wrapper');
            const Location = window.location.pathname;
            Sidebar.onscroll = handleScroll;

            let scroll_nav = localStorage.getItem('scroll_nav');
            if (scroll_nav && Location != '/dashboard') {
                Sidebar.scrollTop = scroll_nav;
            } else {
                Sidebar.scrollTop = 0;
                localStorage.setItem('scroll_nav', 0);
            }
        });
    </script>

    @yield('footer_js')
    @stack('js')
    @stack('footer_js')
    
    <script src="{{ versioned_url('assets/js/toastr.min.js') }}"></script>
    {!! Toastr::message() !!}

    <!-- PWA Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('{{ url('/sw.js') }}')
                    .then(function(registration) {
                        console.log('ServiceWorker registration successful with scope: ', registration.scope);
                    }, function(err) {
                        console.log('ServiceWorker registration failed: ', err);
                    });
            });
        }
    </script>

</body>

</html>
