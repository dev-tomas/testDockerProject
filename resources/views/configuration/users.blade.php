@extends('layouts.azia')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card text-center">
                <div class="card-header color-gray">
                    <div class="row">
                        <div class="col-12">
                            <h3 class="card-title">LISTA DE USUARIOS</h3>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary-custom pull-left mg-r-15" id="btnAddUser">Nuevo Usuario</button>
                            <a class="btn btn-primary-custom pull-left" href="/roles">Funciones de Usuario</a>
                            <a class="btn btn-secondary-custom pull-right" href="{{route("downloadUsers")}}"><i class="fa fa-download"></i> Excel</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table id="tbl_data" class="dt-bootstrap4 table-hover dataTable no-footer">
                                    <thead>
                                        <th>#</th>
                                        <th>Nombres y Apellidos</th>
                                        <th>Foto</th>
                                        <th>Teléfono</th>
                                        <th>Usuario Email</th>
                                        <th>Passw</th>
                                        <th>Función</th>
                                        <th>Activo?</th>
                                        <th>Locales</th>
                                        <th></th>
                                    </thead>

                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="mdl_add_user" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" style="font-size: 1em !important;"><b style="font-size: 1.5em !important;">NUEVO USUARIO</b></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="font-size: 1.5em !important;">×</span></button>
                </div>
                <div class="modal-body">
                    <form action="{{route('create_user')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>Nombres y Apellidos</label>
                                            <input type="text" class="form-control" name="name" id="name" required>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>Email-Usuario</label>
                                            <input type="text" class="form-control" name="email" id="email" required>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>Contraseña</label>
                                            <input type="password" class="form-control" name="password" id="password" required>
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
                                            <input type="text" class="form-control" name="phone" id="phone" required>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>Cargo</label>
                                            <select name="rol" id="rol" class="form-control" required>
                                                <option value="">Seleccionar Cargo</option>
                                                @foreach($roles as $rol)
                                                    @if ($rol->slug != 'superadmin')
                                                        <option value="{{$rol->id}}">{{$rol->name}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12" style="display: none;">
                                        <label>ESTADO</label>
                                        <div class="form-group">
                                            <input type="checkbox" checked value="1" name="status" id="status" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label>Añadir código supervisor</label><br>
                                    <label><input type="checkbox" id="is_supervisor" name="is_supervisor" value="1"> Pin Supervisor</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label>Pin Supervisor</label>
                                    <input type="number" class="form-control" name="pin" id="pin" min="00000000" max="99999999" step="00000001" readonly autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <fieldset>
                            <div class="row">
                                <div class="col-12">
                                    <h3 class="title text-center" style="font-weight: 600 !important;">Asignar Permisos</h3>
                                </div>
                                {{-- <div class="row"> --}}
                                    <div class="col-6" style="margin: 1em 0;">
                                        <button class="btn btn-primary-custom" type="button" data-toggle="collapse" data-target="#permissioncont" id="btnAsignPermissions">Asignar Permisos</button>
                                    </div>
                                    <div class="col-6">
                                        <div class="div">
                                            <label for="property">Acceso Total</label>
                                            <div class="form-group">
                                                <input type="checkbox" value="1" id="actotalgeneral">
                                            </div>
                                        </div>
                                    </div>
                                {{-- </div> --}}
                                <div class="col-12 collapse" id="permissioncont">
                                    <ul class="nav nav-tabs" id="custom-content-above-tab" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="pmenu-tab" data-toggle="pill" href="#pmenu" role="tab" aria-controls="pmenu" aria-selected="true">Menu</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="pacomercial-tab" data-toggle="pill" href="#pacomercial" role="tab" aria-controls="pacomercial" aria-selected="false">Área Comercial</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="palogistica-tab" data-toggle="pill" href="#palogistica" role="tab" aria-controls="palogistica" aria-selected="false">Área Logística</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="palmacen-tab" data-toggle="pill" href="#palmacen" role="tab" aria-controls="palmacen" aria-selected="false">Almacen</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="pcontabilidad-tab" data-toggle="pill" href="#pcontabilidad" role="tab" aria-controls="pcontabilidad" aria-selected="false">Contabilidad</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="pconfiguraciones-tab" data-toggle="pill" href="#pconfiguraciones" role="tab" aria-controls="pconfiguraciones" aria-selected="false">Configuraciones</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content" id="custom-content-above-tabContent">
                                        <div class="tab-pane fade active show" id="pmenu" role="tabpanel" aria-labelledby="pmenu-tab">
                                            <div class="row">
                                                <div class="col-8">
                                                    @foreach($pmenu as $pm)
                                                        <ul class="list-group">
                                                            <li class="list-group-item">
                                                                <label>
                                                                    {{ Form::checkbox('permissions[]', $pm->id, null,['class'=>'pmenu  pmenugen', 'data-permission'=>'0']) }}
                                                                    {{ $pm->name }}
                                                                    <em>({{ $pm->description ?: 'Sin descripcion' }})</em>
                                                                </label>
                                                            </li>
                                                        </ul>
                                                    @endforeach
                                                </div>
                                                <div class="col-4">
                                                    <label for="property">Acceso Total</label>
                                                    <div class="form-group">
                                                        <input type="checkbox" value="1" data-section='0' class="swac" id="acmenu">
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="tab-pane fade" id="pacomercial" role="tabpanel" aria-labelledby="pacomercial-tab">
                                            <div class="row">
                                                <div class="col-12">
                                                    <label for="property">Acceso Total para Area Comercial</label>
                                                    <div class="form-group">
                                                        <input type="checkbox" value="1" id="atcomercial">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="panel-group" id="accordion">
                                                        <div class="panel panel-default">
                                                            <div class="panel-heading">
                                                                <h6 class="panel-title">
                                                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Cotizaciones</a>
                                                                </h6>
                                                            </div>
                                                            <div id="collapseOne" class="panel-collapse collapse in">
                                                                <div class="row">
                                                                    <div class="col-8">
                                                                        <ul class="list-group">
                                                                            @foreach($pcotizaciones as $pc)
                                                                                <li class="list-group-item">
                                                                                    <label>
                                                                                        {{ Form::checkbox('permissions[]', $pc->id, null,['class'=>'pcotizaciones  pcomercial', 'data-permission'=>'1']) }}
                                                                                        {{ $pc->name }}
                                                                                        <em>({{ $pc->description ?: 'Sin descripcion' }})</em>
                                                                                    </label>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-4">
                                                                        <label for="property">Acceso Total</label>
                                                                        <div class="form-group">
                                                                            <input type="checkbox" value="1" data-section='1' class="swac" id="accotizaciones">
                                                                        </div>
                                                                    </div>
                                                                </div>
    
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="panel-group" id="accordion">
                                                        <div class="panel panel-default">
                                                            <div class="panel-heading">
                                                                <h6 class="panel-title">
                                                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">Ventas</a>
                                                                </h6>
                                                            </div>
                                                            <div id="collapseTwo" class="panel-collapse collapse in">
                                                                <div class="row">
                                                                    <div class="col-8">
                                                                        <ul class="list-group">
                                                                            @foreach($pventas as $pv)
                                                                                <li class="list-group-item">
                                                                                    <label>
                                                                                        {{ Form::checkbox('permissions[]', $pv->id, null,['class' => 'pventas pcomercial', 'data-permission'=>'2']) }}
                                                                                        {{ $pv->name }}
                                                                                        <em>({{ $pv->description ?: 'Sin descripcion' }})</em>
                                                                                    </label>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-4">
                                                                        <label for="property">Acceso Total</label>
                                                                        <div class="form-group">
                                                                            <input type="checkbox" value="1" data-section="2" class="swac" id="acventas">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="panel-group" id="accordion">
                                                        <div class="panel panel-default">
                                                            <div class="panel-heading">
                                                                <h6 class="panel-title">
                                                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree">Clientes</a>
                                                                </h6>
                                                            </div>
                                                            <div id="collapseThree" class="panel-collapse collapse in">
                                                                <div class="row">
                                                                    <div class="col-8">
                                                                        <ul class="list-group">
                                                                            @foreach($pclientes as $pcl)
                                                                                <li class="list-group-item">
                                                                                    <label>
                                                                                        {{ Form::checkbox('permissions[]', $pcl->id, null,['class'=>'pclientes pcomercial', 'data-permission'=>'3']) }}
                                                                                        {{ $pcl->name }}
                                                                                        <em>({{ $pcl->description ?: 'Sin descripcion' }})</em>
                                                                                    </label>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-4">
                                                                        <label for="property">Acceso Total</label>
                                                                        <div class="form-group">
                                                                            <input type="checkbox" value="1" data-section="3" class="swac" id="acclientes">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="panel-group" id="accordion">
                                                        <div class="panel panel-default">
                                                            <div class="panel-heading">
                                                                <h6 class="panel-title">
                                                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseA">Reportes</a>
                                                                </h6>
                                                            </div>
                                                            <div id="collapseA" class="panel-collapse collapse in">
                                                                <div class="row">
                                                                    <div class="col-8">
                                                                        <ul class="list-group">
                                                                            @foreach($preportes as $pre)
                                                                                <li class="list-group-item">
                                                                                    <label>
                                                                                        {{ Form::checkbox('permissions[]', $pre->id, null,['class'=>'preportes pcomercial', 'data-permission'=>'20']) }}
                                                                                        {{ $pre->name }}
                                                                                        <em>({{ $pre->description ?: 'Sin descripcion' }})</em>
                                                                                    </label>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-4">
                                                                        <label for="property">Acceso Total</label>
                                                                        <div class="form-group">
                                                                            <input type="checkbox" value="1" data-section="20" class="swac" id="acreportes">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="palogistica" role="tabpanel" aria-labelledby="palogistica-tab">
                                            <div class="row">
                                                <div class="col-12">
                                                    <label for="property">Acceso Total para Area Logistica</label>
                                                    <div class="form-group">
                                                        <input type="checkbox" value="1" id="atlogistica">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="panel-group" id="accordion">
                                                        <div class="panel panel-default">
                                                            <div class="panel-heading">
                                                                <h6 class="panel-title">
                                                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseFour">Proveedores</a>
                                                                </h6>
                                                            </div>
                                                            <div id="collapseFour" class="panel-collapse collapse in">
                                                                <div class="row">
                                                                    <div class="col-8">
                                                                        <ul class="list-group">
                                                                            @foreach($pproveedores as $pp)
                                                                                <li class="list-group-item">
                                                                                    <label>
                                                                                        {{ Form::checkbox('permissions[]', $pp->id, null,['class'=>'pproveedores plogistica', 'data-permission'=>'4']) }}
                                                                                        {{ $pp->name }}
                                                                                        <em>({{ $pp->description ?: 'Sin descripcion' }})</em>
                                                                                    </label>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-4">
                                                                        <label for="property">Acceso Total</label>
                                                                        <div class="form-group">
                                                                            <input type="checkbox" value="1" data-section="4" class="swac" id="acproveedores">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="panel-group" id="accordion">
                                                        <div class="panel panel-default">
                                                            <div class="panel-heading">
                                                                <h6 class="panel-title">
                                                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapsSesenta">Centro de Costos</a>
                                                                </h6>
                                                            </div>
                                                            <div id="collapsSesenta" class="panel-collapse collapse in">
                                                                <div class="row">
                                                                    <div class="col-8">
                                                                        <ul class="list-group">
                                                                            @foreach($pcenterCost as $pcc)
                                                                                <li class="list-group-item">
                                                                                    <label>
                                                                                        {{ Form::checkbox('permissions[]', $pcc->id, null,['class'=>'pproveedores plogistica', 'data-permission'=>'60']) }}
                                                                                        {{ $pcc->name }}
                                                                                        <em>({{ $pcc->description ?: 'Sin descripcion' }})</em>
                                                                                    </label>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-4">
                                                                        <label for="property">Acceso Total</label>
                                                                        <div class="form-group">
                                                                            <input type="checkbox" value="1" data-section="60" class="swac" id="accentercost">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="panel-group" id="accordion">
                                                        <div class="panel panel-default">
                                                            <div class="panel-heading">
                                                                <h6 class="panel-title">
                                                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseFive">Productos y Servicios</a>
                                                                </h6>
                                                            </div>
                                                            <div id="collapseFive" class="panel-collapse collapse in">
                                                                <div class="row">
                                                                    <div class="col-8">
                                                                        <ul class="list-group">
                                                                            @foreach($pservicios as $ps)
                                                                                <li class="list-group-item">
                                                                                    <label>
                                                                                        {{ Form::checkbox('permissions[]', $ps->id, null,['class' => 'pproductos plogistica', 'data-permission'=>'5']) }}
                                                                                        {{ $ps->name }}
                                                                                        <em>({{ $ps->description ?: 'Sin descripcion' }})</em>
                                                                                    </label>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-4">
                                                                        <label for="property">Acceso Total</label>
                                                                        <div class="form-group">
                                                                            <input type="checkbox" value="1" data-section="5" class="swac" id="acproductos">
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="panel-group" id="accordion">
                                                        <div class="panel panel-default">
                                                            <div class="panel-heading">
                                                                <h6 class="panel-title">
                                                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseFourTeen">Compras</a>
                                                                </h6>
                                                            </div>
                                                            <div id="collapseFourTeen" class="panel-collapse collapse in">
                                                                <div class="row">
                                                                    <div class="col-8">
                                                                        <ul class="list-group">
                                                                            @foreach($pcompras as $pcp)
                                                                                <li class="list-group-item">
                                                                                    <label>
                                                                                        {{ Form::checkbox('permissions[]', $pcp->id, null,['class' => 'pcompras plogistica', 'data-permission'=>'14']) }}
                                                                                        {{ $pcp->name }}
                                                                                        <em>({{ $pcp->description ?: 'Sin descripcion' }})</em>
                                                                                    </label>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-4">
                                                                        <label for="property">Acceso Total</label>
                                                                        <div class="form-group">
                                                                            <input type="checkbox" value="1" data-section="14" class="swac" id="accompras">
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="panel-group" id="accordion">
                                                        <div class="panel panel-default">
                                                            <div class="panel-heading">
                                                                <h6 class="panel-title">
                                                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseSix">Categorías</a>
                                                                </h6>
                                                            </div>
                                                            <div id="collapseSix" class="panel-collapse collapse in">
                                                                <div class="row">
                                                                    <div class="col-8">
                                                                        <ul class="list-group">
                                                                            @foreach($pcategorias as $pca)
                                                                                <li class="list-group-item">
                                                                                    <label>
                                                                                        {{ Form::checkbox('permissions[]', $pca->id, null,['class'=>'pcategorias plogistica', 'data-permission'=>'6']) }}
                                                                                        {{ $pca->name }}
                                                                                        <em>({{ $pca->description ?: 'Sin descripcion' }})</em>
                                                                                    </label>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-4">
                                                                        <label for="property">Acceso Total</label>
                                                                        <div class="form-group">
                                                                            <input type="checkbox" value="1" data-section="6" class="swac" id="accategorias">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="palmacen" role="tabpanel" aria-labelledby="palmacen-tab">
                                            <div class="row">
                                                <div class="col-12">
                                                    <label for="property">Acceso Total para Almacenes</label>
                                                    <div class="form-group">
                                                        <input type="checkbox" value="1" id="atalmacenes">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="panel-group" id="accordion">
                                                        <div class="panel panel-default">
                                                            <div class="panel-heading">
                                                                <h6 class="panel-title">
                                                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseSeven">Almacenes</a>
                                                                </h6>
                                                            </div>
                                                            <div id="collapseSeven" class="panel-collapse collapse in">
                                                                <div class="row">
                                                                    <div class="col-8">
                                                                        <ul class="list-group">
                                                                            @foreach($palmacenes as $pa)
                                                                                <li class="list-group-item">
                                                                                    <label>
                                                                                        {{ Form::checkbox('permissions[]', $pa->id, null,['class'=>'palmacen palmacenes', 'data-permission'=>'7']) }}
                                                                                        {{ $pa->name }}
                                                                                        <em>({{ $pa->description ?: 'Sin descripcion' }})</em>
                                                                                    </label>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-4">
                                                                        <label for="property">Acceso Total</label>
                                                                        <div class="form-group">
                                                                            <input type="checkbox" value="1" data-section="7" class="swac" id="acalmacen">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="panel panel-default">
                                                            <div class="panel-heading">
                                                                <h6 class="panel-title">
                                                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseB">Inventario</a>
                                                                </h6>
                                                            </div>
                                                            <div id="collapseB" class="panel-collapse collapse in">
                                                                <div class="row">
                                                                    <div class="col-8">
                                                                        <ul class="list-group">
                                                                            @foreach($pinventarios as $pi)
                                                                                <li class="list-group-item">
                                                                                    <label>
                                                                                        {{ Form::checkbox('permissions[]', $pi->id, null,['class'=>'pinventarios palmacenes', 'data-permission'=>'21']) }}
                                                                                        {{ $pi->name }}
                                                                                        <em>({{ $pi->description ?: 'Sin descripcion' }})</em>
                                                                                    </label>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-4">
                                                                        <label for="property">Acceso Total</label>
                                                                        <div class="form-group">
                                                                            <input type="checkbox" value="1" data-section="21" class="swac" id="acalmacen">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="pcontabilidad" role="tabpanel" aria-labelledby="pcontabilidad-tab">
                                            <div class="row">
                                                <div class="col-12">
                                                    <label for="property">Acceso Total a Contabilidad</label>
                                                    <div class="form-group">
                                                        <input type="checkbox" value="1" id="atcontabilidad">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="panel-group" id="accordion">
                                                        <div class="panel panel-default">
                                                            <div class="panel-heading">
                                                                <h6 class="panel-title">
                                                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseC">Contabilidad</a>
                                                                </h6>
                                                            </div>
                                                            <div id="collapseC" class="panel-collapse collapse in">
                                                                <div class="row">
                                                                    <div class="col-8">
                                                                        <ul class="list-group">
                                                                            @foreach($pcomprobantes as $pcm)
                                                                                <li class="list-group-item">
                                                                                    <label>
                                                                                        {{ Form::checkbox('permissions[]', $pcm->id, null,['class'=>'pcomprobantes pcontabilidad', 'data-permission'=>'22']) }}
                                                                                        {{ $pcm->name }}
                                                                                        <em>({{ $pcm->description ?: 'Sin descripcion' }})</em>
                                                                                    </label>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-4">
                                                                        <label for="property">Acceso Total</label>
                                                                        <div class="form-group">
                                                                            <input type="checkbox" value="1" data-section="22" class="swac" id="accontabilidad">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="pconfiguraciones" role="tabpanel" aria-labelledby="pconfiguraciones-tab">
                                            <div class="row">
                                                <div class="col-12">
                                                    <label for="property">Acceso Total para Configuraciones</label>
                                                    <div class="form-group">
                                                        <input type="checkbox" value="1" id="atconfiguracion">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="panel-group" id="accordion">
                                                        <div class="panel panel-default">
                                                            <div class="panel-heading">
                                                                <h6 class="panel-title">
                                                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseEight">Empresa</a>
                                                                </h6>
                                                            </div>
                                                            <div id="collapseEight" class="panel-collapse collapse in">
                                                                <div class="row">
                                                                    <div class="col-8">
                                                                        <ul class="list-group">
                                                                            @foreach($pempresa as $pe)
                                                                                <li class="list-group-item">
                                                                                    <label>
                                                                                        {{ Form::checkbox('permissions[]', $pe->id, null, ['class' => 'pempresa pconfiguracion', 'data-permission'=>'8']) }}
                                                                                        {{ $pe->name }}
                                                                                        <em>({{ $pe->description ?: 'Sin descripcion' }})</em>
                                                                                    </label>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-4">
                                                                        <label for="property">Acceso Total</label>
                                                                        <div class="form-group">
                                                                            <input type="checkbox" value="1" data-section="8" class="swac" id="acempresa">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="panel-group" id="accordion">
                                                        <div class="panel panel-default">
                                                            <div class="panel-heading">
                                                                <h6 class="panel-title">
                                                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseNine">Locales y Series</a>
                                                                </h6>
                                                            </div>
                                                            <div id="collapseNine" class="panel-collapse collapse in">
                                                                <div class="row">
                                                                    <div class="col-8">
                                                                        <ul class="list-group">
                                                                            @foreach($plocalserie as $lcs)
                                                                                <li class="list-group-item">
                                                                                    <label>
                                                                                        {{ Form::checkbox('permissions[]', $lcs->id, null,['class'=>'plocal pconfiguracion', 'data-permission'=>'9']) }}
                                                                                        {{ $lcs->name }}
                                                                                        <em>({{ $lcs->description ?: 'Sin descripcion' }})</em>
                                                                                    </label>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-4">
                                                                        <label for="property">Acceso Total</label>
                                                                        <div class="form-group">
                                                                            <input type="checkbox" value="1" data-section="9" class="swac" id="aclocal">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="panel-group" id="accordion">
                                                        <div class="panel panel-default">
                                                            <div class="panel-heading">
                                                                <h6 class="panel-title">
                                                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseEleven">Usuarios</a>
                                                                </h6>
                                                            </div>
                                                            <div id="collapseEleven" class="panel-collapse collapse in">
                                                                <div class="row">
                                                                    <div class="col-8">
                                                                        <ul class="list-group">
                                                                            @foreach($pusuarios as $pu)
                                                                                <li class="list-group-item">
                                                                                    <label>
                                                                                        {{ Form::checkbox('permissions[]', $pu->id, null,['class'=>'pusuario pconfiguracion', 'data-permission'=>'11']) }}
                                                                                        {{ $pu->name }}
                                                                                        <em>({{ $pu->description ?: 'Sin descripcion' }})</em>
                                                                                    </label>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-4">
                                                                        <label for="property">Acceso Total</label>
                                                                        <div class="form-group">
                                                                            <input type="checkbox" value="1" data-section="11" class="swac" id="acusurios">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="panel-group" id="accordion">
                                                        <div class="panel panel-default">
                                                            <div class="panel-heading">
                                                                <h6 class="panel-title">
                                                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwelve">Roles</a>
                                                                </h6>
                                                            </div>
                                                            <div id="collapseTwelve" class="panel-collapse collapse in">
                                                                <div class="row">
                                                                    <div class="col-8">
                                                                        <ul class="list-group">
                                                                            @foreach($proles as $pro)
                                                                                <li class="list-group-item">
                                                                                    <label>
                                                                                        {{ Form::checkbox('permissions[]', $pro->id, null,['class'=>'proles pconfiguracion', 'data-permission'=>'12']) }}
                                                                                        {{ $pro->name }}
                                                                                        <em>({{ $pro->description ?: 'Sin descripcion' }})</em>
                                                                                    </label>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                    <div class="col-4">
                                                                        <label for="property">Acceso Total</label>
                                                                        <div class="form-group">
                                                                            <input type="checkbox" value="1" data-section="12" class="swac" id="acroles">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset>
                            <div class="row">
                                <div class="col-12">
                                    <h3 class="title text-center" style="font-weight: 600 !important;">LOCAL ASIGNADO</h3>
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
                                        <button type="submit" class="btn btn-primary-custom btn-block">Crear Usuario</button>
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
    <script>
        let tbl_data = $("#tbl_data").DataTable({
            'pageLength' : 15,
            'bLengthChange' : false,
            'lengthMenu': false,
            'language': {
                'url': '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json'
            },
            "order": [[ 0, "asc" ]],
            'searching': false,
            'processing': false,
            'serverSide': true,
            'ajax': {
                'url': '{{route("dt_users")}}',
                'type' : 'get',
                'data': function(d) {

                }
            },
            'columns': [
                {
                    data: 'id'
                },
                {
                    data: 'name',
                },
                {
                    data: 'logo',
                    render: function(data) {
                        let url = '{{asset('images/profile')}}' + '/' + data;
                        return '<img style="width: 40px; height: 40px;" src="' + url + '"' + '>';
                    }
                },
                {
                    data: 'phone'
                },
                {
                    data: 'email'
                },
                {
                    data: 'id',
                    render: function(data, type, row) {
                        return '*********';
                    }
                },
                {
                    data: 'roles[0].name'
                },
                {
                    data: 'id'
                },
                {
                    data: 'headquarter.description'
                },
                {data: 'id'}
            ],
            'fnRowCallback': function( nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                if(aData['status'] == 1) {
                    $(nRow).find('td:eq(7)').html('<button type="button" class="btn btn-rounded btn-danger-custom btn-sm btn_disable">Deshabilitar</button>');
                } else {
                    $(nRow).find('td:eq(7)').html('<button type="button" class="btn btn-rounded btn-secondary-custom btn-sm btn_enable">Activar</button>');
                }

                // $(nRow).find('td:eq(5)').html(aData['roles'][0]['name']);

                $(nRow).find('td:eq(9)').html('<button type="button" class="btn  btn-redondos btn-secondary-custom btn_edit"><i class="fa fa-edit"><i></button>');
                var index = iDisplayIndex + 1;
                $('td:eq(0)',nRow).html(index);
            }
        });

        $('body').on('click', '.btn_disable', function() {
            let data = tbl_data.row( $(this).parents('tr') ).data();
            $.ajax({
                url: '{{route('updateStatusUser')}}',
                data: {
                    id: data['id'],
                    status: 2,
                    _token: '{{csrf_token()}}',
                },
                dataType: 'json',
                type: 'put',
                success: function(response) {
                    if(response === true) {
                        toastr.success('Estado actualizado correctamente');
                        tbl_data.ajax.reload();
                    }
                }
            });
        });

        $('body').on('click', '.btn_enable', function() {
            let data = tbl_data.row( $(this).parents('tr') ).data();
            $.ajax({
                url: '{{route('updateStatusUser')}}',
                data: {
                    id: data['id'],
                    status: 1,
                    _token: '{{csrf_token()}}',
                },
                dataType: 'json',
                type: 'put',
                success: function(response) {
                    if(response === true) {
                        toastr.success('Estado actualizado correctamente');
                        tbl_data.ajax.reload();
                    }
                }
            });
        });


        $('body').on('click', '.btn_edit', function() {
            let data = tbl_data.row( $(this).parents('tr') ).data();
            window.location = '/configuration/users/edit/' + data['id'];
        });

        $('body').on('click', '#btnAddUser', function() {
            $('#mdl_add_user').modal('show');
        });
        $('body').on('click', '#btnAsignPermissions', function() {
            $('#mdl_asign_permissions').modal('show');
        });


        //////////////////////////////////////////////////////////////////

        $("#actotalgeneral").on( 'change', function() {
            if( $(this).is(':checked') ) {
                $('#atcomercial').parent().click();
                $('#atlogistica').parent().click();
                $('#atalmacenes').parent().click();
                $('#atcontabilidad').parent().click();
                $('#atconfiguracion').parent().click();
                $('#acmenu').parent().click();
            } else {
                $('#atcomercial').parent().click();
                $('#atlogistica').parent().click();
                $('#atalmacenes').parent().click();
                $('#atcontabilidad').parent().click();
                $('#atconfiguracion').parent().click();
                $('#acmenu').parent().click();
            }
        });

        $("#atcomercial").on( 'change', function() {
            if( $(this).is(':checked') ) {
                // $(".pcomercial").prop('checked', $(this).prop('checked'));
                $('.pcomercial').not(this).prop('checked', this.checked);

                if ($('#accotizaciones').parent().hasClass('off')) {
                    $('#accotizaciones').parent().click();
                }
                if ($('#acventas').parent().hasClass('off')) {
                    $('#acventas').parent().click();
                }
                if ($('#acclientes').parent().hasClass('off')) {
                    $('#acclientes').parent().click();
                }
            } else {
                $('.pcomercial').not(this).prop('checked', false);

                if (!$('#accotizaciones').parent().hasClass('off')) {
                    $('#accotizaciones').parent().click();
                }
                if (!$('#acventas').parent().hasClass('off')) {
                    $('#acventas').parent().click();
                }
                if (!$('#acclientes').parent().hasClass('off')) {
                    $('#acclientes').parent().click();
                }
            }
        });
        $("#atlogistica").on( 'change', function() {
            if( $(this).is(':checked') ) {
                // $(".pcomercial").prop('checked', $(this).prop('checked'));
                $('.plogistica').not(this).prop('checked', this.checked);

                if ($('#acproveedores').parent().hasClass('off')) {
                    $('#acproveedores').parent().click();
                }
                if ($('#acproductos').parent().hasClass('off')) {
                    $('#acproductos').parent().click();
                }
                if ($('#accategorias').parent().hasClass('off')) {
                    $('#accategorias').parent().click();
                }
            } else {
                $('.plogistica').not(this).prop('checked', false);

                if (!$('#acproveedores').parent().hasClass('off')) {
                    $('#acproveedores').parent().click();
                }
                if (!$('#acproductos').parent().hasClass('off')) {
                    $('#acproductos').parent().click();
                }
                if (!$('#accategorias').parent().hasClass('off')) {
                    $('#accategorias').parent().click();
                }
            }
        });
        $("#atalmacenes").on( 'change', function() {
            if( $(this).is(':checked') ) {
                // $(".pcomercial").prop('checked', $(this).prop('checked'));
                $('.palmacenes').not(this).prop('checked', this.checked);

                if ($('#acalmacen').parent().hasClass('off')) {
                    $('#acalmacen').parent().click();
                }
            } else {
                $('.palmacenes').not(this).prop('checked', false);

                if (!$('#acalmacen').parent().hasClass('off')) {
                    $('#acalmacen').parent().click();
                }
            }
        });
        $("#atcontabilidad").on( 'change', function() {
            if( $(this).is(':checked') ) {
                // $(".pcomercial").prop('checked', $(this).prop('checked'));
                $('.pcontabilidad').not(this).prop('checked', this.checked);

                if ($('#accontabilidad').parent().hasClass('off')) {
                    $('#accontabilidad').parent().click();
                }
            } else {
                $('.pcontabilidad').not(this).prop('checked', false);

                if (!$('#accontabilidad').parent().hasClass('off')) {
                    $('#accontabilidad').parent().click();
                }
            }
        });
        $("#atconfiguracion").on( 'change', function() {
            if( $(this).is(':checked') ) {
                // $(".pcomercial").prop('checked', $(this).prop('checked'));
                $('.pconfiguracion').not(this).prop('checked', this.checked);

                if ($('#acempresa').parent().hasClass('off')) {
                    $('#acempresa').parent().click();
                }
                if ($('#aclocal').parent().hasClass('off')) {
                    $('#aclocal').parent().click();
                }
                if ($('#acapariencia').parent().hasClass('off')) {
                    $('#acapariencia').parent().click();
                }
                if ($('#acusuario').parent().hasClass('off')) {
                    $('#acusuario').parent().click();
                }
                if ($('#roles').parent().hasClass('off')) {
                    $('#roles').parent().click();
                }
            } else {
                $('.pconfiguracion').not(this).prop('checked', false);

                if (!$('#acempresa').parent().hasClass('off')) {
                    $('#acempresa').parent().click();
                }
                if (!$('#aclocal').parent().hasClass('off')) {
                    $('#aclocal').parent().click();
                }
                if (!$('#acapariencia').parent().hasClass('off')) {
                    $('#acapariencia').parent().click();
                }
                if (!$('#acusuario').parent().hasClass('off')) {
                    $('#acusuario').parent().click();
                }
                if (!$('#roles').parent().hasClass('off')) {
                    $('#roles').parent().click();
                }
            }
        });

        $('body').on('change','.swac', function() {
            let type = $(this).data('section');
            // console.log(type);
            if($(this).is(':checked')) {
                $('input:checkbox[data-permission='+ type +']').each(function() {
                    // console.log($(this));
                    $(this).prop('checked', true);
                });
            } else {
                $('input:checkbox[data-permission='+ type +']').each(function() {
                    // console.log($(this));
                    $(this).prop('checked', false);
                });
            }
        });

        $('#rol').change(function() {
            if ($(this).val() == 2 || $(this).val() == 3) {
                $('#actotalgeneral').attr('checked', true);
            } else {
                $('#actotalgeneral').attr('checked', false);
            }
        });

        $('#is_supervisor').change(function(e) {
            e.preventDefault();

            if ($(this).is(':checked')) {
                $('#pin').attr('readonly', false);
            } else {
                $('#pin').attr('readonly', true);
            }
        })

        $('#pin').keyup(function(e) {
            e.preventDefault();
            if ($(this).val().length > 8) {
                $(this).val($(this).val().slice(0, 8));
            }
        })
    </script>
    @if(Session::has('success'))
        <script>
            toastr.success('Se registró correctamente el usuario.');
        </script>
    @endif
    @if(Session::has('error'))
        <script>
            toastr.error('Oucrrió un error cuando intentaba grabar el usuario');
        </script>
    @endif
@stop