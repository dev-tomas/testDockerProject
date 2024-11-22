@extends('layouts.azia')
@section('css')
    <style>.prepare,.delete{display: none;}</style>
    @can('categorias.edit')
        <style>.prepare{display: inline-block;}</style>
    @endcan
    @can('categorias.delete')
        <style>.delete{display: inline-block;}</style>
    @endcan
@endsection
@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header color-gray">
                    <div class="row">
                        <div class="col-12 text-center">
                            <h3 class="card-title">
                                CENTRO DE COSTOS
                            </h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-10">
                            <button class="btn btn-primary-custom" id="openModalCategory">
                                NUEVO CENTRO DE COSTOS
                            </button>
                        </div>
                        <div class="col-12 col-md-2">
                            <input type="text" class="form-control" id="search" name="search" placeholder="Buscar Centro de Costos">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tbl_data" class="dt-bootstrap4 table-hover" style="width: 100%;">
                            <thead>
                            <th>Código</th>
                            <th>Centro de Costos</th>
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

<form role="form" data-toggle="validator" id="frm_category">
    <div id="mdl_add_category" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" style="font-size: 1.5em !important;">NUEVO CENTRO DE COSTOS</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="font-size: 1.5em !important;">×</span></button>
                </div>
                <div class="modal-body">
                    <form role="form" data-toggle="validator" id="frm_category">
                        <input type="hidden" id="center_id" name="center_id">
                        <div class="card card-default">
                            <div class="card-header" style="background: #F4F5F7 !important;">
                                <h3 class="card-title">
                                    Registrar Centro de Costos
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="description"> Código</label>
                                            <input type="text" class="form-control" name="code" id="code" required>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="description"> Centro de Costo</label>
                                            <input type="text" class="form-control" name="description" id="description" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" id="save" class="btn btn-primary-custom">
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
</form>
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
            'processing': false,
            'serverSide': true,
            'searching': false,
            'ajax': {
                'url': '/logistic.costcenter.dt',
                'type' : 'get'
            },
            'columns': [
                {data: 'code'},
                {data: 'center'},
                {data: 'id'},
            ],
            'fnRowCallback': function( nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                $(nRow).find('td:eq(2)').html('' +
                    '<button type="button" class="prepare btn-redondos  btn btn-secondary-custom btn-sm" title="Editar"><i class="fa fa-edit"></i></button>' +
                    '<button type="button" class="delete  btn-redondos btn btn-danger-custom btn-sm m-lg-2" title="Anular"><i class="fa fa-trash"></i></button>');
                // $(nRow).find('td:eq(2)').html(options);
            }
        });

        $('#tbl_data').on( 'click', '.prepare', function () {
            var data = tbl_data.row( $(this).parents('tr') ).data();
            if(data == undefined) {
                tbl_data = $("#tbl_data").DataTable();
                data = tbl_data.row( $(this).parents('tr') ).data();
            }

            $("#center_id").val(data['id']);

            $.post('/logistic.costcenter.get',
                'center_id=' + data['id'] +
                '&_token=' + '{{ csrf_token() }}', function(response) {
                    $('#center_id').val(response['id']);
                    $('#description').val(response['center']);
                    $('#code').val(response['code']);
                    $('#mdl_add_category').modal('show');
                }, 'json');
        });
        $('#tbl_data').on( 'click', '.delete', function () {
            var data = tbl_data.row( $(this).parents('tr') ).data();
            if(data == undefined) {
                tbl_data = $("#tbl_data").DataTable();
                data = tbl_data.row( $(this).parents('tr') ).data();
            }

            $("#center_id").val(data['id']);

            $.post('/logistic.costcenter.delete',
                'center_id=' + data['id'] +
                '&_token=' + '{{ csrf_token() }}', function(response) {
                    if (response == true) {
                        toastr.success('Se eliminó satisfactoriamente el centro de costos');
                        $("#tbl_data").DataTable().ajax.reload();
                    } else {
                        toastr.danger('No se puedo eliminar el centro de costos');
                        $("#tbl_data").DataTable().ajax.reload();
                    }
                }, 'json');
        });

        $('#frm_category').validator().on('submit', function(e) {
            if(e.isDefaultPrevented()) {
                toastr.warning('Debe llenar todos los campos obligatorios');
            } else {
                e.preventDefault();
                let data = $('#frm_category').serialize();

                $.ajax({
                    url: '/logistic.costcenter.save',
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
                            toastr.success('Se grabó satisfactoriamente el centro de costos');
                            $("#tbl_data").DataTable().ajax.reload();
                            clearDataCategory();
                            $('#mdl_add_category').modal('hide');
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

        $('#openModalCategory').on('click', function() {
            $('#mdl_add_category').modal('show');
        });

        function clearDataCategory() {
            $('#center_id').val('');
            $('#description').val('');
            $('#status').val('');
        }
    </script>
@stop