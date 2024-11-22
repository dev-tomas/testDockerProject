@extends('layouts.azia')

@section('content')
    <form method="post" role="form" data-toggle="validator" id="frm_retention">
        <div class="row">
            <div class="col-12">
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title text-center">
                            NUEVA RETENCIÓN
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-5">
                                <div class="form-group">
                                    <label for="customer"> </label>
                                    <div class="input-group">
                                        <select name="customer" id="customer" class="form-control" style="width: 80%;">
                                            @foreach($customers as $c)
                                                <option value="{{$c->id}}">{{$c->description}}</option>
                                            @endforeach
                                        </select>
                                        <div class="input-group-append" id="openCustomer" style="cursor: pointer;">
                                            <button type="button" class="btn btn-primary-custom">
                                                NUEVO
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-2">
                                <div class="form-group">
                                    <label for="typevoucher">Serie</label>

                                    <select class="form-control" name="serial_number" id="serial_number">
                                        <option value="{{ $correlative->serialnumber }}">{{ $correlative->serialnumber }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-2">
                                <div class="form-group">
                                    <label for="typevoucher">Número (Referencial)</label>
                                    <input type="text" name="correlative" id="correlative" class="form-control" disabled value="{{$correlative->correlative}}">
                                </div>
                            </div>
                            <div class="col-12 col-md-3">
                                <div class="form-group">
                                    <label for="date">Fecha de Emisión</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                        </div>
                                        <input value="{{$currentDate}}" type="text" class="form-control datepicker" name="issue_create" id="issue_create" autocomplete="off" readonly="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-2">
                                <label for="typeoperation">Retención</label>
                                <select name="regime" id="regime" class="form-control">
                                    @foreach($regimes as $r)
                                        <option value="{{$r->id}}" rate="{{$r->rate}}">{{$r->description}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-2">
                                <label for="coin">Moneda</label>
                                <select name="coin" id="coin" class="form-control">
                                    @foreach($coins as $c)
                                        @if($c->id == 1)
                                            <option value="{{$c->description}}">{{$c->description}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 col-md-2">
                                <label for="exchange_rate">Tipo cambio</label>
                                <div class="form-group">
                                    <input type="text" class="form-control" name="change_type" id="exchange_rate">
                                </div>
                            </div>
                            <div class="col-12 col-md-1">
                                <input type="hidden" data-toggle="toggle" data-on="Si" data-off="No" id="paidout" name="paidout" value="1">
                            </div>
                        </div>

                        <fieldset>
                            <div class="row">
                                <div class="col-12 table-responsive">
                                    <table class="table" id="tbl_sales">
                                        <thead>
                                        <th width="400px">SERIE/CORRELATIVO</th>
                                        <th width="150px">FECHA EMISIÓN</th>
                                        <th width="90px">TOTAL</th>
                                        <th width="100px">PAGO SIN RETENCIÓN</th>
                                        <th width="100px">IMPORTE RETENIDO</th>
                                        <th width="100px">IMPORTE PAGADO</th>
                                        <th width="10px">*</th>
                                        </thead>
                                        <tbody class="table-sales">
                                        <tr>
                                            <td>
                                                <div class="input-group">
                                                    <select class="form-control sale" name="sale[]">
                                                        <option value="0">Seleccionar Factura</option>
                                                        @if($sales->count() > 0)
                                                            @foreach($sales as $s)
                                                                <option value="{{$s->id}}" issue="{{$s->issue}}" total="{{$s->total}}">
                                                                    {{$s->serialnumber}} - {{$s->correlative}}
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control issue" readonly="readonly" name="issue[]" required>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control total" readonly="readonly">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control no-retention" name="no_retention[]">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control retained" name="retained_amount[]">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control amount-paid" name="amount_paid[]">
                                            </td>
                                            <td>

                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-12">
                                    <button type="button" class="btn btn-primary-custom" id="btnAddVoucher">
                                        <i class="fa fa-plus-circle"></i>
                                        Agregar Comprobante
                                    </button>
                                </div>
                            </div>
                        </fieldset>

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="row">
                                    <div class="col-12">
                                        <label for="observation">Observaciones:</label>
                                        <div class="form-group">
                                            <textarea name="observation" id="observation" cols="30" rows="4" class="form-control"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table">
                                            <tbody>
                                                <tr>
                                                    <td>Sub Total</td>
                                                    <td><input type="text" class="form-control" id="subtotal" name="subtotal" readonly></td>
                                                </tr>
                                                <tr>
                                                    <td>Retención</td>
                                                    <td><input type="text" class="form-control" id="retained" name="retained" readonly></td>
                                                </tr>
                                                <tr>
                                                    <td>Total</td>
                                                    <td><input type="text" class="form-control" id="total" name="total" readonly></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary-custom btn-block" id="btnSaveRetention">GRABAR RETENCIÓN</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <form id="frm_cliente" method="post" class="form-horizontal" role="form" data-toggle="validator">
        <input type="hidden" id="validado" value="0">
        <div id="mdl_add_cliente" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
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
                                            <label for="description">Cliente *</label>
                                            <input id="description" name="description" type="text" class="form-control" required data-error="Este campo no puede estar vacío">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="phone">Teléfono</label>
                                            <input id="phone" name="phone" type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-12">
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
@stop

@section('script_admin')
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
                                clearDataCustomer();
                                $('#typedocument').val('');
                                getCustomers();
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

        function getCustomers()
        {
            $.get('/commercial.customer.all/' + '1', function(response) {
                $('#customer').html('');
                $('#customer').select2('destroy');
                let option = '';
                for (let i = 0;i < response.length; i++) {
                    option += '<option value="' + response[i].id + '">' + response[i].description + '</option>';
                }

                $('#customer').append(option);
                $('#customer').select2();
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
                    clearDataCustomer();
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

        $('#tbl_sales').on('change', '.sale', function() {
            recalculate();
        });

        function recalculate()
        {
            let subtotal = 0;
            let retained_final = 0;
            let total_final = 0;
            $('#tbl_sales tbody tr').each(function() {
                let tr = $(this);
                let total = 0.00;
                let issue = 0.00;
                let rate = 0.00;

                if($(tr).find('.sale').val() === '0') {
                    total = 0.00;
                    issue = '';
                    rate = 0.00;
                } else {
                    total = $('option:selected', $(tr).find('.sale')).attr('total');
                    issue = $('option:selected', $(tr).find('.sale')).attr('issue');
                    rate = $('option:selected', $('#regime')).attr('rate');
                }

                let rate_final = (total * rate) / 100;
                let amount_paid = total - rate_final;

                $(tr).find('.total').val(total);
                $(tr).find('.issue').val(issue);
                $(tr).find('.no-retention').val(total);
                $(tr).find('.retained').val(rate_final);
                $(tr).find('.amount-paid').val(amount_paid);



                total_final += total * 1;
                retained_final += rate_final;
                subtotal += amount_paid;
            });

            $('#subtotal').val(total_final.toFixed(2));
            $('#retained').val(retained_final.toFixed(2));
            $('#total').val(subtotal.toFixed(2));
        }

        $('#btnAddVoucher').click(function() {
            let tr = '';
            tr += '<tr>';
                tr += '<td>' + getSelect() + '</td>';
                tr += '<td><input type="text" class="form-control issue" readonly="readonly" name="issue[]" required></td>';
                tr += '<td><input type="text" class="form-control total" readonly="readonly"></td>';
                tr += '<td><input type="text" class="form-control no-retention" name="no_retention[]"></td>';
                tr += '<td><input type="text" class="form-control retained" name="retained_amount[]"></td>';
                tr += '<td><input type="text" class="form-control amount-paid" name="amount_paid[]"></td>';
                tr += '<td><button type="button" class="btn btn-danger-custom btn-xs remove"><i class="fa fa-trash"></i></button></td>';
            tr += '</tr>';
            $('#tbl_sales tbody').append(tr);
        });

        $('body').on('click', '.remove', function() {
            $(this).parent().parent().remove();
            recalculate();
        });

        let sales = '';
        function getSelect() {
            y = sales;
            $.ajax({
                'type': 'get',
                'url': '/ajax/sales/1',
                success: function(response) {
                    select = y;
                    select += '<select class="form-control sale" name="sale[]">';
                    select += '<option value="0">Seleccionar Factura</option>';
                    for (let i = 0; i < response.length; i++) {
                        select += '<option value="' + response[i].id + '" issue="' + response[i].issue + '" total="' + response[i].total + '">';
                        select += response[i].serialnumber + '-' + response[i].correlative;
                        select += '</option>;';
                    }
                    select += '</select>';
                    return select;
                }
            });

            return select;
        }

        $('#frm_retention').on('submit', function(e) {
            if(e.isDefaultPrevented()) {
                toastr.info('Debe completar todos los campos');
            } else {
                e.preventDefault();
                let data = $('#frm_retention').serialize();

                if($('#tbl_sales tbody tr').length == 0) {
                    toastr.error('Debe seleccionar algún comprobante');
                    return false;
                }

                $.ajax({
                    url: '/retention/store',
                    type: 'post',
                    data: data + '&_token=' + '{{ csrf_token() }}',
                    dataType: 'json',
                    beforeSend: function() {
                        $('#btnSaveRetention').attr('disabled', true);
                    },
                    complete: function() {

                    },
                    success: function(response) {
                        if(response['response'] == true) {
                            toastr.success('Se grabó satisfactoriamente el Comprobante');
                            toastr.success('El comprobante fue enviado a Sunat satisfactoriamente');
                        } else if(response['response'] == -1) {
                            toastr.success('Se grabó satisfactoriamente el Comprobante');
                            toastr.warning('Ocurrió un error con el comprobante, reviselo y vuelva a enviarlo.');
                        } else if(response['response'] == -2) {
                            toastr.success('Se grabó satisfactoriamente el Comprobante');
                            toastr.error('El comprobante fue enviado a Sunat y fue rechazado automáticamente, vuelva a enviarlo manualmente');
                        } else if(response['response'] == -3) {
                            toastr.success('Se grabó satisfactoriamente el Comprobante');
                            toastr.info('El comprobante fue enviado a Sunat y fue validado con una observación.');
                        } else {
                            toastr.success('Se grabó satisfactoriamente el Comprobante');
                            toastr.error('Ocurrió un error desconocido,revise el comprobante.');
                        }

                        setTimeout(function() {
                            window.location = '/retentions';
                        }, 5000);
                    },
                    error: function(response) {
                        console.log(response.responseText);
toastr.error('Ocurrio un error');
                        $('#btnSaveRetention').removeAttr('disabled');
                    }
                });
            }
        });
    </script>
@stop
