@extends('layouts.azia')
@section('css')
    <style>
        .table-responsive,
        .dataTables_scrollBody {
            overflow : visible !important;
        }

        .table-responsive-disabled .dataTables_scrollBody {
            overflow : hidden !important;
        }
    </style>
@stop
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header color-gray">
                    <div class="row">
                        <div class="col-12">
                            <h3 class="card-title text-center">MONITOR DE ENVÍO DE COMPROBANTES</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <button class="btn btn-primary float-right" type="button" id="consultMassive"> Consultar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="">Tipo de  Documento</label>
                                <select id="document_type" class="form-control">
                                    <option value="">Todos los Documentos</option>
                                    <option value="1">FACTURA</option>
                                    <option value="2">BOLETA DE VENTA</option>
                                    <option value="22">RESUMEN DIARIO DE BOLETAS</option>
                                    <option value="19">COMUNICACION DE BAJA</option>
                                    <option value="3">NOTA DE CREDITO</option>
                                    <option value="5">NOTA DE DEBITO</option>
                                    <option value="7">GUIA DE REMISION</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="">Empresas</label>
                                <select id="company" class="form-control">
                                    <option value="">Todas las Empresas</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}">{{ $company->document }} - {{ $company->trade_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="">Filtro por Fechas</label>
                                <input type="text"
                                       id="filter_date"
                                       class="form-control"
                                       placeholder="Seleccionar fechas">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="table-responsive">
                            <table id="tbl_data"
                                   class="dt-bootstrap4 table-hover"
                                   style="width: 100%;">
                                <thead>
                                    <th width="60px">FECHA</th>
                                    <th width="400px">CLIENTE</th>
                                    <th width="200px">T. DOC.</th>
                                    <th width="60px">SERIE</th>
                                    <th width="60px">NUM.</th>
                                    <th width='400px'>ENTIDAD</th>
                                    <th width="60px">M.</th>
                                    <th width='50px'>TOTAL</th>
                                    <th width='70px'>RESPUESTA SUNAT/OSE</th>
                                    <th width='70px'>ESTADO SUNAT</th>
                                    <th width='70px'>*</th>
                                    <th width='60px'>*</th>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="mdl_status"
         class="modal fade bd-example-modal-lg"
         tabindex="-1"
         role="dialog"
         aria-labelledby="myLargeModalLabel"
         aria-hidden="true"
         style="z-index: 9999;">
        <div class="modal-dialog modal-xs">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <table class="table"
                                   id="tbl_status">
                                <tbody></tbody>
                            </table>
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
                            <button class="btn btn-primary btnOpen" id="0">Abrir en navegador</button>
                            <button class="btn btn-danger pull-right" id="btnClose"> <i class="fa fa-close"></i></button>
                        </div>
                        <div class="col-12">
                            <iframe frameborder="0" width="100%;" height="700px;" id="frame_pdf"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="consultMassiveModal" tabindex="-1" aria-labelledby="consultMassiveModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="consultMassiveModalLabel">Consultar</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>Fecha</label>
                                <input type="text" class="form-control" name="date_consult" id="date_consult" autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" id="consult-invoices-btn" class="btn btn-primary">Consultar Comprobantes</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('script_admin')
    <script>
        var base = $("#base_urlx").val();
        let tbl_data = $("#tbl_data").DataTable({
            "pageLength": 15,
            "bLengthChange": false,
            "lengthMenu": false,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            },
            "order": [
                [
                    0,
                    "desc"
                ]
            ],
            "searching": false,
            "processing": false,
            "serverSide": true,
            "ajax": {
                "url": '/manage/monitor/dt',
                "type": "get",
                "data": function (d) {
                    let rangeDates = $("#filter_date").val();
                    var arrayDates = rangeDates.split(" ");
                    var dateSpecificOne = arrayDates[0].split("/");
                    var dateSpecificTwo = arrayDates[2].split("/");

                    d.dateOne = dateSpecificOne[2] + "-" + dateSpecificOne[1] + "-" + dateSpecificOne[0];
                    d.dateTwo = dateSpecificTwo[2] + "-" + dateSpecificTwo[1] + "-" + dateSpecificTwo[0];
                    d.document_type = $('#document_type').val();
                    d.company = $('#company').val();
                }
            },
            "columns": [
                {
                    data: "date"
                },
                {
                    data: "client"
                },
                {
                    data: "voucher"
                },
                {
                    data: "serialnumber"
                },
                {
                    data: "correlative"
                },
                {
                    data: "customer"
                },
                {
                    data: "coin"
                },
                {
                    data: "total"
                },
                {
                    data: "id"
                },
                {
                    data: "cpe_status"
                },
                {
                    data: "invoice_status"
                },
                {
                    data: "id"
                },
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                $(nRow).find("td:eq(0)").html(moment(aData['date']).format('DD-MM-YYYY'));

                let textButton = aData['sunat_code'] != null ? aData['sunat_code']['code'] : 'Ver';
                if(aData['response_sunat'] == 90 || aData["response_sunat"] > 139) {
                    $(nRow).find("td:eq(8)").html(`<button type='button' class='btn btn-danger btn-sm search'><i class='fa fa-close'></i> ${textButton}</button>`);
                } else if (aData["response_sunat"] <= 139 && aData['response_sunat'] >= 2) {
                    $(nRow).find("td:eq(8)").html(`<button type='button' class='btn btn-warning btn-sm search'><i class='fa fa-circle-o-notch'></i> ${textButton}</button>`);
                }

                if (aData['response_sunat'] == null || aData['status_sunat'] == null) {
                    $(nRow).find("td:eq(8)").html("<button class='btn btn-secondary'><i class='fa fa-spinner fa-pulse'></i> Pendiente</button>");
                }

                let status = '-';
                let invoiceStatus = '-';

                if (aData["typevoucher"] == 1 || aData["typevoucher"] == 2) {
                    switch (aData['cpe_status']) {
                        case '0': status = 'NO EXISTE'; break;
                        case '1': status = 'ACEPTADO'; break;
                        case '2': status = 'ANULADO'; break;
                        case '3': status = 'AUTORIZADO'; break;
                        case '4': status = 'NO AUTORIZADO'; break;
                    }

                    if (aData['invoice_status'] == 9) {
                        invoiceStatus = 'COINCIDE';
                    } else if (aData['invoice_status'] == 2) {
                        invoiceStatus = 'NO ANULADO';
                    } else if (aData['invoice_status'] == 0) {
                        invoiceStatus = 'NO EXISTE';
                    }
                }

                $(nRow).find("td:eq(9)").html(status);
                $(nRow).find("td:eq(10)").html(invoiceStatus);

                let button = "<div class='btn-group'>";
                button += "<button type='button' class='btn btn-secondary dropdown-toggle dropdown-button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'> Opciones";
                button += "</button>";
                button += "<div class='dropdown-menu' x-placement='bottom-start' style='position: absolute; transform: translate3d(-56px, 33px, 0px); top: 0px; left: 0px; will-change: transform; right: 0px; width: 200px;'>";

                if (aData["typevoucher"] == 1 || aData["typevoucher"] == 2) {
                    button += "<div class='dropdown-divider'></div>";
                    button += "<a class='dropdown-item sendSunat' href='#'  style='color: #ea1024;'>Enviar a la SUNAT</a>";
                    button += "<div class='dropdown-divider'></div>";
                    button += "<a class='dropdown-item consultCDR' href='#'  style='color: #ea1024;'>Consultar CDR</a>";
                    button += "<div class='dropdown-divider'></div>";
                    button += "<a class='dropdown-item consultState text-success' href='#'>Consultar Estado en SUNAT</a>";
                }

                if (aData["typevoucher"] == 4 || aData["typevoucher"] == 3) {
                    button += "<div class='dropdown-divider'></div>";
                    button += "<a class='dropdown-item consultCDR' href='#'  style='color: #ea1024;'>Consultar CDR</a>";
                }

                if (aData["typevoucher"] == 7 || aData["typevoucher"] == 11) {
                    button += "<div class='dropdown-divider'></div>";
                    button += '<a class="dropdown-item consulTSunat" href="#">Consultar Estado en Sunat</a>';
                }

                button += "<div class='dropdown-divider'></div>";


                button += "</div>";
                button += "</div>";

                $(nRow).find("td:eq(11)").html(button);
            },
            drawCallback: function () {
            }
        });

        $("#tbl_data").on("click", ".sendSunat", function () {
            let data = tbl_data.row($(this).parents("tr")).data();
            $.confirm({
                icon: "fa fa-warning",
                theme: "modern",
                animation: "scale",
                type: "orange",
                draggable: false,
                title: "¿Está seguro de enviar este comprobante?",
                content: "",
                buttons: {
                    Confirmar: {
                        text: "Confirmar",
                        btnClass: "btn btn-green",
                        action: function () {
                            $.ajax({
                                type: "post",
                                url: "/commercial.sales.sunat.send/" + data["id"] + "/5",
                                data: {
                                    _token: '{{csrf_token()}}'
                                },
                                dataType: "json",
                                success: function (response) {
                                    if (response["response"] === true) {
                                        toastr.success("Se envió satisfactoriamente el comprobante!");
                                        tbl_data.ajax.reload();
                                    }else if (response["response"] == false) {
                                        toastr.error('El comprobante esta Rechazado');
                                    } else {
                                        toastr.error("Ocurrió un error!");
                                    }
                                    tbl_data.ajax.reload();
                                },
                                error: function (response) {
                                    toastr.error("Los servidores de la Sunat no están disponibles, vuevla a intentarlo mas tarde");
                                    console.log(response.responseText);
                                }
                            });
                        }
                    },
                    Cancelar: {
                        text: "Cancelar",
                        btnClass: "btn btn-red"
                    }
                }
            });
        });
        $("#tbl_data").on("click", ".consultCDR", function () {
            let data = tbl_data.row($(this).parents("tr")).data();
            let type = data['typevoucher'] == 1 || data['typevoucher'] == 2 ? 1 : 2;
            $.confirm({
                icon: "fa fa-warning",
                theme: "modern",
                animation: "scale",
                type: "orange",
                draggable: false,
                title: "¿Está seguro de consultar el CDR para est Comprobante?",
                content: "",
                buttons: {
                    Confirmar: {
                        text: "Confirmar",
                        btnClass: "btn btn-green",
                        action: function () {
                            $.ajax({
                                type: "post",
                                url: `/commercial.sales.consult.cdr/${data["id"]}/${type}/${data['client_id']}`,
                                data: {
                                    _token: '{{csrf_token()}}'
                                },
                                dataType: "json",
                                success: function (response) {
                                    if (response["status"] == true) {
                                        toastr.success(response["message"]);
                                        tbl_data.ajax.reload();
                                    }else if (response["status"] == false) {
                                        toastr.error(response["message"]);
                                    } else {
                                        toastr.error("Ocurrió un error!");
                                    }
                                },
                                error: function (response) {
                                    toastr.error("Los servidores de la Sunat no están disponibles, vuevla a intentarlo mas tarde");
                                    console.log(response.responseText);
                                }
                            });
                        }
                    },
                    Cancelar: {
                        text: "Cancelar",
                        btnClass: "btn btn-red"
                    }
                }
            });
        });

        $("#tbl_data").on("click", ".consultState", function () {
            let data = tbl_data.row($(this).parents("tr")).data();
            $.ajax({
                type: "post",
                url: `/manage/monitor/consutl-sunat-status/${data["id"]}`,
                data: {
                    _token: '{{csrf_token()}}'
                },
                dataType: "json",
                success: function (response) {
                    if (response["success"] == true) {
                        toastr.success(response["message"]);
                        tbl_data.ajax.reload();
                    }else if (response["success"] == false) {
                        toastr.error(response["message"]);
                    } else {
                        toastr.error("Ocurrió un error!");
                    }
                },
                error: function (response) {
                    toastr.error("Los servidores de la Sunat no están disponibles, vuevla a intentarlo mas tarde");
                    console.log(response.responseText);
                }
            });
        });

        $("body").on("click", ".search", function () {
            let d = tbl_data.row($(this).parents("tr")).data();

            let icon = "<i class='fa fa-remove'></i>";
            let icon_accept = "<i class='fa fa-remove'></i>";
            if (d.status_sunat == 1) {
                icon = "<i class='fa fa-check'></i>";
            }

            if (d.response_sunat == 1) {
                icon_accept = "<i class='fa fa-check'></i>";
            }

            let data = "<tr>";
            data += "<td>Enviada a la Sunat</td><td>" + icon + "</td>";
            data += "</tr>";
            data += "<tr>";
            data += "<td>Aceptada por la Sunat</td><td>" + icon_accept + "</td>";
            data += "</tr>";
            data += "<tr>";
            let scode = "";
            if (d["sunat_code"] != null) {
                scode = d["sunat_code"]["code"];
            } else {
                scode = "-";
            }
            data += "<td>Código</td><td>" + scode + "</td>";
            data += "</tr>";
            data += "<tr>";
            let sdescription = "";
            if (d["sunat_code"]["description"] != null) {
                sdescription = d["sunat_code"]["description"];
            } else {
                sdescription = "-";
            }
            data += "<td>Descripción</td><td>" + sdescription + "</td>";
            data += "</tr>";
            data += "<tr>";
            let s_what = "";
            if (d["sunat_code"]["what_to_do"] != null) {
                s_what = d["sunat_code"]["what_to_do"];
            } else {
                s_what = "Nada";
            }
            data += "<td>¿Que hacer?</td><td>" + s_what + "</td>";
            data += "</tr>";
            $("#tbl_status tbody").html("");
            $("#tbl_status tbody").append(data);
            $("#mdl_status").modal("show");
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

        });

        $('#company').select2();

        $("#filter_date").change(function () {
            tbl_data.ajax.reload();
        });
        $("#company").change(function () {
            tbl_data.ajax.reload();
        });

        $("#document_type").on("change", function () {
            tbl_data.ajax.reload();
        });

        $('#generateExcel').click(function(e) {
            e.preventDefault();
            let date = $("#filter_date").val();
            let client = $('#company').val();

            if (date == '') {
                toastr.warning('Debe de seleccionar una fecha')

                return false;
            }

            window.open(`/manage/invoice-status/excel?date=${date}&client=${client}`, "_blank");
        });

        $("body").on("click", "#btnClose", function () {
            $("#mdl_preview").modal("hide");
        });


        /**
         * view pdf
         */
        $("body").on("click", ".pdf", function () {
            let data = $(this).data('link');

            let id = $(this).attr("id");
            $(".btnOpen").data("link", data);
            $("#frame_pdf").attr("src", data);

            $("#mdl_preview").modal("show");

        });
        $("body").on("click", ".btnOpen", function () {
            let data = $(this).data('link');
            window.open(data + "_blank");
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
                                        toastr.warning(response['description']);
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

        $('#consultMassive').click(function(e) {
            e.preventDefault();
            $("#date_consult").val(moment().format('DD-MM-YYYY'));

            $('#consultMassiveModal').modal('show');
        })

        $('#consultMassiveModal').on('hidden.bs.modal', function (event) {
            $("#consult-invoices-btn").attr("disabled", false);
            $("#consult-guides-btn").attr("disabled", false);
        })

        $('#consult-invoices-btn').click(function(e) {
            e.preventDefault();
            let date = moment($('#date_consult').val(), 'DD-MM-YYYY').format('YYYY-MM-DD');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                }
            });
            $.ajax({
                type: 'get',
                url: `/manage/consult-integration-invoice/${date}`,
                dataType: 'json',
                beforeSend: function () {
                    $("#consult-invoices-btn").attr("disabled", true);
                },
                success: function(response) {
                    $('#page-loader').hide();
                    toastr.success('Consulta realizada correctamente.');
                    tbl_data.ajax.reload();
                    $('#consultMassiveModal').modal('hide');
                },
                error: function(response) {
                    $('#page-loader').hide();
                    toastr.error('Los servidores de la Sunat no están disponibles, vuevla a intentarlo mas tarde');
                }
            });
        })

        $("#date_consult").datepicker({
            format: "dd-mm-yyyy",
            autoclose: true,
            language: "es"
        });
    </script>
@stop
