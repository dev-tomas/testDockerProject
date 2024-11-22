@extends('layouts.azia')

@section('content')
<form id="frm_reference_guide" method="post" role="form" data-toggle="validator">
    <div class="row">
        <div class="col-12">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title text-center">
                        NUEVA GUÍA DE REMISIÓN
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-5">
                            <div class="form-group">
                                <label for="customer">Cliente</label>
                                <div class="input-group">
                                    <select name="customer" id="customer" class="form-control" style="width: 80%;" required>
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
                                <select class="form-control" name="serialnumber" id="serialnumber">
                                    <option value="">Selecciones una Serie</option>
                                    @foreach ($correlatives as $c)
                                        <option value="{{ $c->serialnumber }}" {{ $loop->first ? "selected" : "" }}>{{ $c->serialnumber }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-2">
                            <div class="form-group">
                                <label for="typevoucher">Número (Referencial)</label>
                                <input type="text" id="correlative" class="form-control" disabled>
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
                                    <input value="{{$currentDate}}" type="text" class="form-control" name="date" id="date" autocomplete="off" readonly="">
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="form-group">
                                <label>Tipo de Guía de Remisión</label>
                                <select name="typeGuide" id="typeGuide" class="form-control" required>
                                    <option value="1">GUÍA DE REMISIÓN REMITENTE</option>
                                    {{-- <option value="2">GUÍA DE REMISIÓN TRANSPORTISTA</option> --}}
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="accordion" id="accordionExample">
                                <div class="card">
                                    <div class="card-header" id="headingOne">
                                        <h2 class="mb-0">
                                            <button class="btn btn-white" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                DATOS DEL TRASLADO
                                            </button>
                                        </h2>
                                    </div>
                                    <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-12 col-md-2">
                                                    <div class="form-group">
                                                        <label>Fecha de Inicio de Traslado</label>
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">
                                                                    <i class="fa fa-calendar"></i>
                                                                </span>
                                                            </div>
                                                            <input required value="{{$currentDate}}" type="text" class="form-control datepicker" name="startTraslate" id="startTraslate" autocomplete="off" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-3">
                                                    <div class="form-group">
                                                        <label>Descripción de Motivo de Traslado</label>
                                                        <select name="motive" class="form-control" id="" required>
                                                            <option value="">Seleccione Descripción de Motivo</option>
                                                            <option value="9">Venta</option>
                                                            <option value="10">Compra</option>
                                                            <option value="1">Venta sujeta a confirmación de la misma empresa</option>
                                                            <option value="2">Traslado entre establecimientos</option>
                                                            <option value="3">Traslado de bienes para transformación</option>
                                                            <option value="4">Recojo de bienes</option>
                                                            <option value="5">Traslado por emisor itinerante</option>
                                                            <option value="6">Traslado zona primaria</option>
                                                            <option value="7">Venta con entrega a terceros</option>
                                                            <option value="8">Otras no incluida en los puntos anteriores.</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-2">
                                                    <div class="form-group">
                                                        <label>Modalidad de Traslado</label>
                                                        <select name="modality" class="form-control" id="modality" required>
                                                            <option value="">Seleccione Modalidad de traslado</option>
                                                            <option value="1">Transporte Privado</option>
                                                            <option value="2">Transporte Público</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-3">
                                                    <div class="form-group">
                                                        <label>Peso Bruto (KG)</label>
                                                        <input type="number" step="0.01" class="form-control" name="weight" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header" id="headingTwo">
                                        <h2 class="mb-0">
                                            <button class="btn btn-white" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                                                DATOS DEL DESTINATARIO
                                            </button>
                                        </h2>
                                    </div>
                                    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
                                        <div class="card-body">
                                            <div class="row content-overlaR">
                                                <div class="col-12 col-md-2">
                                                    <div class="form-group">
                                                        <label for="typedocument">Tipo Documento</label>
                                                        <select name="receiverTypeDoc" id="receiverTypeDoc" class="form-control" required>
                                                            <option value="">Seleccionar</option>
                                                            @if($typedocuments->count() > 0)
                                                                @foreach($typedocuments as $td)
                                                                    <option value="{{$td->id}}">{{$td->description}}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-2">
                                                    <div class="form-group">
                                                        <label>Documento de Indentidad</label>
                                                        <input type="number" class="form-control" name="receiverDoc" id="receiverDoc" required>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <div class="form-group">
                                                        <label>Apellido y Nombre, Denominación, Razón Social</label>
                                                        <input type="text" class="form-control" name="receiver" id="receiver" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header" id="headingThree">
                                        <h2 class="mb-0">
                                            <button class="btn btn-white" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
                                                DATOS DEL PUNTO DE PARTIDA Y PUNTO DE LLEGADA
                                            </button>
                                        </h2>
                                    </div>
                                    <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
                                        <div class="card-body">
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label>Ubigeo de Punto de Partida</label>
                                                    <select name="start_address_ubigeo" id="start_address_ubigeo" class="form-control select2" required style="width: 100%;">
                                                        <option value="">Seleccionar</option>
                                                        @foreach($ubigeo as $u)
                                                            <option value="{{$u->id}}">
                                                                {{$u->department}} - {{$u->province}} - {{$u->district}}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Dirección de Punto de Partida</label>
                                                    <input type="text" class="form-control" name="startingPointAddress" required>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label>Ubigeo Punto de Llegada</label>
                                                    <select name="arrival_address_ubigeo" id="arrival_address_ubigeo" class="form-control select2" required style="width: 100%;">
                                                        <option value="">Seleccionar</option>
                                                        @foreach($ubigeo as $u)
                                                            <option value="{{$u->id}}">
                                                                {{$u->department}} - {{$u->province}} - {{$u->district}}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Dirección de Punto de Llegada</label>
                                                    <input type="text" class="form-control" name="arrivalPointAddress" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header" id="headingFour">
                                        <h2 class="mb-0">
                                            <button class="btn btn-white" type="button" data-toggle="collapse" data-target="#collapseFour" aria-expanded="true" aria-controls="collapseFour">
                                                DATOS DEL TRANSPORTE
                                            </button>
                                        </h2>
                                    </div>
                                    <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#accordionExample">
                                        <div class="card-body">
                                            <div class="row content-overlaT">
                                                <div class="col-12 mb-2">
                                                    <label><strong>TRANSPORTISTA</strong></label>
                                                </div>
                                                <div class="col-12 col-md-2">
                                                    <div class="form-group">
                                                        <label for="typedocument">Tipo Documento</label>
                                                        <select name="transportTypeDoc" id="transportTypeDoc" class="form-control" required>
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
                                                <div class="col-12 col-md-3">
                                                    <div class="form-group">
                                                        <label>Documento</label>
                                                        <input type="number" class="form-control" name="transportDoc" id="transportDoc" required>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <div class="form-group">
                                                        <label>Nombre</label>
                                                        <input type="text" class="form-control" name="transportName" id="transportName" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 my-2">
                                                    <label><strong>VEHICULO</strong></label>
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <div class="form-group">
                                                        <label>Placa</label>
                                                        <input type="text" name="vehicle" class="form-control" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row content-overlaD">
                                                <div class="col-12 my-2">
                                                    <label><strong>CONDUCTOR</strong></label>
                                                </div>
                                                <div class="col-12 col-md-2">
                                                    <div class="form-group">
                                                        <label for="typedocument">Tipo Documento</label>
                                                        <select name="typeDocDriver" id="typeDocDriver" class="form-control" required>
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
                                                <div class="col-12 col-md-3">
                                                    <div class="form-group">
                                                        <label>Documento</label>
                                                        <input type="number" class="form-control" name="driverDoc" id="driverDoc" required>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-4">
                                                    <div class="form-group">
                                                        <label>Nombre</label>
                                                        <input type="text" class="form-control" name="driverName" id="driverName" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <fieldset class="my-3">
                        <div class="row">
                            <div class="col-12 my-3">
                                <div class="form-group">
                                    <label><input type="checkbox" name="asocsale" id="asocsale" value="1" {{ $sale == null ? '' : 'checked' }}> Asociar esta Guía de Remisión a un comprobante de venta</label>
                                </div>
                            </div>
                            <div class="col-4 my-2" id="cont-sales">
                                <select name="sales" id="sales" class="form-control select_2">
                                    <option value="">Seleccione un Comprobante de Venta</option>
                                    @foreach ($sales as $s)
                                        @if ($sale != null)
                                            <option value="{{ $s->id }}" {{ $sale->id == $s->id ? 'selected' : '' }} >{{ $s->serialnumber }} - {{ $s->correlative }}</option>
                                        @else
                                            <option value="{{ $s->id }}">{{ $s->serialnumber }} - {{ $s->correlative }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <small style="color: #fff;">Los Items se agregarán automaticamente al crear esta Guía de Remisión</small>
                            </div>
                            <div class="col-12 my-3" id="cont-newRow">
                                <button type="button" class="btn btn-primary-custom" id="btnAddProduct">
                                    <i class="fa fa-plus-circle"></i>
                                    Agregar Producto
                                </button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="table-responsive" id="cont-table">
                                <table class="table table-hovered">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th width="600px">Producto</th>
                                            <th width="600px">Cantidad</th>
                                            <th>*</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </fieldset>
                    <div class="row">
                        <div class="col-12">
                            <button class="btn btn-primary-custom" type="submit" id="btnGrabarGuia">Crear Guía de Remisión</button>
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
@endsection
@section('script_admin')
    <script>
        $(document).ready(function() {
            getCorrelative();
            $('.select_2').select2({width: 'element'});
            if( $('#asocsale').attr('checked') ) {
                $('#cont-sales').show();
                $('#cont-newRow').hide();
                $('#cont-table').hide();
            } else {
                $('#cont-sales').hide();
            }
            let gtP = new gp();
        });
        $('#serialnumber').change(function() {
            getCorrelative();
        });

        $('#asocsale').click(function() {
            if($('#asocsale').prop('checked')) {
                $('#cont-sales').show();
                $('#cont-newRow').hide();
                $('#cont-table').hide();
                $('#tbody').html('');
            } else {
                $('#cont-sales').hide();
                $('#cont-newRow').show();
                $('#cont-table').show();
                $('#sales').val('');
            }
        });

        function getCorrelative() {
            let serialnumber = $('#serialnumber').val();

            $.ajax({
                url: '/reference-guide/getcorrelative/' + serialnumber,
                type: 'post',
                data: {
                    '_token': "{{ csrf_token() }}"
                },
                dataType: 'json',
                success: function(response) {
                    $('#correlative').val(('00') + ((response.correlative)*1 + 1));
                },
                error: function(response) {
                    console.log(response);
                }
            });
        }

        $('#openCustomer').click(function() {
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
        function getCustomers()
        {
            $.get('/commercial.customer.all', function(response) {
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

        $('#transportDoc').on('keyup', function() {
            let url = '';
            if($('#transportTypeDoc').val() == 2) {
                if($(this).val().length == 8) {
                    url = '/consult.dni/' + $(this).val();
                    getCustomerTransport(url, $('#transportTypeDoc').val());
                }
            } else if($('#transportTypeDoc').val() == 4) {
                if($(this).val().length == 11) {
                    url = '/consult.ruc/' + $(this).val();
                    getCustomerTransport(url, $('#transportTypeDoc').val());
                }
            }
        });
        $('#transportTypeDoc').change(function() {
            $('#transportDoc').val('');
            $('#transportName').val('');
        });
        function getCustomerTransport(url, typedocument)
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
                    $('.content-overlaT').append(effect);
                },
                complete: function() {
                    $('.effect').remove();
                },
                success: function(response) {
                    if(typedocument == 2) {
                        $('#transportName').val(response['nombres'] + ' ' + response['apellidoPaterno'] + ' ' + response['apellidoMaterno']);
                        $('#validado').val(1);
                    } else {
                        $('#transportName').val(response['razonSocial']);
                        $('#validado').val(1);
                    }
                },
                error: function(response) {
                    clearDataCustomer();
                    toastr.info('El cliente no existe.');
                }
            });
        }
        $('#driverDoc').on('keyup', function() {
            let url = '';
            if($('#typeDocDriver').val() == 2) {
                if($(this).val().length == 8) {
                    url = '/consult.dni/' + $(this).val();
                    getDriverInfo(url, $('#typeDocDriver').val());
                }
            } else if($('#typeDocDriver').val() == 4) {
                if($(this).val().length == 11) {
                    url = '/consult.ruc/' + $(this).val();
                    getDriverInfo(url, $('#typeDocDriver').val());
                }
            }
        });
        $('#typeDocDriver').change(function() {
            $('#driverDoc').val('');
            $('#driverName').val('');
        });
        function getDriverInfo(url, typedocument)
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
                    $('.content-overlaD').append(effect);
                },
                complete: function() {
                    $('.effect').remove();
                },
                success: function(response) {
                    if(typedocument == 2) {
                        $('#driverName').val(response['nombres'] + ' ' + response['apellidoPaterno'] + ' ' + response['apellidoMaterno']);
                        $('#validado').val(1);
                    } else {
                        $('#driverName').val(response['razonSocial']);
                        $('#validado').val(1);
                    }
                },
                error: function(response) {
                    clearDataCustomer();
                    toastr.info('El cliente no existe.');
                }
            });
        }
        $('#receiverDoc').on('keyup', function() {
            let url = '';
            if($('#receiverTypeDoc').val() == 2) {
                if($(this).val().length == 8) {
                    url = '/consult.dni/' + $(this).val();
                    getReciverInfo(url, $('#receiverTypeDoc').val());
                }
            } else if($('#receiverTypeDoc').val() == 4) {
                if($(this).val().length == 11) {
                    url = '/consult.ruc/' + $(this).val();
                    getReciverInfo(url, $('#receiverTypeDoc').val());
                }
            }
        });
        $('#receiverTypeDoc').change(function() {
            $('#receiverDoc').val('');
            $('#receiver').val('');
        });
        function getReciverInfo(url, typedocument)
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
                    $('.content-overlaR').append(effect);
                },
                complete: function() {
                    $('.effect').remove();
                },
                success: function(response) {
                    if(typedocument == 2) {
                        $('#receiver').val(response['nombres'] + ' ' + response['apellidoPaterno'] + ' ' + response['apellidoMaterno']);
                        $('#validado').val(1);
                    } else {
                        $('#receiver').val(response['razonSocial']);
                        $('#validado').val(1);
                    }
                },
                error: function(response) {
                    clearDataCustomer();
                    toastr.info('El cliente no existe.');
                }
            });
        }

        let products = '';
        function gp() {
            y = products;
            $.ajax({
                url: '/commercial.quotations.products',
                type: 'post',
                data: '&_token=' + '{{ csrf_token() }}',
                dataType: 'json',
                success: function(response) {
                    j = y;
                    for (var i = 0; i < response.length; i++) {
                        j += '<option value="' + response[i].id + '" p-stock="' + response[i].stock + '" p-price="' + response[i].price + '" p-otype="' + response[i].operation_type + '">' + response[i].description + '</option>';
                    }

                    return j;
                },
                error: function(response) {
                    console.log(response.responseText);
toastr.error('Ocurrio un error');
                }
            });

            return j;
        }

        $('#btnAddProduct').on('click', function() {
            let data = `
                <tr>
                    <td>
                            <select class="form-control select_2  c_product" id="c_product" name="cd_product[]" required>
                            <option value="">Seleccionar Producto</option>
                            `
                            + gp() +
                            `
                            </select>
                    </td>
                    <td>
                        <input type="number" class="form-control c_quantity" name="cd_quantity[]" value="1"/>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger-custom remove"><i class="fa fa-close"></i></button>
                    </td>
                </tr>
            `;
            $('#tbody').append(data);

            $('.select_2').select2({width: 'element'});
        });




        /**@argument
        */
        $('#frm_reference_guide').validator().on('submit', function(e) {
            if(e.isDefaultPrevented()) {
                toastr["warning"]("Debe llenar todos los campos obligatorios");
            }else {
                e.preventDefault();
                let data = $('#frm_reference_guide').serialize();

                $.confirm({
                    icon: 'fa fa-question',
                    theme: 'modern',
                    animation: 'scale',
                    type: 'green',
                    title: '¿Está seguro de crear esta Guía de Remision?',
                    content: '',
                    buttons: {
                        Confirmar: {
                            text: 'Confirmar',
                            btnClass: 'btn-green',
                            action: function(){
                                $.confirm({
                                    icon: 'fa fa-check',
                                    title: 'Guia de Remision Creada correctamente',
                                    theme: 'modern',
                                    type: 'green',
                                    draggable: false,
                                    buttons: {
                                        Cerrar: {
                                            text: 'Cerrar',
                                            btnClass: 'btn-red',
                                            action: function(){
                                                window.location = '/reference-guide/';
                                            }
                                        },
                                    },
                                    content: function () {
                                        var self = this;
                                        return $.ajax({
                                            url: '/reference-guide/store',
                                            type: 'post',
                                            data: data + '&_token=' + '{{ csrf_token() }}',
                                            dataType: 'json',
                                            beforeSend: function() {
                                                $('#btnGrabarGuia').attr('disabled');
                                            },
                                            complete: function() {

                                            },
                                            success: function(response) {
                                                if(response == true) {
                                                    toastr.success('Se grabó satisfactoriamente el Comprobante');
                                                    toastr.success('El comprobante fue enviado a Sunat satisfactoriamente');
                                                } else if(response == -1) {
                                                    toastr.success('Se grabó satisfactoriamente el Comprobante');
                                                    toastr.warning('Ocurrió un error con el comprobante, reviselo y vuelva a enviarlo.');
                                                } else if(response == -2) {
                                                    toastr.success('Se grabó satisfactoriamente el Comprobante');
                                                    toastr.error('El comprobante fue enviado a Sunat y fue rechazado automáticamente, vuelva a enviarlo manualmente');
                                                } else if(response == -3) {
                                                    toastr.success('Se grabó satisfactoriamente el Comprobante');
                                                    toastr.info('El comprobante fue enviado a Sunat y fue validado con una observación.');
                                                } else {
                                                    toastr.success('Se grabó satisfactoriamente el Comprobante');
                                                    toastr.error('Ocurrió un error desconocido,revise el comprobante.');
                                                }

                                                // setTimeout(function() {
                                                //     window.location = '/reference-guide/';
                                                // }, 5000);
                                            },
                                            error: function(response) {
                                                console.log(response.responseText);
toastr.error('Ocurrio un error');
                                                $('#btnGrabarGuia').removeAttr('disabled');
                                            }
                                        });
                                    }
                                });
                            }
                        },
                        Cancelar: {
                            text: 'Cancelar',
                            btnClass: 'btn-red',
                            action: function(){
                            }
                        }
                    }
                });
            }
        });
        $('.select2').select2();
    </script>
@endsection
