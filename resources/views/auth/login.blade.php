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
    <title>Acceder - BautiFak</title>
</head>
<body class="az-body">
    <div class="az-signin-wrapper">
        <div class="az-card-signin">
            <img src="{{ asset('images/logo.png') }}" class="az-logo" height="68px">
            <div class="az-signin-header">
                <h2>¡Bienvenido de nuevo!</h2>
                <h4>Por favor Inicia Sesión para continuar</h4>

                <form method="POST" action="{{ route('login') }}">
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
                    <div class="form-group">
                        <label>Contraseña</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6 offset-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                <label class="form-check-label" for="remember">
                                    {{ __('Remember Me') }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-primary-custom btn-block">Iniciar Sesión</button>
                </form>
            <div>
            <div class="az-signin-footer mg-t-20">
                @if (Route::has('password.request'))
                    <p><a href="{{ route('password.request') }}">
                        {{ __('Forgot Your Password?') }}
                    </a></p>
                @endif
            </div>
        </div>
    </div>
</body>
</html>