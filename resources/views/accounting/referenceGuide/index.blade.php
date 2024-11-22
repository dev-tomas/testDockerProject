@extends('layouts.azia')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-default text-center">
                <div class="card-header color-gray">
                    <div class="row">
                        <div class="col-12">
                            <h3 class="card-title">GUÍAS DE REMISIÓN</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-10">
                            <a href="{{ route('referenceguide.create') }}" class="btn btn-primary-custom pull-left">
                                NUEVA GUÍA DE REMISIÓN
                            </a>
                        </div>
                        <div class="col-12 col-md-2">
                            {{-- <a href="#" id=""  class="btnSale btn btn-primary ml-2 pull-right">
                                <i class="fa fa-download"></i>
                                Excel
                            </a> --}}
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
                                <th width="60">FECHA</th>
                                <th>T. DOC.</th>
                                <th>SERIE</th>
                                <th>NUM.</th>
                                <th>RUC/DNI/ETC</th>
                                <th>DENOMINACIÓN</th>
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
    @if(session()->has('id'))
        <input type="hidden" id="ncid" value="{{ session()->get('id') }}">
    @endif
    @if(session()->has('idd'))
        <input type="hidden" id="ndid" value="{{ session()->get('idd') }}">
    @endif

    <div class="modal fade" id="mdlCreditNote" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    {{-- <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5> --}}
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <h1 style="font-size: 1.5em !important;">NOTA DE CRÉDITO ELECTRÓNICA</h1>
                    <h3 class="font-weight-light mb-2 mt-2" style="font-size: 1.5em !important;" id="nc_correlative"></h3>

                    <fieldset class="mb-2">
                        <button class="btn btn-primary btn-block" id="printNC" data-id="">IMPRIMIR</button>
                    </fieldset>
                    <fieldset class="mb-2">
                        <div class="row">
                            <div class="col-md-6"><button class="btn btn-danger btn-block" id="showPDFNC" data-id="">VER PDF</button></div>
                            <div class="col-md-6"><button class="btn btn-success btn-block" id="">DESCARGAR XML</button></div>
                        </div>
                    </fieldset>
                    <fieldset class="mb-2">
                        <ul class="list-unstyled">
                            <li><a href="#" class="btn-link">Enviar a la SUNAT</a></li>
                            <li><a href="#" class="btn-link">Enviar email</a></li>
                            <li><a href="/commercial.sales.create/1" class="btn-link">Generar otra Factura de Venta</a></li>
                            <li><a href="/commercial.sales.create/2" class="btn-link">Generar otra Boleta de Venta</a></li>
                        </ul>
                    </fieldset>
                    <fieldset class="mb-2">
                        <button class="btn btn-danger btn-block" id="">ANULAR o comunicar baja</button>
                    </fieldset>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mdlDebitNote" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    {{-- <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5> --}}
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <h1 style="font-size: 1.5em !important;">NOTA DE DÉBITO ELECTRÓNICA</h1>
                    <h3 class="font-weight-light mb-2 mt-2" style="font-size: 1.5em !important;" id="nd_correlative"></h3>

                    <fieldset class="mb-2">
                        <button class="btn btn-primary btn-block" id="printND" data-id="">IMPRIMIR</button>
                    </fieldset>
                    <fieldset class="mb-2">
                        <div class="row">
                            <div class="col-md-6"><button class="btn btn-danger btn-block" id="showPDFND" data-id="">VER PDF</button></div>
                            <div class="col-md-6"><button class="btn btn-success btn-block" id="">DESCARGAR XML</button></div>
                        </div>
                    </fieldset>
                    <fieldset class="mb-2">
                        <ul class="list-unstyled">
                            <li><a href="#" class="btn-link">Enviar a la SUNAT</a></li>
                            <li><a href="#" class="btn-link">Enviar email</a></li>
                            <li><a href="/commercial.sales.create/1" class="btn-link">Generar otra Factura de Venta</a></li>
                            <li><a href="/commercial.sales.create/2" class="btn-link">Generar otra Boleta de Venta</a></li>
                        </ul>
                    </fieldset>
                    <fieldset class="mb-2">
                        <button class="btn btn-danger btn-block" id="">ANULAR o comunicar baja</button>
                    </fieldset>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
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
                            <button class="btn btn-primary btnPrint" id="">IMPRIMIR</button>
                            <button class="btn btn-primary btnOpen" id="0">Abrir en navegador</button>
                            <button class="btn btn-danger pull-right" id="btnClose">
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
            'lengthMenu': false,
            'language': {
                'url': '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json'
            },
            "order": [[ 6, "desc" ]],
            'searching': false,
            'processing': false,
            'serverSide': true,
            'ajax': {
                'url': '/reference-guide/dt',
                'type' : 'get',
                "data": function (d) {
                    d.denomination = $('#denomination').val();
                    d.serial = $('#document').val();
                    let rangeDates = $("#filter_date").val();
                    var arrayDates = rangeDates.split(" ");
                    var dateSpecificOne = arrayDates[0].split("/");
                    var dateSpecificTwo = arrayDates[2].split("/");

                    d.saleid = $('#idsale').val();
                    d.dateOne = dateSpecificOne[2] + "-" + dateSpecificOne[1] + "-" + dateSpecificOne[0];
                    d.dateTwo = dateSpecificTwo[2] + "-" + dateSpecificTwo[1] + "-" + dateSpecificTwo[0];
                }
            },
            'columns': [
                {
                    data: 'date',
                    // "width": "200px"
                },
                {
                    data: 'type_voucher.description',
                },
                {
                    data: 'serialnumber'
                },
                {
                    data: 'correlative'
                },
                {
                    data: 'receiver_document'
                },
                {
                    data: 'receiver'
                },
                {
                    data: 'id'
                },
                // {
                //     data: 'id'
                // },
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
                $(nRow).find('td:eq(6)').html('<button type="button" class="btn btn-danger-custom btn-sm pdf" id="' + aData['id'] + '" >PDF</button>');
                $(nRow).find('td:eq(7)').html('<button type="button" class="btn btn-secondary-custom btn-sm xml">XML</button>');

                if (aData["sunat_code"] != null) {
                    if (aData['has_cdr'] == 1) {
                        $(nRow).find("td:eq(8)").html("<button type=\"button\" class=\"btn btn-secondary-custom btn-sm cdr\">CDR</button>");
                    } else {
                        $(nRow).find("td:eq(8)").html("-");
                    }
                    if (aData["response_sunat"] == 1) {
                        $(nRow).find("td:eq(9)").html("<button type=\"button\" class=\"btn btn-success btn-sm search\">" + "<i class=\"fa fa-check\"></i>" + " Ver</button>");
                    } else if (aData["response_sunat"] <= 139 && aData['response_sunat'] >= 2) {
                        $(nRow).find("td:eq(9)").html("<button type=\"button\" class=\"btn btn-warning btn-sm search\">" + "<i class=\"fa fa-circle-o-notch\"></i>" + " Ver</button>");
                    } else if(aData["response_sunat"] > 139 ) {
                        $(nRow).find("td:eq(9)").html("<button type=\"button\" class=\"btn btn-orange btn-sm search\">" + "<i class=\"fa fa-close\"></i>" + " Ver</button>");
                    }
                } else {
                    $(nRow).find("td:eq(8)").html("<button class=\"btn btn-secondary\"><i class=\"fa fa-spinner fa-pulse\"></i></button>");
                    $(nRow).find("td:eq(9)").html("<span class=\"badge badge-secondary\">PENDIENTE</span>");
                }

                let button = '<div class="btn-group">';
                button += '<button type="button" class="btn btn-secondary-custom dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Opciones';
                button += '</button>';
                button += '<div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 38px, 0px); top: 0px; left: 0px; will-change: transform;">';
                // if (aData['status'] == 2) {
                // button += '<a class="dropdown-item" href="#" class="editSale" >Editar</a>';
                // }

                if(aData['low_communication_id'] === null) {
                    button += '<a class="dropdown-item cancelOrCancel" href="#" style="color: #ea1024;">Anular o Comunicar de Baja</a>';
                }

                button += '<a class="dropdown-item consulTSunat" href="#">Consultar Estado en Sunat</a>';

                button += '</div>';
                button += '</div>';
                $(nRow).find("td:eq(10)").html(button);
            }
        });

        $('body').on('click', '.pdf', function() {
            //let id = $(this).parent().parent().parent().parent().parent().parent().attr('id');
            let data = tbl_data.row( $(this).parents('tr') ).data();
            let id = $(this).attr('id');
            $('.btnOpen').attr('id', id);
            $('.btnSend').attr('id', id);
            $('.btnPrint').attr('id', id);
            $('#frame_pdf').attr('src', '/reference-guide/show/pdf/' + data['id']);

            $('#mdl_preview').modal('show');

        });

        $('body').on('click', '.cancelOrCancel', function() {
            let data = tbl_data.row( $(this).parents('tr') ).data();

            $.confirm({
                icon: 'fa fa-warning',
                theme: 'modern',
                animation: 'scale',
                type: 'orange',
                draggable: false,
                title: '¿Está seguro de anular el comprobante?',
                content: function() {
                    let value = '';

                    return '<div class="form-group">' +
                        '<label style="color: #000 !important;"><strong>Motivo</strong></label>' +
                        '<input type="text" id="motive" name="motive" class="form-control" value="' + value + '" />' +
                        '<option></option>' +
                        '</div>'
                },
                buttons: {
                    Confirmar: {
                        text: 'Confirmar',
                        btnClass: 'btn btn-green',
                        action: function() {
                            $.ajax({
                                type: 'post',
                                url: '/commercial/send/low/communication',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    id: data['id'],
                                    motive: $('#motive').val(),
                                    type: 4
                                },
                                dataType: 'json',
                                success: function(response) {
                                    if(response === true) {
                                        toastr.success('Se anuló satisfactoriamente el comprobante');
                                        toastr.success('El comprobante fue enviado a Sunat satisfactoriamente');
                                    } else if(response === '-1') {
                                        toastr.success('Se anuló satisfactoriamente el Comprobante');
                                        toastr.warning('Ocurrió un error con el comprobante, reviselo y vuelva a enviarlo.');toastr.warning('Debe de generar correlativos para nota de credito');
                                    } else if(response === '-2') {
                                        toastr.success('Se anuló satisfactoriamente el Comprobante');
                                        toastr.error('El comprobante fue enviado a Sunat y fue rechazado automáticamente, vuelva a enviarlo manualmente');
                                    } else if(response === '-3') {
                                        toastr.success('Se anuló satisfactoriamente el Comprobante');
                                        toastr.info('El comprobante fue enviado a Sunat y fue validado con una observación.');
                                    } else {
                                        toastr.success('Se anuló satisfactoriamente el Comprobante');
                                        toastr.error('Los servidores de la Sunat están teniendo problemas.');
                                    }

                                    tbl_data.ajax.reload();
                                },
                                error: function(response) {
                                    toastr.error(response.responseText);
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

        /**
         * Convert to Voucher
         */
        $('body').on('click', '.print', function() {
            var id = $(this).parent().parent().parent().parent().parent().parent().attr('id');
            window.open('/commercial.sales.print/');
        });

        /**
         *btnOpen
         **/
        $('body').on('click', '.btnOpen', function(){
            let id = $(this).attr('id');
            let src = $('#frame_pdf').attr('src');
            window.open(src + '_blank');

        });

        $('body').on('click', '#btnClose', function(){
            $('#mdl_preview').modal('hide');
        })

        /**
         *btnPrint
         **/
        $('body').on('click', '.btnPrint', function(){
            $("#frame_pdf").get(0).contentWindow.print();
        });

        $('#tbl_data').on('click', '.xml', function() {
            let data = tbl_data.row( $(this).parents('tr') ).data();
            if(data == undefined) {
                tbl_data = $('#tbl_data').DataTable();
                data = tbl_data.row( $(this).parents('tr') ).data();
            }

            let date = data['date'].split('-');

            let file = '/commercial.download.xml/' + '{{Auth::user()->headquarter->client->document}}' + '-';
            file += data['type_voucher']['code'] + '-' + data['serialnumber'] + '-' + data['correlative'];
            window.open(file, '_blank');
        });

        $('#tbl_data').on('click', '.cdr', function() {
            let data = tbl_data.row( $(this).parents('tr') ).data();
            if(data == undefined) {
                tbl_data = $('#tbl_data').DataTable();
                data = tbl_data.row( $(this).parents('tr') ).data();
            }

            let date = data['date'].split('-');

            let file = '/files/cdr/' + 'R-' + '{{Auth::user()->headquarter->client->document}}' + '-';
            file += data['type_voucher']['code'] + '-' + data['serialnumber'] + '-' + data['correlative'];
            window.open(file, '_blank');
        });

        $('body').on('click', '.search', function(){
            let d = tbl_data.row($(this).parents("tr")).data();
            // console.log(d.response_sunat);
            let icon = "<i class=\"fa fa-remove\"></i>";
            let icon_accept = "<i class=\"fa fa-remove\"></i>";
            if (d.status_sunat == 1) {
                icon = "<i class=\"fa fa-check\"></i>";
            }

            if (d.response_sunat == 1) {
                icon_accept = "<i class=\"fa fa-check\"></i>";
            }
            let scode = "";
            if (d["sunat_code"] != null) {
                scode = d["sunat_code"]["code"];
            } else {
                scode = "-";
            }
            let sdescription = "";
            if (d["sunat_code"]["description"] != null) {
                sdescription = d["sunat_code"]["description"];
            } else {
                sdescription = "-";
            }

            if (d["sunat_code"]["what_to_do"] != null) {
                s_what = d["sunat_code"]["what_to_do"];
            } else {
                s_what = "Nada";
            }
            let stype = "";
            if (d["sunat_code"]["detail"] != null) {
                stype = d["sunat_code"]["detail"] == 'NINGUNO' ? 'ACEPTADO' : d['sunat_code']['detail'];
            } else {
                stype = "-";
            }

            let ticket = '';
            if (d['ticket'] != null) {
                ticket = `
                <tr>
                    <td>Ticket Sunat</td>
                    <td>${d['ticket']}</td>
                </tr>
                `;
            }
            let receptionDate = '';
            if (d['reception_date']) {
                receptionDate = `
                <tr>
                    <td>Fecha de Recepción Sunat</td>
                    <td>${moment(d['reception_date']).format('DD-MM-YYYY H:m')}</td>
                </tr>
                `;
            }

            let data = `
                <tr>
                    <td>Enviada a la Sunat</td>
                    <td>${icon}</td>
                </tr>
                <tr>
                    <td>Aceptada por la Sunat</td>
                    <td>${icon_accept}</td>
                </tr>
                <tr>
                    <td>Código</td>
                    <td>${scode}</td>
                </tr>
                <tr>
                    <td>Descripción</td>
                    <td>${sdescription}</td>
                </tr>
                <tr>
                    <td>Tipo</td>
                    <td>${stype}</td>
                </tr>
                <tr>
                    <td>¿Qué hacer?</td>
                    <td>${s_what}</td>
                </tr>
                ${ticket}
                ${receptionDate}
            `;

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
                type: 'orange',
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
                                url: '/commercial/references/sunat/send/' + data['id'],
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

        $('#tbl_data').on('click', '.consulTSunat', function() {
            let data = tbl_data.row( $(this).parents('tr') ).data();
            $.confirm({
                icon: 'fa fa-warning',
                theme: 'modern',
                animation: 'scale',
                type: 'orange',
                draggable: false,
                title: '¿Está seguro de consultar esta Guía de Remisión?',
                content: '',
                buttons: {
                    Confirmar: {
                        text: 'Confirmar',
                        btnClass: 'btn btn-green',
                        action: function() {
                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                }
                            });
                            $.ajax({
                                type: 'get',
                                url: '/reference-guide/consult-api-sunat/' + data['uuid'],
                                dataType: 'json',
                                success: function(response) {
                                    if(response['response'] === true) {
                                        toastr.success(response['description']);
                                    } else {
                                        toastr.error('Ocurrió un error!');
                                    }

                                    tbl_data.ajax.reload();
                                },
                                error: function(response) {
                                    toastr.error('Los servidores de la Sunat no están disponibles, vuevla a intentarlo mas tarde');
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

        $('#denomination').keyup(function() {
            tbl_data.ajax.reload();
        });
        $('#document').keyup(function() {
            tbl_data.ajax.reload();
        });
        $('#filter_date').change(function() {
            tbl_data.ajax.reload();
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
    </script>
@stop
