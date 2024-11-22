@extends('adminlte::master')

@section('body_class', 'login-page')

@section('body')
    <div class="container" id="app">
        <div class="row py-100">
            <div class="col-md-5 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center">
                            <div class="logo">
                                <img src="{{asset('vendor/adminlte3/gyo/img/logo.png')}}" style="width: 150px;">
                            </div>
                            {{-- <div class="form-title">Acceder</div> --}}
                        </div>

                        <form action="{{ url(config('adminlte.login_url', 'login')) }}" method="post">
                            {!! csrf_field() !!}
                            <div class="form-group">
                                <input name="email" type="text" class="form-control" id="email" required>
                                <label for="email" style="color: #000000;">Email - Usuario</label>
                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group">
                                <input type="password" class="form-control" name="password" id="password" required>
                                <label for="password" style="color: #000000;">Contraseña</label>
                                <span class="toggle-password">
                                    <i class="fas fa-eye" style="font-size: 1.6em;"></i>
                                </span>
                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="row">
                                <!--<div class="col d-flex flex-column justify-content-center">
                                    <a href="{{route('password.update')}}" title="Recuperar contraseña" style="display: inline-block; font-size: 12px;">¿Olvidaste la contraseña?</a>
                                </div>-->
                                <div class="form-group col text-right">
                                    <button type="submit" name="login" class="btn btn-primary-custom">Ingresar</button>
                                </div>
                            </div>
                        </form>
                        <div class="text-center">
                            <!--<a href="{{route('register')}}" style="text-decoration: none; display: inline-block; margin-bottom: 20px; font-size: 16px;">Regístrate gratis</a>-->
                        </div>
                        <!--<div class="text-center">
                            <ul class="form-links text-center">
                                <li>
                                    <a href="#">Política de privacidad</a>
                                    <a href="#">Garantía de seguridad</a><br>
                                    <a href="#">Términos de servicio y condiciones de uso.</a>
                                </li>
                            </ul>
                        </div>-->
                    </div>
                </div>
            </div>
        </div>
    </div>
