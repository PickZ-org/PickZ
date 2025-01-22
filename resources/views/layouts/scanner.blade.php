<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title>PickZ | @yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script type="text/javascript">
        var APP_URL = {!! json_encode(url('/')) !!}
            window.Laravel = {
            csrfToken: '{{csrf_token()}}'
        }
    </script>
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback"
          rel="stylesheet">

    <link href="{{ url('/') }}/plugins/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
            integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
            crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.12/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.12/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"
            integrity="sha256-bqVeqGdJ7h/lYPq6xrPv/YGzMEb6dNxlfiTUHSgRCp8=" crossorigin="anonymous"></script>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.standalone.min.css"
          integrity="sha256-jO7D3fIsAq+jB8Xt3NI5vBf3k4tvtHwzp8ISLQG4UWU=" crossorigin="anonymous"/>
    <link href="{{ url('/') }}/css/toastr.min.css" rel="stylesheet" type="text/css">

    <link href="{{ url('/') }}/css/adminlte.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{ url('/css/scanner.css') }}" rel="stylesheet" type="text/css">

    <link rel="shortcut icon" href="{{ url('/') }}/img/favicon.png"/>
</head>
<body style="">
<div class="container-fluid">
    <div class="row d-flex py-3" style="margin-bottom:15px;">
        <div class="col-4">
            @hasSection( 'back-link' )
                <a class="text-gray" href="@yield('back-link')"><i class="fas fa-angle-double-left fa-2x"></i></a>
            @endif
        </div>
        <div class="col-4 text-center">
                <a href="{{url('/scanner')}}">
                    <img src="{{ url('/') }}/img/logo_small.png" alt="PickZ logo" class="brand-image w-100" style="max-width:160px;">
                </a>
        </div>
        <div class="col-4 text-right">
            <a class="text-gray" href="{{ route('logout') }}"
               onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i
                    class="fas fa-power-off fa-2x"></i></a>
        </div>
    </div>
    <!-- Hidden form for logout -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
    <div class="row d-flex">
        <div class="col-12">
            <h5 class="text-uppercase text-secondary pb-3 text-center">
                @hasSection( 'icon' )
                    <i class="mr-1 @yield('icon')"></i>
                @endif
                @yield('title')
            </h5>
        </div>
    </div>
    <div class="row d-flex">
        @yield('content')
    </div>

    @stack('modals')
</div>
<!-- app scripts -->
<script src="{{ url('/') }}/plugins/bootstrap/js/bootstrap.bundle.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/js/toastr.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/js/BrowserPrint-3.0.216.min.js" type="text/javascript"></script>
<script src="{{ url('/') }}/js/scanner.js" type="text/javascript"></script>
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
    @if ($errors->any())
    @foreach ($errors->all() as $error)
    toastr.error('{{ $error }}', 'Error');
    @endforeach
    @endif
</script>

</body>
</html>
