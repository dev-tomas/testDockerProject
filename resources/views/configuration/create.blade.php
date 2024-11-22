@extends('layouts.azia')
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header text-center">
                    <div class="row">
                        <div class="col-12">
                            <h3 class="card-title">NUEVO LOCAL</h3>
                        </div>
                        <div class="col-12">
                            <p>
                                La serie debe empezar con la letra F para FACTURAS y NOTAS asociadas,
                                B para BOLETAS DE VENTAS y sus NOTAS asociadas, R para comprobantes
                                de RETENCIÓN, P para comprobantes de PERCEPCIÓN.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="post" id="frmData" name="frmData">
                        <div class="row">
                            <div class="col-12 col-md-2">
                                <div class="form-group">
                                    <label>CÓDIGO:</label>
                                    <input type="number" class="form-control" name="code" id="code" value="0{{ $newCodeH }}">
                                </div>
                            </div>
                            <div class="col-12 col-md-8">
                                <div class="form-group">
                                    <label>NOMBRE CORTO:</label>
                                    <input type="text" class="form-control" name="description" id="description" required>
                                </div>
                            </div>
                            <div class="col-12 col-md-2">
                                <div class="form-group">
                                    <label>CÓDIGO SUNAT:</label>
                                    <input type="text" class="form-control" name="sunat_code" id="sunat_code" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label>UBIGEO INEI (Igual a la SUNAT, DISTRITO):</label>
                                    <div>
                                        <select name="ubigeo" id="ubigeo" class="form-control select2" required>
                                            <option value="">SELECCIONAR</option>
                                            @foreach($ubigeos as $u)
                                                <option value="{{$u->id}}">
                                                    {{$u->department}} - {{$u->department}} - {{$u->district}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>DIRECCIÓN EXACTA:</label>
                                    <input type="text" class="form-control" name="address" id="address" required>
                                </div>
                            </div>
                        </div>
                        <fieldset>
                            <div class="row">
                                <div class="col-12 text-center">
                                    <h2>DOCUMENTOS Y SERIES</h2>
                                </div>
                            </div>
                            <div class="correlatives correlatives_container">
                                <div class="row">
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label>Documento</label>
                                            <select class="form-control" name="document_type[]">
                                                <option value="">SELECCIONAR</option>
                                                @foreach ($typeVoucher as $tv)
                                                    <option value="{{ $tv->id }}" {{ $tv->id == 5 ? 'selected' : '' }}>{{ $tv->description }}</option>
                                                @endforeach
                                            <select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label>Serie</label>
                                            <input type="text" maxlength="8" class="form-control" name="serial_number[]" required>
                                        </div>
                                    </div>
                                        <div class="col-12 col-md-3">
                                            <div class="form-group">
                                                <label>Correlativo</label>
                                            <input class="form-control" type="number" name="correlative[]" value="000000" required>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <label>Contingencia?</label>
                                        <div class="form-group">
                                            <input name="contingency[]" type="checkbox" value="1" />
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-1">
                                        <label> </label>
                                        <div class="form-group">
                                            <button type="button" class="btn btn-danger-custom delete_correlative_temporal">Eliminar</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label>Documento</label>
                                            <select class="form-control" name="document_type[]">
                                                <option value="">SELECCIONAR</option>
                                                @foreach ($typeVoucher as $tv)
                                                    <option value="{{ $tv->id }}" {{ $tv->id == 3 ? 'selected' : '' }}>{{ $tv->description }}</option>
                                                @endforeach
                                            <select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label>Serie</label>
                                            <input type="text" maxlength="8" class="form-control" name="serial_number[]" required>
                                        </div>
                                    </div>
                                        <div class="col-12 col-md-3">
                                            <div class="form-group">
                                                <label>Correlativo</label>
                                            <input class="form-control" type="number" name="correlative[]" value="000000" required>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <label>Contingencia?</label>
                                        <div class="form-group">
                                            <input name="contingency[]" type="checkbox" value="1" />
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-1">
                                        <label> </label>
                                        <div class="form-group">
                                            <button type="button" class="btn btn-danger-custom delete_correlative_temporal">Eliminar</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label>Documento</label>
                                            <select class="form-control" name="document_type[]">
                                                <option value="">SELECCIONAR</option>
                                                @foreach ($typeVoucher as $tv)
                                                    <option value="{{ $tv->id }}" {{ $tv->id == 2 ? 'selected' : '' }}>{{ $tv->description }}</option>
                                                @endforeach
                                            <select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label>Serie</label>
                                            <input type="text" maxlength="8" class="form-control" name="serial_number[]" required>
                                        </div>
                                    </div>
                                        <div class="col-12 col-md-3">
                                            <div class="form-group">
                                                <label>Correlativo</label>
                                            <input class="form-control" type="number" name="correlative[]" value="000000" required>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <label>Contingencia?</label>
                                        <div class="form-group">
                                            <input name="contingency[]" type="checkbox" value="1" />
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-1">
                                        <label> </label>
                                        <div class="form-group">
                                            <button type="button" class="btn btn-danger-custom delete_correlative_temporal">Eliminar</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label>Documento</label>
                                            <select class="form-control" name="document_type[]">
                                                <option value="">SELECCIONAR</option>
                                                @foreach ($typeVoucher as $tv)
                                                    <option value="{{ $tv->id }}" {{ $tv->id == 4 ? 'selected' : '' }}>{{ $tv->description }}</option>
                                                @endforeach
                                            <select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label>Serie</label>
                                            <input type="text" maxlength="8" class="form-control" name="serial_number[]" required>
                                        </div>
                                    </div>
                                        <div class="col-12 col-md-3">
                                            <div class="form-group">
                                                <label>Correlativo</label>
                                            <input class="form-control" type="number" name="correlative[]" value="000000" required>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <label>Contingencia?</label>
                                        <div class="form-group">
                                            <input name="contingency[]" type="checkbox" value="1" />
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-1">
                                        <label> </label>
                                        <div class="form-group">
                                            <button type="button" class="btn btn-danger-custom delete_correlative_temporal">Eliminar</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label>Documento</label>
                                            <select class="form-control" name="document_type[]">
                                                <option value="">SELECCIONAR</option>
                                                @foreach ($typeVoucher as $tv)
                                                    <option value="{{ $tv->id }}" {{ $tv->id == 6 ? 'selected' : '' }}>{{ $tv->description }}</option>
                                                @endforeach
                                            <select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label>Serie</label>
                                            <input type="text" maxlength="8" class="form-control" name="serial_number[]" required>
                                        </div>
                                    </div>
                                        <div class="col-12 col-md-3">
                                            <div class="form-group">
                                                <label>Correlativo</label>
                                            <input class="form-control" type="number" name="correlative[]" value="000000" required>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <label>Contingencia?</label>
                                        <div class="form-group">
                                            <input name="contingency[]" type="checkbox" value="1" />
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-1">
                                        <label> </label>
                                        <div class="form-group">
                                            <button type="button" class="btn btn-danger-custom delete_correlative_temporal">Eliminar</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label>Documento</label>
                                            <select class="form-control" name="document_type[]">
                                                <option value="">SELECCIONAR</option>
                                                @foreach ($typeVoucher as $tv)
                                                    <option value="{{ $tv->id }}" {{ $tv->id == 1 ? 'selected' : '' }}>{{ $tv->description }}</option>
                                                @endforeach
                                            <select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label>Serie</label>
                                            <input type="text" maxlength="8" class="form-control" name="serial_number[]" required>
                                        </div>
                                    </div>
                                        <div class="col-12 col-md-3">
                                            <div class="form-group">
                                                <label>Correlativo</label>
                                            <input class="form-control" type="number" name="correlative[]" value="000000" required>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <label>Contingencia?</label>
                                        <div class="form-group">
                                            <input name="contingency[]" type="checkbox" value="1" />
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-1">
                                        <label> </label>
                                        <div class="form-group">
                                            <button type="button" class="btn btn-danger-custom delete_correlative_temporal">Eliminar</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label>Documento</label>
                                            <select class="form-control" name="document_type[]">
                                                <option value="">SELECCIONAR</option>
                                                @foreach ($typeVoucher as $tv)
                                                    <option value="{{ $tv->id }}" {{ $tv->id == 7 ? 'selected' : '' }}>{{ $tv->description }}</option>
                                                @endforeach
                                            <select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label>Serie</label>
                                            <input type="text" maxlength="8" class="form-control" name="serial_number[]" required>
                                        </div>
                                    </div>
                                        <div class="col-12 col-md-3">
                                            <div class="form-group">
                                                <label>Correlativo</label>
                                            <input class="form-control" type="number" name="correlative[]" value="000000" required>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <label>Contingencia?</label>
                                        <div class="form-group">
                                            <input name="contingency[]" type="checkbox" value="1" />
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-1">
                                        <label> </label>
                                        <div class="form-group">
                                            <button type="button" class="btn btn-danger-custom delete_correlative_temporal">Eliminar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button type="button" class="btn btn-secondary-custom btn-block addSerial">Agregar Serie</button>
                                </div>
                            </div>
                        </fieldset>
                        <div class="row"><div class="col-12"><br></div></div>
                        <fieldset>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <button class="btn btn-primary-custom btn-block">GRABAR LOCAL</button>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
@section('script_admin')
    <script>
        let voucherTypes = '';
        $.get('{{route("allVoucherTypes")}}', function(response) {
            $.each(response, function(index, column) {
                voucherTypes += '<option value="' + column.id + '">' + column.description + '</option>';
            });
        }, 'json');
        $('.select2').select2();
        $('.delete_correlative').click(function() {
            let row = $(this).parent().parent().parent();
            let id = row.find('.correlative_id').val();
            $.ajax({
                url: '{{route("deleteCorrelative")}}',
                type: 'get',
                data: {
                    'correlative_id': id
                },
                dataType: 'json',
                success: function(response) {
                    if(response === true) {
                        row.remove();
                        toastr.success('Se eliminó correctamente.');
                    }
                }
            });
        });

        $('.addSerial').click(function() {
            let data = '';
                data += '<div class="row">';
                    data += '<div class="col-12 col-md-3">';
                        data += '<div class="form-group">';
                            data += '<label>Documento</label>';
                            data += '<select class="form-control" name="document_type[]">';
                                data += '<option value="">SELECCIONAR</option>';
                                data += voucherTypes;
                            data += '<select>';
                        data += '</div>';
                    data += '</div>';
                    data += '<div class="col-12 col-md-3">';
                        data += '<div class="form-group">';
                            data += '<label>Serie</label>';
                            data += '<input type="text" maxlength="8" class="form-control" name="serial_number[]" required>';
                        data += '</div>';
                    data += '</div>';
                        data += '<div class="col-12 col-md-3">';
                            data += '<div class="form-group">';
                                data += '<label>Correlativo</label>';
                            data += '<input class="form-control" name="correlative[]" required value="000000">';
                        data += '</div>';
                    data += '</div>';
                    data += '<div class="col-12 col-md-2">';
                        data += '<label>Contingencia?</label>';
                        data += '<div class="form-group">';
                            data += '<input name="contingency[]" type="checkbox" value="1" />';
                        data += '</div>';
                    data += '</div>';
                    data += '<div class="col-12 col-md-1">';
                        data += '<label> </label>';
                        data += '<div class="form-group">';
                            data += '<button type="button" class="btn btn-danger-custom delete_correlative_temporal">Eliminar</button>';
                        data += '</div>';
                    data += '</div>';
                data += '</div>';
            $('.correlatives_container').append(data);
        });

        $('body').on('click', '.delete_correlative_temporal', function() {
            console.log($(this).parent().parent().parent());
            $(this).parent().parent().parent().remove();
        });

        $('#frmData').validator().on('submit', function(e) {
            //alert('Hola');
            if(e.isDefaultPrevented()) {
                toastr.warning("Debe llenar todos los campos obligatorios");
            }else {
                e.preventDefault();
                let data = $('#frmData').serialize();
                $.ajax({
                    url: '{{route("saveHeadQuarter")}}',
                    type: 'post',
                    data: data + '&_token=' + '{{csrf_token()}}',
                    dataType: 'json',
                    beforeSend: function() {

                    },
                    complete: function() {

                    },
                    success: function(response) {
                        if(response === true) {
                            toastr.success('Se grabó satisfactoriamente el local.');
                            window.setInterval(
                                window.location = '{{route('configuration.headquarters')}}'
                                ,3000)
                        } else {
                            toastr.error("Ha ocurrido un error :(");
                        }
                    },
                    error: function(response) {
                        toastr.error("Ha ocurrido un error :(");
                    }
                });
            }
        });
    </script>
@stop
