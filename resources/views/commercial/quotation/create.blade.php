@extends('layouts.azia')
@section('css')
    <style>
        #tbl_products tbody tr td { padding-left: 0;padding-right: 0;}
        .select2-container{
            width: 80%!important;
            max-width: 80%!important;
            min-width: 80%!important;
        }
        #select2-c_product-container {
            min-width: 350px;
            max-width: 350px;
        }
    </style>
@endsection
@section('content')
    <form method="post" role="form" data-toggle="validator" id="frm_quotation">
        <input type="hidden" value="0" id="quotation_id" />
        <input type="hidden" value="{{$igv->value}}" id="igv" />
        <input type="hidden" value="{{$clientInfo->price_type}}" id="pt" />
        <input type="hidden" value="{{$clientInfo->less_employees}}" id="le" />
        <input type="hidden" value="{{$clientInfo->consumption_tax_plastic_bags_price}}" id="pbp" />
        <div class="row">
            <div class="col-12">
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="text-center">
                            <b>NUEVA COTIZACIÓN</b>
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-5">
                                <div class="form-group">
                                    <label for="customer"> </label>
                                    <div class="input-group">
                                        <select name="customer" id="customer" class="form-control" style="width: 80%;" required>
                                            @if($customers->count() > 0)
                                                @foreach($customers as $c)
                                                    <option value="{{$c->id}}" c-email="{{$c->email}}">
                                                        {{$c->document}} - {{$c->description}}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <div class="input-group-append" id="openCustomer" style="cursor: pointer;">
                                            <button type="button" class="btn btn-primary-custom">NUEVO</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-2">
                                <div class="form-group">
                                    <label for="date">Fecha de Emisión</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                        </div>
                                        <input value="{{$currentDate}}" type="text" class="form-control datepicker" name="date" id="date" autocomplete="off" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-1">
                                <label for="coin">Moneda</label>
                                <select name="coin" id="coin" class="form-control" required>
                                    @if($coins->count() > 0)
                                        @foreach($coins as $c)
                                            <option value="{{$c->id}}">{{$c->symbol}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-6 col-md-2">
                                <label for="exchange_rate">Tipo cambio</label>
                                <div class="form-group">
                                    <input type="text" class="form-control" name="change_type" id="exchange_rate">
                                </div>
                            </div>
                            <div class="col-12 col-md-2">
                                <div class="form-group">
                                    <label for="expiration">Fecha Vencimiento</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                        </div>
                                        <input type="text" class="form-control datepicker" name="expiration" id="expiration" autocomplete="off" required value="{{$currentDateLast}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label for="condition">Condición de pago</label>
                                    <select name="condition" id="condition" class="form-control">
                                        <option value="EFECTIVO" data-days="0">EFECTIVO</option>
                                        <option value="CREDITO">CREDITO</option>
                                        <option value="TARJETA DE CREDITO" data-days="0">TARJETA DE CREDITO</option>
                                        <option value="TARJETA DE DEBITO" data-days="0">TARJETA DE DEBITO</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-1">
                                <label for="exchange_rate"></label>
                                <div class="form-group">
                                    <button type="button" id="showPayments" class="btn btn-primary-custom btn-sm"><i class="fa fa-eye"></i></button>
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label for="order">O/C</label>
                                    <input type="text" readonly class="form-control" name="order" id="order" />
                                </div>
                            </div>
                        </div>

                        <fieldset>
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table" id="tbl_products" style="width: 100%;">
                                            <thead>
                                            <th width="400px">Producto/Servicios</th>
                                            <th width="200px">Detalle</th>
                                            <th width="50px">Cantidad</th>
                                            <th width="85px">Precio</th>
                                            <th width="85px">SubTotal</th>
                                            <th width="85px">Total</th>
                                            <th width="10px">*</th>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                {{-- {{ dd($products) }} --}}
                                                <td>
                                                    <div class="input-group" style="width: 100%;">
                                                        <select style="width: 80%;" class="form-control select_2 c_product" id="c_product" name="cd_product[]" required>
                                                            <option value="">Seleccionar Producto</option>
                                                            @if($products->count() > 0)
                                                                @foreach($products as $p)
                                                                    <option value="{{$p->id}}" p-it="{{ $p->priceIncludeRC }}" p-taxbase="{{ $p->taxbase }}" p-tax="{{ $p->tax }}" p-type_product="{{$p->type_product}}" p-stock="{{$p->stock}}" p-igv-type="{{ $p->type_igv_id }}" p-price="{{$p->price}}" p-otype='{{ $p->operation_type }}' p-exonerated="{{ $p->exonerated }}">{{ $p->internalcode }} - {{$p->description}}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
{{--                                                             <div class="input-group-append">--}}
                                                        <div class="input-group-append pl-4">
                                                            <label><input type="checkbox" class="is-free"> Es Bonificación</label>
                                                            <input type="hidden" name="is_free[]" class="is_free_c" value="0">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control c_detail" name="cd_detail[]" />
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control c_quantity" step="0.01" min="1" name="cd_quantity[]" value="1" max="99999"/>
                                                </td>
                                                <td>
                                                    {{-- <input type="number" step="0.01"  class="form-control c_price" name="cd_price[]" value="0"/> --}}
                                                    <select name="cd_price[]" class="form-control c_price"></select>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control c_subtotal" name="cd_subtotal[]" value="0" readonly/>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control c_total" name="cd_total[]" value="0" readonly/>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger-custom btn-rounded remove"><i class="fa fa-close"></i></button>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button type="button" class="btn btn-primary-custom" id="btnAddProduct">
                                        <i class="fa fa-plus-circle"></i>
                                        Agregar Producto
                                    </button>
                                </div>
                            </div>
                        </fieldset>

                        <div class="row">
                            <div class="col-12"><br></div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="row">
                                    <div class="col-12">
                                        <label for="observation">Observaciones:</label>
                                        <div class="form-group">
                                            <textarea name="observation" id="observation" cols="30" rows="4" class="form-control"></textarea>
                                            <input type="text" name="t_detraction" id="t_detraction" class="form-control" readonly>
                                        </div>
                                    </div>
                                    {{-- @if ($clientInfo->detraction != null) --}}
                                    <div class="col-12 col-md-3">
                                        <label></label>
                                        <label class="ckbox">
                                            <input type="checkbox" value="1" name="detraction" id="detraction"><span>¿Detracción?</span>
                                        </label>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label></label>
                                        <label class="ckbox">
                                            <input type="checkbox" value="1" name="ordernote" id="ordernote"><span>Nota de Pedido</span>
                                        </label>
                                    </div>
                                    
                                    {{-- @endif --}}
                                    @if ($clientInfo->jungle_region_goods == '1')
                                        <div class="col-12 col-md-4">
                                            <label class="ckbox">
                                                <input type="checkbox" value="1" name="product_region" id="product_region"><span>¿Bienes Región Selva?</span>
                                            </label>
                                        </div>
                                    @endif
                                    @if ($clientInfo->jungle_region_services == '1')
                                        <div class="col-12 col-md-5">
                                            <div class="ckbox">
                                                <input type="checkbox" value="1" name="service_region" id="service_region"><span>¿Servicios Región Selva?</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                {{-- <div class="row">
                                    <div class="col-6 text-right">
                                        <label>% Descuento Global</label>
                                    </div>
                                    <div class="col-6">
                                        <input name="c_percentage" id="c_percentage" type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6 text-right">
                                        <label>Descuento Total (-)</label>
                                    </div>
                                    <div class="col-6 text-right">
                                        <input name="c_discount" id="c_discount" type="text" class="form-control" readonly value="0.00">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6 text-right">
                                        <label>Anticipo (-)</label>
                                    </div>
                                    <div class="col-6">
                                        <input name="c_discountItem" type="text" class="form-control" readonly value="0.00">
                                    </div>
                                </div> --}}
                                <input name="c_percentage" type="hidden" value="0">
                                <input name="c_discount" type="hidden" value="0">
                                <div class="row">
                                    <div class="col-6 text-right">
                                        <label>Exonerada</label>
                                    </div>
                                    <div class="col-6">
                                        <input name="c_exonerated" id="exonerated" type="text" class="form-control" readonly value="0.00">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6 text-right">
                                        <label>Inafecta</label>
                                    </div>
                                    <div class="col-6">
                                        <input name="c_unaffected" id="c_unaffected" type="text" class="form-control" readonly value="0.00">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6 text-right">
                                        <label>Gravada</label>
                                    </div>
                                    <div class="col-6">
                                        <input name="c_taxed" id="c_taxed" type="text" class="form-control" readonly value="0.00">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6 text-right">
                                        <label>IGV ({{ $igv->value }} %)</label>
                                    </div>
                                    <div class="col-6 text-right">
                                        <input name="c_igv" id="c_igv" type="text" class="form-control" readonly value="0.00">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6 text-right">
                                        <label>Gratuita</label>
                                    </div>
                                    <div class="col-6 text-right">
                                        <input name="c_free" id="c_free" type="text" class="form-control" readonly value="0.00">
                                    </div>
                                </div>
                                @if ($clientInfo->consumption_tax_plastic_bags == 1)
                                    <div class="row">
                                        <div class="col-6 text-right">
                                            <label>ICBPER</label>
                                        </div>
                                        <div class="col-6 text-right">
                                            <input name="c_t" id="c_t" type="text" class="form-control" value="00.00" readonly>
                                        </div>
                                    </div>
                                @else
                                    <input id="c_t" type="hidden" class="form-control" value="0.00">
                                @endif
                                <div class="row">
                                    <div class="col-6 text-right">
                                        <label>RECARGO AL CONSUMO</label>
                                    </div>
                                    <div class="col-6 text-right">
                                        <input name="recharge" id="recharge" type="text" class="form-control" value="0.00" readonly>
                                        <input name="recharge_value" id="recharge_value" type="hidden" value="{{ $clientInfo->automatic_consumption_surcharge_price }}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6 text-right">
                                        <label>Total</label>
                                    </div>
                                    <div class="col-6 text-right">
                                        <input name="c_total" id="c_total" type="text" class="form-control" readonly value="0.00">
                                        <input name="estateQuotation" id="estateQuotation" type="hidden" class="form-control" readonly value="">
                                        @if($bankInfo)
                                            <input type="hidden" id="detractionAccount" value="{{ $bankInfo->number }}">
                                            <input type="hidden" id="detractionBank" value="{{ $bankInfo->bank_name }}">
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group" id="buttonSaves">
                                @if($correlative != null)
                                    <!-- <button id="saveQuo" type="submit" class="btn btn-primary-custom-custom btn-block">GRABAR COTIZACIÓN</button> -->
                                        <button id="savePad" type="submit" class="btn btn-primary-custom btn-block">GRABAR COTIZACIÓN</button>
                                    @else
                                        <div class="alert alert-danger alert-dismissible">
                                            <h5><i class="fa fa-ban"></i> Alerta</h5>

                                            PRIMERO DEBE CONFIGURAR UN CORRELATIVO PARA LAS COTIZACIONES
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="creditMdl" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLabel">Cuotas</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Monto Pendiente de Pago</label>
                                    <input type="text" class="form-control form-gray" name="amountPending" id="amountPendingModal" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label>Frecuencia de Vencimiento</label>
                                    <input type="number" step="1" min="1" class="form-control" value="7" name="dueFrecuency" id="dueFrecuency">
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label>Número de Cuota</label>
                                    <input type="text" class="form-control" name="numberDues" value="1" id="numberDues">
                                </div>
                            </div>
                            
                        </div>
                        <div id="payments_lists">
                            
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="button" type="button" id="generateDues" class="btn btn-primary">Generar</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <form id="frm_cliente" method="post" class="form-horizontal" role="form" data-toggle="validator">
        <input type="hidden" id="validado" value="0">
        <div id="mdl_add_cliente" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Agregar Cliente</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="card content-overlay">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="typedocument"> Tipo Documento *</label>
                                            <select name="typedocument" id="typedocument" class="form-control" data-error="Este campo no puede estar vacío" required>
                                                <option value="">Seleccionar</option>
                                                @if($typedocuments->count() > 0)
                                                    @foreach($typedocuments as $td)
                                                        <option value="{{$td->id}}">{{$td->description}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="document">Documento *</label>
                                            <input type="text" id="document" name="document" class="form-control" placeholder="Ingresar Documento" required data-error="Este campo no puede estar vacío">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="description">Cliente *</label>
                                            <input id="description" name="description" type="text" class="form-control" required data-error="Este campo no puede estar vacío">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="code">Código de Cliente *</label>
                                            <input id="code" name="code" type="text" class="form-control" required data-error="Este campo no puede estar vacío">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="phone">Teléfono</label>
                                            <input id="phone" name="phone" type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="address">Dirección</label>
                                            <input id="address" name="address" type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="email">Correo Principal</label>
                                            <input id="email" name="email" type="email" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="email">Correo Opcional</label>
                                            <input id="emailOptional" name="emailOptional" type="email" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="detraction">Cuenta de Detracción</label>
                                            <input id="detractionclient" name="detraction" type="text" class="form-control" id="detraction">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="detraction">Contacto</label>
                                            <input id="contact" name="contact" type="text" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <button id="btnGrabarCliente" type="submit" class="btn btn-primary-custom pull-right"> CREAR CLIENTE</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

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
                                                        <select name="category" id="category" class="form-control">
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
                                                        <select name="brand" id="brand" class="form-control">
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
                                                        <div class="input-group-append">
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-4">
                                                <div class="form-group">
                                                    <label for="brand"> Centro de Costos</label>
                                                    <div class="input-group">
                                                        <select name="centerCost" id="centerCost" class="form-control">
                                                            <option value="">Seleccione un Centro de Costo</option>
                                                            @if($costsCenters->count() > 0)
                                                                @foreach($costsCenters as $c)
                                                                    <option value="{{$c->id}}">{{ $c->code }} - {{$c->center}}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                        <div class="input-group-append">
                                                            <button class="btn btn-primary-custom btnAddCenterCost" type="button">
                                                                <i class="fa fa-plus"></i>
                                                            </button>
                                                        </div>
                                                    </div>
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
                                                    <label for="external_code"> Código Externo</label>
                                                    <input type="text" name="external_code" id="external_code" class="form-control" >
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
                                                    <label for="type">Unidad de Medida Venta* </label>
                                                    <select name="type" id="type" class="form-control">
                                                        @foreach($operations_type as $operation_type)
                                                            <option value="{{$operation_type->id}}" selected="selected">{{$operation_type->description}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-10 col-lg-4">
                                                <div class="form-group">
                                                    <label for="">Tipo de Producto</label>
                                                    <select name="product_type" id="product_type" class="form-control" required>
                                                        <option value="">Tipo de Producto</option>
                                                        <option value="0">Equipamiento</option>
                                                        <option value="1">Inventario</option>
                                                        <option value="2">Servicio</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-4">
                                                <div class="form-group">
                                                    <label for="tax">Impuesto </label>
                                                    <select name="tax" id="tax" class="form-control">
                                                        <option value="">Sin Impuesto</option>
                                                        @foreach($taxes as $tax)
                                                            <option value="{{$tax->id}}">{{$tax->description}} - {{ $tax->value }} %</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-4">
                                                <div class="form-group">
                                                    <label for="description"> Nombre del Producto o Servicio*</label>
                                                    <input type="text" name="description" id="pdescription" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="col-10 col-lg-4">
                                                <div class="form-group">
                                                    <label for="description"> Detalle del producto</label>
                                                    <input type="text" name="detailProduct" id="detailProduct" class="form-control">
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
                                        </div>
                                        <div class="row"></div>
                                        <div class="row">
                                            <div class="col-12">
                                                @if ($clientInfo->price_type == 1)
                                                    <div class="alert alert-warning">
                                                        <h4><i class="icon fa fa-warning"></i> Nota</h4>
                                                        Tus Productos y Servicios están configurados en MODO VALOR PRECIO UNITARIO (costos y ventas incluyen IGV).
                                                    </div>
                                                @else
                                                    <div class="alert alert-success">
                                                        <h4><i class="icon fa fa-warning"></i> Nota</h4>
                                                        Tus Productos y Servicios están configurados en MODO VALOR VENTA UNITARIO (costos y ventas no incluyen IGV).
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-lg-2">
                                                <div class="form-group">
                                                    <label for="initial_stock">Stock Inicial</label>
                                                    <input type="text" class="form-control" name="initial_stock" id="initial_stock">
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-2">
                                                <div class="form-group">
                                                    <label for="initial_stock">Fecha de Inicio Inicial</label>
                                                    <input type="date" class="form-control" name="intial_date" id="intial_date">
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-2">
                                                <div class="form-group">
                                                    <label for="maximum_stock">Stock máximo</label>
                                                    <input type="text" class="form-control" name="maximum_stock" id="maximum_stock">
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-2">
                                                <div class="form-group">
                                                    <label for="minimum_stock">Stock mínimo</label>
                                                    <input type="text" class="form-control" name="minimum_stock" id="minimum_stock">
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-2 quantity">
                                                <div class="form-group">
                                                    <label for="quantity">Cantidad*</label>
                                                    <input type="number" step="1" class="form-control" id="quantity" name="quantity" required>
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-2">
                                                <div class="form-group">
                                                    <label for="cost">Costo</label>
                                                    <input type="number" step="0.01" class="form-control" id="cost" name="cost">
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
                                                    <input type="number" step="0.01" class="form-control" id="location" name="location">
                                                </div>
                                            </div>

                                            <div class="col-12 col-lg-2">
                                                <div class="form-group">
                                                    <label>Exonerado</label>
                                                    <input type="checkbox" value="1" name="exonerated" id="exonerated">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-lg-3" style="display: none;">
                                                <div class="form-group">
                                                    <label for="cost">Utilidad Unitario* (%)</label>
                                                    <input type="number" step="0.01" class="form-control" id="utility" name="utility">
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-3" style="display: none;">
                                                <div class="form-group">
                                                    <label for="price">Valor Venta Unitario*</label>
                                                    <input type="hidden" id="sale_value" name="sale_value">
                                                    <input type="number" step="0.01" class="form-control" id="price" name="price">
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-3" style="display: none;">
                                                <div class="form-group">
                                                    <label for="higher_price">Precio Por Mayor*</label>
                                                    <input type="number" step="0.01" class="form-control" value="0" id="higher_price" name="higher_price">
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
                                                <th>Utilidad(%)</th>
                                                <th>Precio(INC. IGV)</th>
                                                <th>*</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
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
                        {{-- <form role="form" data-toggle="validator" id="frm_category"> --}}
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
                        {{-- </form> --}}
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

    <div id="mdl_last_quotation" class="modal fade bd-example-modal-lg" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <button class="btn btn-primary-custom btnPrint" id="">IMPRIMIR</button>
                            <button class="btn btn-primary-custom btnOpen" id="">Abrir en navegador</button>
                            <button class="btn btn-dark" id="btnSend">Enviar al Cliente</button>
                            <button class="btn btn-danger-custom pull-right" id="btnClose">
                                <i class="fa fa-close"></i>
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <iframe frameborder="0" width="100%;" height="700px;" id="frame_pdf">

                            </iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('script_admin')
    <script>
        $(document).ready(function() {
            $('#showPayments').hide();
        })
        if ($('#coin').val() == 1) {
            $('#exchange_rate').attr('readonly', true);
        }
        var igv = $('#igv').val();
        $("#customer").select2({
            placeholder: 'Buscar Cliente',
            allowClear: true
        });

        var emailSelect = $('option:selected', $('#customer')).attr('c-email');

        $('#customer').on('change', function() {
            emailSelect = $('option:selected', this).attr('c-email');
        });

        $("#product").select2({
            placeholder: 'Buscar Producto',
            allowClear: true
        });

        $('#openCustomer').on('click', function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: 'post',
                url: '/commercial/customer/getcode',
                dataType: 'json',
                success: function(response) {
                    $('#code').val(response);
                    $('#code').attr('readonly', true);
                },
                error: function(response) {
                    toastr.error(response.responseText);
                }
            });

            $('#mdl_add_cliente').modal('show');
        });

        $('#typedocument').on('change', function() {
            clearDataCustomer();
            $("#validado").val(0)
        });

        $('#condition').change(function () {
            if ($(this).val() == 'CREDITO') {
                $('#amountPendingModal').val($('#c_total').val())
                $('#showPayments').show();
                $('#creditMdl').modal('show');
            }
            let days = $('#condition option:selected').data('days');

            let currentDate = $('#expiration').val();
        });

        $('#generateDues').click(function() {
            generateDues();
        });

        $('body').on('click','#showPayments', function(e) {
            e.preventDefault()
            $('#amountPendingModal').val($('#c_total').val())
            $('#creditMdl').modal('show');
        })

        function generateDues() {
            $('#payments_lists').html(''); 
            let frecuency = $('#dueFrecuency').val();
            let numberDues = $('#numberDues').val();
            let pending = $('#amountPendingModal').val();

            let due = parseFloat(pending) / parseFloat(numberDues);
            let paymentDate = moment().format('DD-MM-YYYY');

            let days = parseInt(frecuency) * parseInt(numberDues);

            let date = moment().add(days, 'days').format('DD-MM-YYYY');

            $('#expiration').val(date);

            $('#expiration').trigger('changeDate');

            for (let index = 0; index < numberDues; index++) {
                paymentDate = moment(paymentDate, 'DD-MM-YYYY').add(frecuency, 'days').format('DD-MM-YYYY');
                console.log(paymentDate);
                let tr = `
                        <div class="row">
                            <div class="col-5">
                                <div class="form-group">
                                    <label>Fecha</label>
                                    <input type="text" class="form-control datepick" name="payment_date[]" value="${paymentDate}">
                                </div>
                            </div>
                            <div class="col-5">
                                <div class="form-group">
                                    <label>Monto</label>
                                    <input type="number" step="0.01" min="1" class="form-control" value="${parseFloat(due).toFixed(2)}" name="payment_amount[]">
                                </div>
                            </div>
                            <div class="col-2">
                                <button class="btn btn-sm btn-danger mt-4 removeRowPayments" type="button"><i class="fa fa-close"></i></button>
                            </div>
                        </div>
                `;
                $('#payments_lists').append(tr); 
            }
        }

        /**
         *btnPrint
         **/
        $('body').on('click', '.btnPrint', function(){
            $("#frame_pdf").get(0).contentWindow.print();
        })

        $('body').on('focus',".datepick", function(){
            $(this).datepicker({
                format: 'dd-mm-yyyy',
                autoclose: true,
            });
        });
        $('body').on('click', '.removeRowPayments', function() {
            $(this).parent().parent().remove();
        })


        $('body').on('click', '#btnSend', function() {
            $('#mdl_last_quotation').modal('hide');
            let id = $('#quotation_id').val();
            console.log("DONDE");
            console.log(id);
            $.confirm({
                icon: 'fa fa-question',
                theme: 'modern',
                animation: 'scale',
                title: '¿Está seguro de enviar esta cotización?',
                content: function() {
                    let value = emailSelect;

                    return '<div class="form-group">' +
                        '<input type="text" id="emailEnviar" name="email" class="form-control" value="' + value + '" />' +
                        '<option></option>' +
                        '</div>'
                },
                buttons: {
                    Confirmar: {
                        text: 'Confirmar',
                        btnClass: 'btn-green',
                        action: function(){
                            $.confirm({
                                icon: 'fa fa-check',
                                title: 'Mensaje enviado!',
                                theme: 'modern',
                                type: 'green',
                                buttons: {
                                    Cerrar: function() {
                                        window.location.href = '/commercial.quotations';
                                    }
                                },
                                content: function() {
                                    var self = this;
                                    return $.ajax({
                                        type: 'post',
                                        url: '/commercial.quotations.send',
                                        data: {
                                            _token: '{{ csrf_token() }}',
                                            quotation_id: id,
                                            email: $('#emailEnviar').val(),
                                        },
                                        dataType: 'json',
                                        success: function(response) {
                                            if(response == true) {
                                                toastr.success('Se envió satisfactoriamente la cotización');

                                            } else {
                                                toastr.error(response);
                                            }

                                        },
                                        error: function(response) {
                                            console.log(response.responseText);
toastr.error('Ocurrio un error');
                                            console.log(response.responseText)
                                        }
                                    });
                                }
                            });
                        }
                    },
                    Cancelar: {
                        text: 'Cancelar',
                        btnClass: 'btn-red',
                        action: function(){
                            window.location.href = '/commercial.quotations';
                        }
                    }
                }
            });
        });

        /**
         *btnOpen
         **/
        $('body').on('click', '.btnOpen', function(){
            let id = $(this).attr('id');
            window.open('/commercial.quotations.show.pdf/' + id, '_blank');

        })

        $('body').on('keyup click', '.c_quantity', function() {
            recalculate();
        });

        $('body').on('change', '.c_price', function() {
            recalculate();
        });

        function calculate_beta() {
            let c_subtotal = 0;
            let c_total = 0;
            let c_gravada = 0;
            let tr = $('.c_quantity').parent().parent();
            let price = tr.find('.c_price').val();
            let total = (tr.find('.c_quantity').val() * price);

            $('#tbl_products tbody tr').each(function(index, tr) {
                c_subtotal = (c_subtotal * 1) + (($(tr).find('.c_total').val() * 1) - ($(tr).find('.c_subtotal').val() * 1));
                c_total = (c_total * 1) + ($(tr).find('.c_total').val() * 1);
                c_gravada = (c_gravada * 1) + ($(tr).find('.c_subtotal').val() * 1);
            });

            let c_percentage = parseFloat($('#c_percentage').val());
            //console.log(c_percentage);
            if (c_percentage >= 0 && c_gravada >0){
                c_gravada = c_gravada * (1-c_percentage/100);
                let p_igv = $('#igv').val();
                c_subtotal = (c_gravada * 1) * (p_igv*1/100);
                c_total = c_gravada *1 + c_subtotal *1;
            }


            $('#c_igv').val((c_subtotal*1).toFixed(2));
            $('#c_total').val(c_total.toFixed(2));
            $('#c_taxed').val((c_gravada * 1).toFixed(2));
            $('#c_exonerated').val(0.00);
        }

        function recalculate() {
            let c_subtotal_gen = 0;
            let c_total_gen = 0;
            let c_gravada_gen = 0;
            let c_exonerated_gen = 0;
            let c_sum_free = 0;
            let c_sum_unaffected = 0;
            let c_percentage = 0;
            let c_discount = 0;
            let totalFinal = 0;
            let discount = 0;
            let recharge = 0.00;
            let totalR = 0;
            let tax = 0.00;
            let total2 = 0.00;
            let total = 0.00;

            let rechargeValue = parseFloat($('#recharge_value').val()) / 100;

            let t = 0;

            if ($('#c_t') == undefined) {
                t = 0.00;
            } else {
                t = parseFloat($('#c_t').val()).toFixed(2);
            }
            // if ($('#recharge') == undefined) {
            //     recharge = 0.00;
            // }  else {
            //     recharge = $('#recharge').val();
            // }

            $('#tbl_products tbody tr').each(function(index, tr) {
                tax = 0.00;
                let product = $(tr).find('.c_product');
                let price = $(tr).find('.c_price').val();
                let quantity = $(tr).find('.c_quantity').val();
                let tigv = $('option:selected', product).attr('p-igv-type');
                let isfree = $(tr).find('.is-free').is(':checked');

                let typeRecharge = $('option:selected', product).attr('p-taxbase');
                let pr = $('option:selected', product).attr('p-tax');
                let oldRecharge = 0.00;
                let totalRecharge = 0.00;
                let newprice = 0.00;

                if (typeRecharge == '2') {
                    tax = parseFloat(pr/100);
                    let oldPrice = price;
                    newprice = parseFloat(price) / parseFloat(tax + 1);
                    totalRecharge = parseFloat(oldPrice) - parseFloat(newprice);
                    oldRecharge = parseFloat(recharge) + totalRecharge;
                    // recharge = (oldRecharge).toFixed(2);
                    total = parseFloat((quantity * newprice)).toFixed(2);
                    totalR = parseFloat((quantity * price));
                } else {
                    total = parseFloat((quantity * price)).toFixed(2);
                }
                
                total2 = parseFloat((quantity * price)).toFixed(2);
                if (tigv == 2 || tigv == 3 || tigv == 4 || tigv == 5 || tigv == 6 || tigv == 7 || tigv == 10 || tigv == 11 || tigv == 12 ||
                    tigv == 13 || tigv == 14 || tigv == 15 || tigv == 17 || isfree) {
                    $(tr).find('.c_subtotal').val(price);
                    $(tr).find('.c_total').val(price);

                    totalR = price;

                    c_sum_free = parseFloat((c_sum_free * 1) + ($(tr).find('.c_total').val() * 1)).toFixed(2);
                } else if(tigv == 8) {
                    let subtotal = parseFloat((parseFloat(total2)) / (1 + (igv / 100) + tax)).toFixed(2);
                    if (typeRecharge == '2') {
                        recharge = (parseFloat(subtotal) * parseFloat(tax) + parseFloat(recharge));
                    }
                    $(tr).find('.c_subtotal').val(subtotal);
                    $(tr).find('.c_total').val((parseFloat(subtotal) + (parseFloat(subtotal) * (igv / 100))).toFixed(2));

                    totalR = total;

                    c_exonerated_gen = parseFloat((c_exonerated_gen * 1) + ($(tr).find('.c_total').val() * 1)).toFixed(2);
                    c_total_gen = parseFloat((c_total_gen * 1) + (parseFloat(total2))).toFixed(2);
                } else if(tigv == 9 || tigv == 16) {
                    $(tr).find('.c_subtotal').val(parseFloat(price) * parseFloat(quantity));
                    $(tr).find('.c_total').val(parseFloat(price) * parseFloat(quantity));

                    totalR = price;

                    c_sum_unaffected = parseFloat((c_sum_unaffected * 1) + ($(tr).find('.c_total').val() * 1)).toFixed(2);
                    console.log(price)
                    c_total_gen = parseFloat((c_total_gen * 1) + (parseFloat(total2))).toFixed(2);
                } else {
                    let subtotal = parseFloat((parseFloat(total2)) / (1 + (igv / 100) + tax)).toFixed(2);
                    let igvLinea = (parseFloat(subtotal) * (igv/100)).toFixed(2)
                    let totalLinea = parseFloat(parseFloat(subtotal) + (parseFloat(subtotal) * (igv / 100))).toFixed(2);
                    if (typeRecharge == '2') {
                        recharge = parseFloat(parseFloat(totalR) - parseFloat(parseFloat(subtotal) + parseFloat(igvLinea)) ) + parseFloat(recharge);
                    }
                    $(tr).find('.c_subtotal').val(parseFloat(subtotal).toFixed(2));
                    $(tr).find('.c_total').val(parseFloat(totalLinea).toFixed(2));



                    totalR = total

                    if($('option:selected', product).attr('p-exonerated') === '0') {
                        c_gravada_gen = parseFloat((c_gravada_gen * 1) + ($(tr).find('.c_subtotal').val() * 1)).toFixed(2);
                        c_subtotal_gen = parseFloat(parseFloat(igvLinea) + parseFloat(c_subtotal_gen)).toFixed(2);
                        c_total_gen = parseFloat((c_total_gen * 1) + (parseFloat(total2))).toFixed(2);
                    } else {
                        c_total_gen = parseFloat((c_total_gen * 1) + (parseFloat(total2))).toFixed(2);
                        c_total_gen = parseFloat((c_total_gen * 1) + (parseFloat(total2))).toFixed(2);
                    }
                }

                totalFinal = (parseFloat(c_total_gen) + parseFloat(t)).toFixed(2);

                if ($('#c_percentage').val() == NaN || $('#c_percentage').val() == '' || $('#c_percentage').val() == undefined) {
                    c_percentage = 0.00;
                } else {
                    c_percentage = parseFloat($('#c_percentage').val()).toFixed(2);
                    discount = ((parseFloat(totalFinal) * parseFloat(c_percentage)) / 100).toFixed(2)
                    totalFinal = (parseFloat(totalFinal) - parseFloat(discount));
                    c_gravada_gen = parseFloat(totalFinal) / (1 + (igv / 100)).toFixed(2);
                    c_subtotal_gen = (totalFinal - c_gravada_gen).toFixed(2);
                }

                console.log(tax, 's')
            });

            totalFinal = (parseFloat(totalFinal)).toFixed(2);

            c_gravada_gen = (parseFloat(c_gravada_gen)).toFixed(2)

            $('#c_igv').val(c_subtotal_gen);
            $('#c_total').val(totalFinal);
            $('#c_taxed').val(c_gravada_gen);
            $('#exonerated').val(c_exonerated_gen);
            $('#c_free').val(c_sum_free);
            $('#c_unaffected').val(c_sum_unaffected);
            $('#c_discount').val(discount);
            $('#recharge').val(parseFloat(recharge).toFixed(2));

            $('#mountPayment').val(totalFinal);
        }

        $('body').on('change', '.is-free', function (e) {
            let field = $(this).parent().parent().find('.is_free_c');
            if($(this).is(':checked')) {
                field.val(1)
            } else {
                field.val(0)
            }
            recalculate()
        });

        function calc(num) {
            var with2Decimals = num.toString().match(/^-?\d+(?:\.\d{0,2})?/)
            return with2Decimals
        }

        $('body').on('change', '.c_product', function() {
            let tr = $(this).parent().parent().parent();
            tr.find('.c_price').val($('option:selected', this).attr('p-price'));
            tr.find('.c_quantity').keyup();
            tr.find('.c_quantity').focus();

            let isService = $('option:selected', $(this)).attr('p-otype');
            let totalService = tr.find('.c_total').val();
            let ide;
            if (isService == 2) {
                if (totalService >= 700.00) {
                    isDetraccion = 1;
                    if (isDetraccion == 1) {
                        let account = $('#detractionAccount').val(),
                            bank = $('#detractionBank').val();
                        $("#t_detraction").val('Operación sujeta al Sistema de Pago de Obligaciones Tributarias: '+ bank +' CTA. DETRACCIÓN Nº ' + account + '. ');
                        isDetraccion = 0;
                        ide = 1;
                        // if ($('#detraction').parent().hasClass('off')) {
                            $('#detraction').prop('checked', true);
                        // }
                    }
                }
            }

            if (isService == 22) {
                icbperTotal = ($('#pbp').val()  * tr.find('.c_quantity').val());
                $('#c_t').val(icbperTotal.toFixed(2));
            }

            recalculate();
        });

        let isDetraccion;
        $('body').on('keyup', '.c_quantity', function() {

            let tr = $(this).parent().parent();
            let product = tr.find('.c_product');
            let isService = $('option:selected', product).attr('p-otype');
            let totalService = tr.find('.c_total').val();
            let ide;
            if (isService == 2) {
                if (totalService >= 700.00) {
                    isDetraccion = 1;
                    if (isDetraccion == 1) {
                        let account = $('#detractionAccount').val(),
                            bank = $('#detractionBank').val();
                        $("#t_detraction").val('Operación sujeta al Sistema de Pago de Obligaciones Tributarias: '+ bank +' CTA. DETRACCIÓN Nº ' + account + '. ');
                        isDetraccion = 0;
                        ide = 1;
                        $('#detraction').prop('checked', true);
                    }
                }
            }

            if (isService == 22) {
                icbperTotal = ($('#pbp').val()  * tr.find('.c_quantity').val());
                $('#c_t').val(icbperTotal.toFixed(2));
            }

            recalculate()
        });

        $("#detraction").on( 'change', function() {
            if( $(this).is(':checked') ) {
                $("#t_detraction").val('Operación sujeta al Sistema de Pago de Obligaciones Tributarias: '+ bank +' CTA. DETRACCIÓN Nº ' + account + '. ');
            } else {
                $("#t_detraction").val('');
            }
        })

        $('body').on('change', '.c_price', function() {
            let tr = $(this).parent().parent();
            let quantity = tr.find('.c_quantity');
            tr.find('.c_quantity').keyup();
            tr.find('.c_quantity').focus();
            tr.find('.c_quantity').val(1);

            let product = tr.find('.c_product');
            let isService = $('option:selected', product).attr('p-otype');
            let totalService = tr.find('.c_total').val();
            let ide;
            if (isService == 2) {
                if (totalService >= 700.00) {
                    isDetraccion = 1;
                    if (isDetraccion == 1) {
                        let account = $('#detractionAccount').val(),
                            bank = $('#detractionBank').val();
                        $("#t_detraction").val('Operación sujeta al Sistema de Pago de Obligaciones Tributarias: '+ bank +' CTA. DETRACCIÓN Nº ' + account + '. ');
                        isDetraccion = 0;
                        ide = 1;
                        $('#detraction').prop('checked', true);
                    }
                }  else {
                    $('#detraction').prop('checked', false);
                }
            }

            if (isService == 22) {
                icbperTotal = ($('#pbp').val()  * tr.find('.c_quantity').val());
                $('#c_t').val(icbperTotal.toFixed(2));
            }

            recalculate()
        });

        $("#service_region").on( 'change', function() {
            if( $(this).is(':checked') ) {
                let ex = $('#c_igv').val();
                $('#c_igv').val(0.00);
                let taxed = $('#c_taxed').val();
                $('#c_total').val(taxed);
                $('#c_exonerated').val(ex);
            } else {
                recalculate();
            }
        });
        $("#product_region").on( 'change', function() {
            if( $(this).is(':checked') ) {
                let ex = $('#c_igv').val();
                $('#c_igv').val(0.00);
                let taxed = $('#c_taxed').val();
                $('#c_total').val(taxed);
                $('#c_exonerated').val(ex);
            } else {
                recalculate();
            }
        });


        $('#quantity').on('change', function() {
            calculateAmount();
        });

        function clearDataProduct() {
            $('#category').val('');
            $('#measure').val('');
            $('#brand').val('');
            $('#classification').val('');
            $('#centerCost').val('');
            $('#equivalence_code').val('');
            $('#external_code').val('');
            $('#detailProduct').val('');
            $('#code').val('');
            $('#internalcode').val('');
            $('#pdescription').val('');
            $('#product_id').val('');
            $('#quantity').val('');
            $('#cost').val('');
            $('#price').val('');
            $('#type').val('');
            $('#coin').val('1');
            $('#utility').val('');
            $('#initial_stock').val('');
            $('#maximum_stock').val('');
            $('#minimum_stock').val('');
            $('#location').val('');
            $('#warehouse').val('');
            $('#intial_date').val('');
        }

        $('#coin').change(function() {
            if ($(this).val() == 1) {
                $('#exchange_rate').attr('readonly', true);
            } else {
                $('#exchange_rate').attr('readonly', false);
            }
        });

        $('body').on('click', '.openModalProduct', function() {
            clearDataProduct();
            $('#mdl_add_product').modal('show');
        });

        $('.select3').select2();

        $('.btnAddCategory').click(function() {
            $('#add_category').val('');
            $('#mdl_add_category').modal('show');
        });

        $('#btnAddCategory').click(function() {
            $.post('/warehouse.category.save',
                'description=' + $('#add_category').val() +
                '&_token=' + '{{ csrf_token() }}', function(response) {
                    if(response == true) {
                        toastr.success('Categoría grabada satisfactoriamente.');
                        $.get('/warehouse.category.all', function(response) {
                            let option = '<option>SIN CATEGORÍA</option>';
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

        $('body').on('click', '.btnAddBrand', function() {
            $('#mdl_add_brand').modal('show');
        });

        $('#saveBrand').click(function() {
            $.post('/logistic.brand.save',
                'add_brand=' + $('#add_brand').val() +
                '&_token=' + '{{ csrf_token() }}', function(response) {
                    if(response == true) {
                        $('#add_brand').val('');
                        $.get('/logistic.brand.get', function(response) {
                            let option = '<option value="">SIN MARCA</option>';
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

        $('body').on('click', '.openModalProduct', function() {
            let tr = $(this).parent().parent();

            let products = tr.find('#c_product').html('');
        });

        $("#mdl_add_product").on('hidden.bs.modal', function () {
            $.post('/commercial.quotations.products', '_token=' +  '{{ csrf_token() }}', function(response) {
                let option = '<option>Seleccionar Producto</option>';
                for (var i = 0; i < response.length; i++) {
                    option += '<option value="' + response[i].id + '" p-stock="' + response[i].stock + '" p-price="' + response[i].price + '" p-otype="' + response[i].operation_type + '">';
                    option += response[i].description + '</option>';
                }

                // $('.c_product').html('');
                $('.c_product').append(option);
            }, 'json');
        });

        $('#frm_product').validator().on('submit', function(e) {
            if(e.isDefaultPrevented()) {
                toastr.warning('Debe llenar todos los campos obligatorios');
            } else {
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
                        return;
                    }

                    //let data = new FormData($('#frm_product, #frm_prices').serialize());

                    let data = new FormData($('#frm_product')[0]);
                    let data2 = $('#frm_prices').serializeArray();
                    data2.forEach(function (fields) {
                        data.append(fields.name, fields.value);
                    });

                    console.log(data,'data')
                    console.log(data2,'data2')

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
                                $('#mdl_add_product').modal('hide');
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
                } else {
                    toastr.warning('Debe registrar al menos un precio');
                    e.preventDefault();
                }
            }
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

        let products = '';
        function gp() {
            y = products;
            $.ajax({
                url: '/commercial.quotations.products',
                type: 'post',
                data: '&_token=' + '{{ csrf_token() }}',
                dataType: 'json',
                success: function(response) {
                    j = y;
                    for (var i = 0; i < response.length; i++) {
                        j += '<option value="' + response[i].id + '" p-taxbase="' + response[i].taxbase + '" p-tax="' + response[i].tax + '" p-stock="' + response[i].stock + '" p-price="' + response[i].price + '" p-igv-type="' + response[i].type_igv_id + '" p-otype="' + response[i].operation_type + '" p-exonerated="' + response[i].exonerated + '">' + response[i].internalcode + ' - ' + response[i].description + '</option>';
                    }

                    return j;
                    console.log(response);
                },
                error: function(response) {
                    console.log(response.responseText);
toastr.error('Ocurrio un error');
                }
            });

            return j;
        }

        $('#btnAddProduct').on('click', function() {
            let le = '';
                le = `<div class="input-group-append pl-4">
                        <label><input type="checkbox" class="is-free"> Es Bonificación</label>
                        <input type="hidden" name="is_free[]" class="is_free_c" value="0">
                    </div>`;
            let data = `
                <tr>
                    <td>
                        <div class="input-group">
                            <select style="width: 80%;" class="form-control select_2  c_product" id="c_product" name="cd_product[]" required>
                            <option value="">Seleccionar Producto</option>
                            `
                + gp() +
                `
                            </select>
                            `
                +
                le
                +
                `
                    </td>
                    <td>
                        <input type="text" class="form-control c_detail" name="cd_detail[]" />
                    </td>
                    <td>
                        <input type="number" class="form-control c_quantity" step="0.01" min="1" name="cd_quantity[]" value="1"/>
                    </td>
                   
                    <td>
                        <select name="cd_price[]" class="form-control c_price"></select>
                    </td>
                    <td>
                        <input type="text" class="form-control c_subtotal" name="cd_subtotal[]" value="0" readonly/>
                    </td>
                    <td>
                        <input type="text" class="form-control c_total" name="cd_total[]" value="0" readonly/>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger-custom btn-rounded remove"><i class="fa fa-close"></i></button>
                    </td>
                </tr>
            `;
            $('#tbl_products tbody').append(data);

            $('.select_2').select2();
        });

        $('body').on('click', '.remove', function() {
            $(this).parent().parent().remove();
            recalculate()
        });

        $('#btnClose').click(function() {
            window.location.href = '/commercial.quotations';
        });



        $('#c_percentage').keyup(function () {
            recalculate();
        });


        /**
         * Methods Ajax
         */

        $('#document').on('keyup', function() {
            let url = '';
            if($('#typedocument').val() == 2) {
                if($(this).val().length == 8) {
                    url = '/consult.dni/' + $(this).val();
                    getCustomer(url, $('#typedocument').val());
                }
            } else if($('#typedocument').val() == 4) {
                if($(this).val().length == 11) {
                    url = '/consult.ruc/' + $(this).val();
                    getCustomer(url, $('#typedocument').val());
                }
            }
        });

        $('#buttonSaves button').on('click', function(e) {
            let idButton = $(this).attr('id');
            if(idButton == "saveQuo"){
                $('#estateQuotation').val('1');
            }else{
                $('#estateQuotation').val('0');
            }

        });



        $('#frm_quotation').validator().on('submit', function(e) {
            if(e.isDefaultPrevented()) {
                toastr["warning"]("Debe llenar todos los campos obligatorios");
            }else {
                e.preventDefault();
                let data = $('#frm_quotation').serialize();

                if($('#tbl_products tbody tr').length == 0) {
                    toastr["error"]("Debe seleccionar algún producto o servicio");
                    return false;
                }else{
                    if($('#c_total').val() <= 0){
                        toastr["error"]("El total de la cotización no puede ser 0");
                    }else{
                        $.confirm({
                            icon: 'fa fa-question',
                            theme: 'modern',
                            animation: 'scale',
                            type: 'green',
                            title: '¿Está seguro de crear esta cotización?',
                            content: '',
                            buttons: {
                                Confirmar: {
                                    text: 'Confirmar',
                                    btnClass: 'btn-green',
                                    action: function(){
                                        $.ajax({
                                            url: '/commercial.quotations.create',
                                            type: 'post',
                                            data: data + '&_token=' + '{{ csrf_token() }}',
                                            dataType: 'json',
                                            beforeSend: function() {
                                                $('#btnGrabarCliente').attr('disabled');
                                            },
                                            complete: function() {

                                            },
                                            success: function(response) {
                                                if(response['response'] == true) {
                                                    $('.btnOpen').attr('id', response['quotation_id']);
                                                    // toastr.success('Se grabó satisfactoriamente el cliente');
                                                    $('#mdl_last_quotation').modal('show');
                                                    $('#quotation_id').val(response['quotation_id']);
                                                    $('#frame_pdf').attr('src', '/commercial.quotations.show.pdf/' + response['quotation_id']);
                                                    clearDataCustomer();
                                                    $('#typedocument').val('');
                                                    getCustomers();
                                                    $('#mdl_add_cliente').modal('hide');
                                                } else {
                                                    console.log(response.responseText);
toastr.error('Ocurrio un error');
                                                }
                                            },
                                            error: function(response) {
                                                console.log(response.responseText);
toastr.error('Ocurrio un error');
                                                $('#btnGrabarCliente').removeAttr('disabled');
                                            }
                                        });
                                    }
                                },
                                Cancelar: {
                                    text: 'Cancelar',
                                    btnClass: 'btn-red',
                                    action: function(){
                                    }
                                }
                            }
                        });
                    }

                }
            }

        });

        $("#frm_cliente").validator().on('submit', function(e) {
            if (e.isDefaultPrevented()) {
                toastr.warning('Debe llenar todos los campos obligatorios.');
            } else {
                e.preventDefault();
                let data = $('#frm_cliente').serialize();
                $.ajax({
                    url: '/commercial.customer.create',
                    type: 'post',
                    data: data + '&_token=' + '{{ csrf_token() }}',
                    dataType: 'json',
                    beforeSend: function() {
                        $('#btnGrabarCliente').attr('disabled');
                    },
                    complete: function() {

                    },
                    success: function(response) {
                        console.log(response);
                        if(response == true) {
                            toastr.success('Se grabó satisfactoriamente el cliente');
                            clearDataCustomer();
                            $('#typedocument').val('');
                            $('#mdl_add_cliente').modal('hide');
                            getCustomers();
                        } else {
                            console.log(response.responseText);
toastr.error('Ocurrio un error');
                        }
                    },
                    error: function(response) {
                        console.log(response.responseText);
toastr.error('Ocurrio un error');
                        $('#btnGrabarCliente').removeAttr('disabled');
                    }
                });
            }
        });

        function getCustomers()
        {
            $.get('/commercial.customer.all', function(response) {
                $('#customer').html('');
                $('#customer').select2('destroy');
                let option = '';
                for (let i = 0;i < response.length; i++) {
                    option += '<option value="' + response[i].id + '">' + response[i].description + '</option>';
                }

                $('#customer').append(option);
                $('#customer').select2();
            }, 'json');
        }

        function getCustomer(url, typedocument)
        {
            $.ajax({
                url: url,
                type: 'post',
                data: {
                    '_token': "{{ csrf_token() }}"
                },
                dataType: 'json',
                beforeSend: function() {
                    let effect = '<div class="overlay effect">';
                    effect += '<i class="fa fa-refresh fa-spin">';
                    effect += '</div>';
                    $('.content-overlay').append(effect);
                },
                complete: function() {
                    $('.effect').remove();
                },
                success: function(response) {
                    if(typedocument == 2) {
                        $('#description').val(response['nombres'] + ' ' + response['apellidoPaterno'] + ' ' + response['apellidoMaterno']);
                        $('#validado').val(1);
                    } else {
                        $('#description').val(response['razonSocial']);
                        $('#phone').val(response['telefonos']);
                        if(response['direccion'] != '-'){
                            $('#address').val(response['direccion'] + ' - ' + response['provincia'] + ' - ' + response['distrito']);
                        } else {
                            $('#address').val(response['direccion'] + ' - ' + response['provincia'] + ' - ' + response['distrito']);
                        }
                        $('#validado').val(1);
                    }
                },
                error: function(response) {
                    clearDataCustomer();
                    toastr.info('El cliente no existe.');
                }
            });
        }

        function clearDataCustomer()
        {
            $('#document').val('');
            $('#description').val('');
            $('#phone').val('');
            $('#address').val('');
            $('#email').val('');
            $('#emailOptional').val('');
        }

        function calculateAmount()
        {
            let subtotal = $('#quantity').val() * $('#price').val();
            $('#subtotal').val(subtotal.toFixed(2));
            let total = subtotal - $('#igvitem').val();
            $('#total').val(total.toFixed(2));
        }

        $('.select_2').select2({width: 'element'});
        $('#utility').keyup(function() {
            let cost = $('#cost').val() * 1;
            let utility = parseFloat($(this).val() / 100).toFixed(2);
            let percent = parseFloat(cost * utility).toFixed(2);
            let price = (cost + (percent * 1)).toFixed(2);
            $('#price').val(price);
        });

        $('#price').keyup(function() {
            let price = $('#price').val() * 1;
            let cost = $('#cost').val() * 1;
            let percent = price - cost;
            let utility = ((percent * 100) / cost).toFixed(2);

            console.log(typeof price);

            $('#utility').val(utility);
        });


        $('body').on('change', '.c_product', function() {
            let that = $(this);
            let pid = that.val();
            let product;

            let myPrices = '';
            $.ajax({
                url: '/product/price/list/get',
                type: 'get',
                data: {
                    id: pid,
                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function (response) {
                    for(let x = 0; x < response.length; x++) {
                        myPrices += '<option value="' + response[x].price + '">' + response[x]['price_list'].description + ' - ' + response[x].price +'</option>';
                    }

                    let productRow = that.parent().parent().parent()

                    let listPriceSelect = productRow.find('.c_price');
                    listPriceSelect.html('');
                    listPriceSelect.append(myPrices);

                    that.find('.c_quantity').val(1);
                    that.find('.c_quantity').keyup();
                    recalculate();
                }
            });

            that.find('.c_quantity').focus();
            that.find('.c_quantity').val(1);
            that.find('.c_quantity').keyup();
            that.find('.c_quantity').click();
            recalculate();
        });

        $('body').on('change', '.c_price', function() {
            recalculate();
        });

        $('#btnConfigPrices').click(function () {
            $('#mdl_add_prices').modal('show');
        });

        $('#btnAddPrice').click(function () {
            let tr = '';
            tr += '<tr>';
                tr += '<td>' + select_price_lists + '</td>';
                tr += '<td><input type="text" class="form-control pricePercentage" name="pricePercentage[]" /></td>';
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
        var price_lists = @json($price_lists);
        var select_price_lists = '<select class="form-control product_price_list" name="product_price_list[]">';
        $(price_lists).each(function (index, data) {
            select_price_lists += '<option value="' + data['id'] + '">' + data['description'] + '</option>';
        });

        select_price_lists += '</select>';

        $(document).on('hidden.bs.modal', function (event) {
            if ($('.modal:visible').length) {
                $('body').addClass('modal-open');
            }
        });

        $('body').on('keyup', '.priceListValue', function () {
            let tr = $(this).parent().parent();
            calculateUtilityPriceList(tr);
        });

        $('body').on('keyup', '.pricePercentage', function () {
            let tr = $(this).parent().parent();
            calculatePercentPriceList(tr);
        });

        $('#cost').on('keyup', function () {
            $('.product_price_list').each(function (i, obj) {
                calculatePercentPriceList($(this).parent().parent());
            });
        });

        function calculatePercentPriceList(tr) {
            let priceListUtility = tr.find('.pricePercentage').val();
            let cost = $('#cost').val();
            let final_price = (cost * 1) + ((cost * priceListUtility) / 100);
            tr.find('.priceListValue').val(Math.round(final_price * 100) / 100);
        }

        function calculateUtilityPriceList(tr) {
            let priceListUtility = tr.find('.priceListValue').val();
            let cost = $('#cost').val();
            let difference = priceListUtility - cost;
            let utility = (difference * 100) / cost;
            tr.find('.pricePercentage').val(utility.toFixed(2));
        }

        $('body').on('click', '.btnAddClassification', function () {
            $('#mdl_add_classification').modal('show');
        });
        $('body').on('click', '.btnAddCenterCost', function () {
            $('#mdl_add_centercost').modal('show');
        });

        $('#quantity').attr('readonly', true);

        $('#type').change(function() {
            if($(this).val() == 2) {
                $('#quantity').val('');
                $('.quantity').hide(100);
                $('#initial_stock').attr('readonly', true);
                $('#intial_date').attr('readonly', true);
                $('#maximum_stock').attr('readonly', true);
                $('#minimum_stock').attr('readonly', true);
                $('#warehouse').attr('readonly', true);
                $('#location').attr('readonly', true);
                $('#quantity').removeAttr('required');
                $('#quantity').val(0);
                $('#initial_stock').val(0);
                $('#maximum_stock').val(0);
                $('#minimum_stock').val(0);
            } else {
                $('.quantity').show(100);
                $('#initial_stock').attr('readonly', false);
                $('#maximum_stock').attr('readonly', false);
                $('#minimum_stock').attr('readonly', false);
                $('#intial_date').attr('readonly', false);
                $('#warehouse').attr('readonly', false);
                $('#location').attr('readonly', false);
                $('#quantity').attr('required');
            }
        });

        $('#initial_stock').keyup(function() {
            $('#quantity').val($(this).val());
        });
        
        let daterep = false;

        $('#date').change(function(e) {
            //alert($("#date").val()); 
            if (!daterep) {
                // ni idea de porque llama 3 y 2 veces cuando cambia de fecha alternativamente :'u
                daterep = true;
                actualizarTipoCambio(); 
            }else{
                daterep = false; //aunque sea reduce a 2 y 1 vez :'u 
            }
        });

        $('#coin').change(function(e) {
            e.preventDefault();
            actualizarTipoCambio(); 
        });

        function actualizarTipoCambio() {
            //let date = moment($('#date').val(), 'DD-MM-YYYY').add(1, "days").format('DD-MM-YYYY')
            let date = moment($('#date').val(), 'DD-MM-YYYY').format('DD-MM-YYYY');
            let coinValue = $('#coin').val();
            if (coinValue == 2) { // 2 == dólares
                $.get(`/get-exchangerate/by-date/${date}`, function(response) {
                    $('#exchange_rate').val(response.venta);
                }, 'json');
            } else {
                $('#exchange_rate').val('');
                $('#exchange_rate').attr('readonly', true);
            }
        }
    </script>
@stop
