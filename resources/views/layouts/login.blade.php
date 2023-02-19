<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title>PickZ | @yield('title')</title>
    <meta name="description" content="PickZ">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- authentication token -->
    <script>
        var APP_URL = {!! json_encode(url('/')) !!}
            window.Laravel = {
            csrfToken: '{{csrf_token()}}'
        }
    </script>
    <!--begin::Web font -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback"
          rel="stylesheet">
    <!--end::Web font -->
    <!--begin::Plug-in Styles -->
    <link href="{{ url('/') }}/plugins/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{ url('/') }}/plugins/toastr/toastr.min.css" rel="stylesheet" type="text/css"/>
    <!--begin::Global Theme Styles -->
    <link href="{{ url('/') }}/css/adminlte.min.css" rel="stylesheet" type="text/css"/>
    <!--end::Global Theme Styles -->
    <link href="{{ url('/') }}/css/style.css" rel="stylesheet" type="text/css"/>
    <link rel="shortcut icon" href="{{ url('/') }}/img/favicon.png"/>
</head>
<body class='login-page'>
@yield('content')
@stack('modals')
<!-- end:: Page -->

<!--begin::Framework Scripts -->
<script src="{{ url('/') }}/plugins/jquery/jquery.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/plugins/bootstrap/js/bootstrap.bundle.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/js/adminlte.min.js" type="text/javascript"></script>
<!--end::Page Scripts -->

<!-- custom scripts -->
<script src="{{ url('/') }}/js/scanner.js" type="text/javascript"></script>
@stack('scripts')
</body>
<!-- end::Body -->
</html>
