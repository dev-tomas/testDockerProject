@extends('adminlte::master')

@section('body')
    <div class="container-fluid" id="app">
        <div class="row py-100 h-v100">
            <div class="col-md-5 mx-auto">
                <div class="card h-100">
                    <div class="card-body">

                        <form method="POST" action="{{ route('password.request') }}" aria-label="{{ __('Reset Password') }}">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">
                            <div class="text-center">
                                <div class="logo">
                                    <img src="https://gyosoluciones.com/gyo/img/logo.png" style="width: 150px;">
                                </div>
                                <div class="form-title">Cambiar contraseña</div>
                            </div>

                            <div class="form-group">
                                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ $email ?? old('email') }}" required autofocus>
                                <label for="email">Email - Usuario</label>
                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group">
                                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>
                                <label for="password">Contraseña</label>

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                                <label for="password-confirm">Confirmar Contraseña</label>
                            </div>

                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-primary-custom">Cambiar Contraseña</button>
                            </div>
                        </form>
                        <div class="text-center">
                            <img src="https://gyosoluciones.com/gyo/img/ssl.png" style="width: 137px;">
                            <p><small>Protegido con un certificado digital ssl <b>https://</b></small></p>
                            <ul class="form-links text-center">
                                <li>
                                    <a href="#">Política de privacidad</a>
                                    <a href="#">Garantía de seguridad</a><br>
                                    <a href="#">Términos de servicio y condiciones de uso.</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
