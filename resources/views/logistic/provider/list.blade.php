@extends('layouts.azia')
@section('css')
    <style>.edit,.eliminar{display: none;}</style>
    @can('proveedores.edit')
        <style>.edit{display: inline-block;}</style>
    @endcan
    @can('proveedores.delete')
        <style>.eliminar{display: inline-block;}</style>
    @endcan
@endsection
@section('content')
    <div class="row">
        <input type="hidden" value="0" id="method">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header color-gray">
                    <div class="row">
                        <div class="col-12 text-center">
                            <h3 class="card-title">PROVEEDORES</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-10">
                            @can('proveedores.create')
                                <button class="btn btn-primary-custom" id="openModalProvider">
                                    NUEVO PROVEEDOR
                                </button>
                            @endcan
                            @can('proveedores.importar')
                                <button class="btn btn-secondary-custom" id="openModalUpload">
                                    <i class="fa fa-upload"></i>
                                    EXCEL
                                </button>
                            @endcan
                            @can('proveedores.export')
                                <a class="btn btn-secondary-custom" href="{{route('export.providers')}}">
                                    <i class="fa fa-download"></i>
                                    EXCEL
                                </a>
                            @endcan
                            {{-- @can('propuestas.show')
                                <a href="{{ route('proposal.get') }}" class="btn btn-dark-custom">PROPUESTAS</a>
                            @endcan
                            @can('ocompra.show')
                                <a href="{{ route('order.purchase') }}" class="btn btn-dark-custom">O/C</a>
                            @endcan --}}
                        </div>
                        <div class="col-12 col-md-2">
                            <input type="text" class="form-control" id="search" name="search" placeholder="Buscar proveedor">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tbl_data" class="dt-bootstrap4" style="width:100%">
                            <thead>
                                <th width="30px">Tipo</th>
                                <th width="50px">Código</th>
                                <th width="120px">Número</th>
                                <th width="250px">Denominación/Nombres.</th>
                                <th width="350px">Dirección Fiscal</th>
                                <th>Email</th>
                                
                                <th width="350px">Cuentas</th>
                                <th>Teléfono.</th>
                                
                                <th width="100px">OPCIONES</th>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form action="{{ route('import.providers') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div id="mdl_upload_providers" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="z-index: 9999;">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">SUBIR PROVEEDORES</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body">
                        <form role="form" data-toggle="validator" id="frm_provider">
                            <div class="row">
                                <div class="col-12">
                                    <a class="btn btn-secondary-custom btn-block" href="/logistic/download/templateProviders">
                                        Descargar Plantilla Excel
                                    </a>
                                </div>

                                <div class="col-12">
                                    <br>
                                </div>

                                <div class="col-12">
                                    <input type="file" name="file" class="custom-file-input">
                                    <label class="custom-file-label">SELECCIONAR ARCHIVO</label>
                                </div>

                                <div class="col-12">
                                    <br>
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-primary-custom btn-block">Subir Excel</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <form id="frm_provider" method="post" class="form-horizontal" role="form" data-toggle="validator">
        <input type="hidden" id="validado" value="0">
        <div id="mdl_add_provider" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="z-index: 9999;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"><span id="modalTitleProvider">Crear</span> Proveedor</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="card content-overlay">
                            <div class="card-body">
                                <div class="row">
                                    <input type="hidden" id="provider_id" name="provider_id">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="typedocument"> Tipo Documento *</label>
                                            <select name="typedocument" id="typedocument" class="form-control" data-error="Este campo no puede estar vacío" required>
                                                <option value="">Seleccionar</option>
                                                @if($typedocuments->count() > 0)
                                                    @foreach($typedocuments as $td)
                                                        <option value="{{$td->id}}">{{$td->description}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="document">Documento *</label>
                                            <input type="text" id="document" name="document" class="form-control" placeholder="Ingresar Documento" required data-error="Este campo no puede estar vacío">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="description">Proveedor *</label>
                                            <input id="description" name="description" type="text" class="form-control" required data-error="Este campo no puede estar vacío">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="code">Código de Proveedor *</label>
                                            <input id="code" name="code" type="text" class="form-control" readonly required data-error="Este campo no puede estar vacío">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="phone">Teléfono</label>
                                            <input id="phone" name="phone" type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="address">Dirección</label>
                                            <input id="address" name="address" type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="email">Correo Principal</label>
                                            <input id="email" name="email" type="email" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="email">Correo Opcional</label>
                                            <input id="emailOptional" name="emailOptional" type="email" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="detraction">Cuentas</label>
                                            <input id="detraction" name="detraction" type="text" class="form-control" id="detraction">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="contact">Contacto</label>
                                            <input id="contact" name="contact" type="text" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <button id="btnGrabarCliente" type="submit" class="btn btn-primary-custom pull-right">GUARDAR</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop

@section('script_admin')
    <script>
        let tbl_data = $("#tbl_data").DataTable({
            'pageLength': 15,
            'bLengthChange': false,
            'lengthMenu': false,
            'language': {
                'url': '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json'
            },
            'processing': false,
            'serverSide': true,
            'searching': false,
            'ajax': {
                'url': '/logistic.dt.providers',
                'type': 'get',
                'data': function(d) {

                    d.search = $('#search').val();
                }
            },
            'columns': [{
                    data: 'td_description'
                },
                {
                    data: 'code'
                },
                {
                    data: 'document'
                },
                {
                    data: 'c_description'
                },
                {
                    data: 'address'
                },
                {
                    data: 'email'
                },
                {
                    data: 'detraction'
                },
                {
                    data: 'phone'
                },
                {
                    data: 'id'
                }
            ],
            'fnRowCallback': function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                //$(nRow).find('td:eq(5)').html('<button type="button" class="edit btn btn-primary-custom btn-sm"><i class="fa fa-edit"></i></button>');
                let button = '<button type="button" class="edit btn btn-redondos btn-secondary-custom btn-sm"><i class="fa fa-edit"></i></button>';
                button += '<button type="button" class="eliminar btn btn-redondos btn-danger-custom btn-sm"><i class="fa fa-trash"></i></button>';
                $(nRow).find('td:eq(8)').html(button);
            }
        });

        $('#tbl_data').on( 'click', '.edit', function () {
            var data = tbl_data.row($(this).parents('tr')).data();
            if (data == undefined) {
                tbl_data = $("#tbl_data").DataTable();
                data = tbl_data.row($(this).parents('tr')).data();
            }

            $("#provider_id").val(data['id']);
            $('#method').val('1');

            $.get('/logistic.provider.get',
                'provider_id=' + data['id'] +
                '&_token=' + '{{ csrf_token() }}', function(response) {
                    $('#typedocument').val(response['typedocument_id']);
                    $('#document').val(response['document']);
                    $('#description').val(response['description']);
                    $('#phone').val(response["phone"]);
                    $('#address').val(response['address']);
                    $('#email').val(response['email']);
                    $('#contact').val(response['contact']);
                    $('#detraction').val(response['detraction']);
                    $('#code').val(response['code']);
                    $('#modalTitleProvider').text('Editar')
                    $('#mdl_add_provider').modal('show');
                }, 'json');
            $("html, body").animate({ scrollTop: 0 }, 600);
        });

        $('#tbl_data').on('click', '.eliminar', function() {
            var data = tbl_data.row($(this).parents('tr')).data();
            $.confirm({
                icon: 'fa fa-question',
                theme: 'modern',
                animation: 'scale',
                title: '¿Está seguro de que desea borrar este registro?',
                buttons: {
                    Confirmar: function() {
                        $.ajax({
                            'url': '{{ route('provider.detroy') }}',
                            'data': {
                                provider_id: data['id']
                            },
                            success: function(response) {
                                if (response == true) {
                                    // $('#md_proceso').modal('hide');
                                    toastr.success('Se elimino satisfactoriamente');
                                } else {
                                    toastr.error('Ocurrió un error');
                                }

                                $('#tbl_data').DataTable().ajax.reload();
                            }
                        });
                    },
                    Cancelar: function() {

                    }
                }
            });


        });

        $('#document').on('keyup', function() {
            let url = '';
            if ($('#typedocument').val() == 2) {
                if ($(this).val().length == 8) {
                    url = '/consult.dni/' + $(this).val();
                    getProvider(url, $('#typedocument').val());
                }
            } else if ($('#typedocument').val() == 4) {
                if ($(this).val().length == 11) {
                    url = '/consult.ruc/' + $(this).val();
                    getProvider(url, $('#typedocument').val());
                }
            }
        });

        function getProvider(url, typedocument) {
            $.ajax({
                url: url,
                type: 'post',
                data: {
                    '_token': "{{ csrf_token() }}"
                },
                dataType: 'json',
                beforeSend: function() {
                    let effect = '<div class="overlay effect">';
                    effect += '<i class="fa fa-refresh fa-spin">';
                    effect += '</div>';
                    $('.content-overlay').append(effect);
                },
                complete: function() {
                    $('.effect').remove();
                },
                success: function(response) {
                    if (typedocument == 2) {
                        $('#description').val(response['nombres'] + ' ' + response['apellidoPaterno'] + ' ' + response['apellidoMaterno']);
                        $('#validado').val(1);
                    } else {
                        $('#description').val(response['razonSocial']);
                        $('#phone').val(response['telefonos']);
                        $('#address').val(response['direccion']);
                        $('#validado').val(1);
                    }
                },
                error: function(response) {
                    clearDataProvider();
                    toastr.info('El cliente no existe.');
                }
            });
        }

        $('#frm_provider').validator().on('submit', function(e) {
            if (e.isDefaultPrevented()) {
                toastr.warning('Debe llenar todos los campos obligatorios');
            } else {
                e.preventDefault();
                let data = $('#frm_provider').serialize();
                if ($('#method').val() == '0') {
                    var method = 'create';
                } else {
                    var method = 'update';
                }

                $.ajax({
                    url: '/logistic.provider.' + method,
                    type: 'post',
                    data: data + '&_token=' + '{{ csrf_token() }}',
                    dataType: 'json',
                    beforeSend: function() {
                        $('#save').attr('disabled');
                    },
                    complete: function() {

                    },
                    success: function(response) {
                        if (response == '-99') {
                            toastr.warning('El proveedor ya existe.');
                        } else if (response == true) {
                            toastr.success('Se grabó satisfactoriamente el cliente');
                            clearDataProvider();
                            $('#typedocument').val('');
                            $("#tbl_data").DataTable().ajax.reload();
                            $('#mdl_add_provider').modal('hide');
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

        $('#openModalProvider').on('click', function() {
            clearDataProvider();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: 'post',
                url: '/logistic.providers/get-provider-code',
                dataType: 'json',
                success: function(response) {
                    $('#code').val(response);
                },
                error: function(response) {
                    toastr.error(response.responseText);
                }
            });

            $('#modalTitleProvider').text('Crear')
            $('#mdl_add_provider').modal('show');
            $('#method').val('0');
        });

        function clearDataProvider() {
            $('#description').val('');
            $('#phone').val('');
            $('#address').val('');
            $('#provider_id').val('');
            $('#document').val('');
            $('#email').val('');
            $('#detraction').val('');
            $('#contact').val('');
            $('#code').val('');
        }

        $('#openModalUpload').click(function() {
            $('#mdl_upload_providers').modal('show');
        });

        $('#search').on('keyup', function() {
            $("#tbl_data").DataTable().ajax.reload();
        });
    </script>
@stop
