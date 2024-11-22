@extends('layouts.azia')
@section('css')
    @if (auth()->user()->info->type_theme == 1)
        <style>
            .btn-white {color: #202124 !important;}
            .btn-white:active {outline: none;}
            .card-body h3, .title  {color: #202124 !important;}
        </style>
    @endif
@endsection
@section('content')
    <input type="hidden" value="{{auth()->user()->headquarter->client->issue_with_previous_data}}" id="ipd" />
    <input type="hidden" value="{{auth()->user()->headquarter->client->issue_with_previous_data_days}}" id="ipnd" />
    <form id="frm_reference_guide" method="post" role="form" data-toggle="validator">
        <input type="hidden" value="{{ request()->sale }}" name="sale">
        <div class="row">
            <div class="col-12">
                <div class="card card-default">
                    <div class="card-header">
                        <h3 class="card-title text-center">
                            NUEVA GUÍA DE REMISIÓN REMITENTE
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-5">
                                <div class="form-group">
                                    <label for="customer">Cliente</label>
                                    <div class="input-group">
                                        <select name="customer" id="customer" style="width: 80%;" class="form-control">
                                            @foreach($customers as $c)
                                                <option value="{{$c->id}}" data-email="{{ $c->email }}">{{$c->document}} - {{$c->description}}</option>
                                            @endforeach
                                        </select>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                    <p id="customersMessageValidation"></p>
                                </div>
                            </div>
                            <input type="hidden" id="type_voucher" value="7">
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
                                        <input value="{{$currentDate}}" readonly type="text" class="form-control" name="date" id="date" autocomplete="off" readonly="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-2">
                                <div class="form-group">
                                    <label>Descripción de Motivo de Traslado</label>
                                    <select name="motive" class="form-control" id="motive" required>
                                        <option value="">Seleccione Descripción de Motivo</option>
                                        <option value="9">VENTA</option>
                                        <option value="10">COMPRA</option>
                                        <option value="2">TRASLADOS ENTRE ESTABLECIMIENTOS</option>
                                        <option value="8">OTROS</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-2">
                                <div class="form-group">
                                    <label>Modalidad de Traslado</label>
                                    <select name="modality" class="form-control" id="modality" required>
                                        <option value="">Seleccione Modalidad de traslado</option>
                                        <option value="01">TRANSPORTE PUBLICO</option>
                                        <option value="02">TRANSPORTE PRIVADO</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-2">
                                <div class="form-group">
                                    <label>Comprobantes</label>
                                    <select name="sale" class="form-control" id="sales" required>
                                        <option value="">Seleccione un Comprobante</option>
                                        @foreach($sales as $s)
                                            <option value="{{ $s->id }}">{{ $s->serialnumber }}-{{ $s->correlative }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-2" id="warehouses_destination_container">
                                <div class="form-group">
                                    <label>Almacén de Destino</label>
                                    <select name="warehouse_destination" class="form-control" id="warehouse_destination" required>
                                        <option value="">Seleccione un Almacén</option>
                                        @foreach($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}">{{ $warehouse->description }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <fieldset class="my-3">
                            @if($sale != null)
                                <div class="row">
                                    <div class="col-12">
                                        <p><strong>Documento Relacionado</strong> {{ $sale->type_voucher->description }} {{ $sale->serialnumber }} - {{ $sale->correlative }}</p>
                                    </div>
                                </div>
                            @endif
                            <div class="row">
                                <div class="table-responsive">
                                    <table class="table" id="table-details">
                                        <thead class="thead-light">
                                            <tr>
                                                <th width="600px">Producto</th>
                                                <th width="600px">Detalle Adicional</th>
                                                <th width="150px">Cantidad</th>
                                                <th width="50px">*</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody">
                                            @if ($sale != null)
                                                    @foreach ($sale->detail as $item)
                                                        <tr>
                                                            <td>
                                                                {{$item->product->internalcode}} - {{$item->product->description}}
                                                                <input type="hidden" value="{{ $item->product_id }}" name="cd_product[]"/>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control c_detail" name="cd_detail[]" value="{{ $item->details }}" />
                                                            </td>
                                                            <td>
                                                                <input type="number" step="0.00001" class="form-control c_quantity" name="cd_quantity[]" value="{{ $item->quantity }}"/>
                                                            </td>
                                                            <td>
                                                                <button type="button" class="btn btn-danger removeRow"><i class="fa fa-close"></i></button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 my-3" id="cont-newRow">
                                    <button type="button" class="btn btn-primary-custom pull-left" id="btnAddProduct">
                                        <i class="fa fa-plus-circle"></i>
                                        Agregar Producto
                                    </button>
                                </div>
                            </div>
                        </fieldset>
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
                                                                <input required value="{{$currentDate}}" type="text" class="form-control" name="startTraslate" id="startTraslate" autocomplete="off" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-2">
                                                        <div class="form-group">
                                                            <label>Peso Bruto</label>
                                                            <input type="number" step="0.001" class="form-control" name="weight" id="weight" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-2">
                                                        <div class="form-group">
                                                            <label>Unidad de Medida</label>
                                                            <select name="weight_measure" id="weight_measure" class="form-control" required>
                                                                <option value="">Selecciona una unidad de Medida</option>
                                                                <option value="KGM">KILOGRAMOS</option>
                                                                <option value="TNE">TONELADAS</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-2">
                                                        <div class="form-group">
                                                            <label>Número de Bultos</label>
                                                            <input type="number" step="0.01" class="form-control" name="lumps" id="lumps" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header" id="headingTransportist">
                                            <h2 class="mb-0">
                                                <button class="btn btn-white" type="button" data-toggle="collapse" data-target="#collapseTransportist" aria-expanded="true" aria-controls="collapseTransportist">
                                                    DATOS DEL TRANSPORTISTA
                                                </button>
                                            </h2>
                                        </div>
                                        <div id="collapseTransportist" class="collapse" aria-labelledby="headingTransportist" data-parent="#accordionExample">
                                            <div class="card-body">
                                                <div class="row content-overlaT">
                                                    <div class="col-12 col-md-2">
                                                        <div class="form-group">
                                                            <label for="typedocument">Tipo Documento</label>
                                                            <select name="transportTypeDoc" id="transportTypeDoc" class="form-control" required>
                                                                <option value="">Seleccionar</option>
                                                                @if($typedocuments->count() > 0)
                                                                    @foreach($typedocuments as $td)
                                                                        @if ($td->id == 4)
                                                                            <option value="{{$td->id}}">{{$td->description}}</option>
                                                                        @endif
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
                                                    <div class="col-12 col-md-3">
                                                        <div class="form-group">
                                                            <label>Vehículo - Placa</label>
                                                            <input type="text" name="vehicle" id="vehicle" class="form-control" placeholder="Ej.ABC123 o A0C123" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card" id="cardDriver">
                                        <div class="card-header" id="headingFour">
                                            <h2 class="mb-0">
                                                <button class="btn btn-white" type="button" data-toggle="collapse" data-target="#collapseFour" aria-expanded="true" aria-controls="collapseFour">
                                                    DATOS DEL CONDUCTOR
                                                </button>
                                            </h2>
                                        </div>
                                        <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#accordionExample">
                                            <div class="card-body">
                                                <div class="row content-overlaD">
                                                    <div class="col-12 col-md-4">
                                                        <div class="form-group">
                                                            <label for="typedocument">Tipo Documento</label>
                                                            <select name="typeDocDriver" id="typeDocDriver" class="form-control" required>
                                                                <option value="">Seleccionar</option>
                                                                @if($typedocuments->count() > 0)
                                                                    @foreach($typedocuments as $td)
                                                                        @if ($td->id != 4)
                                                                            <option value="{{$td->id}}">{{$td->description}}</option>
                                                                        @endif
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                            <div class="help-block with-errors"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-4">
                                                        <div class="form-group">
                                                            <label>Documento</label>
                                                            <input type="number" class="form-control" name="driverDoc" id="driverDoc" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-4">
                                                        <div class="form-group">
                                                            <label>Nombre Completo</label>
                                                            <input type="text" class="form-control" name="driverName" id="driverName" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" id="driverExtraData">
                                                    <div class="col-12 col-md-4">
                                                        <div class="form-group">
                                                            <label>Nombre del Conductor</label>
                                                            <input type="text" class="form-control" name="driver_firstname" maxlength="200" id="driver_firstname">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-4">
                                                        <div class="form-group">
                                                            <label>Apellidos del Conductor</label>
                                                            <input type="text" class="form-control" name="driver_familyname" maxlength="200" id="driver_familyname">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-4">
                                                        <div class="form-group">
                                                            <label>Licencia de Conducir</label>
                                                            <input type="text" class="form-control" name="driver_license" maxlength="10" id="driver_license" placeholder="Q12345678">
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
                                                <div class="row">
                                                    <div class="col-12 col-md-5">
                                                        <div class="form-group">
                                                            <label>Ubigeo de Punto de Partida</label>
                                                            <select name="start_address_ubigeo" id="start_address_ubigeo" class="form-control ubigeos2" required style="width: 100%;">
                                                                <option value="">Seleccionar</option>
                                                                @foreach($ubigeo as $u)
                                                                    <option value="{{$u->id}}">
                                                                        {{$u->department}} - {{$u->province}} - {{$u->district}}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-5">
                                                        <div class="form-group">
                                                            <label>Dirección Punto de Partida</label>
                                                            <input type="text" class="form-control" maxlength="100" name="start_address" id="start_address"  required>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-2">
                                                        <div class="form-group">
                                                            <label>Cod. Local Anexo de Partida</label>
                                                            <input type="text" class="form-control" maxlength="4" name="start_code" id="start_code" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12 col-md-5">
                                                        <div class="form-group">
                                                            <label>Ubigeo Punto de Llegada</label>
                                                            <select name="arrival_address_ubigeo" id="arrival_address_ubigeo" class="form-control ubigeos2" required style="width: 100%;">
                                                                <option value="">Seleccionar</option>
                                                                @foreach($ubigeo as $u)
                                                                    <option value="{{$u->id}}">
                                                                        {{$u->department}} - {{$u->province}} - {{$u->district}}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-5">
                                                        <div class="form-group">
                                                            <label>Dirección Punto de Llegada</label>
                                                            <input type="text" class="form-control" maxlength="100" name="arrival_address" id="arrival_address" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-2">
                                                        <div class="form-group">
                                                            <label>Cod. Local Anexo de Llegada</label>
                                                            <input type="text" class="form-control" maxlength="4" name="arrival_code" id="arrival_code"  required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label>Observaciones:</label>
                                                <textarea name="observations" id="observations" class="form-control"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-12">
                                <button class="btn btn-primary-custom btn-block" type="submit" id="btnGrabarGuia">Crear Guía de Remisión</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="modal fade" id="exportationModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">CÓDIGO DAM o DS</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul>
                        <li>Enviar primero la SERIE del código DAM o DS de 4 dígitos numéricos.</li>
                        <li>Posteriormente enviar el código DAM o DS.</li>
                    </ul>
                    <div class="row">
                        <div class="col-12">
                                <b>IMPORTANTE:</b>
                                <br>
                                Si el motivo de traslado es <b>IMPORTACIÓN</b>
                                debe registrar el siguiente formato en el formulario del código DAM o DS. <br><br>
                                a) Si el tipo de documento relacionado es <b>50 - Declaración Aduanera de Mercancías</b> el formato sería:<br>
                                xxxx(serie)/xxx-xxxx-10-xxxxxx(DAM) <br>
                                <b>Ejemplo:</b> 0001/123-1234-10-123456
                                <br><br>
                                b) Si el tipo de documento relacionado es <b>52 - Declaración Simplificada (DS)</b> el formato sería:<br>
                                xxxx(serie)/xxx-xxxx-18-xxxxxx(DS) <br>
                                <b>Ejemplo:</b> 0001/123-1234-18-123456
                                <br><br>
                                <hr>
                                <br>
                                Si el motivo de traslado es <b>EXPORTACIÓN</b>
                                debe registrar el siguiente formato en el formulario del código DAM o DS. <br><br>
                                a) Si el tipo de documento relacionado es <b>50 - Declaración Aduanera de Mercancías</b> el formato sería:<br>
                                xxxx(serie)/xxx-xxxx-40-xxxxxx(DAM) <br>
                                <b>Ejemplo:</b> 0001/123-1234-40-123456
                                <br><br>
                                b) Si el tipo de documento relacionado es <b>52 - Declaración Simplificada (DS)</b> el formato sería:<br>
                                xxxx(serie)/xxx-xxxx-48-xxxxxx(DS) <br>
                                <b>Ejemplo:</b> 0001/123-1234-48-123456
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script_admin')
    <script>
        $(document).ready(function() {
            $('#driverExtraData').hide();
            $('#warehouses_destination_container').hide();
            getCorrelative();
            $('.ubigeos2').select2();
            if( $('#asocsale').attr('checked') ) {
                $('#cont-sales').show();
                $('#motive').val(9);
                $('#motive').attr('readonly', true);
                $('#cont-newRow').hide();
                $('#cont-table').hide();
            } else {
                $('#cont-sales').hide();
                $('#motive').val('');
                $('#motive').attr('readonly', false);
            }
            $('.c_um').each(function(i, e) {
                $(e).attr('disabled', true);
            })
        });
        $('#serialnumber').change(function() {
            getCorrelative();
        });

        $('#asocsale').click(function() {
            if ($('#discount_stock').is(':checked')) {
                toastr.warning('Debe de desactivar la opción Descontar Stock del inventario para poder asociar un comprobante a esta Guía de Remisión.')
                return false;
            }
            if($('#asocsale').prop('checked')) {
                $('#cont-sales').show();
                $('#cont-newRow').hide();
                $('#cont-table').hide();
                $('#motive').val(9);
                $('#motive').attr('readonly', true);
                $('#tbody').html('');
            } else {
                $('#cont-sales').hide();
                $('#cont-newRow').show();
                $('#cont-table').show();
                $('#sales').val('');
                $('#motive').val('');
                $('#motive').attr('readonly', false);
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
                        $('#transportName').val(response['data']['nombre']);
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
                        $('#driverName').val(response['nombres']);
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

            if ($(this).val() == 2) {
                limitText('#receiverDoc', 8)
            } else if($(this).val() == 4) {
                limitText('#receiverDoc', 11)
            } else {
                $('#receiverDoc').removeAttr('maxlength');
            }
        });

        function limitText(field, maxChar){
            $(field).attr('maxlength',maxChar);
        }
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
                        $('#receiver').val(response['data']['nombre']);
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

        $('#btnAddProduct').on('click', function() {
            let data = `
                <tr>
                    <td>
                        <select class="form-control select_2  c_product" id="c_product" name="cd_product[]" required>
                            <option value="">Seleccionar Producto</option>
                            @foreach($products as $p)
                                <option value="{{$p->id}}">{{ $p->internalcode }} - {{$p->description}}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="text" class="form-control c_detail" name="cd_detail[]"/>
                    </td>
                    <td>
                        <input type="number" class="form-control c_quantity" name="cd_quantity[]" value="1"/>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger removeRow"><i class="fa fa-close"></i></button>
                    </td>
                </tr>
            `;
            $('#tbody').append(data);
            initailizeSelect2();
            if ($('#motive').val() == 11 || $('#motive').val() == 12) {
                $('.c_um').each(function(i, e) {
                    $(e).attr('disabled', false).removeClass('form-gray');
                })
                $('.c_dam').each(function(i, e) {
                    $(e).attr('readonly', false).removeClass('form-gray');
                })
            } else {
                $('.c_um').each(function(i, e) {
                    $(e).attr('disabled', true).addClass('form-gray');
                })
                $('.c_dam').each(function(i, e) {
                    $(e).attr('readonly', true).addClass('form-gray');
                })
            }
        });

        $('body').on('click', '.removeRow', function() {
            $(this).parent().parent().remove();
        });

        function initailizeSelect2(){
            $(".c_product").select2();
        }

        $('.c_product').select2();

        function validateInvoice() {
            let status = true

            let notValidateCustomer = ['2', '10'];

            const expresiones = {
                address: /^[a-zA-Z0-9 .-]{4,100}$/,
                placa: /^[0-9A-Z]{6,8}$/,
                licencia: /^[0-9A-Z]{9,10}$/,
                localCode: /^[0-9]{4}$/
            }

            if (notValidateCustomer.includes($('#motive').val()) == false) {
                if ($('#customer').val() == null) {
                    $('#customer').addClass('is-invalid')
                    let message = 'Debe de Seleccionar un Cliente';
                    addMessageValidation(message)
                    status = false;
                } else {
                    $('#customer').removeClass('is-invalid')
                }
            }

            if ($('#motive').val() == 2) {
                if ($('#warehouse_destination').val() == '') {
                    $('#warehouse_destination').addClass('is-invalid')
                    let message = 'Debe de Seleccionar un Almacén de destino';
                    addMessageValidation(message)
                    status = false;
                }
            }


            if ($('#tbody tr').length == 0) {
                let message = 'Debe agregar al menos un producto';
                addMessageValidation(message)
                status = false;
            }

            $('#tbody tr').each(function(index, tr) {
                if ($(tr).find('.c_quantity').val() == null) {
                    $(tr).find('.c_quantity').addClass('is-invalid');
                    let message = 'Debe de agregar cantidad.';
                    addMessageValidation(message)
                    status = false;
                } else {
                    $(tr).find('.c_quantity').removeClass('is-invalid');
                }

                if ($(tr).find('.c_quantity').val() <= 0 ) {
                    $(tr).find('.c_quantity').addClass('is-invalid');
                    let message = 'La cantidad debe de ser mayor a cero (0).';
                    addMessageValidation(message)
                    status = false;
                } else {
                    $(tr).find('.c_quantity').removeClass('is-invalid');
                }

                if ($('#motive').val() == 11) {
                    if ($(tr).find('.c_dam').val() == '' ) {
                        $(tr).find('.c_dam').addClass('is-invalid');
                        let message = 'DAM es obligatorio.';
                        addMessageValidation(message)
                        status = false;
                    } else {
                        $(tr).find('.c_dam').removeClass('is-invalid');
                    }

                    if ($(tr).find('.c_um').val() == '') {
                        $(tr).find('.c_um').addClass('is-invalid');
                        let message = 'La unidad de medida es obligatoria.';
                        addMessageValidation(message)
                        status = false;
                    } else {
                        $(tr).find('.c_um').removeClass('is-invalid');
                    }
                }
            })

            if ($('#weight_measure').val() == null) {
                $('#weight_measure').addClass('is-invalid')
                let message = 'Debe de Seleccionar una Unidad de Medida';
                addMessageValidation(message)
                status = false;
            } else {
                $('#weight_measure').removeClass('is-invalid')
            }

            if ($('#motive').val() == null || $('#motive').val() == '') {
                $('#motive').addClass('is-invalid')
                let message = 'Debe de Seleccionar un Motivo';
                addMessageValidation(message)
                status = false;
            } else {
                $('#motive').removeClass('is-invalid')
            }

            if ($('#modality').val() == null || $('#modality').val() == "") {
                $('#modality').addClass('is-invalid')
                let message = 'Debe de Seleccionar una Modalidad';
                addMessageValidation(message)
                status = false;
            } else {
                $('#modality').removeClass('is-invalid')
            }

            if ($('#weight').val() == null || $('#weight').val() == "" || $('#weight').val() <= 0.00) {
                $('#weight').addClass('is-invalid')
                let message = 'Debe de agregar peso bruto.';
                addMessageValidation(message)
                status = false;
            } else {
                $('#weight').removeClass('is-invalid')
            }

            if ($('#modality').val() == 2) {
                if ($('#vehicle').val() == null || $('#vehicle').val() == '') {
                    $('#vehicle').addClass('is-invalid')
                    let message = 'Debe de agregar una placa válida.';
                    addMessageValidation(message)
                    status = false;
                } else {
                    if (!expresiones.placa.test($('#vehicle').val())) {
                        $('#vehicle').addClass('is-invalid')
                        let message = 'Placa formato inválido. Maximo 8 caracteres. Ej. V1U123';
                        addMessageValidation(message)
                        status = false;
                    } else {
                        $('#vehicle').removeClass('is-invalid')
                    }
                }

                if ($('#typeDocDriver').val() == null || $('#typeDocDriver').val() == '') {
                    $('#typeDocDriver').addClass('is-invalid')
                    let message = 'Debe de seleccionar un tipo de documento valido.';
                    addMessageValidation(message)
                    status = false;
                } else {
                    $('#typeDocDriver').removeClass('is-invalid')
                }

                if ($('#driverDoc').val() == null || $('#driverDoc').val() == '') {
                    $('#driverDoc').addClass('is-invalid')
                    let message = 'Debe de agregar un documento valido.';
                    addMessageValidation(message)
                    status = false;
                } else {
                    $('#driverDoc').removeClass('is-invalid')
                }

                if ($('#driverName').val() == null || $('#driverName').val() == '') {
                    $('#driverName').addClass('is-invalid')
                    let message = 'Debe de agregar un nombre o razon social válido.';
                    addMessageValidation(message)
                    status = false;
                } else {
                    $('#driverName').removeClass('is-invalid')
                }


                if ($('#driver_firstname').val() == '') {
                    $('#driver_firstname').addClass('is-invalid')
                    let message = 'Debe de agregar el nombre del conductor.';
                    addMessageValidation(message)
                    status = false;
                } else {
                    $('#driver_firstname').removeClass('is-invalid')
                }

                if ($('#driver_familyname').val() == '') {
                    $('#driver_familyname').addClass('is-invalid')
                    let message = 'Debe de agregar el apellido del conductor.';
                    addMessageValidation(message)
                    status = false;
                } else {
                    $('#driver_familyname').removeClass('is-invalid')
                }

                if ($('#driver_license').val() == '') {
                    $('#driver_license').addClass('is-invalid')
                    let message = 'Debe de agregar la licencia del conductor del conductor.';
                    addMessageValidation(message)
                    status = false;
                } else {
                    if (! expresiones.licencia.test($('#driver_license').val())) {
                        $('#driver_license').addClass('is-invalid')
                        let message = 'Licencia de conducir formato inválido. Maximo 10 caracteres. Q12345678';
                        addMessageValidation(message)
                        status = false;
                    } else {
                        $('#driver_license').removeClass('is-invalid')
                    }
                    $('#driver_license').removeClass('is-invalid')
                }
            } else {
                if ($('#transportTypeDoc').val() == null || $('#transportTypeDoc').val() == "") {
                    $('#transportTypeDoc').addClass('is-invalid')
                    let message = 'Debe de seleccionar un tipo de documento valido.';
                    addMessageValidation(message)
                    status = false;
                } else {
                    $('#transportTypeDoc').removeClass('is-invalid')
                }

                if ($('#transportDoc').val() == null || $('#transportDoc').val() == '') {
                    $('#transportDoc').addClass('is-invalid')
                    let message = 'Debe de agregar un documento valido.';
                    addMessageValidation(message)
                    status = false;
                } else {
                    $('#transportDoc').removeClass('is-invalid')
                }

                if ($('#transportName').val() == null || $('#transportName').val() == '') {
                    $('#transportName').addClass('is-invalid')
                    let message = 'Debe de agregar un nombre o razon social válido.';
                    addMessageValidation(message)
                    status = false;
                } else {
                    $('#transportName').removeClass('is-invalid')
                }
            }

            if ($('#start_address_ubigeo').val() == null || $('#start_address_ubigeo').val() == '') {
                $('#start_address_ubigeo').addClass('is-invalid')
                let message = 'Debe de seleccionar un ubigeo válido.';
                addMessageValidation(message)
                status = false;
            } else {
                $('#start_address_ubigeo').removeClass('is-invalid')
            }

            if ($('#start_code').val() == null || $('#start_code').val() == '') {
                $('#start_code').addClass('is-invalid')
                let message = 'Debe de agregar el Cod. Local Anexo de Partida.';
                addMessageValidation(message)
                status = false;
            } else {
                if (! expresiones.localCode.test($('#start_code').val())) {
                    $('#start_code').addClass('is-invalid')
                    let message = 'Codigo de Local Anexo formato inválido. 4 caracteres. Ej. 0000 - 0001';
                    addMessageValidation(message)
                    status = false;
                } else {
                    $('#start_code').removeClass('is-invalid')
                }
                $('#start_code').removeClass('is-invalid')
            }

            if ($('#start_address').val() == null || $('#start_address').val() == '') {
                $('#start_address').addClass('is-invalid')
                let message = 'Debe de agregar una dirección válida.';
                addMessageValidation(message)
                status = false;
            } else {
                $('#start_address').removeClass('is-invalid')
            }

            let start_address = $('#start_address').val()

            if ($('#start_address').val() != null && start_address.length < 5 && start_address.length > 100) {
                $('#start_address').addClass('is-invalid')
                let message = 'Direccion no debe de tener mas de 100 caracteres.';
                addMessageValidation(message)
                status = false;
            } else {
                $('#start_address').removeClass('is-invalid')
            }

            if ($('#arrival_address_ubigeo').val() == null || $('#arrival_address_ubigeo').val() == '') {
                $('#arrival_address_ubigeo').addClass('is-invalid')
                let message = 'Debe de seleccionar un ubigeo válido.';
                addMessageValidation(message)
                status = false;
            } else {
                $('#arrival_address_ubigeo').removeClass('is-invalid')
            }

            if ($('#arrival_code').val() == null || $('#arrival_code').val() == '') {
                $('#arrival_code').addClass('is-invalid')
                let message = 'Debe de agregar el Cod. Local Anexo de Llegada.';
                addMessageValidation(message)
                status = false;
            } else {
                if (! expresiones.localCode.test($('#arrival_code').val())) {
                    $('#start_code').addClass('is-invalid')
                    let message = 'Codigo de Local Anexo formato inválido. 4 caracteres. Ej. 0000 - 0001';
                    addMessageValidation(message)
                    status = false;
                } else {
                    $('#arrival_code').removeClass('is-invalid')
                }
                $('#arrival_code').removeClass('is-invalid')
            }

            if ($('#arrival_address').val() == null || $('#arrival_address').val() == '') {
                $('#arrival_address').addClass('is-invalid')
                let message = 'Debe de agregar una dirección válida.';
                addMessageValidation(message)
                status = false;
            } else {
                $('#arrival_address').removeClass('is-invalid')
            }

            let arrival_address = $('#arrival_address').val()

            if ($('#arrival_address').val() != null && arrival_address.length < 5 && arrival_address.length > 100) {
                $('#arrival_address').addClass('is-invalid')
                let message = 'Direccion no debe de tener mas de 100 caracteres.';
                addMessageValidation(message)
                status = false;
            } else {
                $('#arrival_address').removeClass('is-invalid')
            }

            if ($('#motive').val() == 9) {
                let companyDoc = @json(auth()->user()->headquarter->client->document);
                if ($('#transportDoc').val() == companyDoc) {
                    $('#transportDoc').addClass('is-invalid')
                    let message = 'Transportista no debe ser igual al remitente o destinatario.';
                    addMessageValidation(message)
                    status = false;
                }
            }

            return status;
        }

        function addMessageValidation(message) {
            toastr.error(message);
        }

        /**@argument
         */
        $('#frm_reference_guide').validator().on('submit', function(e) {
            e.preventDefault()
            $('.c_um').each(function(i, e) {
                $(e).attr('disabled', false);
            })
            let status = validateInvoice()
            if (status == false) {
                return false;
            } else {
                let data = $('#frm_reference_guide').serialize();

                $.confirm({
                    icon: 'fa fa-question',
                    theme: 'modern',
                    animation: 'scale',
                    type: 'blue',
                    title: '¿Está seguro de crear esta Guía de Remision?',
                    content: '',
                    buttons: {
                        Confirmar: {
                            text: 'Confirmar',
                            btnClass: 'btn-green',
                            action: function(){
                                        var self = this;
                                        return $.ajax({
                                            url: '/reference-guide/store',
                                            type: 'post',
                                            data: data + '&_token=' + '{{ csrf_token() }}',
                                            dataType: 'json',
                                            timeout: 10000,
                                            beforeSend: function() {
                                                $('#btnGrabarGuia').attr('disabled', true);
                                            },
                                            complete: function() {

                                            },
                                            success: function(response) {
                                                if(response['response'] == true) {
                                                    toastr.success('Se grabó satisfactoriamente el Comprobante');
                                                    toastr.success('El comprobante fue enviado a Sunat satisfactoriamente');
                                                    window.location = '/reference-guide/';
                                                }else if (response["response"] == false) {
                                                    toastr.error(response["description"]);
                                                    toastr.warning('Ocurrió un error con el comprobante, reviselo y vuelva a enviarlo.');
                                                    $('#btnGrabarGuia').attr('disabled', false);
                                                } else if(response['response'] == -1) {
                                                    toastr.success('Se grabó satisfactoriamente el Comprobante');
                                                    toastr.warning('Ocurrió un error con el comprobante, reviselo y vuelva a enviarlo.');
                                                    window.location = '/reference-guide/';
                                                } else if(response['response'] == -2) {
                                                    toastr.success('Se grabó satisfactoriamente el Comprobante');
                                                    toastr.error('El comprobante fue enviado a Sunat y fue rechazado automáticamente, vuelva a enviarlo manualmente');
                                                    window.location = '/reference-guide/';
                                                } else if(response['response'] == -3) {
                                                    toastr.success('Se grabó satisfactoriamente el Comprobante');
                                                    toastr.info('El comprobante fue enviado a Sunat y fue validado con una observación.');
                                                    window.location = '/reference-guide/';
                                                } else if(response['response'] == -45) {
                                                    toastr.error(response["description"]);
                                                } else {
                                                    toastr.success('Se grabó satisfactoriamente el Comprobante');
                                                    toastr.error('Ocurrió un error desconocido,revise el comprobante.');
                                                }
                                                window.location = '/reference-guide/';
                                            },
                                            error: function (response, textstatus, message) {
                                                if(textstatus === "timeout") {
                                                    window.location = "/reference-guide";
                                                } else {
                                                    toastr.error(response.responseText);
                                                    $('#btnGrabarGuia').removeAttr('disabled');
                                                }
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
        let company = @json(auth()->user()->headquarter->client->trade_name);

        $('#motive').change(function() {
            if ($(this).val() == 2) {
                $('#customer').val('')
                $('#customer').attr('disabled', true)
                $('#customer').attr('required', false)
                $('#customersMessageValidation').html(`Para el motivo de seleccionado <b>Traslado entre establecimientos</b>, el destinatario será <b>${company}</b>`)
                $('.c_um').each(function(i, e) {
                    $(e).attr('disabled', true).addClass('form-gray');
                })
                $('.c_dam').each(function(i, e) {
                    $(e).attr('readonly', true).addClass('form-gray');
                })
                $('#doc_transport_merchandise_container').hide();
                $('#warehouses_destination_container').show();
            } else if ($(this).val() == 10) {
                $('#customer').val('')
                $('#customer').attr('disabled', true)
                $('#customer').attr('required', false)
                $('#customersMessageValidation').html(`Para el motivo de seleccionado <b>Compra</b>, el destinatario será <b>${company}</b>`)
                $('.c_um').each(function(i, e) {
                    $(e).attr('disabled', true).addClass('form-gray');
                })
                $('.c_dam').each(function(i, e) {
                    $(e).attr('readonly', true).addClass('form-gray');
                })
                $('#doc_transport_merchandise_container').hide();
                $('#warehouses_destination_container').hide();
            } else if ($(this).val() == 11 || $(this).val() == 12) {
                $('.c_um').each(function(i, e) {
                    $(e).attr('disabled', false).removeClass('form-gray');
                })
                $('.c_dam').each(function(i, e) {
                    $(e).attr('readonly', false).removeClass('form-gray');
                })
                $('#doc_transport_merchandise_container').show();
                $('#warehouses_destination_container').hide();
            } else {
                $('#customer').attr('disabled', false)
                $('#customer').attr('required', true)
                $('#customersMessageValidation').text('');
                $('.c_um').each(function(i, e) {
                    $(e).attr('disabled', true).addClass('form-gray');
                })
                $('.c_dam').each(function(i, e) {
                    $(e).attr('readonly', true).addClass('form-gray');
                })
                $('#doc_transport_merchandise_container').hide();
                $('#warehouses_destination_container').hide();
            }
        })

        $('#customer').select2();

        var date = new Date();

        $('#date').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
            startDate: date,
            endDate: date,
            language: 'es'
        });

        $('#startTraslate').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
            startDate: date,
            language: 'es'
        });

        $('#sales').change(function () {
            let sale = $(this).val();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                }
            });
            $.ajax({
                type: 'post',
                url: '/reference-guide/get-sale-detail',
                data: {sale: sale},
                dataType: 'json',
                success: function(response) {
                    $('#table-details tbody').html('')

                    $.each(response, function (idx, item) {
                        let tr = `
                            <tr>
                                <td>
                                    ${item.product}
                                    <input type="hidden" value="${item.product_id}" class="c_product " name="cd_product[]"/>
                                </td>
                                <td>
                                    <input type="text" class="form-control c_detail" name="cd_detail[]" value="${item.detail}" />
                                </td>
                                <td>
                                    <input type="number" min="0" step="0.00001" class="form-control c_quantity" name="cd_quantity[]" value="${item.quantity}"/>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger removeRow"><i class="fa fa-close"></i></button>
                                </td>
                            </tr>
                        `;

                        $('#table-details tbody').append(tr)
                    })
                },
                error: function(response) {
                    toastr.error('Ocurrió un error!');
                }
            });
        })

        $('#modality').change(function() {
            if ($(this).val() == 2) {
                $('#vehicle').removeClass('form-gray').attr('readonly', false).attr('required', true);
                $('#driverExtraData').show();
                $('#cardDriver').show();
                $('#transportTypeDoc').addClass('form-gray').attr('readonly', true).attr('required', false)
                $('#transportDoc').addClass('form-gray').attr('readonly', true).attr('required', false)
                $('#transportName').addClass('form-gray').attr('readonly', true).attr('required', false)
            } else {
                $('#vehicle').addClass('form-gray').attr('readonly', true).attr('required', false);
                $('#driverExtraData').hide();
                $('#cardDriver').hide();
                $('#transportTypeDoc').removeClass('form-gray').attr('readonly', false).attr('required', true)
                $('#transportDoc').removeClass('form-gray').attr('readonly', false).attr('required', true)
                $('#transportName').removeClass('form-gray').attr('readonly', false).attr('required', true)
            }
        })
    </script>
@endsection
