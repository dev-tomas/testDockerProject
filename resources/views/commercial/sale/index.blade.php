@extends('layouts.azia')
@section('css')
    <style>.cancelOrCancel,.disable-voucher,.creditNote,.debitNote,.change_payment,#btnSale1,#btnSale2{display: none}</style>
    @can('ventas.lows')
        <style>.cancelOrCancel,.disable-voucher{display: inline-block;}</style>
    @endcan
    @can('ventas.creditnote')
        <style>.creditNote{display: inline-block;}</style>
    @endcan
    @can('ventas.boletacreate')
        <style>.debitNote{display: inline-block;}</style>
    @endcan
    @can('ventas.debitnote')
        <style>.debitNote{display: inline-block;}</style>
    @endcan
    @can('ventas.facturacreate')
        <style>#btnSale1{display: inline-block;}</style>
    @endcan
    @can('ventas.boletacreate')
        <style>#btnSale2{display: inline-block;}</style>
    @endcan
    @can('ventas.cambiarformapago')
        <style>.change_payment{display: inline-block;}</style>
    @endcan
    <style>
        /* .dropdown-menu {   position: fixed; } */
        .table-responsive,
        .dataTables_scrollBody {
            overflow: visible !important;
        }

        .table-responsive-disabled .dataTables_scrollBody {
            overflow: hidden !important;
        }
    </style>
@stop
@section('content')
    <input type="hidden" id="type_send_boletas" value="{{ auth()->user()->headquarter->client->type_send_boletas }}">
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header color-gray">
                    <div class="row">
                        <div class="col-12 col-md-9">
                            <h3 class="card-title">TRANSACCIONES DE VENTA</h3>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="btn-group float-md-right" role="group">
                                <button id="btnTransactions" class="btn btn-primary-custom dropdown-toggle" data-toggle="dropdown">Nueva Transacción</button>
                                <div class="dropdown-menu" aria-labelledby="btnTransactions">
                                    <a class="dropdown-item" id="btnSale1" href="#">Factura</a>
                                    <a class="dropdown-item" id="btnSale2" href="#">Boleta</a>
                                    <a class="dropdown-item" href="/account/notes/credit">Nota de Crédito</a>
                                    <a class="dropdown-item" href="/account/notes/debit">Nota de Débito</a>
                                    <a class="dropdown-item" href="#">Comprobante de Contingencia</a>
                                    <a class="dropdown-item" href="#">Subir Comprobantes de Contingencia</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-12 col-md-2">
                            <button type="button" id="btnExcelSales"  class="btnSale btn btn-secondary-custom">
                                <i class="fa fa-download"></i>
                                Excel
                            </button>
                        </div>
                        <!--<div class="col-12 col-md-10">
                            @can('ventas.boletacreate')
                                <button id="btnSale2" type="button" class="btnSale btn btn-primary-custom pull-left">
                                    Nueva Boleta
                                </button>
                            @endcan
                            @can('ventas.facturacreate')
                                <button id="btnSale1" type="button" class="btnSale btn btn-primary-custom ml-2 pull-left">
                                    Nueva Factura
                                </button>
                            @endcan
                            {{-- <a href="{{ route('noteList') }}" class="btn btn-primary-custom pull-left">NOTAS CREDTIO/DEBITO</a> --}}
                        </div>-->
                    </div>
                </div>
                <input type="hidden" value="{{$idQuotation}}" id="idQuotaion" />
                <input type="hidden" value="{{ auth()->user()->headquarter->client->document}}" id="rucclient" />
                <div class="card-body">
                    <div class="row my-4">
                        <div class="col-12 col-md-6">
                            <p>Sin pagar en los últimos 365 días</p>
                            <div class="row">
                                <div class="col-12 col-md-7 py-2 text-white" style="background: #FB7E00;">
                                    <p style="font-size: 1.5em; margin: 0;">S/. <span>{{ number_format($defeated->sum('debt'),2,'.',',') }}</span></p>
                                    <p><span>{{ $defeated->count() }}</span> VENCIDO</p>
                                </div>
                                <div class="col-12 col-md-5 py-2" style="background: #d1d3d6;">
                                    <p style="font-size: 1.5em; margin: 0;">S/. <span>{{ number_format($pend,2,'.',',') }}</span></p>
                                    <p><span>{{ count($pending) }}</span> FACTURA PENDIENTE</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <p>Pagado</p>
                            <div class="row">
                                <div class="col-12 col-md-6 py-2 text-white" style="background: #7DCD00;">
                                    <p style="font-size: 1.5em; margin: 0;">S/. <span>{{ number_format($paidLastMonth['total'],2,'.',',') }}</span></p>
                                    <p><span>{{ $paidLastMonth['count'] }}</span> PAGADOS</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-3">
                            <div class="form-group">
                                <label for="">Buscar Entidad</label>
                                <input type="text" id="denomination" class="form-control" placeholder="Ingresar Entidad">
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="form-group">
                                <label for="">Buscar Documento</label>
                                <input type="text" id="document" class="form-control" placeholder="Ingresar documento">
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="form-group">
                                <label for="">Filtro por Fechas</label>
                                <input type="text" id="filter_date" class="form-control" placeholder="Seleccionar fechas">
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="form-group">
                                <label for="">Filtro por Estado (Aplicado según "Filtro por Fechas")</label>
                                <select id="filter_status" class="form-control">
                                    <option value="1">Todo los Estados</option>
                                    <option value="4">Pagados</option>
                                    <option value="2">Pendientes</option>
                                    <option value="3">Vencidos</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="table-responsive">
                            <table id="tbl_data" class="dt-bootstrap4 table-hover" style="width: 100%;">
                                <thead>
                                <th>*</th>
                                <th width="60">FECHA</th>
                                <th>T. DOC.</th>
                                <th>SERIE</th>
                                <th>NUM.</th>
                                <th>RUC/DNI/ETC</th>
                                <th>DENOMINACIÓN</th>
                                <th>M.</th>
                                <th width='50px'>TOTAL ONEROSA</th>
                                <th width='50px'>TOTAL PENDIENTE</th>
                                <th>ENVIADO AL CLIENTE</th>
                                <th>PDF</th>
                                <th>XML</th>
                                <th>CDR</th>
                                <th>ESTADO EN LA SUNAT</th>
                                <th width='50px'>ESTADO</th>
                                <th>*</th>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr style="background: #f7f7f7">
                                        <td colspan="17">
                                            <div class="row">
                                                <div class="col-12 col-md-4">
                                                    <div class="row py-2">
                                                        <div class="col-6 text-right"><strong>Total de Facturas S/</strong></div>
                                                        <div class="col-6"><span id="totalFacturasSoles"></span></div>
                                                    </div>
                                                    <div class="row py-2">
                                                        <div class="col-6 text-right"><strong>Total de Boletas S/</strong></div>
                                                        <div class="col-6"><span id="totalBoletasSoles"></span></div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <div class="row py-2">
                                                        <div class="col-6 text-right"><strong>Total Ventas Soles S/</strong></div>
                                                        <div class="col-6"><span id="totalSoles"></span></div>
                                                    </div>
                                                    <div class="row py-2">
                                                        <div class="col-6 text-right"><strong>Docs. Realizados</strong></div>
                                                        <div class="col-6"><span id="totalDocs"></span></div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <div class="row py-2">
                                                        <div class="col-6 text-right"><strong>Total Pendiente S/</strong></div>
                                                        <div class="col-6"><span id="totalPending"></span></div>
                                                    </div>
                                                    <div class="row py-2">
                                                        <div class="col-6 text-right"><strong>Docs. Pendientes</strong></div>
                                                        <div class="col-6"><span id="docPending"></span></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr style="background: #f7f7f7">
                                        <td colspan="17">
                                            <div class="row">
                                                <div class="col-12 col-md-4">
                                                    <div class="row py-2">
                                                        <div class="col-6 text-right"><strong>Total de Facturas $</strong></div>
                                                        <div class="col-6"><span id="totalFacturasUsd"></span></div>
                                                    </div>
                                                    <div class="row py-2">
                                                        <div class="col-6 text-right"><strong>Total de Boletas $</strong></div>
                                                        <div class="col-6"><span id="totalBoletasUsd"></span></div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <div class="row py-2">
                                                        <div class="col-6 text-right"><strong>Total Ventas Soles $</strong></div>
                                                        <div class="col-6"><span id="totalUsd"></span></div>
                                                    </div>
                                                    <div class="row py-2">
                                                        <div class="col-6 text-right"><strong>Docs. Realizados</strong></div>
                                                        <div class="col-6"><span id="totalDocsUsd"></span></div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <div class="row py-2">
                                                        <div class="col-6 text-right"><strong>Total Pendiente $</strong></div>
                                                        <div class="col-6"><span id="totalPendingUsd"></span></div>
                                                    </div>
                                                    <div class="row py-2">
                                                        <div class="col-6 text-right"><strong>Docs. Pendientes$</strong></div>
                                                        <div class="col-6"><span id="docPendingUsd"></span></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if(session()->has('id'))
        <input type="hidden" id="ncid" value="{{ session()->get('id') }}">
    @endif
    @if(session()->has('idd'))
        <input type="hidden" id="ndid" value="{{ session()->get('idd') }}">
    @endif

    <div class="modal fade" id="mdlCreditNote" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    {{-- <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5> --}}
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <h1 style="font-size: 1.5em !important;">NOTA DE CRÉDITO ELECTRÓNICA</h1>
                    <h3 class="font-weight-light mb-2 mt-2" style="font-size: 1.5em !important;" id="nc_correlative"></h3>

                    {{-- <fieldset class="mb-2">
                        <button class="btn btn-primary-custom btn-block" id="showPDFNC" data-id="">IMPRIMIR</button>
                    </fieldset> --}}
                    <fieldset class="mb-2">
                        <div class="row">
                            <div class="col-md-6"><button class="btn btn-danger-custom btn-block" id="showPDFNC" data-id="">VER PDF</button></div>
                            <div class="col-md-6"><button class="btn btn-secondary-custom btn-block" id="showXMLNC">DESCARGAR XML</button></div>
                        </div>
                    </fieldset>
                    <fieldset class="mb-2">
                        <ul class="list-unstyled">
{{--                            <li><a href="#" class="btn-link">Enviar email</a></li>--}}
                            <li><a href="/commercial.sales.create/1" class="btn-link">Generar otra Factura de Venta</a></li>
                            <li><a href="/commercial.sales.create/2" class="btn-link">Generar otra Boleta de Venta</a></li>
                        </ul>
                    </fieldset>
                    {{-- <fieldset class="mb-2">
                        <button class="btn btn-danger-custom btn-block" id="">ANULAR o comunicar baja</button>
                    </fieldset> --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdlDebitNote" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    {{-- <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5> --}}
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <h1 style="font-size: 1.5em !important;">NOTA DE DÉBITO ELECTRÓNICA</h1>
                    <h3 class="font-weight-light mb-2 mt-2" style="font-size: 1.5em !important;" id="nd_correlative"></h3>
                    {{--
                                        <fieldset class="mb-2">
                                            <button class="btn btn-primary-custom btn-block" id="printND" data-id="">IMPRIMIR</button>
                                        </fieldset> --}}
                    <fieldset class="mb-2">
                        <div class="row">
                            <div class="col-md-6"><button class="btn btn-danger-custom btn-block" id="showPDFND" data-id="">VER PDF</button></div>
                            <div class="col-md-6"><button class="btn btn-secondary-custom btn-block" id="showXMLND">DESCARGAR XML</button></div>
                        </div>
                    </fieldset>
                    <fieldset class="mb-2">
                        <ul class="list-unstyled">
{{--                            <li><a href="#" class="btn-link">Enviar email</a></li>--}}
                            <li><a href="/commercial.sales.create/1" class="btn-link">Generar otra Factura de Venta</a></li>
                            <li><a href="/commercial.sales.create/2" class="btn-link">Generar otra Boleta de Venta</a></li>
                        </ul>
                    </fieldset>
                    {{-- <fieldset class="mb-2">
                        <button class="btn btn-danger-custom btn-block" id="">ANULAR o comunicar baja</button>
                    </fieldset> --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <div id="mdl_preview" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <button class="btn btn-primary-custom btnPrint" id="">IMPRIMIR</button>
                            <!--<button class="btn btn-secondary-custom btnOpen" id="0">Abrir en navegador</button>-->

                            {{--<button class="btn btn-dark-custom btnSend" id="0">Enviar al Cliente</button>--}}
                            <button class="btn btn-danger-custom pull-right" id="btnClose">
                                <i class="fa fa-close"></i>
                            </button>
                        </div>
                        <div class="col-12">
                            <iframe frameborder="0" width="100%;" height="700px;" id="frame_pdf">

                            </iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="mdl_status" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="z-index: 999999;">
        <div class="modal-dialog modal-xs">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <table class="table" id="tbl_status"><tbody></tbody></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="frm_payment">
        <div class="modal fade" id="mdlPayment" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" style="z-index: 9999;">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Recibir Pago</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label><strong>Fecha</strong></label>
                                    <p id="creditDate"></p>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label><strong>Documento</strong></label>
                                    <p id="creditDocument"></p>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label><strong>Total Documento</strong></label>
                                    <p id="creditTotal"></p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label><strong>Método de Pago</strong></label>
                                </div>
                            </div>
                            <div class="col-12 col-md-2">
                                <div class="form-group">
                                    <label><strong>Fecha de Pago</strong></label>
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label><strong>Depositar en</strong></label>
                                </div>
                            </div>
                            <div class="col-12 col-md-2">
                                <div class="form-group">
                                    <label><strong>Operación</strong></label>
                                </div>
                            </div>
                            <div class="col-12 col-md-2">
                                <div class="form-group">
                                    <label><strong>Importe Recibido</strong></label>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="rTableBody"></div>
                        <div class="row" id="inputsNewForm">
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <select name="payment_type" id="payment_type" class="form-control" required>
                                        {{-- <option value="">Seleccione Tipo de Pago</option> --}}
                                        <option value="EFECTIVO" selected>EFECTIVO</option>
                                        <option value="DEPOSITO EN CUENTA">DEPOSITO EN CUENTA</option>
                                        <option value="TARJETA DE CREDITO">TARJETA DE CREDITO</option>
                                        <option value="TARJETA DE DEBITO">TARJETA DE DEBITO</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-2">
                                <div class="form-group">
                                    <input type="text" class="form-control datepicker" id="payment_date" name="payment_date" required autocomplete="off">
                                    <input type="hidden" name="credit_id" id="ci">
                                </div>
                            </div>
                            <div class="col-12 col-md-3" id="contOptionDepositoCofre">
                                <div class="form-group">    
                                    <select name="cash" id="cash" class="form-control">
                                        @foreach ($cashes as $cash)
                                            <option value="{{ $cash->id }}">{{ $cash->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-3" id="contOptionDepositoCuenta">
                                <div class="form-group">
                                    <select name="bank" id="" class="form-control">
                                        @foreach ($bankAccounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->bank_name }} - {{ $account->number }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-3" id="contOptionDepositoPOS">
                                <div class="form-group">
                                    <select name="mp" id="" class="form-control">
                                        @foreach ($paymentMethods as $paymentMethod)
                                            <option value="{{ $paymentMethod->id }}">{{ $paymentMethod->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-2">
                                <div class="form-group">
                                    <input type="text" name="operation_bank" id="operation_bank" class="form-control" autocomplete="off" >
                                </div>
                            </div>
                            <div class="col-12 col-md-2">
                                <div class="form-group">
                                    <input type="number" step="0.01" name="payment_mont" id="payment_mont" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <br><br>
                        <div class="row">
                            <div class="col-6 text-right">
                                <strong>DEUDA PENDIENTE:</strong>
                            </div>
                            <div class="col-6 text-left"><span id="montoRestante"></span></div>
                        </div>
                        <div class="row" id="msgSaldoRestante">
                            <div class="col-6 text-right">
                                <strong>SALDO RESTANTE:</strong>
                            </div>
                            <div class="col-6 text-left"><span id="saldoRestante"></span></div>
                        </div>
                        <div class="row" id="msgCancelled">
                            <div class="col-12 text-center">
                                <h2><strong style="font-size: 2.2em">FACTURA CANCELADA</strong></h2>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" id="save" class="btn btn-primary-custom">Grabar</button>
                    </div>
                </div>
            </div>
        </div> 
    </form>

    <form id="frm_change_payment">
        <div class="modal fade" id="mdlChangePayment" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" style="z-index: 9999;">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Cambiar Forma de Pago</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="change_payment_sale" name="change_payment_sale">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <h5 id="change_payment_document"></h5>
                            </div>
                            <div class="col-12 col-md-6">
                                <h5 id="change_payment_customer"></h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label for="condition">Forma de pago</label>
                                    <select name="change_condition" id="change_condition" class="form-control">
                                        <option value="EFECTIVO" data-days="0">EFECTIVO</option>
                                        <option value="DEPOSITO EN CUENTA" data-days="0">DEPOSITO EN CUENTA</option>
                                        <option value="TARJETA DE CREDITO" data-days="0">TARJETA DE CREDITO</option>
                                        <option value="TARJETA DE DEBITO" data-days="0">TARJETA DE DEBITO</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-3" id="changeContOptionDepositoCofre">
                                <div class="form-group">
                                    <label>Cofre</label>
                                    <select name="change_cash" id="change_cash" class="form-control">
                                        @foreach ($cashes as $cash)
                                            <option value="{{ $cash->id }}">{{ $cash->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-3" id="changeContOptionDepositoCuenta">
                                <div class="form-group">
                                    <label>Cuenta</label>
                                    <select name="change_bank" id="change_bank" class="form-control">
                                        @foreach ($bankAccounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->bank_name }} - {{ $account->number }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-3" id="changeContOptionDepositoPOS">
                                <div class="form-group">
                                    <label>Medio de Pago</label>
                                    <select name="change_method" id="change_method" class="form-control">
                                        @foreach ($paymentMethods as $paymentMethod)
                                            <option value="{{ $paymentMethod->id }}">{{ $paymentMethod->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label>Monto</label>
                                    <input type="text" class="form-control" id="changeMountPayment" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" id="save" class="btn btn-primary-custom">Grabar</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop

@section('script_admin')
    <script>
        $(document).ready(function() {
            $('#azSidebarToggle').click();
            $('#contOptionDepositoCuenta').hide();
            $('#contOptionDepositoPOS').hide();
            $('#changeContOptionDepositoPOS').hide();
            $('#changeContOptionDepositoCuenta').hide();

            getTotalSales();
        });
        function getTotalSales() {
            let data = $('#filter_date').val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '/comercial.getTotalSale',
                type: 'post',
                data: 'dates=' + data + '&denomination=' + $("#denomination").val() + '&document='+ $('#document').val() + '&status=' + $('#filter_status').val(),
                dataType: 'json',
                success: function(response) {
                    $('#totalFacturasSoles').text(response['salesFacturasSoles'])
                    $('#totalBoletasSoles').text(response['salesBoletasSoles'])
                    $('#totalSoles').text(response['salesSoles'])
                    $('#totalDocs').text(response['sales_count'])
                    $('#totalPending').text(response['totalPending'])
                    $('#docPending').text(response['docPending'])

                    $('#totalFacturasUsd').text(response['salesFacturasUsd'])
                    $('#totalBoletasUsd').text(response['salesBoletasUsd'])
                    $('#totalUsd').text(response['salesUsd'])
                    $('#totalDocsUsd').text(response['sales_count_usd'])
                    $('#totalPendingUsd').text(response['totalPendingUsd'])
                    $('#docPendingUsd').text(response['docPendingUsd'])
                },
                error: function(response) {
                    toastr.error(response.responseText);
                }
            });
        }
        $('#payment_type').change(function () {
            if ($(this).val() == 'EFECTIVO') {
                $('#contOptionDepositoCofre').show();
                $('#contOptionDepositoCofre div select').attr('disabled', false);
                $('#contOptionDepositoCuenta').hide();
                $('#contOptionDepositoCuenta div select').attr('disabled', true);
                $('#contOptionDepositoPOS').hide();
                $('#contOptionDepositoPOS div select').attr('disabled', true);
            } else if ($(this).val() == 'DEPOSITO EN CUENTA') {
                $('#contOptionDepositoCofre').hide();
                $('#contOptionDepositoCofre div select').attr('disabled', true);
                $('#contOptionDepositoCuenta').show();
                $('#contOptionDepositoCuenta div select').attr('disabled', false);
                $('#contOptionDepositoPOS').hide();
                $('#contOptionDepositoPOS div select').attr('disabled', true);
            } else if ($(this).val() == 'TARJETA DE CREDITO' || $(this).val() == 'TARJETA DE DEBITO') {
                $('#contOptionDepositoCofre').hide();
                $('#contOptionDepositoCofre div select').attr('disabled', true);
                $('#contOptionDepositoCuenta').hide();
                $('#contOptionDepositoCuenta div select').attr('disabled', true);
                $('#contOptionDepositoPOS').show();
                $('#contOptionDepositoPOS div select').attr('disabled', false);
            } else {
                $('#contOptionDepositoCofre').hide();
                $('#contOptionDepositoCofre div select').attr('disabled', true);
                $('#contOptionDepositoCuenta').hide();
                $('#contOptionDepositoCuenta div select').attr('disabled', true);
                $('#contOptionDepositoPOS').hide();
                $('#contOptionDepositoPOS div select').attr('disabled', true);
            }
        });
        var client = @json(Auth::user()->headquarter->client);
        let tbl_data = $("#tbl_data").DataTable({
            'pageLength' : 15,
            'bLengthChange' : false,
            'lengthMenu': false,
            'language': {
                'url': '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json'
            },
            "order": [[ 10, "desc"]],
            'searching': false,
            'processing': false,
            'serverSide': true,
            'ajax': {
                'url': '/commercial.dt.sales',
                'type' : 'get',
                'data': function(d) {
                    if( $('#idQuotaion').val() != null){
                        d.idQuotation = $('#idQuotaion').val();
                    }else{
                        d.idQuotation = null;
                    }
                    d.denomination = $('#denomination').val();
                    d.serial = $('#document').val();
                    d.status = $('#filter_status').val();

                    let rangeDates = $('#filter_date').val();
                    var arrayDates = rangeDates.split(" ");
                    var dateSpecificOne =  arrayDates[0].split("/");
                    var dateSpecificTwo =  arrayDates[2].split("/");

                    d.dateOne = dateSpecificOne[2]+'-'+dateSpecificOne[1]+'-'+dateSpecificOne[0];
                    d.dateTwo = dateSpecificTwo[2]+'-'+dateSpecificTwo[1]+'-'+dateSpecificTwo[0];
                    console.log(d.dateOne);
                    console.log(d.dateTwo);
                }
            },
            'columns': [
                {data: null},
                {
                    data: 'date',
                    // "width": "200px"
                },
                {
                    data: 'type_voucher.description',
                },
                {
                    data: 'serialnumber'
                },
                {
                    data: 'correlative'
                },
                {
                    data: 'customer.document'
                },
                {
                    data: 'customer.description'
                },
                {
                    data: 'coin.symbol'
                },
                {
                    data: 'total'
                },
                {
                    data: 'total'
                },
                {
                    data: 'id'
                },
                {
                    data: 'id'
                },
                {
                    data: 'id'
                },
                {
                    data: 'id'
                },
                {
                    data: 'id'
                },
                {
                    data: 'id'
                },
                {
                    data: 'id'
                }
            ],
            'fnRowCallback': function( nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                construct_buttons_for_columns('pdf', $(nRow).find('td:eq(11)'), aData['typevoucher_id'], aData);
                construct_buttons_for_columns('xml', $(nRow).find('td:eq(12)'), aData['typevoucher_id'], aData);
                construct_buttons_for_columns('cdr', $(nRow).find('td:eq(13)'), aData['typevoucher_id'], aData);
                construct_buttons_for_columns('state_sunat', $(nRow).find('td:eq(14)'), aData['typevoucher_id'], aData);
                construct_buttons_for_columns('options', $(nRow).find('td:eq(16)'), aData['typevoucher_id'], aData);
                construct_buttons_for_columns('column', $(nRow), aData['typevoucher_id'], aData);

                if (aData['status_condition'] == 0) {
                    $(nRow).find('td:eq(15)').text('Pendiente');
                } else if(aData['status_condition'] == 9) {
                    $(nRow).find('td:eq(15)').text('Anulado');
                    $(nRow).css({
                        'text-decoration': 'line-through',
                        'text-decoration-color': 'gray',
                        'color': 'gray'
                    });
                }else {
                    $(nRow).find('td:eq(15)').text('Pagado');
                }

                if (aData['sendemail'] == 0) {
                    $(nRow).attr('id', aData['id']);
                    $(nRow).find('td:eq(10)').html('<span class="badge bg-danger text-center"><i class="fa fa-close"></i></span>');
                } else {
                    $(nRow).attr('id', aData['id']);
                    $(nRow).find('td:eq(10)').html('<span class="badge bg-green-custom text-center"><i class="fa fa-check"></i></span>');
                }

                var fecha = aData['date'].split("-");

                $(nRow).find("td:eq(1)").html(fecha[2]+'-'+fecha[1]+'-'+fecha[0]);

                let totalDebt = "0.00";
                if (aData['credito'] != null) {
                    totalDebt = parseFloat(aData['credito']['debt']).toFixed(2);
                }

                $(nRow).find("td:eq(9)").html(totalDebt);
            },
            drawCallback: function () {
                $('.popoverStatus').popover({
                    "html": true,
                    trigger: 'hover',
                    placement: 'left',
                    "content": function () {
                        return "<div>Revisar en el resumen diario.</div>";
                    }
                });

                $('.popoverButton').popover({
                    "html": true,
                    trigger: 'hover',
                    placement: 'left',
                    "content": function () {
                        return "<div>EL CDR aparecerá después de que el Resumen Diario sea aprobado por SUNAT.</div>";
                    }
                });

                $('.popoverStatus').popover({
                    "html": true,
                    trigger: 'hover',
                    placement: 'left',
                    delay: {
                        'hide': 1000
                    },
                    "content": function () {
                        return "<div style='text-align: center;'>Pendiente de generación</div>" +
                            "<div style='text-align: center;'>El estado cambiará al día siguiente o más tardar hasta el SÉTIMO DÍA CALENDARIO. </b>" +
                            "Luego de que nuestro sistema GENERE y ENVÍE el RESUMEN DIARIO de las Boletas de venta y notas asociadas a la SUNAT (Esto es automático). " +
                            "Sin embargo, tambien se puede generar el CDR de forma manual. " +
                            "</div><br>" +
                            "<div style='text-align: center'><a target='_blank' href='{{route('generateSummary')}}' class='btn btn-primary-custom'>Generar Resumen Diario</a></div>";
                    }
                });
            }
        });

        $('#tbl_data').on('click', function(e){
            if($('.popoverButton').length > 1)
                $('.popoverButton').popover('hide');
            $(e.target).popover('toggle');
        });

        $('body').on('click', '.disable-voucher', function() {
            let data = tbl_data.row( $(this).parents('tr') ).data();

            $.confirm({
                icon: 'fa fa-warning',
                theme: 'modern',
                animation: 'scale',
                type: 'green',
                draggable: false,
                title: '¿Está seguro de anular el comprobante?',
                content: '',
                buttons: {
                    Confirmar: {
                        text: 'Confirmar',
                        btnClass: 'btn btn-green',
                        action: function() {
                            $.ajax({
                                type: 'post',
                                url: '/disable/voucher/' + data['id'] + '/2',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                },
                                dataType: 'json',
                                success: function(response) {
                                    if(response == true) {
                                        toastr.success('Se anuló satisfactoriamente el comprobante.');
                                    } else {
                                        toastr.error('Ocurrió un error cuando intentaba anular el comprobante.');
                                    }

                                    tbl_data.ajax.reload();
                                },
                                error: function(response) {
                                    console.log(response.responseText);
toastr.error('Ocurrio un error');
                                    console.log(response.responseText)
                                }
                            });
                        }
                    },
                    Cancelar: {
                        text: 'Cancelar',
                        btnClass: 'btn btn-red'
                    }
                }
            });
        });

        $('body').on('click', '.cancelOrCancel', function() {
            let data = tbl_data.row( $(this).parents('tr') ).data();

            $.confirm({
                icon: 'fa fa-warning',
                theme: 'modern',
                animation: 'scale',
                type: 'green',
                draggable: false,
                title: '¿Está seguro de anular el comprobante?',
                content: function() {
                    let value = '';

                    return '<div class="form-group">' +
                        '<label style="color: #000 !important;"><strong>Motivo</strong></label>' +
                        '<input type="text" id="motive" name="motive" class="form-control" value="' + value + '" />' +
                        '<option></option>' +
                        '</div>'
                },
                buttons: {
                    Confirmar: {
                        text: 'Confirmar',
                        btnClass: 'btn btn-green',
                        action: function() {
                            $.ajax({
                                type: 'post',
                                url: '/commercial/send/low/communication',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    id: data['id'],
                                    motive: $('#motive').val(),
                                    type: 1
                                },
                                dataType: 'json',
                                success: function(response) {
                                    if(response === true) {
                                        toastr.success('Se anuló satisfactoriamente el comprobante');
                                        toastr.success('El comprobante fue enviado a Sunat satisfactoriamente');
                                    } else if(response === '-1') {
                                        toastr.success('Se anuló satisfactoriamente el Comprobante');
                                        toastr.warning('Ocurrió un error con el comprobante, reviselo y vuelva a enviarlo.');toastr.warning('Debe de generar correlativos para nota de credito');
                                    } else if(response === '-2') {
                                        toastr.success('Se anuló satisfactoriamente el Comprobante');
                                        toastr.error('El comprobante fue enviado a Sunat y fue rechazado automáticamente, vuelva a enviarlo manualmente');
                                    } else if(response === '-3') {
                                        toastr.success('Se anuló satisfactoriamente el Comprobante');
                                        toastr.info('El comprobante fue enviado a Sunat y fue validado con una observación.');
                                    } else {
                                        toastr.success('Se anuló satisfactoriamente el Comprobante');
                                        toastr.error('Los servidores de la Sunat están teniendo problemas.');
                                    }

                                    tbl_data.ajax.reload();
                                    location.reload();
                                },
                                error: function(response) {
                                    console.log(response.responseText);
                                    toastr.error('Ocurrio un error');
                                    console.log(response.responseText)
                                }
                            });
                        }
                    },
                    Cancelar: {
                        text: 'Cancelar',
                        btnClass: 'btn btn-red'
                    }
                }
            });
        });

        $('body').on('click', '.search', function(){
            let d = tbl_data.row( $(this).parents('tr') ).data();
            let icon = '<i class="fa fa-remove"></i>';
            let icon_accept = '<i class="fa fa-remove"></i>';
            if(d['status_sunat'] == 1) {
                icon = '<i class="fa fa-check"></i>';
            }

            if(d['response_sunat'] == 1) {
                icon_accept = '<i class="fa fa-check"></i>';
            }

            let data = '<tr>';
            data += '<td>Enviada a la Sunat</td><td>' + icon + '</td>'
            data += '</tr>';
            data += '<tr>';
            data += '<td>Aceptada por la Sunat</td><td>' + icon_accept + '</td>'
            data += '</tr>';
            data += '<tr>';
            let scode = '';
            if (d['sunat_code'] != null) {
                scode = d['sunat_code']['code'];
            } else {
                scode = '-';
            }
            data += '<td>Código</td><td>' +scode  + '</td>'
            data += '</tr>';
            data += '<tr>';
            let sdescription = '';
            if (d['sunat_code']['description'] != null) {
                sdescription = d['sunat_code']['description'];
            } else {
                sdescription = '-';
            }
            data += '<td>Descripción</td><td>' + sdescription + '</td>'
            data += '</tr>';
            data += '<tr>';
            let s_what = '';
            if (d['sunat_code']['what_to_do'] != null) {
                s_what = d['sunat_code']['what_to_do'];
            } else {
                s_what = 'Nada';
            }
            data += '<td>¿Que hacer?</td><td>' + s_what + '</td>'
            data += '</tr>';
            $('#tbl_status tbody').html('');
            $('#tbl_status tbody').append(data);
            $('#mdl_status').modal('show');
        });

        /***@author Daniel Lopez
         * Editar Sale
         */
        $('body').on('click', '.editSale', function (e) {
            e.preventDefault();

            let data = tbl_data.row( $(this).parents('tr') ).data();
            if(data == undefined) {
                tbl_data = $('#tbl_data').DataTable();
                data = tbl_data.row( $(this).parents('tr') ).data();
            }

            let id = data['id'],
                td = data['typevoucher_id'];

            window.location.href = '/commercial.sales.edit/'+ td +'/' + id;
        });


        /**
         * Send Client
         */
        $('body').on('click', '.send_client', function() {
            let data = tbl_data.row( $(this).parents('tr') ).data();
            if(data == undefined) {
                tbl_data = $('#tbl_data').DataTable();
                data = tbl_data.row( $(this).parents('tr') ).data();
            }

            let id = data['id'];

            $.confirm({
                icon: 'fa fa-question',
                theme: 'modern',
                animation: 'scale',
                title: '¿Está seguro de enviar esta venta?',
                content: '',
                type: 'green',
                buttons: {
                    Confirmar: {
                        text: 'Confirmar',
                        btnClass: 'btn-green',
                        action: function(){
                            $.ajax({
                                type: 'post',
                                url: '/commercial.sales.send/' + id,
                                data: {
                                    _token: '{{ csrf_token() }}'
                                },
                                dataType: 'json',
                                success: function(response) {
                                    if(response == true) {
                                        $.confirm({
                                            icon: 'fa fa-check',
                                            title: 'Mensaje enviado!',
                                            theme: 'modern',
                                            type: 'green',
                                            autoClose: 'Cerrar|2000',
                                            buttons: {
                                                Cerrar: {
                                                    text: 'Cerrar',
                                                    btnClass: 'btn-red',
                                                    action: function() {}
                                                }

                                            },
                                            content: function() {
                                                var self = this;
                                                return
                                            }
                                        });
                                        tbl_data.ajax.reload();
                                    } else if(response == -5) {
                                        toastr.warning('El Cliente no tiene un correo electrónico configurado.');
                                    } else {
                                        toastr.error(response);
                                    }

                                },
                                error: function(response) {
                                    console.log(response.responseText);
                                    toastr.error('Ocurrio un error  ');
                                    console.log(response.responseText)
                                }
                            });
                        }
                    },
                    Cancelar: {
                        text: 'Cancelar',
                        btnClass: 'btn-red',
                        action: function(){
                        }
                    },
                }
            });
        });

        $('#tbl_data').on('click', '.creditNote', function() {
            let data = tbl_data.row( $(this).parents('tr') ).data();
            window.location = '/commercial/sale/note/' + data['id'] + '/07/1';
        });

        $('#tbl_data').on('click', '.debitNote', function() {
            let data = tbl_data.row( $(this).parents('tr') ).data();
            window.location = '/commercial/sale/note/' + data['id'] + '/07/4';
        });

        /**
         *btnOpen
         **/
        $('body').on('click', '.btnOpen', function(){
            let id = $(this).attr('id');
            let src = $('#frame_pdf').attr('src');
            window.open(src + '_blank');
        });

        /**
         *btnPrint
         **/
        $('body').on('click', '.btnPrint', function(){
            $("#frame_pdf").get(0).contentWindow.print();
        });


        /**
         *btnSend
         **/
        $('body').on('click', '.btnSend', function() {
            let id = $(this).attr('id');
            $('#mdl_preview').modal('hide');
            let data = tbl_data.row( $(this).parents('tr') ).data();
            $.confirm({
                icon: 'fa fa-question',
                theme: 'modern',
                animation: 'scale',
                title: '¿Está seguro de enviar esta venta?',
                content: function() {
                    let value = '';

                    return '<div class="form-group">' +
                        '<input type="text" id="email" name="email" class="form-control" value="' + value + '" />' +
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
                                            email: $('#email').val(),
                                        },
                                        dataType: 'json',
                                        success: function(response) {
                                            if(response == true) {
                                                toastr.success('Se envió satisfactoriamente la cotización');
                                                tbl_data.ajax.reload();
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
                        action: function(){}
                    }
                }
            });
        });


        /**
         *btnClose
         **/
        $('body').on('click', '#btnClose', function(){
            $('#mdl_preview').modal('hide');
        })


        $('body').on('click', '#showXMLNC', function () {
            let id = $('#showPDFNC').attr('data-id');
            $.ajax({
                type: 'post',
                url: '{{route('getNote')}}',
                data: {
                    nc_id: id,
                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function(response) {
                    let file = '/commercial.download.xml/' + '{{Auth::user()->headquarter->client->document}}' + '-';
                    file += response['type_voucher']['code'] + '-' + response['serial_number'] + '-' + response['correlative'];
                    window.open(file, '_blank');
                }
            });
        });

        $('body').on('click', '#showXMLND', function () {
            let id = $('#showPDFNC').attr('data-id');
            $.ajax({
                type: 'post',
                url: '{{route('getDebitNote')}}',
                data: {
                    nc_id: id,
                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function(response) {
                    let file = '/commercial.download.xml/' + '{{Auth::user()->headquarter->client->document}}' + '-';
                    file += response['type_voucher']['code'] + '-' + response['serial_number'] + '-' + response['correlative'];
                    window.open(file, '_blank');
                }
            });
        });

        /**
         * view pdf
         */
        $('body').on('click', '#showPDFNC', function() {
            let id = $(this).attr('data-id');
            $.ajax({
                type: 'get',
                url: '/commercial.sale.note.pdf/' + id,
                dataType: 'json',
                success: function(response) {
                    $('#frame_pdf').attr('src', '/storage/' + response);
                    $('#mdl_preview').modal('show');
                }
            });
        });

        $('body').on('click', '#showPDFND', function() {
            let id = $(this).attr('data-id');
            $.ajax({
                type: 'get',
                url: '/commercial.sale.note.debit.pdf/' + id,
                dataType: 'json',
                success: function(response) {
                    $('#frame_pdf').attr('src', '/storage/' + response);
                    $('#mdl_preview').modal('show');
                }
            });
        });

        $('body').on('click', '.pdf', function() {
            let that = this;
            let data = tbl_data.row( $(this).parents('tr') ).data();
            let pdf_file = '/storage/' + 'pdf/' + client.document + '/' + data['serialnumber'] + '-' + data['correlative'] + '.pdf';
            $.ajax({
                url: '{{route('searchFile')}}',
                type: 'get',
                data: {
                    _token: '{{csrf_token()}}',
                    file_path: pdf_file
                },
                dataType: 'json',
                success: function (response) {
                    if(response === true) {
                        let id = $(that).attr('id');
                        $('.btnOpen').attr('id', id);
                        $('.btnSend').attr('id', id);
                        $('.btnPrint').attr('id', id);
                        $('#frame_pdf').attr('src', pdf_file);

                        $('#mdl_preview').modal('show');
                    }
                }
            });
        });

        /**
         * Convert to Voucher
         */
        $('body').on('click', '.print', function() {
            var id = $(this).parent().parent().parent().parent().parent().parent().attr('id');
            window.open('/commercial.sales.print/');
        });

        $('body').on('click', '.send', function() {
            var id = $(this).parent().parent().parent().parent().parent().parent().attr('id');
            $.confirm({
                icon: 'fa fa-question',
                theme: 'modern',
                animation: 'scale',
                title: '¿Está seguro de enviar este comprobante?',
                content: '',
                buttons: {
                    Confirmar: function () {
                        $.ajax({
                            url: '/commercial.sales.sunat.send/' + id,
                            type: 'post',
                            data: {
                                _token: '{{ csrf_token() }}',
                                sale_id: id
                            },
                            dataType: 'json',
                            beforeSend: function() {

                            },
                            complete: function() {

                            },
                            success: function(response) {
                                console.log(response);
                                if(response == true) {
                                    toastr.success('Se envió satisfactoriamente el comprobante');
                                    //window.location = '/commercial.sales';
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

        $('#filter_date').daterangepicker({
            "minYear": 2000,
            "autoApply": false,
            "locale": {
                "format": "DD/MM/YYYY",
                "separator": " - ",
                "applyLabel": "Aplicar",
                "cancelLabel": "Cancelar",
                "fromLabel": "Desde",
                "toLabel": "Hasta",
                "customRangeLabel": "Custom",
                "weekLabel": "W",
                "daysOfWeek": ["Dom","Lun","Mar","Mie","Ju","Vi","Sab"],
                "monthNames": ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
                "firstDay": 0
            },
            "startDate": moment().startOf('month'),
            "endDate": moment().endOf('month'),
            "cancelClass": "btn-dark"
        }, function(start, end, label) {
            // console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
        });


        $('#filter_date').change(function() {
            tbl_data.ajax.reload();
            getTotalSales();
        });
        
        $('#filter_status').change(function() {
            tbl_data.ajax.reload();
            getTotalSales();
        });

        $('#denomination').on('keyup', function() {
            tbl_data.ajax.reload();
            getTotalSales();
        });

        $('#document').on('keyup', function() {
            tbl_data.ajax.reload();
            getTotalSales();
        });


        $('#btnSale1').click(function() {
            window.location.href = '/commercial.sales.create/1';
        });

        $('#btnSale2').click(function() {
            window.location.href = '/commercial.sales.create/2';
        });

        $('#tbl_data').on('click', '.xml', function() {
            let data = tbl_data.row( $(this).parents('tr') ).data();
            if(data == undefined) {
                tbl_data = $('#tbl_data').DataTable();
                data = tbl_data.row( $(this).parents('tr') ).data();
            }

            let file = '/commercial.download.xml/' + '{{Auth::user()->headquarter->client->document}}' + '-';
            file += data['type_voucher']['code'] + '-' + data['serialnumber'] + '-' + data['correlative'];
            window.open(file, '_blank');
        });

        $('#tbl_data').on('click', '.cdr', function() {
            let data = tbl_data.row( $(this).parents('tr') ).data();
            if(data == undefined) {
                tbl_data = $('#tbl_data').DataTable();
                data = tbl_data.row( $(this).parents('tr') ).data();
            }

            let file = '/files/cdr/' + 'R-' + '{{Auth::user()->headquarter->client->document}}' + '-';
            file += data['type_voucher']['code'] + '-' + data['serialnumber'] + '-' + data['correlative'];
            window.open(file + '.zip', '_blank');
        });

        $(document).ready(function() {
            let nc = $('#ncid').val();
            let nd = $('#ndid').val();

            // $('#mdlCreditNote').show();

            if (nc != undefined && nc != null) {
                $.ajax({
                    'url': '/commercial/sale/note/get?nc_id=' + nc,
                    'data': '&_token=' + '{{ csrf_token() }}',
                    'type': 'post',
                    success: function(response) {
                        $('#nc_correlative').text(response['serial_number'] + ' - ' + response['correlative']);
                        $('#showPDFNC').attr('data-id',response['id']);
                        $('#printNC').attr('data-id',response['id']);
                        $('#mdlCreditNote').modal('show');
                        console.log(response);
                    },
                });
            }

            if (nd != undefined && nd != null) {
                $.ajax({
                    'url': '/commercial/sale/note/debit/get?nd_id=' + nd,
                    'data': '&_token=' + '{{ csrf_token() }}',
                    'type': 'post',
                    success: function(response) {
                        console.log(response['serial_number']);
                        $('#nd_correlative').text(response['serial_number'] + ' - ' + response['correlative']);
                        $('#showPDFND').attr('data-id',response['id']);
                        $('#printNd').attr('data-id',response['id']);
                        $('#mdlDebitNote').modal('show');
                        console.log(response);
                    },
                });
            }
        });

        $('#tbl_data').on('click', '.sendSunat', function() {
            let data = tbl_data.row( $(this).parents('tr') ).data();
            $.confirm({
                icon: 'fa fa-warning',
                theme: 'modern',
                animation: 'scale',
                type: 'green',
                draggable: false,
                title: '¿Está seguro de enviar este comprobante?',
                content: '',
                buttons: {
                    Confirmar: {
                        text: 'Confirmar',
                        btnClass: 'btn btn-green',
                        action: function() {
                            $.ajax({
                                type: 'post',
                                url: '/commercial.sales.sunat.send/' + data['id'] + '/1',
                                data: {
                                    _token: '{{csrf_token()}}'
                                },
                                dataType: 'json',
                                success: function(response) {
                                    if(response == true) {
                                        toastr.success('Se envió satisfactoriamente el comprobante!');
                                        tbl_data.ajax.reload();
                                    } else {
                                        toastr.error('Ocurrió un error!');
                                    }
                                },
                                error: function(response) {
                                    toastr.error('Los servidores de la Sunat no están disponibles, vuevla a intentarlo mas tarde');
                                    console.log(response.responseText)
                                }
                            });
                        }
                    },
                    Cancelar: {
                        text: 'Cancelar',
                        btnClass: 'btn btn-red'
                    }
                }
            });
        });

        $('#btnExcelSales').click(function(e) {
            e.preventDefault();
            let data = $('#filter_date').val();
            let status = $('#filter_status').val();

            window.open(`/commercial.export?date=${data}&status=${status}`, '_blank');
        });

        function construct_buttons_for_columns(type, tr, type_voucher, data) {
            switch (type) {
                case 'xml':
                    construct_button_for_xml(tr, type_voucher, data['sunat_code']);
                    break;
                case 'pdf':
                    construct_button_for_pdf(tr, data);
                    break
                case 'cdr':
                    construct_button_for_cdr(tr, data);
                    break;
                case 'state_sunat':
                    construct_button_for_state_sunat(tr, data);
                    break;
                case 'options':
                    construct_button_for_options(tr, data);
                    break;
                case 'column':
                    construct_button_for_column(tr, data);
                    break;
            }
        }

        function construct_button_for_xml(tr, type_voucher, data) {
            tr.html('<button type="button" class="btn btn-default btn-sm xml"><i style="font-size: 25px;" class="fa fa-file-text-o"></i></button>');
        }

        function construct_button_for_pdf(tr, data) {
            tr.html('<button type="button" class="btn btn-default btn-sm pdf" id="'+data['id']+'"><i style="font-size: 25px;" class="fa fa-file-pdf-o"></i> </button>');
        }

        function construct_button_for_cdr(tr, data) {
            if ($('#type_send_boletas').val() == 1) {
                if (data['sunat_code'] !== null) {
                    if (data['sunat_code']['code'] >= 2000 && data['sunat_code']['code'] <= 3999) {
                        tr.html('<button type="button" class="btn btn-default btn-sm"><i style="font-size: 25px;" class="fa fa-warning"></i></button>');
                    } else {
                        tr.html('<button type="button" class="btn btn-default btn-sm cdr"><i style="font-size: 25px;" class="fa fa-file-zip-o"></i></button>');
                    }
                } else {
                    tr.html('<button class="btn btn-default popoverButton"><i style="font-size: 25px;" class="fa fa-spinner fa-pulse"></i></button>');
                }
            } else {
                if(data['sunat_code'] !== null) {
                    if(data['typevoucher_id'] === 2) {
                        if(data['sunat_code']['code'] >= 2000 && data['sunat_code']['code'] <= 3999) {
                            tr.html('<button type="button" class="btn btn-default btn-sm"><i style="font-size: 25px;" class="fa fa-warning"></i></button>');
                        } else {
                            tr.html('<button type="button" class="btn btn-default btn-sm cdr popoverStatus"><i style="font-size: 25px;" class="fa fa-file-zip-o"></i></button>');
                        }
                    } else {
                        if(data['sunat_code']['code'] >= 2000 && data['sunat_code']['code'] <= 3999) {
                            tr.html('<button type="button" class="btn btn-default btn-sm"><i style="font-size: 25px;" class="fa fa-warning"></i></button>');
                        } else {
                            tr.html('<button type="button" class="btn btn-default btn-sm cdr"><i style="font-size: 25px;" class="fa fa-file-zip-o"></i></button>');
                        }
                    }
                } else {
                    if(data['typevoucher_id'] === 1) {
                        tr.html('<button class="btn btn-default"><i style="font-size: 25px;" class="fa fa-spinner fa-pulse"></i></button>');
                    } else {
                        tr.html('<button class="btn btn-default popoverButton"><i style="font-size: 25px;" class="fa fa-spinner fa-pulse"></i></button>');
                    }
                }
            }
        }

        function construct_button_for_state_sunat(tr, data) {
            if(data['sunat_code'] !== null) {
                // if(data['typevoucher_id'] == 1) {
                    tr.html('<button type="button" class="btn btn-default btn-sm search">' +
                        '<i style="font-size: 25px;" class="fa fa-eye"></i></button>');
                // } else {
                //     tr.html('<span class="badge badge-secondary popoverStatus">PENDIENTE</span>');
                // }
            } else {
                tr.html('<span class="badge badge-secondary popoverStatus">PENDIENTE</span>');
            }
        }

        function construct_button_for_options(tr, aData) {
            let button = '<div class="btn-group">';
            button += '<button type="button" class="btn btn-secondary-custom dropdown-toggle dropdown-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Opciones';
            button += '</button>';
            button += '<div class="dropdown-menu dropdown-menu-right" x-placement="bottom-start" style="position: absolute; transform: translate3d(-56px, 33px, 0px); top: 0px; left: 0px; will-change: transform; right: 0px; width: 200px;">';

            /**
             * 2 = El comprobante está eliminado
             * 1 = El comprobante está activo
             * 3 = El comprobante está anulado
             **/

            if (aData['can_change_payment'] == 0 && aData['condition_payment'] != 'CREDITO' && aData['status'] == 1 && aData['low_communication_id'] == null) {
                button += '<a class="dropdown-item change_payment" href="#">Cambiar Forma de Pago</a>';
                button += '<div class="dropdown-divider"></div>';
            }

            if (aData['status_condition'] == 0) {
                button += '<a class="dropdown-item payment" href="#">Recibir Pago</a>';
                button += '<div class="dropdown-divider"></div>';
            }

            if (aData['status'] === 2) {
                // button += '<a class="dropdown-item editSale" href="#">Editar</a>';
                button += '<div class="dropdown-divider"></div>';
            } else if(aData['status'] === 1) {
                if(aData['sunat_code'] != null) {
                    /**
                     * El comprobante necesita corregirse
                     **/
                    if(aData['sunat_code']['code'] > 0 && aData['sunat_code']['code'] <= 1999) {
                        if(aData['typevoucher_id'] === 1) {
                            button += '<a class="dropdown-item sendSunat" href="#"  style="color: #ea1024;">Enviar a la SUNAT</a>';
                        }
                    }

                    /**
                     * El comprobante se envió satisfactoriamente o con excepciones
                     **/
                    if(aData['sunat_code']['code'] >= 4000 || aData['sunat_code']['code'] == 0) {
                        button += '<a class="dropdown-item send_client" href="#">Enviar por correo al Cliente</a>';
                        if(aData['typevoucher_id'] == 1 && aData['low_communication_id'] === null && aData['credit_note_id'] === null && aData['debit_note_id'] === null) {
                            button += '<a class="dropdown-item cancelOrCancel" href="#" style="color: #ea1024;">Anular o Comunicar de Baja</a>';
                        }

                        if(aData['typevoucher_id'] == 2 && aData['status'] == 1 && aData['credit_note_id'] === null && aData['debit_note_id'] === null) {
                            button += '<a class="dropdown-item disable-voucher" href="#" style="color: #ff0000">Anular Comprobante</a>';
                        }

                        if(aData['low_communication_id'] === null && aData['credit_note_id'] === null && aData['debit_note_id'] === null) {
                            button += '<a class="dropdown-item creditNote" href="/commercial/sale/note/' + aData['id'] + '/'+ aData['type_voucher']['code'] +'"  >Generar Nota de Crédito</a>';
                            button += '<a class="dropdown-item debitNote" href="/commercial.sale.note.debit/' + aData['id'] + '/'+ aData['type_voucher']['code'] +'"  >Generar Nota de Débito</a>';
                            button += '<div class="dropdown-divider"></div>';
                            //button += '<a class="dropdown-item" href="/reference-guide/create/'+ aData['serialnumber'] +'/'+ aData['correlative'] + '" style="color: #28a745;">Generar Guía de Remisión</a>';
                        }
                    }
                } else {
                    if ($('#type_send_boletas').val() == 1) {
                        button += '<a class="dropdown-item sendSunat" href="#"  style="color: #ea1024;">Enviar a la SUNAT</a>';
                    } else {
                        if(aData['typevoucher_id'] === 1) {
                            console.log('factura');
                            button += '<a class="dropdown-item sendSunat" href="#"  style="color: #ea1024;">Enviar a la SUNAT</a>';
                        } else if(aData['typevoucher_id'] === 2) {
                            console.log('boleta');
                            button += '<a class="dropdown-item disable-voucher" href="#" style="color: #ff0000">Anular Comprobante</a>';
                        }
                    }
                }
            }

            button += '<a class="dropdown-item consultCPE" href="http://e-consulta.sunat.gob.pe/ol-ti-itconsvalicpe/ConsValiCpe.htm?E='+ $('#rucclient').val() +'&T='+ aData['tp_description'] +'&R='+ aData['document'] +'&S='+ aData['serialnumber']  +'&N='+ aData['correlative'] +'&F='+aData['date']+'&T='+ aData['total'] +'"  style="color: #28a745;" target="_blank">Verificar CPE en la SUNAT</a>';
            button += '<a class="dropdown-item consultXML" href="http://www.sunat.gob.pe/ol-ti-itconsverixml/ConsVeriXml.htm"  style="color: #28a745;" target="_blank">Verificar XML en la SUNAT</a>';
            button += '<div class="dropdown-divider"></div>';

            button += '</div>';
            button += '</div>';
            tr.html(button);
        }

        function construct_button_for_column(tr, aData) {
            tr.find('td:eq(0)').html('');
            if(aData['low_communication_id'] !== null) {
                tr.css({
                    'text-decoration': 'line-through',
                    'text-decoration-color': 'gray',
                    'color': 'gray'
                });
            }

            if(aData['credit_note_id'] !== null) {
                if(aData['credit_note']['sunat_code']) {
                    if(aData['credit_note']['sunat_code']['code'] == 0) {
                        if (aData['credit_note'].type_credit_note_id == 1) {
                            tr.css({
                                'text-decoration': 'line-through',
                                'text-decoration-color': 'red',
                                'color': 'red'
                            });
                        }
                    }
                }
            }
        }

        var debttt;
        var coin;

        $('body').on('click', '.payment', function(e) {
            e.preventDefault();
            clearData();
            var data = tbl_data.row( $(this).parents('tr') ).data();
            if(data == undefined) {
                tbl_data = $("#tbl_data").DataTable();
                data = tbl_data.row( $(this).parents('tr') ).data();
            }

            var credit;
            var creditTotal;
            coin = data['coin']['symbol'];

            var credit = $.parseJSON(
                $.ajax({
                    url: '/finances/credits/getcredit',
                    type: 'post',
                    data: {
                        _token: '{{ csrf_token() }}',
                        sale_id: data['id']
                    },
                    dataType: 'json',
                    async: false
                }).responseText
            );

            console.log(credit.debt, 'debt');
            
            $('#montoRestante').text(coin + ' ' + credit.debt);
            $('#creditTotal').text(coin + ' ' + credit.total);
            var fecha = credit.date.split("-");
            var fe = fecha[2]+'-'+fecha[1]+'-'+fecha[0];
            $('#creditDate').text(fe);

            $('#rTableBody').html('');

            
            $("#customer_id").val(data['id']);
            $('#ci').val(credit.id);
            debttt = credit.debt;

            
            $('#creditDocument').text(data['serialnumber'] + ' - ' + data['correlative']);

            

            if (data['status_condition'] == 1) {
                $('#inputsNewForm').hide();
                $('#save').hide();
                $('#msgCancelled').show();
                $('#msgSaldoRestante').hide();
            } else {
                $('#inputsNewForm').show();
                $('#save').show();
                $('#msgCancelled').hide();
                $('#msgSaldoRestante').show();
            }

            $.post('/finances/credits/getpayments','credit_id=' + credit.id +'&_token=' + '{{ csrf_token() }}', function(response) {
                $.each(response, function(i, item) {   
                    var fecha = response[i]['date'].split("-");     
                    var newFecha = fecha[2]+'-'+fecha[1]+'-'+fecha[0];
                    var method = '-';
                    if (response[i]['payment_type'] == 'EFECTIVO') {
                        method = response[i]['cash']['name']
                    }
                    if (response[i]['payment_type'] == 'DEPOSITO EN CUENTA') {
                        method = response[i]['bank']['bank_name']
                    }
                    if (response[i]['payment_type'] == 'TARJETA DE CREDITO' || response[i]['payment_type'] == 'TARJETA DE DEBITO') {
                        method = response[i]['payment_method']['name']
                    }
                    let tr = `
                        <div class="col-12 col-md-3">
                            <div class="form-group">
                                <p>`+ response[i]['payment_type'] +`</p>
                            </div>
                        </div>
                        <div class="col-12 col-md-2">
                            <div class="form-group">
                                <p>`+ newFecha + `</p>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="form-group">
                                <p>`+ method +`</p>
                            </div>
                        </div>
                        <div class="col-12 col-md-2">
                            <div class="form-group">
                                <p>`+ response[i]['operation_bank'] +`</p>
                            </div>
                        </div>
                        <div class="col-12 col-md-2">
                            <div class="form-group">
                                <p>`+ response[i]['payment'] +`</p>
                            </div>
                        </div>
                    `;
                    $('#rTableBody').append(tr);
                });

                $('#mdlPayment').modal('show');
            }, 'json');
            $("html, body").animate({ scrollTop: 0 }, 600);
        });

        $('#payment_mont').keyup(function() {
            let newMont = $(this).val();

            if (newMont == '') {
                newMont = 0.00;
            }

            let saldoRestante = parseFloat(debttt).toFixed(2) - parseFloat(newMont).toFixed(2);
            $('#saldoRestante').text(coin + ' ' + parseFloat(saldoRestante).toFixed(2));
        });

        $('#frm_payment').validator().on('submit', function(e) {
            if(e.isDefaultPrevented()) {
                toastr.warning('Debe llenar todos los campos obligatorios');
            } else {
                e.preventDefault();
                let data = $('#frm_payment').serialize();
                
                $.confirm({
                    icon: 'fa fa-question',
                    theme: 'modern',
                    animation: 'scale',
                    type: 'green',
                    title: '¿Está seguro de realizar este abono?',
                    content: '',
                    buttons: {
                        Confirmar: {
                            text: 'Confirmar',
                            btnClass: 'btn-green',
                            action: function(){
                                $.ajax({
                                    url: '/finances/credits/payment/store',
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
                                            $('#mdlPayment').modal('hide');
                                            toastr.success('Se grabó satisfactoriamente el abono');
                                            $("#tbl_data").DataTable().ajax.reload();
                                            getTotalSales();
                                            clearData();
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
        });

        function clearData() {
            $('#payment_date').val('');
            $('#payment_type').val('');
            $('#payment_mont').val('');
            $('#ci').val('');
            $('#montoRestante').html('0.00');
            $('#actionText').val('');
            $('#bank').val('');
            $('#operation_bank').val('');
        }

        $('body').on('click', '.change_payment', function(e) {
            e.preventDefault();

            let data = tbl_data.row( $(this).parents('tr') ).data();
            $('#change_payment_document').text(`${data['type_voucher']['description']} ${data['serialnumber']}-${data['correlative']}`)
            $('#change_payment_customer').text(`${data['customer']['document']} ${data['customer']['description']}`)
            $('#changeMountPayment').val(data['condition_payment_amount'])
            $('#change_condition').val(data['condition_payment']);
            $('#change_payment_sale').val(data['id']);

            if (data['condition_payment'] == 'DEPOSITO EN CUENTA') {
                $('#changeContOptionDepositoCofre').hide();
                $('#changeContOptionDepositoPOS').hide();
                $('#changeContOptionDepositoCuenta').show();
                $('#change_bank').val(data['bank_account_id']);
            } else if (data['condition_payment'] == 'TARJETA DE CREDITO' || data['condition_payment'] == 'TARJETA DE DEBITO') {
                $('#changeContOptionDepositoCofre').hide();
                $('#changeContOptionDepositoCuenta').hide();
                $('#changeContOptionDepositoPOS').show();
                $('#change_method').val(data['payment_method_id'])
            } else {
                $('#changeContOptionDepositoCofre').show();
                $('#change_cash').val(data['cash_id']);
                $('#changeContOptionDepositoCuenta').hide();
                $('#changeContOptionDepositoPOS').hide();
            }

            $('#mdlChangePayment').modal('show')
        })

        $('#change_condition').change(function(e) {
            e.preventDefault();

            if ($(this).val() != 'EFECTIVO') {
                $('#changeContOptionDepositoCofre').hide();

                if ($(this).val() == 'DEPOSITO EN CUENTA') {
                    $('#changeContOptionDepositoPOS').hide();
                    $('#changeContOptionDepositoCuenta').show();
                } else {
                    $('#changeContOptionDepositoPOS').show();
                    $('#changeContOptionDepositoCuenta').hide();
                }
            } else {
                $('#changeContOptionDepositoCofre').show();
                $('#changeContOptionDepositoPOS').hide();
                $('#changeContOptionDepositoCuenta').hide();
            }
        })

        $('#frm_change_payment').submit(function(e) {
            e.preventDefault();

            let data = $(this).serialize();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.confirm({
                icon: 'fa fa-warning',
                theme: 'modern',
                animation: 'scale',
                type: 'green',
                draggable: false,
                title: '¿Está seguro de cambiar la forma de pago de este comprobante?',
                content: '',
                buttons: {
                    Confirmar: {
                        text: 'Confirmar',
                        btnClass: 'btn btn-green',
                        action: function() {
                            $.ajax({
                                type: 'post',
                                url: '/commercial.sales/change-payment',
                                data: data,
                                dataType: 'json',
                                success: function(response) {
                                    if(response == true) {
                                        toastr.success('Se actualizó satisfactoriamente el comprobante.');
                                    } else {
                                        toastr.error('Ocurrió un error cuando intentaba actualizar el comprobante.');
                                    }

                                    tbl_data.ajax.reload();
                                    $('#mdlChangePayment').modal('hide')
                                },
                                error: function(response) {
                                    console.log(response.responseText);
                                    toastr.error('Ocurrio un error');
                                    console.log(response.responseText)
                                }
                            });
                        }
                    },
                    Cancelar: {
                        text: 'Cancelar',
                        btnClass: 'btn btn-red'
                    }
                }
            });
        })

        $('#mdlChangePayment').on('hidden.bs.modal', function (event) {
            $('#changeMountPayment').val('')
            $('#change_condition').val('');
            $('#change_payment_sale').val('');
            $('#change_bank').val('');
            $('#change_method').val('')
            $('#change_cash').val('');
        })
    </script>
@stop
