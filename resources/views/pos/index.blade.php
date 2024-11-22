@extends('layouts.azia')
@section('css')
    <style>
        .az-content-header{display: none !important;}
    </style>
@endsection
@section('content')
<input type="hidden" value="{{$igv->value}}" id="igv" />
<input type="hidden" value="{{$clientInfo->price_type}}" id="pt" />
<input type="hidden" value="{{$clientInfo->less_employees}}" id="le" />
<input type="hidden" value="{{$clientInfo->consumption_tax_plastic_bags}}" id="pbH" />
<input type="hidden" value="{{$clientInfo->consumption_tax_plastic_bags_price}}" id="d" />
<input type="hidden" value="{{$clientInfo->issue_with_previous_data}}" id="ipd" />
<input type="hidden" value="{{$clientInfo->issue_with_previous_data_days}}" id="ipnd" />
<input type="hidden" value="{{$clientInfo->exchange_rate_sale}}" id="ers" />
<input type="hidden" value="{{env('AWS_URL')}}" id="aws_url" />
<div class="row mh-100 h-100">
    <div class="col-12 col-md-7 col-xl-7 pd-t-10">
        <form id="frm_pos">
            <div class="row">
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label>Tipo de Documento</label>
                        <select name="typevoucher_id" id="typeVoucher" class="form-control">
                            <option value="1">Factura</option>
                            <option value="2">Boleta</option>
                        </select>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label>Moneda</label>
                        <select name="coin" id="coin" class="form-control">
                            @foreach ($coins as $coin)
                                <option value="{{ $coin->id }}">{{ $coin->symbol }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="form-group">
                        <label>Fecha</label>
                        <input type="text" class="form-control" name="date" id="date" value="{{ $currentDate }}" autocomplete="off">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="row">
                        <div class="col-11 pd-r-1">
                            <div class="form-group">
                                <label>Cliente:</label>
                                <select name="customer" id="customer" class="form-control select2" style="width: 100%;"></select>
                            </div>
                        </div>
                        <div class="col-1 pd-1">
                            <div class="form-group mg-t-25">
                                <label for=""></label>
                                <button type="button" id="openCustomer" class="btn btn-gray-custom"><i class="fa fa-plus"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row pcont">
                <div class="col-12 h-100">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="tbl_products">
                            <thead>
                                <tr>
                                    <th width="300px">Producto</th>
                                    <th width="230px">Precio</th>
                                    <th width="100px">Cantidad</th>
                                    <th width="100px">Total</th>
                                    <th width="50px">*</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row d-flex align-items-end">
                <div class="col-12">
                    <table class="table">
                        <tr>
                            <td><strong>Exonerada</strong> <span class="mg-b-0" id="p_exonerated">0.00</span> <input name="exonerated" id="exonerated" type="hidden"></td>
                            <td><strong>Inafecta</strong> <span class="mg-b-0" id="p_unaffected">0.00</span><input name="c_unaffected" id="c_unaffected" type="hidden"></td>
                        </tr>
                        <tr>
                            <td><strong>Gravada</strong> <span class="mg-b-0" id="p_taxed">0.00</span> <input name="c_taxed" id="c_taxed" type="hidden"></td>
                            <td><strong>IGV</strong> <span class="mg-b-0" id="p_igv">0.00</span><input name="c_igv" id="c_igv" type="hidden"></td>
                        </tr>
                        <tr>
                            <td><strong>R.C</strong> <span class="mg-b-0" id="p_rc">0.00</span><input name="recharge" id="recharge" type="hidden" value="0.00"></td>
                            @if ($clientInfo->consumption_tax_plastic_bags == 1)
                                <td><strong>ICBPER</strong> <span class="mg-b-0" id="c_te">0.00</span><input name="c_t" id="c_t" type="hidden" value="0.00"></td>
                            @endif
                        </tr>
                    </table>
                </div>
                <div class="col-12 col-md-6 text-right pd-t-5 pd-b-5 bg-gray-900 tx-white">
                    <strong>Total a Pagar</strong>
                </div>
                <div class="col-12 col-md-6 pd-t-5 pd-b-5 bg-gray-900 tx-white">
                    <span class="mg-b-0" id="p_totalg">0.00</span>
                    <input name="c_total" id="c_total" type="hidden">
                </div>
                <div class="col-12 pd-l-0 pd-r-0">
                    <button class="btn btn-block btn-secondary-custom" id="sendSale" type="button"><i class="fa fa-money"></i> Pagar</button>
                </div>
            </div>
        </form>
    </div>
    <div class="col-12 col-md-5 col-xl-5 bg-gray-100 pd-t-10 pd-b-10">
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text searchButton"><i class="fa fa-search"></i></div>
                            <div class="input-group-text barcodeButton"><i class="fa fa-barcode"></i></div>
                        </div>
                        <input type="text" id="searchProduct" class="form-control" placeholder="Buscar productos...">
                    </div>
                </div>
            </div>
            <div class="col-12"><br></div>
        </div>
        <div class="row row-products">
            @foreach ($products->take(20) as $product)
                <div class="pos-product">
                    <button type="button" data-toggle="tooltip" data-placement="top" title data-original-title="{{ $product->description }}" class="pos-product-btn product" p-igv-type="{{ $product->type_igv_id }}" p-taxbase="{{ $product->taxbase }}" p-tax="{{ $product->tax }}" p-stock="{{ $product->stock }}" p-price="{{ $product->price }}" p-ot="{{ $product->operation_type }}" p-p="{{ $product->id }}" p-category="{{ $product->category }}" p-description="{{ $product->description }}" p-exonerated="{{ $product->exonerated }}" p-brand="{{ $product->brand }}">
                        <img src="storage/{{$product->image}}" alt="" style="width: 100%;">
                        {{ $product->description }}
                    </button>
                </div>
            @endforeach
        </div>
    </div>
</div>
<div class="modal fade in" id="paymentModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="payModalLabel" aria-hidden="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Finalizar Compra</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            </div>
            <div class="modal-body" id="payment_content">
                <form id="form_payment">
                    <div class="row">
                        <div class="col-md-10 col-sm-9">
                            <div class="form-group">
                                <label for="biller">Vendedor</label>
                                <input type="text" class="form-control" value="{{ auth()->user()->name }}" readonly>
                            </div>
                            <div class="clearfir"></div>
                            <div id="payments">
                                <div class="well well-sm well_1">
                                    <div class="payment">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label for="amount_1">Monto</label>
                                                    <input name="mountPayment" type="number" id="mountPayment" step="0.01" class="form-control amount" aria-haspopup="true" role="textbox">
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label>Forma de pago</label>
                                                    <select name="condition" id="condition" class="form-control">
                                                        <option value="EFECTIVO" data-days="0">EFECTIVO</option>
                                                        <option value="DEPOSITO EN CUENTA" data-days="0">DEPOSITO EN CUENTA</option>
                                                        {{-- <option value="CREDITO 7 DIAS" data-days="7">CREDITO 7 DIAS</option>
                                                        <option value="CREDITO 15 DIAS" data-days="15">CREDITO 15 DIAS</option>
                                                        <option value="CREDITO 30 DIAS" data-days="30">CREDITO 30 DIAS</option> --}}
                                                        <option value="TARJETA DE CREDITO" data-days="0">TARJETA DE CREDITO</option>
                                                        <option value="TARJETA DE DEBITO" data-days="0">TARJETA DE DEBITO</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-4" id="contOptionDepositoCofre">
                                                <div class="form-group">
                                                    <label>Cofre</label>
                                                    <select name="cash" id="cash" class="form-control">
                                                        @foreach ($cashes as $cash)
                                                            <option value="{{ $cash->id }}">{{ $cash->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-4" id="contOptionDepositoCuenta">
                                                <div class="form-group">
                                                    <label>Cuenta</label>
                                                    <select name="bank" id="" class="form-control">
                                                        @foreach ($bankAccounts as $account)
                                                            <option value="{{ $account->id }}">{{ $account->bank_name }} - {{ $account->number }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-4" id="contOptionDepositoPOS">
                                                <div class="form-group">
                                                    <label>Medio de Pago</label>
                                                    <select name="mp" id="" class="form-control">
                                                        @foreach ($paymentMethods as $paymentMethod)
                                                            <option value="{{ $paymentMethod->id }}">{{ $paymentMethod->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label for="payment_note">Nota de Pago</label>
                                                    <textarea name="payment_note[]" id="payment_note_1" class="form-control" aria-haspopup="true" role="textbox"></textarea>
                                                </div>
                                            </div>
                                        </div> --}}
                                    </div>
                                    <hr>
                                </div>
                            </div>
                            <div id="multi-payment"></div>
                            <button type="button" class="btn btn-primary-custom col-md-12 addButton" id="addPaymentForm" tabindex="-1"><i class="fa fa-plus"></i> Agregar Forma de Pago</button>
                            <div style="clear:both; height:15px;"></div>
                            <div class="font16">
                                <table class="table table-bordered table-condensed table-striped" style="margin-bottom: 0;font-size: 30px;">
                                    <tbody>
                                    <tr>
                                        <td width="25%">Total Items</td>
                                        <td width="25%" class="text-right"><span id="item_count"></span></td>
                                        <td width="25%">Total a Pagar</td>
                                        <td width="25%" class="text-right"><span id="twt"></span></td>
                                    </tr>
                                    <tr>
                                        <td>Pago Efectivo</td>
                                        <td class="text-right"><span id="total_paying"></span><input type="hidden" name="total_paying" id="tp"></td>
                                        <td>Cambio</td>
                                        <td class="text-right"><span id="balance"></span><input type="hidden" name="balance" id="bc"></td>
                                    </tr>
                                    </tbody>
                                </table>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-3 text-center">
                            <span style="font-size: 1.2em; font-weight: bold;">Dinero Rapido</span>

                            <div class="btn-group btn-group-vertical">
                                <button type="button" class="btn btn-lg btn-secondary-custom-custom quick-cash" id="quick-payable" data-mount="" tabindex="-1">2250</button>
                                <button type="button" class="btn btn-lg btn-secondary-custom quick-cash" tabindex="-1" data-mount="10">10</button>
                                <button type="button" class="btn btn-lg btn-secondary-custom quick-cash" tabindex="-1" data-mount="20">20</button>
                                <button type="button" class="btn btn-lg btn-secondary-custom quick-cash" tabindex="-1" data-mount="50">50</button>
                                <button type="button" class="btn btn-lg btn-secondary-custom quick-cash" tabindex="-1" data-mount="100">100</button>
                                <button type="button" class="btn btn-lg btn-secondary-custom quick-cash" tabindex="-1" data-mount="200">200</button>
                                <button type="button" class="btn btn-lg btn-gray-custom" id="clear-cash-notes" tabindex="-1">Limpiar</button>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <button class="btn btn-block btn-lg btn-primary-custom" type="sumit" id="submit-sale" tabindex="-1">Enviar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div id="mdl_preview" class="modal fade bd-example-modal-lg" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <button class="btn btn-primary-custom btnPrint" id="">IMPRIMIR</button>
                        <!--<button class="btn btn-primary-custom btnOpen" id="0">Abrir en navegador</button>-->
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
{{-- INCLUDES --}}
@include('includes.customer')
@include('includes.keyboard')
@endsection
@section('script_admin')
    <script src="{{ asset('js/keyboard.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#contOptionDepositoCuenta').hide();
            $('#contOptionDepositoPOS').hide();
        });
        let type = 1;

        function addColumnProductFromDatabase(product_database) {
            let pid = product_database['id'];
            let product;
            $('#tbl_products tbody tr').each(function(index, tr) {
                if($(tr).find('.c_product').val() == pid) {
                    product = $(tr);
                }
            });

            if (product === undefined) {
                product = product_database['description'];

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
                        myPrices += '<select name="cd_price[]" class="form-control c_price">';
                        for(let x = 0; x < response.length; x++) {
                            myPrices += '<option value="' + response[x].price + '">' + response[x].price + ' - ' + response[x]['price_list'].description +'</option>';
                        }
                        myPrices += '</select>';

                        let price = response[0].price;

                        let exonerated = product_database['exonerated'];

                        let tr = '';

                        if(product_database['operation_type']['id'] == 1 || product_database['operation_type']['id'] == 2) {
                            tr = `
                                    <tr class="product-row">
                                        <td style="width: 100px;">`+ product +`<input type="hidden" class="c_product" p-exonerated="`+ exonerated +`" name="cd_product[]" value="`+ pid +`"</td>
                                        <td>`+ myPrices +`</td>
                                        <td><input min="1" type="number" name="cd_quantity[]" class="form-control-custom c_quantity" value="1"> ` + product_database['operation_type']['description'] +`</td>
                                        <td style="display: none;"><input type="text" class="form-control-custom c_subtotal" name="cd_subtotal[]" value="0" readonly /></td>
                                        <td><input type="text" name="cd_total[]" class="form-control-custom c_total" readonly value="`+ price +`"></td>
                                        <td><button type="button" class="btn btn-custom deleteProduct btn-sm"><i class="fa fa-minus"></i></button></td>
                                    </tr>
                        `;
                        } else {
                            tr = `
                                    <tr class="product-row">
                                        <td>`+ product +`<input type="hidden" class="c_product" p-exonerated="`+ exonerated +`" name="cd_product[]" value="`+ pid +`"</td>
                                        <td>`+ myPrices +`</td>
                                        <td><input type="text" name="cd_quantity[]" class="form-control-custom c_quantity type_number" value="">` + product_database['operation_type']['description'] + `</td>
                                        <td style="display: none;"><input type="text" class="form-control-custom c_subtotal" name="cd_subtotal[]" value="0" readonly /></td>
                                        <td><input type="text" name="cd_total[]" class="form-control-custom c_total" readonly value="`+ price +`"></td>
                                        <td><button type="button" class="btn btn-custom deleteProduct btn-sm"><i class="fa fa-minus"></i></button></td>
                                    </tr>
                        `;
                        }

                        $('#tbl_products tbody').append(tr);

                        $('.type_number').inputmask('##.###', {'placeholder': '00.000'});

                        recalculate();
                    }
                });
            } else {
                let currentQuantity = 0;
                let quantity = $(product).find('.c_quantity').val();
                if(quantity == '') {
                    currentQuantity = 1;
                } else {
                    currentQuantity = parseFloat(quantity) + 1;
                }

                $(product).find('.c_quantity').val(currentQuantity);

                recalculate();
            }

            // recalculate();
        }

        function addColumnProduct(that) {
            let pid = that.attr('p-p');
            let product;
            $('#tbl_products tbody tr').each(function(index, tr) {
                if($(tr).find('.c_product').val() == pid) {
                    product = $(tr);
                }
            });

            if (product === undefined) {
                product = that.attr('p-description');

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
                        myPrices += '<select name="cd_price[]" class="form-control c_price">';
                        for(let x = 0; x < response.length; x++) {
                            if(x == 1) {
                                myPrices += '<option value="' + response[x + 1].price + '">' + response[x + 1].price + ' - ' + response[x + 1].description +'</option>';
                            } else if(x == 2) {
                                myPrices += '<option value="' + response[x - 1].price + '">' + response[x - 1].price + ' - ' + response[x - 1].description +'</option>';
                            } else {
                                myPrices += '<option value="' + response[x].price + '">' + response[x].price + ' - ' + response[x].description +'</option>';
                            }
                        }

                        myPrices += '</select>';

                        let price = response[0].price;

                        let exonerated = that.attr('p-exonerated');

                        let tr = '';

                        if(that.attr('p-measurement') == 1 || that.attr('p-measurement') == 2) {
                            tr = `
                                    <tr class="product-row">
                                        <td style="width: 100px;">`+ product +`<input type="hidden" class="c_product" p-exonerated="`+ exonerated +`" name="cd_product[]" value="`+ pid +`"</td>
                                        <td>`+ myPrices +`</td>
                                        <td><input min="1" type="number" name="cd_quantity[]" class="form-control-custom c_quantity" value="1"> ` + that.attr('p-measurement-description') +`</td>
                                        <td style="display: none;"><input type="text" class="form-control-custom c_subtotal" name="cd_subtotal[]" value="0" readonly /></td>
                                        <td><input type="text" name="cd_total[]" class="form-control-custom c_total" readonly value="`+ price +`"></td>
                                        <td><button type="button" class="btn btn-custom deleteProduct btn-sm"><i class="fa fa-minus"></i></button></td>
                                    </tr>
                        `;
                        } else {
                            tr = `
                                    <tr class="product-row">
                                        <td>`+ product +`<input type="hidden" class="c_product" p-exonerated="`+ exonerated +`" name="cd_product[]" value="`+ pid +`"</td>
                                        <td>`+ myPrices +`</td>
                                        <td><input type="text" name="cd_quantity[]" class="form-control-custom c_quantity type_number" value="">` + that.attr('p-measurement-description') + `</td>
                                        <td style="display: none;"><input type="text" class="form-control-custom c_subtotal" name="cd_subtotal[]" value="0" readonly /></td>
                                        <td><input type="text" name="cd_total[]" class="form-control-custom c_total" readonly value="`+ price +`"></td>
                                        <td><button type="button" class="btn btn-custom deleteProduct btn-sm"><i class="fa fa-minus"></i></button></td>
                                    </tr>
                        `;
                        }

                        $('#tbl_products tbody').append(tr);

                        $('.type_number').inputmask('##.###', {'placeholder': '00.000'});

                        recalculate();
                    }
                });
            } else {
                let currentQuantity = 0;
                quantity = $(product).find('.c_quantity').val();
                if(quantity == '') {
                    currentQuantity = 1;
                } else {
                    currentQuantity = parseFloat(quantity) + 1;
                }

                $(product).find('.c_quantity').val(currentQuantity);

                recalculate();
            }

            // recalculate();
        }


        $(function(){
            'use strict'
            $('[data-toggle="tooltip"]').tooltip();
            // colored tooltip
            $('[data-toggle="tooltip-primary"]').tooltip({ 
                template: '<div class="tooltip tooltip-primary" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
            });

            $('[data-toggle="tooltip-secondary"]').tooltip({
                template: '<div class="tooltip tooltip-secondary" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
            });

            $('.btn').removeClass('btn-rounded');
            $('#azSidebarToggle').click();
            
            $.get('/commercial.customer.all/' + type, function(response) {
                $('#customer').html('');
                let option = '';
                for (let i = 0;i < response.length; i++) {
                    option += `<option value="${response[i].id}">${response[i].document} - ${response[i].description}</option>`;
                }

                $('#customer').append(option);
            }, 'json');
        });

        $('#typeVoucher').change(function() {
            type = $(this).val();

            $.get('/commercial.customer.all/' + type, function(response) {
                $('#customer').html('');
                let option = '';
                for (let i = 0;i < response.length; i++) {
                    option += `<option value="${response[i].id}">${response[i].document} - ${response[i].description}</option>`;
                }

                $('#customer').append(option);
            }, 'json'); 
        });

        $('body').on('click', '.product', function() {
            let that = $(this);
            let pid = that.attr('p-p');
            let ot = that.attr('p-ot');
            let product;
            let typeRecharge =that.attr('p-taxbase');
            let pr =that.attr('p-tax');
            let typeIgv =that.attr('p-igv-type');
            let quantity = 0;

            $('#tbl_products tbody tr').each(function(index, tr) {
                if($(tr).find('.c_product').val() == pid) {
                    product = $(tr);
                }
            });

            if (product === undefined) {
                product = that.attr('p-description');

                if (ot == 23) {
                    let tr = `
                            <tr class="product-row">
                                <td>`+ product +`<input type="hidden" class="c_product" p-ot=`+ot+` p-igv-type="`+typeIgv+`" p-taxbase="`+ typeRecharge +`" p-tax="` + pr + `" p-exonerated="0" name="cd_product[]" value="`+ pid +`"</td>
                                <td><input type="number" step="0.01" class="form-control-custom c_price" name="cd_price[]"></td>
                                <td><input type="number" name="cd_quantity[]" class="form-control-custom c_quantity" value="1"></td>
                                <td style="display: none;"><input type="text" class="form-control-custom c_subtotal" name="cd_subtotal[]" value="0" readonly /></td>
                                <td><input type="text" name="cd_total[]" class="form-control-custom c_total" readonly value="0"></td>
                                <td><button type="button" class="btn btn-custom deleteProduct"><i class="fa fa-minus"></i></button></td>
                            </tr>
                    `;

                    $('#tbl_products tbody').append(tr);
                    recalculate();
                } else {
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
                            myPrices += '<select name="cd_price[]" class="form-control c_price">';
                            for(let x = 0; x < response.length; x++) {
                                myPrices += '<option value="' + response[x].price + '">' + response[x]['price_list'].description + ' - ' + response[x].price +'</option>';
                            }
                            myPrices += '</select>';

                            let price = response[0].price;

                            let exonerated = that.attr('p-exonerated');

                            let tr = `
                                        <tr class="product-row">
                                            <td>`+ product +`<input type="hidden" class="c_product" p-ot=`+ot+` p-igv-type="`+typeIgv+`" p-taxbase="`+ typeRecharge +`" p-tax="` + pr + `" p-exonerated="`+ exonerated +`" name="cd_product[]" value="`+ pid +`"</td>
                                            <td>`+ myPrices +`</td>
                                            <td><input type="number" name="cd_quantity[]" class="form-control-custom c_quantity" value="1"></td>
                                            <td style="display: none;"><input type="text" class="form-control-custom c_subtotal" name="cd_subtotal[]" value="0" readonly /></td>
                                            <td><input type="text" name="cd_total[]" class="form-control-custom c_total" readonly value="`+ price +`"></td>
                                            <td><button type="button" class="btn btn-custom deleteProduct"><i class="fa fa-minus"></i></button></td>
                                        </tr>
                            `;

                            $('#tbl_products tbody').append(tr);

                            if (ot == 22 && $('#pbH').val() == 1) {
                                $('#c_t').val(0.00);
                                let icbper = parseFloat($('#d').val()) * parseFloat(1);
                                let currentICBPER = parseFloat($('#c_t').val()) + parseFloat(icbper);

                                $('#c_t').val(currentICBPER.toFixed(2));
                                $('#c_te').text(currentICBPER.toFixed(2));
                            }

                            recalculate();
                        }
                    });
                }
            } else {
                quantity = $(product).find('.c_quantity').val();
                let currentQuantity = parseInt(quantity) + 1;

                if (ot == 22 && $('#pbH').val() == 1) {
                    $('#c_t').val(0.00);
                    let icbper = parseFloat($('#d').val()) * parseFloat(currentQuantity);
                    let currentICBPER = parseFloat($('#c_t').val()) + parseFloat(icbper);

                    $('#c_t').val(currentICBPER.toFixed(2));
                    $('#c_te').text(currentICBPER.toFixed(2));
                }

                $(product).find('.c_quantity').val(currentQuantity)
            }

            recalculate();
        });
        let cont = 1;
        $('body').on('click', '.close-payment', function() {
            $(this).parent().parent().remove();
            cont--;
            $('#addPaymentForm').show();
        });
        $('#condition').change(function () {
            let days = $('#condition option:selected').data('days');

            let currentDate = $('#expiration').val();

            let date = moment().add(days, 'days').format('DD-MM-YYYY');

            $('#expiration').val(date);

            $('#expiration').trigger('changeDate');

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

        $('#addPaymentForm').click(function() {

            let tr="";

            let total = parseFloat($('#c_total').val());
            let amount = parseFloat ($('#mountPayment').val());
            let mountpayment = total -amount;


            if ($('#condition').val() == "EFECTIVO"){
                //alert('Hola EFECTIVO');
                tr = `
                        <div class="payment bg-gray-200 pd-t-40 pd-l-10 pd-r-10 pd-b-10">
                            <div class="remove-payment">
                                <button type="button" class="close-payment"><i class="fa fa-close"></i></button>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="amount_1">Monto</label>
                                        <input name="mountOtherPayment" type="number" step=0.01 id="mountOtherPayment" class="form-control amount" aria-haspopup="true" role="textbox">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Forma de pago</label>
                                        <select name="otherCondition" id="otherCondition" class="form-control">
                                            <option value="DEPOSITO EN CUENTA" data-days="0">DEPOSITO EN CUENTA</option>
                                            <option value="TARJETA DE CREDITO">TARJETA DE CREDITO</option>
                                            <option value="TARJETA DE DEBITO">TARJETA DE DEBITO</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-4" id="contOptionDepositoCuentaOther">
                                <div class="form-group">
                                    <label>Cuenta</label>
                                    <select name="obank" id="" class="form-control">
                                        @foreach ($bankAccounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->bank_name }} - {{ $account->number }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-4" id="contOptionDepositoPOSOther">
                                <div class="form-group">
                                    <label>Medio de Pago</label>
                                    <select name="omp" id="" class="form-control">
                                        @foreach ($paymentMethods as $paymentMethod)
                                            <option value="{{ $paymentMethod->id }}">{{ $paymentMethod->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            </div>
                            <hr>
                        </div>
            `;
            }else{
                $('#total_paying').html(mountpayment.toFixed(2));
                tr = `
                        <div class="payment bg-gray-200 pd-t-40 pd-l-10 pd-r-10 pd-b-10">
                            <div class="remove-payment">
                                <button type="button" class="close-payment"><i class="fa fa-close"></i></button>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="amount_1">Monto</label>
                                        <input name="mountOtherPayment" type="number" step=0.01 id="mountOtherPayment" class="form-control amount" aria-haspopup="true" role="textbox">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Forma de pago</label>
                                        <select name="otherCondition" id="otherCondition" class="form-control">
                                            <option value="EFECTIVO">EFECTIVO</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-4" id="contOptionDepositoCuentaOther">
                                <div class="form-group">
                                    <label>Cuenta</label>
                                    <select name="obank" id="" class="form-control">
                                        @foreach ($bankAccounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->bank_name }} - {{ $account->number }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-4" id="contOptionDepositoPOSOther">
                                <div class="form-group">
                                    <label>Medio de Pago</label>
                                    <select name="omp" id="" class="form-control">
                                        @foreach ($paymentMethods as $paymentMethod)
                                            <option value="{{ $paymentMethod->id }}">{{ $paymentMethod->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            </div>
                            <hr>
                        </div>
            `;
            }

            if(cont <= 2) {
                $('#multi-payment').append(tr);
                cont++;
            }

            $('#mountOtherPayment').val(mountpayment.toFixed(2));
            //$('#mountOtherPayment').prop('readonly', true);
            $('#contOptionDepositoCuentaOther').show();
            $('#contOptionDepositoPOSOther').hide();
            $('#contOptionDepositoCuentaOther div select').attr('disabled', false);
            $('#addPaymentForm').hide();
        });

        $('body').on('keyup', '.c_quantity', function() {
            let product = $(this).parent().parent().find('.c_product');
            let ot = product.attr('p-ot');
            let quantity = $(this).val();

            if (ot == 22 && $('#pbH').val() == 1) {
                $('#c_t').val(0.00);
                let icbper = parseFloat($('#d').val()) * parseFloat(quantity);
                let currentICBPER = parseFloat($('#c_t').val()) + parseFloat(icbper);

                $('#c_t').val(currentICBPER.toFixed(2));
                $('#c_te').text(currentICBPER.toFixed(2));
            }

            recalculate();
        });
        $('body').on('change', '.c_price', function() {
            let product = $(this).parent().parent().find('.c_product');
            let ot = product.attr('p-ot');
            let quantity = $(this).val();

            if (ot == 22 && $('#pbH').val() == 1) {
                $('#c_t').val(0.00);
                let icbper = parseFloat($('#d').val()) * parseFloat(quantity);
                let currentICBPER = parseFloat($('#c_t').val()) + parseFloat(icbper);

                $('#c_t').val(currentICBPER.toFixed(2));
                $('#c_te').text(currentICBPER.toFixed(2));
            }

            recalculate();
        });

        $('body').on('keyup', '.c_price', function() {
            $(this).change();
            recalculate();
        });

        $('body').on('click', '.deleteProduct', function() {
            $(this).parent().parent().remove();
            recalculate();
        });

        $('body').on('keyup', '#mountPayment', function() {
            calctotal();
        });

        function calctotal (){
            let total = $('#c_total').val();
            let mount =0.00;
            let balance=0.00;

            if ($('#condition').val() == 'EFECTIVO') {
                if($('#mountPayment').val()=="")
                    mount=0.00;
                else
                    mount = parseFloat($('#mountPayment').val());

                balance =mount - total;

                if(balance <0 || isNaN(balance))
                    balance=0;

                $('#balance').text(balance.toFixed(2))
                $('#bc').val(balance.toFixed(2))
                $('#total_paying').html(mount);
                $('#tp').val(mount);
            }
        }
        $('#condition').change(function () {
            if($(this).val()!="EFECTIVO"){
                /*let total_paying = parseFloat($('#total_paying').val());
                let mount = parseFloat($('#mountPayment').val());
                let balance = 0.00*/
                if( typeof $('#otherCondition').val()==="undefined"){
                    $('#balance').text(0.00)
                    $('#bc').val(0.00)
                    $('#total_paying').html(0.00);
                    $('#tp').val(0.00);
                }
            }else{
                calctotal();
            }
        });

        $('body').on('change', '#mountPayment', function() {
            calctotal();
        });

        $('#btnClose').click(function() {
            window.location.href = '/pos';
        });

        $('body').on('click', '.btnPrint', function(){
            $("#frame_pdf").get(0).contentWindow.print();
        });

        $('body').on('click', '.btnOpen', function(){
            let id = $(this).attr('id');
            window.open('/pos/pdf/' + id, '_blank');

        })

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

        var igv = $('#igv').val();

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

            if ($('#pbH').val() == 0) {
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
                let tigv = product.attr('p-igv-type');
                

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
                    $(tr).find('.c_subtotal').val(subtotal);
                    $(tr).find('.c_total').val((parseFloat(subtotal) + (parseFloat(subtotal) * (igv / 100))).toFixed(2));

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
            });

            c_gravada_gen = (parseFloat(c_gravada_gen)).toFixed(2)

            $('#c_igv').val(c_subtotal_gen);
            $('#c_total').val(totalFinal);
            $('#c_taxed').val(c_gravada_gen);
            $('#exonerated').val(c_exonerated_gen);
            $('#c_free').val(c_sum_free);
            $('#c_unaffected').val(c_sum_unaffected);
            $('#c_discount').val(discount);
            $('#recharge').val(parseFloat(recharge).toFixed(2));
            $('#p_igv').text(c_subtotal_gen);
            $('#p_totalg').text(totalFinal);
            $('#p_total').text(totalFinal);
            $('#p_taxed').text(c_gravada_gen);
            $('#p_exonerated').text(c_exonerated_gen);
            $('#p_unaffected').text(c_sum_unaffected);
            $('#p_rc').text(parseFloat(recharge).toFixed(2));

            $('#mountPayment').val(totalFinal);
        }

        function calc(num) {
            var with2Decimals = num.toString().match(/^-?\d+(?:\.\d{0,2})?/)
            return with2Decimals
        }


        $('#sendSale').on('click', function(e) {
            let products = document.getElementsByClassName("product-row").length;

            if (products <= 0) {
                toastr.warning('Debe de agregar al menos un producto para relizar la venta.')
            } else {
                let total = $('#c_total').val();
                                
                $('#mountPayment').val(total);
                $('#item_count').html(products);
                $('#quick-payable').html(total);
                $('#quick-payable').attr('data-mount',total);
                $('#total_paying').html(total);
                $('#tp').val(total);
                $('#twt').text(total);
                $('#paymentModal').modal('show');
            }
        });

        let currentCash = 0;
        let total = 0;
        let badgeCont;
        $('.quick-cash').click(function() {
            total = $('#c_total').val();
            currentCash = parseFloat(currentCash) + parseFloat($(this).attr('data-mount'));

            $('#mountPayment').focus().val(currentCash);

            let balance = currentCash - parseFloat(total);
            let currentBadge = $(this).find('.bage').html();
            
            if (currentBadge === undefined) {
                currentBadge = 1;
            } else {
                currentBadge = parseInt(currentBadge) + parseInt(1);
            }

            let badge = '<span class="bage">'+ currentBadge +'</span>';
            
            $(this).find('.bage').remove();
            $(this).append(badge);
            $('#total_paying').html(currentCash.toFixed(2));
            $('#tp').val(currentCash.toFixed(2));
            $('#balance').html(balance.toFixed(2));
            $('#bc').val(balance.toFixed(2));
        });

        $('#clear-cash-notes').click(function() {
            currentCash = 0;

            $('.quick-cash').each(function(index, tr) {
                $(tr).find('.bage').remove();
            });

            $('#total_paying').html(total);
            $('#tp').val(total);
            $('#balance').html('');
            $('#bc').val(0.00);
            $('#mountPayment').val(total);
            $('#multi-payment .payment').remove();
            cont = 1;
        });

        $('#form_payment').on('submit', function(e) {
            let data1 = $('#frm_pos').serialize();
            let data2 = $('#form_payment').serialize();
            e.preventDefault();

            if($('#customer').val() == '' || $('#customer').val() == null) {
                toastr.error('Debe seleccionar al menos un cliente');
                return;
            }

            $.ajax({
                    url: '/pos/store',
                    type: 'post',
                    data: data1 + '&' + data2 + '&_token=' + '{{ csrf_token() }}',
                    dataType: 'json',
                    beforeSend: function() {
                        $('#submit-sale').attr('disabled', true);
                    },
                    complete: function() {

                    },
                    success: function(response) {
                        if(response['response'] === true) {
                            toastr.success('Se grabó satisfactoriamente el Comprobante');
                            toastr.info('Para comprobar el estado en la sunat del comprobante, verifique la lista de ventas');

                            $('#frame_pdf').attr('src', response['pdf']);
                            $('.btnOpen').attr('id', response['sale_id']);
                            $('#mdl_preview').modal('show');
                            
                        } else if(response == -9) {
                            toastr.warning('El Producto no cuenta con stock');
                            $('#paymentModal').modal('hide');
                        }

                        
                    },
                    error: function(response) {
                        console.log(response.responseText);
                        toastr.error('Ocurrio un error');
                        $('#submit-sale').removeAttr('disabled');
                    }
                });
        });

        $('body').on('keyup', '#mountOtherPayment', function() {


           /* let total = $('#c_total').val();
            let newAmoutn = $(this).val();

            if (newAmoutn == '' || newAmoutn == ' ') {
                newAmoutn = 0.00;
            }

            let newTotal = parseFloat(total) - parseFloat(newAmoutn);

            $('#mountPayment').val(newTotal.toFixed(2));*/
            let total = parseFloat($('#c_total').val());
            let mountother = parseFloat($(this).val());
            let mount = parseFloat($('#mountPayment').val());
            let payment = mount + mountother;
            let balance = payment - total;

            if(balance <0 || isNaN(balance))
                balance=0;

            if( $('#otherCondition').val()=="EFECTIVO"){
                $('#total_paying').html(mountother);
                $('#tp').val(mountother);
            }

            $('#balance').html(balance.toFixed(2));
            $('#bc').val(balance.toFixed(2));
        });


        $('#searchProduct').keyup(function () {
            $.ajax({
                url: '/pos/search/product',
                type: 'get',
                data: {
                    search: $(this).val(),
                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function (response) {
                    let tr = '';
                    for(let x = 0; x < response.length; x++) {
                        tr += '<div class="pos-product">\n' +
                            '<button type="button" data-toggle="tooltip" data-placement="top" title data-original-title="' + response[x].description + '" class="pos-product-btn product" p-taxbase="' + response[x].taxbase + '" p-tax="' + response[x].tax + '" p-igv-type='+ response[x].type_igv_id +' p-stock="' + response[x].stock + '" p-price="' + response[x].price + '" p-ot="' + response[x].operation_type + '" p-p="' + response[x].id +'" p-category="' + response[x].category + '" p-description="' + response[x].description + '" p-exonerated="' + response[x].exonerated + '" p-brand="' + response[x].brand + '">\n' +
                            '                        <img src="storage/' + response[x].image + '" alt="" style="width: 100%;">\n' +
                            '                        ' + response[x].description + '\n' +
                            '                    </button>\n' +
                            '                </div>'
                    }

                    $('.row-products').html('');
                    $('.row-products').append(tr);
                }
            });
        });

        $('#searchProduct').keydown(function(event){
            if($(this).val() == '') {
                return;
            }
            let that = this;
            //event.preventDefault();
            var keycode = (event.keyCode ? event.keyCode : event.which);

            if(keycode == '13'){
                $.ajax({
                    url: '{{route('searchProductByCodeBar')}}',
                    type: 'get',
                    dataType: 'json',
                    data: {
                        _token: '{{csrf_token()}}',
                        bar_code: $(this).val()
                    },
                    success: function (response) {
                        addColumnProductFromDatabase(response);
                        $('#searchProduct').val('');
                    }
                });
            }
        });

        $('.select2').select2();
    </script>
@endsection