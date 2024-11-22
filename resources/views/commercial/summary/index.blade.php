@extends('layouts.azia')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-default text-center">
                <div class="card-header color-gray">
                    <div class="row">
                        <div class="col-12">
                            <h3 class="card-title">Resumen diario de Boletas</h3>
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
                            <table id="tbl_data" class="dt-bootstrap4 table-hover"  style="width: 100%;">
                                <thead>
                                <th>NÚMERO</th>
                                <th>FECHA GENERACIÓN</th>
                                <th>FECHA DOCS</th>
                                <th width="300px;">DOCUMENTO Y DETALLE</th>
                                <th>TICKET(SUNAT)</th>
                                <th>PDF</th>
                                <th>XML</th>
                                <th>CDR</th>
                                <th>ESTADO EN LA SUNAT</th>
                                <th>Opciones</th>
                                </thead>

                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    {{-- <div class="col-12">
                        <button type="button" class="btn btn-primary-custom btnSummary">GENERAR RESUMEN DIARIO</button>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>

    <div id="mdl_preview" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
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

    <div class="modal fade" id="mdlDetail" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title" style="font-size: 1.5em !important;">DETALLE DE RESUMEN</h1>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <div class="row">
                        <div class="col-12">
                            <div class="table" id="tblDetail">
                                <table class="table-hover table">
                                    <thead style="color: black;">
                                    <th>SERIE</th>
                                    <th>CORRELATIVO</th>
                                    <th>ESTADO</th>
                                    <th>GRAVADA</th>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="mdlSummary" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">GENERAR RESUMEN</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <div class="row text-center">
                        <div class="col-12">
                            <p><strong>IMPORTANTE</strong></p>
                            <p>Con esta opción se genera uno o varios resúmenes. Es posible que tenga que usar esta opción si emite documentos con fecha pasada.</p>
                            <p>Selecciona cuidadosamente el día.</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group" style="color: black;">
                                <label for="dateSummary"><strong>FECHA</strong></label>
                                <input type="text" class="form-control datepicker_s" id="dateSummary">
                            </div>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary-custom btn-block" id="generate">GENERAR</button>
                        </div>
                        <div class="col-12">
                            <button type="button" class="btn btn-secondary pull-right" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="mdlNotSend" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <div class="row">
                        <div class="col-12">
                            <div class="table" id="tblDetailNotSend">
                                <table class="table-hover table">
                                    <thead style="color: black;">
                                    <th>SERIE</th>
                                    <th>CORRELATIVO</th>
                                    <th>GRAVADA</th>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="mdl_status" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="z-index: 9999;">
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

@stop

@section('script_admin')
    <script>
        let tbl_data = $("#tbl_data").DataTable({
            'pageLength' : 15,
            'bLengthChange' : false,
            'lengthMenu': false,
            'language': {
                'url': '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json'
            },
            "order": [[ 0, "acs" ]],
            'searching': false,
            'processing': false,
            'serverSide': true,
            'ajax': {
                'url': '/commercial.dt.sales.2/2',
                'type' : 'get',
                'data': function(d) {
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
                    data: 'correlative',
                },
                {
                    data: 'date_generation',
                },
                {
                    data: 'date_issues',
                },
                {
                    data: 'id'
                },
                {
                    data: 'ticket'
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
            ],
            'fnRowCallback': function( nRow, aData) {
                let link = '<a href="#" class="blue detailSummary">' + 'VER NUEVO RESUMEN DE ';
                link += '<b>' + aData['detail'].length + '</b>' + ' ';
                link += 'DOCUMENTOS' + '</a>';

                let xml = '<button class="btn btn-default btn-xs xml"><i style="font-size: 25px;" class="fa fa-file-text-o"></i></button>';

                let see = '';
                let cdr = '';

                if(aData['sunat_code'] == null) {
                    see = '<button class="btn btn-default btn-xs"><i style="font-size: 25px;" class="fa fa-spinner fa-pulse"></i></button>';
                    cdr = '';
                } else {
                    if(aData['sunat_code']['code'] <= 1999) {
                        see = '<button class="btn btn-default"><i style="font-size: 25px;" class="fa fa-spinner fa-pulse"></i></button>';
                        cdr = '';
                    }

                    if(aData['sunat_code']['code'] === '0') {
                        see = '<button class="btn btn-default btn-xs search"><i style="font-size: 25px;" class="fa fa-eye"></i></button>';
                        cdr = '<button class="btn btn-default btn-xs cdr"><i style="font-size: 25px;" class="fa fa-file-zip-o"></i></button>';
                    }
                }

                let pdf = '<button class="btn btn-default btn-xs pdf"><i style="font-size: 25px;" class="fa fa-file-pdf-o"></i></button>';

                let button = '<div class="btn-group">';
                button += '<button type="button" class="btn btn-secondary-custom dropdown-toggle dropdown-button dropdown-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Opciones';
                button += '</button>';
                button += '<div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(-56px, 33px, 0px); top: 0px; left: 0px; will-change: transform; right: 0px; width: 200px;">';
                // button += '<a class="dropdown-item notSend" style="color: #ff0000;">Ver no enviados</a>';

                if(aData['sunat_code'] !== null) {
                    if(aData['sunat_code']['code'] > 0 && aData['sunat_code']['code'] <= 1999) {
                        button += '<a class="dropdown-item sendSunat" href="#"  style="">Enviar a la SUNAT</a>';
                    }
                } else {
                    button += '<a class="dropdown-item sendSunat" href="#"  style="color: #1e7e34;">Enviar a la SUNAT</a>';
                }

                button += '';
                button += '</div>';
                button += '</div>';

                $(nRow).find('td:eq(3)').html(link);
                $(nRow).find('td:eq(5)').html(pdf);
                $(nRow).find('td:eq(6)').html(xml);
                $(nRow).find('td:eq(7)').html(cdr);
                $(nRow).find('td:eq(8)').html(see);
                $(nRow).find('td:eq(9)').html(button);
            }
        });

        $('.btnSummary').click(function() {
            $('#mdlSummary').modal('show');
        });

        $('#generate').click(function() {
            if($('#dateSummary').val() === '') {
                toastr.error('Debe Ingresar una fecha');
            } else {
                $.ajax({
                    'url': '/commercial/summary/send/' + $('#dateSummary').val(),
                    'type': 'get',
                    success: function(response) {
                        if(response['response'] == true) {
                            toastr.success('Se grabó satisfactoriamente el Comprobante');
                            toastr.success('El comprobante fue enviado a Sunat satisfactoriamente');
                        } else if(response['response'] == -1) {
                            toastr.success('Se grabó satisfactoriamente el Comprobante');
                            toastr.warning('Ocurrió un error con el comprobante, reviselo y vuelva a enviarlo.');
                        } else if(response['response'] == -2) {
                            toastr.success('Se grabó satisfactoriamente el Comprobante');
                            toastr.error('El comprobante fue enviado a Sunat y fue rechazado automáticamente, vuelva a enviarlo manualmente');
                        } else if(response['response'] == -3) {
                            toastr.success('Se grabó satisfactoriamente el Comprobante');
                            toastr.info('El comprobante fue enviado a Sunat y fue validado con una observación.');
                        } else if(response['response'] == 99) {
                            toastr.success('Se grabó satisfactoriamente el Comprobante');
                            toastr.info('El comprobante será enviado a la Sunat en un resumen diario.');
                        } else {
                            toastr.error('Ocurrió un error desconocido, intentelo más tarde.');
                        }

                        /*$('#dateSummary').val('');
                        $('#mdlSummary').modal('hide');*/
                        tbl_data.ajax.reload();
                    }
                });
            }
        });

        $('#tbl_data').on('click', '.xml', function() {
            let data = tbl_data.row( $(this).parents('tr') ).data();
            if(data == undefined) {
                tbl_data = $('#tbl_data').DataTable();
                data = tbl_data.row( $(this).parents('tr') ).data();
            }

            let date = data['date_generation'].split('-');

            let file = '/commercial.download.xml/' + '{{Auth::user()->headquarter->client->document}}' + '-';
            file += 'RC-' + date[2] + date[1] + date[0] + '-' + data['correlative'];
            window.open(file, '_blank');
        });

        $('#tbl_data').on('click', '.cdr', function() {
            let data = tbl_data.row( $(this).parents('tr') ).data();
            if(data == undefined) {
                tbl_data = $('#tbl_data').DataTable();
                data = tbl_data.row( $(this).parents('tr') ).data();
            }

            let date = data['date_generation'].split('-');

            let file = 'files/cdr/' + 'R-' + '{{Auth::user()->headquarter->client->document}}' + '-';
            file += 'RC-' + date[2] + date[1] + date[0] + '-' + data['correlative']  + '.zip';

            window.open(file, '_blank');
        });

        $('body').on('click', '.detailSummary', function() {
            $('#tblDetail tbody').html('');
            let data = tbl_data.row( $(this).parents('tr') ).data();
            $.each(data['detail'], function(index, value) {
                let table = `
                    <tr>
                        <td>${value['sale'].serialnumber}</td>
                        <td>${value['sale'].correlative }</td>
                        <td>${value.condition == "1" ? "COMUNICACION" : "ANULACION"}</td>
                        <td>${value['sale'].taxed}</td>
                    </tr>
                `;
                $('#tblDetail tbody').append(table);
            });

            $('#mdlDetail').modal('show');
        });

        $('body').on('click', '.pdf', function() {
            let data = tbl_data.row( $(this).parents('tr') ).data();
            $('#frame_pdf').attr('src', '/commercial/summary/pdf/' + data['id']);
            $('#mdl_preview').modal('show');
        });

        $('body').on('click', '#btnClose', function(){
            $('#mdl_preview').modal('hide');
        });

        $('body').on('click', '.notSend', function() {
            let data = tbl_data.row( $(this).parents('tr') ).data();
            let table = '';
            $('#tblDetailNotSend tbody').html('');

            $.ajax({
                type: 'get',
                url: '/summary/get/not/send/' + data['date_generation'],
                dataType: 'json',
                success: function(response) {
                    for(let x = 0; x < response.length; x++) {
                        table += '<tr>';
                        table += '<td>' + response[x]['serialnumber'] + '</td>';
                        table += '<td>' + response[x]['correlative'] + '</td>';
                        table += '<td>' + response[x]['taxed'] + '</td>';
                        table += '<tr>';
                    }
                }
            });

            $('#tblDetailNotSend tbody').append(table);
            $('#mdlNotSend').modal('show');
        });

        $('body').on('click', '.search', function() {
            let d = tbl_data.row( $(this).parents('tr') ).data();
            let icon = '<i class="fa fa-remove"></i>';
            let icon_accept = '<i class="fa fa-remove"></i>';
            if(d['status_sunat'] == '1') {
                icon = '<i class="fa fa-check"></i>';
            }

            if(d['response_sunat'] == '1') {
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
            if (d['sunat_code']['detail'] != null) {
                sdescription = d['sunat_code']['detail'];
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

        $('#tbl_data').on('click', '.sendSunat', function() {
            let data = tbl_data.row( $(this).parents('tr') ).data();
            $.confirm({
                icon: 'fa fa-warning',
                theme: 'modern',
                animation: 'scale',
                type: 'green',
                draggable: false,
                title: '¿Está seguro de enviar este resumen diario?',
                content: '',
                buttons: {
                    Confirmar: {
                        text: 'Confirmar',
                        btnClass: 'btn btn-green',
                        action: function() {
                            $.ajax({
                                type: 'post',
                                url: '/commercial/summary/send/' + data['id'] + '/1',
                                data: {
                                    _token: '{{csrf_token()}}'
                                },
                                dataType: 'json',
                                success: function(response) {
                                    if(response === true) {
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
        });
        
        $('.datepicker_s').datepicker({
            minDate: "-3",
            format: 'dd-mm-yyyy',
            autoclose: true
        });
    </script>
@stop
