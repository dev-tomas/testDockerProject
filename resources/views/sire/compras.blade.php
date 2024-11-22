@extends('layouts.azia')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header bg-success text-white" style="background-color: #a9c242 !important;">
            <h4 class="mb-0">Registro de Compras Electrónico</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('sunat.resumencompras') }}" method="GET" id="form-filter">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-5">
                        <label for="anio">Año:</label>
                        <select class="form-control" name="anio" id="anio">
                            <option value="">Seleccionar</option>
                            @foreach ($periodos as $year => $ejercicio)
                                <option value="{{ $year }}">{{ $year }} - {{ $ejercicio['desEstado'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-5">
                        <label for="periodo">Mes:</label>
                        <select class="form-control" name="periodo" id="periodo">
                            <option value="">Seleccionar</option>
                            <!-- Los meses se llenarán dinámicamente -->
                        </select>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-success w-100" style="background-color: #a9c242;">Aceptar</button>
                    </div>
                </div>
            </form>

            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="generacion-tab" data-toggle="tab" href="#generacion" role="tab" aria-controls="generacion" aria-selected="true">Generación de Registro</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="gestion-tab" data-toggle="tab" href="#gestion" role="tab" aria-controls="gestion" aria-selected="false">Información de Gestión</a>
                </li>
            </ul>

            <div class="tab-content mt-3" id="myTabContent">
                <div class="tab-pane fade show active" id="generacion" role="tabpanel" aria-labelledby="generacion-tab">
                    <div class="d-flex justify-content-around mb-3">
                        <button class="btn btn-outline-success text-white" style="background-color: #a9c242 !important";>Resumen Propuesta RCE</button>
                        <button class="btn btn-outline-success text-white" style="background-color: #a9c242 !important">Propuesta del RCE</button>
                        <button class="btn btn-outline-success text-white" style="background-color: #a9c242 !important">Preliminar del RCE</button>
                        <button class="btn btn-outline-success text-white" style="background-color: #a9c242 !important">Inconsistencias</button>
                        <button class="btn btn-outline-success text-white" style="background-color: #a9c242 !important">Generación del RCE</button>
                        <button class="btn btn-outline-success text-white" style="background-color: #a9c242 !important">Gestión de Ajustes Posteriores</button>
                    </div>
                </div>
                <div class="tab-pane fade" id="gestion" role="tabpanel" aria-labelledby="gestion-tab">
                    <!-- Aquí se incluirá la información de gestión si es necesario -->
                </div>
            </div>

            <!-- JavaScript para cargar los meses según el año seleccionado -->
            <script>
                document.getElementById('anio').addEventListener('change', function() {
                    var anioSeleccionado = this.value;
                    var mesesSelect = document.getElementById('periodo');
                    mesesSelect.innerHTML = '<option value="">Seleccionar</option>'; // Limpiar meses
            
                    @foreach ($periodos as $year => $ejercicio)
                        if (anioSeleccionado === '{{ $year }}') {
                            @foreach ($ejercicio['lisPeriodos'] as $periodo)
                                @php
                                    $mes = substr($periodo['perTributario'], -2); // Obtener solo los últimos dos dígitos (el mes)
                                    $nombreMes = '';
                                    switch ($mes) {
                                        case '01': $nombreMes = 'Enero'; break;
                                        case '02': $nombreMes = 'Febrero'; break;
                                        case '03': $nombreMes = 'Marzo'; break;
                                        case '04': $nombreMes = 'Abril'; break;
                                        case '05': $nombreMes = 'Mayo'; break;
                                        case '06': $nombreMes = 'Junio'; break;
                                        case '07': $nombreMes = 'Julio'; break;
                                        case '08': $nombreMes = 'Agosto'; break;
                                        case '09': $nombreMes = 'Septiembre'; break;
                                        case '10': $nombreMes = 'Octubre'; break;
                                        case '11': $nombreMes = 'Noviembre'; break;
                                        case '12': $nombreMes = 'Diciembre'; break;
                                    }
                                @endphp
                                var option = document.createElement('option');
                                option.value = '{{ $mes }}';
                                option.text = '{{ $nombreMes }} - {{ $periodo['desEstado'] }}';
                                mesesSelect.add(option);
                            @endforeach
                        }
                    @endforeach
                });
            </script>

            @if (isset($data) && count($data) > 0)
                <div class="table-responsive mt-4">
                    <table class="table table-bordered">
                        <!--<thead>
                            <tr>
                                <th>Tipo de Documento</th>
                                <th>Total Documentos</th>
                                <th>Valor Facturado</th>
                                <th>Base Imponible</th>
                                <th>Dscto. Base Imponible</th>
                                <th>Monto Total IGV</th>
                                <th>Dscto. IGV</th>
                                <th>Importe Total Exonerado</th>
                                <th>Importe Total Inafecto</th>
                                <th>ISC</th>
                                <th>Otros Tributos/Cargos</th>
                                <th>Total CP</th>
                            </tr>
                        </thead>-->
                        <tbody>
                            @foreach ($data as $item)
                                <tr>
                                    <td>{{ $item['tipo_documento'] }}</td>
                                    <td>{{ $item['total_documentos'] }}</td>
                                    <td>{{ $item['BI_ Gravado_DG'] }}</td>
                                    <td>{{ $item['IGV/IPM_DG'] }}</td>
                                    <td>{{ $item['BI_Gravado_DGNG'] }}</td>
                                    <td>{{ $item['IGV/IPM_DGNG'] }}</td>
                                    <td>{{ $item['BI_Gravado_DNG'] }}</td>
                                    <td>{{ $item['IGV/IPM_DNG'] }}</td>
                                    <td>{{ $item['Valor_Adq._NG'] }}</td>
                                    <td>{{ $item['ISC'] }}</td>
                                    <td>{{ $item['ICBPER'] }}</td>
                                    <td>{{ $item['Otro_Trib/Cargos'] }}</td>
                                    <td>{{ $item['Total_CP'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @elseif(isset($data))
                <div class="alert alert-warning mt-4">
                    No se encontraron datos para el resumen seleccionado.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
