@extends('layouts.azia')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-default text-center">
                <div class="card-header color-gray">
                    <div class="row">
                        <div class="col-12 col-md-12">
                            <h3 class="card-title">COMUNICACIONES DE BAJA</h3>
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
                                <th>FECHA BAJA</th>
                                <th>FECHA DE DOC.</th>
                                <th>DOCUMENTOS</th>
                                <th>MOTIVOS</th>
                                <th>TICKET (SUNAT)</th>
                                <th>PDF</th>
                                <th>XML</th>
                                <th>CDR</th>
                                <th>ESTADO EN LA SUNAT</th>
                                <th>*</th>
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

    <div id="mdl_preview" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <button class="btn btn-primary-custom btnPrint" id="">IMPRIMIR</button>
                            <button class="btn btn-primary-custom btnOpen" id="0">Abrir en navegador</button>

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

    <div id="mdl_status" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
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
            'language': {
                'url': '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json'
            },
            "order": [[ 0, "acs" ],[2, 'desc'],[3, 'desc']],
            'searching': false,
            'processing': false,
            'serverSide': true,
            'ajax': {
                'url': '/commercial/low/dt',
                'type' : 'get',
                'data': function(d) {

                }
            },
            'columns': [
                {data: 'correlative'},
                {data: 'generation_date'},
                {data: 'generation_date'},
                {data: 'id'},
                {data: 'detail.motive'},
                {data: 'ticket'},
                {data: 'id'},
                {data: 'id'},
                {data: 'id'},
                {data: 'id'},
                {data: 'id'},
            ],
            'fnRowCallback': function( nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                if(aData['detail']['sale'] !== null) {
                    let text = aData['detail']['sale']['type_voucher']['description'] + ' ';
                    text += aData['detail']['sale']['serialnumber'] + '-' + aData['detail']['sale']['correlative'];
                    $(nRow).find('td:eq(3)').html(text);
                } else if(aData['detail']['debit_note'] !== null) {
                    let text = aData['detail']['debit_note']['type_voucher']['description'] + ' ';
                    text += aData['detail']['debit_note']['serial_number'] + '-' + aData['detail']['debit_note']['correlative'];
                    $(nRow).find('td:eq(3)').html(text);
                } else if(aData['detail']['credit_note'] !== null) {
                    let text = aData['detail']['credit_note']['type_voucher']['description'] + ' ';
                    text += aData['detail']['credit_note']['serial_number'] + '-' + aData['detail']['credit_note']['correlative'];
                    $(nRow).find('td:eq(3)').html(text);
                }

                if(aData['sunat_code_id'] == '1') {
                    $(nRow).find('td:eq(8)').html('<button type="button" class="btn btn-gray-custom btn-sm cdr">CDR</button>');
                } else {
                    $(nRow).find('td:eq(8)').html('');
                }

                $(nRow).find('td:eq(6)').html('<button type="button" class="btn btn-danger-custom btn-sm pdf">PDF</button>');
                $(nRow).find('td:eq(7)').html('<button type="button" class="btn btn-gray-custom btn-sm xml">XML</button>');

                if(aData['sunat_code_id'] !== null) {
                    $(nRow).find('td:eq(9)').html('<button type="button" class="btn btn-secondary-custom btn-sm search">VER</button>');
                } else {
                    $(nRow).find('td:eq(9)').html('');
                }

                // if(aData['sunat_code_id'] !== 1) {
                    let button = '<div class="btn-group">';
                    button += '<button type="button" class="btn btn-secondary-custom dropdown-toggle dropdown-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Opciones';
                    button += '</button>';
                    button += '<div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 38px, 0px); top: 0px; left: 0px; will-change: transform;">';

                    if(aData['sunat']) {
                        if(aData['sunat']['code'] > 0 && aData['sunat']['code'] <= 1999) {
                            button += '<a class="dropdown-item sendSunat" href="#">Enviar a la SUNAT</a>';
                        }
                    } else {
                        if(aData['sunat_code_id'] === null) {
                            button += '<a class="dropdown-item sendSunat" href="#">Enviar a la SUNAT</a>';
                        }
                    }

                    button += '<div class="dropdown-divider"></div>';
                    $(nRow).find('td:eq(10)').html(button);
                // } 
            }
        });

        $('body').on('click', '.pdf', function() {
            let data = tbl_data.row( $(this).parents('tr') ).data();
            let id = $(this).attr('id');
            $('.btnOpen').attr('id', id);
            $('.btnSend').attr('id', id);
            $('.btnPrint').attr('id', id);
            $('#frame_pdf').attr('src', '/commercial/low/show/pdf/' + data['id']);

            $('#mdl_preview').modal('show');
        });

        $('#tbl_data').on('click', '.xml', function() {
            let data = tbl_data.row( $(this).parents('tr') ).data();
            if(data == undefined) {
                tbl_data = $('#tbl_data').DataTable();
                data = tbl_data.row( $(this).parents('tr') ).data();
            }

            let date = data['communication_date'].split('-');

            let file = '/commercial.download.xml/' + '{{Auth::user()->headquarter->client->document}}' + '-';
            file += 'RA-' + date[0] + date[1] + date[2] + '-' + data['correlative'];
            window.open(file, '_blank');
        });

        $('#tbl_data').on('click', '.cdr', function() {
            let data = tbl_data.row( $(this).parents('tr') ).data();
            if(data == undefined) {
                tbl_data = $('#tbl_data').DataTable();
                data = tbl_data.row( $(this).parents('tr') ).data();
            }

            let date = data['communication_date'].split('-');

            let file = '/files/cdr/R-' + '{{Auth::user()->headquarter->client->document}}' + '-';
            file += 'RA-' + date[0] + date[1] + date[2] + '-' + data['correlative'];

            window.open(file, '_blank');
        });

        $('#tbl_data').on('click', '.sendSunat', function() {
            let data = tbl_data.row( $(this).parents('tr') ).data();
            $.confirm({
                icon: 'fa fa-warning',
                theme: 'modern',
                animation: 'scale',
                type: 'green',
                draggable: false,
                title: '¿Está seguro de enviar esta comunicación de baja?',
                content: '',
                buttons: {
                    Confirmar: {
                        text: 'Confirmar',
                        btnClass: 'btn btn-green',
                        action: function() {
                            $.ajax({
                                type: 'post',
                                url: '/commercial/send/low/communication/only/' + data['id'],
                                data: {
                                    _token: '{{ csrf_token() }}',
                                },
                                dataType: 'json',
                                success: function(response) {
                                    if(response == true) {
                                        toastr.success('El comprobante fue enviado a Sunat satisfactoriamente');
                                    } else if(response === '-1') {
                                        toastr.warning('Ocurrió un error con el comprobante, reviselo y vuelva a enviarlo.');toastr.warning('Debe de generar correlativos para nota de credito');
                                    } else if(response === '-2') {
                                        toastr.error('El comprobante fue enviado a Sunat y fue rechazado automáticamente, vuelva a enviarlo manualmente');
                                    } else if(response === '-3') {
                                        toastr.info('El comprobante fue enviado a Sunat y fue validado con una observación.');
                                    } else {
                                        toastr.error('Los servidores de la Sunat están teniendo problemas.');
                                    }

                                    tbl_data.ajax.reload();
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

        $('body').on('click', '.search', function(){
            let d = tbl_data.row( $(this).parents('tr') ).data();
            console.log(d);
            let icon = '<i class="fa fa-remove"></i>';
            let icon_accept = '<i class="fa fa-remove"></i>';
            if(d['status_sunat'] == 0) {
                icon = '<i class="fa fa-check"></i>';
            }

            if(d['sunat_code_id'] === 1) {
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
            if (d['sunat'] != null) {
                scode = d['sunat']['code'];
            } else {
                scode = '-';
            }
            data += '<td>Código</td><td>' +scode  + '</td>'
            data += '</tr>';
            data += '<tr>';
            let sdescription = '';
            if (d['sunat']['detail'] != null) {
                sdescription = d['sunat']['detail'];
            } else {
                sdescription = '-';
            }

            data += '<td>Descripción</td><td>' + sdescription + '</td>'
            data += '</tr>';
            data += '<tr>';
            let s_what = '';
            if (d['sunat']['what_to_doil'] != null) {
                s_what = d['sunat']['what_to_do'];
            } else {
                s_what = 'Nada';
            }
            data += '<td>¿Que hacer?</td><td>' + s_what + '</td>'
            data += '</tr>';
            $('#tbl_status tbody').html('');
            $('#tbl_status tbody').append(data);
            $('#mdl_status').modal('show');
        });
        $('#btnClose').click(function() {
            $('#mdl_preview').modal('hide');
        });
    </script>
@stop
