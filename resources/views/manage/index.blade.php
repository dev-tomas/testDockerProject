@extends('layouts.azia')
@section('css')
    <style>
        .valid-ruc{ height: 33.9px; position: absolute; top: 25%;right: 10px; display: none; user-select: none;} .valid-ruc.active{display: block;} .cont-ruc {position: relative}
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-6 col-md-2">
            <div class="access-direct">
                <a href="{{ route('mange.company') }}"><img src="{{ asset('images/ICON.png') }}">Clientes</a>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="access-direct">
                <a href="{{ route('manage.monitor') }}"><img src="{{ asset('images/ICON.png') }}">Monitor</a>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="access-direct">
                <a href="{{ route('manage.cpe') }}"><img src="{{ asset('images/ICON.png') }}">Comprobantes</a>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="access-direct">
                <a href="{{ route('manage.api') }}"><img src="{{ asset('images/ICON.png') }}">API</a>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="access-direct">
                <a href="/manage/telescope"><img src="{{ asset('images/ICON.png') }}">Telescope</a>
            </div>
        </div>
    </div>
@stop
@section('script_admin')

@stop
