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
                                CATEGORÍAS
                            </h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-10">
                            @can('categorias.create')
                                <button class="btn btn-primary-custom" id="openModalCategory">
                                    NUEVA CATEGORÍA
                                </button>
                            @endcan
                            <a href="/warehouse.products" class="btn btn-gray-custom">Productos</a>
                        </div>
                        <div class="col-12 col-md-2">
                            <input type="text" class="form-control" id="search" name="search" placeholder="Buscar categoría">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tbl_data" class="dt-bootstrap4 table-hover" style="width: 100%;">
                            <thead>
                            <th>Descripción</th>
                            <th>Estado</th>
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
                    <h4 class="modal-title" style="font-size: 1.5em !important;">NUEVA CATEGORÍA</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="font-size: 1.5em !important;">×</span></button>
                </div>
                <div class="modal-body">
                    <form role="form" data-toggle="validator" id="frm_category">
                        <input type="hidden" id="category_id" name="category_id">
                        <div class="card card-default">
                            <div class="card-header" style="background: #F4F5F7 !important;">
                                <h3 class="card-title">
                                    Registrar Categoría
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="description"> Categoría</label>
                                            <input type="text" class="form-control" name="description" id="description">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="status"> Estado</label>
                                            <select name="status" id="status" class="form-control">
                                                <option value="">Seleccionar</option>
                                                <option value="0">Inactivo</option>
                                                <option value="1" selected="selected">Activo</option>
                                            </select>
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
                'url': '/warehouse.dt.categories',
                'type' : 'get'
            },
            'columns': [
                {data: 'description'},
                {data: 'status'},
                {data: 'id'},
            ],
            'fnRowCallback': function( nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                switch (aData['status']) {
                    case 1:
                        $(nRow).find('td:eq(1)').html('<span class="badge badge-success">ACTIVO</span>');
                        break;
                    case 0:
                        $(nRow).find('td:eq(1)').html('<span class="badge badge-danger">INACTIVO</span>');
                        break;
                }

                let options = '<div class="btn-group"><button type="button" class="btn btn-dark">Opciones</button>';
                options += '<button type="button" class="btn btn-rounded btn-dark dropdown-toggle" data-toggle="dropdown">';
                options += '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>';
                options += '<div class="dropdown-menu" role="menu">';
                options += '<a class="dropdown-item prepare" href="#">Editar</a>';
                options += '<a class="dropdown-item delete" href="#">Eliminar</a>';
                options += '</div>';

                $(nRow).find('td:eq(2)').html('' +
                    '<button type="button" class="prepare btn-primary-custom btn btn-primary-custom btn-sm" title="Editar"><i class="fa fa-edit"></i></button>' +
                    '<button type="button" class="delete btn-danger-custom btn btn-danger-custom btn-sm m-lg-2" title="Anular"><i class="fa fa-trash"></i></button>');

            }
        });

        $('#tbl_data').on( 'click', '.prepare', function () {
            var data = tbl_data.row( $(this).parents('tr') ).data();
            if(data == undefined) {
                tbl_data = $("#tbl_data").DataTable();
                data = tbl_data.row( $(this).parents('tr') ).data();
            }

            $("#category_id").val(data['id']);

            $.post('/warehouse.category.prepare',
                'category_id=' + data['id'] +
                '&_token=' + '{{ csrf_token() }}', function(response) {
                    $('#category_id').val(response['id']);
                    $('#description').val(response['description']);
                    $('#status').val(response['status']);
                    $('#mdl_add_category').modal('show');
                }, 'json');
        });

        $('#frm_category').validator().on('submit', function(e) {
            if(e.isDefaultPrevented()) {
                toastr.warning('Debe llenar todos los campos obligatorios');
            } else {
                e.preventDefault();
                let data = $('#frm_category').serialize();

                $.ajax({
                    url: '/warehouse.category.save',
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
                            toastr.success('Se grabó satisfactoriamente la categoría');
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
            $('#category_id').val('');
            $('#description').val('');
            $('#status').val('');
        }
    </script>
@stop