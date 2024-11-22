@extends('layouts.azia')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-default text-center">
                <div class="card-header color-gray">
                    <div class="row">
                        <div class="col-12 col-md-12">
                            <h3 style="color: white;" class="card-title">RETENCIONES</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-10">
                            <button id="btnCreate" type="button" class="btnCreate btn btn-primary-custom pull-left">
                                NUEVA RETENCIÓN
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="table-responsive">
                            <table id="tbl_data" class="dt-bootstrap4 table-hover"  style="width: 100%;">
                                <thead>
                                    <th>SERIE/NÚMERO</th>
                                    <th>RECEPTOR</th>
                                    <th>FEC. EMISIÓN</th>
                                    <th>MONEDA</th>
                                    <th>RETENIDO</th>
                                    <th>PAGADO</th>
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
            'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
            'language': {
                'url': '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json'
            },
            //"order": [[ 0, "acs" ],[2, 'desc'],[3, 'desc']],
            'searching': false,
            'processing': false,
            'serverSide': true,
            'ajax': {
                'url': '/spot/retention/dt',
                'type' : 'get',
                'data': function(d) {

                }
            },
            'columns': [
                {data: 'correlative'},
                {data: 'customer.description'},
                {data: 'issue'},
                {data: 'coin'},
                {data: 'retained_amount'},
                {data: 'amount_paid'},
                {data: 'id'},
                {data: 'id'},
                {data: 'id'},
                {data: 'id'},
                {data: 'id'},
            ],
            'fnRowCallback': function( nRow, aData, iDisplayIndex, iDisplayIndexFull) {

                if(aData['response_sunat'] === '1') {
                    $(nRow).find('td:eq(8)').html('<button type="button" class="btn btn-gray-custom btn-sm cdr">CDR</button>');
                } else {
                    $(nRow).find('td:eq(8)').html('');
                }

                $(nRow).find('td:eq(6)').html('<button type="button" class="btn btn-danger-custom btn-sm pdf">PDF</button>');
                $(nRow).find('td:eq(7)').html('<button type="button" class="btn btn-gray-custom btn-sm xml">XML</button>');

                if(aData['response_sunat'] !== null) {
                    $(nRow).find('td:eq(9)').html('<button type="button" class="btn btn-secondary-custom btn-sm search">VER</button>');
                } else {
                    $(nRow).find('td:eq(9)').html('');
                }

                if(aData['response_sunat'] !== 1) {
                    let button = '<div class="btn-group">';
                    button += '<button type="button" class="btn btn-secondary-custom dropdown-toggle dropdown-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Opciones';
                    button += '</button>';
                    button += '<div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 38px, 0px); top: 0px; left: 0px; will-change: transform;">';

                    if(aData['sunat_code']) {
                        if(aData['sunat_code']['code'] > 0 && aData['sunat_code']['code'] <= 1999) {
                            button += '<a class="dropdown-item sendSunat" href="#">Enviar a la SUNAT</a>';
                        }
                    } else {
                        if(aData['response_sunat'] === null) {
                            button += '<a class="dropdown-item sendSunat" href="#">Enviar a la SUNAT</a>';
                        }
                    }

                    button += '<div class="dropdown-divider"></div>';
                    $(nRow).find('td:eq(10)').html(button);
                }
            }
        });

        $('#btnCreate').click(function() {
            window.location.href = '/retention/create';
        });

        $('body').on('click', '.pdf', function() {
            let data = tbl_data.row( $(this).parents('tr') ).data();
            $('#frame_pdf').attr('src', '/retention/pdf/' + data['id']);
            $('#mdl_preview').modal('show');
        });

        $('#btnClose').click(function() {
            $('#mdl_preview').modal('hide');
        });

        $('body').on('click', '.btnPrint', function(){
            $("#frame_pdf").get(0).contentWindow.print();
        });

        $('body').on('click', '.btnOpen', function(){
            let id = $(this).attr('id');
            let src = $('#frame_pdf').attr('src');
            window.open(src + '_blank');

        });

        $('#tbl_data').on('click', '.xml', function() {
            let data = tbl_data.row( $(this).parents('tr') ).data();
            if(data == undefined) {
                tbl_data = $('#tbl_data').DataTable();
                data = tbl_data.row( $(this).parents('tr') ).data();
            }

            //let file = 'files/xml/' + '{{Auth::user()->headquarter->client->document}}' + '-';
            let file = '/commercial.download.xml/' + '{{Auth::user()->headquarter->client->document}}' + '-';
            file += 20 + '-' + data['serial_number'] + '-' + data['correlative'];
            window.open(file, '_blank');
        });

        $('#tbl_data').on('click', '.cdr', function() {
            let data = tbl_data.row( $(this).parents('tr') ).data();
            if(data == undefined) {
                tbl_data = $('#tbl_data').DataTable();
                data = tbl_data.row( $(this).parents('tr') ).data();
            }

            let file = '/files/cdr/' + 'R-' + '{{Auth::user()->headquarter->client->document}}' + '-';
            file += 20 + '-' + data['serial_number'] + '-' + data['correlative']  + '.zip';
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
                title: '¿Está seguro de enviar esta retención?',
                content: '',
                buttons: {
                    Confirmar: {
                        text: 'Confirmar',
                        btnClass: 'btn btn-green',
                        action: function() {
                            $.ajax({
                                type: 'post',
                                url: '/retention/send/sunat/' + data['id'],
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

        $('body').on('click', '.search', function(){
            let d = tbl_data.row( $(this).parents('tr') ).data();
            let icon = '<i class="fa fa-remove"></i>';
            let icon_accept = '<i class="fa fa-remove"></i>';
            if(d['status_sunat'] === '1') {
                icon = '<i class="fa fa-check"></i>';
            }

            if(d['response_sunat'] === '1') {
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
    </script>
@stop
