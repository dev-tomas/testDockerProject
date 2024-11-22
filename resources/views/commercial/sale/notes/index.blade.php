@extends('layouts.azia')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-default text-center">
                <div class="card-header color-gray">
                    <div class="row">
                        <div class="col-12">
                            <h2 style="color: white;">NOTAS CREDITO/DEBITO</h2>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    {{-- <div class="row">
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
                    </div> --}}
                    <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
                        <li class="nav-item">
                          <a class="nav-link active show" id="custom-tabs-two-home-tab" data-toggle="pill" href="#custom-tabs-two-home" role="tab" aria-controls="custom-tabs-two-home" aria-selected="false">NOTAS DE CREDITO</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="custom-tabs-two-profile-tab" data-toggle="pill" href="#custom-tabs-two-profile" role="tab" aria-controls="custom-tabs-two-profile" aria-selected="false">NOTAS DE DEBITO</a>
                        </li>
                      </ul>
                    <div class="tab-content" id="custom-tabs-two-tabContent">
                        <div class="tab-pane fade active show" id="custom-tabs-two-home" role="tabpanel" aria-labelledby="custom-tabs-two-home-tab">
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
                        <div class="tab-pane fade" id="custom-tabs-two-profile" role="tabpanel" aria-labelledby="custom-tabs-two-profile-tab">
                            <div class="row">
                                <div class="table-responsive">
                                    <table id="tbl_data2" class="dt-bootstrap4 table-hover"  style="width: 100%;">
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

                            {{-- <button class="btn btn-default btnSend" id="0">Enviar al Cliente</button> --}}
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
            "order": [[ 0, "acs" ],[2, 'desc'],[3, 'desc']],
            'searching': false,
            'processing': true,
            'serverSide': true,
            'searching' : false,
            'ajax': {
                'url': '/commercial.sales.notes.get',
                'type' : 'get',
                // 'data': function(d) {
                //     d.denomination = $('#denomination').val();
                //     d.serial = $('#document').val();

                //     let rangeDates = $('#filter_date').val();
                //     var arrayDates = rangeDates.split(" ");
                //     var dateSpecificOne =  arrayDates[0].split("/");
                //     var dateSpecificTwo =  arrayDates[2].split("/");

                //     d.dateOne = dateSpecificOne[2]+'-'+dateSpecificOne[1]+'-'+dateSpecificOne[0];
                //     d.dateTwo = dateSpecificTwo[2]+'-'+dateSpecificTwo[1]+'-'+dateSpecificTwo[0];
                //     console.log(d.dateOne);
                //     console.log(d.dateTwo);
                // }
            },
            'columns': [
                {
                    data: 'tp_description',
                },
                {
                    data: 'serial_number'
                },
                {
                    data: 'correlative'
                },
                {
                    data: 'document'
                },
                {
                    data: 'c_description'
                },
                {
                    data: 'ss',
                    render: function render(data, type, row, meta) {
                        if (type === 'display') {
                            data = row.ss + '-' + row.sc;
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
                // var fecha = aData['date'].split("-");
                $(nRow).find('td:eq(6)').html('<button type="button" class="btn btn-danger-custom btn-sm pdf" id="'+aData['id']+'" >PDF</button>');

                if(aData['sunat_code'] == 0) {
                    $(nRow).find('td:eq(7)').html('<button type="button" class="btn btn-secondary btn-sm xml">XML</button>');
                    $(nRow).find('td:eq(8)').html('<button type="button" class="btn btn-secondary btn-sm cdr">CDR</button>');
                    $(nRow).find('td:eq(9)').html('<button type="button" class="btn btn-secondary-custom btn-sm search">' +
                        '<i class="fa fa-check"></i>' +
                    ' Ver</button>');
                } else {
                    $(nRow).find('td:eq(7)').html('<i class="fa fa-spinner fa-pulse"></i>');
                    $(nRow).find('td:eq(8)').html('<i class="fa fa-spinner fa-pulse"></i>');
                    $(nRow).find('td:eq(9)').html('<i class="fa fa-spinner fa-pulse"></i> Pendiente');
                }
                let button = '<div class="btn-group-vertical">';
                button += '<div class="btn-group">';
                button += '<button class="btn btn-secondary-custom dropdown-toggle dropdown-button" data-toggle="dropdown"> Opciones';
                button += '<span class="caret"></span>'
                button += '</button>';
                button += '<ul class="dropdown-menu text-center p-2" x-placement="bottom-start" style="width: 210px !important;">';
                button += '<li><a href="#" class="send_client" >Enviar por correo al Cliente</a></li>';
                button += '<li><a href="#" class="consultCdr" style="color: #ea1024;">Consultar o recuperar CDR</a></li>';
                button += '<li><a href="http://e-consulta.sunat.gob.pe/ol-ti-itconsvalicpe/ConsValiCpe.htm?E='+ $('#rucclient').val() +'&T='+ aData['tp_description'] +'&R='+ aData['document'] +'&S='+ aData['serialnumber']  +'&N='+ aData['correlative'] +'&F='+aData['date']+'&T='+ aData['total'] +'" class="consultCPE" style="color: #28a745;" target="_blank">Verificar CPE en la SUNAT</a></li>';
                button += '<li><a href="http://www.sunat.gob.pe/ol-ti-itconsverixml/ConsVeriXml.htm" class="consultXML" style="color: #28a745;">Verificar XML en la SUNAT</a></li>';
                button += '<li><a href="#" style="color: black;">-----------------------------------</a></li>';
                button += '<li><a href="#" class="cancelOrCancel" style="color: #ea1024;">Anular o Comunicar de Baja</a></li>';
                button += '</ul>';
                button += '</div>';
                $(nRow).find("td:eq(10)").html(button);
            }
        });
        let tbl_data2 = $("#tbl_data2").DataTable({
            'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
            'language': {
                'url': '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json'
            },
            "order": [[ 0, "acs" ],[2, 'desc'],[3, 'desc']],
            'searching': false,
            'processing': true,
            'serverSide': true,
            'searching' : false,
            'ajax': {
                'url': '/commercial.sales.notes.debit.get',
                'type' : 'get',
                // 'data': function(d) {
                //     d.denomination = $('#denomination').val();
                //     d.serial = $('#document').val();

                //     let rangeDates = $('#filter_date').val();
                //     var arrayDates = rangeDates.split(" ");
                //     var dateSpecificOne =  arrayDates[0].split("/");
                //     var dateSpecificTwo =  arrayDates[2].split("/");

                //     d.dateOne = dateSpecificOne[2]+'-'+dateSpecificOne[1]+'-'+dateSpecificOne[0];
                //     d.dateTwo = dateSpecificTwo[2]+'-'+dateSpecificTwo[1]+'-'+dateSpecificTwo[0];
                //     console.log(d.dateOne);
                //     console.log(d.dateTwo);
                // }
            },
            'columns': [
                {
                    data: 'tp_description',
                },
                {
                    data: 'serial_number'
                },
                {
                    data: 'correlative'
                },
                {
                    data: 'document'
                },
                {
                    data: 'c_description'
                },
                {
                    data: 'ss',
                    render: function render(data, type, row, meta) {
                        if (type === 'display') {
                            data = row.ss + '-' + row.sc;
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
                // var fecha = aData['date'].split("-");
                $(nRow).find('td:eq(6)').html('<button type="button" class="btn btn-danger-custom btn-sm pdfD" id="'+aData['id']+'" >PDF</button>');

                if(aData['sunat_code'] == 0) {
                    $(nRow).find('td:eq(7)').html('<button type="button" class="btn btn-secondary btn-sm xmlD">XML</button>');
                    $(nRow).find('td:eq(8)').html('<button type="button" class="btn btn-secondary btn-sm cdrD">CDR</button>');
                    $(nRow).find('td:eq(9)').html('<button type="button" class="btn btn-secondary-custom btn-sm searchD">' +
                        '<i class="fa fa-check"></i>' +
                    ' Ver</button>');
                } else {
                    $(nRow).find('td:eq(7)').html('<i class="fa fa-spinner fa-pulse"></i>');
                    $(nRow).find('td:eq(8)').html('<i class="fa fa-spinner fa-pulse"></i>');
                    $(nRow).find('td:eq(9)').html('<i class="fa fa-spinner fa-pulse"></i> Pendiente');
                }
                let button = '<div class="btn-group-vertical">';
                button += '<div class="btn-group">';
                button += '<button class="btn btn-secondary-custom dropdown-toggle dropdown-button" data-toggle="dropdown"> Opciones';
                button += '<span class="caret"></span>'
                button += '</button>';
                button += '<ul class="dropdown-menu text-center p-2" x-placement="bottom-start" style="width: 210px !important;">';
                button += '<li><a href="#" class="send_client2" >Enviar por correo al Cliente</a></li>';
                button += '<li><a href="#" class="consultCdr" style="color: #ea1024;">Consultar o recuperar CDR</a></li>';
                button += '<li><a href="http://e-consulta.sunat.gob.pe/ol-ti-itconsvalicpe/ConsValiCpe.htm?E='+ $('#rucclient').val() +'&T='+ aData['tp_description'] +'&R='+ aData['document'] +'&S='+ aData['serialnumber']  +'&N='+ aData['correlative'] +'&F='+aData['date']+'&T='+ aData['total'] +'" class="consultCPE" style="color: #28a745;" target="_blank">Verificar CPE en la SUNAT</a></li>';
                button += '<li><a href="http://www.sunat.gob.pe/ol-ti-itconsverixml/ConsVeriXml.htm" class="consultXML" style="color: #28a745;">Verificar XML en la SUNAT</a></li>';
                button += '</ul>';
                button += '</div>';
                $(nRow).find("td:eq(10)").html(button);
            }
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
                        scode = d['sunat_code'];
                    } else {
                        scode = '-';
                    }
                    data += '<td>Código</td><td>' +scode  + '</td>'
                data += '</tr>';
                data += '<tr>';
                    let sdescription = '';
                    if (d['description'] != null) {
                        sdescription = d['description'];
                    } else {
                        sdescription = '-';
                    }
                    data += '<td>Descripción</td><td>' + sdescription + '</td>'
                data += '</tr>';
            $('#tbl_status tbody').html('');
            $('#tbl_status tbody').append(data);
            $('#mdl_status').modal('show');
        });
        $('body').on('click', '.searchD', function(){
            let d = tbl_data2.row( $(this).parents('tr') ).data();
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
                        scode = d['sunat_code'];
                    } else {
                        scode = '-';
                    }
                    data += '<td>Código</td><td>' +scode  + '</td>'
                data += '</tr>';
                data += '<tr>';
                    let sdescription = '';
                    if (d['description'] != null) {
                        sdescription = d['description'];
                    } else {
                        sdescription = '-';
                    }
                    data += '<td>Descripción</td><td>' + sdescription + '</td>'
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
        $('body').on('click', '.send_client2', function() {
            let data = tbl_data2.row( $(this).parents('tr') ).data();
            if(data == undefined) {
                tbl_data = $('#tbl_data2').DataTable();
                data = tbl_data2.row( $(this).parents('tr') ).data();
            }

            let id = data['id'];

            $.confirm({
                icon: 'fa fa-question',
                theme: 'modern',
                animation: 'scale',
                title: '¿Está seguro de enviar esta nota de débito?',
                buttons: {
                    Confirmar: function () {
                        $.ajax({
                            type: 'post',
                            url: '/commercial.notes.debit.send/' + id,
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
                                        console.log(response.responseText);
toastr.error('Ocurrio un error');
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
            //let id = $(this).parent().parent().parent().parent().parent().parent().attr('id');
            let data = tbl_data.row( $(this).parents('tr') ).data();
            let id = data['id'];
            $('.btnOpen').attr('id', id);
            $('.btnSend').attr('id', id);
            $('.btnPrint').attr('id', id);
            $('#frame_pdf').attr('src', '/commercial.sale.note.pdf/' + data['id']);

            $('#mdl_preview').modal('show');

        });
        $('body').on('click', '.pdfD', function() {
            //let id = $(this).parent().parent().parent().parent().parent().parent().attr('id');
            let data = tbl_data2.row( $(this).parents('tr') ).data();
            let id = data['id'];
            $('.btnOpen').attr('id', id);
            $('.btnSend').attr('id', id);
            $('.btnPrint').attr('id', id);
            $('#frame_pdf').attr('src', '/commercial.sale.note.debit.pdf/' + data['id']);

            $('#mdl_preview').modal('show');

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
            file += data['tp_description'] + '-' + data['serial_number'] + '-' + data['correlative'];
            window.open(file, '_blank');
        });

        $('#tbl_data').on('click', '.cdr', function() {
            let data = tbl_data.row( $(this).parents('tr') ).data();
            if(data == undefined) {
                tbl_data = $('#tbl_data').DataTable();
                data = tbl_data.row( $(this).parents('tr') ).data();
            }

            let file = 'files/cdr/' + 'R-' + '{{Auth::user()->headquarter->client->document}}' + '-';
            file += data['tp_description'] + '-' + data['serial_number'] + '-' + data['correlative']  + '.zip';
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
