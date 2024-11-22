@extends('layouts.azia')
@section('css')
    <style>.prepare,.delete,.p_enabled,.p_disabled{display: none;}</style>
    @can('pservicios.edit')
        <style>.prepare{display: inline-block;}</style>
    @endcan
    @can('pservicios.delete')
        <style>.delete{display: inline-block;}</style>
    @endcan
    @can('pservicios.disable')
        <style>.p_enabled,.p_disabled{display: inline-block;}</style>
    @endcan
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header color-gray">
                    <div class="row">
                        <div class="col-12 text-center">
                            <h3 class="card-title">PRODUCTOS/SERVICIOS</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-10">
                            @can('pservicios.create')
                                <button class="btn btn-primary-custom" id="openModalProduct">
                                    NUEVO PRODUCTO
                                </button>
                            @endcan
                            @can('pservicios.import')
                                <button class="btn btn-secondary-custom" id="openModalUpload">
                                    <i class="fa fa-upload"></i>
                                    EXCEL
                                </button>
                            @endcan
                            @can('pservicios.export')
                                <a class="btn btn-secondary-custom" href="{{route('products.export')}}">
                                    <i class="fa fa-download"></i>
                                    EXCEL
                                </a>
                            @endcan
                            @can('categorias.show')
                                <a href="/warehouse.categories" class="btn btn-dark-custom">G. CATEGORÍAS</a>
                            @endcan
                            <a href="/warehouse/kits" class="btn btn-dark-custom">Gestionar Combos</a>
                            <a href="/warehouse/products/catalog" target="_blank" class="btn btn-dark-custom">Catálogo</a>
                        </div>
                        <div class="col-12 col-md-2">
                            <input type="text" class="form-control" id="search" name="search" placeholder="Buscar producto">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12"><br></div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <form id="frm_table">
                                    <table id="tbl_data" class="dt-bootstrap4 table-hover"  style="width: 100%;">
                                        <thead>
                                        <th> </th>
                                        <th>COD. BARRA</th>
                                        <th>CODIGO</th>
                                        <th>U. MED.</th>
                                        <th>MARCA</th>
                                        <th>DESCRIPCIÓN PRODUCTO/SERVICIO</th>
                                        <th>STOCK</th>
                                        <th>M</th>
                                        <th>PRECIOS Y UTILIDADES</th>
                                        <th>COSTO</th>
                                        <th>ALMACÉN</th>
                                        <th>ESTADO</th>
                                        <th>IMAGEN</th>
                                        <th>Opc.</th>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form action="{{ route('import.products') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div id="mdl_upload_products" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="z-index: 9999;">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">SUBIR PRODUCTOS</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body">
                        <form role="form" data-toggle="validator" id="frm_products">
                            <div class="row">
                                <div class="col-12">
                                    <a class="btn btn-secondary-custom btn-block" href="{{route('export.products.template')}}">
                                        Descargar Plantilla Excel
                                    </a>
                                </div>

                                <div class="col-12">
                                    <br>
                                </div>

                                <div class="col-12" >
                                    <input type="file" name="file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required>
                                </div>

                                <div class="col-12">
                                    <br>
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-primary-custom btn-block">Subir Excel</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="modal fade" id="typeProductModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Información de producto/servicio</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="media mb-3">
                        <img src="..." class="mr-3" alt="...">
                        <div class="media-body">
                            <h5 class="mt-0">Inventario</h5>
                            <div>
                                Los productos que compra o vende y de cuyas cantidades realiza un seguimiento.
                            </div>
                            <button type="button" class="btn btn-primary-custom btn-sm" id="mdlProduct">Agregar</button>
                        </div>
                    </div>
                    <div class="media mb-3">
                        <img src="..." class="mr-3" alt="...">
                        <div class="media-body">
                            <h5 class="mt-0">No está en el Inventario</h5>
                            <div>
                                Los productos que compra o vende y de cuyas cantidades no necesita (o no puede) realizar un seguimiento.
                            </div>
                            <button type="button" class="btn btn-primary-custom btn-sm" id="mdlProduct2">Agregar</button>
                        </div>
                    </div>
                    <div class="media mb-3">
                        <img src="..." class="mr-3" alt="...">
                        <div class="media-body">
                            <h5 class="mt-0">Servicio</h5>
                            Los servcios que les proporciona a los clientes.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="mdl_add_product" class="modal fade bd-example-modal-lg" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog modal-xl" >
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">NUEVO PRODUCTO/SERVICIO</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <form role="form" data-toggle="validator" id="frm_product" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="product_id" name="product_id">
                        <nav>
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-principal" role="tab" aria-controls="nav-principal" aria-selected="true">Opciones principales</a>
                                <!--<a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-advanced" role="tab" aria-controls="nav-profile" aria-selected="false">Opciones avanzadas</a>-->
                            </div>
                        </nav>

                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-principal" role="tabpanel" aria-labelledby="nav-principal-tab">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-12 col-lg-4">
                                                <div class="form-group">
                                                    <label for="category"> Categoría</label>
                                                    <div class="input-group">
                                                        <select name="category" id="category" class="form-control" required>
                                                            @if($categories->count() > 0)
                                                                @foreach($categories as $c)
                                                                    <option value="{{$c->id}}" {{$c->description == 'SIN CATEGORÍA' ? 'selected' : ''}}>{{$c->description}}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                        <div class="input-group-append">
                                                            <button class="btn btn-primary-custom btnAddCategory" type="button">
                                                                <i class="fa fa-plus"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-4">
                                                <div class="form-group">
                                                    <label for="brand"> Marca</label>
                                                    <div class="input-group">
                                                        <select name="brand" id="brand" class="form-control" required>
                                                            @if($brands->count() > 0)
                                                                @foreach($brands as $b)
                                                                    <option value="{{$b->id}}" {{$b->description == 'SIN MARCA' ? 'selected' : ''}}>{{$b->description}}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                        <div class="input-group-append">
                                                            <button class="btn btn-primary-custom btnAddBrand" type="button">
                                                                <i class="fa fa-plus"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-4">
                                                <div class="form-group">
                                                    <label for="brand"> Clasificación</label>
                                                    <div class="input-group">
                                                        <select name="classification" id="classification" class="form-control">
                                                            @if($classifications->count() > 0)
                                                                @foreach($classifications as $c)
                                                                    <option value="{{$c->id}}" {{$c->description == 'SIN CLASIFICACIÓN' ? 'selected' : ''}}>{{$c->description}}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="description"> Nombre Producto o Servicio*</label>
                                                    <input type="text" name="description" id="pdescription" class="form-control" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-lg-4">
                                                <div class="form-group">
                                                    <label for="code">Código de Barras</label>
                                                    <input type="text" name="code" id="code" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-4">
                                                <div class="form-group">
                                                    <label for="internalcode"> Código interno</label>
                                                    <input type="text" name="internalcode" id="internalcode" class="form-control" >
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-4">
                                                <div class="form-group">
                                                    <label for="equivalence_code">Código de Equivalencia</label>
                                                    <input type="text" name="equivalence_code" id="equivalence_code" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-4">
                                                <div class="form-group">
                                                    <label for="type">Tipo de Operación* </label>
                                                    <select name="type" id="type" class="form-control">
                                                        @foreach($operations_type as $operation_type)
                                                            @if($operation_type->id == 1 || $operation_type->id == 2 || $operation_type->id == 22 )
                                                                <option value="{{$operation_type->id}}" >{{$operation_type->description}}</option>
                                                            @endif
                                                            @if ($operation_type->id == 23 && auth()->user()->hasRole('admin'))
                                                                <option value="{{$operation_type->id}}" >{{$operation_type->description}}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-4">
                                                <div class="form-group">
                                                    <label for="type">Unidad de Medida Venta* </label>
                                                    <select name="measure" id="measure" class="form-control">
                                                        @foreach($measures as $measure)
                                                            <option value="{{$measure->id}}" selected="selected">{{$measure->description}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-10 col-lg-4">
                                                <div class="form-group">
                                                    <label for="">Tipo de Producto</label>
                                                    <select name="product_type" id="product_type" class="form-control" required>
                                                        <option value="">Tipo de Producto</option>
                                                        <option value="0">Activo Fijo</option>
                                                        <option value="1">Mercaderia</option>
                                                        <option value="2">Gasto</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-4">
                                                <div class="form-group">
                                                    <label for="tax">Impuesto </label>
                                                    <select name="tax" id="tax" class="form-control">
                                                        <option value="">Sin Impuesto</option>
                                                        @foreach($taxes as $tax)
                                                            <option value="{{$tax->id}}" data-value="{{ $tax->value }}">{{$tax->description}} - {{ $tax->value }} %</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-10 col-lg-8">
                                                <div class="form-group">
                                                    <label for="description"> Detalle del producto</label>
                                                    <input type="text" name="detailProduct" id="detailProduct" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-10 col-lg-4">
                                                <div class="form-group">
                                                    <label for="description">IGV *</label>
                                                    <select name="igv_type" id="igv_type" class="form-control" required>
                                                        <option value="1">Gravado</option>
                                                        <option value="9">Inafecto</option>
                                                        <option value="8">Exonerado</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-lg-4">
                                                <div class="form-group">
                                                    <label for="description"> Imagen del Producto</label>
                                                    <input type="file" name="imageProduct" id="imageProduct" class="file form-control" accept="image/*">
                                                    <input type="hidden" name="imagePath" id="imagePath" value="">
                                                </div>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <img id="preview_image" src="storage/products/default.jpg" style="width: 100px;" />
                                                        <br>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-2 col-lg-2">
                                                <label>Activo</label>
                                                <div class="ckbox">
                                                    <input type="checkbox" checked value="1" name="status" id="status"/><span></span>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <label>Es Combo</label>
                                                <div class="form-group">
                                                    <input type="checkbox" value="1" name="is_kit" id="is_kit" />
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row"></div>
                                        <div class="row">
                                            <div class="col-12 col-lg-2">
                                                <div class="form-group">
                                                    <label for="initial_stock">Stock Inicial</label>
                                                    <input type="number" min="0" class="form-control" name="initial_stock" id="initial_stock">
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-2">
                                                <div class="form-group">
                                                    <label for="initial_stock">Fecha de Stock Inicial</label>
                                                    <input type="date" class="form-control" name="intial_date" id="intial_date">
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-2">
                                                <div class="form-group">
                                                    <label for="maximum_stock">Stock máximo</label>
                                                    <input type="number" min="0" class="form-control" name="maximum_stock" id="maximum_stock">
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-2">
                                                <div class="form-group">
                                                    <label for="minimum_stock">Stock mínimo</label>
                                                    <input type="number" min="0" class="form-control" name="minimum_stock" id="minimum_stock">
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-2 quantity">
                                                <div class="form-group">
                                                    <label for="quantity">Cantidad*</label>
                                                    <input type="number" min="0" step="1" class="form-control" id="quantity" name="quantity" required>
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-2">
                                                <div class="form-group">
                                                    <label for="cost">Costo</label>
                                                    <input type="number" min="0" step="0.01" class="form-control" id="cost" name="cost">
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-2">
                                                <div class="form-group">
                                                    <label>Almacenes</label>
                                                    <select name="warehouse" id="warehouse" class="form-control">
                                                        <option value="">Seleccionar Almacén</option>
                                                        @if($warehouses->count() > 0)
                                                            @foreach($warehouses as $wh)
                                                                <option value="{{$wh->id}}">{{$wh->description}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-12 col-lg-2">
                                                <div class="form-group">
                                                    <label for="location">Ubicación</label>
                                                    <input type="text" class="form-control" id="location" name="location">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-lg-3" style="display: none;">
                                                <div class="form-group">
                                                    <label for="cost">Utilidad Unitario* (%)</label>
                                                    <input type="number" min="0" step="0.01" class="form-control" id="utility" name="utility">
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-3" style="display: none;">
                                                <div class="form-group">
                                                    <label for="price">Valor Venta Unitario*</label>
                                                    <input type="hidden" id="sale_value" name="sale_value">
                                                    <input type="number" min="0" step="0.01" class="form-control" id="price" name="price">
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-3" style="display: none;">
                                                <div class="form-group">
                                                    <label for="higher_price">Precio Por Mayor*</label>
                                                    <input type="number" min="0" step="0.01" class="form-control" id="higher_price" name="higher_price">
                                                </div>
                                            </div>

                                            <div class="col-12 col-lg-3">
                                                <div class="form-group">
                                                    <button type="button" class="btn btn-primary-custom" id="btnConfigPrices"><i class="fa fa-calculator"></i> CONFIGURAR PRECIOS</button>
                                                </div>
                                            </div>
                                        </div>
                                        <small>* Datos Obligatorios</small>
                                        <div class="row">
                                            <div class="col-12">
                                                <button type="submit" id="save" class="btn btn-secondary-custom">
                                                    <i class="fa fa-save"></i>
                                                    GRABAR DATOS
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="nav-advanced" role="tabpanel" aria-labelledby="nav-advanced-tab">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-12 col-lg-3">
                                                <div class="form-group">
                                                    <label for="type">Unidad de Medida Compra* </label>
                                                    <select name="typePurchase" id="typePurchase" class="form-control">
                                                        <option value="1" selected="selected">NIU/PRODUCTOS</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-3">
                                                <div class="form-group">
                                                    <label>Cantidad General Compra</label>
                                                    <input type="number" value="1" class="form-control" id="quantityGenCompra" name="quantityGenCompra">
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-3">
                                                <div class="form-group">
                                                    <label>Cantidad Unitaria Compra</label>
                                                    <input type="number" value="1" class="form-control" id="quantityUnitCompra" name="quantityUnitCompra">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-lg-4">
                                                <div class="form-group">
                                                    <label for="coin"> Moneda(Referencia)</label>
                                                    <select name="coin" id="coin" class="form-control">
                                                        <option value="">Sin Moneda</option>
                                                        @if($coins->count() > 0)
                                                            @foreach($coins as $c)
                                                                <option value="{{$c->id}}" {{ $c->id == 1 ? "selected" : "" }}>{{$c->description}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-4">
                                                <div class="form-group">
                                                    <label for="coin">Código Producto SUNAT</label>
                                                    <select name="code_sunat" id="code_sunat" class="form-control select3" style="width: 100%;">
                                                        <option value="">Buscar</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="mdl_add_prices" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">CONFIGURAR PRECIOS</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <form role="form" id="frm_prices" enctype="multipart/form-data">
                        <div class="card card-default">
                            <div class="card-body">
                                <div class="row">
                                    <table class="table" id="tablePrice">
                                        <thead>
                                            <tr>
                                                <th>Descripción</th>
                                                <th>Precio</th>
                                                <th>*</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                    {{-- <div class="col-12">
                                        <label><input type="checkbox" id="includeRC" name="priceIncludeRC" value="1"> ¿El precio incluye recargo al consumo?</label>
                                    </div> --}}
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-12">
                                        <button class="btn btn-secondary-custom" type="button" id="btnAddPrice"><i class="fa fa-plus"></i> AGREGAR PRECIO</button>
                                    </div>
                                </div>
                                <div class="row"><div class="col-12"><br></div></div>
                                <div class="row">
                                    <div class="col-12">
                                        <button type="button" class="btn btn-danger-custom right-bottom float-right" id="btnGoToBack">
                                            <i class="fa fa-retweet"></i>
                                            REGRESAR
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="mdl_add_brand" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">NUEVA MARCA</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <form role="form" data-toggle="validator" id="frm_brand">
                        <div class="card card-default">
                            <div class="card-header">
                                <h3 class="card-title">
                                    Registrar Marca
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-md-8">
                                        <div class="form-group">
                                            <label for="add_brand"> Marca</label>
                                            <input type="text" class="form-control" name="add_brand" id="add_brand" placeholder="Agregar Marca" required />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-12">
                                        <button type="button" id="saveBrand" class="btn btn-secondary-custom">
                                            <i class="fa fa-save"></i>
                                            GRABAR MARCA
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
        
    <div id="mdl_add_category" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">NUEVA CATEGORÍA</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <div class="card card-default">
                        <div class="card-header">
                            <h3 class="card-title">
                                Registrar Categoría
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="add_category"> Categoría</label>
                                        <input type="text" class="form-control" name="add_category" id="add_category" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-12">
                                    <button type="button" class="btn btn-secondary-custom" id="btnAddCategory">
                                        <i class="fa fa-save"></i>
                                        GRABAR CATEGORÍA
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form role="form" data-toggle="validator" id="frm_centercost">
        <div id="mdl_add_centercost" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="z-index: 9999;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" style="font-size: 1.5em !important;">NUEVO CENTRO DE COSTOS</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="font-size: 1.5em !important;">×</span></button>
                    </div>
                    <div class="modal-body">
                        <form role="form" data-toggle="validator" id="frm_category">
                            <input type="hidden" id="center_id" name="center_id">
                            <div class="card card-default">
                                <div class="card-header" style="background: #F4F5F7 !important;">
                                    <h3 class="card-title">
                                        Registrar Centro de Costos
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="description"> Código</label>
                                                <input type="text" class="form-control" name="code" id="code" required>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="description"> Centro de Costo</label>
                                                <input type="text" class="form-control" name="description" id="description" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-12">
                                            <button type="submit" id="save" class="btn btn-primary-custom">
                                                <i class="fa fa-save"></i>
                                                GRABAR DATOS
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div id="mdl_add_classification" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">NUEVA CLASIFICACIÓN</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <div class="card card-default">
                        <div class="card-header">
                            <h3 class="card-title">
                                Registrar Clasificación
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="add_classification"> Clasificación</label>
                                        <input type="text" class="form-control" name="add_classification" id="add_classification" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-12">
                                    <button type="button" class="btn btn-secondary-custom" id="btnAddClassification">
                                        <i class="fa fa-save"></i>
                                        GRABAR CLASIFICACIÓN
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script_admin')
    <script>
        var saveProductCount = 0;
        var price_lists = @json($price_lists);
        var select_price_lists = '<select class="form-control product_price_list" name="product_price_list[]">';
        $(price_lists).each(function (index, data) {
            select_price_lists += '<option value="' + data['id'] + '">' + data['description'] + '</option>';
        });

        select_price_lists += '</select>';

        $('.btnAddCategory').click(function() {
            $('#mdl_add_category').modal('show');
        });

        $('#openModalUpload').click(function() {
           $('#mdl_upload_products').modal('show');
        });

        $('#quantityUnitCompra').keyup(function() {
            let quantity = parseFloat($('#quantityGenCompra').val()).toFixed(2) * parseFloat($(this).val()).toFixed(2);
            $('#quantity').val(parseFloat(quantity).toFixed(2));
        });
        $('#quantityGenCompra').keyup(function() {
            let quantity = parseFloat($('#quantityUnitCompra').val()).toFixed(2) * parseFloat($(this).val()).toFixed(2);
            $('#quantity').val(parseFloat(quantity).toFixed(2));
        });

        $('#btnAddClassification').click(function () {
            $.post('/warehouse.classification.save',
                'description=' + $('#add_classification').val() +
                '&_token=' + '{{ csrf_token() }}', function(response) {
                    if(response == true) {
                        toastr.success('Clasificación grabada satisfactoriamente.');
                        $.get('/warehouse.classification.all', function(response) {
                            $('#add_classification').val('');
                            let option = '';
                            for (var i = 0; i < response.length; i++) {
                                option += '<option value="' + response[i].id + '">' + response[i].description + '</option>';
                            }

                            $('#classification').html('');
                            $('#classification').append(option);
                            $('#mdl_add_classification').modal('hide');
                        }, 'json');
                    } else {
                        toastr.error('Ocurrió un error inesperado.');
                    }
                }, 'json');
        });

        $('#btnAddCategory').click(function() {
            $.post('/warehouse.category.save',
                'description=' + $('#add_category').val() +
                '&_token=' + '{{ csrf_token() }}', function(response) {
                    if(response == true) {
                        toastr.success('Categoría grabada satisfactoriamente.');
                        $.get('/warehouse.category.all', function(response) {
                            $('#add_category').val('');
                            let option = '';
                            for (var i = 0; i < response.length; i++) {
                                option += '<option value="' + response[i].id + '">' + response[i].description + '</option>';
                            }

                            $('#category').html('');
                            $('#category').append(option);
                            $('#mdl_add_category').modal('hide');
                        }, 'json');
                    } else {
                        toastr.error('Ocurrió un error inesperado.');
                    }
            }, 'json');
        });

        $('#saveBrand').click(function() {
            $.post('/logistic.brand.save',
                'add_brand=' + $('#add_brand').val() +
                '&_token=' + '{{ csrf_token() }}', function(response) {
                    if(response == true) {
                        $('#add_brand').val('');
                        $.get('/logistic.brand.get', function(response) {
                            let option = '';
                            for (var i = 0; i < response.length; i++) {
                                option += '<option value="' + response[i].id + '">' + response[i].description + '</option>';
                            }

                            $('#brand').html('');
                            $('#brand').append(option);
                            $('#mdl_add_brand').modal('hide');
                        }, 'json');
                        toastr.success('Marca Grabada Satisfactoriamente');

                    } else {
                        toastr.error('Ocurrió un error inesperado');
                    }
            }, 'json');
        });

        $('body').on('keyup', '.priceListValue', function() {
            let cost = $('#cost').val();
            if ($(this).val() <= cost) {
                toastr.warning('El precio debe de ser mayor al costo.');
            }
        });

        let tbl_data = $("#tbl_data").DataTable({
            'pageLength' : 15,
            'bLengthChange' : false,
            'lengthMenu': false,
            'language': {
                'url': '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json'
            },
            "searching": false,
            'processing': false,
            'serverSide': true,
            'ajax': {
                'url': '/warehouse.dt.products',
                'type' : 'get',
                'data': function(d) {
                    d.search2 = $('#search').val();
                }
            },
            'columns': [
                {data: 'product.id'},
                {data: 'product.code'},
                {data: 'product.internalcode'},
                {data: 'product.operation_type.description'},
                {data: 'product.brand.description'},
                {data: 'product.description'},
                {data: 'stock'},
                {data: 'product.coin_product.symbol'},
                {data: 'product.product_price_list'},
                {data: 'product.cost'},
                {data: 'warehouse.description'},
                {data: 'product.status'},
                {
                    data: 'product.image',
                    orderable: false
                },
                {data: 'product.cost'},
            ],
            'columnDefs': [{
                'targets': [0],
                'orderable':false,
            }],
            'fnRowCallback': function( nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                switch (aData['product']['status']) {
                    case 1:
                        $(nRow).find('td:eq(11)').html('<span class="badge badge-success">ACTIVO</span>');
                        break;
                    case 0:
                        $(nRow).find('td:eq(11)').html('<span class="badge badge-danger">INACTIVO</span>');
                        break;
                }

                let options = '';
                options += '<button type="button" class="btn btn-rounded btn-dark dropdown-toggle" data-toggle="dropdown">';
                options += '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>';
                options += '<div class="dropdown-menu" role="menu">';
                options += '<a class="dropdown-item prepare" href="#">Editar</a>';
                options += '<a class="dropdown-item delete" href="#">Eliminar</a>';

                if(aData['product']['status'] == 1) {
                    options += '<a class="dropdown-item p_disabled" href="#"">Deshabilitar</a>';
                } else {
                    options += '<a class="dropdown-item p_enabled" href="#">Habilitar</a>';
                }

                options += '</div>';

                let button = '<div class="btn-group">';
                    button += '<button type="button" class="btn btn-rounded btn-secondary-custom dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Opc.';
                    button += '</button>';
                    button += '<div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(-56px, 33px, 0px); top: 0px; left: 0px; will-change: transform; right: 0px; width: 200px;">';
                    button += '<a class="dropdown-item prepare" href="#">Editar</a>';
                    button += '<a class="dropdown-item delete" href="#">Eliminar</a>';
                    button += '</div>';
                    button += '</div>';

                $(nRow).find('td:eq(13)').html(button);
                // $(nRow).find('td:eq(10)').html(options);
                $(nRow).find('td:eq(0)').html('<input name="check_s[]" type="checkbox" class="select" value="' + aData['product']['id'] + '" style="display: none;" />');

                if(aData['status'] === 0) {
                    $(nRow).addClass('table-danger');
                }

                let price = '';

                $.each(aData['product']['product_price_list'], function (key, value) {
                    price += value['price_list']['description'] + ': <strong>' + value['price'] + '</strong> | UTILIDAD: <strong>' + value['utility_percentage'] + '%</strong></br>';
                });

                $(nRow).find('td:eq(8)').html(price);
                $(nRow).find('td:eq(12)').html('<img class="thumbnail" src="storage/' + aData['product']['image'] + '" style="width: 100px;">');
            }
        });

        $('#search').on('keyup', function() {
            $('#tbl_data').DataTable().ajax.reload();
        });

        $('body').on('click', '.btnAddBrand', function() {
            $('#mdl_add_brand').modal('show');
        });

        $('body').on('click', '.btnAddClassification', function () {
            $('#mdl_add_classification').modal('show');
        });
        $('body').on('click', '.btnAddCenterCost', function () {
            $('#mdl_add_centercost').modal('show');
        });

        $('#frm_centercost').validator().on('submit', function(e) {
            if(e.isDefaultPrevented()) {
                toastr.warning('Debe llenar todos los campos obligatorios');
            } else {
                e.preventDefault();
                let data = $('#frm_centercost').serialize();

                $.ajax({
                    url: '/logistic.costcenter.save',
                    type: 'post',
                    data: data + '&_token=' + '{{ csrf_token() }}',
                    dataType: 'json',
                    beforeSend: function() {
                        $('#save').attr('disabled');
                    },
                    complete: function() {

                    },
                    success: function(response) {
                        if(response == true) {
                            toastr.success('Se grabó satisfactoriamente el centro de costos');
                            // clearDataCategory();
                            
                            $.post('/logistic.costcenter.get2','_token=' + '{{ csrf_token() }}', function(response) {
                                console.log(response);
                                let option = '';
                                for (var i = 0; i < response.length; i++) {
                                    option += '<option value="' + response[i].id + '">' + response[i].center + '</option>';
                                }

                                $('#centerCost').html('');
                                $('#centerCost').append(option);
                                $('#mdl_add_centercost').modal('hide');
                            }, 'json');
                        } else {
                            console.log(response.responseText);
                            toastr.error('Ocurrio un error');
                        }
                    },
                    error: function(response) {
                        console.log(response.responseText);
                        toastr.error('Ocurrio un error');
                        $('#save').removeAttr('disabled');
                    }
                });
            }
        });

        $('#tbl_data').on('click', '.delete', function() {
            let data = tbl_data.row( $(this).parents('tr') ).data();
            if(data == undefined) {
                tbl_data = $('#tbl_data').DataTable();
                data = tbl_data.row( $(this).parents('tr') ).data();
            }

            $('#product_id').val(data['product']['id']);

            $.confirm({
                icon: 'fa fa-question',
                theme: 'modern',
                animation: 'scale',
                title: '¿Está seguro de eliminar este producto?',
                content: '<div>Si elimina este producto, el stock de este producto será eliminado</div>',
                buttons: {
                    Confirmar: function () {
                        $.ajax({
                            type: 'get',
                            url: '/warehouse.product.delete',
                            data: {
                                _token: '{{ csrf_token() }}',
                                product_id: $('#product_id').val()
                            },
                            dataType: 'json',
                            beforeSend: function() {

                            },
                            complete: function() {

                            },
                            success: function(response) {
                                if(response == true) {
                                    toastr.success('Se eliminó satisfactoriamente el producto');
                                    $("#tbl_data").DataTable().ajax.reload();
                                } else if(response == -2) {
                                    toastr.error('Uno o más productos no alcanzan el stock suficiente');
                                } else {
                                    console.log(response.responseText);
                                    toastr.error('Ocurrio un error');
                                }

                            },
                            error: function(response) {
                                console.log(response.responseText);
                                toastr.error('Ocurrio un error');
                            }
                        });
                    },
                    Cancelar: function () {

                    }
                }
            });
        });

        $('#tbl_data').on('click', '.delete_selection', function(){
            let data = $('#frm_table').serialize();
            $.confirm({
                icon: 'fa fa-question',
                theme: 'modern',
                animation: 'scale',
                title: '¿Está seguro de eliminar los productos seleccionados?',
                content: '<div>Si elimina los productos, el stock de los productos será eliminado también</div>',
                buttons: {
                    Confirmar: function () {
                        $.ajax({
                            type: 'POST',
                            url: '/warehouse.products.delete',
                            data: '_token=' + '{{csrf_token()}}' + '&' + data,
                            dataType: 'json',
                            beforeSend: function() {

                            },
                            complete: function() {

                            },
                            success: function (response) {
                                if(response == true) {
                                    toastr.success('Se eliminaron satisfactoriamente los productos');
                                    $("#tbl_data").DataTable().ajax.reload();
                                } else if(response == -2) {
                                    toastr.error('Uno o más productos no alcanzan el stock suficiente');
                                } else {
                                    console.log(response.responseText);
                                    toastr.error('Ocurrio un error');
                                }
                            },
                            error: function(response) {
                                console.log(response.responseText);
                                toastr.error('Ocurrio un error');
                            }

                        });
                    },
                    Cancelar: function () {

                    }
                }
            });
        });

        $('#tbl_data').on( 'click', '.prepare', function () {
            $('#warehouse').hide();
            var data = tbl_data.row( $(this).parents('tr') ).data();
            if(data == undefined) {
                tbl_data = $("#tbl_data").DataTable();
                data = tbl_data.row( $(this).parents('tr') ).data();
            }
            $("#product_id").val(data['product']['id']);
            $.get('/warehouse.product.prepare',
                'product_id=' + data['product']['id'] +
                '&_token=' + '{{ csrf_token() }}', function(response) {
                    $('#save').removeClass('disabled');
                    $('#pdescription').val(response['description']);
                    $('#category').val(response['category_id']);
                    $('#brand').val(response['brand_id']);
                    $('#type').val(response['operation_type']);
                    $('#code').val(response['code']);
                    $('#internalcode').val(response['internalcode']);
                    $('#coin').val(response['coin_id']);
                    $('#price').val(response['stock']['price']);
                    $('#higher_price').val(response['stock']['higher_price']);
                    $('#warehouse').val(response['stock']['warehouse_id']);
                    $('#cost').val(response['cost']);
                    $('#status').val(response['status']);
                    $('#mdl_add_product').modal('show');
                    $('#product_id').val(response['id']);
                    $('#quantity').val(response['stock']['stock']);
                    $('#utility').val(response['utility']);
                    $('#typePurchase').val(response['operation_type_purchase']);
                    $('#quantityUnitCompra').val(response['quantity_unit_purchase']);
                    $('#quantityGenCompra').val(response['quantity_purchase']);
                    if(response['exonerated'] == 1) {
                        $('#exonerated').attr('checked', true);
                    } else {
                        $('#exonerated').attr('checked', false);
                    }
                    if(response['is_kit'] == 1) {
                        $('#is_kit').attr('checked', true);
                    } else {
                        $('#is_kit').attr('checked', false);
                    }
                    $('#classification').val(response['classification_id']);
                    $('#external_code').val(response['external_code']);
                    $('#equivalence_code').val(response['equivalence_code']);
                    $('#detailProduct').val(response['detail']);
                    $('#maximum_stock').val(response['stock']['maximum_stock']);
                    $('#minimum_stock').val(response['stock']['minimum_stock']);
                    $('#location').val(response['stock']['location']);
                    $('#preview_image').attr('src', 'storage/' + response['image']);

                    $('#initial_stock').val(response['initial_stock'])
                    $('#intial_date').val(response['initial_date'])
                    $('#initial_stock').attr('readonly', true)
                    $('#intial_date').attr('readonly', true)
                    $('#quantity').attr('readonly', true);

                    $('#tax').val(response['tax_id']);

                    $('#product_type').val(response['type_product']);
                    $('#igv_type').val(response['type_igv_id']);
                    $('#measure').val(response['measure_id']);

                    let tr = '';

                    for(let x = 0; x < response['product_price_list'].length; x++) {
                        tr += '<tr>';
                            tr += '<td><select class="form-control product_price_list" name="product_price_list[]">';
                            tr +=  '<option value="' + response['product_price_list'][x]['price_list']['id'] + '">' + response['product_price_list'][x]['price_list']['description'] + '</option></select></td>';
                            tr += '<input type="hidden" class="form-control pricePercentage" name="pricePercentage[]" value="' + response['product_price_list'][x]['utility_percentage'] + '" />';
                            tr += '<td><input type="text" class="form-control priceListValue" name="priceListValue[]" value="' + response['product_price_list'][x]['price'] + '" /></td>';
                            tr += '<td><button class="btn btn-danger-custom deletePrice" type="button"><i class="fa fa-trash"></i></button></td>';
                        tr += '</tr>';
                    }

                    $('#tablePrice tbody').html(tr);
                }, 'json');
            $("html, body").animate({ scrollTop: 0 }, 600);
        });

        $('#type').change(function() {
            if($(this).val() == 2) {
                $('#initial_stock').attr('readonly', true)
                $('#intial_date').attr('readonly', true)
                $('#quantity').attr('readonly', true);
            } else {
                $('#initial_stock').attr('readonly', false)
                $('#intial_date').attr('readonly', false)
            }
        });


        //Accion para desahabilitar un registro producto
        $('#tbl_data').on('click','.p_disabled' ,function () {
            let datos = tbl_data.row( $(this).parents('tr') ).data();
            $('#product_id').val(datos['id']);
            let data = $('#frm_product').serialize();
            let status_change = '&status_change='+0;

            $.confirm({
                icon: 'fa fa-question',
                theme: 'modern',
                animation: 'scale',
                title: '¿Está seguro que desea inhabilitar este producto?',
                content: '<div>Si desactiva este producto no podrá utilizarlo</div>',
                buttons:{
                    Confirmar: function () {
                        $.ajax({
                            url : '/warehouse.product.status.update',
                            type: 'post',
                            data: data + '&_token=' + '{{ csrf_token() }}' + status_change,
                            dataType: 'json',
                            beforeSend: function () {},
                            complete: function () {},
                            success: function (response) {
                                if(response == true){
                                    toastr.success('Se cambio el estado de los productos satisfactoriamente.');
                                    $('#tbl_data').DataTable().ajax.reload();
                                }else{
                                    console.log(response.responseText);
                                    toastr.error('Ocurrio un error');
                                }
                            },
                            error: function (response) {
                                console.log(response.responseText);
                                toastr.error('Ocurrio un error');
                            }
                        });
                    },
                    Cancelar: function () {

                    }
                }
            });

        });

        $('#tbl_data').on('click','.disabled_selection' ,function (){
            let data = $('#frm_table').serialize();
            let status_change = '&status_change='+0;
            $.confirm({
                icon: 'fa fa-question',
                theme: 'modern',
                animation: 'scale',
                title: '¿Está seguro que desea inhabilitar los productos seleccionados?',
                content: '<div>Si desactiva los productos no podrá utilizarlos</div>',
                buttons:{
                    Confirmar: function () {
                        $.ajax({
                            type    : 'POST',
                            url     : '/warehouse.products.status.update',
                            data    : data + '&_token=' + '{{ csrf_token() }}' + status_change,
                            dataType: 'json',
                            beforeSend: function () {},
                            complete: function () {},
                            success: function (response) {
                                if(response == true){
                                    toastr.success('Se cambio el estado de los productos satisfactoriamente.');
                                    $('#tbl_data').DataTable().ajax.reload();
                                }else{
                                    console.log(response.responseText);
                                    toastr.error('Ocurrio un error');
                                }
                            },
                            error: function (response) {
                                console.log(response.responseText);
                                toastr.error('Ocurrio un error');
                            }
                        });
                    },
                    Cancelar: function () {

                    }
                }
            });

        });

        //Accion para hablitar un registro producto
        $('#tbl_data').on('click','.p_enabled' ,function () {
            let datos = tbl_data.row( $(this).parents('tr') ).data();
            $('#product_id').val(datos['id']);
            let data = $('#frm_product').serialize();
            let status_change = '&status_change='+1;

            $.confirm({
                icon: 'fa fa-question',
                theme: 'modern',
                animation: 'scale',
                title: '¿Está seguro que desea inhabilitar este producto?',
                content: '<div>Si desactiva este producto no podrá utilizarlo?</div>',
                buttons:{
                    Confirmar: function () {
                        $.ajax({
                            url : '/warehouse.product.status.update',
                            type: 'post',
                            data: data + '&_token=' + '{{ csrf_token() }}' + status_change,
                            dataType: 'json',
                            beforeSend: function () {},
                            complete: function () {},
                            success: function (response) {
                                if(response == true){
                                    toastr.success('Se cambio el estado de los productos satisfactoriamente.');
                                    $('#tbl_data').DataTable().ajax.reload();
                                }else{
                                    console.log(response.responseText);
                                    toastr.error('Ocurrio un error');
                                }
                            },
                            error: function (response) {
                                console.log(response.responseText);
                                toastr.error('Ocurrio un error');
                            }
                        });
                    },
                    Cancelar: function () {

                    }
                }
            });

        });

        $('#tbl_data').on('click','.enabled_selection' ,function (){
            let data = $('#frm_table').serialize();
            let status_change = '&status_change='+1;
            $.ajax({
                type    : 'POST',
                url     : '/warehouse.products.status.update',
                data    : data + '&_token=' + '{{ csrf_token() }}' + status_change,
                dataType: 'json',
                success :function (response) {
                    if(response == true){
                        toastr.success('Se cambio el estado de los productos satisfactoriamente.');
                        $('#tbl_data').DataTable().ajax.reload();
                    }else{
                        toastr.error('Ocurrio un error insesperado');
                    }
                }
            });
        });

        $('#frm_product').validator().on('submit', function(e) {
            saveProductCount = saveProductCount + 1;
            if(e.isDefaultPrevented()) {
                toastr.warning('Debe llenar todos los campos obligatorios');
                saveProductCount = 0;
            } else {
                if (saveProductCount == 1) {
                    if($('.product_price_list').length > 0) {
                        e.preventDefault();

                        let prices = [];
                        let price_duplicate = false;
                        $('.product_price_list').each(function (index, element) {
                            prices.push($(element).val());
                        });

                        let originalLength = prices.length;

                        var uniqueArray = prices.filter(function(value, index, self) {
                            return self.indexOf(value) === index;
                        });

                        if (uniqueArray < originalLength) {
                            price_duplicate = true;
                        }

                        if(price_duplicate === true) {
                            toastr.error('No puedes seleccionar el mismo precio más de 1 vez');
                            saveProductCount = 0;
                            return;
                        }

                        //let data = new FormData($('#frm_product, #frm_prices').serialize());

                        let data = new FormData($('#frm_product')[0]);
                        let data2 = $('#frm_prices').serializeArray();
                        data2.forEach(function (fields) {
                            data.append(fields.name, fields.value);
                        });

                        $.ajax({
                            url: '/warehouse.product.save',
                            type: 'post',
                            data: data,
                            dataType: 'json',
                            contentType: false,
                            processData: false,
                            beforeSend: function() {
                                $('#save').attr('disabled');
                            },
                            complete: function() {

                            },
                            success: function(response) {
                                if(response == true) {
                                    toastr.success('Se grabó satisfactoriamente el producto');
                                    $("#tbl_data").DataTable().ajax.reload();
                                    clearDataProduct();
                                    saveProductCount = 0;
                                    $('#mdl_add_product').modal('hide');
                                } else {
                                    console.log(response.responseText);
                                    saveProductCount = 0;
                                    toastr.error('Ocurrio un error');
                                }
                            },
                            error: function(response) {
                                console.log(response.responseText);
                                toastr.error('Ocurrio un error');
                                saveProductCount = 0;
                                $('#save').removeAttr('disabled');
                            }
                        });
                    } else {
                        saveProductCount = 0;
                        toastr.warning('Debe registrar al menos un precio');
                        e.preventDefault();
                    }
                }
            }
        });

        let isNewProduct = 0;
        $('#quantity').attr('readonly', true);
        $('#openModalProduct').on('click', function() {
            $('#warehouse').show();
            $('#initial_stock').attr('readonly', false)
            $('#intial_date').attr('readonly', false)
            $('#mdl_add_product').modal('show');
            isNewProduct = 1;
            clearDataProduct();
        });

        $('#initial_stock').keyup(function() {
            $('#quantity').val($(this).val());
        });

        function clearDataProduct() {
            $('#measure').val('');
            $('#code').val('');
            $('#internalcode').val('');
            $('#pdescription').val('');
            $('#product_id').val('');
            $('#cost').val(0);
            $('#price').val(0);
            $('#higher_price').val(0);
            $('#warehouse').val('');
            $('#type').val(1);
            $('#utility').val(0);
            $('#exonerated').attr('checked', false);
            $('#typePurchase').val(1);
            $('#quantityUnitCompra').val(1);
            $('#quantityGenCompra').val(1);
            $('#classification').val(1);
            $('#category').val(1);
            $('#brand').val(1);
            $('#external_code').val('');
            $('#equivalence_code').val('');
            $('#detailProduct').val('');
            $('#preview_image').attr('src', 'storage/products/default.jpg')
            $('#product_type').val('');
            $('#igv_type').val(1);
            $('#tablePrice tbody').html('');
            $('#initial_stock').val('')
            $('#intial_date').val('')
            $('#quantity').val('');
            $('#maximum_stock').val('');
            var $el = $('#imageProduct');
            $el.wrap('<form>').closest('form').get(0).reset();
            $el.unwrap();
        }

        $('.select2').select2();

        $('#type').change(function() {
            if($(this).val() != 2) {
                $('.quantity').show(100);
                $('#initial_stock').attr('readonly', false);
                $('#maximum_stock').attr('readonly', false);
                $('#minimum_stock').attr('readonly', false);
                $('#warehouse').attr('readonly', false);
                $('#location').attr('readonly', false);
                $('#quantity').attr('required');
            } else {
                $('#quantity').val('');
                $('.quantity').hide(100);
                $('#initial_stock').attr('readonly', true);
                $('#maximum_stock').attr('readonly', true);
                $('#minimum_stock').attr('readonly', true);
                $('#warehouse').attr('readonly', true);
                $('#location').attr('readonly', true);
                $('#quantity').removeAttr('required');
                $('#quantity').val(0);
                $('#initial_stock').val(0);
                $('#maximum_stock').val(0);
                $('#minimum_stock').val(0);
            }
        });

        $('#check_all').change(function () {
            $('.select').prop("checked",$(this).prop("checked"));
        });

        $('#utility').keyup(function() {
            let cost = $('#cost').val();
            if (cost == '') {
                cost = 0;
            }
            let utility = parseFloat($(this).val() / 100).toFixed(2);
            let percent = parseFloat(parseFloat(cost) * utility).toFixed(2);
            let price = (cost + percent).toFixed(2);
            $('#price').val(price);
        });

        $('#price').keyup(function() {
            let price = $('#price').val() * 1;
            let cost = $('#cost').val();
            if (cost == '') {
                cost = 0;
            }
            let percent = price - cost;
            let utility = ((percent * 100) / cost).toFixed(2);

            $('#utility').val(utility);
        });

        $('#btnConfigPrices').click(function () {
            $('#mdl_add_prices').modal('show');
        });

        $('#btnAddPrice').click(function () {
            let tr = '';
            tr += '<tr>';
                tr += '<td>' + select_price_lists + '</td>';
                tr += '<input type="hidden" class="form-control pricePercentage" name="pricePercentage[]"  value="0.00"/>';
            tr += '<td><input type="text" class="form-control priceListValue" name="priceListValue[]" /></td>';
                tr += '<td><button class="btn btn-danger-custom deletePriceList" type="button"><i class="fa fa-trash"></i></button></td>';
            tr += '</tr>';

            $('#tablePrice tbody').append(tr);
        });

        $('body').on('click', '.deletePriceList', function () {
            $(this).parent().parent().remove();
        });

        $('#btnGoToBack').click(function () {
            $('#mdl_add_prices').modal('hide');
        });

        $('body').on('click', '.deletePrice', function () {
            let tr = $(this).parent().parent();
            let id = tr.find('.product_price_list').val();
            $.ajax({
                url: '/product/price/list/delete',
                type: 'post',
                dataType: 'json',
                data: {
                    id: id,
                    _token: '{{csrf_token()}}'
                },
                success: function (response) {
                    if(response === true) {
                        toastr.success('El precio se eliminó!');
                        tr.remove();
                    } else {
                        toastr.error('Ocurrión un error');
                    }
                }
            });
        });

        $(document).on('hidden.bs.modal', function (event) {
            if ($('.modal:visible').length) {
                $('body').addClass('modal-open');
            }
        });

        function readURL(input) {
            let url = 'storage/products/default.jpg';
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    url = e.target.result;
                    $('#preview_image').attr('src', url);
                }

                reader.readAsDataURL(input.files[0]); // convert to base64 string
            }
        }

        $('#imageProduct').change(function () {
            readURL(this);
        });

        $('body').on('keyup', '.priceListValue', function () {
            let tr = $(this).parent().parent();
            calculateUtilityPriceList(tr);
        });

        $('body').on('keyup', '.pricePercentage', function () {
            let tr = $(this).parent().parent();
            calculatePercentPriceList(tr);
        });

        // $('#cost').on('keyup', function () {
        //     $('.product_price_list').each(function (i, obj) {
        //         calculatePercentPriceList($(this).parent().parent());
        //     });
        // });

        function calculatePercentPriceList(tr) {
            let priceListUtility = tr.find('.pricePercentage').val();
            let cost = $('#cost').val();
            if (cost == '') {
                cost = 0;
            }
            let final_price = (cost * 1) + ((cost * priceListUtility) / 100);
            tr.find('.priceListValue').val(Math.round(final_price * 100) / 100);
        }

        function calculateUtilityPriceList(tr) {
            let priceListUtility = tr.find('.priceListValue').val();
            let cost = $('#cost').val();
            if (cost == '') {
                cost = 0;
            }
            let utility = 0;
            let difference = parseFloat(priceListUtility) - parseFloat(cost);
            if (cost == 0) {
                utility = parseFloat(difference);
            } else {
                utility = (parseFloat(difference) * 100) / parseFloat(cost);
            }

            console.log(cost, difference, utility, priceListUtility);
            tr.find('.pricePercentage').val(utility.toFixed(2));
        }
    </script>
@stop
