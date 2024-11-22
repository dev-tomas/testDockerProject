@extends('layouts.azia')
@section('css')
    <style>
        .select2-container{
            width: 80% !important;
            max-width: 80% !important;
            min-width: 80% !important;
        }
        .table tbody tr td { padding-left: 0;padding-right: 0; }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header">
                    <h1 class="text-center">
                        COMPRA FÍSICA
                    </h1>
                </div>
                <div class="card-body">
                    <form id="frm_shopping" method="post" class="form-horizontal" role="form" data-toggle="validator">
                        <div class="row">
                            <div class="col-12 col-md-5">
                                <div class="form-group">
                                    <label> </label>
                                    <div class="input-group">
                                        <select name="provider" id="provider" class="form-control" style="width: 80%;" required>
                                            @if($providers->count() > 0)
                                                @foreach($providers as $c)
                                                    <option value="{{$c->id}}" {{ $shopping ? ($shopping->provider_id  == $c->id ? 'selected' : '') : '' }} data-document="{{ $c->document }}" c-email="{{$c->email}}">
                                                        {{ $c->document }} - {{$c->description}}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <div class="input-group-append" id="openProvider" style="cursor: pointer;">
                                            <button type="button" class="btn btn-primary-custom">NUEVO</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-1">
                                <button class="btn btn-primary-custom mt-4" type="button" id="showQrModal">QR</button>
                            </div>
                            <div class="col-12 col-md-2">
                                <div class="form-group">
                                    <label>Fecha</label>
                                    <input class="form-control date-picker datepicker" value="{{ $shopping ? date('d-m-Y', strtotime($shopping->date)) : $date }}" id="fecha" name="fecha" type="text" data-date-format="yyyy-mm-dd" required autocomplete="off">
                                </div>
                            </div>

                            <div class="col-12 col-md-2">
                                <div class="form-group">
                                    <label>Moneda</label>
                                    <select class="form-control" name="moneda" id="moneda" required>
                                        @foreach($coin as $dt)
                                            @if($dt->id == 1 || $dt->id == 2)
                                                <option value="{{$dt->id}}" {{ $shopping ? ($shopping->coin_id  == $dt->id ? 'selected' : '') : '' }}>{{$dt->symbol}} {{$dt->description}}</option>
                                            @endif()
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-2">
                                <div class="form-group">
                                    <label>Tipo de Cambio</label>
                                    <input type="text" class="form-control" name="exchange_rate" id="exchange_rate" value="{{ $shopping ? $shopping->exchange_rate : '' }}" >
                                </div>
                            </div>
                            <div class="col-12 col-md-2">
                                <div class="form-group">
                                    <label> Tipo Documento</label>
                                    <select class="form-control" name="tipvou" id="tipdoc" required="">
                                        <option value="">ELEGIR TIPO DE DOCUMENTO</option>
                                        <option value="1" {{ $shopping ? ($shopping->type_vouchers_id  == '1' ? 'selected' : '') : '' }} data-code="01">FACTURA</option>
                                        <option value="2" {{ $shopping ? ($shopping->type_vouchers_id  == '2' ? 'selected' : '') : '' }} data-code="03">BOLETA DE VENTA</option>
                                        <option value="3" {{ $shopping ? ($shopping->type_vouchers_id  == '3' ? 'selected' : '') : '' }}>NOTA DE CRÉDITO DE BOLETA</option>
                                        <option value="4" {{ $shopping ? ($shopping->type_vouchers_id  == '4' ? 'selected' : '') : '' }}>NOTA DE CRÉDITO DE FACTURA</option>
                                        <option value="5" {{ $shopping ? ($shopping->type_vouchers_id  == '5' ? 'selected' : '') : '' }}>NOTA DE DÉBITO DE BOLETA</option>
                                        <option value="6" {{ $shopping ? ($shopping->type_vouchers_id  == '6' ? 'selected' : '') : '' }}>NOTA DE DÉBITO DE FACTURA</option>
                                        <option value="8" {{ $shopping ? ($shopping->type_vouchers_id  == '8' ? 'selected' : '') : '' }}>TICKET DE MAQUINA REGISTRADORA</option>
                                        <option value="17" {{ $shopping ? ($shopping->type_vouchers_id  == '17' ? 'selected' : '') : '' }}>COMPROBANTE DE PERCEPCIÓN</option>
                                        <option value="26" {{ $shopping ? ($shopping->type_vouchers_id  == '26' ? 'selected' : '') : '' }}>RECIBO DE SERVICIOS</option>
                                        <option value="27" {{ $shopping ? ($shopping->type_vouchers_id  == '27' ? 'selected' : '') : '' }}>SIN DOCUMENTO</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-2">
                                <div class="form-group">
                                    <label>Serie</label>
                                    <input type="text" maxlength="5" name="shoppingSerie" id="shoppingSerie" class="form-control" value="{{ $shopping ? $shopping->shopping_serie  : '' }}" required>
                                </div>
                            </div>

                            <div class="col-12 col-md-2">
                                <div class="form-group">
                                    <label>Número</label>
                                    <input type="number" step="1" name="shoppingCorrelative" id="shoppingCorrelative" class="form-control" value="{{ $shopping ? $shopping->shopping_correlative  : '' }}" required>
                                </div>
                            </div>

                            <div class="col-12 col-md-2">
                                <div class="form-group">
                                    <label for="condition">Forma de pago</label>
                                    <select name="condition" id="condition" class="form-control">
                                        <option value="EFECTIVO" data-days="0" {{ $shopping ? ($shopping->payment_type == 'EFECTIVO' ? 'selected' : '') : '' }}>EFECTIVO</option>
                                        <option value="DEPOSITO EN CUENTA" data-days="0" {{ $shopping ? ($shopping->payment_type == 'DEPOSITO EN CUENTA' ? 'selected' : '') : '' }}>DEPOSITO EN CUENTA</option>
                                        <option value="CREDITO" data-days="7" {{ $shopping ? ($shopping->payment_type == 'CREDITO' ? 'selected' : '') : '' }}>CREDITO</option>
                                        <option value="TARJETA DE CREDITO" data-days="0" {{ $shopping ? ($shopping->payment_type == 'TARJETA DE CREDITO' ? 'selected' : '') : '' }}>TARJETA DE CREDITO</option>
                                        <option value="TARJETA DE DEBITO" data-days="0" {{ $shopping ? ($shopping->payment_type == 'TARJETA DE DEBITO' ? 'selected' : '') : '' }}>TARJETA DE DEBITO</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-1">
                                <div class="form-group" id="contCreditBalance">
                                    <label>Monto</label>
                                    <input type="text" class="form-control" id="mountPayment" value="0.00" name="mountPayment" {{ $shopping ? $shopping->total : '0.00' }} readonly>
                                </div>
                            </div>
                            <div class="col-12 col-md-1" id="contOptionDepositoCofre">
                                <div class="form-group">
                                    <label>Cofre</label>
                                    <select name="cash" id="cash" class="form-control">
                                        @foreach ($cashes as $cash)
                                            <option value="{{ $cash->id }}" {{ $shopping ? ($shopping->cash_id ==  $cash->id ? 'selected' : '') : '' }}>{{ $cash->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-1" id="contOptionDepositoCuenta">
                                <div class="form-group">
                                    <label>Cuenta</label>
                                    <select name="bank" id="bankOption" class="form-control">
                                        @foreach ($bankAccounts as $account)
                                            <option value="{{ $account->id }}" {{ $shopping ? ($shopping->bank_account_id ==  $account->id ? 'selected' : '') : '' }}>{{ $account->bank_name }} - {{ $account->number }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-1" id="contOptionDepositoPOS">
                                <div class="form-group">
                                    <label>Medio de Pago</label>
                                    <select name="mp" id="methodOption" class="form-control">
                                        @foreach ($paymentMethods as $paymentMethod)
                                            <option value="{{ $paymentMethod->id }}" {{ $shopping ? ($shopping->payment_method_id ==  $paymentMethod->id ? 'selected' : '') : '' }}>{{ $paymentMethod->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-md-2">
                                <div class="form-group">
                                    <label for="tax_base">BASE IMPONIBLE</label>
                                    <select name="tax_base" id="tax_base" class="form-control">
                                        <option value="006" selected="selected">OPER. GRAVADAS</option>
                                        <option value="007">OPER. GRAV. Y NO GRAVA.</option>
                                        <option value="008">OPER. NOGRAVADAS</option>
                                        <option value="999">EXONERADAS</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-2">
                                <div class="form-group">
                                    <label for="igv_percentage">PORCENTAJE DE IGV</label>
                                    <select name="igv_percentage" id="igv_percentage" class="form-control">
                                        <option value="10" selected="selected">10.00%</option>
                                        <option value="18" selected>18.00%</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-2" id="QuoteGenerate">
                                <div class="form-group">
                                    <label># de Cuotas</label>
                                    <input type="number" class="form-control" name="quotes">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Código de Barras</label>
                                    <input class="form-control" id="codebar" name="codebar" type="text" placeholder="Ingresar el código de barras del producto" autofocus>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-12">
                                <div class="table-responsive">
                                    <table id="tbl_items" class="table">
                                        <thead>
                                            <th width="220px">Producto/Servicio</th>
                                            <th width="110px">Tipo Compra</th>
                                            <th width="110px">Almacén / Centro C.</th>
                                            <th width="80px">Cantidad</th>
                                            <th width="80px">Pre. Uni.</th>
                                            <th width="80px">Val. Uni.</th>
                                            <th width="80px">Subtotal</th>
                                            <th width="80px">Total</th>
                                            <th width="50px">Opciones</th>
                                        </thead>
                                        <tbody class="list_productos">
                                            @if ($hasData)
                                                @foreach ($shopping->detail as $item)
                                                    <tr>
                                                        <td>
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <select style="width: 80%;" class="form-control select_2 producto" id="producto" name="producto[]" required>
                                                                        <option value="">Seleccionar Producto</option>
                                                                        @if($product->count() > 0)
                                                                            @foreach($product as $p)
                                                                                <option value="{{$p->id}}" {{ $item->product_id == $p->id ? 'selected' : '' }} p-type-igv="{{ $p->type_igv_id }}" codebar="{{ $p->code }}" p-price="{{$p->price}}" >{{$p->code}} - {{$p->description}}</option>
                                                                            @endforeach
                                                                        @endif
                                                                    </select>
                                                                    <div class="input-group-append">
                                                                        <button class="btn btn-primary-custom openModalProduct" type="button" id="">
                                                                            <i class="fa fa-plus"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <select name="typepurchase[]" id="typePurchase" class="form-control slecTypePurchase" required>
                                                                    <option value="">Tipo de Compra</option>
                                                                    <option value="1" {{ $item->type_purchase == 1 ? 'selected' : '' }}>Mercaderia</option>
                                                                    <option value="0" {{ $item->type_purchase == 0 ? 'selected' : '' }}>Activo Fijo</option>
                                                                    <option value="2" {{ $item->type_purchase == 2 ? 'selected' : '' }}>Gastos</option>
                                                                </select>
                                                            </div>
                                                        </td>
                                                        <td class="centercell">
                                                            @if ($item->type_purchase == 1)
                                                                <select name="location[]" id="warehouse" class="form-control warehouse" required>
                                                                    <option value="">Seleccione un Almacen</option>
                                                                    @foreach ($warehouses as $w)
                                                                        <option value="{{ $w->id }}" {{ $item->warehouse_id == $w->id ? 'selected' : '' }}>{{ $w->description }}</option>
                                                                    @endforeach
                                                                </select>
                                                            @else
                                                                <select name="location[]" id="" class="form-control centerCost" required>
                                                                    <option value="">Seleccione un Centro de Costo</option>
                                                                    @foreach ($costCenters as $cc)
                                                                        <option value="{{ $cc->id }}" {{ $item->center_cost_id == $cc->id ? 'selected' : '' }}>{{ $cc->center }}</option>
                                                                    @endforeach
                                                                </select>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <input class="form-control cantidad" id="cantidad" name="cantidad[]" type="text" value="{{ $item->quantity }}" required>
                                                        </td>
                                                        <td>
                                                            <input class="form-control pre_uni" id="pre_uni" name="pre_uni[]" type="text" value="{{ $item->unit_price }}" required>
                                                        </td>
                                                        <td>
                                                            <input class="form-control val_uni" id="val_uni" name="val_uni[]" type="text" value="{{ $item->unit_value }}" required>
                                                        </td>
                                                        <td>
                                                            <input class="form-control subtotal" id="subtotal" name="subtotal[]" type="text" value="{{ $item->subtotal }}" readonly>
                                                        </td>
                                                        <td>
                                                            <input class="form-control total" id="total" name="total[]" type="text" value="{{ $item->total }}" >
                                                        </td>
                                                        <td><button type="button" class="btn btn-danger-custom remove"><i class="fa fa-close"></i></button></td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td>
                                                        <div class="form-group">
                                                            <div class="input-group">
                                                                <select style="width: 80%;" class="form-control select_2 producto" id="producto" name="producto[]" required>
                                                                    <option value="">Seleccionar Producto</option>
                                                                    @if($product->count() > 0)
                                                                        @foreach($product as $p)
                                                                            <option value="{{$p->id}}" p-type-igv="{{ $p->type_igv_id }}" codebar="{{ $p->code }}" p-price="{{$p->price}}" >{{ $p->code }} - {{$p->description}}</option>
                                                                        @endforeach
                                                                    @endif
                                                                </select>
                                                                <div class="input-group-append">
                                                                    <button class="btn btn-primary-custom openModalProduct" type="button" id="">
                                                                        <i class="fa fa-plus"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <select name="typepurchase[]" id="typePurchase" class="form-control slecTypePurchase" required>
                                                                <option value="">Tipo de Compra</option>
                                                                <option value="1">Mercaderia</option>
                                                                <option value="0">Activo Fijo</option>
                                                                <option value="2">Gastos</option>
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <td class="centercell"></td>
                                                    <td>
                                                        <input class="form-control cantidad" id="cantidad" name="cantidad[]" type="text" value="0" required>
                                                    </td>
                                                    <td>
                                                        <input class="form-control pre_uni" id="pre_uni" name="pre_uni[]" type="text" value="0" required>
                                                    </td>
                                                    <td>
                                                        <input class="form-control val_uni" id="val_uni" name="val_uni[]" type="text" value="0" required>
                                                    </td>
                                                    <td>
                                                        <input class="form-control subtotal" id="subtotal" name="subtotal[]" type="text" readonly>
                                                    </td>
                                                    <td>
                                                        <input class="form-control total" id="total" name="total[]" type="text" >
                                                    </td>
                                                    <td><button type="button" class="btn btn-danger-custom remove"><i class="fa fa-close"></i></button></td>
                                                </tr>
                                            @endif

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="button" class="btn btn-primary-custom" id="btnAddProduct">
                                    <i class="fa fa-plus-circle"></i>
                                    Agregar Item
                                </button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-8 mt-4">
                                <label>Observaciones</label>
                                <textarea name="observations" id="observations" class="form-control"></textarea>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="row">
                                    <div class="col-6 text-right">
                                        <label>Descuento</label>
                                    </div>
                                    <div class="col-6 text-right">
                                        <input name="c_discount" id="c_discount" type="text" class="form-control c_discount" value="0.00" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6 text-right">
                                        <label>Subtotal</label>
                                    </div>
                                    <div class="col-6 text-right">
                                        <input name="c_subtotal" id="c_subtotal" type="text" class="form-control c_subtotal" value="0.00" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6 text-right">
                                        <label>Exonerada</label>
                                    </div>
                                    <div class="col-6 text-right">
                                        <input name="exot" id="exot" type="text" class="form-control exot" readonly value="0.00" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6 text-right">
                                        <label>Inafecta</label>
                                    </div>
                                    <div class="col-6 text-right">
                                        <input name="inat" id="inat" type="text" class="form-control inat" readonly value="0.00" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6 text-right">
                                        <label>Gravada</label>
                                    </div>
                                    <div class="col-6 text-right">
                                        <input name="gravt" id="gravt" type="text" class="form-control gravt" readonly value="0.00" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6 text-right">
                                        <label>IGV</label>
                                    </div>
                                    <div class="col-6 text-right">
                                        <input name="igvt" id="igvt" type="text" class="form-control igvt" readonly value="0.00" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6 text-right">
                                        <label>Total</label>
                                    </div>
                                    <div class="col-6 text-right">
                                        <input name="totalt" id="totalt" type="text" class="form-control totalt" readonly value="0.00" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <br>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary-custom pull-right" id="btnSave">
                                    REGISTRAR COMPRA
                                </button>
                                <button type="button" class="btn btn-secondary-custom pull-right" id="btngoShopping">
                                    IR A COMPRAS
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <form id="frm_proveedor" method="post" class="form-horizontal" role="form" data-toggle="validator">
        <input type="hidden" id="validado" value="0">
        <div id="mdl_add_provider" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Agregar Proveedor</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="card content-overlay">
                            <div class="card-body">
                                <div class="row">
                                    <input type="hidden" id="provider_id" name="provider_id">
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
                                            <label for="description">Proveedor *</label>
                                            <input id="description" name="description" type="text" class="form-control" required data-error="Este campo no puede estar vacío">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="code">Código de Proveedor *</label>
                                            <input id="code" name="code" type="text" class="form-control" readonly required data-error="Este campo no puede estar vacío">
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
                                            <button id="btnGrabarProveedor" type="submit" class="btn btn-primary-custom pull-right"> CREAR PROVEEDOR</button>
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
    <div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Escanear Factura</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="reader"></div>
                </div>
            </div>
        </div>
    </div>
    @include('components.products', ['typeModal' => 2])
@stop
@section('script_admin')
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        const products = @json($product);

        $(document).ready(function() {
            $('body').on('keypress', 'input', function(e) {
                if(e.keyCode == 13){
                    e.preventDefault();
                }
            });
            $('#deleteOptionPayment').hide();
            $('#contOptionDepositoCuenta').hide();
            $('#contOptionDepositoPOS').hide();
            $('#showPayments').hide();
            const hasData = @json($hasData);
            if (hasData) {
                $('#condition').change();
                recalculate()
            }
        });

        $('#codebar').keydown(function(event){
            let that = this;
            //event.preventDefault();
            var keycode = (event.keyCode ? event.keyCode : event.which);

            if(keycode == '13'){
                let product = products.find(product => {
                    return product.code === $(that).val()
                });

                if(product !== undefined) {
                    codebar = $('#codebar').val();
                    $('.producto').each(function () {
                        let tr = $(this).parent().parent().parent().parent();
                        if ($(this).val() === '') {
                            if(checkExistsProcuct(product.id) === 0) {
                                $('#codebar').val('');
                                $(this).val(product.id);
                                $(this).find('option[data-codebar="' + codebar + '"]').attr('selected', 'selected');
                                $(this).trigger('change');
                                tr.find('.cantidad').val(1);

                                addProductColumn();
                            }

                        } else if($(this).val() == product.id) {
                            let cant = tr.find('.cantidad').val() * 1;
                            tr.find('.cantidad').val(cant + 1);
                            $('#codebar').val('');
                        }
                    });
                } else {
                    toastr.info('Producto no encontrado');
                }
            } else {
                codebar = $('#codebar').val();
            }
        });

        function checkExistsProcuct(value) {
            let response = 0;
            $('.producto').each(function () {
                if($(this).val() == value) {
                    response = 1;
                }
            });

            return response;
        }

        $(document).ready(function() {
            $('#QuoteGenerate').hide();

            $('#contCenterCost').hide();
            $('#contWarehouse').hide();
        });

        let centerElement = `

                <select name="location[]" id="" class="form-control centerCost" required>
                    <option value="">Seleccione un Centro de Costo</option>
                    @foreach ($costCenters as $cc)
                        <option value="{{ $cc->id }}">{{ $cc->center }}</option>
                    @endforeach
                </select>`;
        let warehouseElemente = `<select name="location[]" id="warehouse" class="form-control warehouse" required>
                    <option value="">Seleccione un Almacen</option>
                    @foreach ($warehouses as $w)
                        <option value="{{ $w->id }}">{{ $w->description }}</option>
                    @endforeach
                </select>`;


        $('body').on('change', '.slecTypePurchase', function() {
            if ($(this).val() == 1) {
                $(this).parent().parent().parent().find('.centercell').html('');
                $(this).parent().parent().parent().find('.centercell').append(warehouseElemente);
            } else {
                $(this).parent().parent().parent().find('.centercell').html('');
                $(this).parent().parent().parent().find('.centercell').html(centerElement);
            }
        });

        $('#payment').change(function (){
            let method = $(this).val();

            if (method === 'CREDITO') {
                $('#QuoteGenerate').show();
            } else {
                $('#QuoteGenerate').hide();
            }
        });

        $("#provider").select2({
            placeholder: 'Buscar Proveedor',
            allowClear: true
        });

        $('#openProvider').on('click', function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: 'post',
                url: '/logistic.providers/get-provider-code',
                dataType: 'json',
                success: function(response) {
                    $('#code').val(response);
                },
                error: function(response) {
                    toastr.error(response.responseText);
                }
            });

            $('#mdl_add_provider').modal({
                backdrop: 'static',
                keyboard: false
            });
        });

        $("#frm_proveedor").validator().on('submit', function(e) {
            if (e.isDefaultPrevented()) {
                toastr.warning('Debe llenar todos los campos obligatorios.');
            } else {
                e.preventDefault();
                let data = $('#frm_proveedor').serialize();
                $.ajax({
                    url: '{{ route('saveProvider') }}',
                    type: 'post',
                    data: data + '&_token=' + '{{ csrf_token() }}',
                    dataType: 'json',
                    beforeSend: function() {
                        $('#btnGrabarProveedor').attr('disabled');
                    },
                    complete: function() {

                    },
                    success: function(response) {
                        console.log(response);
                        if(response == true) {
                            toastr.success('Se grabó satisfactoriamente el Proveedor');
                            clearDataProvider();
                            $('#typedocument').val('');
                            $('#mdl_add_provider').modal('hide');
                            getProvider();
                        } else {
                            console.log(response.responseText);
toastr.error('Ocurrio un error');
                        }
                    },
                    error: function(response) {
                        console.log(response.responseText);
toastr.error('Ocurrio un error');
                        $('#btnGrabarProveedor').removeAttr('disabled');
                    }
                });
            }
        });

        $('#btnConfigPrices').click(function () {
            $('#mdl_add_prices').modal('show');
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

        $('body').on('click', '.btnAddClassification', function () {
            $('#mdl_add_classification').modal('show');
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

        $('#type').change(function() {
            if($(this).val() != 2) {
                $('.quantity').show(100);
                $('#initial_stock').attr('readonly', false)
                $('#intial_date').attr('readonly', false)
                $('#maximum_stock').attr('readonly', false)
                $('#minimum_stock').attr('readonly', false)
                $('#warehouse').attr('readonly', false)
                $('#location').attr('readonly', false)
            } else {
                $('#quantity').val('');
                $('.quantity').hide(100);
                $('#initial_stock').attr('readonly', true)
                $('#intial_date').attr('readonly', true)
                $('#maximum_stock').attr('readonly', true)
                $('#minimum_stock').attr('readonly', true)
                $('#warehouse').attr('readonly', true)
                $('#location').attr('readonly', true)
                $('#quantity').attr('readonly', true);
            }
        });

        $('#initial_stock').keyup(function() {
            $('#quantity').val($(this).val());
        });

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

        $('#document').on('keyup', function() {
            let url = '';
            if($('#typedocument').val() == 2) {
                if($(this).val().length == 8) {
                    url = '/consult.dni/' + $(this).val();
                    getProviders(url, $('#typedocument').val());
                }
            } else if($('#typedocument').val() == 4) {
                if($(this).val().length == 11) {
                    url = '/consult.ruc/' + $(this).val();
                    getProviders(url, $('#typedocument').val());
                }
            }
        });

        $('#typedocument').on('change', function() {
            clearDataProvider();
            $("#validado").val(0)
        });

        function getProvider()
        {
            $.get('{{ route('getAllProviders') }}', function(response) {
                $('#provider').html('');
                $('#provider').select2('destroy');
                let option = '';
                for (let i = 0;i < response.length; i++) {
                    option += '<option value="' + response[i].id + '">' + response[i].description + '</option>';
                }

                $('#provider').append(option);
                $('#provider').select2();
            }, 'json');
        }

        function getProviders(url, typedocument)
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
                    toastr.info('No se encontró al proveedor, debe ingresar los datos manualmente.');
                    //clearDataProvider();
                }
            });
        }

        function clearDataProvider()
        {
            $('#document').val('');
            $('#description').val('');
            $('#phone').val('');
            $('#address').val('');
            $('#email').val('');
        }

        function calculateAmount(){
            let totalt = 0;
            let totalCalculable = 0;
            let igvt = 0;
            $('#tbl_items tbody tr').each(function(index, tr) {
                let igv_type = $(tr).find('.producto option:selected').attr('p-type-igv');
                if (igv_type == 1) {
                    totalCalculable = totalt + (($(tr).find('.total').val() * 1));
                    igvt = totalCalculable - (parseFloat(totalCalculable) / 1.18);
                }
                totalt = totalt + (($(tr).find('.total').val() * 1));
            });



            $('.totalt').val(totalt.toFixed(2));
            $('.igvt').val(igvt.toFixed(2));
        }

        $('#tax_base').change(function() {
            recalculate();
        })

        var igv = 18;

        $('#igv_percentage').change(function() {
            recalculate()
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
            let recharge = 0.00;
            let totalR = 0;
            let tax = 0.00;
            let total2 = 0.00;
            let total = 0.00;
            let subtotal_gen = 0.00
            let igvPercentage = $('#igv_percentage').val();
            let igvDecimal = parseFloat(igvPercentage)/ 100;
            let igvInteger = parseFloat(igvDecimal) + 1;

            c_discount = $('#c_discount').val();

            if (c_discount == '') {
                c_discount = 0;
            }

            $('#tbl_items tbody tr').each(function(index, tr) {
                tax = 0.00;
                let product = $(tr).find('.producto');
                let price = $(tr).find('.pre_uni').val();
                let quantity = $(tr).find('.cantidad').val();
                let tigv = $('option:selected', product).attr('p-type-igv');

                if ($('#tax_base').val() == '999') {
                    tigv = 8;
                } else if ($('#tax_base').val() == '008') {
                    tigv = 9;
                }

                total = parseFloat((quantity * price));

                if(tigv == 8) {
                    $(tr).find('.subtotal').val(parseFloat(price) * parseFloat(quantity));
                    $(tr).find('.total').val(parseFloat(price) * parseFloat(quantity));
                    $(tr).find('.val_uni').val(price);

                    c_exonerated_gen = parseFloat((c_exonerated_gen * 1) + ($(tr).find('.total').val() * 1));
                } else if(tigv == 9) {
                    $(tr).find('.subtotal').val(parseFloat(price) * parseFloat(quantity));
                    $(tr).find('.total').val(parseFloat(price) * parseFloat(quantity));
                    $(tr).find('.val_uni').val(price);


                    c_sum_unaffected = parseFloat((c_sum_unaffected * 1) + ($(tr).find('.total').val() * 1));
                } else {
                    let unit_val = parseFloat(price) / parseFloat(igvInteger);
                    let subtotal = parseFloat(unit_val) * parseFloat(quantity);
                    $(tr).find('.subtotal').val(subtotal);
                    $(tr).find('.total').val((total).toFixed(2));
                    $(tr).find('.val_uni').val(parseFloat(unit_val).toFixed(2));

                    c_gravada_gen = parseFloat(c_gravada_gen) + parseFloat(subtotal);
                    c_subtotal_gen = parseFloat(c_subtotal_gen) + parseFloat(total) -   parseFloat(subtotal)
                }

                c_total_gen = parseFloat((c_total_gen * 1) + (parseFloat(total)));
            });

            subtotal_gen = parseFloat(c_gravada_gen);
            c_gravada_gen = parseFloat(subtotal_gen) - parseFloat(c_discount);
            c_subtotal_gen = parseFloat(c_gravada_gen) * parseFloat(igvDecimal);

            c_total_gen = parseFloat(c_gravada_gen) + parseFloat(c_subtotal_gen) + parseFloat(c_exonerated_gen) + parseFloat(c_sum_unaffected);

            $('#c_subtotal').val(parseFloat(subtotal_gen).toFixed(2));
            $('#igvt').val(parseFloat(c_subtotal_gen).toFixed(2));
            $('#totalt').val(parseFloat(c_total_gen).toFixed(2));
            $('#mountPayment').val(parseFloat(c_total_gen).toFixed(2));
            $('#gravt').val(parseFloat(c_gravada_gen).toFixed(2));
            $('#exot').val(parseFloat(c_exonerated_gen).toFixed(2));
            $('#inat').val(parseFloat(c_sum_unaffected).toFixed(2));
        }

        $('#c_discount').keyup(function(e) {
            recalculate();
        })

        $('body').on('keyup', '.pre_uni', function() {
            recalculate()
        });

        $('body').on('keyup', '.val_uni', function() {
            recalculate()
        });

        $('body').on('keyup', '.cantidad', function() {
            recalculate()
        });

        $('body').on('keyup','.total', function () {
            recalculate()
        });


        function calCanti() {
            let service_counter = 0;
            let tr = $('.cantidad').parent().parent();
            let precio = tr.find('.pre_uni').val();
            let cantidad = $('.cantidad').val();
            let total = (precio *1) * (cantidad *1);
            tr.find('.subtotal').val(total.toFixed(2));
            tr.find('.total').val(total.toFixed(2));
            calculateAmount();
        }

        $('.select_2').select2();


        $('#btnAddProduct').on('click', function() {
            addProductColumn();
        });

        function addProductColumn() {
            let data = '';
            data += '<tr>';
            data += '<td><div class="form-group">';
            data += '<div class="input-group">';
            data += '<select style="width: 80%;" class="form-control select_2 producto" id="producto" name="producto[]">';
            data += '<option value="">Seleccionar Producto</option>';
            data += '@if($product->count() > 0)';
            data += '@foreach($product as $p)';
            data += '<option value="{{$p->id}}" codebar="{{ trim($p->code) }}" p-type-igv="{{$p->type_igv_id}}"  p-price="{{$p->price}}" >{{$p->code}} {{$p->description}}</option>';
            data += '@endforeach';
            data += '@endif';
            data += '</select>';
            data += '<div class="input-group-append">';
            data += '<button class="btn btn-primary-custom openModalProduct" type="button" id="">';
            data += '<i class="fa fa-plus"></i>';
            data += '</button>';
            data += '</div>';
            data += '</div>';
            data += '</div></td>';
            data += `<td>
                                                    <div class="form-group">
                                                        <select name="typepurchase[]" id="typePurchase" class="form-control slecTypePurchase" required>
                                                            <option value="">Tipo de Compra</option>
                                                            <option value="1">Mercaderia</option>
                                                            <option value="0">Activo Fijo</option>
                                                            <option value="2">Gastos</option>
                                                        </select>
                                                    </div>
                                                </td>
                                                <td class="centercell"></td>`;
            data += '<td><input class="form-control cantidad" id="cantidad" name="cantidad[]" type="text" value="0"></td>';
            data += '<td><input class="form-control pre_uni" id="pre_uni" name="pre_uni[]" type="text" value="0"></td>';
            data += '<td><input class="form-control val_uni" id="val_uni" name="val_uni[]" type="text" value="0"></td>';
            data += '<td><input class="form-control subtotal" id="subtotal" name="subtotal[]" type="text" readonly></td>';
            data += '<td><input class="form-control total" id="total" name="total[]" type="text"></td>';
            data += '<td>';
            data += '<button type="button" class="btn btn-danger-custom remove"><i class="fa fa-close"></i></button>';
            data += '</td>';

            data += '</tr>';
            $('#tbl_items tbody').append(data);

            $('.select_2').select2();
        }

        $('body').on('click', '.remove', function() {
            $(this).parent().parent().remove();
            calculateAmount();
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

                    console.log(data, 'data')
                    console.log(data2, 'data2')

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

                                // $.post('/commercial.quotations.products', '_token=' +  '{{ csrf_token() }}', function(response) {
                                //     let option = '<option>Seleccionar Producto</option>';
                                //     for (var i = 0; i < response.length; i++) {
                                //         option += '<option value="' + response[i].id + '" >';
                                //         option += response[i].description + '</option>';
                                //     }

                                //     $('.producto').html('');
                                //     $('.producto').append(option);

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
                } else {
                    toastr.warning('Debe registrar al menos un precio');
                    e.preventDefault();
                }
            }
        });

        $("#mdl_add_product").on('hidden.bs.modal', function () {
            $.post('/commercial.quotations.products', '_token=' +  '{{ csrf_token() }}', function(response) {
                let option = '<option>Seleccionar Producto</option>';
                for (var i = 0; i < response.length; i++) {
                    option += '<option value="' + response[i].id + '">';
                    option += response[i].description + '</option>';
                }

                $('.producto').html('');
                $('.producto').append(option);
            }, 'json');
        });

        $('body').on('click', '.openModalProduct', function() {
            $('#mdl_add_product').modal('show');
            clearDataProduct();
        });

        function clearDataProduct() {
            $('#measure').val('');
            $('#code').val('');
            $('#internalcode').val('');
            $('#pdescription').val('');
            $('#product_id').val('');
            $('#quantity').val(1);
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
            $('#maximum_stock').val();
            $('#minimum_stock').val();
            $('#preview_image').attr('src', 'storage/products/default.jpg')

            $('#tablePrice tbody').html('');
        }

        $('body').on('change', '.producto', function() {
            let service_counter = 0;
            let tr = $(this).parent().parent().parent().parent();
            // tr.find('#val_uni').val($('option:selected', this).attr('p-price'));
            // tr.find('#pre_uni').val($('option:selected', this).attr('p-price'));
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

        $('body').on('click', '.btnAddBrand', function() {
            $('#add_brand').val('');
            $('#mdl_add_brand').modal('show');
        });


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



        $('#btngoShopping').click(function() {
            window.location.href = '{{route('logistic.purchases')}}';
        });

        $("#frm_shopping").validator().on('submit', function(e) {
            let that = this;
            let error = false;
            if(this.click === true) {
                e.preventDefault();
            }
            $('#btnSave').attr('disabled');
            that.click = true;
            if (e.isDefaultPrevented()) {
                toastr.warning('Debe llenar todos los campos obligatorios.');
                that.click = false;
                error = true;
                e.preventDefault();
            } else {
                e.preventDefault();
                $('.producto').each(function(current, element) {
                    let parent = $(element).parent().parent().parent().parent();
                    let typePurchase = $(parent).find('.slecTypePurchase').val();
                    if (typePurchase == 1) {
                        let warehouse = parent.find('.warehouse').val();
                        console.log(warehouse);
                        if (warehouse == '') {
                            toastr.warning('Debes seleccionar un almacén!');
                            that.click = false;
                            error = true;
                            e.preventDefault();
                        }
                    } else {
                        let centerCost = parent.find('.centerCost').val();
                        console.log(centerCost);
                        if (centerCost == '') {
                            toastr.warning('Debes seleccionar un centro de costos!');
                            that.click = false;
                            error = true;
                            e.preventDefault();
                        }
                    }
                });

                if (error === false) {
                    let data = $('#frm_shopping').serialize();
                    $.ajax({
                        url: '{{ route('saveShopping') }}',
                        type: 'post',
                        data: data + '&_token=' + '{{ csrf_token() }}',
                        dataType: 'json',
                        beforeSend: function() {
                            $('#btnSave').attr('disabled');
                        },
                        complete: function() {

                        },
                        success: function(response) {
                            if(response == true) {
                                toastr.success('Se grabó satisfactoriamente la Compra');
                                window.location.href = '{{route('logistic.purchases')}}';
                            } else if(response == -1) {
                                toastr.error('Debe Ingresar al menos un Producto o Servicio');
                            } else if (response == -18) {
                                toastr.warning('La compra ya se encuentra registrada');
                            } else {
                                toastr.info(response.responseText);
                            }
                        },
                        error: function(response) {
                            console.log(response.responseText);
                            toastr.error('Ocurrio un error');
                            $('#btnSave').removeAttr('disabled');
                        }
                    });
                }
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

        var date = new Date();

        $('#date').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
            startDate: date,
            // endDate: enddate
        });

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
                $('#showPayments').hide();
            } else if ($(this).val() == 'DEPOSITO EN CUENTA') {
                $('#contOptionDepositoCofre').hide();
                $('#contOptionDepositoCofre div select').attr('disabled', true);
                $('#contOptionDepositoCuenta').show();
                $('#contOptionDepositoCuenta div select').attr('disabled', false);
                $('#contOptionDepositoPOS').hide();
                $('#contOptionDepositoPOS div select').attr('disabled', true);
                $('#showPayments').hide();
            } else if ($(this).val() == 'TARJETA DE CREDITO' || $(this).val() == 'TARJETA DE DEBITO') {
                $('#contOptionDepositoCofre').hide();
                $('#contOptionDepositoCofre div select').attr('disabled', true);
                $('#contOptionDepositoCuenta').hide();
                $('#contOptionDepositoCuenta div select').attr('disabled', true);
                $('#contOptionDepositoPOS').show();
                $('#contOptionDepositoPOS div select').attr('disabled', false);
                $('#showPayments').hide();
            } else if ($(this).val() == 'CREDITO') {
                $('#contOptionDepositoCofre').hide();
                $('#contOptionDepositoCofre div select').attr('disabled', true);
                $('#contOptionDepositoCuenta').hide();
                $('#contOptionDepositoCuenta div select').attr('disabled', true);
                $('#contOptionDepositoPOS').hide();
                $('#contOptionDepositoPOS div select').attr('disabled', true);
                $('#amountPendingModal').val($('#mountPayment').val())
                $('#showPayments').show();
                $('#creditMdl').modal('show');
            } else {
                $('#contOptionDepositoCofre').hide();
                $('#contOptionDepositoCofre div select').attr('disabled', true);
                $('#contOptionDepositoCuenta').hide();
                $('#contOptionDepositoCuenta div select').attr('disabled', true);
                $('#contOptionDepositoPOS').hide();
                $('#contOptionDepositoPOS div select').attr('disabled', true);
                $('#showPayments').hide();
            }
        });
        const html5QrCode = new Html5Qrcode("reader");

        $('#showQrModal').click(function(e) {
            e.preventDefault();
            $('#qrModal').modal('show');


            const qrCodeSuccessCallback = (decodedText, decodedResult) => {
                let data = decodedText.split('|');
                $('#shoppingSerie').val(data[2])
                $('#shoppingCorrelative').val(data[3])
                $('#fecha').val(moment(data[6]).format('DD-MM-YYYY'))

                let providerId = $(`#provider option[data-document=${data[0]}]`).attr('value');
                let typeDocId = $(`#tipdoc option[data-code=${data[1]}]`).attr('value');
                $(`#provider`).val(providerId).trigger('change')
                $(`#provider option[data-document=${data[0]}]`).attr('selected', true);
                $(`#tipdoc`).val(typeDocId)

                html5QrCode.stop().then(ignore => {
                    $('#qrModal').modal('hide');
                }).catch(err => {
                    console.log("Unable to stop scanning.");
                });
            };
            const config = { fps: 10, qrbox: { width: 250, height: 250 } };
            html5QrCode.start({ facingMode: "environment" }, config, qrCodeSuccessCallback);
        })

        $('#qrModal').on('hidden.bs.modal', function (event) {
            html5QrCode.stop().then(ignore => {
            }).catch(err => {
                console.log("Unable to stop scanning.");
            });
        })

        $('#moneda').change(function(e) {
            e.preventDefault()
            let date = moment($('#fecha').val(), 'DD-MM-YYYY').add(1, "days").format('DD-MM-YYYY')
            if ($(this).val() == 2) {
                $.get(`/get-exchangerate/by-date/${date}`, function(response) {
                    $('#exchange_rate').val(response.venta);
                }, 'json');
            }
        })
    </script>
@stop
