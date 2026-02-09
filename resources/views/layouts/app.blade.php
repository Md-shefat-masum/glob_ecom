<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="" name="description" />
    <meta content="MyraStudio" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link href="{{ versioned_url('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    @yield('header_css')
    <style>
        .content_wrapper {
            display: grid;
            align-items: center;
            /*height: calc(100vh - 48px);*/
            overflow-x: hidden;
        }
    </style>
</head>

<body style="background-color: #F8F9FB">
    <div class="content_wrapper">
        @yield('content')
    </div>
</body>

</html>
