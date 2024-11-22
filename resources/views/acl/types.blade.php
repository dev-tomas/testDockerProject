@extends('layouts.azia')

@section('title', 'AdminLTE')

@section('content_header')
    <h1>Escoger</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-body">
                    <div class="form-group">
                        <a href="{{route('users')}}"><i class="fa fa-key"></i> Permisos por Usuario</a>
                    </div>
                    <div class="form-group">
                        <a href="{{route('roles')}}"><i class="fa fa-users"></i> Permisos por Rol</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop