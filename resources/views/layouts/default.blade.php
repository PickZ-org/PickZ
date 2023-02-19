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
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" rel="stylesheet">
    <!--end::Web font -->
    <!--begin::Plug-in Styles -->
    <link href="{{ url('/') }}/plugins/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{ url('/') }}/plugins/jquery-ui/jquery-ui.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{ url('/') }}/plugins/toastr/toastr.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{ url('/') }}/plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{ url('/') }}/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{ url('/') }}/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{ url('/') }}/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{ url('/') }}/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{ url('/') }}/plugins/datatables-responsive/css/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{ url('/') }}/plugins/datatables-select/css/select.bootstrap4.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{ url('/') }}/plugins/datatables-buttons/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css"/>

    <link href="https://unpkg.com/ionicons@4.5.10-0/dist/css/ionicons.min.css" rel="stylesheet">
    <!--end::Plug-in Styles -->
    <!--begin::Global Theme Styles -->
    <link href="{{ url('/') }}/css/adminlte.min.css" rel="stylesheet" type="text/css"/>
    <!--end::Global Theme Styles -->
    <link href="{{ url('/') }}/css/style.css" rel="stylesheet" type="text/css"/>
    <link rel="shortcut icon" href="{{ url('/') }}/img/favicon.png"/>
</head>
<body class='hold-transition sidebar-mini'>

<!-- begin:: Page -->
<div class="wrapper">
    @include('layouts.partials.header')
    @include('layouts.partials.sidebar-left')
    <div class="content-wrapper">
        @include('layouts.partials.subheader')
        <div class="content">
            @yield('content')
        </div>
        @stack('modals')
    </div>
    @include('layouts.partials.footer')
</div>
<!-- end:: Page -->

<!--begin::Framework Scripts -->
<script src="{{ url('/') }}/plugins/jquery/jquery.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/plugins/bootstrap/js/bootstrap.bundle.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/plugins/toastr/toastr.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/plugins/sweetalert2/sweetalert2.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/plugins/chart.js/Chart.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/plugins/select2/js/select2.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/plugins/datatables-responsive/js/dataTables.responsive.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/plugins/datatables-responsive/js/responsive.bootstrap4.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/plugins/datatables-select/js/dataTables.select.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/plugins/datatables-select/js/select.bootstrap4.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/plugins/datatables-buttons/js/dataTables.buttons.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/plugins/datatables-buttons/js/buttons.html5.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/plugins/datatables-buttons/js/buttons.bootstrap4.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/plugins/jquery-repeater/jquery.repeater.min.js" type="text/javascript"></script>

<script src="{{ url('/') }}/js/adminlte.min.js" type="text/javascript"></script>
<!--end::Page Scripts -->

<!-- custom scripts -->
<script src="{{ url('/') }}/js/script.js" type="text/javascript"></script>
@stack('scripts')
<script>
    @foreach (['error', 'warning', 'success', 'info'] as $key)
    @if(Config::has($key))
    toastr.{{ $key }}('{{Config::get($key)}}', '{{ ucfirst($key) }}');
    @endif
    @if(Session::has($key))
    toastr.{{ $key }}('{{Session::get($key)}}', '{{ ucfirst($key) }}');
    @endif
    @endforeach
</script>
</body>
<!-- end::Body -->
</html>
