@extends('layouts.azia')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header color-gray text-center">
                    <div class="row">
                        <div class="col-12">
                            <h2 style="color: white;">NUEVO USUARIO</h2>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{route('create_user')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>Nombres y Apellidos</label>
                                            <input type="text" class="form-control" name="name" id="name">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="text" class="form-control" name="email" id="email">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>Contraseña</label>
                                            <input type="password" class="form-control" name="password" id="password">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>SUBIR FOTO</label>
                                            <input type="file" class="form-control" name="logo" id="logo">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>Teléfono</label>
                                            <input type="text" class="form-control" name="phone" id="phone">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>Cargo</label>
                                            <select name="rol" id="rol" class="form-control">
                                                <option value="">Seleccionar Cargo</option>
                                                @foreach($roles as $rol)
                                                    <option value="{{$rol->id}}">{{$rol->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12" style="display: none;">
                                        <label>ESTADO</label>
                                        <div class="form-group">
                                            <input type="checkbox" checked data-toggle="toggle" value="1" data-on="Si" data-off="No" name="status" id="status" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <fieldset>
                            <div class="row">
                                <div class="col-12">
                                    <h3 class="title text-center">LOCAL ASIGNADO</h3>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <select name="headquarter" id="headquarter" class="form-control">
                                            @foreach($headquarters as $headquarter)
                                                <option value="{{$headquarter->id}}">{{$headquarter->description}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <button class="btn btn-primary-custom btn-block">Crear Usuario</button>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
@section('script_admin')
    @if(Session::has('success'))
        <script>
            toastr.success('Se registró correctamente el usuario.');
            window.setInterval(
                window.location = '{{route('users')}}',
                3000
            );
        </script>
    @endif

    @if(Session::has('error'))
        <script>
            toastr.error('Oucrrió un error cuando intentaba grabar el usuario');
        </script>
    @endif
@stop
