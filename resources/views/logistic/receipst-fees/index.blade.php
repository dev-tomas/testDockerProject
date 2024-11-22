@extends('layouts.azia')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header color-gray">
                    <div class="row">
                        <div class="col-12 text-center">
                            <h3 class="card-title">RECIBOS POR HONORARIOS</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-10">
                            <a class="btn btn-primary-custom" href="/recibos-honorarios/crear">
                                Registrar Recibo por Honorarios
                            </a>
                            <button class="btn btn-primary-custom" id="receipts_purchase_btn">
                                Registrar Recibo por Honorarios SIRE
                            </button>
                        </div>
                        <div class="col-12 col-md-2">
                            <button type="button" id="btnExcel"  class="btnSale float-right btn btn-secondary-custom">
                                <i class="fa fa-download"></i>
                                Excel
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="">Buscar Entidad</label>
                                <input type="text" id="denomination" class="form-control" placeholder="Ingresar Entidad">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="">Buscar Documento</label>
                                <input type="text" id="document" class="form-control" placeholder="Ingresar documento">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="">Filtro por Fechas</label>
                                <input type="text" id="filter_date" class="form-control" placeholder="Seleccionar fechas">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="table-responsive">
                            <table id="tbl_data" class="dt-bootstrap4" style="width: 100%;">
                                <thead>
                                <th>FECHA</th>
                                <th>SERIE</th>
                                <th>NUM.</th>
                                <th>RUC/DNI/ETC</th>
                                <th>DENOMINACIÓN</th>
                                <th>M.</th>
                                <th>TOTAL RETENCION</th>
                                <th>TOTAL</th>
                                <th></th>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
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
                                    <label><strong>Deposita desde</strong></label>
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
                                    <input type="text" class="form-control datepicker" id="payment_date" name="payment_date" required>
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
                                    <input type="text" name="operation_bank" id="operation_bank" class="form-control" >
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
    <div class="modal fade" id="receiptsSireMdl" tabindex="-1" aria-labelledby="receiptsSireMdlLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="/logistic.recibos-honorarios.register-sire" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="purchaseSireMdlLabel">Registrar recibo por honorario SIRE</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Archivo SIRE (.txt)</label>
                                    <input type="file" name="file" class="form-control" accept=".txt" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary-custom" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary-custom">Subir</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
