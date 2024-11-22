@extends('layouts.azia')
@section('css')
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
    </style>
@endsection
@section('content')
    <form method="post" role="form" data-toggle="validator" id="frm_note">
        <input type="hidden" name="sale_id" value="{{$sale->id}}">
        <input type="hidden" name="type" value="{{ $type }}">
        <input type="hidden" value="{{$igv->value}}" id="igv" />
        <input type="hidden" value="{{$clientInfo->price_type}}" id="pt" />
        <input type="hidden" value="{{$clientInfo->less_employees}}" id="le" />
        <input type="hidden" value="{{$clientInfo->consumption_tax_plastic_bags}}" id="pbH" />
        <input type="hidden" value="{{$clientInfo->consumption_tax_plastic_bags_price}}" id="pbp" />
        <input type="hidden" value="{{$clientInfo->issue_with_previous_data}}" id="ipd" />
        <input type="hidden" value="{{$clientInfo->issue_with_previous_data_days}}" id="ipnd" />
        <input type="hidden" value="{{$clientInfo->exchange_rate_sale}}" id="ers" />
        <div class="container-fluid">
            <div class="col-12">
                <div class="card card-default">
                    <div class="card-header color-gray text-center">
                        <div class="row">
                            <div class="col-12">
                                <h3 class="card-title">CREAR NOTA DE DEBITO</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-7">
                                <div class="form-group">
                                    <label for="customer">Cliente</label>
                                    <div class="input-group">
                                        <select name="customer" id="customer" class="form-control" style="width: 80%;" required>
                                            <option value="{{$customer->id}}">{{$customer->description}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-1">
                                <div class="form-group">
                                    <label for="typevoucher">Serie</label>
                                    <select class="form-control" name="serialnumber" id="serialnumber">
                                        <option value="{{$correlative->serialnumber}}">{{$correlative->serialnumber}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-2">
                                <div class="form-group">
                                    <label for="typevoucher">Número (Referencial)</label>
                                    <input type="text" name="correlative" id="correlative" class="form-control" readonly value="{{ (int)$correlative->correlative + 001}}">
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
                                        <input type="text" class="form-control datepicker" name="date_issue" value="{{$currentDateLast}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-2">
                                <label for="typeoperation">Tipo de operación</label>
                                <select name="typeoperation" id="typeoperation" class="form-control" required>
                                    <option value="{{$type_operation->id}}">{{$type_operation->operation}}</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-1">
                                <label for="coin">Moneda</label>
                                <select name="coin" id="coin" class="form-control" required>
                                    <option value="{{$coin->id}}">{{$coin->description}}</option>
                                </select>
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
                                        <input type="text" class="form-control datepicker" name="due_date" value="{{$currentDateLast}}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <fieldset>
                            <div class="row">
                                <div class="col-12 table-responsive">
                                    <table class="table" id="tbl_products">
                                        <thead>
                                        <th width="400px">Producto</th>
                                        {{-- <th width="150px">Detalle</th> --}}
                                        <th width="90px">Cantidad</th>
                                        <th width="100px">Precio</th>
                                        <th width="100px">SubTotal</th>
                                        <th width="100px">Total</th>
                                        <th width="10px">*</th>
                                        </thead>
                                        <tbody class="table-sales">
                                            @foreach ($sale_detail as $sd)
                                                <tr>
                                                    <td>
                                                        <input type="text" value="{!! $sd->product->description !!}" class="form-control" readonly>
                                                        <input type="hidden" value="{{$sd->product->id}}" p-it="{{ $sd->product->priceIncludeRC }}" p-taxbase="{{ $sd->product->tax_id != null ? $sd->product->tax->base : '' }}" p-tax="{{ $sd->product->tax_id != null ? $sd->product->tax->value : '' }}" p-type_product="{{$sd->product->type_product}}" p-stock="{{$sd->product->stock}}" p-igv-type="{{ $sd->product->type_igv_id }}" p-price="{{$sd->product->price}}" p-otype='{{ $sd->product->operation_type }}' p-exonerated="{{ $sd->product->exonerated }}" class="c_product pcov" name="product[]">
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control c_quantity" name="cd_quantity[]" value="{{ $sd->quantity }}" />
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control c_price" step="0.01" name="cd_price[]" value="{{ round($sd->price, 2) }}" />
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control c_subtotal" name="cd_subtotal[]" value="{{ round($sd->subtotal, 2) }}" readonly />
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control c_total" name="cd_total[]" value="{{ round($sd->total, 2) }}" readonly />
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger-custom remove"><i class="fa fa-minus"></i></button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                {{-- <div class="col-12">
                                    <button type="button" class="btn btn-primary-custom" id="btnAddProduct">
                                        <i class="fa fa-plus-circle"></i>
                                        Agregar Producto
                                    </button>
                                </div> --}}
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
                                        </div>
                                    </div>
                                </div>
                                <fieldset>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <div id="cont-guides">
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label>DOCUMENTO A MODIFICAR</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-md-4">
                                                            <label for="modify_document">DOCUMENTO</label>
                                                            <input type="text" id="modify_document" name="modify_document" class="form-control" value="{{$sale->type_voucher->description}}" readonly>
                                                        </div>
                                                        <div class="col-12 col-md-4">
                                                            <label for="modify_serial">SERIE</label>
                                                            <input type="text" id="modify_serial" name="modify_serial" class="form-control" value="{{$sale->serialnumber}}" readonly>
                                                        </div>
                                                        <div class="col-12 col-md-4">
                                                            <label for="modify_correlative">NÚMERO</label>
                                                            <input type="text" id="modify_correlative" name="modify_correlative" class="form-control" value="{{$sale->correlative}}" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <label for="type_note">Tipo Nota de Debito</label>
                                                            <select name="type_credit_note" id="type_credit_note" class="form-control select2">
                                                                @foreach($type_debit_notes as $tcn)
                                                                    <option value="{{$tcn->id}}">{{$tcn->description}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-12 col-md-6">
                                {{-- <div class="row">
                                    <div class="col-6 text-right">
                                        <label>% Descuento Global</label>
                                    </div>
                                    <div class="col-6">
                                        <input name="c_percentage" type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6 text-right">
                                        <label>Descuento Global (-)</label>
                                    </div>
                                    <div class="col-6">
                                        <input name="c_discountGlobal" type="text" class="form-control" readonly value="0.00">
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
                                        <input name="c_exonerated" id="exonerated" type="text" class="form-control" readonly value="{{ $sale->exonerated }}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6 text-right">
                                        <label>Inafecta</label>
                                    </div>
                                    <div class="col-6">
                                        <input name="c_unaffected" id="c_unaffected" type="text" class="form-control" readonly value="{{ $sale->unaffected }}">
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
                                        <input name="c_free" id="c_free" type="text" class="form-control" readonly value="{{ $sale->free }}">
                                    </div>
                                </div>
                                <div class="row" style="display: none;">
                                    <div class="col-6 text-right">
                                        <label>Otros Cargos</label>
                                    </div>
                                    <div class="col-6 text-right">
                                        <input name="c_othercharge" type="text" class="form-control">
                                    </div>
                                </div>
                                @if ($clientInfo->consumption_tax_plastic_bags == 1)
                                    <div class="row">
                                        <div class="col-6 text-right">
                                            <label>ICBPER</label>
                                        </div>
                                        <div class="col-6 text-right">
                                            {{-- <input name="c_t" id="c_t" type="text" class="form-control" readonly value="{{ $clientInfo->consumption_tax_plastic_bags_price }}"> --}}
                                            <input name="c_t" id="c_t" value="{{ $sale->icbper }}" value="0.00" type="text" class="form-control" readonly>
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
                                        <input name="recharge" id="recharge" type="text" class="form-control" value="{{ $sale->recharge }}" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6 text-right">
                                        <label>Total</label>
                                    </div>
                                    <div class="col-6 text-right">
                                        <input name="c_total" id="c_total" type="text" class="form-control" readonly value="{{ $sale->total }}">
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
                                <div class="form-group">
                                    <button class="btn btn-primary-custom btn-block">Crear Documento</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop
@section('script_admin')
    <script>
        $('#frm_note').validator().on('submit', function(e) {
            let data = $('#frm_note').serialize();
            if (e.isDefaultPrevented()) {
                toastr.warning('Debe llenar todos los campos');
            } else {
                e.preventDefault();
                $.confirm({
                    icon: 'fa fa-question',
                    theme: 'modern',
                    animation: 'scale',
                    draggable: false,
                    title: '¿Está seguro de generar esta Nota de Debito?',
                    content: '',
                    buttons: {
                        Confirmar: {
                            text: 'Confirmar',
                            btnClass: 'btn-green',
                            action: function(){
                                $.ajax({
                                    'url': '{{route('saveNoteDebit')}}',
                                    'data': data + '&_token=' + '{{ csrf_token() }}',
                                    'type': 'post',
                                    success: function(response) {
                                        if(response === true) {
                                            toastr.success('Se generó satisfactoriamente la nota');
                                            toastr.success('El comprobante fue enviado a Sunat satisfactoriamente');
                                        } else if(response === '-1') {
                                            toastr.success('Se grabó satisfactoriamente el Comprobante');
                                            toastr.warning('Ocurrió un error con el comprobante, reviselo y vuelva a enviarlo.');toastr.warning('Debe de generar correlativos para nota de credito');
                                        } else if(response === '-2') {
                                            toastr.success('Se grabó satisfactoriamente el Comprobante');
                                            toastr.error('El comprobante fue enviado a Sunat y fue rechazado automáticamente, vuelva a enviarlo manualmente');
                                        } else if(response === '-3') {
                                            toastr.success('Se grabó satisfactoriamente el Comprobante');
                                            toastr.info('El comprobante fue enviado a Sunat y fue validado con una observación.');
                                        } else if(response['response'] == 99) {
                                            toastr.success('Se grabó satisfactoriamente el Comprobante');
                                            toastr.info('El comprobante será enviado a la Sunat en un resumen diario.');
                                        } else {
                                            toastr.success('Se grabó satisfactoriamente el Comprobante');
                                            toastr.error('Ocurrió un error desconocido, revise el comprobante.');
                                        }

                                        setTimeout(function() {
                                            window.location.href = "/commercial.sales";
                                        }, 2000)
                                    },
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

        var igv = $('#igv').val();

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

            $('#tbl_products tbody tr').each(function(index, tr) {
                tax = 0.00;
                let product = $(tr).find('.c_product');
                let price = $(tr).find('.c_price').val();
                let quantity = $(tr).find('.c_quantity').val();
                let tigv = $(product).attr('p-igv-type');
                let ot = $(product).attr('p-otype');

                if (ot == 22 && $('#pbH').val() == 1) {
                    $('#c_t').val(0.00);
                    let icbper = parseFloat($('#pbp').val()) * parseFloat(quantity);
                    let currentICBPER = parseFloat($('#c_t').val()) + parseFloat(icbper);

                    $('#c_t').val(currentICBPER.toFixed(2));
                }

                let typeRecharge = product.attr('p-taxbase');
                let pr = product.attr('p-tax');
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
                    tigv == 13 || tigv == 14 || tigv == 15 || tigv == 17) {
                    $(tr).find('.c_subtotal').val(price);
                    $(tr).find('.c_total').val(price);

                    totalR = price;

                    c_sum_free = parseFloat((c_sum_free * 1) + ($(tr).find('.c_total').val() * 1)).toFixed(2);
                } else if(tigv == 8) {
                    let subtotal = parseFloat((parseFloat(total2)) / (1 + (igv / 100) + tax)).toFixed(2);
                    if (typeRecharge == '2') {
                        recharge = (parseFloat(subtotal) * parseFloat(tax) + parseFloat(recharge)).toFixed(2);
                    }
                    $(tr).find('.c_subtotal').val(subtotal);
                    $(tr).find('.c_total').val((parseFloat(subtotal) + (parseFloat(subtotal) * (igv / 100))).toFixed(2));

                    totalR = total;

                    c_exonerated_gen = parseFloat((c_exonerated_gen * 1) + ($(tr).find('.c_total').val() * 1)).toFixed(2);
                    c_total_gen = parseFloat((c_total_gen * 1) + (parseFloat(total2))).toFixed(2);
                } else if(tigv == '9' || tigv == 16) {
                    $(tr).find('.c_subtotal').val(parseFloat(price) * parseFloat(quantity));
                    $(tr).find('.c_total').val(parseFloat(price) * parseFloat(quantity));

                    totalR = price;

                    c_sum_unaffected = parseFloat((c_sum_unaffected * 1) + ($(tr).find('.c_total').val() * 1)).toFixed(2);
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

                    if(product.attr('p-exonerated') === '0') {
                        c_gravada_gen = parseFloat((c_gravada_gen * 1) + ($(tr).find('.c_subtotal').val() * 1)).toFixed(2);
                        c_subtotal_gen = parseFloat(parseFloat(igvLinea) + parseFloat(c_subtotal_gen)).toFixed(2);
                        c_total_gen = parseFloat((c_total_gen * 1) + (parseFloat(total2))).toFixed(2);
                    } else {
                        c_total_gen = parseFloat((c_total_gen * 1) + (parseFloat(total2))).toFixed(2);
                        c_total_gen = parseFloat((c_total_gen * 1) + (parseFloat(total2))).toFixed(2);
                    }
                }

                totalFinal = (parseFloat(c_total_gen)).toFixed(2);

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

            t = $('#c_t').val();

            totalFinal = parseFloat(parseFloat(totalFinal) + parseFloat(t)).toFixed(2);

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

        function calc(num) {
            var with2Decimals = num.toString().match(/^-?\d+(?:\.\d{0,2})?/)
            return with2Decimals
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
            data += '<td><input type="number" class="form-control c_price" step="0.01" name="cd_price[]" value="0"/></td>';
            data += '<td><input type="text" class="form-control c_subtotal" name="cd_subtotal[]" value="0" readonly /></td>';
            data += '<td><input type="text" class="form-control c_total" name="cd_total[]" value="0" readonly /></td>';
            data += '<td>';
            data += '<button type="button" class="btn btn-danger-custom remove"><i class="fa fa-minus"></i></button>';
            data += '</td>';

            data += '</tr>';
            $('#tbl_products tbody').append(data);

            $('.select_2').select2();
        });

        $('.select_2').select2();

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
    </script>
@stop