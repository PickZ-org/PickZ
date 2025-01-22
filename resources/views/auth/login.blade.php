@extends('layouts.login')

@section('title', 'Log-in')

@section('content')
    <div class="login-box">
        <div class="login-logo">
            <img src="{{ url('/') }}/img/logo_small.png" alt="PickZ logo" class="brand-image">
        </div>
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Sign in to start your session</p>
                <form action="{{ route('login') }}" method="post" id="form-login">
                    @csrf
                    <div class="input-group regular-login">
                        <input type="text" class="form-control allow-enter @if($errors->has('username')) is-invalid @endif"
                               placeholder="Username" name="username">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>
                    @if ($errors->has('username'))
                        <small class="regular-login">{{ $errors->first('username') }}</small>
                    @endif
                    <div class="input-group mt-3 regular-login">
                        <input type="password" class="form-control allow-enter @if($errors->has('password')) is-invalid @endif"
                               placeholder="Password" name="password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    @if ($errors->has('password'))
                        <small class="regular-login">{{ $errors->first('password') }}</small>
                    @endif
                    <div class="input-group mb-3 qrcode-login" style="display:none;">
                        <input class="form-control scannerinput" type="password"
                               placeholder="{{ __('Scan your QR code') }}"
                               name="qrcode" data-validation-value="" data-submitter="true">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-qrcode"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row regular-login mt-3">
                        <div class="col-8">
                            <div class="icheck-primary">
                                <input type="checkbox" id="remember">
                                <label for="remember">
                                    Remember Me
                                </label>
                            </div>
                        </div>

                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                        </div>
                    </div>
                </form>
                <div class="social-auth-links text-center mb-3">
                    <p>- OR -</p>
                    <a href="#" class="btn btn-block btn-primary regular-login" id="qrlogin_button">
                        <i class="fas fa-qrcode mr-2"></i> Sign in using QR code
                    </a>
                    <a href="#" class="btn btn-block btn-primary qrcode-login" id="regularlogin_button"
                       style="display: none;">
                        <i class="fas fa-sign-in-alt mr-2"></i> Sign in using regular login
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $('#qrlogin_button').click(function () {
            $('.regular-login').hide();
            $('.qrcode-login').show();
            $('.scannerinput').focus();
            $('#form-login').attr('action', '{{route('qrlogin')}}')
        });
        $('#regularlogin_button').click(function () {
            $('.qrcode-login').hide();
            $('.regular-login').show();
            $('#form-login').attr('action', '{{route('login')}}')
        });
    </script>
@endpush
