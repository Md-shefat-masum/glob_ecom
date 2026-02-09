@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-5 d-none d-lg-block bg-register rounded-left"></div>
        <div class="col-lg-7">
            <div class="p-5">
                <div class="text-center">
                    <a href="index.html" class="d-block mb-5">
                        {{-- <img src="assets/images/logo-dark.png" alt="app-logo" height="18" /> --}}
                        <h3>GenericCommerce-V1 Admin Panel</h3>
                    </a>
                </div>
                <h1 class="h5 mb-1">Create an Account!</h1>
                <p class="text-muted mb-4">Don't have an account? Create your own account, it takes less than a minute</p>
                <form class="user" method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="form-group">
                        <input type="text" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus class="form-control form-control-user @error('name') is-invalid @enderror" id="exampleFirstName name" placeholder="Full Name">
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email" class="form-control form-control-user @error('email') is-invalid @enderror" id="exampleInputEmail email" placeholder="Email Address">
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-6 mb-3 mb-sm-0">
                            <input type="password" name="password" required autocomplete="new-password" class="form-control form-control-user @error('password') is-invalid @enderror" id="exampleInputPassword password" placeholder="Password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-sm-6">
                            <input type="password" name="password_confirmation" required autocomplete="new-password" class="form-control form-control-user" id="exampleRepeatPassword password-confirm" placeholder="Repeat Password">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success btn-block">{{ __('Register') }}</button>
                </form>

                <div class="row mt-4">
                    <div class="col-12 text-center">
                        <p class="text-muted mb-0">Already have account?  <a href="{{url('/login')}}" class="text-muted font-weight-medium ml-1"><b>Sign In</b></a></p>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
