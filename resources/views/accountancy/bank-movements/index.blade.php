@extends('layouts.azia')
@section('content')
    <input type="hidden" id="audocument" value="{{ auth()->user()->headquarter->client->document }}">
    <input type="hidden" id="autydocument" value="{{ auth()->user()->headquarter->client->document_type->code }}">
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header color-gray">
                    <div class="row">
                        <div class="col-12 text-center">
                            <h3 class="card-title">BANCOS</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-3">
                            <button class="btn btn-primary-custom" type="button" data-toggle="modal" data-target="#importBankModal">Importar</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form id="formPreview">
                        <div class="row">
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label for="">Filtro por Fechas</label>
                                    <input type="text" id="filter_date" name="dates" class="form-control" placeholder="Seleccionar fechas">
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label>Filtro Banco</label>
                                    <select name="bank_filter" id="bank_filter" class="form-control">
                                        <option value="">Todos los Bancos</option>
                                        <option value="BCP">BCP</option>
                                        <option value="INTERBANK">Interbank</option>
                                        <option value="BBVA">BBVA</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <button class="btn btn-primary-custom" type="submit" id="generatePreview">Generar</button>
                            </div>
                        </div>
                    </form>

                    <form id="frm_preview">
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover" id="previewTable">
                                        <thead>
                                            <tr>
                                                <th>FECHA</th>
                                                <th>BANCO</th>
                                                <th>NUMERO OP.</th>
                                                <th>DESCRIPCION</th>
                                                <th>MOV.</th>
                                                <th>MONTO</th>
                                                <th>VENTA</th>
                                                <th>COMPRA</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary-custom btn-block" type="submit">Grabar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="importBankModal" tabindex="-1" aria-labelledby="importBankModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('bank-movements.import') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="importBankModalLabel">Importar Movimientos</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Banco</label>
                                    <select name="bank" id="bank" class="form-control" required>
                                        <option value="">Selecciona un Banco</option>
                                        <option value="1">BCP</option>
                                        <option value="2">Interbank</option>
                                        <option value="3">BBVA</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Archivo</label>
                                    <input type="file" name="file" id="movement" class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Importar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdlPayment" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Pagos</h4>
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
                        <div class="col-12">
                            <table class="table" id="table-payments">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Método de Pag</th>
                                        <th>Fecha de Pago</th>
                                        <th>Depositar en</th>
                                        <th>Operación</th>
                                        <th>Importe Recibido</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="mdlPaymentShopping" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Pagos</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label><strong>Fecha</strong></label>
                                <p id="creditShoppingDate"></p>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label><strong>Documento</strong></label>
                                <p id="creditShoppingDocument"></p>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label><strong>Total Documento</strong></label>
                                <p id="creditShoppingTotal"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <table class="table" id="table-payments-shoppings">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>Método de Pag</th>
                                    <th>Fecha de Pago</th>
                                    <th>Depositar en</th>
                                    <th>Operación</th>
                                    <th>Importe Recibido</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script_admin')
    <script>
        $('#formPreview').submit(function(e) {
            e.preventDefault();
            getData();
        })

        function getData() {
            $('#previewTable tbody').html('');
            let data = $('#formPreview').serialize();
            $.ajax({
                url: '/accounting/movimientos-bancos/preview/generate',
                type: 'post',
                data: data + '&_token=' + '{{ csrf_token() }}',
                dataType: 'json',
                success: function(response) {
                    let salesOptions = response.sales;
                    let shoppingsOptions = response.shoppings;

                    $.each(response.movements, function(i, item) {
                        let idLine = setNewNumberLine();
                        let tr = `
                            <tr data-line="${idLine}">
                                <td>
                                    ${moment(item.date).format('DD-MM-YYYY')}
                                    <input type="hidden" name="data[${idLine}][movement]" value="${item.id}">
                                    <input type="hidden" class="line-id" name="line[]" value="${idLine}">
                                </td>
                                <td>${item.bank}</td>
                                <td>${item.operation_number}</td>
                                <td>${item.description}</td>
                                <td>${item.movement_type}</td>
                                <td>${item.amount}</td>
                                <td>
                                    <select name="data[${idLine}][movement_sale]" data-line="${idLine}" class="form-control movement-sale">
                                        <option value="">Seleccione una Venta</option>
                                        ${getOptions(salesOptions, item.sale_id)}
                                    </select>
                                    <input type="hidden" name="data[${idLine}][payment_line]" class="movement-sale-payment">
                                </td>
                                <td>
                                    <select name="data[${idLine}][movement_shopping]" data-line="${idLine}" class="form-control movement-shopping">
                                        <option value="">Seleccione una Compra</option>
                                        ${getOptions(shoppingsOptions, item.shopping_id)}
                                    </select>
                                    <input type="hidden" name="data[${idLine}][payment_line]" class="movement-shopping-payment">
                                </td>
                            </tr>
                        `;

                        $('#previewTable tbody').append(tr);
                    });

                    $('#previewTable tbody').show();
                    if (response.length == 0) {
                        $('#executePreview').hide();
                        toastr.info('No se encontraron datos');
                    } else {
                        $('#executePreview').show();
                    }
                },
                error: function(response) {
                    console.log(response.responseText);
                    toastr.error('Ocurrio un error');
                }
            });
        }

        function getOptions(data, value) {
            let options = '';
            $.each(data, function(i, e) {
                let serie = e.serialnumber != undefined ? e.serialnumber : e.shopping_serie;
                let correlative = e.correlative != undefined ? e.correlative : e.shopping_correlative;
                let selected = e.id == value ? "selected" : '';
                let type = e.condition_payment != undefined ? e.condition_payment : e.payment_type;

                options += `<option value="${e.id}" data-type="${type}" ${selected}>${serie}-${correlative} [${type}]</option>`
            });

            return options;
        }

        $('body').on('change', '.movement-sale', function(e) {
            e.preventDefault();

            let type = $('option:selected', $(this)).data('type');
            let line = $(this).parent().parent().data('line');

            if (type == 'CREDITO') {
                let credit = $.parseJSON(
                    $.ajax({
                        url: '/finances/credits/getcredit',
                        type: 'post',
                        data: {
                            _token: '{{ csrf_token() }}',
                            sale_id: $(this).val()
                        },
                        dataType: 'json',
                        async: false
                    }).responseText
                );

                $('#creditDate').text(moment(credit.date).format('DD-MM-YYYY'));
                $('#creditDocument').text($('option:selected', $(this)).text());
                $('#creditTotal').text(credit.total);
                $('#table-payments tbody').html('');

                $.post('/accounting/movimientos-bancos/getpayments','credit_id=' + credit.id +'&_token=' + '{{ csrf_token() }}', function(response) {
                    $.each(response, function(idx, el) {
                        let tr = `
                                <tr>
                                    <td>`;
                                if (el.bank_movement_id == null) {
                                    tr += `
                                            <input type="radio" data-line="${line}" name="payment-credit" class="payment-credit" value="${el.id}">
                                        `;
                                }
                            tr += `</td>
                                    <td>${el.payment_type}</td>
                                    <td>${moment(el.date).format('DD-MM-YYYY')}</td>
                                    <td>${el.bank ? el.bank.bank_name : '-'}</td>
                                    <td>${el.operation_bank}</td>
                                    <td>${el.payment}</td>
                                </tr>`;

                        $('#table-payments tbody').append(tr);
                    })

                    $('#mdlPayment').modal('show')
                }, 'json');
            }
        });

        $('body').on('change', '.movement-shopping', function(e) {
            e.preventDefault();

            let type = $('option:selected', $(this)).data('type');
            let line = $(this).parent().parent().data('line');

            if (type == 'CREDITO') {
                let credit = $.parseJSON(
                    $.ajax({
                        url: '/finances/credits/provider/getcredit',
                        type: 'post',
                        data: {
                            _token: '{{ csrf_token() }}',
                            shopping: $(this).val()
                        },
                        dataType: 'json',
                        async: false
                    }).responseText
                );

                $('#creditShoppingDate').text(moment(credit.date).format('DD-MM-YYYY'));
                $('#creditShoppingDocument').text($('option:selected', $(this)).text());
                $('#creditShoppingTotal').text(credit.total);
                $('#table-payments tbody').html('');

                $.post('/accounting/movimientos-bancos/shopping/getpayments','credit_id=' + credit.id +'&_token=' + '{{ csrf_token() }}', function(response) {
                    $.each(response, function(idx, el) {
                        let tr = `
                                <tr>
                                    <td>`;
                                if (el.bank_movement_id == null) {
                                    tr += `
                                            <input type="radio" data-line="${line}" name="payment-credit-shopping" class="payment-credit-shopping" value="${el.id}">
                                        `;
                                }
                            tr += `</td>
                                    <td>${el.payment_type}</td>
                                    <td>${moment(el.date).format('DD-MM-YYYY')}</td>
                                    <td>${el.bank ? el.bank.bank_name : '-'}</td>
                                    <td>${el.operation_bank}</td>
                                    <td>${el.payment}</td>
                                </tr>`;

                        $('#table-payments-shoppings tbody').append(tr);
                    })

                    $('#mdlPaymentShopping').modal('show')
                }, 'json');
            }
        });

        $('body').on('change', 'input[name="payment-credit"]', function() {
            if ($(this).prop('checked')) {
                let selectedValue = $(this).val();
                let selectedDataLine = $(this).data('line');
                let tr = $('#previewTable tbody').find('tr[data-line="' + selectedDataLine + '"]');
                let paymentInputRow =  tr.find('input[name="data[' + selectedDataLine + '][payment_line]"]');
                ;
                paymentInputRow.val(selectedValue);
                $('#mdlPayment').modal('hide')
            }
        });

        $('body').on('change', 'input[name="payment-credit-shopping"]', function() {
            if ($(this).prop('checked')) {
                let selectedValue = $(this).val();
                let selectedDataLine = $(this).data('line');
                let tr = $('#previewTable tbody').find('tr[data-line="' + selectedDataLine + '"]');
                let paymentInputRow =  tr.find('input[name="data[' + selectedDataLine + '][payment_line]"]');
                ;
                paymentInputRow.val(selectedValue);
                $('#mdlPaymentShopping').modal('hide')
            }
        });

        $('#frm_preview').submit(function(e) {
            e.preventDefault();

            let data = $(this).serialize();

            $.ajax({
                url: '/accounting/movimientos-bancos/store',
                type: 'post',
                data: data + '&_token=' + '{{ csrf_token() }}',
                dataType: 'json',
                success: function(response) {
                    if (response) {
                        toastr.success('Se guardó correctamente.');
                        getData();
                    } else {
                        toastr.error('Ocurrio un error');
                    }
                },
                error: function(response) {
                    console.log(response.responseText);
                    toastr.error('Ocurrio un error');
                }
            });
        });

        function setNewNumberLine() {
            let newLine = Math.floor(Math.random() * 9000000000) + 1000000000;

            return parseInt(newLine)
        }

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
    </script>
@stop
