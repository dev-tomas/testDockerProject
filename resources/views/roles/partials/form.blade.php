<div class="form-group">
    {{ Form::label('name', 'Nombre') }}
    {{ Form::text('name', null, ['class' => 'form-control']) }}
</div>
<div class="form-group">
    {{ Form::label('slug', 'Slug') }}
    {{ Form::text('slug', null, ['class' => 'form-control']) }}
</div>
<div class="form-group">
    {{ Form::label('description', 'Descripcion') }}
    {{ Form::textarea('description', null, ['class' => 'form-control']) }}
</div>
<hr>
<h3>Permiso Especial</h3>
<div class="form-group">
    <label>{{ Form::radio('special', 'all-access') }} Acceso Total</label>
    <label>{{ Form::radio('special', 'no-access') }} Ningun Acceso</label>
</div>
<hr>
<h3>Lista de Permisos</h3>
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
        <a class="nav-link" id="pconfiguraciones-tab" data-toggle="pill" href="#pconfiguraciones" role="tab" aria-controls="pconfiguraciones" aria-selected="false">Configuraciones</a>
    </li>
</ul>
<div class="tab-content" id="custom-content-above-tabContent">
    <div class="tab-pane fade active show" id="pmenu" role="tabpanel" aria-labelledby="pmenu-tab">
        Permisos para Mostrar la siguientes secciones en el menu del sistema.
        @foreach($pmenu as $pm)
            <ul class="list-unstyled">
                <li>
                    <label>
                        {{ Form::checkbox('permissions[]', $pm->id, null) }}
                        {{ $pm->name }}
                        <em>({{ $pm->description ?: 'Sin descripcion' }})</em>
                    </label>
                </li>
            </ul>
        @endforeach
    </div>
    <div class="tab-pane fade" id="pacomercial" role="tabpanel" aria-labelledby="pacomercial-tab">
        <ul class="nav nav-tabs" id="custom-content-above-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="pcotizaciones-tab" data-toggle="pill" href="#pcotizaciones" role="tab" aria-controls="pcotizaciones" aria-selected="true">Cotizaciones</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="pventas-tab" data-toggle="pill" href="#pventas" role="tab" aria-controls="pventas" aria-selected="false">Ventas</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="pclientes-tab" data-toggle="pill" href="#pclientes" role="tab" aria-controls="pclientes" aria-selected="false">Clientes</a>
            </li>
        </ul>
        <div class="tab-content" id="custom-content-above-tabContent">
            <div class="tab-pane fade active show" id="pcotizaciones" role="tabpanel" aria-labelledby="pcotizaciones-tab">
                Permisos para permisos para la sección de cotizaciones.
                @foreach($pcotizaciones as $pc)
                    <ul class="list-unstyled">
                        <li>
                            <label>
                                {{ Form::checkbox('permissions[]', $pc->id, null) }}
                                {{ $pc->name }}
                                <em>({{ $pc->description ?: 'Sin descripcion' }})</em>
                            </label>
                        </li>
                    </ul>
                @endforeach
            </div>
            <div class="tab-pane fade" id="pventas" role="tabpanel" aria-labelledby="pventas-tab">
                    Permisos para permisos para la sección de ventas.
                @foreach($pventas as $pv)
                    <ul class="list-unstyled">
                        <li>
                            <label>
                                {{ Form::checkbox('permissions[]', $pv->id, null) }}
                                {{ $pv->name }}
                                <em>({{ $pv->description ?: 'Sin descripcion' }})</em>
                            </label>
                        </li>
                    </ul>
                @endforeach
            </div>
            <div class="tab-pane fade" id="pclientes" role="tabpanel" aria-labelledby="pclientes-tab">
                    Permisos para permisos para la sección de clientes.
                @foreach($pclientes as $pcl)
                    <ul class="list-unstyled">
                        <li>
                            <label>
                                {{ Form::checkbox('permissions[]', $pcl->id, null) }}
                                {{ $pcl->name }}
                                <em>({{ $pcl->description ?: 'Sin descripcion' }})</em>
                            </label>
                        </li>
                    </ul>
                @endforeach
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="palogistica" role="tabpanel" aria-labelledby="palogistica-tab">
        <ul class="nav nav-tabs" id="custom-content-above-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="pproveedores-tab" data-toggle="pill" href="#pproveedores" role="tab" aria-controls="pproveedores" aria-selected="true">Proveedores</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="pservicios-tab" data-toggle="pill" href="#pservicios" role="tab" aria-controls="pservicios" aria-selected="false">Productos y Servicios</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="pcatgorias-tab" data-toggle="pill" href="#pcatgorias" role="tab" aria-controls="pcatgorias" aria-selected="false">Categorías</a>
            </li>
        </ul>
        <div class="tab-content" id="custom-content-above-tabContent">
            <div class="tab-pane fade active show" id="pproveedores" role="tabpanel" aria-labelledby="pproveedores-tab">
                Permisos para permisos para la sección de Proveedores.
                @foreach($pproveedores as $pp)
                    <ul class="list-unstyled">
                        <li>
                            <label>
                                {{ Form::checkbox('permissions[]', $pp->id, null) }}
                                {{ $pp->name }}
                                <em>({{ $pp->description ?: 'Sin descripcion' }})</em>
                            </label>
                        </li>
                    </ul>
                @endforeach
            </div>
            <div class="tab-pane fade" id="pservicios" role="tabpanel" aria-labelledby="pservicios-tab">
                    Permisos para permisos para la sección de Productos y Servicios.
                @foreach($pservicios as $ps)
                    <ul class="list-unstyled">
                        <li>
                            <label>
                                {{ Form::checkbox('permissions[]', $ps->id, null) }}
                                {{ $ps->name }}
                                <em>({{ $ps->description ?: 'Sin descripcion' }})</em>
                            </label>
                        </li>
                    </ul>
                @endforeach
            </div>
            <div class="tab-pane fade" id="pcatgorias" role="tabpanel" aria-labelledby="pcatgorias-tab">
                    Permisos para permisos para la sección de Categorías.
                @foreach($pcategorias as $pca)
                    <ul class="list-unstyled">
                        <li>
                            <label>
                                {{ Form::checkbox('permissions[]', $pca->id, null) }}
                                {{ $pca->name }}
                                <em>({{ $pca->description ?: 'Sin descripcion' }})</em>
                            </label>
                        </li>
                    </ul>
                @endforeach
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="palmacen" role="tabpanel" aria-labelledby="palmacen-tab">
        <ul class="nav nav-tabs" id="custom-content-above-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="palmacenes-tab" data-toggle="pill" href="#palmacenes" role="tab" aria-controls="palmacenes" aria-selected="true">Almacenes</a>
            </li>
        </ul>
        <div class="tab-content" id="custom-content-above-tabContent">
            <div class="tab-pane fade active show" id="palmacenes" role="tabpanel" aria-labelledby="palmacenes-tab">
                Permisos para permisos para la sección de Almacenes.
                @foreach($palmacenes as $pa)
                    <ul class="list-unstyled">
                        <li>
                            <label>
                                {{ Form::checkbox('permissions[]', $pa->id, null) }}
                                {{ $pa->name }}
                                <em>({{ $pa->description ?: 'Sin descripcion' }})</em>
                            </label>
                        </li>
                    </ul>
                @endforeach
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="pconfiguraciones" role="tabpanel" aria-labelledby="pconfiguraciones-tab">
        <ul class="nav nav-tabs" id="custom-content-above-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="pempresa-tab" data-toggle="pill" href="#pempresa" role="tab" aria-controls="pempresa" aria-selected="true">Empresa</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="plocalserie-tab" data-toggle="pill" href="#plocalserie" role="tab" aria-controls="plocalserie" aria-selected="true">Locales y Series</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="papariencia-tab" data-toggle="pill" href="#papariencia" role="tab" aria-controls="papariencia" aria-selected="true">Apariencia</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="pusuarios-tab" data-toggle="pill" href="#pusuarios" role="tab" aria-controls="pusuarios" aria-selected="true">Usuarios</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="proles-tab" data-toggle="pill" href="#proles" role="tab" aria-controls="proles" aria-selected="true">Roles y Permisos</a>
            </li>
        </ul>
        <div class="tab-content" id="custom-content-above-tabContent">
            <div class="tab-pane fade active shows" id="pempresa" role="tabpanel" aria-labelledby="pempresa-tab">
                Permisos para permisos para la sección de Empresa.
                @foreach($pempresa as $pe)
                    <ul class="list-unstyled">
                        <li>
                            <label>
                                {{ Form::checkbox('permissions[]', $pe->id, null) }}
                                {{ $pe->name }}
                                <em>({{ $pe->description ?: 'Sin descripcion' }})</em>
                            </label>
                        </li>
                    </ul>
                @endforeach
            </div>
            <div class="tab-pane fade" id="plocalserie" role="tabpanel" aria-labelledby="plocalserie-tab">
                Permisos para permisos para la sección de Locales y Series.
                @foreach($plocalserie as $pls)
                    <ul class="list-unstyled">
                        <li>
                            <label>
                                {{ Form::checkbox('permissions[]', $pls->id, null) }}
                                {{ $pls->name }}
                                <em>({{ $pls->description ?: 'Sin descripcion' }})</em>
                            </label>
                        </li>
                    </ul>
                @endforeach
            </div>
            <div class="tab-pane fade" id="papariencia" role="tabpanel" aria-labelledby="papariencia-tab">
                Permisos para permisos para la sección de Apariencia.
                @foreach($papariencia as $pap)
                    <ul class="list-unstyled">
                        <li>
                            <label>
                                {{ Form::checkbox('permissions[]', $pap->id, null) }}
                                {{ $pap->name }}
                                <em>({{ $pap->description ?: 'Sin descripcion' }})</em>
                            </label>
                        </li>
                    </ul>
                @endforeach
            </div>
            <div class="tab-pane fade" id="pusuarios" role="tabpanel" aria-labelledby="pusuarios-tab">
                Permisos para permisos para la sección de Usuarios.
                @foreach($pusuarios as $pu)
                    <ul class="list-unstyled">
                        <li>
                            <label>
                                {{ Form::checkbox('permissions[]', $pu->id, null) }}
                                {{ $pu->name }}
                                <em>({{ $pu->description ?: 'Sin descripcion' }})</em>
                            </label>
                        </li>
                    </ul>
                @endforeach
            </div>
            <div class="tab-pane fade" id="proles" role="tabpanel" aria-labelledby="proles-tab">
                Permisos para permisos para la sección de Roles y Permisos.
                @foreach($proles as $pro)
                    <ul class="list-unstyled">
                        <li>
                            <label>
                                {{ Form::checkbox('permissions[]', $pro->id, null) }}
                                {{ $pro->name }}
                                <em>({{ $pro->description ?: 'Sin descripcion' }})</em>
                            </label>
                        </li>
                    </ul>
                @endforeach
            </div>
        </div>
    </div>
</div>
<div class="form-group">
    {{ Form::submit('Guardar', null, ['class' => 'btn btn-primary-custom']) }}
</div>

