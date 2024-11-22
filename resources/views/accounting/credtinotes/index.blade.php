@extends('layouts.azia')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-default text-center">
                <div class="card-header color-gray">
                    <div class="row">
                        <div class="col-12">
                            <h3 class="card-title">NOTAS CREDITO</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="table-responsive">
                            <table id="tbl_data" class="dt-bootstrap4 table-hover"  style="width: 100%;">
                                <thead>
                                <th>T. DOC.</th>
                                <th>SERIE</th>
                                <th>NUM.</th>
                                <th>RUC/DNI/ETC</th>
                                <th>DENOMINACIÓN</th>
                                <th>DOC. SUSTITUYE</th>
                                <th>PDF</th>
                                <th>XML</th>
                                <th>CDR</th>
                                <th>ESTADO EN LA SUNAT</th>
                                <th>OPCIONES</th>
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

                            <!-- <button class="btn btn-default btnSend" id="0">Enviar al Cliente</button> -->
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

    <div id="mdl_preview" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <button class="btn btn-primary-custom btnPrint" id="">IMPRIMIR</button>
                            <!--<button class="btn btn-secondary-custom btnOpen" id="0">Abrir en navegador</button>-->

                            <button class="btn btn-dark-custom btnSend" id="0">Enviar al Cliente</button>
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

@stop

