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
        h3 a {
            color: #fff;
        }
    </style>
@endsection
@section('content')
    <form method="post" role="form" data-toggle="validator" id="frm_quotation">
        <input type="hidden" value="{{$igv->value}}" id="igv" />
        <input type="hidden" value="{{$clientInfo->price_type}}" id="pt" />
        <input type="hidden" value="{{$clientInfo->less_employees}}" id="le" />
        <input type="hidden" value="{{$clientInfo->consumption_tax_plastic_bags_price}}" id="pbp" />
        <input type="hidden" value="{{$clientInfo->issue_with_previous_data}}" id="ipd" />
        <input type="hidden" value="{{$clientInfo->issue_with_previous_data_days}}" id="ipnd" />
        <input type="hidden" value="{{$clientInfo->exchange_rate_sale}}" id="ers" />
        <div class="row">
            <div class="col-12">
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title text-center">
                            @if ($type === '1')
                                NUEVA FACTURA
                            @elseif ($type === '2')
                                NUEVA BOLETA DE VENTA
                            @endif
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-5">
                                <div class="form-group">
                                    <label for="customer"> </label>
                                    <div class="input-group">
                                        <select name="customer" id="customer" class="form-control" style="width: 80%;">
                                            {{-- @if($customers->count() > 0) --}}
                                                @foreach($customers as $c)
                                                    <option value="{{$c->id}}" {{ $sale->customer_id == $c->id ? 'selected' : '' }}>{{$c->description}}</option>
                                                @endforeach
                                            {{-- @endif --}}
                                        </select>
                                        <div class="input-group-append" id="openCustomer" style="cursor: pointer;">
                                            <button type="button" class="btn btn-primary-custom">
                                                NUEVO
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if ($type === 1)
                                <div class="col-12 col-md-2">
                                    <div class="form-group">
                                        <label for="typevoucher">Correo Cliente</label>
                                        <input type="text" class="form-control" name="customerEmail" id="customerEmail">
                                    </div>
                                </div>
                            @endif
                            <div class="col-12 col-md-2 col-lg-1">
                                <div class="form-group">
                                    <label for="typevoucher">Serie</label>
                                    <input type="hidden" id="typevoucher_id" name="typevoucher_id" value="{{ $type }}">
                                    <input type="text" disabled class="form-control" name="serialnumber" value="{{ $sale->serialnumber }}">
                                </div>
                            </div>
                            <div class="col-12 col-md-2">
                                <div class="form-group">
                                    <label for="typevoucher">Número (Referencial)</label>
                                    <input type="text" name="correlative" id="" value="{{ $sale->correlative }}" class="form-control" disabled>
                                    {{-- <select class="form-control" readonly="" name="correlative" id="correlative"></select> --}}
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label for="date">Fecha de Emisión</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                        </div>
                                        <input value="{{ \Carbon\Carbon::parse($sale->issue)->format('d-m-Y') }}" type="text" class="form-control" name="date" id="date" autocomplete="off" readonly="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-2">
                                <label for="typeoperation">Tipo de operación</label>
                                <select name="typeoperation" id="typeoperation" class="form-control">
                                    @if($typeoperations->count() > 0)
                                        @foreach($typeoperations as $o)
                                            <option value="{{$o->id}}" {{ $sale->typeoperation_id == $o->id ? 'selected' : '' }}>{{$o->operation}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-12 col-md-1">
                                <label for="coin">Moneda</label>
                                <select name="coin" id="coin" class="form-control">
                                    @if($coins->count() > 0)
                                        @foreach($coins as $c)
                                            <option value="{{$c->id}}" {{ $sale->coin_id == $c->id ? 'selected' : '' }}>{{$c->symbol}}</option>
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
                                        <input value="{{$currentDateLast}}" type="text" class="form-control datepicker" name="expiration" id="expiration" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-1">                
                                <input type="hidden" data-toggle="toggle" data-on="Si" data-off="No" id="paidout" name="paidout" value="1">
                            </div>
                            <div class="col-12 col-md-2">
                                <div class="form-group">
                                    <label for="condition">Condición de pago</label>
                                    <select name="condition" id="condition" class="form-control">
                                        <option value="EFECTIVO" {{ $sale->condition_payment == 'EFECTIVO' ? 'selected' : '' }}>EFECTIVO</option>
                                        <option value="CREDITO 15 DIAS" {{ $sale->condition_payment == 'CREDITO 15 DIAS' ? 'selected' : '' }}>CREDITO 15 DIAS</option>
                                        <option value="CREDITO 30 DIAS" {{ $sale->condition_payment == 'CREDITO 30 DIAS' ? 'selected' : '' }}>CREDITO 30 DIAS</option>
                                        <option value="TARJETA DE CREDITO" {{ $sale->condition_payment == 'TARJETA DE CREDITO' ? 'selected' : '' }}>TARJETA DE CREDITO</option>
                                        <option value="TARJETA DE DEBITO" {{ $sale->condition_payment == 'TARJETA DE DEBITO' ? 'selected' : '' }}>TARJETA DE DEBITO</option>
                                    </select>
                                    {{-- <input type="text" class="form-control" name="condition" id="condition" value="" /> --}}
                                </div>
                            </div>
                            <div class="col-12 col-md-2">
                                <div class="form-group">
                                    <label for="order">O/C</label>
                                    <input type="text" class="form-control" name="order" id="order" value="{{ $sale->order }}" />
                                </div>
                            </div>
                        </div>

                        <fieldset>
                            <div class="row">
                                <div class="col-12 table-responsive"> 
                                    <table class="table" id="tbl_products">
                                        <thead>
                                            <th width="400px">Producto</th>
                                            <th width="90px">Cantidad</th>
                                            <th width="100px">Stock</th>
                                            <th width="50px">Tipo IGV</th>
                                            <th width="100px">Precio</th>
                                            <th width="100px">SubTotal</th>
                                            <th width="100px">Total</th>
                                            <th width="10px">*</th>
                                        </thead>
                                        <tbody class="table-sales">
                                            @foreach ($sale->detail as $sd)
                                                <input type="hidden" name="sdid[]" value="{{ $sd->id }}">
                                                <tr>
                                                    <td>
                                                        <div class="input-group"  style="width: 100%;">
                                                            <select style="width: 80%;" class="form-control select_2 c_product" id="c_product" name="cd_product[]" >
                                                                <option value="">Seleccionar Producto</option>
                                                                @if($products->count() > 0)
                                                                    @foreach($products as $p)
                                                                        <option value="{{$p->id}}" {{ $sd->product_id == $p->id ? 'selected' : '' }} p-stock="{{$p->stock}}" p-price="{{$p->price}}" p-otype='{{ $p->operation_type }}'>{{ $p->internalcode }} - {{$p->description}}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                            @if ($clientInfo->less_employees == 1)
                                                                <div class="input-group-append">
                                                                    <button class="btn btn-primary-custom openModalProduct" type="button" id="">
                                                                        <i class="fa fa-plus"></i>
                                                                    </button>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control c_quantity" name="cd_quantity[]" value="{{ $sd->quantity }}" />
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control c_stock" name="c_stock[]" value="{{ $sd->product->stock->stock }}" readonly />
                                                    </td>
                                                    <td>
                                                        <select class="form-control type_igv" name="type_igv[]" id="type_igv">
                                                            @foreach ($igvType as $it)
                                                                <option value="{{ $it->id }}" {{ $sd->type_igv_id == $it->id ? 'selected' : ''}}>{{ $it->description }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control c_price" name="cd_price[]" value="{{ $sd->price }}" />
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control c_subtotal" name="cd_subtotal[]" value="{{ $sd->subtotal }}" readonly />
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control c_total" name="cd_total[]" value="{{ $sd->total }}" readonly />
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger-custom remove"><i class="fa fa-minus"></i></button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
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
                                <fieldset>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <div id="cont-guides">
                                                    <div class="row">
                                                        <div class="col-12 col-md-6">
                                                            <label for="g_serialnumber">Serie - Número</label>
                                                            <input type="text" id="g_serialnumber" name="g_serialnumber[]" class="form-control">
                                                        </div>
                                                        <div class="col-12 col-md-6">
                                                            <label for="g_type">Tipo</label>
                                                            <select name="g_type[]" id="g_type" class="form-control">
                                                                <option value="">- Seleccionar -</option>
                                                                <option value="1">GUÍA DE REMISIÓN REMITENTE</option>
                                                                <option value="2">GUÍA DE REMISIÓN TRANSPORTISTA</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12" style="margin-top: 2em;">
                                                        <button class="btn btn-primary-custom btn-block" id='addReferralGuide' type="button">AGREGAR GUIA DE REMISIÓN FÍSICA</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                                <div class="row">
                                    <div class="col-12">
                                        <label for="observation">Observaciones:</label>
                                        <div class="form-group">
                                            <textarea name="observation" id="observation" cols="30" rows="4" class="form-control" >{{ $sale->observation }}</textarea>
                                            <input type="text" name="t_detraction" id="t_detraction" class="form-control" readonly>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label>¿Detracción?</label>
                                        <div class="form-group">
                                            <input type="checkbox" data-toggle="toggle" value="1" data-on="Si" data-off="No" name="detraction" id="detraction" {{ $sale->detraction == 1 ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label for="property">¿Bienes Región Selva?</label>
                                        <div class="form-group">
                                            <input type="checkbox" data-toggle="toggle" value="1" data-on="Si" data-off="No" name="product_region" id="product_region" {{ $sale->productregion == 1 ? 'checked' : '' }} == 1 ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-5">
                                        <label for="services">¿Servicios Región Selva?</label>
                                        <div class="form-group">
                                            <input type="checkbox" data-toggle="toggle" value="1" data-on="Si" data-off="No" name="service_region" id="service_region" {{ $sale->serviceregion == 1 ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                {{-- <div class="row">
                                    <div class="col-6 text-right">
                                        <label>% Descuento Global</label>
                                    </div>
                                    <div class="col-6">
                                        <input name="c_percentage" type="text" class="form-control" value="{{ $sale->discount }}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6 text-right">
                                        <label>Descuento Global (-)</label>
                                    </div>
                                    <div class="col-6">
                                        <input name="c_discountGlobal" type="text" class="form-control" readonly value="{{ $sale->discount }}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6 text-right">
                                        <label>Descuento por Item (-)</label>
                                    </div>
                                    <div class="col-6">
                                        <input name="c_discountItem" type="text" class="form-control" readonly value="0.00">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6 text-right">
                                        <label>Descuento Total (-)</label>
                                    </div>
                                    <div class="col-6 text-right">
                                        <input name="c_discount" type="text" class="form-control" readonly value="0.00">
                                    </div>
                                </div> --}}
                                <input name="c_percentage" type="hidden" value="0">
                                <input name="c_discount" type="hidden" value="0">
                                <div class="row">
                                    <div class="col-6 text-right">
                                        <label>Anticipo (-)</label>
                                    </div>
                                    <div class="col-6">
                                        <input name="c_discountItem" type="text" class="form-control" readonly value="0.00">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6 text-right">
                                        <label>Exonerada</label>
                                    </div>
                                    <div class="col-6">
                                        <input name="c_exonerated" id="exonerated" type="text" class="form-control" readonly value="{{ $sale->exonerated }}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6 text-right">
                                        <label>Inafecta</label>
                                    </div>
                                    <div class="col-6">
                                        <input name="c_unaffected" type="text" class="form-control" readonly value="0.00" {{ $sale->unaffected }}>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6 text-right">
                                        <label>Gravada</label>
                                    </div>
                                    <div class="col-6">
                                        <input name="c_taxed" id="c_taxed" type="text" class="form-control" readonly value="{{ $sale->taxed }}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6 text-right">
                                        <label>IGV</label>
                                    </div>
                                    <div class="col-6 text-right">
                                        <input name="c_igv" id="c_igv" type="text" class="form-control" readonly value="{{ $sale->igv }}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6 text-right">
                                        <label>Gratuita</label>
                                    </div>
                                    <div class="col-6 text-right">
                                        <input name="c_free" type="text" class="form-control" readonly value="{{ $sale->free }}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6 text-right">
                                        <label>Otros Cargos</label>
                                    </div>
                                    <div class="col-6 text-right">
                                        <input name="c_othercharge" type="text" class="form-control" value="{{ $sale->othercharge }}">
                                    </div>
                                </div>
                                @if ($clientInfo->consumption_tax_plastic_bags == 1)
                                    <div class="row">
                                        <div class="col-6 text-right">
                                            <label>ICBPER</label>
                                        </div>
                                        <div class="col-6 text-right">
                                            {{-- <input name="c_t" id="c_t" type="text" class="form-control" readonly value="{{ $clientInfo->consumption_tax_plastic_bags_price }}"> --}}
                                            <input name="c_t" id="c_t" type="text" class="form-control" value="{{ $sale->icbper }}" readonly>
                                        </div>
                                    </div>
                                @endif
                                @if ($clientInfo->automatic_consumption_surcharge == 1)
                                    <div class="row">
                                        <div class="col-6 text-right">
                                            <label>RC ({{ $clientInfo->automatic_consumption_surcharge_price }} %)</label>
                                        </div>
                                        <div class="col-6 text-right">
                                            <input name="recharge" id="recharge" type="text" class="form-control" value="0.00" readonly>
                                            <input name="recharge_value" id="recharge_value" type="hidden" value="{{ $clientInfo->automatic_consumption_surcharge_price }}">
                                        </div>
                                    </div>
                                @else
                                    <input id="recharge" name="recharge" type="hidden" class="form-control" value="0.00">
                                    <input name="recharge_value" id="recharge_value" type="hidden" value="0">
                                @endif
                                <div class="row">
                                    <div class="col-6 text-right">
                                        <label>Total</label>
                                    </div>
                                    <div class="col-6 text-right">
                                        <input name="c_total" id="c_total" type="text" class="form-control" readonly value="{{ $sale->total }}">
                                        <input name="estateQuotation" id="estateQuotation" type="hidden" class="form-control" readonly >
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
                            @if ($type == 1)
                                <div class="col-6">
                                    <div class="form-group">
                                        @if($correlative != null)
                                            <button type="submit" name="draft" value="1" class="btn btn-secondary-custom btn-block generate">ACTUALIZAR BORRADOR</button>
                                        @else
                                            <div class="alert alert-danger alert-dismissible">
                                                <h5><i class="fa fa-ban"></i> Alerta</h5>
                                                PRIMERO DEBE CONFIGURAR UN CORRELATIVO PARA LAS VENTAS
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        @if($correlative != null)
                                            <button type="submit" name="commit" value="0" class="btn btn-primary-custom btn-block generate">GRABAR VENTA</button>
                                        @else
                                            <div class="alert alert-danger alert-dismissible">
                                                <h5><i class="fa fa-ban"></i> Alerta</h5>
                                                PRIMERO DEBE CONFIGURAR UN CORRELATIVO PARA LAS VENTAS
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="col-12">
                                    <div class="form-group">
                                        @if($correlative != null)
                                            <button type="submit" name="commit" class="btn btn-primary-custom btn-block generate">GRABAR VENTA</button>
                                        @else
                                            <div class="alert alert-danger alert-dismissible">
                                                <h5><i class="fa fa-ban"></i> Alerta</h5>
                                                PRIMERO DEBE CONFIGURAR UN CORRELATIVO PARA LAS VENTAS
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
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
                                            <label for="phone">Teléfono</label>
                                            <input id="phone" name="phone" type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-12">
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
                                            <input id="detraction" name="detraction" type="text" class="form-control" id="detraction">
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

    <div id="mdl_add_product" class="modal fade bd-example-modal-lg" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">NUEVO PRODUCTO/SERVICIO</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <form role="form" data-toggle="validator" id="frm_product">
                        <input type="hidden" id="product_id" name="product_id">
                        <div class="row">
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label for="category"> Categoría</label>
                                    <div class="input-group">
                                        <select name="category" id="category" class="form-control">
                                            <option value="">Sin Categoría</option>
                                            @if($categories->count() > 0)
                                                @foreach($categories as $c)
                                                    <option value="{{$c->id}}">{{$c->description}}</option>
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
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label for="type">Tipo*</label>
                                    <select name="type" id="type" class="form-control" required>
                                        <option value="">Seleccionar</option>
                                        @if($operations_type->count() > 0)
                                            @foreach($operations_type as $o)
                                                <option value="{{$o->id}}">{{$o->code}} - {{$o->description}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label for="brand"> Marca</label>
                                    <div class="input-group">
                                        <select name="brand" id="brand" class="form-control">
                                            <option value="">Sin Marca</option>
                                            @if($brands->count() > 0)
                                                @foreach($brands as $b)
                                                    <option value="{{$b->id}}">{{$b->description}}</option>
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
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label for="code"> Código de Barras</label>
                                    <input type="text" name="code" id="code" class="form-control">
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label for="internalcode"> Código interno*</label>
                                    <input type="text" name="internalcode" id="internalcode" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
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
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label for="coin">Código Producto SUNAT</label>
                                    <select name="code_sunat" id="code_sunat" class="form-control select3" style="width: 100%;">
                                        <option value="">Buscar</option>

                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-7">
                                <div class="form-group">
                                    <label for="description"> Nombre del Producto o Servicio*</label>
                                    <input type="text" name="description" id="pdescription" class="form-control">
                                </div>
                            </div>
                            <div class="col-12 col-md-1">
                                <label>Activo</label>
                                <div class="form-group">
                                    <input type="checkbox" checked data-toggle="toggle" value="1" data-on="Si" data-off="No" name="status" id="status" />
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
                            <div class="col-12 col-md-3 quantity">
                                <div class="form-group">
                                    <label for="quantity">Cantidad*</label>
                                    <input type="number" step="0.01" class="form-control" id="quantity" name="quantity" required>
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label for="cost">Costo*</label>
                                    <input type="number" step="0.01" class="form-control" id="cost" name="cost" required>
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label for="cost">Utilidad* (%)</label>
                                    <input type="number" step="0.01" class="form-control" id="utility" name="utility" required>
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label for="price">Valor Venta*</label>
                                    <input type="hidden" id="sale_value" name="sale_value">
                                    <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label>Exonerado</label>
                                    <input type="checkbox" value="1" name="exonerated" id="exonerated">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" id="save" class="btn btn-secondary-custom">
                                    <i class="fa fa-save"></i>
                                    GRABAR DATOS
                                </button>
                            </div>
                        </div>
                        <small>* Datos Obligatorios</small>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="mdl_add_category" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
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
    <div id="mdl_add_brand" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
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
                                            <input type="text" class="form-control" name="add_brand" id="add_brand" placeholder="Agregar Marca" required/>
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
@stop
@section('script_admin')
    <script>
        $(document).ready(function() {
            $('#c_t').val(0.00);
            if($('#c_t') === undefined) {$('#c_t').val(0.00)}
            $('#azSidebarToggle').click();

            $('#frm_quotation').keypress(function(e){   
                if(e.keyCode == 13){
                    e.preventDefault();
                }
            });

            $('body').on('keypress', 'input', function() {
                if(e.keyCode == 13){
                    e.preventDefault();
                }
            });
        });


        var igv = $('#igv').val();
        
        $("#customer").select2({
            placeholder: 'Buscar Cliente',
            allowClear: true
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
            if($(this).val() !== 2 && $(this).val() !== 4) {
                $("#validado").val(1);
            } else {
                $("#validado").val(1);
            }
        });

        $('body').on('change', '.type_igv', function() {
            recalculate();
        });


        $('body').on('keyup', '.c_quantity', function() {
            let c_subtotal = 0;
            let c_total = 0;
            let c_gravada = 0;
            let t = 0;
            let tr = $(this).parent().parent();
            let price = tr.find('.c_price').val();
            let total = parseFloat($(this).val() * price).toFixed(2);

            if ($('#c_t') == undefined) {
                t = 0.00;
            } else {
                t = parseFloat($('#c_t').val()).toFixed(2);
            }

            if ($('#pt').val() == 1) {
                let subtotal = parseFloat(total /(1 + (igv / 100))).toFixed(2);
                tr.find('.c_subtotal').val(subtotal);
                tr.find('.c_total').val(total);
            } else if($('#pt').val() == 0) {
                let c4 = total;
                let ihb = parseFloat(c4 * (1 + (igv / 100))).toFixed(2);
                tr.find('.c_subtotal').val(c4);
                tr.find('.c_total').val(ihb);
            }


            $('#tbl_products tbody tr').each(function(index, tr) {
                c_subtotal = parseFloat((c_subtotal * 1) + (($(tr).find('.c_total').val() * 1) - ($(tr).find('.c_subtotal').val() * 1))).toFixed(2);
                c_total = parseFloat((c_total * 1) + ($(tr).find('.c_total').val() * 1)).toFixed(2);
                c_gravada = parseFloat((c_gravada * 1) + ($(tr).find('.c_subtotal').val() * 1)).toFixed(2);;
            });

            let totalFinal = parseFloat(c_total) + parseFloat(t);


            $('#c_igv').val(c_subtotal);
            $('#c_total').val(totalFinal.toFixed(2));
            $('#c_taxed').val(c_gravada);

            recalculate();
        });

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
            let recharge = 0;

            let t = 0;

            if ($('#c_t') == undefined) {
                t = 0.00;
            } else {
                t = parseFloat($('#c_t').val()).toFixed(2);
            }
            if ($('#recharge') == undefined) {
                recharge = 0.00;
            } else {
                recharge = parseFloat($('#recharge').val()).toFixed(2);
            }

            $('#tbl_products tbody tr').each(function(index, tr) {
                console.log(tr);
                let product = $(tr).find('.c_product');
                let price = $(tr).find('.c_price').val();
                let quantity = $(tr).find('.c_quantity').val();
                let tigv = $(tr).find('.type_igv').val();
                let total = parseFloat((quantity * price)).toFixed(2);

                if ($('#pt').val() == 1) {
                    if (tigv == 2 || tigv == 3 || tigv == 4 || tigv == 5 || tigv == 6 || tigv == 7 || tigv == 10 || tigv == 11 || tigv == 12 ||
                        tigv == 13 || tigv == 14 || tigv == 15 || tigv == 17) {
                        $(tr).find('.c_subtotal').val(price);
                        $(tr).find('.c_total').val(price);

                        c_sum_free = parseFloat((c_sum_free * 1) + ($(tr).find('.c_total').val() * 1)).toFixed(2);
                    } else if(tigv == 8) {
                        let subtotal = parseFloat(total / (1 + (igv / 100))).toFixed(2);
                        $(tr).find('.c_subtotal').val(subtotal);
                        $(tr).find('.c_total').val(total);
                        /*$(tr).find('.c_subtotal').val(price);
                        $(tr).find('.c_total').val(price);*/

                        c_exonerated_gen = parseFloat((c_exonerated_gen * 1) + ($(tr).find('.c_total').val() * 1)).toFixed(2);
                        c_total_gen = parseFloat((c_total_gen * 1) + ($(tr).find('.c_total').val() * 1)).toFixed(2);
                    } else if(tigv == 9 || tigv == 16) {
                        $(tr).find('.c_subtotal').val(price);
                        $(tr).find('.c_total').val(price);

                        c_sum_unaffected = parseFloat((c_sum_unaffected * 1) + ($(tr).find('.c_total').val() * 1)).toFixed(2);
                        c_total_gen = parseFloat((c_total_gen * 1) + ($(tr).find('.c_total').val() * 1)).toFixed(2);
                    } else {
                        let subtotal = parseFloat(total / (1 + (igv / 100))).toFixed(2);
                        $(tr).find('.c_subtotal').val(subtotal);
                        $(tr).find('.c_total').val(total);

                        if($('option:selected', product).attr('p-exonerated') === '0') {
                            c_subtotal_gen = parseFloat((c_subtotal_gen * 1) + (($(tr).find('.c_total').val() * 1) - ($(tr).find('.c_subtotal').val() * 1))).toFixed(2);
                            c_total_gen = parseFloat((c_total_gen * 1) + ($(tr).find('.c_total').val() * 1)).toFixed(2);
                            c_gravada_gen = parseFloat((c_gravada_gen * 1) + ($(tr).find('.c_subtotal').val() * 1)).toFixed(2);
                        } else {
                            c_total_gen = parseFloat((c_total_gen * 1) + ($(tr).find('.c_total').val() * 1)).toFixed(2);
                            c_exonerated_gen = parseFloat((c_exonerated_gen * 1) + ($(tr).find('.c_total').val() * 1)).toFixed(2);
                        }
                    }

                    let rechargeValue = parseFloat($('#recharge_value').val()) / 100;

                    recharge = (parseFloat(c_total_gen) * parseFloat(rechargeValue)).toFixed(2);

                    totalFinal = (parseFloat(c_total_gen) + parseFloat(t) +  parseFloat(recharge) ).toFixed(2);

                    if ($('#c_percentage').val() == NaN || $('#c_percentage').val() == '' || $('#c_percentage').val() == undefined) {
                        c_percentage = 0.00;
                    } else {
                        c_percentage = parseFloat($('#c_percentage').val()).toFixed(2);
                        discount = ((parseFloat(totalFinal) * parseFloat(c_percentage)) / 100).toFixed(2)
                        totalFinal = (parseFloat(totalFinal) - parseFloat(discount));
                        c_gravada_gen = parseFloat(totalFinal / (1 + (igv / 100))).toFixed(2);
                        c_subtotal_gen = (totalFinal - c_gravada_gen).toFixed(2);
                    }
                } else if($('#pt').val() == 0) {
                    if (tigv == 2 || tigv == 3 || tigv == 4 || tigv == 5 || tigv == 6 || tigv == 7 || tigv == 10 || tigv == 11 || tigv == 12 ||
                        tigv == 13 || tigv == 14 || tigv == 15 || tigv == 17) {
                        $(tr).find('.c_subtotal').val(price);
                        $(tr).find('.c_total').val(price);

                        c_sum_free = parseFloat((c_sum_free * 1) + ($(tr).find('.c_total').val() * 1)).toFixed(2);
                    } else if(tigv == 8) {
                        $(tr).find('.c_subtotal').val(price);
                        $(tr).find('.c_total').val(price);

                        c_exonerated_gen = parseFloat((c_exonerated_gen * 1) + ($(tr).find('.c_total').val() * 1)).toFixed(2);
                        c_total_gen = parseFloat((c_total_gen * 1) + ($(tr).find('.c_total').val() * 1)).toFixed(2);
                    } else if(tigv == 9 || tigv == 16) {
                        $(tr).find('.c_subtotal').val(price);
                        $(tr).find('.c_total').val(price);

                        c_sum_unaffected = parseFloat((c_sum_unaffected * 1) + ($(tr).find('.c_total').val() * 1)).toFixed(2);
                        c_total_gen = parseFloat((c_total_gen * 1) + ($(tr).find('.c_total').val() * 1)).toFixed(2);
                    } else {
                        let c4 = total;
                        let ihb = parseFloat(c4 * (1 + (igv / 100))).toFixed(2);
                        $(tr).find('.c_subtotal').val(c4);
                        $(tr).find('.c_total').val(ihb);

                        if($('option:selected', product).attr('p-exonerated') === '0') {
                            c_subtotal_gen = parseFloat((c_subtotal_gen * 1) + (($(tr).find('.c_total').val() * 1) - ($(tr).find('.c_subtotal').val() * 1))).toFixed(2);
                            c_total_gen = parseFloat((c_total_gen * 1) + ($(tr).find('.c_total').val() * 1)).toFixed(2);
                            c_gravada_gen = parseFloat((c_gravada_gen * 1) + ($(tr).find('.c_subtotal').val() * 1)).toFixed(2);
                        } else {
                            c_total_gen = parseFloat((c_total_gen * 1) + ($(tr).find('.c_total').val() * 1)).toFixed(2);
                            c_exonerated_gen = parseFloat((c_exonerated_gen * 1) + ($(tr).find('.c_total').val() * 1)).toFixed(2);
                        }
                    }

                    let rechargeValue = parseFloat($('#recharge_value').val()) / 100;


                    recharge = (parseFloat(c_total_gen) * parseFloat(rechargeValue)).toFixed(2);

                    totalFinal = (parseFloat(c_total_gen) + parseFloat(t) +  parseFloat(recharge) ).toFixed(2);

                    console.log(totalFinal)

                    if ($('#c_percentage').val() == NaN || $('#c_percentage').val() == '' || $('#c_percentage').val() == undefined) {
                        c_percentage = 0.00;
                    } else {
                        c_percentage = parseFloat($('#c_percentage').val()).toFixed(2);
                        discount = ((parseFloat(totalFinal) * parseFloat(c_percentage)) / 100).toFixed(2)
                        totalFinal = (parseFloat(totalFinal) - parseFloat(discount)).toFixed(2);
                        c_gravada_gen = parseFloat(totalFinal / (1 + (igv / 100))).toFixed(2);
                        c_subtotal_gen = (totalFinal - c_gravada_gen).toFixed(2);
                    }
                }
            });

            $('#c_igv').val(c_subtotal_gen);
            $('#c_total').val(totalFinal);
            $('#c_taxed').val(c_gravada_gen);
            $('#exonerated').val(c_exonerated_gen);
            $('#c_free').val(c_sum_free);
            $('#c_unaffected').val(c_sum_unaffected);
            $('#c_discount').val(discount);
            $('#recharge').val(recharge);
        }

        $('body').on('keyup', '.c_price', function() {
            let c_subtotal = 0;
            let c_total = 0;
            let c_gravada = 0;
            let t = 0;
            let tr = $('.c_quantity').parent().parent();
            let price = parseFloat(tr.find(this).val()).toFixed(2);
            let total = parseFloat(($('.c_quantity').val() * price)).toFixed(2);


            if ($('#c_t') == undefined) {
                t = 0.00;
            } else {
                t = parseFloat($('#c_t').val()).toFixed(2);
            }

            if ($('#pt').val() == 1) {
                let subtotal = parseFloat(total /(1 + (igv / 100))).toFixed(2);
                tr.find('.c_subtotal').val(subtotal);
                tr.find('.c_total').val(total);
            } else if($('#pt').val() == 0) {
                let c4 = total;
                let ihb = parseFloat(c4 * (1 + (igv / 100))).toFixed(2);
                tr.find('.c_subtotal').val(c4);
                tr.find('.c_total').val(ihb);
            }

            $('#tbl_products tbody tr').each(function(index, tr) {
                c_subtotal = parseFloat((c_subtotal * 1) + (($(tr).find('.c_total').val() * 1) - ($(tr).find('.c_subtotal').val() * 1))).toFixed(2);
                c_total = parseFloat((c_total * 1) + ($(tr).find('.c_total').val() * 1)).toFixed(2);
                c_gravada = parseFloat((c_gravada * 1) + ($(tr).find('.c_subtotal').val() * 1)).toFixed(2);;
            });

            let totalFinal = parseFloat(c_total) + parseFloat(t);


            $('#c_igv').val(c_subtotal);
            $('#c_total').val(totalFinal.toFixed(2));
            $('#c_taxed').val(c_gravada);
            recalculate();
        });

        $('body').on('change', '.c_product', function() {
            let tr = $(this).parent().parent().parent();
            tr.find('.c_price').val($('option:selected', this).attr('p-price'));
            tr.find('.c_stock').val($('option:selected', this).attr('p-stock'));
            tr.find('.c_quantity').keyup();
            tr.find('.c_quantity').focus();

            tr = $(this).parent().parent().parent();
            tr.find('.c_price').val($('option:selected', this).attr('p-price'));
            tr.find('.c_quantity').keyup();
            tr.find('.c_quantity').focus();

            if ($('option:selected', this).attr('p-stock') < 1) {
                toastr.warning('Cantidad insuficiente.');
            }

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
                        if ($('#detraction').parent().hasClass('off')) {
                            $('#detraction').parent().click();
                        }
                    }
                }
            }

            if (isService == 22) {
                icbperTotal = ($('#pbp').val()  * tr.find('.c_quantity').val());
                $('#c_t').val(icbperTotal.toFixed(2));
            }
        });

        let isDetraccion;
        $('body').on('keyup', '.c_quantity', function() {
            let tr = $(this).parent().parent();
            let product = tr.find('.c_product');
            let isService = $('option:selected', product).attr('p-otype');
            let isExonerated = $('option:selected', product).attr('p-otype');
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
                        if ($('#detraction').parent().hasClass('off')) {
                            $('#detraction').parent().click();
                        }
                    }
                }
            }
            if (isService == 22) {
                icbperTotal = ($('#pbp').val()  * tr.find('.c_quantity').val());
                $('#c_t').val(icbperTotal.toFixed(2));
            }

            recalculate();
        });

        $("#detraction").on( 'change', function() {
            if( $(this).is(':checked') ) {
                $("#t_detraction").val('Operación sujeta al Sistema de Pago de Obligaciones Tributarias: '+ bank +' CTA. DETRACCIÓN Nº ' + account + '. ');
            } else {
                $("#t_detraction").val('');
            }
        });

        $('body').on('change', '.c_price', function() {
            let tr = $(this).parent().parent();
            let quantity = tr.find('.c_quantity');

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
                        if ($('#detraction').parent().hasClass('off')) {
                            $('#detraction').parent().click();
                        }
                    }
                }  else {
                    if (!$('#detraction').parent().hasClass('off')) {
                        $('#detraction').parent().click();
                    }
                }
            }
            recalculate();
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

        function clearDataProduct() {
            $('#category').val('');
            $('#measure').val('');
            $('#brand').val('');
            $('#code').val('');
            $('#internalcode').val('');
            $('#pdescription').val('');
            $('#product_id').val('');
            $('#quantity').val('');
            $('#cost').val('');
            $('#price').val('');
            $('#type').val('');
            $('#coin').val('1');
        }

        $('body').on('click', '.openModalProduct', function() {
            clearDataProduct();
            $('#mdl_add_product').modal('show');
        });
        $('.btnAddCategory').click(function() {
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

        $("#service_region").on( 'change', function() {
            let oldigv = $('#c_igv').val();
            if( $(this).is(':checked') ) {
                $('#exonerated').val(oldigv);
                $('#c_igv').val('0.00');
                let taxed = $('#c_taxed').val();
                $('#c_total').val(taxed);
            } else {
                recalculate();
            }
        });
        $("#product_region").on( 'change', function() {
            let oldigv = $('#c_igv').val();
            if( $(this).is(':checked') ) {
                $('#exonerated').val(oldigv);
                $('#c_igv').val('0.00');
                let taxed = $('#c_taxed').val();
                $('#c_total').val(taxed);
            } else {
                recalculate();
            }
        });

        $('#quantity').on('keyup', function() {
            calculateAmount();
        });

        var products = "";
        var igvs = "";
        // function getdsds() {
            $.post('/commercial.quotations.products', '_token=' +  '{{ csrf_token() }}', function(response) {
                for (var i = 0; i < response.length; i++) {
                    products += '<option value="' + response[i].id + '" p-stock="' + response[i].stock + '" p-price="' + response[i].price + '" p-otype="' + response[i].operation_type + '" p-exonerated="' + response[i].exonerated + '">' + response[i].internalcode + ' - ' + response[i].description + '</option>';
                }
            }, 'json');

            $.post('/commercial.sales/gettypeigv', '_token=' +  '{{ csrf_token() }}', function(response) {
                for (var i = 0; i < response.length; i++) {
                    igvs += '<option value="' + response[i].id + '">'+ response[i].description + '</option>';
                }

                console.log(response);
            }, 'json');
        // }  


        $('#btnAddProduct').on('click', function() {
            // getdsds();
            let le = '';
            if ($('#le').val() == 1) {
                le = '<div class="input-group-append"><button class="btn btn-primary-custom openModalProduct" type="button" id=""><i class="fa fa-plus"></i></button></div> </div>'
            }
            let data = '<tr>';
            data += '<td>'+ `
                        <div class="input-group">
                            <select style="width: 80%;" class="form-control select_2 c_product" id="c_product" name="cd_product[]">
                            <option value="">Seleccionar Producto</option>
                            `
                            + products +
                            `
                            </select>
                            `
                            + '</td>';
            data += '<td><input type="text" class="form-control c_detail" name="cd_detail[]"/></td>';
            data += '<td><input type="number" class="form-control c_quantity" name="cd_quantity[]" value="1"/></td>';
            data += '<td><input type="text" class="form-control c_stock" name="c_stock[]" value="0" readonly/></td>';
            data += '<td><select class="form-control type_igv" name="type_igv[]" id="type_igv">'+ igvs +'</select></td>';
            data += '<td><select name="cd_price[]" class="form-control c_price"></select></td>';
            data += '<td><input type="text" class="form-control c_subtotal" name="cd_subtotal[]" value="0" readonly /></td>';
            data += '<td><input type="text" class="form-control c_total" name="cd_total[]" value="0" readonly /></td>';
            data += '<td>';
            data += '<button type="button" class="btn btn-danger-custom remove"><i class="fa fa-minus"></i></button>';
            data += '</td>';

            data += '</tr>';
            $('#tbl_products tbody').append(data);

            $('.select_2').select2();
        });

        $('body').on('click', '.remove', function() {
            $(this).parent().parent().remove();
            let c_subtotal = 0;
            let c_total = 0;
            let c_gravada = 0;
            let t = 0;

            if ($('#c_t') === undefined) {
                let t = 0.00;
            } else {
                let t = $('#c_t').val();
            }
            let icbperTotal = 0;
            $('.c_product').each(function() {
                let tr = $(this).parent().parent();
                let product = tr.find('.c_product');
                let isBGP = $('option:selected', product).attr('p-otype');
                let BGPprice = $('option:selected', product).attr('p-price');

                if (isBGP == 21) {
                    icbperTotal = ($(this).val()  * BGPprice) + icbperTotal;
                } else {
                    icbperTotal = 0 + icbperTotal;
                }
            });
            $('#c_t').val(icbperTotal.toFixed(2));

            $('#tbl_products tbody tr').each(function() {
                let tr = $(this);
                c_subtotal = (c_subtotal * 1) + (($(tr).find('.c_total').val() * 1) - ($(tr).find('.c_subtotal').val() * 1));
                c_total = (c_total * 1) + ($(tr).find('.c_total').val() * 1) + (t * 1);
                c_gravada = (c_gravada * 1) + ($(tr).find('.c_subtotal').val() * 1);
            });

            $('#c_igv').val((c_subtotal*1).toFixed(2));
            $('#c_total').val(c_total.toFixed(2));
            $('#c_taxed').val((c_gravada * 1).toFixed(2));
            $('#c_exonerated').val(0.00);
        });

        $('#addReferralGuide').on('click', function() {
            let guides =    `<div class="row">
                                <div class="col-12 col-md-6">
                                    <label for="g_serialnumber">Serie - Número</label>
                                    <input type="text" id="g_serialnumber" name="g_serialnumber[]" class="form-control">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="g_type">Tipo</label>
                                    <select name="g_type[]" id="g_type" class="form-control">
                                        <option value="">- Seleccionar -</option>
                                        <option value="1">GUÍA DE REMISIÓN REMITENTE</option>
                                        <option value="2">GUÍA DE REMISIÓN TRANSPORTISTA</option>
                                    </select>
                                </div>
                            </div>`;
            $('#cont-guides').append(guides);
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

        $('#frm_product').validator().on('submit', function(e) {
            if(e.isDefaultPrevented()) {
                toastr.warning('Debe llenar todos los campos obligatorios');
            } else {
                e.preventDefault();
                let data = $('#frm_product').serialize();
                $.ajax({
                    url: '/warehouse.product.save',
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
                            toastr.success('Se grabó satisfactoriamente el producto');
                            $("#tbl_data").DataTable().ajax.reload();
                            clearDataProduct();
                            $('#mdl_add_product').modal('hide');
                            $.post('/commercial.quotations.products', '_token=' +  '{{ csrf_token() }}', function(response) {
                                let option = '<option>Seleccionar Producto</option>';
                                for (var i = 0; i < response.length; i++) {
                                    option += '<option value="' + response[i].id + '" p-stock="' + response[i].stock + '" p-price="' + response[i].price + '" p-otype="' + response[i].operation_type + '">';
                                    option += response[i].description + '</option>';
                                }

                                $('.c_product').html('');
                                $('.c_product').append(option);

                            }, 'json');

                            // products = '<div class="form-group"><div class="input-group"><select style="width: 80%;"';
                            // products += 'class="form-control select_2 c_product" id="c_product" name="cd_product[]">';
                            // products += '<option value="">Seleccionar Producto</option>'
                            // $.post('/commercial.quotations.products', '_token=' +  '{{ csrf_token() }}', function(response) {
                            //     for (var i = 0; i < response.length; i++) {
                            //         products += '<option value="' + response[i].id + '" p-stock="' + response[i].stock + '" p-price="' + response[i].price + '" p-otype="' + response[i].operation_type + '">';
                            //         products += response[i].description + '</option>';
                            //     }

                            //     products += '</select> <div class="input-group-append">';
                            //     products += '<button class="btn btn-primary-custom openModalProduct" type="button" id="">';
                            //     products += '<i class="fa fa-plus"></i></button>';
                            //     products += '</div> </div></div>';

                            // }, 'json');
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

        let typeGenerate;
        $('body').on('click', '.generate', function() {
           typeGenerate = $(this).attr('name');
        });

        $('#frm_quotation').on('submit', function(e) {
            if(e.isDefaultPrevented()) {
                toastr.warning('Debe llenar todos los campos obligatorios');
            } else {
                e.preventDefault();
                let data = $('#frm_quotation').serialize();

                if($('#tbl_products tbody tr').length == 0) {
                    toastr.error('Debe seleccionar algún producto o servicio');
                    return false;
                }

                $.ajax({
                    url: '/commercial.sales.update',
                    type: 'post',
                    data: data + '&typegenerate=' + typeGenerate + '&_token=' + '{{ csrf_token() }}',
                    dataType: 'json',
                    beforeSend: function() {
                        $('#btnGrabarCliente').attr('disabled');
                    },
                    complete: function() {

                    },
                    success: function(response) {
                        if(response['response'] == true) {
                            toastr.success('Se grabó satisfactoriamente el cliente');
                            window.location = '/commercial.sales';
                        } else if(response['response'] == -2) {
                            toastr.error('Uno o más productos no alcanzan el stock suficiente');
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

        $("#frm_cliente").validator().on('submit', function(e) {
            if (e.isDefaultPrevented()) {
                toastr.warning('Debe llenar todos los campos obligatorios.');
            } else {
                e.preventDefault();
                let data = $('#frm_cliente').serialize();

                if($('#validado').val() == 0){
                    toastr.error('El Cliente que intenta registrar no está validado');
                } else {
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
                            if(response == true) {
                                toastr.success('Se grabó satisfactoriamente el cliente');
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
                        $('#address').val(response['direccion'] + ' - ' + response['provincia'] + ' - ' + response['distrito']);
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
        }

        function calculateAmount()
        {
            let subtotal = $('#quantity').val() * $('#price').val();
            $('#subtotal').val(subtotal.toFixed(2));
            let total = subtotal - $('#igvitem').val();
            $('#total').val(total.toFixed(2));
        }

        $('#serialnumber').change(function() {
            let serialnumber = $(this).val();
            let type = $('#typevoucher_id').val();

            console.log(serialnumber + ' ' + type);

            $.ajax({
                url: '/sales/getcorrelative/' + serialnumber + '/' + type,
                type: 'post',
                data: {
                    '_token': "{{ csrf_token() }}"
                },
                dataType: 'json',
                success: function(response) {
                    console.log(response);
                    $('#correlative').val(('0000') + ((response.correlative)*1 + 1));
                },
                error: function(response) {
                    console.log(response);
                }
            });
        });

        $('.select_2').select2();
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

        var date = new Date();
        let enddate;

        if ($('#ipd').val() == 1) {
            let nd = parseInt($('#ipnd').val());
            date.setDate(date.getDate()-nd);
            enddate = date + nd;
        } else {
            date.setDate(date.getDate());
            enddate = date;
        }

        $('#date').datepicker({ 
            format: 'dd-mm-yyyy',
            autoclose: true,
            startDate: date,
            endDate: enddate
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

                    recalculate();
                }
            });

            recalculate();
        });
    </script>
@stop
