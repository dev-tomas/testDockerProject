@extends('layouts.azia')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header text-center">
                    <div class="row">
                        <div class="col-12">
                            <h2>Editar Role</h2>
                        </div>
                        <div class="col-12">
                            <p>
                                {{-- La serie debe empezar con la letra F para FACTURAS y NOTAS asociadas,
                                B para BOLETAS DE VENTAS y sus NOTAS asociadas, R para comprobantes
                                de RETENCIÓN, P para comprobantes de PERCEPCIÓN. --}}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    {!! Form::model($role, ['route' => ['roles.update', $role->id], 'method' => 'PUT']) !!}
                        @include('roles.partials.form')
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@stop