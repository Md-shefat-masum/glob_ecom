@extends('backend.master')

@section('page_title')
    Password Change
@endsection
@section('page_heading')
    User Password Change
@endsection

@section('header_css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css" />
    <style>
        form i {
            margin-left: -30px;
            cursor: pointer;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 col-xl-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Password Change Form</h4>

                    <form class="needs-validation" method="POST" action="{{url('change/password')}}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="user_id" value="{{Auth::user()->id}}">

                        <div class="form-group row">
                            <label for="colFormLabel" class="col-sm-2 col-form-label">Name <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" name="name" class="form-control mb-2" id="colFormLabel" value="{{Auth::user()->name}}" required>
                            </div>

                            <label for="colFormLabel" class="col-sm-2 col-form-label">Email <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="email" name="email" class="form-control mb-2" id="colFormLabel" value="{{Auth::user()->email}}" readonly>
                            </div>

                            <label for="colFormLabel" class="col-sm-2 col-form-label">Phone <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" name="phone" class="form-control mb-2" id="colFormLabel" value="{{Auth::user()->phone}}" readonly>
                            </div>

                            <label for="colFormLabel" class="col-sm-2 col-form-label">Current Password <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="password" name="prev_password" id="password" class="form-control mb-2 d-inline-block" id="colFormLabel" required>
                                <i class="bi bi-eye-slash" id="togglePassword"></i>
                            </div>

                            <label for="colFormLabel" class="col-sm-2 col-form-label">New Password <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="password" name="new_password" id="new_password" class="form-control mb-2 d-inline-block" id="colFormLabel" required>
                                <i class="bi bi-eye-slash" id="togglePassword2"></i>
                            </div>
                        </div>


                        <div class="form-group">
                            <button class="btn btn-primary" type="submit">Change Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section("footer_js")
    <script>
        const togglePassword = document.querySelector("#togglePassword");
        const password = document.querySelector("#password");

        togglePassword.addEventListener("click", function () {
            // toggle the type attribute
            const type = password.getAttribute("type") === "password" ? "text" : "password";
            password.setAttribute("type", type);

            // toggle the icon
            this.classList.toggle("bi-eye");
        });

        // prevent form submit
        const form = document.querySelector("form");
        form.addEventListener('submit', function (e) {
            e.preventDefault();
        });


        // new password toggle
        const togglePassword2 = document.querySelector("#togglePassword2");
        const newPassword = document.querySelector("#new_password");

        togglePassword2.addEventListener("click", function () {
            // toggle the type attribute
            const type = newPassword.getAttribute("type") === "password" ? "text" : "password";
            newPassword.setAttribute("type", type);

            // toggle the icon
            this.classList.toggle("bi-eye");
        });

        // prevent form submit
        const form2 = document.querySelector("form");
        form2.addEventListener('submit', function (e) {
            e.preventDefault();
        });
    </script>
@endsection
