<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}" type="image/x-icon">
    <link href="{{ asset('css/azia.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/custom.min.css') }}">
    <title>Restablecer Contraseña - SOFACT</title>
</head>
<body class="az-body">
    <div class="az-signin-wrapper">
        <div class="az-card-signin">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
            <img src="{{ asset('images/sofactlogo.svg') }}" class="az-logo" height="68px">
            <div class="az-signin-header">
                <h2>¡Hola!</h2>
                <h4>Por favor ingresa tu nueva contraseña</h4>

                <form method="POST" action="{{ route('password.request') }}" aria-label="{{ __('Reset Password') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <div class="form-group">
                        <label>Email</label>
                        <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ $email ?? old('email') }}" required autofocus>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Contraseña</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Confirmar Contraseña</label>
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                    </div>
                    <button class="btn btn-primary-custom btn-block">Restablecer Contraseña</button>
                </form>
            <div>
            <div class="az-signin-footer mg-t-20">
                <p><a href="{{ route('login') }}">
                    {{ __('Login') }}
                </a></p>
            </div>
        </div>
    </div>
</body>
</html>