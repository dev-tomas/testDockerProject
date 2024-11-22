@extends('layouts.azia')
@section('css')
    <style>.prepare,.destroy{display: none;}</style>
    @can('roles.edit')
        <style>.prepare{display: inline-block;}</style>
    @endcan
    @can('roles.delete')
        <style>.destroy{display: inline-block;}</style>
    @endcan
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card text-center">
                <div class="card-header color-gray">
                    <div class="row">
                        <div class="col-12" style="margin-bottom: 1em">
                            FUNCIONES
                        </div>
                        <div class="col-12">
                            @can('roles.create')
                                <button class="btn btn-primary-custom" id="addRole">Nueva Función</button>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table id="tbl_data" class="dt-bootstrap4 table-hover table">
                                    <thead>
                                        <th>#</th>
                                        <th>Funciones</th>
                                        <th>Acciones</th>
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
    <form role="form" data-toggle="validator" id="frm_function">
        <div id="mdl_add_function" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="z-index: 9999;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header" style="background: #fff !important;">
                        <h4 class="modal-title">GESTIONAR FUNCIÓN</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <input type="hidden" name="role_id" id="role_id">
                    </div>
                    <div class="modal-body">
                        <form role="form" data-toggle="validator" id="frm_category">
                            <input type="hidden" id="category_id" name="category_id">
                            <div class="card card-default">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        Datos de Función
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="name"> Función</label>
                                                <input type="text" class="form-control" name="name" id="name" required>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="descrption">Descripción</label>
                                                <textarea name="description" id="description" class="form-control"></textarea>
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
            "order": [[ 0, "asc" ]],
            'searching': false,
            'processing': false,
            'serverSide': true,
            'ajax': {
                'url': '{{route("roles.get")}}',
                'type' : 'get',
                'data': function(d) {

                }
            },
            'columns': [
                {
                    data: 'id'
                },
                {
                    data: 'name',
                },
                {
                    data: 'id'
                },
            ],
            'fnRowCallback': function( nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                let button = '<button class="btn btn-redondos btn-secondary-custom btn-sm prepare">';
                        button += '<i class="fa fa-edit"></i>'
                    button += '</button>';
                    if (aData['administrable'] == 1) {
                        button += '<button class="btn btn-redondos btn-danger-custom btn-sm destroy">';
                        button += '<i class="fa fa-trash"></i>'
                        button += '</button>';
                    }
                $(nRow).find("td:eq(2)").html(button);
                var index = iDisplayIndex + 1;
                $('td:eq(0)',nRow).html(index);
            }
        });

        $('#tbl_data').on( 'click', '.destroy', function () {
            let data = tbl_data.row( $(this).parents('tr') ).data();
            if(data == undefined) {
                tbl_data = $('#tbl_data').DataTable();
                data = tbl_data.row( $(this).parents('tr') ).data();
            }
            $.confirm({
                title: '¡Eliminar!',
                content: '¿Desea eliminar este Role?',
                theme: 'bootstrap',
                type: 'red',
                typeAnimated: true,
                draggable: false,
                buttons: {
                    confirm: {
                        text: 'Eliminar',
                        btnClass: 'btn-red',
                        action: function () {
                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                }
                            });
                            $.ajax({
                                url: '/roles/' + data['id'],
                                type: 'delete',
                                dataType: 'json',
                                success: function(response) {
                                    if(response === true) {
                                        toastr.success('Se eliminó satisfactoriamente el role');
                                        $("#tbl_data").DataTable().ajax.reload();
                                        $("html, body").animate({ scrollTop: 0 }, 600);
                                    }
                                }
                            });
                        }
                    },
                    cancel: function () {}
                }
            });
        });

        $('#addRole').on('click', function() {
            $('#mdl_add_function').modal('show');
            $('#description').val('');
            $('#name').val('');
        });
        $('#frm_function').validator().on('submit', function(e) {
            if(e.isDefaultPrevented()) {
                toastr.warning('Debe llenar todos los campos obligatorios');
            } else {
                e.preventDefault();
                let data = $('#frm_function').serialize();

                $.ajax({
                    url: '/roles/store',
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
                            clearData();
                            $('#mdl_add_function').modal('hide');
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

        $('#tbl_data').on( 'click', '.prepare', function () {
            var data = tbl_data.row( $(this).parents('tr') ).data();
            if(data == undefined) {
                tbl_data = $("#tbl_data").DataTable();
                data = tbl_data.row( $(this).parents('tr') ).data();
            }

            $('#role_id').val(data['id']);

            $.get('/roles/prepare',
                'role_id=' + data['id'] +
                '&_token=' + '{{ csrf_token() }}', function(response) {
                    $('#description').val(response['description']);
                    $('#name').val(response['name']);
                    $('#mdl_add_function').modal('show');
                }, 'json');
            $('html', 'body').animate({ scrollTop: 0 }, 600);
        });

        function clearData() {
            $('#name').val('');
            $('#description').val('');
            $('#role_id').val('');
        }
    </script>
@stop
