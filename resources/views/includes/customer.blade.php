@inject('tds', 'App\Http\Controllers\AjaxController')
<form id="frm_cliente" method="post" class="form-horizontal" role="form" data-toggle="validator">
    <input type="hidden" id="validado" value="0">
    <div id="mdl_add_cliente" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Agregar Cliente</h4>
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
                                            @if($tds::getTypeDocuments()->count() > 0)
                                                @foreach($tds::getTypeDocuments() as $td)
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
                                        <label for="description">Cliente *</label>
                                        <input id="description" name="description" type="text" class="form-control" required data-error="Este campo no puede estar vacío">
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="code">Código de Cliente *</label>
                                        <input id="code" name="code" type="text" class="form-control" required data-error="Este campo no puede estar vacío">
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
                                        <button id="btnGrabarCliente" type="submit" class="btn btn-primary-custom pull-right"> CREAR CLIENTE</button>
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
@section('script_includes')
    <script>
        $('#openCustomer').on('click', function() {
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
                    $('#code').attr('readonly', true);
                },
                error: function(response) {
                    toastr.error(response.responseText);
                }
            });

            $('#mdl_add_cliente').modal('show');
        });

        $("#frm_cliente").validator().on('submit', function(e) {
            if (e.isDefaultPrevented()) {
                toastr.warning('Debe llenar todos los campos obligatorios.');
            } else {
                e.preventDefault();
                let data = $('#frm_cliente').serialize();

                if($('#validado').val() == 0){
                    toastr.error('El Cliente que intenta registrar no está validado');
                } else {
                    $.ajax({
                        url: '/commercial.customer.create',
                        type: 'post',
                        data: data + '&_token=' + '{{ csrf_token() }}',
                        dataType: 'json',
                        beforeSend: function() {
                            $('#btnGrabarCliente').attr('disabled');
                        },
                        complete: function() {

                        },
                        success: function(response) {
                            if(response == true) {
                                toastr.success('Se grabó satisfactoriamente el cliente');
                                $('#typedocument').val('');
                                getCustomers($('#description').val(), $('#document').val());
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
                            $('#btnGrabarCliente').removeAttr('disabled');
                        }
                    });
                }
            }
        });
        $('#document').on('keyup', function() {
            let url = '';
            if($('#typedocument').val() == 2) {
                if($(this).val().length == 8) {
                    url = '/consult.dni/' + $(this).val();
                    getCustomer(url, $('#typedocument').val());
                }
            } else if($('#typedocument').val() == 4) {
                if($(this).val().length == 11) {
                    url = '/consult.ruc/' + $(this).val();
                    getCustomer(url, $('#typedocument').val());
                }
            }
        });

        function getCustomers(name, document)
        {
            $.get('/commercial.customer.all/' + $('#typeVoucher').val(), function(response) {
                $('#customer').html('');
                let option = '';
                for (let i = 0;i < response.length; i++) {
                    option += '<option value="' + response[i].id + '">' + response[i].description + ' - ' + response[i].document + '</option>';
                }

                let nam_doc = name + ' - ' + document;
                $('#customer').append(option);
                $('#customer option:contains(' + nam_doc + ')').attr('selected', true);
            }, 'json');
        }

        function getCustomer(url, typedocument)
        {
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
                        $('#validado').val(1);
                    } else {
                        $('#description').val(response['razonSocial']);
                        $('#phone').val(response['telefonos']);
                        $('#address').val(response['direccion'] + ' - ' + response['provincia'] + ' - ' + response['distrito']);
                        $('#validado').val(1);
                    }
                },
                error: function(response) {
                    $('#validado').val(1);
                    toastr.info('El cliente no existe.');
                }
            });
        }

        function clearDataCustomer()
        {
            $('#document').val('');
            $('#description').val('');
            $('#phone').val('');
            $('#address').val('');
            $('#email').val('');
        }
    </script>
@endsection