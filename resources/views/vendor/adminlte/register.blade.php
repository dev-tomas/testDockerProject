@extends('adminlte::master')

@section('body')
    <div class="container">
        <div class="row py-100">
            <div class="col-md-5 col-sm-6 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center">
                            <div class="logo">
                                <img src="{{asset('vendor/adminlte3/gyo/img/logo.png')}}" style="width: 150px;">
                            </div>
                            <div class="form-title">Registro</div>
                        </div>
                        <form action="{{ url(config('adminlte.register_url', 'register')) }}" method="post">
                            {!! csrf_field() !!}
                            <div class="form-group" {{ $errors->has('name') ? 'has-error' : '' }}>
                                <input type="text" class="form-control" name="name" id="name" required value="{{ old('name') }}">
                                <label for="name">Nombre *</label>
                                <small class="form-text text-muted">Nombre completo de la persona de contacto</small>
                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group" {{ $errors->has('phone') ? 'has-error' : '' }}>
                                <input type="text" class="form-control" value="{{ old('phone') }}" name="phone" id="phone" minlength="9" maxlength="9" pattern="^[9]+[0-9]*$" required>
                                <label for="phone">Teléfono *</label>
                                <small class="form-text text-muted">Teléfono móvil (9 caractéres exactos)</small>
                                @if ($errors->has('phone'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group" {{ $errors->has('email') ? 'has-error' : '' }}>
                                <input type="email" class="form-control" value="{{ old('email') }}" name="email" id="email" required>
                                <label for="email">Email - Usuario *</label>
                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group" {{ $errors->has('password') ? 'has-error' : '' }}>
                                <input type="password" class="form-control" name="password" id="password" required>
                                <label for="password">Contraseña *</label>
                                <span class="toggle-password">
                                    <i class="fas fa-eye" style="font-size: 1.6em;"></i>
                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                    @endif
                                </span>
                            </div>

                            <div class="form-group" style="position: relative;" {{ $errors->has('document') ? 'has-error' : '' }}>
                                <input type="text" class="form-control" name="document" value="{{ old('document') }}" id="document" required>
                                <label for="ruc">RUC</label>
                                <span class="valid-ruc"> RUC CORRECTO</span>
                                <small class="form-text text-muted">RUC SUNAT de empresa o persona para esta empresa</small>
                                @if ($errors->has('document'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('document') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-primary-custom" name="register" id="btn-registro">Siguiente</button>
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