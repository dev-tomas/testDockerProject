@extends('layouts.azia')
@section('css')
    <style>.edit,.delete,.send {display: none;}</style>
    @can('ocompra.edit')
        <style>.edit{display: block;}</style>
    @endcan
    @can('ocompra.delete')
        <style>.delete{display: block;}</style>
    @endcan
    @can('ocompra.send')
        <style>.send{display: block;}</style>
    @endcan
@stop
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header color-gray">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <h3 class="card-title">ÓRDENES DE COMPRA</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            @can('ocompra.export')
                                <a href="{{ route('purchaseorder.excel') }}" class="btn btn-secondary-custom">
                                    Descargar Excel
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="">Buscar Orden de Compra</label>
                                <input type="text" id="filter-num" class="form-control" placeholder="Ingresar Entidad" data-column="1">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="">Buscar Documento</label>
                                <input type="text" id="document" class="form-control" placeholder="Ingresar documento" data-column="2">
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
                                    <th width="150">RUC/DNI/ETC</th>
                                    <th>DENOMINACION</th>
                                    <th width="150">PLAZO DE ENTREGA</th>
                                    <th width="150">CONDICION</th>
                                    <th width="150">ENTREGA</th>
                                    <th width="100">TOTAL O.</th>
                                    <th>OPCIONES</th>
                                </thead>

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
            "order": [[ 1, "asc" ]],
            'searching': false,
            'processing': false,
            'serverSide': true,
            'ajax': {
                'url': '/logistic.order.purchase.get',
                'type' : 'get',
            },
            'columns': [
                {
                    data: 'date',
                    render: function render(data,type,row,meta) {
                        if (type === 'display') {
                            data = moment(data).format('DD-MM-YYYY');
                        }

                        return data;
                    }
                },
                {
                    data: 'correlative',
                    render: function(data, type, row) {
                        return row.serie + '-' + row.correlative;
                    }
                },
                {
                    data: 'document'
                },
                {
                    data: 'pname'
                },
                
                {
                    data: 'plazo'
                },
                {
                    data: 'condicion'
                },
                {
                    data: 'entrega'
                },
                {
                    data: 'total'
                },
                {
                    data: 'id'
                }
            ],
            
            'fnRowCallback': function( nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                let button = '<div class="btn-group">';
                    button += '<button type="button" class="btn btn-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Opciones</button>';
                    button += '<div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 38px, 0px); top: 0px; left: 0px; will-change: transform;">';
                    button += '<a href="#" class="dropdown-item send" oci="'+ aData['id'] +'">Enviar al proveedor</a>';
                    button += '<a href="/logistic.order.purchase.edit/' +aData['serie']  +'/' + aData['correlative'] + '" class=" dropdown-item edit">Editar</a>';
                    button += '<a href="#" style="color: red;" class="dropdown-item delete">Borrar</a>';
                    button += '</div>';
                button += '</div>';
                $(nRow).find("td:eq(8)").html(button);
            }
        });
        $('#filter-num').keyup(function(){
            tbl_data.column($(this).data('column')).search($(this).val()).draw();
        });
        $('#document').keyup(function(){
            tbl_data.column($(this).data('column')).search($(this).val()).draw();
        });

        $('#tbl_data').on('click', '.delete', function() {
            let data = tbl_data.row( $(this).parents('tr') ).data();
            $.get('/logistic.order.purchase.delete', '_token=' + '{{ csrf_token() }}' + '&order_id=' + data['id'], function(response) {
                if(response == true) {
                    toastr.success('Se eliminó satsifactoriamente la Orden de Compra');
                    tbl_data.ajax.reload();
                } else {
                    toastr.error('Ocurrió un error mientras intentaba eliminar la Orden de Compra');
                }
            }, 'json');
        });

        $('body').on('click', '.send', function() {
            let data = tbl_data.row( $(this).parents('tr') ).data();
            let id = $(this).attr('oci');

            console.log(id);
            $.confirm({
                icon: 'fa fa-question',
                theme: 'modern',
                animation: 'scale',
                type: 'green',
                draggable: false,
                title: '¿Está seguro de enviar esta Orden de Compra?',
                buttons: {
                    Confirmar: {
                        text: 'Confirmar',
                        btnClass: 'btn-green',
                        action: function(){
                            $.ajax({
                                type: 'post',
                                url: '/logistic.order.purchase.send',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    oc_id: id,
                                },
                                dataType: 'json',
                                success: function(response) {
                                    if(response == true) {
                                        $.confirm({
                                            icon: 'fa fa-check',
                                            title: 'Mensaje enviado!',
                                            theme: 'modern',
                                            type: 'green',
                                            draggable: false,
                                            buttons: {
                                                Cerrar: {
                                                    text: 'Cerrar',
                                                    btnClass: 'btn-red',
                                                    action: function(){
                                                    }
                                                }
                                            },
                                            content: function() {
                                                // var self = this;
                                                // return $.ajax({
                                                //     type: 'post',
                                                //     url: '/logistic.order.purchase.send',
                                                //     data: {
                                                //         _token: '{{ csrf_token() }}',
                                                //         oc: id,
                                                //     },
                                                //     dataType: 'json',
                                                //     success: function(response) {
                                                //         if(response == true) {
                                                //             toastr.success('Se envió satisfactoriamente la Orden de Compra');
                                                //         } else {
                                                //             toastr.error(response);
                                                //         }

                                                //     },
                                                //     error: function(response) {
                                                //         console.log(response.responseText);
toastr.error('Ocurrio un error');
                                                //         console.log(response.responseText)
                                                //     }
                                                // });
                                            }
                                        });
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
                    },
                    Cancelar: {
                        text: 'Cancelar',
                        btnClass: 'btn-red',
                        action: function(){
                        }
                    }
                }
            });
        });
    </script>
@stop
