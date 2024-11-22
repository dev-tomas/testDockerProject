@extends('layouts.azia')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">
                        Cotizaciones
                        <a href="{{'/commercial.quotations.create'}}" type="button" class="btn btn-primary-custom pull-right">
                            <i class="fa fa-plus-circle"></i>
                        </a>
                    </h3>
                </div>
            </div>

            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Lista de Cotizaciones</h3>
                </div>
                <div class="card-body">
                    <div class="table">
                        <table id="tbl_data" class="dt-bootstrap4">
                            <thead>
                            <th>FECHA</th>
                            <th>NUM.</th>
                            <th>T. DOC.</th>
                            <th>DENOMINACIÓN</th>
                            <th>M.</th>
                            <th>TOTAL ONEROSA</th>
                            <th>TOTAL GRATUITA</th>
                            <th>ENVIADO AL CLIENTE</th>
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
@stop

@section('script_admin')
    <script>
        let tbl_data = $("#tbl_data").DataTable({
            'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
            'language': {
                'url': '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json'
            },
            'processing': true,
            'serverSide': true,
            'ajax': {
                'url': '/commercial.dt.quotations',
                'type' : 'get'
            },
            'columns': [
                {
                    data: 'date'
                },
                {
                    data: 'correlative'
                },
                {
                    data: 'tp_description'
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
                    data: 'free'
                },
                {
                    data: 'id'
                },
                {
                    data: 'id'
                }
            ],
            'fnRowCallback': function( nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                switch (aData['sendemail']) {
                    case 0:
                        $(nRow).attr('id', aData['id']);
                        $(nRow).find('td:eq(7)').html('<span class="badge bg-danger"><i class="fa fa-close"></i></span>');
                        break;
                    default:
                        $(nRow).attr('id', aData['id']);
                        $(nRow).find('td:eq(7)').html('<span class="badge bg-danger"><i class="fa fa-close"></i></span>');
                        break;
                }

                let button = '<div class="btn-group-vertical">';
                button += '<div class="btn-group">';
                button += '<button class="btn btn-default dropdown-toggle" data-toggle="dropdown"> Opciones';
                button += '<span class="caret"></span>'
                button += '</button>';
                button += '<ul class="dropdown-menu" x-placement="bottom-start">';
                button += '<li><a href="#">Ver / Impriir PDF</a></li>';
                button += '<li><a href="#">Enviar al cliente</a></li>';
                button += '<li><a href="#" class="convert">Convertir a comprobante</a></li>';
                button += '<li><a href="#">Editar</a></li>';
                button += '<li><a href="#" style="color: red;">Borrar</a></li>';
                button += '</ul>';
                button += '</div>';
                $(nRow).find("td:eq(8)").html(button);
            }
        });

        /**
         * Convert to Boucher
         */
        $('body').on('click', '.convert', function() {
            var id = $(this).parent().parent().parent().parent().parent().parent().attr('id');
            $.confirm({
                icon: 'fa fa-question',
                theme: 'modern',
                animation: 'scale',
                title: '¿Está seguro de convertir esta cotización en un comprobante?',
                content: '<div class="form-group">' +
                    '<label>Seleccionar comprobante</label>' +
                    '<select class="form-control" name="boucher" id="boucher">' +
                    '<option value="1">BOLETA</option>' +
                    '<option value="2">FACTURA</option>' +
                    '<option></option>' +
                    '</select></div>',
                buttons: {
                    Confirmar: function () {
                        var boucher_id = this.$content.find('#boucher').val();

                        $.ajax({
                            url: '/commercial.quotations.convert',
                            type: 'post',
                            data: {
                                _token: '{{ csrf_token() }}',
                                typeboucher_id: boucher_id,
                                quotation_id: id
                            },
                            dataType: 'json',
                            beforeSend: function() {

                            },
                            complete: function() {

                            },
                            success: function(response) {
                                console.log(response);
                                if(response == true) {
                                    toastr.success('Se grabó satisfactoriamente el comprobante');
                                    window.location = '/commercial.sales';
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
        })
    </script>
@stop