@section('script_admin')
    <script>
        $(document).ready(function() {
            $('#contOptionDepositoCuenta').hide();
            $('#contOptionDepositoPOS').hide();
        });

        $('#openModalPurchase').on('click', function() {
            clearData();
            $('#mdl_add_purchase_requirement').modal('show');
        });

        $('#openModalRegisterPurchases').on('click', function() {
            $('#filex').val('');
            $('#mdl_RegisterPurchases').modal('show');
        });

        $('#physical_record').on('click', function() {
            window.location.href = '{{route('physicalRecord')}}';
        });

        function clearData() {
            $('#requirements').prop('checked', false);
            $('#provider').val('');
        }

        $('body').on('click', '.remove', function() {
            $(this).parent().parent().remove();
        });

        let tbl_data = $("#tbl_data").DataTable({
            'pageLength' : 15,
            'bLengthChange' : false,
            'lengthMenu': false,
            'language': {
                'url': '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json'
            },
            "order": [[ 0, "desc" ]],
            'searching': false,
            'processing': false,
            'serverSide': true,
            'ajax': {
                'url': '/recibos-honorarios/dt',
                'type' : 'get',
                'data': function(d) {
                    d.denomination = $('#denomination').val();
                    d.serial = $('#document').val();

                    let rangeDates = $('#filter_date').val();
                    var arrayDates = rangeDates.split(" ");
                    var dateSpecificOne =  arrayDates[0].split("/");
                    var dateSpecificTwo =  arrayDates[2].split("/");

                    d.dateOne = dateSpecificOne[2]+'-'+dateSpecificOne[1]+'-'+dateSpecificOne[0];
                    d.dateTwo = dateSpecificTwo[2]+'-'+dateSpecificTwo[1]+'-'+dateSpecificTwo[0];
                }
            },
            'columns': [
                {
                    data: 'date',
                },
                {
                    data: 'shopping_serie'
                },
                {
                    data: 'shopping_correlative'
                },
                {
                    data: 'provider.document'
                },
                {
                    data: 'provider.description'
                },
                {
                    data: 'coin.symbol'
                },
                {
                    data: 'total_retention',
                },
                {
                    data: 'total'
                },
                {
                    data: 'id'
                }
            ],
            'fnRowCallback': function( nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                if (aData['status'] == 9) {
                    $(nRow).css({
                        'text-decoration': 'line-through',
                        'text-decoration-color': 'gray',
                        'color': 'gray'
                    });
                }
                $(nRow).find("td:eq(0)").html(moment(aData['date']).format('DD-MM-YYYY'));

                let button = '<div class="btn-group">';
                button += '<button type="button" class="btn btn-secondary-custom dropdown-toggle dropdown-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Opciones';
                button += '</button>';
                button += '<div class="dropdown-menu dropdown-menu-right" x-placement="bottom-start" style="position: absolute; transform: translate3d(-56px, 33px, 0px); top: 0px; left: 0px; will-change: transform; right: 0px; width: 200px;">';
                button += '<a class="dropdown-item" href="/recibos-honorarios/show/' + aData.id +  '">Ver Compra</a>';
                if (aData['payment_type'] == 'CREDITO' && aData['credit'] != null && aData['credit']['status'] == 0) {
                    button += '<div class="dropdown-divider"></div>';
                    button += '<a class="dropdown-item payment" href="#">Hacer Pago</a>';
                    button += '<div class="dropdown-divider"></div>';
                }
                if (aData['status'] == 1) {
                    button += '<a class="dropdown-item lowPurchase text-danger" href="#">Anular Compra</a>';
                    button += '<div class="dropdown-divider"></div>';
                }
                if (aData['status'] == 9) {
                    button += `<a class="dropdown-item text-danger" href="/recibos-honorarios/crear?has_data=true&shopping=${aData['id']}">Duplicar Compra</a>`;
                    button += '<div class="dropdown-divider"></div>';
                } else {
                    button += `<a class="dropdown-item" href="/recibos-honorarios/editar/${aData['id']}">Editar</a>`;
                    button += '<div class="dropdown-divider"></div>';
                }
                button += '</div>';
                button += '</div>';

                $(nRow).find("td:eq(8)").html(button);
            }
        });

        $('body').on('click', '.lowPurchase', function(e) {
            e.preventDefault();

            var data = tbl_data.row( $(this).parents('tr') ).data();

            $.confirm({
                icon: 'fa fa-question',
                theme: 'modern',
                animation: 'scale',
                type: 'green',
                title: '¿Está seguro de anular esta compra?',
                content: '',
                buttons: {
                    Confirmar: {
                        text: 'Confirmar',
                        btnClass: 'btn-green',
                        action: function(){
                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                }
                            });
                            $.ajax({
                                url: '/recibos-honorarios/delete',
                                type: 'post',
                                data: {purchase: data['id']},
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
        })

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

        $('body').on('click', '.payment', function(e) {
            e.preventDefault();

            var data = tbl_data.row( $(this).parents('tr') ).data();
            let credit = data['credit'];

            $('#creditDate').text(moment(credit['date'], 'YYYY-MM-DD').format('DD-MM-YYYY'))
            $('#creditDocument').text(data['shopping_serie'] + '-' + data['shopping_correlative'])
            $('#creditTotal').text(credit.total)
            $('#montoRestante').text(credit.debt);
            $('#ci').val(credit.id);
            $('#rTableBody').html('')

            if (credit['status'] == 1) {
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

            $.post('/finances/credits/provider/getpayments','credit_id=' + credit.id +'&_token=' + '{{ csrf_token() }}', function(response) {
                $.each(response, function(i, item) {
                    var method = '-';
                    if (item.payment_type == 'EFECTIVO') {
                        method = item.cash.name;
                    }
                    if (item.payment_type == 'DEPOSITO EN CUENTA') {
                        method = item.bank.bank_name;
                    }
                    if (item.payment_type == 'TARJETA DE CREDITO' || item.payment_type == 'TARJETA DE DEBITO') {
                        method = item.payment_method.name;
                    }
                    let tr = `
                        <div class="col-12 col-md-3">
                            <div class="form-group">
                                <p>${item.payment_type}</p>
                            </div>
                        </div>
                        <div class="col-12 col-md-2">
                            <div class="form-group">
                                <p>${moment(item.date, 'YYYY-MM-DD').format('DD-MM-YYYY')}</p>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="form-group">
                                <p>${method}</p>
                            </div>
                        </div>
                        <div class="col-12 col-md-2">
                            <div class="form-group">
                                <p>${item.operation_bank}</p>
                            </div>
                        </div>
                        <div class="col-12 col-md-2">
                            <div class="form-group">
                                <p>${item.payment}</p>
                            </div>
                        </div>
                    `;
                    $('#rTableBody').append(tr);
                });

                $('#mdlPayment').modal('show');
            }, 'json');
            $("html, body").animate({ scrollTop: 0 }, 600);
        })
        $('#payment_mont').keyup(function() {
            let newMont = $(this).val();

            if (newMont == '') {
                newMont = 0.00;
            }

            let debt = $('#montoRestante').text();

            let saldoRestante = parseFloat(debt).toFixed(2) - parseFloat(newMont).toFixed(2);
            $('#saldoRestante').text(parseFloat(saldoRestante).toFixed(2));
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
                                    url: '/finances/credits/provider/payment/store',
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
                                            clearData();
                                        } else {
                                            toastr.error('Ocurrio un error');
                                        }
                                    },
                                    error: function(response) {
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
                "daysOfWeek": [
                    "Dom",
                    "Lu",
                    "Mar",
                    "Mie",
                    "Ju",
                    "Vi",
                    "Sab"
                ],
                "monthNames": [
                    "Enero",
                    "Febrero",
                    "Marzo",
                    "Abril",
                    "Mayo",
                    "Junio",
                    "Julio",
                    "Agosto",
                    "Septiembre",
                    "Octubre",
                    "Noviembre",
                    "Diciembre"
                ],
                "firstDay": 0
            },
            "startDate": moment().subtract(6, 'days'),
            "endDate": moment().add(1, 'days'),
            "cancelClass": "btn-dark"
        }, function(start, end, label) {
            // console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
        });

        $('#filter_date').change(function() {
            tbl_data.ajax.reload();
        });

        $('#denomination').on('keyup', function() {
            tbl_data.ajax.reload();
        });

        $('#document').on('keyup', function() {
            tbl_data.ajax.reload();
        });

        $('#btnExcel').click(function(e) {
            e.preventDefault();
            let data = $('#filter_date').val();

            window.open(`/recibos-honorarios/export?date=${data}`, '_blank');
        });

        $('#receipts_purchase_btn').click(function (e) {
        e.preventDefault();

        $('#receiptsSireMdl').modal('show');
    })

    </script>
@stop
