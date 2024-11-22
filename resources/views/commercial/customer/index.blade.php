@extends('layouts.azia')
@section('css')
    <style>.edit,.delete{display: none;}</style>
    @can('clientes.edit')
        <style>.edit{display: inline-block;}</style>
    @endcan
    @can('clientes.delete')
        <style>.delete{display: inline-block;}</style>
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
                            <h3 class="card-title"">
                                CLIENTES
                            </h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-7">
                            @can('clientes.create')
                                <button class="btn btn-primary-custom" id="openModalCustomer">
                                    Nuevo Cliente
                                </button>
                            @endcan
                            @can('clientes.import')
                                <button class="btn btn-secondary-custom" id="openModalUpload">
                                    <i class="fa fa-upload"></i>
                                    Excel
                                </button>
                            @endcan
                            @can('clientes.export')
                                <a class="btn btn-secondary-custom" href="{{route('export.customers')}}">
                                    <i class="fa fa-download"></i>
                                    Excel
                                </a>
                            @endcan
                        </div>
                        <div class="col-12 col-md-5">
                            <input type="text" class="form-control" id="search" name="search" placeholder="Buscar empresa">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="table-responsive">
                            <table id="tbl_data" class="dt-bootstrap4 table-hover" style="width: 100%;">
                                <thead>
                                    <th width="30px">Tipo</th>
                                    <th width="50px">Código</th>
                                    <th width="120px">Número</th>
                                    <th width="250px">Denominación/Nombres.</th>
                                    <th width="400px">Dirección Fiscal</th>
                                    <th>Email</th>
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
    </div>
    <form action="{{ route('import.customers') }}" method="POST" enctype="multipart/form-data">
        @csrf
    <div id="mdl_upload_customers" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">SUBIR CLIENTES</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <form role="form" data-toggle="validator" id="frm_customer">
                        <div class="row">
                            <div class="col-12">
                                <a class="btn btn-secondary-custom btn-block" href="{{asset('templates/Plantilla_Clientes.xlsx')}}">
                                    Descargar Plantilla Excel
                                </a>
                            </div>

                            <div class="col-12">
                                <br>
                            </div>

                            <div class="col-12" >
                                <div class="custom-file">
                                    <input class="custom-file-label" required type="file" id="fileUpload" name="file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                                    <label class="custom-file-label" for="fileUpload">Seleccionar Archivo...</label>
                                </div>
                                {{-- <label class="custom-file-label archive_name" style="margin: 0 15px">SELECCIONAR ARCHIVO</label> --}}
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
    <form id="frm_customer" method="post" class="form-horizontal" role="form" data-toggle="validator">
        <input type="hidden" id="validado" value="0">
        <div id="mdl_add_cliente" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="z-index: 9999;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"><span id="modalCustomerTitle">Agregar </span>Cliente</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="card content-overlay">
                            <div class="card-body">
                                <div class="row">
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
                                        <input type="hidden" id="customer_id" name="customer_id">
                                        <div class="form-group">
                                            <label for="document">Documento *</label>
                                            <input type="text" id="document" name="document" class="form-control" placeholder="Ingresar Documento" required data-error="Este campo no puede estar vacío">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="description">Cliente *</label>
                                            <input id="description" name="description" type="text" class="form-control" required data-error="Este campo no puede estar vacío">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="code">Código de Cliente (Referencial)*</label>
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
                                            <label for="detraction">Cuenta de Detracción</label>
                                            <input id="detraction" name="detraction" type="text" class="form-control" id="detraction">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="detraction">Contacto</label>
                                            <input id="contact" name="contact" type="text" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <button id="btnGrabarCliente" type="submit" class="btn btn-primary-custom pull-right"> GUARDAR</button>
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
            // 'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
            'pageLength' : 20,
            'bLengthChange' : false,
            'lengthMenu': false,
            'language': {
                'url': '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json'
            },
            'processing': false,
            'serverSide': true,
            'searching': false,
            'ajax': {
                'url': '/commercial.dt.customers',
                'type' : 'get',
                'data': function(d) {
                    d._token = '{{ csrf_token() }}';
                    d.denomination = $('#search').val();
                }
            },
            'columns': [
                {
                    data: 'td_description'
                },
                {
                    data: 'code',
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
                    data: 'phone'
                },
                {
                    data: 'id'
                }
            ],
            'fnRowCallback': function( nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                $(nRow).find('td:eq(7)').html('' +
                    '<button type="button" class="edit btn btn-redondos btn-secondary-custom btn-sm" title="Editar"><i class="fa fa-edit"></i></button>' +
                    '<button type="button" class="delete btn btn-redondos btn-danger-custom btn-sm m-lg-2" title="Anular"><i class="fa fa-trash"></i></button>');
            }
        });

        $('#tbl_data').on( 'click', '.edit', function () {
            var data = tbl_data.row( $(this).parents('tr') ).data();
            if(data == undefined) {
                tbl_data = $("#tbl_data").DataTable();
                data = tbl_data.row( $(this).parents('tr') ).data();
            }

            $("#customer_id").val(data['id']);

            $.post('/commercial.customer.prepare',
                'customer_id=' + data['id'] +
                '&_token=' + '{{ csrf_token() }}', function(response) {
                $('#typedocument').val(response['typedocument_id']);
                $('#customer_id').val(data['id']);
                $('#document').val(response['document']);
                $('#description').val(response['description']);
                $('#phone').val(response["phone"]);
                $('#address').val(response['address']);
                $('#email').val(response['email']);
                $('#contact').val(response['contact']);
                $('#code').val(response['code']);
                $('#emailOptional').val(response['secondary_email']);
                $('#detraction').val(response['detraction']);
                $('#method').val(1);
                $('#modalCustomerTitle').text('Editar ');
                $('#mdl_add_cliente').modal('show');
            }, 'json');
            $("html, body").animate({ scrollTop: 0 }, 600);
        });

        $('#tbl_data').on('click','.delete', function () {
            let data = tbl_data.row( $(this).parents('tr') ).data();
            if(data == undefined) {
                tbl_data = $('#tbl_data').DataTable();
                data = tbl_data.row( $(this).parents('tr') ).data();
            }
            $("#customer_id").val(data['id']);

            $.confirm({
                icon: 'fa fa-question',
                theme: 'modern',
                animation: 'scale',
                title: '¿Está seguro de eliminar este registro?',
                content: '<div>Esté registro se eliminará permanentemente.</div>',
                buttons: {
                    Confirmar: function () {
                        $.ajax({
                            type: 'get',
                            url: '/commercial.customer.delete',
                            data: {
                                _token: '{{ csrf_token() }}',
                                customer_id: $("#customer_id").val()
                            },
                            dataType: 'json',
                            beforeSend: function() {

                            },
                            complete: function() {

                            },
                            success: function(response) {
                                if(response == true) {
                                    toastr.success('Se eliminó satisfactoriamente el cliente');
                                    $("#tbl_data").DataTable().ajax.reload();
                                }  else {
                                    toastr.error('No se puede borrar un cliente con Comprobantes Relacionados');
                                }

                            },
                            error: function(response) {
                                toastr.error('Ocurrio un error inespertado!');
                            }
                        });
                    },
                    Cancelar: function () {

                    }
                }
            });
        });

        $('#document').on('keyup', function() {       
            let url = '';
            if($('#typedocument').val() == 2) {
                var documentValue = $(this).val().substring(0, 8);
                documentValue = documentValue.replace(/\D/g, '');
                $('#document').val(documentValue);
                if($(this).val().length == 8) {
                    url = '/consult.dni/' + $(this).val();
                    getCustomer(url, $('#typedocument').val());
                }
            } else if($('#typedocument').val() == 4) {
                var documentValue = $(this).val().substring(0, 11);
                documentValue = documentValue.replace(/\D/g, '');
                $('#document').val(documentValue);
                if($(this).val().length == 11) {
                    url = '/consult.ruc/' + $(this).val();
                    getCustomer(url, $('#typedocument').val());
                }
            }
        });

        function getCustomer(url, typedocument) {
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
                    if(typedocument == 2) {
                        $('#description').val(response['nombres'] + ' ' + response['apellidoPaterno'] + ' ' + response['apellidoMaterno']);
                        toastr.info('Datos extraidos de la RENIEC.');
                        $('#validado').val(1);
                    } else {
                        $('#description').val(response['razonSocial']);
                        $('#phone').val(response['telefonos']);
                        let address = response['direccion'];
                        if(response['departamento'] != null) {
                            address += ' ' + response['departamento'];
                        }

                        if(response['provincia'] != null) {
                            address += ' - ' + response['provincia'];
                        }

                        if(response['distrito'] != null) {
                            address += ' - ' + response['distrito'];
                        }
                        $('#address').val(address);
                        toastr.info('Datos extraidos de la SUNAT.');
                        $('#validado').val(1);
                    }
                },
                error: function(response) {
                    toastr.error('No se encontró al cliente, deberá registrarlo manualmente.');
                    //clearDataCustomer();
                }
            });
        }

        $('#frm_customer').validator().on('submit', function(e) {
            if(e.isDefaultPrevented()) {
                toastr.warning('Debe llenar todos los campos obligatorios');
            } else {
                e.preventDefault();
                let data = $('#frm_customer').serialize();
                var method = '';
                if($('#method').val() == 0) {
                    method = 'create';
                } else {
                    method = 'update';
                }

                $.ajax({
                    url: '/commercial.customer.' + method,
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
                            toastr.warning('El cliente ya existe.');
                        } else if(response == true) {
                            toastr.success('Se grabó satisfactoriamente el cliente');
                            clearDataCustomer();
                            $('#typedocument').val('');
                            $("#tbl_data").DataTable().ajax.reload();
                            clearDataCustomer();
                            $('#mdl_add_cliente').modal('hide');
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

        $('#openModalCustomer').on('click', function() {
            clearDataCustomer();
            $('#modalCustomerTitle').text('Crear ')
            

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: 'post',
                url: '/commercial/customer/getcode',
                dataType: 'json',
                success: function(response) {
                    $('#code').val(response);
                },
                error: function(response) {
                    toastr.error(response.responseText);
                }
            });

            $('#mdl_add_cliente').modal('show');
        });

        function clearDataCustomer() {
            $('#typedocument').val('');
            $('#document').val('');
            $('#description').val('');
            $('#phone').val('');
            $('#address').val('');
            $('#email').val('');
            $('#customer_id').val('');
            $('#method').val(0);
            $('#contact').val('');
            $('#detraction').val('');
            $('#customer_id').val('');
            $('#code').val('');
        }

        $('#openModalUpload').click(function() {
            $('#mdl_upload_customers').modal('show');
            $('.archive_name').text('SELECCIONAR ARCHIVO');

        });

        $('#search').on('keyup', function() {
            $("#tbl_data").DataTable().ajax.reload();
        });

        $('.file').change(function (e) {
            let name = $(this).val();
            if(name != '' && name != null) {
                $('.archive_name').text(name);
            }
        });
    </script>
@stop
