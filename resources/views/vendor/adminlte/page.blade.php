@extends('adminlte::master_content')
@php
    use App\Http\Controllers\ChangeHeadquarterController as Headquarters;

    $headquarters = Headquarters::getHeadquarter();
    $headquarter = Headquarters::currentLocal();
@endphp
@section('body')
    <div class="col-md-3 left_col">
        <div class="left_col scroll-view">
            <div class="navbar nav_title" style="border: 0;">
                <a href="{{ route('home') }}" class="site_title"> <img src="{{asset('images/' . Auth::user()->headquarter->client->logo)}}" style="display: block; width: auto; height: 100%; margin: 0 auto"></a>
            </div>

            <div class="clearfix"></div>
            <div class="profile-company" style="text-align: center;color: #fff;padding: 5px 8px; margin: 1em 10px 0 10px;">
                <div>
                    <p>{{ Auth::user()->headquarter->client->trade_name }}</p>
                </div>
                <div>
                    {{-- <p>R.U.C: {{ Auth::user()->headquarter->client->document }}</p> --}}
                </div>
            </div>
            <div class="profile clearfix">
                {{-- <div class="profile_pic">
                    <img src="{{asset('images/profile/' . Auth::user()->logo)}}" alt="..." class="img-circle profile_img">
                </div> --}}
                <div class="profile_info" style="width: 100%">
                    <div class="btn-group show" style="width: 100%">
                        <button type="button" class="btn btn-secondary btn-block dropdown-toggle" style="width: 100%" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                            {{ Auth::user()->name }}
                        </button>
                        <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 38px, 0px);">
                        <a class="dropdown-item" href="#">Mi Perfil</a>
                        <div class="dropdown-divider"></div>
                            <a href="{{ route('logout') }}" class="dropdown-item" onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();"><span class="glyphicon glyphicon-off" aria-hidden="true"></span> Cerrar Sesión</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <br />
            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                <div class="menu_section active">
                    <ul class="nav side-menu" style="">
                        @each('adminlte::partials.menu-item', $adminlte->menu(), 'item')
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="top_nav">
        <div class="nav_menu">
            <div class="nav toggle">
                <a id="menu_toggle" style="font-size: 1.8em !important;"><i class="fa fa-bars" style="font-size: 1em !important;"></i></a>
            </div>
            <nav class="nav navbar-nav">
                <div class="row" style="max-width: 1600px !important;">
                    <div class="col-6" style="padding: 10px 0 !important; height: 75px !important;">
                        @if (Auth::user()->headquarter->client->production == 0)
                            <span class="modelabelProduction danger">MODO DEMO</span>
                        @elseif(Auth::user()->headquarter->client->production == 1)
                            <span class="modelabelProduction">MODO PRODUCCIÓN</span>
                        @endif
                        @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin') || auth()->user()->hasPermissionTo('change.headquarter'))
                            <button class="btn btn-dark" id="changeLocal">{{ $headquarter->description }}</button>
                        @else
                            {{ Auth::user()->headquarter->description }}
                        @endif
                    </div>
                    <div class="col-6 pull-right" style="padding: 0 !important; height: 75px !important;">
                        <ul class=" navbar-right" style="line-height: 1.5em">

                            <li>
                                <div class="btn-group">
                                    <div class="dropdown-menu" x-placement="bottom-start" style="width: 290px;position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(-138px, 40px, 0px);">
                                        <a class="dropdown-item" href="#">Manual de uso de GYO MANAGER</a>
                                        <a class="dropdown-item" href="#">Últimos cambios</a>
                                        <a class="dropdown-item" href="mailto:soporte@gyosoluciones.com">soporte@gyosoluciones.com</a>
                                        <a class="dropdown-item" download="AnyDesk.exe" href="{{ asset('templates/AnyDesk.exe') }}">Descargar AnyDesk (Escritorio Remoto)</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="#">GYO SOLUCIONES INFORMATICAS.S.A.C.<br>RUC: 20603289324</a>
                                    </div>
                                </div>
                            </li>
                            @if (auth()->user()->hasRole('superadmin'))
                                <li><a href="{{ route('mange.index') }}" class="btn btn-gray-custom">GESTIONAR</a></li>
                            @endif
                            @if (session()->has('saou'))
                                <li><a href="{{ route('mange.revertir') }}" class="btn btn-warning">CERRAR SESIÓN ACTUAL</a></li>
                            @endif
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </div>
    <div class="right_col" role="main" style="min-height: 3750px;">
        <div class="">
            @yield('content')
        </div>
    </div>
    <div class="modal fade" id="mdl-help" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">CAMBIAR DE LOCAL</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <form id="frm_changeLocal">
                        <div class="form-group">
                            <label>Local:</label>
                            <select name="headquarter" id="headquarter" class="form-control">
                                <option value="">Selecciona un Local</option>
                                @foreach ($headquarters as $h)
                                    <option value="{{ $h->id }}" {{ session()->get('headlocal') == $h->id ? 'selected' : '' }}>{{ $h->description }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary-custom" id="btnChangeLocal">Cambiar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('adminlte_js')
    @stack('js')
    @yield('js')
@stop
