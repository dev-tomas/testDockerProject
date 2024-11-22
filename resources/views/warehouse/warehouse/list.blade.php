@extends('layouts.azia')
@section('css')
    <style>.prepare{display: none;}</style>
    @can('almacenes.edit')
        <style>.prepare{display: inline-block;}</style>
    @endcan
@endsection
@section('content')
    <div id="mdl_add_warehouse" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" style="font-size: 1.5em !important;">Agregar Almacén</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="font-size: 1.5em !important;">×</span></button>
                </div>
                <div class="modal-body">
                    <form role="form" data-toggle="validator" id="frm_warehouse">
                        <input type="hidden" id="warehouse_id" name="warehouse_id">
                        <div class="card card-default">
                            <div class="card-header" style="background: #F4F5F7 !important;">
                                <h3 class="card-title">
                                    Registrar Almacén
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="document"> Código</label>
                                            <input type="text" name="code" id="code" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="document"> Nombre</label>
                                            <input type="text" name="description" id="description" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="description"> Dirección</label>
                                            <input type="text" name="address" id="address" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="description"> Responsable</label>
                                            <input type="text" name="responsable" id="responsable" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" id="save" class="btn btn-secondary-custom">
                                            <i class="fa fa-save"></i>
                                            GRABAR DATOS
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header color-gray">
                    <div class="row">
                        <div class="col-12 text-center">
                            <h3 class="card-title">
                                LISTA DE ALMACENES 
                            </h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            @can('almacenes.create')
                                <button class="btn btn-primary-custom" id="openModalWarehouse">
                                    NUEVO ALMACÉN
                                </button>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tbl_data" class="table dt-bootstrap4">
                            <thead>
                            <th>CODIGO</th>
                            <th>NOMBRE</th>
                            <th>DIRECCIÓN</th>
                            <th>RESPONSABLE</th>
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
            'pageLength' : 10,
            'lengthMenu': false,
            'bLengthChange' : false,
            'language': {
                'url': '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json'
            },
            'processing': false,
            'serverSide': true,
            'searching': false,
            'ajax': {
                'url': '/commercial.dt.warehouses',
                'type' : 'get'
            },
            'columns': [
                {
                    data: 'code',
                },
                {
                    data: 'description'
                },
                {
                    data: 'address'
                },
                {
                    data: 'responsable'
                },
                {
                    data: 'id'
                }
            ],
            'fnRowCallback': function( nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                $(nRow).find('td:eq(4)').html('<button type="button" class="prepare btn-redondos btn btn-secondary-custom btn-sm"><i class="fa fa-edit"></i></button>');
            }
        });

        $('#tbl_data').on( 'click', '.prepare', function () {
            var data = tbl_data.row( $(this).parents('tr') ).data();
            if(data == undefined) {
                tbl_data = $("#tbl_data").DataTable();
                data = tbl_data.row( $(this).parents('tr') ).data();
            }

            $('#warehouse_id').val(data['id']);

            $.get('/warehouse.prepare',
                'warehouse_id=' + data['id'] +
                '&_token=' + '{{ csrf_token() }}', function(response) {
                    $('#code').val(response['code']);
                    $('#description').val(response['description']);
                    $('#address').val(response['address']);
                    $('#responsable').val(response['responsable']);
                    $('#mdl_add_warehouse').modal('show');
                }, 'json');
            $('html', 'body').animate({ scrollTop: 0 }, 600);
        });

        $('#frm_warehouse').validator().on('submit', function(e) {
            if(e.isDefaultPrevented()) {
                toastr.warning('Debe llenar todos los campos obligatorios');
            } else {
                e.preventDefault();
                let data = $('#frm_warehouse').serialize();

                $.ajax({
                    url: '/warehouse.save',
                    type: 'post',
                    data: data + '&_token=' + '{{ csrf_token() }}',
                    dataType: 'json',
                    beforeSend: function() {
                        $('#save').attr('disabled');
                    },
                    complete: function() {

                    },
                    success: function(response) {
                        if(response == true) {
                            toastr.success('Se grabó satisfactoriamente el almacén');
                            $("#tbl_data").DataTable().ajax.reload();
                            clearDataWarehouse();
                            $('#mdl_add_warehouse').modal('hide');
                        } else {
                            console.log(response.responseText);
toastr.error('Ocurrio un error');
                        }
                    },
                    error: function(response) {
                        console.log(response.responseText);
toastr.error('Ocurrio un error');
                        $('#save').removeAttr('disabled');
                    }
                });
            }
        });

        $('#openModalWarehouse').on('click', function() {
            clearDataWarehouse();
            $('#mdl_add_warehouse').modal('show');
        });

        function clearDataWarehouse() {
            $('#description').val('');
            $('#address').val('');
            $('#responsable').val('');
            $('#warehouse_id').val('');
        }
    </script>
@stop