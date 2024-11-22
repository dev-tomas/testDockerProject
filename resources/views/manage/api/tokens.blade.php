@extends('layouts.azia')
@section('css')
    <style>
        .valid-ruc{ height: 33.9px; position: absolute; top: 25%;right: 10px; display: none; user-select: none;} .valid-ruc.active{display: block;} .cont-ruc {position: relative}
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header color-gray">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <h3 class="card-title">TOKENS</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <button class="btn btn-primary-custom" data-toggle="modal" data-target="#newTokenModal">
                                Nuevo Token
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="table-responsive">
                            <table id="tbl_data" class="dt-bootstrap4" style="width: 100%;">
                                <thead>
                                <tr>
                                    <th width="150px">Fecha Hora</th>
                                    <th width="350px">Cliente</th>
                                    <th width="300px">Token</th>
                                    <th width="100px">Estado</th>
                                    <th>*</th>
                                </tr>
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
    <div class="modal fade" id="newTokenModal" tabindex="-1" aria-labelledby="newTokenModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="frm_new_token">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="newTokenModalLabel">Nuevo Token</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Empresa</label>
                                    <select id="company" class="form-control" required>
                                        <option value="">Seleccione una Empresa</option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->id }}">{{ $company->document }} - {{ $company->trade_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary-custom" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary-custom">Grabar</button>
                    </div>
                </div>
            </form>
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
                'url': '/manage/api/tokens/dt',
                'type' : 'get',
                'data': function(d) {
                    d.dates = $('#filter_date').val();
                }
            },
            'columns': [
                {
                    data: 'created_at',
                },
                {
                    data: 'client.document',
                },
                {
                    data: 'token',
                },
                {
                    data: 'status',
                },
                {
                    data: 'id',
                },
            ],
            'fnRowCallback': function( nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                $(nRow).find('td:eq(0)').text(moment(aData['created_at'], 'YYYY-MM-DD H:m:s').format('DD-MM-YYYY H:m'));
                $(nRow).find('td:eq(1)').text(`${aData['client']['document']}-${aData['client']['trade_name']}`);
                let status = `<span class="badge badge-success">ACTIVO</span>`;
                if (aData['status'] != 'active') {
                    status = `<span class="badge badge-danger">INACTIVO</span>`;
                }
                $(nRow).find('td:eq(3)').html(status);

                let button = '<div class="btn-group">';
                button += '<button type="button" class="btn btn-secondary-custom dropdown-toggle dropdown-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Opciones';
                button += '</button>';
                button += '<div class="dropdown-menu dropdown-menu-right" x-placement="bottom-start" style="position: absolute; transform: translate3d(-56px, 33px, 0px); top: 0px; left: 0px; will-change: transform; right: 0px; width: 200px;">';

                if (aData['status'] == 'active') {
                    button += '<a class="dropdown-item disable text-danger" href="#">Desactivar Token</a>';
                } else {
                    button += '<a class="dropdown-item activeToken text-success" href="#">Activar Token</a>';
                }

                button += '<div class="dropdown-divider"></div>';
                button += '<a class="dropdown-item delete text-danger" href="#">Eliminar Token</a>';

                button += '</div>';
                button += '</div>';
                $(nRow).find('td:eq(4)').html(button);
            },
        });

        $('#frm_new_token').submit(function(e) {
            e.preventDefault();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: 'post',
                url: '/manage/api/create',
                data: {
                    client_id: $('#company').val()
                },
                dataType: 'json',
                success: function(response) {
                    toastr.success('Token creado correctamente');
                    $('#newTokenModal').modal('hide');
                    $('#company').val('');
                    tbl_data.ajax.reload();
                },
                error: function(response) {
                    toastr.error(response.responseText);
                }
            });
        });

        $('body').on('click', '.disable', function(e) {
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            let data = tbl_data.row( $(this).parents('tr') ).data();

            $.confirm({
                icon: 'fa fa-warning',
                theme: 'modern',
                animation: 'scale',
                type: 'green',
                draggable: false,
                title: '¿Está seguro de desactivar este token?',
                content: '',
                buttons: {
                    Confirmar: {
                        text: 'Confirmar',
                        btnClass: 'btn btn-green',
                        action: function() {
                            $.ajax({
                                type: 'post',
                                url: '/manage/api/tokens/change-status',
                                data: {type: 2, token: data['id']},
                                dataType: 'json',
                                success: function(response) {
                                    if(response == true) {
                                        toastr.success('Se actualizó satisfactoriamente el token.');
                                    } else {
                                        toastr.error('Ocurrió un error cuando intentaba actualizar el token.');
                                    }

                                    tbl_data.ajax.reload();
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
                        btnClass: 'btn btn-red'
                    }
                }
            });
        })

        $('body').on('click', '.activeToken', function(e) {
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            let data = tbl_data.row( $(this).parents('tr') ).data();

            $.confirm({
                icon: 'fa fa-warning',
                theme: 'modern',
                animation: 'scale',
                type: 'green',
                draggable: false,
                title: '¿Está seguro de activar este token?',
                content: '',
                buttons: {
                    Confirmar: {
                        text: 'Confirmar',
                        btnClass: 'btn btn-green',
                        action: function() {
                            $.ajax({
                                type: 'post',
                                url: '/manage/api/tokens/change-status',
                                data: {type: 1, token: data['id']},
                                dataType: 'json',
                                success: function(response) {
                                    if(response == true) {
                                        toastr.success('Se actualizó satisfactoriamente el token.');
                                    } else {
                                        toastr.error('Ocurrió un error cuando intentaba actualizar el token.');
                                    }

                                    tbl_data.ajax.reload();
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
                        btnClass: 'btn btn-red'
                    }
                }
            });
        })

        $('body').on('click', '.delete', function(e) {
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            let data = tbl_data.row( $(this).parents('tr') ).data();

            $.confirm({
                icon: 'fa fa-warning',
                theme: 'modern',
                animation: 'scale',
                type: 'green',
                draggable: false,
                title: '¿Está seguro de eliminar este token?',
                content: '',
                buttons: {
                    Confirmar: {
                        text: 'Confirmar',
                        btnClass: 'btn btn-green',
                        action: function() {
                            $.ajax({
                                type: 'post',
                                url: '/manage/api/tokens/delete',
                                data: {token: data['id']},
                                dataType: 'json',
                                success: function(response) {
                                    if(response == true) {
                                        toastr.success('Se eliminó satisfactoriamente el token.');
                                    } else {
                                        toastr.error('Ocurrió un error cuando intentaba eliminar el token.');
                                    }

                                    tbl_data.ajax.reload();
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
                        btnClass: 'btn btn-red'
                    }
                }
            });
        })
    </script>
@stop
