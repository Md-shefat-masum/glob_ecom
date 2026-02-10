@extends('layouts.app')

@section('content')
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
        }

        body {
            background: #F8F9FB;
            /*display: flex;*/
            /*align-items: center;*/
            /*justify-content: center;*/
        }

        .login-container {
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .background-iamge {
            width: 100%;
            height: 100%;
            position: relative;
            display: flex;
            justify-content: flex-start;
            align-items: end;
            padding-top: 80px;
        }

        .background-iamge img {
            margin-top: 10rem;
            width: 70%;
            max-width: 1028px;
            height: auto;
            object-fit: contain;
        }

        .login_mobile {
            display: none;
        }

        .login-card {
            position: absolute;
            width: 90%;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: end;
        }

        .login-right {
            padding: 45px 50px;
        }

        .login-logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .login-logo img {
            height: 70px;
        }

        .login-title {
            font-weight: 700;
            margin-bottom: 5px;
            text-align: center;
        }

        .login-subtitle {
            font-size: 14px;
            color: #6c757d;
            text-align: center;
            margin-bottom: 30px;
        }

        .form-control {
            height: 46px;
            border-radius: 8px;
        }

        .btn-login {
            height: 46px;
            border-radius: 25px;
            font-weight: 600;
            background: linear-gradient(90deg, #7ed957, #3bb54a, #7ed957);
            background-size: 200% 100%;
            border: none;
            color: #fff;
            transition: background-position 0.4s ease;
        }

        .btn-login:hover {
            background-position: 100% 0;
        }

        @media (max-width: 1600px) and (min-width: 900px) {
            .background-iamge img {
                max-width: 900px;
            }
        }


        @media (max-width: 991px) {
            .login-left {
                display: none;
            }

            .login-right {
                padding: 35px 25px;
                margin-bottom: 3rem;
            }

            .background-iamge {
                width: 100%;
                height: 100%;
                position: relative;
                display: flex;
                justify-content: flex-start;
                align-items: flex-start;
                padding-top: 0;
            }

            .background-iamge img {
                margin-top: -39px;
                width: 100%;
                height: auto;
                object-fit: contain;
            }

            .login_desktop {
                display: none !important;
            }

            .login_mobile {
                display: block;
            }

            .login-card {
                width: 100%;
                align-items: flex-end;
            }


        }
    </style>

    @if (env('APP_NAME') == 'bme')
        <style>
            .login-card {
                position: absolute;
                width: 90%;
                height: 559px;
                display: flex;
                align-items: center;
                justify-content: end;
                backdrop-filter: blur(8px);
                background: rgb(255 255 255 / 50%);
            }
        </style>
    @endif

    <div class="login-container">
        @if (env('APP_NAME') == 'bizo')
            <div class= "background-iamge">
                <img src="{{ asset('assets/images/loginbg.png') }}" alt = "Getprotouch Mark" class="login_desktop">
                <img src="{{ asset('assets/images/loginbg_mobile.png') }}" alt="Getprotouch Mark" class="login_mobile">
            </div>
        @endif
        @if (env('APP_NAME') == 'bme')
            <div style="width: 100%; height: 100%;">
                <img src="https://onemoneyway.com/wp-content/uploads/2024/10/pos-machine2.png"
                    style="width: 100%; height: 100%; object-fit: cover;" alt = "" class="login_desktop">
                <img src="https://onemoneyway.com/wp-content/uploads/2024/10/pos-machine2.png"
                    style="width: 100%; height: 100%; object-fit: cover;" alt="" class="login_mobile">
            </div>
        @endif
        <div class="login-card row m-0">
            <!-- RIGHT FORM -->
            <div class="col-lg-6 login-right">

                <div class="login-logo">
                    @if (env('APP_NAME') == 'bizo')
                        <img src="{{ asset('assets/images/myBizoLogo.png') }}" alt="myBizo Logo">
                    @endif
                    @if (env('APP_NAME') == 'bme')
                        <img src="https://bme.com.bd/uploads/logo/y73J0zC9luaOjL7AB2L2B3YDzZsZM7Ddg8Mh1Pt4.gif"
                            alt="Logo">
                    @endif
                </div>

                <p class="login-subtitle">Continue to your Dashboard</p>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-group mb-3">
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            placeholder="Email or Username" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                            placeholder="Password" required>
                        @error('password')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="remember" name="remember">
                            <label class="custom-control-label" for="remember">Remember Me</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-login btn-block text-white">
                        Login
                    </button>
                </form>

                <p class="text-center mt-4 text-muted" style="font-size: 13px;">
                    All rights reserved © {{ date('Y') }} {{ env('APP_NAME') }}
                </p>

            </div>
        </div>
    </div>
@endsection
