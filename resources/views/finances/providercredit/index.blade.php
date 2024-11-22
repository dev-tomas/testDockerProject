@extends('layouts.azia')
@section('css')
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
    <div class="row">
        <div class="col-12">
            <div class="card card-default text-center">
                <div class="card-header color-gray">
                    <div class="row">
                        <div class="col-12">
                            <h3 class="card-title">CUENTAS POR PAGAR</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-10">
                        </div>
                        <div class="col-12 col-md-2">
                            <button type="button" id="btnExcelCredits"  class="btnSale btn btn-secondary-custom ml-2 pull-right">
                                <i class="fa fa-download"></i>
                                Excel
                            </button>
                            <button type="button" id="btnPdfCredits"  class="btnSale btn btn-secondary-custom ml-2 pull-right">
                                <i class="fa fa-download"></i>
                                PDF
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="">Buscar por Proveedor</label>
                                <select name="provider" id="provider" class="form-control">
                                    <option value="">Todos los Proveedores</option>
                                    @foreach ($providers as $provider)
                                        <option value="{{ $provider->id }}">{{ $provider->description }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="">Filtro por Estado</label>
                                <select name="credit_status" id="credit_status" class="form-control">
                                    <option value="">Todos los Estados</option>
                                    <option value="0">Pendiente</option>
                                    <option value="1">Cancelado</option>
                                    <option value="2">Vencido</option>
                                </select>
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
                            <table id="tbl_data" class="dt-bootstrap4 table-hover"  style="width: 100%;">
                                <thead>
                                    <th>PROVEEDOR</th>
                                    <th width="60">FECHA</th>
                                    <th>DOC. VENTA</th>
                                    <th>M.</th>
                                    <th>MONTO</th>
                                    <th>VENCIMIENTO</th>
                                    <th>DEUDA</th>
                                    <th>DIAS VENCIDO</th>
                                    <th>ESTADO</th>
                                    <th>*</th>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="6" style="text-align:right">Total Deuda:</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="frm_payment">
        <div class="modal fade" id="mdlPayment" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" style="z-index: 9999;">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Abonos</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <input type="hidden" name="ci" id="ci">
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
                            <div class="col-12 col-md-2">
                                <div class="form-group">
                                    <label><strong>Forma de Pago</strong></label>
                                </div>
                            </div>
                            <div class="col-12 col-md-2">
                                <div class="form-group">
                                    <label><strong>Fecha de Expiración</strong></label>
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label><strong>Banco</strong></label>
                                </div>
                            </div>
                            <div class="col-12 col-md-2">
                                <div class="form-group">
                                    <label><strong>Operación</strong></label>
                                </div>
                            </div>
                            <div class="col-12 col-md-2">
                                <div class="form-group">
                                    <label><strong>Monto a Abonar</strong></label>
                                </div>
                            </div>
                            <div class="col-12 col-md-1">
                                <div class="form-group">
                                    <label><strong>Pagado</strong></label>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="rTableBody"></div>
                        <br><br>
                        <div class="row">
                            <div class="col-6 text-right">
                                <strong>DEUDA PENDIENTE:</strong>
                            </div>
                            <div class="col-6 text-left"><span id="montoRestante"></span></div>
                        </div>
                        <div class="row" id="msgCancelled">
                            <div class="col-12 text-center">
                                <h2><strong style="font-size: 1em">FACTURA CANCELADA</strong></h2>
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
            $('#registerActionForm').hide();
            $('#msgCancelled').hide();
        });
        let tbl_data = $("#tbl_data").DataTable({
            'pageLength' : 15,
            'bLengthChange' : false,
            'lengthMenu': false,
            'language': {
                'url': '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json'
            },
            "order": [[ 0, "desc"]],
            'searching': false,
            'processing': false,
            'serverSide': true,
            'ajax': {
                'url': '/finances/payment-providers/dt',
                'type' : 'get',
                'data': function(d) {
                    d.denomination = $('#provider').val();
                    d.status = $('#credit_status').val();

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
                    data: 'provider.description'
                },
                {
                    data: 'date',
                },
                {
                    data: 'shopping.serial',
                },
                {
                    data: 'id'
                },
                {
                    data: 'total'
                },
                {
                    data: 'expiration'
                },
                {
                    data: 'debt'
                },
                {
                    data: 'expiration',
                    render: function render(data, type, row, meta) {
                        if (type === 'display') {
                            if (row['status'] == 0) {
                                var fe1 = new Date();
                                var fe2 = new Date(row['expiration']);
                                if (fe1 > fe2) {
                                    var f1 = moment(row['expiration']).format('DD/MM/YYYY');
                                    var f2 = moment(new Date()).format('DD/MM/YYYY');
                                    var aFecha1 = f1.split('/');
                                    var aFecha2 = f2.split('/');
                                    var fFecha1 = Date.UTC(aFecha1[2],aFecha1[1]-1,aFecha1[0]);
                                    var fFecha2 = Date.UTC(aFecha2[2],aFecha2[1]-1,aFecha2[0]);
                                    var dif = fFecha2 - fFecha1;
                                    var dias = Math.floor(dif / (1000 * 60 * 60 * 24));

                                    data = dias;
                                } else {
                                    data = '-';
                                }
                            } else {
                                data = '-';
                            }
                            
                        }
                        return data;
                    }
                },
                {
                    data: 'status',
                    render: function render(data, type, row, meta) {
                        if (type === 'display') {
                            if (data == '0') {
                                data = '<span class="badge badge-warning">PENDIENTE</span>';
                            } else if (data == '1') {
                                data = '<span class="badge badge-success">CANCELADO</span>';
                            }
                        }
                        return data;
                    }
                },
                {
                    data: 'id'
                },
            ],
            'fnRowCallback': function( nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                var fecha = aData['date'].split("-");
                var expiration = aData['expiration'].split("-");

                $(nRow).find("td:eq(1)").html(fecha[2]+'-'+fecha[1]+'-'+fecha[0]);
                $(nRow).find("td:eq(5)").html(expiration[2]+'-'+expiration[1]+'-'+expiration[0]);
                $(nRow).find('td:eq(2)').html(aData['shopping']['serial'] + ' - ' + aData['shopping']['correlative']);

                let button = '<div class="btn-group">';
                    button += '<button type="button" class="btn btn-secondary-custom dropdown-toggle dropdown-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Opciones';
                    button += '</button>';
                    button += '<div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(-56px, 33px, 0px); top: 0px; left: 0px; will-change: transform; right: 0px; width: 200px;">';
                    button += '<a class="dropdown-item payment" href="#">Abonos</a>';
                    button += '</div>';
                    button += '</div>';

                $(nRow).find('td:eq(9)').html(button);

                $(nRow).find('td:eq(3)').html(aData['shopping']['coin']['symbol']);
            },
            "footerCallback": function ( row, data, start, end, display ) {
                var api = this.api(), data;
                var intVal = function ( i ) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '')*1 :
                        typeof i === 'number' ?
                            i : 0;
                };
                total = api
                    .column(6)
                    .data()
                    .reduce( function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0 );

                $(api.column(6).footer()).html(
                    parseFloat(total).toFixed(2)
                );
            }
        });

        let debttt = 0;
        let coin = '';

        $('body').on('click', '.payment', function() {
            var data = tbl_data.row( $(this).parents('tr') ).data();
            if(data == undefined) {
                tbl_data = $("#tbl_data").DataTable();
                data = tbl_data.row( $(this).parents('tr') ).data();
            }

            clearData();
            $('#rTableBody').html('');

            coin = data['shopping']['coin']['symbol'];
            $("#customer_id").val(data['id']);
            $('#ci').val(data['id']);
            $('#montoRestante').html(coin + ' ' + data['debt']);
            debttt = data['debt'];

            var fecha = data['date'].split("-");
            var fe = fecha[2]+'-'+fecha[1]+'-'+fecha[0];

            $('#creditDate').text(fe);
            $('#creditDocument').text(data['shopping']['serial'] + ' - ' + data['shopping']['correlative']);
            $('#creditTotal').text(data['shopping']['coin']['symbol'] + ' ' + data['total']);

            if (data['status'] == 1) {
                $('#inputsNewForm').hide();
                $('#save').hide();
                $('#msgCancelled').show();
            } else {
                $('#inputsNewForm').show();
                $('#save').show();
                $('#msgCancelled').hide();
            }

            $.post('/finances/payment-providers/get-payments','credit_id=' + data['id'] +'&_token=' + '{{ csrf_token() }}', function(response) {
                console.log(response);
                $.each(response, function(i, item) {   
                    var fecha = response[i]['expiration'].split("-");     
                    var newFecha = fecha[2]+'-'+fecha[1]+'-'+fecha[0];
                    let paymentType = ''; 
                    let paid = '';

                    if (response[i]['payment_type'] != null) {
                        paymentType = `<div class="form-group">
                            <p>`+ response[i]['payment_type'] +`</p>
                            </div>`;
                    } else {
                        paymentType = `<div class="form-group">
                                <input type="hidden" name="pid[]" value=` + response[i]['id'] + `'>
                                <select name="payment_type[]" id="payment_type" class="form-control" required>
                                    <option value="">Seleccione Tipo de Pago</option>
                                    <option value="DEPOSITO BANCARIO">DEPOSITO BANCARIO</option>
                                    <option value="TRANSFERENCIA">TRANSFERENCIA BANCARIA</option>
                                    <option value="CHEQUE">CHEQUE AL DIA</option>
                                    <option value="TARJETA DE CREDITO">TARJETA DE CREDITO</option>
                                    <option value="TARJETA DE DEBITO">TARJETA DE DEBITO</option>
                                    <option value="EFECTIVO">EFECTIVO</option>
                                </select>
                            </div>`;
                    }
                    if (response[i]['bank'] != null) {
                        bank = `<div class="form-group">
                            <p>`+ response[i]['bank'] +`</p>
                            </div>`;
                    } else {
                        bank = `<div class="form-group">
                                    <select name="bank[]" id="bank" class="form-control" required>
                                        <option value="">Seleccione Banco</option>
                                        <option value="BBVA">BBVA</option>
                                        <option value="BCP">BCP</option>
                                        <option value="SCOTIABANK">SCOTIABANK</option>
                                        <option value="INTERBANK">INTERBANK</option>
                                    </select>
                                </div>`;
                    }
                    if (response[i]['operation_bank'] != null) {
                        operationBank = `<div class="form-group">
                            <p>`+ response[i]['operation_bank'] +`</p>
                            </div>`;
                    } else {
                        operationBank = `<div class="form-group">
                                    <input type="text" name="operation_bank[]" id="operation_bank" class="form-control" required>
                                </div>`;
                    }
                    if (response[i]['paid'] == 0) {
                        paid = `<div class="form-group">
                                <input type="checkbox" name="paydoid[]" value="1">
                            </div>`;
                    } else {
                        paid = `<div class="form-group">
                                <p>SI</p>
                            </div>`;
                    }
                    
                    let tr = `
                        <div class="col-12 col-md-2">
                            `+ paymentType +`
                        </div>
                        <div class="col-12 col-md-2">
                            <div class="form-group">
                                <p>`+ newFecha + `</p>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            `+ bank +`
                        </div>
                        <div class="col-12 col-md-2">
                            `+ operationBank +`
                        </div>
                        <div class="col-12 col-md-2">
                            <div class="form-group">
                                <p>`+ response[i]['payment'] +`</p>
                            </div>
                        </div>
                        <div class="col-12 col-md-1">
                            `+ paid +`
                        </div>
                    `;
                    $('#rTableBody').append(tr);
                });

                $('#mdlPayment').modal('show');
            }, 'json');
            $("html, body").animate({ scrollTop: 0 }, 600);
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
                                    url: '/finances/payment-providers/store-payments',
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
                    "Lu",
                    "Mar",
                    "Mie",
                    "Ju",
                    "Vi",
                    "Sab",
                    "Dom"
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
                    "Deciembre"
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
             console.log( $('#filter_date').val());
        });

        $('#provider').on('change', function() {
            tbl_data.ajax.reload();
        });

        $('#credit_status').on('change', function() {
            tbl_data.ajax.reload();
        });

        $('#btnExcelCredits').click(function(e) {
            e.preventDefault();
            let data = $('#filter_date').val();
            let client = $('#provider').val();
            let status = $('#credit_status').val();
            window.open('/finances/payment-providers/excel/export?date=' + data + '&customer=' + client + '&status='+ status, '_blank');
        });
        $('#btnPdfCredits').click(function(e) {
            e.preventDefault();
            let data = $('#filter_date').val();
            let client = $('#provider').val();
            let status = $('#credit_status').val();
            window.open('/finances/payment-providers/pdf/export?date=' + data + '&customer=' + client + '&status='+ status, '_blank');
        });
    </script>
@stop
