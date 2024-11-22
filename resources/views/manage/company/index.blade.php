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
                            <h3 class="card-title">EMPRESAS</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <button class="btn btn-primary-custom" id="addCompany">
                                Nueva Empresa
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label >Estado Sunat</label>
                                <select id="byProduction" class="form-control" placeholder="Selecciona un Estado">
                                    <option value="">Todos</option>
                                    <option value="1">En Producción</option>
                                    <option value="0">En Demo</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label >Estado Sistema </label>
                                <select id="byStatus" class="form-control" placeholder="Selecciona un Estado">
                                    <option value="">Todos</option>
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label for="">Empresa:</label>
                                <input type="text" class="form-control" id="byCompany">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="table-responsive">
                            <table id="tbl_data" class="dt-bootstrap4" style="width: 100%;">
                                <thead>
                                    <th>RUC - DENOMINACION - RAZON SOCIAL</th>
                                    <th>EMAIL DE EMPRESAS - TELEFONO</th>
                                    <th>FINANZAS</th>
                                    <th width="80px">PRODUCCIÓN</th>
                                    <th width="60px">ACTIVO</th>
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
    <div id="mdl_add_company" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">NUEVA EMPRESA</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <form role="form" data-toggle="validator" id="frm_add_client">
                        <div class="card content-overlay">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label> Nombre Administrador *</label>
                                            <input type="text" class="form-control" name="name" id="name" required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label> Email - Usuario *</label>
                                            <input type="email" class="form-control" name="email" id="email" required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label> Teléfono *</label>
                                            <input type="number" class="form-control" name="phone" id="phone" required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label> Contraseña *</label>
                                            <input type="password" class="form-control" name="password" id="password" required>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label> RUC *</label>
                                            <div class="cont-ruc">
                                                <input type="number" class="form-control" name="ruc" id="ruc" required>
                                                <span class="valid-ruc text-success"> RUC CORRECTO</span>
                                            </div>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" id="send" class="btn btn-primary-custom">
                                            Agregar Empresa
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
                'url': '/manage/company/dt',
                'type' : 'get',
                'data': function(d) {
                    d.status = $('#byStatus').val();
                    d.production = $('#byProduction').val();
                    d.company = $('#byCompany').val();
                }
            },
            'columns': [
                {
                    data: 'document',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            data = '<div>' + row.document + '</div><div> ' + row.trade_name + '</div><div><a href="/manage/company/'+ row.document +'" class="text-primary" style="font-size: 1.2em !important;">[Ingresar]</a></div>';
                        }
                        return data;
                    }
                },
                {
                    data: 'phone',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            var p2 =  row.phone2 != null ? row.phone2 : '';
                            data = row.email + ' - ' + row.phone + ' - ' + p2;
                        }
                        return data;
                    }
                },
                {
                    data: 'certificate',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            if (data != null) {
                                data = '<div class="text-center">'+row.expiration_certificate + '</div>';
                            } else {
                                data = '<div class="text-center">Certificado no Registrado</div>';
                            }
                        }
                        return data;
                    }
                },
                {
                    data: 'production',
                    render: function render(data, type, row, meta) {
                        if (type === 'display') {
                            if (data == '1') {
                                data = '<div class="text-center"><i class="fa fa-check text-success"></i><br><span>'+ row.production_at +'</span></div>';
                            } else {
                                data = '<div class="text-center"><i class="fa fa-close text-danger"></i></div>';
                            }
                        }
                        return data;
                    }
                },
                {
                    data: 'status',
                    render: function render(data, type, row, meta) {
                        if (type === 'display') {
                            if (data == '1') {
                                data = '<div class="text-center"><i class="fa fa-check text-success"></i></div>';
                            } else {
                                data = '<div class="text-center"><i class="fa fa-close text-danger"></i></div>';
                            }
                        }
                        return data;
                    }
                },
            ],
        });

        $('#addCompany').on('click', function() {
            clearData();
            $('#mdl_add_company').modal('show');
        });

        $("#frm_add_client").validator().on('submit', function(e) {
            if (e.isDefaultPrevented()) {
                toastr.warning('Debe completar todos los campos.');
            } else {
                e.preventDefault();
                let data = $('#frm_add_client').serialize();

                $.ajax({
                    url: '/manage/company/new/company',
                    type: 'post',
                    data: data + '&_token=' + '{{ csrf_token() }}',
                    dataType: 'json',
                    beforeSend: function() {
                        $('#send').attr('disabled');
                        let effect = '<div class="overlay effect">';
                        effect += '<i class="fa fa-refresh fa-spin">';
                        effect += '</div>';
                        $('.content-overlay').append(effect);
                    },
                    complete: function() {
                        $('.effect').remove();
                    },
                    success: function(response) {
                        if(response == true) {
                            $('#mdl_add_company').modal('hide');
                            toastr.success('Se agregó satisfactoriamente la nueva empresa');
                            $("#tbl_data").DataTable().ajax.reload();
                        } else {
                            console.log(response.responseText);
                            toastr.error('Ocurrio un error');
                        }
                    },
                    error: function(response) {
                        console.log(response.responseText);
                        toastr.error('Ocurrio un error');
                        $('#send').removeAttr('disabled');
                    }
                });
            }
        });

        function clearData() {
            $('#email').val('');
            $('#ruc').val('');
            $('#name').val('');
            $('#phone').val('');
            $('#password').val('');
        }

        $('body').on('keyup', '#ruc', function() {
            if($(this).val().length == 11) {
                $.ajax({
                    url: '/consult.ruc/' + $(this).val(),
                    type: 'post',
                    data: {
                        '_token': "{{ csrf_token() }}"
                    },
                    dataType: 'json',
                    success: function(response) {
                        if(response != 'No se encontro el ruc') {
                            $('.valid-ruc').addClass('active');
                        } else {
                            $('.valid-ruc').removeClass('active');
                        }
                    },
                    error: function(response) {

                    }
                });
            } else {
                $('.valid-ruc').removeClass('active');
            }
        });

        $('#byStatus').on('change', function() {
            tbl_data.ajax.reload();
        });

        $('#byProduction').on('change', function() {
            tbl_data.ajax.reload();
        });
        $('#byCompany').on('keyup', function() {
            tbl_data.ajax.reload();
        });
    </script>
@stop
