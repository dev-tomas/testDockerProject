@extends('layouts.azia')
@section('css')
    <style>.is-free-container{display: none}</style>
    @can('ventas.bonificaciones')
        <style>.is-free-container{display: inline-block;}</style>
    @endcan
    <style>
        #tbl_products tbody tr td { padding-left: 0;padding-right: 0;}
        #tbl_products tbody tr td input,#tbl_products tbody tr td select{ padding-left: 3px;padding-right: 1px;}
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
        <input type="hidden" value="{{$clientInfo->consumption_tax_plastic_bags}}" id="pbH" />
        <input type="hidden" value="{{$clientInfo->consumption_tax_plastic_bags_price}}" id="pbp" />
        <input type="hidden" value="{{$clientInfo->issue_with_previous_data}}" id="ipd" />
        <input type="hidden" value="{{$clientInfo->issue_with_previous_data_days}}" id="ipnd" />
        <input type="hidden" value="{{$clientInfo->exchange_rate_sale}}" id="ers" />
        <input type="hidden" value="{{ auth()->user()->is_supervisor }}" id="is_supervisor" />
        <div class="row">
            <div class="col-12">
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title text-center">
                            {{-- <a href="{{ route('commercial.sales') }}"> --}}
                                @if ($type === 1)
                                    NUEVA FACTURA
                                @elseif ($type === 2)
                                    NUEVA BOLETA DE VENTA
                                @endif
                            {{-- </a> --}}
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-5">
                                <div class="form-group">
                                    <label for="customer"> </label>
                                    <div class="input-group">
                                        <select name="customer" id="customer" class="form-control" style="width: 80%;" required>
                                            @if ($type == 2)
                                                @foreach($customers as $c)
                                                    <option value="{{$c->id}}" {{ $c->typedocument_id == 6 ? 'selected' : '' }} data-email="{{ $c->email }}">{{$c->document}} - {{$c->description}}</option>
                                                @endforeach
                                            @else
                                                @foreach($customers as $c)
                                                    <option value="{{$c->id}}" data-email="{{ $c->email }}">{{$c->document}} - {{$c->description}}</option>
                                                @endforeach
                                            @endif
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
                                    <select class="form-control" name="serialnumber" id="serialnumber">
                                        <option value="">Selecciones una Serie</option>
                                        @foreach ($correlative as $c)
                                            <option value="{{ $c->serialnumber }}" {{ $loop->first ? "selected" : "" }}>{{ $c->serialnumber }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-2">
                                <div class="form-group">
                                    <label for="typevoucher">Número (Referencial)</label>
                                    <input type="text" name="correlative" id="correlative" class="form-control" disabled>
                                    {{-- <select class="form-control" readonly="" name="correlative" id="correlative"></select> --}}
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
                                        <input value="{{$currentDate}}" type="text" class="form-control datepick" name="date" id="date" autocomplete="off">
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
                                        <option value="{{$o->id}}" {{ $o->id == 1 ? 'selected' : '' }}>{{$o->operation}}</option>
                                    @endforeach
                                @endif
                            </select>
                            </div>
                            <div class="col-12 col-md-1">
                                <label for="coin">Moneda</label>
                                <select name="coin" id="coin" class="form-control">
                                    @if($coins->count() > 0)
                                        @foreach($coins as $c)
                                            <option value="{{$c->id}}">{{$c->symbol}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-6 col-md-1">
                                <label for="exchange_rate">Tipo cambio</label>
                                <div class="form-group">
                                    <input type="text" class="form-control" name="change_type" id="exchange_rate" readonly>
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
                            <div class="col-12 col-md-2">
                                <div class="form-group">
                                    <label for="condition">Forma de pago</label>
                                    <select name="condition" id="condition" class="form-control">
                                        <option value="EFECTIVO" data-days="0">EFECTIVO</option>
                                        <option value="DEPOSITO EN CUENTA" data-days="0">DEPOSITO EN CUENTA</option>
                                        <option value="CREDITO" data-days="7">CREDITO</option>
                                        <option value="TARJETA DE CREDITO" data-days="0">TARJETA DE CREDITO</option>
                                        <option value="TARJETA DE DEBITO" data-days="0">TARJETA DE DEBITO</option>
                                        <option value="DEVOLUCION" data-days="0">DEVOLUCION</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-1">
                                <div class="form-group" id="contCreditBalance">
                                    <label>Monto</label>
                                    <input type="text" class="form-control" id="mountPayment" value="0.00" name="mountPayment" readonly>
                                </div>
                            </div>
                            <div class="col-12 col-md-1" id="contOptionDepositoCofre">
                                <div class="form-group">
                                    <label>Cofre</label>
                                    <select name="cash" id="cash" class="form-control">
                                        @foreach ($cashes as $cash)
                                            <option value="{{ $cash->id }}">{{ $cash->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-1" id="contOptionDepositoCuenta">
                                <div class="form-group">
                                    <label>Cuenta</label>
                                    <select name="bank" id="bankOption" class="form-control">
                                        @foreach ($bankAccounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->bank_name }} - {{ $account->number }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-1" id="contOptionDepositoPOS">
                                <div class="form-group">
                                    <label>Medio de Pago</label>
                                    <select name="mp" id="methodOption" class="form-control">
                                        @foreach ($paymentMethods as $paymentMethod)
                                            <option value="{{ $paymentMethod->id }}">{{ $paymentMethod->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-1">
                                <label for="exchange_rate"></label>
                                <div class="form-group">
                                    <button type="button" id="showPayments" class="btn btn-primary-custom btn-sm"><i class="fa fa-eye"></i></button>
                                    <button type="button" id="addOptionPayment" class="btn btn-primary-custom btn-sm"><i class="fa fa-plus"></i></button>
                                    <button type="button" id="deleteOptionPayment" class="btn btn-danger-custom btn-sm"><i class="fa fa-minus"></i></button>
                                </div>
                            </div>
                            <div class="col-12 col-md-2" id="credit_note_return_container">
                                <label>Notas de Crédito</label>
                                <select class="form-control select_2" name="credit_note_return" id="credit_note_return" >
                                    <option value="">Selecciona una Nota de Crédito</option>
                                    @foreach($returners as $returner)
                                        <option value="{{ $returner->id }}" data-total="{{ $returner->total }}">{{ $returner->serial_number }}-{{ $returner->correlative }} S/ {{ $returner->total }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-1">
                                <div class="form-group">
                                    <label for="order">O/C</label>
                                    <input type="text" class="form-control" name="order" id="order" />
                                </div>
                            </div>
                        </div>
                        <div class="row"  id="otherMethodPaymentCont">
                            
                        </div>
                        <fieldset>
                            <div class="row">
                                <div class="col-12 table-responsive">
                                    <table class="table" id="tbl_products">
                                        <thead>
                                        <th width="400px">Producto</th>
                                        <th width="90px">Cantidad</th>
                                        <th width="100px">Stock</th>
                                        <th width="100px">Precio</th>
                                        <th width="100px">SubTotal</th>
                                        <th width="100px">Total</th>
                                        <th width="10px">*</th>
                                        </thead>
                                        <tbody class="table-sales">
                                            <tr>
                                                <td>
                                                    <select style="width: 100%;" class="form-control select_2 c_product" id="c_product" name="cd_product[]" >
                                                        <option value="0">Seleccionar Producto</option>
                                                        @if($products->count() > 0)
                                                            @foreach($products as $p)
                                                                <option value="{{$p->id}}" p-it="{{ $p->priceIncludeRC }}"
                                                                        p-taxbase="{{ $p->taxbase }}" p-tax="{{ $p->tax }}"
                                                                        p-type_product="{{$p->type_product}}" p-stock="{{$p->stock}}"
                                                                        p-igv-type="{{ $p->type_igv_id }}" p-price="{{$p->price}}"
                                                                        p-otype='{{ $p->operation_type }}' p-exonerated="{{ $p->exonerated }}"
                                                                        p-is-kit='{{ $p->is_kit }}'>
                                                                    {{ $p->internalcode }} - {{$p->description}}
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                    <br>
                                                    <label class="is-free-container">
                                                        <input type="checkbox" class="is-free"> Es Bonificación
                                                    </label>
                                                    <input type="hidden" name="is_free[]" class="is_free_c" value="0">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control c_quantity" name="cd_quantity[]" value="1" />
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control c_stock" name="c_stock[]" value="0" readonly />
                                                </td>
                                                <td class="priceCell">
                                                    {{-- <input type="number" class="form-control c_price" name="cd_price[]" value="0" /> --}}
                                                    <select name="cd_price[]" class="form-control c_price"></select>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control c_subtotal" name="cd_subtotal[]" value="0" readonly />
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control c_total" name="cd_total[]" value="0" readonly />
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger-custom btn-rounded remove"><i class="fa fa-minus"></i></button>
                                                </td>
                                            </tr>
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
                                            <textarea name="observation" id="observation" cols="30" rows="4" class="form-control"></textarea>
                                            <input type="text" name="t_detraction" id="t_detraction" class="form-control" readonly>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label class="ckbox">
                                            <input type="checkbox" readonly value="1" name="detraction" id="detraction"><span>¿Detracción?</span>
                                        </label>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="ckbox">
                                            <input type="checkbox" value="1" name="product_region" id="product_region"><span>¿Bienes Región Selva?</span>
                                          </label>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="ckbox">
                                            <input type="checkbox" value="1"  name="service_region" id="service_region"><span>¿Servicios Región Selva?</span>
                                          </label>
                                    </div>
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
                                 --}}
                                <div class="row">
                                    <div class="col-3">
                                        <button class="btn btn-primary-custom float-right" type="button" id="showSupervisorModal" >
                                            <i class="fa fa-lock"></i>
                                        </button>
                                    </div>
                                    <div class="col-3 text-right">
                                        <label>Descuento (-)</label>
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
                                </div>
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
                                            <input name="c_t" id="c_t" type="text" class="form-control" readonly>
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
                            @if ($type == 1)
                                {{-- <div class="col-6">
                                    <div class="form-group">
                                        @if($correlative != null)
                                            <button type="submit" name="draft" value="1" class="btn btn-secondary-custom btn-block generate">GUARDAR COMO BORRADOR</button>
                                        @else
                                            <div class="alert alert-danger alert-dismissible">
                                                <h5><i class="fa fa-ban"></i> Alerta</h5>
                                                PRIMERO DEBE CONFIGURAR UN CORRELATIVO PARA LAS VENTAS
                                            </div>
                                        @endif
                                    </div>
                                </div> --}}
                                <div class="col-12">
                                    <div class="form-group">
                                        @if($correlative != null)
                                            <button type="submit" name="commit" id="btnSaveSale" value="0" class="btn btn-primary-custom btn-block generate">GRABAR VENTA</button>
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

        <div class="modal fade" id="creditMdl" tabindex="-1" data-toggle="modal" data-target="#staticBackdrop" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                        <button type="button" class="btn btn-secondary" id="closeCreditMdl">Cerrar</button>
                        <button type="button" type="button" id="generateDues" class="btn btn-primary">Generar</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <form id="frm_cliente" method="post" class="form-horizontal" role="form" data-toggle="validator">
        <input type="hidden" id="validado" value="0">
        <div id="mdl_add_cliente" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="z-index: 9999;">
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

    <div id="mdl_add_product" class="modal fade bd-example-modal-lg" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="z-index: 9999;">
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
    <div class="modal fade" id="validateSupervisorModal" tabindex="-1" aria-labelledby="validateSupervisorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="frm_validate">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="validateSupervisorModalLabel">Validar</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Pin supervisor</label>
                                    <input type="password" class="form-control" name="pin" id="pin" min="00000000" max="99999999" step="00000001" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Validar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop
@section('script_admin')
    <script>
        var click = false;
        $(document).ready(function() {
            $('#c_t').val(0.00);
            // $('#recharge').val(0.00);
            // if($('#c_t').val() === undefined) {$('#c_t').val(0.00)}
            getCorrelative();
            $('#azSidebarToggle').click();
            getCustomerEmail();
            $('#frm_quotation').keypress(function(e){   
                if(e.keyCode == 13){
                    e.preventDefault();
                }
            });
            $('body').on('keypress', 'input', function(e) {
                if(e.keyCode == 13){
                    e.preventDefault();
                }
            });
            $('#deleteOptionPayment').hide();
            $('#contOptionDepositoCuenta').hide();
            $('#contOptionDepositoPOS').hide();
            $('#showPayments').hide();
            $('#credit_note_return_container').hide();
        });
        $('#customer').change(function() {
            getCustomerEmail();
            getCreditNoteReturned();
        });
        function getCustomerEmail() {
            let email = $('#customer option:selected').data('email');
            $('#customerEmail').val(email);
        }
        function getCreditNoteReturned() {
            $('#condition').val('EFECTIVO').change();
            $.ajax({
                url: '/get-credit-note-returned',
                type: 'post',
                data: {
                    'customer': $('#customer').val(),
                    '_token': "{{ csrf_token() }}"
                },
                dataType: 'json',
                success: function(response) {
                    $('#credit_note_return').html('');
                    if (response.length > 0) {
                        let options = '<option value="">Selecciona una Nota de Crédito</option>';
                        $.each(response, function(index, el) {
                            options += `<option value="${ el.id }" data-total="${ el.total }">${ el.serial_number }-${ el.correlative } S/ ${ el.total }</option>`
                        })

                        $('#credit_note_return').append(options);
                        if ($('#condition option[value="DEVOLUCION"]').length === 0) {
                            $('#condition').append('<option value="DEVOLUCION" data-days="0">DEVOLUCION</option>');
                        }
                    } else {
                        $('#condition option[value="DEVOLUCION"]').remove();
                    }

                    console.log(response)
                },
                error: function(response) {
                    console.log(response);
                }
            });
        }

        $('#condition').change(function () {
            let days = $('#condition option:selected').data('days');
            let currentDate = $('#expiration').val();
            let date = moment().add(days, 'days').format('DD-MM-YYYY');
            $('#expiration').val(date);
            $('#expiration').trigger('changeDate');
            $('#showPayments').hide();
            if ($(this).val() == 'EFECTIVO') {
                $('#contOptionDepositoCofre').show();
                $('#contOptionDepositoCofre div select').attr('disabled', false);
                $('#contOptionDepositoCuenta').hide();
                $('#contOptionDepositoCuenta div select').attr('disabled', true);
                $('#contOptionDepositoPOS').hide();
                $('#contOptionDepositoPOS div select').attr('disabled', true);
                $('#credit_note_return_container').hide();
                $('#credit_note_return').attr('disabled', true);
                $('#showPayments').hide();
            } else if ($(this).val() == 'DEPOSITO EN CUENTA') {
                $('#contOptionDepositoCofre').hide();
                $('#contOptionDepositoCofre div select').attr('disabled', true);
                $('#contOptionDepositoCuenta').show();
                $('#contOptionDepositoCuenta div select').attr('disabled', false);
                $('#contOptionDepositoPOS').hide();
                $('#contOptionDepositoPOS div select').attr('disabled', true);
                $('#credit_note_return_container').hide();
                $('#credit_note_return').attr('disabled', true);
                $('#showPayments').hide();
            } else if ($(this).val() == 'TARJETA DE CREDITO' || $(this).val() == 'TARJETA DE DEBITO') {
                $('#contOptionDepositoCofre').hide();
                $('#contOptionDepositoCofre div select').attr('disabled', true);
                $('#contOptionDepositoCuenta').hide();
                $('#contOptionDepositoCuenta div select').attr('disabled', true);
                $('#contOptionDepositoPOS').show();
                $('#contOptionDepositoPOS div select').attr('disabled', false);
                $('#credit_note_return_container').hide();
                $('#credit_note_return').attr('disabled', true);
                $('#showPayments').hide();
            } else if ($(this).val() == 'CREDITO') {
                $('#contOptionDepositoCofre').hide();
                $('#contOptionDepositoCofre div select').attr('disabled', true);
                $('#contOptionDepositoCuenta').hide();
                $('#contOptionDepositoCuenta div select').attr('disabled', true);
                $('#contOptionDepositoPOS').hide();
                $('#contOptionDepositoPOS div select').attr('disabled', true);
                $('#amountPendingModal').val($('#mountPayment').val())
                $('#credit_note_return_container').hide();
                $('#credit_note_return').attr('disabled', true);
                $('#showPayments').show();
                $('#creditMdl').modal('show');
            } else if ($(this).val() == 'DEVOLUCION') {
                $('#credit_note_return_container').show();
                $('#credit_note_return').attr('disabled', false);
                $('#contOptionDepositoCofre').show();
                $('#contOptionDepositoCofre div select').attr('disabled', false);
                $('#contOptionDepositoCuenta').hide();
                $('#contOptionDepositoCuenta div select').attr('disabled', true);
                $('#contOptionDepositoPOS').hide();
                $('#contOptionDepositoPOS div select').attr('disabled', true);
                $('#showPayments').hide();
            } else {
                $('#contOptionDepositoCofre').hide();
                $('#contOptionDepositoCofre div select').attr('disabled', true);
                $('#contOptionDepositoCuenta').hide();
                $('#contOptionDepositoCuenta div select').attr('disabled', true);
                $('#contOptionDepositoPOS').hide();
                $('#contOptionDepositoPOS div select').attr('disabled', true);
                $('#credit_note_return_container').hide();
                $('#credit_note_return').attr('disabled', true);
                $('#showPayments').hide();
            }
        });

        $('#generateDues').click(function() {
            generateDues();
        });

        $('#closeCreditMdl').click(function() {
            let status = true;
            let totalDues = 0;

            if ($('.payment_amount').length < 1) {
                status = false;
                toastr.error('Debe de agregar al menos una cuota.')
            }

            $('.payment_amount').each(function(i, el) {
                if ($(el).val() == '' || $(el).val() <= 0) {
                    status = false;
                } else {
                    totalDues = parseFloat(totalDues) + parseFloat($(el).val());
                }
            })

            let totalDays = parseInt($('#dueFrecuency').val()) * parseInt($('#numberDues').val());
            let expirationDate = moment().add(totalDays, 'days').format('DD-MM-YYYY');

            if (! moment(expirationDate, 'DD-MM-YYYY').isValid()) {
                status = false;
            }

            if (parseFloat($('#amountPendingModal').val()).toFixed(2) != parseFloat(totalDues).toFixed(2)) {
                status = false;
            }

            if (status) {
                $('#expiration').val(expirationDate)
                $('#creditMdl').modal('hide');
            } else {
                toastr.error('Verifique los montos de cada pago.')
                return;
            }
        });

        $('body').on('click','#showPayments', function(e) {
            e.preventDefault()
            $('#amountPendingModal').val($('#mountPayment').val())
            $('#creditMdl').modal('show');
        })

        function generateDues() {
            $('#payments_lists').html(''); 
            let frecuency = $('#dueFrecuency').val();
            let numberDues = $('#numberDues').val();
            let pending = $('#amountPendingModal').val();

            let due = parseFloat(pending) / parseFloat(numberDues);
            let paymentDate = moment().format('DD-MM-YYYY');

            let totalDays = parseInt(frecuency) * parseInt(numberDues);
            let expirationDate = moment().add(totalDays, 'days').format('DD-MM-YYYY');
            $('#expiration').val(expirationDate)

            for (let index = 0; index < numberDues; index++) {
                paymentDate = moment(paymentDate, 'DD-MM-YYYY').add(frecuency, 'days').format('DD-MM-YYYY');
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
                                    <input type="number" step="0.01" min="1" class="form-control payment_amount" value="${parseFloat(due).toFixed(2)}" name="payment_amount[]">
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

        $('body').on('change', '#otherCondition', function() {
            if ($(this).val() == 'EFECTIVO') {
                $('#contOptionDepositoCofreOther').show();
                $('#contOptionDepositoCofreOther div select').attr('disabled', false);
                $('#contOptionDepositoCuentaOther').hide();
                $('#contOptionDepositoCuentaOther div select').attr('disabled', true);
                $('#contOptionDepositoPOSOther').hide();
                $('#contOptionDepositoPOSOther div select').attr('disabled', true);
            } else if ($(this).val() == 'DEPOSITO EN CUENTA') {
                $('#contOptionDepositoCofreOther').hide();
                $('#contOptionDepositoCofreOther div select').attr('disabled', true);
                $('#contOptionDepositoCuentaOther').show();
                $('#contOptionDepositoCuentaOther div select').attr('disabled', false);
                $('#contOptionDepositoPOSOther').hide();
                $('#contOptionDepositoPOSOther div select').attr('disabled', true);
            } else if ($(this).val() == 'TARJETA DE CREDITO' || $(this).val() == 'TARJETA DE DEBITO') {
                $('#contOptionDepositoCofreOther').hide();
                $('#contOptionDepositoCofreOther div select').attr('disabled', true);
                $('#contOptionDepositoCuentaOther').hide();
                $('#contOptionDepositoCuentaOther div select').attr('disabled', true);
                $('#contOptionDepositoPOSOther').show();
                $('#contOptionDepositoPOSOther div select').attr('disabled', false);
            } else {
                $('#contOptionDepositoCofreOther').hide();
                $('#contOptionDepositoCofreOther div select').attr('disabled', true);
                $('#contOptionDepositoCuentaOther').hide();
                $('#contOptionDepositoCuentaOther div select').attr('disabled', true);
                $('#contOptionDepositoPOSOther').hide();
                $('#contOptionDepositoPOSOther div select').attr('disabled', true);
            }
        });
        $('#serialnumber').change(function() {
            getCorrelative();
        });
        function getCorrelative() {
            let serialnumber = $('#serialnumber').val();
            let type = $('#typevoucher_id').val();
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
        }
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
            let c_discount = $('#c_discount').val();
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
                let isFree = $(tr).find('.is-free').is(':checked');
                
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
                    total = parseFloat((quantity * newprice));
                    totalR = parseFloat((quantity * price));
                } else {
                    total = parseFloat((quantity * price));
                }
                
                total2 = parseFloat((quantity * price));
                if (tigv == 2 || tigv == 3 || tigv == 4 || tigv == 5 || tigv == 6 || tigv == 7 || tigv == 10 || tigv == 11 || tigv == 12 ||
                    tigv == 13 || tigv == 14 || tigv == 15 || tigv == 17 || isFree) {
                    $(tr).find('.c_subtotal').val(total2);
                    $(tr).find('.c_total').val(total2);
                    totalR = price;
                    c_sum_free = parseFloat((c_sum_free * 1) + ($(tr).find('.c_total').val() * 1));
                } else if(tigv == 8) {
                    let subtotal = parseFloat(price) * parseFloat(quantity);
                    if (typeRecharge == '2') {
                        recharge = (parseFloat(subtotal) * parseFloat(tax) + parseFloat(recharge));
                    }
                    $(tr).find('.c_subtotal').val(parseFloat(subtotal).toFixed(2));
                    $(tr).find('.c_total').val(parseFloat(subtotal).toFixed(2));
                    totalR = total;
                    c_exonerated_gen = parseFloat((c_exonerated_gen * 1) + ($(tr).find('.c_total').val() * 1));
                    c_total_gen = parseFloat((c_total_gen * 1) + (parseFloat(total2)));
                } else if(tigv == 9 || tigv == 16) {
                    $(tr).find('.c_subtotal').val(parseFloat(price) * parseFloat(quantity));
                    $(tr).find('.c_total').val(parseFloat(price) * parseFloat(quantity));
                    totalR = price;
                    c_sum_unaffected = parseFloat((c_sum_unaffected * 1) + ($(tr).find('.c_total').val() * 1));
                    console.log(price)
                    c_total_gen = parseFloat((c_total_gen * 1) + (parseFloat(total2)));
                } else {
                    let subtotal = parseFloat((parseFloat(total2)) / (1 + (igv / 100) + tax));
                    let igvLinea = (parseFloat(subtotal) * (igv/100))
                    let totalLinea = parseFloat(parseFloat(subtotal) + (parseFloat(subtotal) * (igv / 100)));
                    if (typeRecharge == '2') {
                        recharge = parseFloat(parseFloat(totalR) - parseFloat(parseFloat(subtotal) + parseFloat(igvLinea)) ) + parseFloat(recharge);
                    }
                    $(tr).find('.c_subtotal').val(parseFloat(subtotal).toFixed(2));
                    $(tr).find('.c_total').val(parseFloat(totalLinea).toFixed(2));
                    totalR = total
                    if($('option:selected', product).attr('p-exonerated') === '0') {
                        c_gravada_gen = parseFloat((c_gravada_gen * 1) + (subtotal * 1));
                        c_subtotal_gen = parseFloat(igvLinea) + parseFloat(c_subtotal_gen);
                        console.log(c_subtotal_gen)
                        c_total_gen = parseFloat((c_total_gen * 1) + (parseFloat(total2)));
                    } else {
                        c_total_gen = parseFloat((c_total_gen * 1) + (parseFloat(total2)));
                    }
                }
                totalFinal = (parseFloat(c_total_gen) + parseFloat(t));
                if ($('#c_percentage').val() == NaN || $('#c_percentage').val() == '' || $('#c_percentage').val() == undefined) {
                    c_percentage = 0.00;
                } else {
                    c_percentage = parseFloat($('#c_percentage').val()).toFixed(2);
                    discount = ((parseFloat(totalFinal) * parseFloat(c_percentage)) / 100);
                    totalFinal = (parseFloat(totalFinal) - parseFloat(discount));
                    c_gravada_gen = parseFloat(totalFinal) / (1 + (igv / 100));
                    c_subtotal_gen = (totalFinal - c_gravada_gen);
                }
            });
            totalFinal = (parseFloat(totalFinal)) - parseFloat(c_discount);
            $('#c_igv').val(parseFloat(c_subtotal_gen).toFixed(2));
            $('#c_total').val(parseFloat(totalFinal).toFixed(2));
            $('#c_taxed').val(parseFloat(c_gravada_gen).toFixed(2));
            $('#exonerated').val(parseFloat(c_exonerated_gen).toFixed(2));
            $('#c_free').val(parseFloat(c_sum_free).toFixed(2));
            $('#c_unaffected').val(parseFloat(c_sum_unaffected).toFixed(2));
            $('#recharge').val(parseFloat(recharge).toFixed(2));
            $('#mountPayment').val(parseFloat(totalFinal).toFixed(2));
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
        $('#c_percentage').keyup(function () {
            recalculate();
        });
        $('body').on('keyup', '#mountOtherPayment', function() {
            let total = $('#c_total').val();
            let newAmoutn = $(this).val();
            if (newAmoutn == '' || newAmoutn == ' ') {
                newAmoutn = 0.00;
            }
            let newTotal = parseFloat(total) - parseFloat(newAmoutn);
            $('#mountPayment').val(newTotal.toFixed(2));
        });
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
            tr = $(this).parent().parent().parent();
            tr.find('.c_price').val($('option:selected', this).attr('p-price'));
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
                        $('#detraction').prop('checked', true);
                        tr.find('.c_quantity').keyup();
                    }
                }
            }
            if (isService != 2 || isService != 23) {
                if ($('option:selected', this).attr('p-stock') < 1) {
                    toastr.warning('Cantidad insuficiente.');
                }
            }
            if (isService == 22 && $('#pbH').val() == 1) {
                icbperTotal = ($('#pbp').val()  * tr.find('.c_quantity').val());
                $('#c_t').val(icbperTotal.toFixed(2));
            }
        });
        let isDetraccion;
        $('body').on('keyup', '.c_quantity', function() {
            recalculate();
        });
        $("#detraction").on( 'change', function() {
            if( $(this).is(':checked') ) {
                let account = $('#detractionAccount').val(),
                            bank = $('#detractionBank').val();
                console.log('Operación sujeta al Sistema de Pago de Obligaciones Tributarias: '+ bank +' CTA. DETRACCIÓN Nº ' + account + '. ');
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
                        // if ($('#detraction').parent().hasClass('off')) {
                        //     $('#detraction').parent().click();
                        // }
                        tr.find('.c_quantity').keyup();
                        $('#detraction').prop('checked', true);
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
                $("#product_region").prop('checked', false);
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
                $("#service_region").prop('checked', false);
            } else {
                recalculate();
            }
        });
        var products = "";
        var igvs = "";
        // function getdsds() {
            $.post('/commercial.quotations.products', '_token=' +  '{{ csrf_token() }}', function(response) {
                for (var i = 0; i < response.length; i++) {
                    products += `<option value="${response[i].id}" p-taxbase="${response[i].taxbase}"
                                    p-tax="${response[i].tax}" p-stock="${response[i].stock}" p-price="${response[i].price}"
                                    p-igv-type="${response[i].type_igv_id}" p-otype="${response[i].operation_type}"
                                    p-exonerated="${response[i].exonerated}" p-is-kit="${response[i].is_kit}">
                                    ${response[i].internalcode} - ${response[i].description}
                                </option>`;
                }
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
                            <br>
                            <label class="is-free-container"><input type="checkbox" class="is-free"> Es Bonificación</label>
                            `
                            + '<input type="hidden" name="is_free[]" class="is_free_c" value="0"></td>';
            data += '<td><input type="number" class="form-control c_quantity" name="cd_quantity[]" value="1"/></td>';
            data += '<td><input type="text" class="form-control c_stock" name="c_stock[]" value="0" readonly/></td>';
            data += '<td class="priceCell"><select name="cd_price[]" class="form-control c_price"></select></td>';
            data += '<td><input type="text" class="form-control c_subtotal" name="cd_subtotal[]" value="0" readonly /></td>';
            data += '<td><input type="text" class="form-control c_total" name="cd_total[]" value="0" readonly /></td>';
            data += '<td>';
            data += '<button type="button" class="btn btn-danger-custom btn-rounded remove"><i class="fa fa-minus"></i></button>';
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
            recalculate();
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
        $('#frm_quotation').validator().on('submit', function(e) {
            if(this.click === true) {
                e.preventDefault();
            }
            $('#btnSave').attr('disabled');
            this.click = true;
            if(e.isDefaultPrevented()) {
                toastr.warning('Debe llenar todos los campos obligatorios');
                this.click = false;
                e.preventDefault();
            } else {
                e.preventDefault();
                let data = $('#frm_quotation').serialize();
                console.log(data)
                if($('#tbl_products tbody tr').length == 0) {
                    toastr.error('Debe seleccionar algún producto o servicio');
                    this.click = false
                    return false;
                }
                if($('#condition').val() == 'DEPOSITO EN CUENTA') {
                    if ($('#bankOption').val() == '' || $('#bankOption').val() == null) {
                        toastr.error('Debe seleccionar una cuenta');
                        this.click = false
                        return false;
                    }
                }
                if($('#condition').val() == 'TARJETA DE CREDITO' || $('#condition').val() == 'TARJETA DE DEBITO') {
                    if ($('#methodOption').val() == '' || $('#methodOption').val() == null) {
                        toastr.error('Debe seleccionar un método de pago');
                        this.click = false
                        return false;
                    }
                }

                if($('#condition').val() == 'CREDITO') {
                    let totalDues = 0;

                    $('.payment_amount').each(function(i, el) {
                        if ($(el).val() == '' || $(el).val() <= 0) {
                            let message = `El monto de la cuota #${i+1} no es válido.`;
                            toastr.error(message)
                            $('#amountPendingModal').val($('#mountPayment').val())
                            $('#creditMdl').modal('show')
                            this.click = false
                            return false;
                        } else {
                            totalDues = parseFloat(totalDues) + parseFloat($(el).val());
                        }
                    })

                    if (parseFloat($('#mountPayment').val()).toFixed(2) != parseFloat(totalDues).toFixed(2)) {
                        $('#amountPendingModal').val($('#mountPayment').val())
                        $('#creditMdl').modal('show')
                        toastr.error('Verifique los montos de cada pago.')
                        this.click = false
                        return false;
                    }
                }

                if($('#condition').val() == 'DEVOLUCION') {
                    if ($('#credit_note_return').val() == '') {
                        toastr.warning('Debe de seleccionar una Nota de Credito')
                        return  false;
                    } else {
                        let totalNC = $('option:selected', $('credit_note_return')).data('total');
                        let total = $('#c_total').val();

                        if (parseFloat(totalNC) > parseFloat(total)) {
                            toastr.warning('Para esta forma de pago, el total del comprobante debe de ser Mayor o igual al total de la Nota de credito')
                            return  false;
                        }
                    }
                }

                $.ajax({
                    url: '/commercial.sales.create',
                    type: 'post',
                    data: data + '&typegenerate=' + typeGenerate + '&_token=' + '{{ csrf_token() }}',
                    dataType: 'json',
                    beforeSend: function() {
                        $('#btnSaveSale').attr('disabled', true);
                    },
                    complete: function() {
                    },
                    success: function(response) {
                        console.log(response);
                        if(response['response'] == true) {
                            toastr.success('Se grabó satisfactoriamente el Comprobante');
                            toastr.success('El comprobante fue enviado a Sunat satisfactoriamente');
                            setTimeout(function() {
                                window.location = '/commercial.sales';
                            }, 2000);
                        } else if(response['response'] == -1) {
                            toastr.success('Se grabó satisfactoriamente el Comprobante');
                            toastr.warning('Ocurrió un error con el comprobante, reviselo y vuelva a enviarlo.');
                            setTimeout(function() {
                                window.location = '/commercial.sales';
                            }, 2000);
                        } else if(response['response'] == -2) {
                            toastr.success('Se grabó satisfactoriamente el Comprobante');
                            toastr.error('El comprobante fue enviado a Sunat y fue rechazado automáticamente, vuelva a enviarlo manualmente');
                            setTimeout(function() {
                                window.location = '/commercial.sales';
                            }, 2000);
                        } else if(response['response'] == -3) {
                            toastr.success('Se grabó satisfactoriamente el Comprobante');
                            toastr.info('El comprobante fue enviado a Sunat y fue validado con una observación.');
                            setTimeout(function() {
                                window.location = '/commercial.sales';
                            }, 2000);
                        } else if(response['response'] == 99) {
                            toastr.success('Se grabó satisfactoriamente el Comprobante');
                            toastr.info('El comprobante será enviado a la Sunat en un resumen diario.');
                            setTimeout(function() {
                                window.location = '/commercial.sales';
                            }, 2000);
                        } else if(response == -9) {
                            toastr.warning('Cantidad insuficiente.');
                            $('#btnSaveSale').removeAttr('disabled');
                        } else if(response == -10) {
                            toastr.warning('No se Pudo descontar el stock');
                            $('#btnSaveSale').removeAttr('disabled');
                        } else if(response == -100) {
                            toastr.warning('La sumatoria de las cuotas difiere del total del comprobante');
                            $('#btnSaveSale').removeAttr('disabled');
                        } else {
                            toastr.success('Se grabó satisfactoriamente el Comprobante');
                            // toastr.error('Ocurrió un error desconocido,revise el comprobante.');
                            setTimeout(function() {
                                window.location = '/commercial.sales';
                            }, 2000);
                        }
                    },
                    error: function(response) {
                        console.log(response.responseText);
                        toastr.error('Ocurrio un error');
                        $('#btnSaveSale').removeAttr('disabled');
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
            $.get('/commercial.customer.all/' + '{{$type}}', function(response) {
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
        $('body').on('focus',".datepick", function(){
            $(this).datepicker({
                format: 'dd-mm-yyyy',
                autoclose: true,
            });
        });
        $('body').on('click', '.removeRowPayments', function() {
            $(this).parent().parent().remove();
        })
        $('#coin').change(function() {
            if ($(this).val() == 2) {
                let ers = $('#ers').val();
                $('#exchange_rate').val(ers);
            } else {
                $('#exchange_rate').val('');
            }
        });
        $('body').on('change', '.c_product', function() {
            let that = $(this);
            let tr = $(this).parent().parent()
            let it = $(this).data('it');
            let pid = that.val();
            let is_kit = $('option:selected', that).attr('p-is-kit')
            let product;
            let optype = $('option:selected', that).attr('p-otype');
            if (optype != 23) {
                let productRow = that.parent().parent().parent()
                let priceCell = $(productRow).find('.priceCell');
                priceCell.html('')
                priceCell.append('<select name="cd_price[]" class="form-control c_price"></select>')
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
            } else {
                let productRow = that.parent().parent().parent()
                let priceCell = $(productRow).find('.priceCell');
                priceCell.html('')
                priceCell.append('<input type="number" class="form-control c_price" name="cd_price[]" value="0" />')
            }

            if(is_kit == 1) {
                getItemsKit($(this).val(), tr)
            }

            // recalculate();
        });
        function getItemsKit(product, currentRow) {
            $.get(`/warehouse/kits/get-items?product=${product}`, function(response) {
                $.each(response, function(idx, el) {
                    let tr = `
                        <tr>
                            <td>
                                <div class="input-group">
                                    <select style="width: 80%;" class="form-control select_2 c_product" id="c_product" name="cd_product[]">
                                        <option value="${el.product.id}"
                                            p-it="${el.product.priceIncludeRC}"
                                            p-taxbase="${ el.product.tax ? el.product.tax.base : '' }" p-tax="${ el.product.tax ? el.product.tax.value : '' }"
                                            p-type_product="${el.product.type_product}" p-stock="${el.product.stock.stock}"
                                            p-igv-type="${el.product.type_igv_id}"
                                            p-otype='${ el.product.operation_type }' p-exonerated="${el.product.exonerated}"
                                            p-is-kit='${el.product.is_kit}'
                                        >
                                            ${el.product.internalcode} - ${el.product.description}
                                        </option>
                                    </select>
                                    <br>
                                    <label class="is-free-container">
                                        <input type="checkbox" class="is-free"> Es Bonificación
                                    </label>
                                    <input type="hidden" name="is_free[]" class="is_free_c" value="0">
                                </div>
                            </td>
                            <td><input type="number" class="form-control c_quantity" name="cd_quantity[]" value="${el.quantity}"/></td>
                            <td><input type="text" class="form-control c_stock" name="c_stock[]" value="${el.product.stock.stock}" readonly/></td>
                            <td class="priceCell">
                                <select name="cd_price[]" class="form-control c_price">
                            `;
                                $.each(el.product.product_price_list, function(i, e) {
                                   tr += `
                                        <option value="${e.price}" ${i == 0 ? 'selected' : ''}>${e.price_list.description} - ${e.price}</option>
                                   `;
                                });
                    tr += `
                                </select>
                            </td>
                            <td><input type="text" class="form-control c_subtotal" name="cd_subtotal[]" value="0" readonly /></td>
                            <td><input type="text" class="form-control c_total" name="cd_total[]" value="0" readonly /></td>
                            <td><button type="button" class="btn btn-danger-custom btn-rounded remove"><i class="fa fa-minus"></i></button></td>
                        </tr>
                    `;

                    $('#tbl_products tbody').append(tr);
                });
                if (response.length > 0) {
                    $(currentRow).remove();
                }
                recalculate();
                $('.select_2').select2();
            }, 'json');

        }
        $('#addOptionPayment').click(function() {
            let row = `
                        <div class="col-12 col-md-2 offset-md-6">
                            <div class="form-group">
                                <label for="condition">Otra Forma de Pago</label>
                                <select name="otherCondition" id="otherCondition" class="form-control">
                                    <option value="DEPOSITO EN CUENTA" data-days="0">DEPOSITO EN CUENTA</option>
                                    <option value="TARJETA DE CREDITO">TARJETA DE CREDITO</option>
                                    <option value="TARJETA DE DEBITO">TARJETA DE DEBITO</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-1">
                            <div class="form-group" id="contCreditBalance">
                                <label>Monto</label>
                                <input type="text" class="form-control" id="mountOtherPayment" name="mountOtherPayment">
                            </div>
                        </div>
                            <div class="col-12 col-md-1" id="contOptionDepositoCuentaOther">
                                <div class="form-group">
                                    <label>Cuenta</label>
                                    <select name="obank" id="" class="form-control">
                                        @foreach ($bankAccounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->bank_name }} - {{ $account->number }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-1" id="contOptionDepositoPOSOther">
                                <div class="form-group">
                                    <label>Medio de Pago</label>
                                    <select name="omp" id="" class="form-control">
                                        @foreach ($paymentMethods as $paymentMethod)
                                            <option value="{{ $paymentMethod->id }}">{{ $paymentMethod->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
            `;
            $('#otherMethodPaymentCont').append(row);
            $(this).hide();
            $('#contOptionDepositoCuentaOther').show();
            $('#contOptionDepositoPOSOther').hide();
            $('#contOptionDepositoCuentaOther div select').attr('disabled', false);
            $('#deleteOptionPayment').show();
            recalculate();
        });
        $('#deleteOptionPayment').click(function() {
            $('#otherMethodPaymentCont').html('');
            $(this).hide();
            $('#addOptionPayment').show();
        });
        $('#detraction').click(function () {
            toastr.info('La detracción se aplica a servicios mayores a S/. 700 ');
        })
        $('#pin').keyup(function(e) {
            e.preventDefault();
            if ($(this).val().length > 8) {
                $(this).val($(this).val().slice(0, 8));
            }
        })
        $('#frm_validate').submit(function(e) {
            e.preventDefault();

            const data = $(this).serialize();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                }
            });
            $.ajax({
                url: '/validate/user',
                type: 'post',
                data: data,
                dataType: 'json',
                success: function (response) {
                   if (response == true) {
                       $('#validateSupervisorModal').modal('hide');
                       $('#pin').val('')
                       $('#c_discount').attr('readonly', false).focus();
                       toastr.success('Pin validado correctamente');
                   } else {
                       toastr.warning('Pin incorrecto. Intente nuevamente');
                   }
                }
            });
        })
        $('#c_discount').keyup(function() {
            recalculate();
        })

        $('#showSupervisorModal').click(function() {
            if ($('#is_supervisor').val() == 1) {
                $('#validateSupervisorModal').modal('show')
            } else {
                $('#c_discount').attr('readonly', false).focus();
            }
        })

    </script>
@stop