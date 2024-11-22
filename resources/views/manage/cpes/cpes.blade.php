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
            <div class="card card-default text-center">
                <div class="card-header color-gray">
                    <div class="row">
                        <div class="col-12">
                            <h3 class="card-title">COMPROBANTES</h3>
                        </div>
                    </div>
                    <div class="row">
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-3">
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
                        <div class="col-12 col-md-2">
                            <div class="form-group">
                                <label for="">Estado SUNAT</label>
                                <select id="cpe_status" class="form-control">
                                    <option value="">Todos los Estados</option>
                                    <option value="0">No Existe</option>
                                    <option value="1">Aceptado</option>
                                    <option value="2">Anulado</option>
                                    <option value="3">Autorizado</option>
                                    <option value="4">No Autorizado</option>
                                    <option value="5">No Consultado</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-2 col-xl-1">
                            <div class="form-group">
                                <label for="">Coincide</label>
                                <select id="invoice_status" class="form-control">
                                    <option value="">Todos</option>
                                    <option value="9">Coincide</option>
                                    <option value="2" selected>No Coincide</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-2">
                            <div class="form-group">
                                <label for="">Buscar Documento</label>
                                <input type="text" id="document" class="form-control" placeholder="Ingresar documento">
                            </div>
                        </div>
                        <div class="col-12 col-md-2">
                            <div class="form-group">
                                <label for="">Filtro por Fecha</label>
                                <input type="text" id="date" class="form-control" placeholder="Seleccionar fecha" value="{{ date('d-m-Y') }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-1">
                            <div class="form-group">
                                <br>
                                <label><input type="checkbox" class="mt-3" id="only-low" value="1"> Solo Anulados</label>
                                <input type="hidden" id="lows" value="0">
                            </div>
                        </div>
                        <div class="col-12 col-md-1">
                            <div class="form-group">
                                <br>
                                <button class="btn btn-primary" type="button" id="consultDate">Consultar</button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="table-responsive">
                            <table id="tbl_data" class="dt-bootstrap4 table-hover" style="width: 100%;">
                                <thead>
                                    <th width="60">FECHA E</th>
                                    <th width='50px'>FECHA C</th>
                                    <th>CLIENTE</th>
                                    <th>T. DOC.</th>
                                    <th>SERIE</th>
                                    <th>NUM.</th>
                                    <th>ENTIDAD</th>
                                    <th>CDR</th>
                                    <th>ESTADO BAUTIFAK</th>
                                    <th>ESTADO SUNAT</th>
                                    <th>COINCIDE</th>
                                    <th>*</th>
                                </thead>
                                <tbody></tbody>
                            </table>
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
@stop
@section('script_admin')
    <script>
        let tbl_data = $("#tbl_data").DataTable({
            "pageLength": 15,
            "bLengthChange": false,
            "lengthMenu": false,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            },
            "order": [
                [
                    8,
                    "desc"
                ]
            ],
            "searching": false,
            "processing": false,
            "serverSide": true,
            "ajax": {
                "url": "/manage/cpe/dt",
                "type": "get",
                "data": function (d) {
                    d.cpe = $("#cpe_status").val();
                    d.invoice = $("#invoice_status").val();
                    d.serial = $("#document").val();
                    d.companies = $("#company").val();

                    d.date = $("#date").val();
                    d.onlylows = $('#lows').val()
                }
            },
            "columns": [
                {
                    data: "date"
                },
                {
                    data: "date"
                },
                {
                    data: "client.trade_name"
                },
                {
                    data: "type_voucher.description"
                },
                {
                    data: "serialnumber"
                },
                {
                    data: "correlative"
                },
                {
                    data: "customer.description"
                },
                {
                    data: "id"
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
                }
            ],
            "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                $(nRow).find("td:eq(1)").html(moment(aData['created_at']).format('DD-MM-YYYY HH:mm'));

                if (aData['response_sunat'] >= 139) {
                    $(nRow).css({
                        "text-decoration": "line-through",
                        "text-decoration-color": "red",
                        "color": "red"
                    });
                }

                if (aData["low_communication_id"] !== null) {
                    $(nRow).css({
                        "text-decoration": "line-through",
                        "text-decoration-color": "gray",
                        "color": "gray"
                    });
                }
                if (aData["typevoucher_id"] == 2) {
                    if (aData["status"] == 0) {
                        $(nRow).css({
                            "text-decoration": "line-through",
                            "text-decoration-color": "gray",
                            "color": "gray"
                        });
                    }
                }
                // if (aData["credit_note_id"] !== null) {
                //     if (aData["credit_note"]["sunat_code"]) {
                //         if (aData["credit_note"]["sunat_code"]["code"] === "0") {
                //             $(nRow).css({
                //                 "text-decoration": "line-through",
                //                 "text-decoration-color": "red",
                //                 "color": "white"
                //             });
                //         }
                //     }
                // }

                let cpeStatus = ''
                switch (aData['cpe_status']) {
                    case '0': cpeStatus = 'NO EXISTE'; break;
                    case '1': cpeStatus = 'ACEPTADO'; break;
                    case '2': cpeStatus = 'ANULADO'; break;
                    case '3': cpeStatus = 'AUTORIZADO'; break;
                    case '4': cpeStatus = 'NO AUTORIZADO'; break;
                    case null: cpeStatus = 'NO CONSULTADO'; break;
                }

                $(nRow).find("td:eq(9)").html(cpeStatus);

                let invoiceStatus = ''
                if (aData['invoice_status'] == 9) {
                    invoiceStatus = 'COINCIDE';
                } else if (aData['invoice_status'] == null) {
                    invoiceStatus = 'NO CONSULTADO'
                } else {
                    invoiceStatus = 'NO COINCIDE';
                }

                $(nRow).find("td:eq(10)").html(invoiceStatus);

                $(nRow).find("td:eq(6)").html(aData['total'].toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' '));
                $(nRow).find("td:eq(0)").html(moment(aData['issue']).format('DD-MM-YYYY'));
             
                if (aData["status"] == 1 || aData['status'] == 0) {
                    if (aData["status_sunat"] == 0 && aData["response_sunat"] == 1) {
                        $(nRow).find("td:eq(8)").html("<button type='button' class='btn btn-danger btn-sm search'>" + "<i class='fa fa-warning'></i>" + " Verificar</button>");
                        $(nRow).find("td:eq(7)").html("<button type='button' class='btn btn-secondary btn-sm cdr'>CDR</button>");
                    } else if (aData["status_sunat"] == 0 && aData["response_sunat"] == null) {
                        $(nRow).find("td:eq(7)").html("<i class='fa fa-spinner fa-pulse'></i>");
                        $(nRow).find("td:eq(8)").html("<span class='badge badge-secondary'><i class='fa fa-spinner fa-pulse'></i>Pendiente</span>");
                    } else if (aData["status_sunat"] == 0 && aData["response_sunat"] == null) {
                        $(nRow).find("td:eq(7)").html("<button type='button' class='btn btn-secondary btn-sm cdr'>CDR</button>");
                        $(nRow).find("td:eq(8)").html("<button type='button' class='btn btn-success btn-sm search'>" + "<i class='fa fa-check'></i>" + " Ver</button>");
                    }

                    if (aData["sunat_code"] != null) {
                        if (aData['cdr_status'] == 1) {
                            $(nRow).find("td:eq(7)").html("<button type='button' class='btn btn-secondary btn-sm cdr'>CDR</button>");
                        } else {
                            $(nRow).find("td:eq(7)").html("");
                        }
                        if (aData["response_sunat"] == 1) {
                            if (aData['cpe_status'] != null && aData['invoice_status'] != null) {
                                if (aData['cpe_status'] != 2) {
                                    $(nRow).find("td:eq(8)").html("<button type='button' class='btn btn-success btn-sm search'>" + "<i class='fa fa-check'></i>" + " Ver</button>");    
                                } else {
                                    $(nRow).find("td:eq(8)").html("<button type='button' class='btn btn-secondary btn-sm search'>" + "<i class='fa fa-check'></i>" + " Ver</button>");    
                                }
                            } else {
                                $(nRow).find("td:eq(8)").html("<button type='button' class='btn btn-success btn-sm search'>" + "<i class='fa fa-check'></i>" + " Ver</button>");
                            }
                        } else if (aData["response_sunat"] <= 139 && aData['response_sunat'] >= 2) {
                            $(nRow).find("td:eq(8)").html("<button type='button' class='btn btn-warning btn-sm search'>" + "<i class='fa fa-circle-o-notch'></i>" + " Ver</button>");
                        } else if(aData["response_sunat"] > 139 ) {
                            $(nRow).find("td:eq(8)").html("<button type='button' class='btn btn-orange btn-sm search'>" + "<i class='fa fa-close'></i>" + " Ver</button>");
                        } else {
                            $(nRow).find("td:eq(8)").html("<span class='badge badge-secondary popoverStatus'>PENDIENTE</span>");
                        }
                    }
                } else if (aData["status"] == 2) {
                    $(nRow).find("td:eq(7)").html("");
                    $(nRow).find("td:eq(8)").html("<span class='badge badge-info'></i> Borrador</span>");
                }
                
                let button = "<div class='btn-group'>";
                button += "<button type='button' class='btn btn-secondary dropdown-toggle dropdown-button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'> Opciones";
                button += "</button>";
                button += "<div class='dropdown-menu' x-placement='bottom-start' style='position: absolute; transform: translate3d(-56px, 33px, 0px); top: 0px; left: 0px; will-change: transform; right: 0px; width: 200px;'>";
                    button += "<a class='dropdown-item consultState text-success' href='#'>Consultar Estado en Sunat</a>";
                if (aData["admin_status"] == 1) {
                    if (aData["status"] == 1) { 
                        if (aData["sunat_code"] == null || aData["sunat_code"]["code"] > 0 && aData["sunat_code"]["code"] <= 1080) {
                            button += "<div class='dropdown-divider'></div>";
                            button += "<a class='dropdown-item sendSunat' href='#'  style='color: #ea1024;'>Enviar a la SUNAT</a>";
                            
                        }
                    }

                    button += "<div class='dropdown-divider'></div>";
                    button += "<a class='dropdown-item consultState text-success' href='#'>Consultar Estado en Sunat</a>";


                    button += "<div class='dropdown-divider'></div>";
                    button += "<a class='dropdown-item consultCPE' href='https://ww1.sunat.gob.pe/ol-ti-itconsultaunificadalibre/consultaUnificadaLibre/consulta' style='color: #28a745;' target='_blank'>Verificar CPE en la SUNAT</a>";
                    button += "<div class='dropdown-divider'></div>";
                }

                button += "</div>";
                button += "</div>";

                if (aData["admin_status"] == 0) {
                    $(nRow).css({
                        "text-decoration": "line-through",
                        "text-decoration-color": "gray",
                        "color": "gray"
                    });

                    $(nRow).find("td:eq(7)").html("-");
                }

                $(nRow).find("td:eq(11)").html(button);
            },
            drawCallback: function () {
            }
        });

        $('#only-low').change(function() {
            if ($(this).is(":checked")) {
                $('#lows').val(1)
                tbl_data.ajax.reload();
            } else {
                $('#lows').val(0)
                tbl_data.ajax.reload();
            }
        });

        $("body").on("click", ".search", function () {
            let d = tbl_data.row($(this).parents("tr")).data();
            // console.log(d.response_sunat);
            let icon = "<i class='fa fa-remove'></i>";
            let icon_accept = "<i class='fa fa-remove'></i>";
            if (d.status_sunat == 1) {
                icon = "<i class='fa fa-check'></i>";
            }

            if (d.response_sunat == 1) {
                icon_accept = "<i class='fa fa-check'></i>";
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

            if (d['cpe_status'] != null && d['invoice_status'] != null) {
                switch (d['cpe_status']) {
                    case '0': stype = 'NO EXISTE'; break;
                    case '1': stype = 'ACEPTADO'; break;
                    case '2': stype = 'ANULADO'; break;
                    case '3': stype = 'AUTORIZADO'; break;
                    case '4': stype = 'NO AUTORIZADO'; break;
                }
            } else {
                if (d["sunat_code"]["detail"] != null) {
                    stype = d["sunat_code"]["detail"] == 'NINGUNO' ? 'ACEPTADO' : d['sunat_code']['detail'];
                } else {
                    stype = "-";
                }
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
            `;

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
            // console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
        });


        $("#date").change(function () {
            tbl_data.ajax.reload();
        });

        $("#invoice_status").on("change", function () {
            tbl_data.ajax.reload();
        });
        
        $("#cpe_status").on("change", function () {
            tbl_data.ajax.reload();
        });

        $("#document").on("keyup", function () {
            tbl_data.ajax.reload();
        });
        
        $("#company").on("change", function () {
            tbl_data.ajax.reload();
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
                                url: "/commercial.sales.sunat.send/" + data["id"] + "/5/" + data['client_id'],
                                data: {
                                    _token: '{{csrf_token()}}'
                                },
                                dataType: "json",
                                success: function (response) {
                                    if (response["response"] === true) {
                                        toastr.success("Se envió satisfactoriamente el comprobante!");
                                        tbl_data.ajax.reload();
                                    }else if (response["response"] == false) {
                                        toastr.error(response["description"]);
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
        $("#tbl_data").on("click", ".consultCDR", function () {
            let data = tbl_data.row($(this).parents("tr")).data();
            let type = data['typevoucher_id'] == 1 || data['typevoucher_id'] == 2 ? 1 : 2;
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

        $('#consultDate').click(function(e) {
            e.preventDefault();

            let date = $('#date').val();

            if (date != null || date != '') {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: 'post',
                    url: '/manage/monitor/consutl-sunat-status/by-date/' + date,
                    dataType: 'json',
                    success: function(response) {
                        if(response == true) {
                            toastr.success('La consulta puede tomar tiempo. Recomiendo realizar una consulta a la vez.')
                        } else if(response == false) {
                            toastr.warning('Actualmente hay un proceso ejecutandose. Intentalo más tarde.')
                        } else {
                            toastr.warning('Actualmente hay un proceso ejecutandose. Intentalo más tarde.')
                        }
                },
                    error: function(response) {
                        toastr.error(response.responseText);
                    }
                });
            }
        })

        $('#company').select2();

        $("#date").datepicker({
            format: "dd-mm-yyyy",
            autoclose: true,
            language: "es"
        });
    </script>
@stop
