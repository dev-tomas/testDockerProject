<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <link href="{{ asset('css/azia.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/custom.min.css') }}">
    <title>Cambiar Contraseña - BAUTIFAK</title>
</head>
<body class="az-body">
    <div class="az-signin-wrapper">
        <div class="az-card-signin">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
            <img src="{{ asset('images/logo.png') }}" class="az-logo" height="68px">
                <div class="az-signin-header">
                <h2>¡Hola!</h2>
                <h4>Por favor ingresa tu email para recuperar tu Contraseña</h4>

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="form-group">
                        <label>Email</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <button class="btn btn-primary-custom btn-block">Cambiar Contraseña</button>
                </form>
            <div>
            <div class="az-signin-footer mg-t-20">
                <p><a class="btn btn-secondary-custom btn-block" href="{{ route('login') }}">
                    {{ __('Login') }}
                </a></p>
            </div>
        </div>
    </div>
</body>
</html>