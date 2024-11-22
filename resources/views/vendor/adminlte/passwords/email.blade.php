@extends('adminlte::master')

@section('body')
    <div class="container-fluid" id="app">
        <div class="row py-100 h-v100">
            <div class="col-md-5 mx-auto">
                <div class="card h-100">
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf
                            <div class="text-center">
                                <div class="logo">
                                    <img src="{{asset('vendor/adminlte3/gyo/img/logo.png')}}" style="width: 150px;">
                                </div>
                                <div class="form-title">Cambiar contraseña</div>
                            </div>

                            <div class="form-group">
                                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" id="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                <label for="email">Email - Usuario</label>
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            </div>

                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-primary-custom">Siguiente</button>
                            </div>
                        </form>
                        <div class="text-center">
                            <img src="{{asset('vendor/adminlte3/gyo/img/ssl.png')}}" style="width: 137px;">
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
