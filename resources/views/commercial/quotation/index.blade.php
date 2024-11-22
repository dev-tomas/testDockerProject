@extends('layouts.azia')
@section('css')
    <style>.edit,.delete,.convert,.send {display: none;}</style>
    @can('cotizaciones.edit')
        <style>.edit{display: block;}</style>
    @endcan
    @can('cotizaciones.delete')
        <style>.delete{display: block;}</style>
    @endcan
    @can('cotizaciones.convert')
        <style>.convert{display: block;}</style>
    @endcan
    @can('cotizaciones.send')
        <style>.send{display: block;}</style>
    @endcan
    <style>
    .table-responsive,
.dataTables_scrollBody {
    overflow: visible !important;
}

.table-responsive-disabled .dataTables_scrollBody {
    overflow: hidden !important;
}</style>
@stop
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card text-center">
                <div class="card-header color-gray">
                    <div class="row">
                        <div class="col-12">
                            <h3 class="card-title">COTIZACIONES</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            @can('cotizaciones.create')
                                <button type="button" id="btnQuotation" class="btn btn-primary-custom pull-left">
                                    Nueva Cotización
                                </button>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="">Buscar Cotización</label>
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
                                    <th width="70">NUM.</th>
                                    <th> RUC/DNI/ETC </th>
                                    <th>DENOMINACIÓN</th>
                                    <th>M.</th>
                                    <th>TOTAL ONEROSA</th>
                                    <th>ENVIADO AL CLIENTE</th>
                                    <th>PDF</th>
                                    <th>CPE RELACIONADO</th>
                                    <th>OPCIONES</th>
                                </thead>
                                <tfoot>
                                    <tr style="background: #f7f7f7">
                                        <td colspan="9">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="row py-2">
                                                        <div class="col-6 text-right"><strong>Total Cotizaciones</strong></div>
                                                        <div class="col-4"><span id="totalCot"></span></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">

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
                            <button class="btn btn-secondary-custom btnOpen" id="0">Abrir en navegador</button>

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
            'pageLength' : 15,
            'bLengthChange' : false,
            'lengthMenu': false,
            'language': {
                'url': '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json'
            },
            "order": [[ 1, "desc" ]],
            'searching': false,
            'processing': false,
            'serverSide': true,
            'ajax': {
                'url': '/commercial.dt.quotations',
                'type' : 'get',
                'data': function(d) {
                    d.denomination = $('#denomination').val();
                    d.document = $('#document').val();

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
                    data: 'correlative',
                    render: function(data, type, row) {
                        return row.serial_number + '-' + row.correlative;
                    }
                },
                {
                    data: 'document'
                },
                {
                    data: 'c_description'
                },
                {
                    data: 'symbol'
                },
                {
                    data: 'total'
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

                var fecha = aData['date'].split("-");

                $(nRow).find("td:eq(0)").html(fecha[2]+'-'+fecha[1]+'-'+fecha[0]);
                switch (aData['sendemail']) {
                    case 1:
                        $(nRow).attr('id', aData['id']);
                        $(nRow).find('td:eq(6)').html('<span class="badge bg-green-custom"><i class="fa fa-check"></i></span>');
                        break;
                    case 0:
                        $(nRow).attr('id', aData['id']);
                        $(nRow).find('td:eq(6)').html('<span class="badge bg-danger"><i class="fa fa-close"></i></span>');
                        break;
                }


                let button = '<div class="btn-group">';
                    button += '<button type="button" class="btn btn-secondary-custom dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Opciones';
                    button += '</button>';
                    button += '<div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(-56px, 33px, 0px); top: 0px; left: 0px; will-change: transform; right: 0px; width: 200px;">';
                    button += '<a class="dropdown-item send" href="#">Enviar al cliente</a>';
                    if( aData['sale_id'] == null ){
                        button += '<a class="dropdown-item convert" href="#">Convertir a comprobante</a>';
                        button += '<a class="dropdown-item edit" href="#">Editar</a>';
                        button += '<a class="dropdown-item delete" href="#" style="color: red;">Borrar</a>';
                    }
                    button += '</div>';
                    button += '</div>';
                $(nRow).find("td:eq(9)").html(button);

                if( aData['sale_id'] != null ){
                    $(nRow).find("td:eq(8)").html("<a href='/commercial.sales?idQuotation="+aData['id']+"' class='btn-primary-custom btn-rounded btn-sm'> "+aData['serialnumber'] + '-' + aData['salesCorrelative']+" </a>");
                }else{
                    $(nRow).find("td:eq(8)").html("");
                }

                $(nRow).find("td:eq(7)").html('<button type="button" class="btn btn-rounded btn-danger-custom btn-sm print">PDF</button>');
                // if(aData['stateProduction'] == 0){
                //     $(nRow).addClass("table-danger");
                // }
            },
            drawCallback: function () {
            }
        });
        $(document).ready(function() {
            getTotalSales()
        })

        /**
         * Download Pdf
         **/
        $('body').on('click', '.btnPdf', function() {
            let id = $(this).parent().parent().attr('id');
            window.open('/commercial.quotations.download.pdf/' + id);
        });

        /**
         * Print
         */
        $('body').on('click', '.print', function() {
            //let id = $(this).parent().parent().parent().parent().parent().parent().attr('id');
            let id = $(this).parent().parent().attr('id');
            $('.btnOpen').attr('id', id);
            $('.btnSend').attr('id', id);
            $('.btnPrint').attr('id', id);
            $('#frame_pdf').attr('src', '/commercial.quotations.show.pdf/' + id);

            $('#mdl_preview').modal('show');
        });


        /**
         * Edit quotations
         */
        $('body').on('click', '.edit', function() {
            let data = tbl_data.row( $(this).parents('tr') ).data();
            window.location.href = '/commercial.quotations.edit/'+data['id'];
        });

        /**
         *btnOpen
         **/
        $('body').on('click', '.btnOpen', function(){
            let id = $(this).attr('id');
            window.open('/commercial.quotations.show.pdf/' + id, '_blank');

        })

        /**
         *btnPrint
         **/
        $('body').on('click', '.btnPrint', function(){
            $("#frame_pdf").get(0).contentWindow.print();
        })

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
                    Confirmar: {
                        text: 'Confirmar',
                        btnClass: 'btn-green',
                        action: function(){
                            $.confirm({
                                icon: 'fa fa-check',
                                title: 'Mensaje enviado!',
                                theme: 'modern',
                                type: 'green',
                                buttons: {
                                    Cerrar: {
                                        text: 'Cancelar',
                                        btnClass: 'btn-red',
                                        action: function(){
                                        }
                                    },
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
                        }
                    },
                    Cancelar: {
                        text: 'Cancelar',
                        btnClass: 'btn-red',
                        action: function(){
                        }
                    },
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
         ** Delete
         **/
          $('#tbl_data').on('click', '.delete', function() {
              let data = tbl_data.row( $(this).parents('tr') ).data();
              $.get('/commercial.quotations.delete', '_token=' + '{{ csrf_token() }}' + '&quotation_id=' + data['id'], function(response) {
                  if(response == true) {
                      toastr.success('Se eliminó satsifactoriamente la cotización');
                      tbl_data.ajax.reload();
                  } else {
                      toastr.error('Ocurrió un error mientras intentaba eliminar la cotización');
                  }
              }, 'json');
          });

        /**
         * Send Email
         */
        $('body').on('click', '.send', function() {
            let data = tbl_data.row( $(this).parents('tr') ).data();
            let id = $(this).parent().parent().parent().parent().parent().parent().attr('id');
            $.confirm({
                icon: 'fa fa-question',
                theme: 'modern',
                animation: 'scale',
                title: '¿Está seguro de enviar esta cotización?',
                content: function() {
                    let value = '';
                    if(data['customer_email'] == undefined) {
                        value = '';
                    } else {
                        value = data['customer_email'];
                    }
                    return '<div class="form-group">' +
                    '<input type="text" id="email" name="email" class="form-control" value="' + value + '" />' +
                    '<option></option>' +
                    '</div>'
                },
                buttons: {
                    Confirmar: {
                        text: 'Confirmar',
                        btnClass: 'btn-green',
                        action: function(){
                            $.confirm({
                                icon: 'fa fa-check',
                                title: 'Mensaje enviado!',
                                theme: 'modern',
                                type: 'green',
                                buttons: {
                                    Cerrar: {
                                        text: 'Cerrar',
                                        btnClass: 'btn-red',
                                        action: function(){
                                        }
                                    },
                                },
                                content: function() {
                                    var self = this;
                                    return $.ajax({
                                        type: 'post',
                                        url: '/commercial.quotations.send',
                                        data: {
                                            _token: '{{ csrf_token() }}',
                                            quotation_id: data['id'],
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
                        }
                    },
                    Cancelar: {
                        text: 'Cancelar',
                        btnClass: 'btn-red',
                        action: function(){
                        }
                    },
                }
            });
        });

        /**
         * Convert to Voucher
         */
        $('body').on('click', '.convert', function() {
            let data = tbl_data.row( $(this).parents('tr') ).data();
            let id = $(this).parent().parent().parent().parent().parent().parent().attr('id');
            $.confirm({
                theme: 'modern',
                animation: 'scale',
                icon: 'fa fa-exclamation-triangle',
                type: 'green',
                draggable: false,
                title: '',
                content: function() {
                    let option = '';
                    if(data['typedocument_id'] == 4) {
                        option = '<option value="1" selected="selected">FACTURA</option>';
                    }  else {
                        option = '<option value="2" selected>BOLETA</option>'
                    }
                    return '<div>Antes de <b>CONVERTIR, VERIFICAR</b> la cotización con la Opción EDITAR del menú <b>OPCIONES</b></div>' +
                        '<div class="form-group"><br>' +
                        // '<label>Seleccionar comprobante</label>' +
                        '<select class="form-control converjconfirmslc" name="boucher" id="boucher">' +
                        '' +
                        option +
                        '</select></div>';
                },
                buttons: {
                    Confirmar: {
                        text: 'Confirmar',
                        btnClass: 'btn-green',
                        action: function(){
                            var boucher_id = this.$content.find('#boucher').val();

                            $.ajax({
                                type: 'post',
                                url: '/commercial.sales.create',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    typevoucher_id: boucher_id,
                                    quotation_id: data['id']
                                },
                                dataType: 'json',
                                beforeSend: function() {

                                },
                                complete: function() {

                                },
                                success: function(response) {
                                    if(response['response'] == true) {
                                        toastr.success('Se grabó satisfactoriamente el cliente');
                                        window.location = '/commercial.sales?idQuotation='+data['id'];
                                    } else if(response['response'] == -2) {
                                        toastr.error('Uno o más productos no alcanzan el stock suficiente');
                                    } else if(response == -2) {
                                        toastr.error('Debe Configurar un correlativo para el tipo de pago que escogió');
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
                        }
                    },
                    Cancelar: {
                        text: 'Cancelar',
                        btnClass: 'btn-red',
                        action: function(){
                        }
                    },
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
            getTotalSales()
        });

        $('#denomination').on('keyup', function() {
            tbl_data.ajax.reload();
            getTotalSales()
        });

        $('#document').on('keyup', function() {
            tbl_data.ajax.reload();
            getTotalSales()
        });

        $('#btnQuotation').click(function() {
            window.location.href = '/commercial.quotations.create';
        });

        function getTotalSales() {
            let data = $('#filter_date').val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '/commercial.quotations.totals',
                type: 'post',
                data: 'dates=' + data + '&denomination=' + $("#denomination").val() + '&document='+ $('#document').val(),
                dataType: 'json',
                success: function(response) {
                    $('#totalPresales').text(response['totalPreSale'])
                    $('#totalCot').text(response['totalQuoations'])
                    $('#totalConvert').text(response['totalConvert'])
                },
                error: function(response) {
                    toastr.error(response.responseText);
                }
            });
        }
    </script>
@stop
