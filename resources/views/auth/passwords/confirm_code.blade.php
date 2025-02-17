<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <meta name="robots" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta property="og:title" content="">
    <meta property="og:description" content="">
    <meta property="og:image" content="">
    <meta name="format-detection" content="">

    <!-- PAGE TITLE HERE -->
    <title>Bighit</title>

    <!-- FAVICONS ICON -->
    <link rel="shortcut icon" type="image/png" href="{{ asset('xhtml/images/favicon.png') }}">
    <link href="{{ asset('xhtml/vendor/jquery-nice-select/css/nice-select.css') }}" rel="stylesheet">
    <link href="{{ asset('xhtml/css/style.css') }}" rel="stylesheet">

</head>

<body class="">
    <div class="authincation">
        <div class="authincation-content">
            <div class="auth-form">
                <div class="text-center mb-3">
                    <!-- <a href="/"><img src="{{ asset('xhtml/images/logo/logo-full.png') }}" alt=""></a> -->
                </div>
                <h4 class="text-center mb-4">Confirm Password Reset Code</h4>
                <p>
                <p class="text-center mb-4 " style="color: #000;">
                    A code has been send to
                    <span style="color: blueviolet;">{{ $email }} <span>
                </p>
                <form class="login-form" method="POST" action="{{ route('submitPasswordResetCode') }}">
                    @csrf

                    <input class="form-control" hidden type="text" value="{{ $email }}" name="user_email">

                    <div class="mb-4">
                        <label>Code</label>
                        <input type="password" class="form-control @error('code') is-invalid @enderror" value="" name="code" placeholder="Enter Code" autofocus>
                        <span class="text-danger">@error ('code') {{ $message }} @enderror</span>
                    </div>

                    <div class="form-row d-flex justify-content-between align-items-center mt-4 mb-2">
                        <div class="mb-4">
                        </div>
                        <div class="mb-4">
                            <a href="" class="btn-link text-primary">Resend Code</a>
                        </div>
                    </div>




                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-block">SUBMIT</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .authincation {
            width: 100%;
            margin: 0 auto;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .auth-form {
            width: 700px;
        }

        .auth-form form {
            box-shadow: rgba(0, 0, 0, 0.16) 0px 1px 4px;
            width: 100%;
            padding: 40px;
        }

        @media only screen and (max-width: 767px) {
            .auth-form {
                width: 100% !important;
                padding: 0px;
            }

            .auth-form form {
                padding: 20px;
            }
        }
    </style>

    <script src="{{ asset('xhtml/vendor/global/global.min.js') }}"></script>
    <script src="{{ asset('xhtml/js/deznav-init.js') }}"></script>

    <!-- sweet alert  -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    @if (session('message'))
    <script>
        swal("Successful!", "{{ session('message') }}!", "success");
    </script>
    @endif
    @if (session('error'))
    <script>
        swal("Error!", "{{ session('error') }}!", "warning");
    </script>
    @endif
    @if (Session::has('success'))
    <script>
        swal("Successful!", "{{ Session::get('success') }}!", "success");
    </script>
    @endif

    @if (Session::has('error'))
    <script>
        swal("Warning!", "{{ Session::get('error') }}!", "warning");
    </script>
    @endif
</body>

</html>