@section('script_admin')
    <script>
        let tbl_data = $("#tbl_data").DataTable({
            'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
            'language': {
                'url': '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json'
            },
            "order": [[ 0, "acs" ],[2, 'desc'],[3, 'desc']],
            'searching': false,
            'processing': false,
            'serverSide': true,
            'ajax': {
                'url': '/commercial.sales.notes.get',
                'type' : 'get',
            },
            'columns': [
                {
                    data: 'type_voucher.description',
                },
                {
                    data: 'serial_number'
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
                    data: 'sale.correlative',
                    render: function render(data, type, row, meta) {
                        if (type === 'display') {
                            data = row.sale.serialnumber + '-' + row.sale.correlative;
                        }
                        return data;
                    }
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
                if(aData['sunat_code']) {
                    if(aData['sunat_code']['code'] > 1999 && aData['sunat_code']['code'] <= 3999) {
                        $(nRow).css({
                            'text-decoration': 'line-through',
                            'text-decoration-color': 'red',
                            'color': 'black'
                        });
                    }
                }

                $(nRow).find('td:eq(6)').html('<button type="button" class="btn btn-danger-custom btn-sm pdf" id="'+aData['id']+'" >PDF</button>');
                $(nRow).find('td:eq(7)').html('<button type="button" class="btn btn-secondary btn-sm xml">XML</button>');

                if(aData['status_sunat'] == '1') {
                    $(nRow).find('td:eq(9)').html('<button type="button" class="btn btn-secondary-custom btn-sm search">' +
                        '<i class="fa fa-check"></i>' +
                        ' Ver</button>');
                } else {
                    $(nRow).find('td:eq(9)').html('<i class="fa fa-spinner fa-pulse"></i> Pendiente');
                }

                if(aData['response_sunat'] !== null) {
                    $(nRow).find('td:eq(8)').html('<button type="button" class="btn btn-secondary btn-sm cdr">CDR</button>');
                } else {
                    $(nRow).find('td:eq(8)').html('');
                }

                let button = '<div class="btn-group-vertical">';
                button += '<div class="btn-group">';
                button += '<button class="btn btn-secondary-custom dropdown-toggle dropdown-button" data-toggle="dropdown"> Opciones';
                button += '<span class="caret"></span>';
                button += '</button>';
                button += '<ul class="dropdown-menu text-center p-2" x-placement="bottom-start" style="width: 210px !important;">';
                button += '<li><a href="#" class="send_client" >Enviar por correo al Cliente</a></li>';

                button += '<li><a href="#" style="color: black;">-----------------------------------</a></li>';

                if(aData['sunat_code']) {
                    if(aData['sunat_code'] > 0 && aData['sunat_code'] <= 1999) {
                        button += '<li><a class="dropdown-item sendSunat" href="#"  style="color: #ea1024;">Enviar a la SUNAT</a></li>';
                        button += '<li><a href="#" class="cancelOrCancel" style="color: #ea1024;">Anular o Comunicar de Baja</a></li>';
                    }
                } else {
                    button += '<li><a class="dropdown-item sendSunat" href="#"  style="color: #ea1024;">Enviar a la SUNAT</a></li>';
                }

                button += '</ul>';
                button += '</div>';
                $(nRow).find("td:eq(10)").html(button);
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
                title: '¿Está seguro de enviar esta comunicación de baja?',
                content: '',
                buttons: {
                    Confirmar: {
                        text: 'Confirmar',
                        btnClass: 'btn btn-green',
                        action: function() {
                            $.ajax({
                                type: 'post',
                                url: '/commercial/note/send/sunat/' + data['id'] + '/1' + '/0',
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
                    if (d['sunat_code']['code'] != null) {
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
                    let stype = '';
                    if (d['sunat_code']['detail'] != null) {
                        stype = d['sunat_code']['detail'];
                    } else {
                        stype = '-';
                    }
                    data += '<td>Tipo</td><td>' + stype + '</td>';
                data += '</tr>';
                data += '<tr>';
                    let what_to_do = '';
                    if (d['sunat_code']['what_to_do'] != null) {
                        what_to_do = d['sunat_code']['what_to_do'];
                    } else {
                        what_to_do = '-';
                    }
                    data += '<td>¿Qué hacer?</td><td>' + what_to_do + '</td>';
                data += '</tr>';

            $('#tbl_status tbody').html('');
            $('#tbl_status tbody').append(data);
            $('#mdl_status').modal('show');
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
                title: '¿Está seguro de enviar esta nota de crédito?',
                buttons: {
                    Confirmar: function () {
                        $.ajax({
                            type: 'post',
                            url: '/commercial.notes.send/' + id,
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
                                        buttons: {
                                            Cerrar: function() {

                                            }
                                        },
                                        content: function() {
                                            var self = this;
                                            return
                                        }
                                    });
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
                    },
                    Cancelar: function () {

                    }
                }
            });
        });

        $('#tbl_data').on('click', '.creditNote', function() {
            let data = tbl_data.row( $(this).parents('tr') ).data();
            window.location = '/commercial/sale/note/' + data['id'] + '/07/1';
        });

        $('#tbl_data').on('click', '.creditNote', function() {
            let data = tbl_data.row( $(this).parents('tr') ).data();
            window.location = '/commercial/sale/note/' + data['id'] + '/07/4';
        });

        /**
         *btnOpen
         **/
        $('body').on('click', '.btnOpen', function(){
            let id = $(this).attr('id');
            let src = $('#frame_pdf').attr('src');
            window.open(src, '_blank');
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
                title: '¿Está seguro de enviar esta cotización?',
                content: function() {
                    let value = '';

                    return '<div class="form-group">' +
                    '<input type="text" id="email" name="email" class="form-control" value="' + value + '" />' +
                    '<option></option>' +
                    '</div>'
                },
                buttons: {
                    Confirmar: function () {
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
                                        console.log(response.responseText);toastr.error('Ocurrio un error');
                                        console.log(response.responseText)
                                    }
                                });
                            }
                        });
                    },
                    Cancelar: function () {

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


        /**
         * view pdf
         */
        $('body').on('click', '.pdf', function() {
            let data = tbl_data.row( $(this).parents('tr') ).data();
            let id = data['id'];
            $('.btnOpen').attr('id', id);
            $('.btnSend').attr('id', id);
            $('.btnPrint').attr('id', id);
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
                                    console.log(response.responseText);toastr.error('Ocurrio un error');
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
            startDate: new Date(2019, 1, 1),
            endDate: moment().startOf('hour').add(32, 'hour'),
            locale: {
              format: 'DD/MM/YYYY'
            }
        });


        $('#filter_date').change(function() {
             tbl_data.ajax.reload();
             console.log( $('#filter_date').val());
        });

        $('#denomination').on('keyup', function() {
            tbl_data.ajax.reload();
        });

        $('#document').on('keyup', function() {
            tbl_data.ajax.reload();
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

            //let file = 'files/xml/' + '{{Auth::user()->headquarter->client->document}}' + '-';
            let file = '/commercial.download.xml/' + '{{Auth::user()->headquarter->client->document}}' + '-';
            file += data['type_voucher']['code'] + '-' + data['serial_number'] + '-' + data['correlative'];
            window.open(file, '_blank');
        });

        $('#tbl_data').on('click', '.cdr', function() {
            let data = tbl_data.row( $(this).parents('tr') ).data();
            if(data == undefined) {
                tbl_data = $('#tbl_data').DataTable();
                data = tbl_data.row( $(this).parents('tr') ).data();
            }

            let file = '/storage/cdr/'+ '{{Auth::user()->headquarter->client->document}}' + '/R-' + '{{Auth::user()->headquarter->client->document}}' + '-';
            file += data['type_voucher']['code'] + '-' + data['serial_number'] + '-' + data['correlative']  + '.zip';
            window.open(file, '_blank');
        });

        $(document).ready(function() {
            let nc = $('#ncid').val();
            let nd = $('#ndid').val();

            // $('#mdlCreditNote').show();

            if (nc != undefined) {
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

            if (nd != undefined) {
                $.ajax({
                    'url': '/commercial/sale/note/debit/get?nd_id=' + nd,
                    'data': '&_token=' + '{{ csrf_token() }}',
                    'type': 'post',
                    success: function(response) {
                        $('#nd_correlative').text(response['serial_number'] + ' - ' + response['correlative']);
                        $('#showPDFND').attr('data-id',response['id']);
                        $('#printNd').attr('data-id',response['id']);
                        $('#mdlDebitNote').modal('show');
                        console.log(response);
                    },
                });
            }
        });

        $('body').on('click', '#showPDFNC', function() {
            let id = $(this).attr('data-id');
            $('#frame_pdf').attr('src', '/commercial.sale.note.pdf/' +id);
            $('#mdl_preview').modal('show');
        });

        $('body').on('click', '#showPDFND', function() {
            let id = $(this).attr('data-id');
            $('#frame_pdf').attr('src', '/commercial.sale.note.debit.pdf/' +id);
            $('#mdl_preview').modal('show');
        });
    </script>
@stop
