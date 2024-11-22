@extends('layouts.azia')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header bg-success text-white" style="background-color: #a9c242 !important;">
            <h4 class="mb-0">Registro de Ventas e Ingresos Electrónicos</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('sunat.resumen') }}" method="GET" id="form-filter">
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
                        <button type="submit" class="btn btn-success w-100" style="background-color: #a9c242 !important;">Aceptar</button>
                    </div>
                </div>
            </form>
            
            <button type="button" onclick="generarTicketPropuesta()" class="btn btn-outline-success text-white" style="background-color: #a9c242 !important;">
                Generar Ticket Propuesta
            </button>
            <button type="button" onclick="consultarEstadoTicket()" class="btn btn-outline-success text-white" style="background-color: #a9c242 !important;">
                Consultar Estado de Ticket
            </button>            

            <script>
            function generarTicketPropuesta() {
    const anio = document.getElementById('anio').value;
    const periodo = document.getElementById('periodo').value;

    if (!anio || !periodo) {
        alert("Por favor, selecciona un año y un mes antes de generar el ticket de propuesta.");
        return;
    }

    fetch(`/exportar-propuesta?anio=${anio}&periodo=${periodo}`)
        .then(response => {
            if (!response.ok) {
                throw new Error("No se pudo generar el ticket.");
            }
            return response.json();
        })
        .then(data => {
            // Aquí cambiamos de 'data.ticket' a 'data.numTicket'
            if (data.numTicket) {
                const mensaje = `
                    <p>Se ha generado un ticket para realizar la exportación. El número de ticket generado es <strong>${data.numTicket}</strong>, para verificar el estado del ticket puede consultarlo a través de la opción <strong>Estado de envío de ticket RVIE</strong>.</p>
                `;
                document.getElementById('mensajeModalContenido').innerHTML = mensaje;

                // Mostrar el modal
                const modal = new bootstrap.Modal(document.getElementById('mensajeInformativoModal'));
                modal.show();
            } else if (data.error) {
                alert(data.error);
            } else {
                alert("No se pudo generar el ticket. Verifica los datos y vuelve a intentarlo.");
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("Hubo un problema al generar el ticket.");
        });
}

            </script>
            <!-- Modal de Mensaje Informativo -->
            <!-- Modal -->
<div class="modal fade" id="mensajeInformativoModal" tabindex="-1" aria-labelledby="mensajeInformativoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #a9c242 !important; color: white;">
                <h5 class="modal-title" id="mensajeInformativoLabel">Mensaje Informativo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="mensajeModalContenido" style="font-size: 1.1rem; padding: 20px; text-align: center;">
                <!-- Aquí se llenará el contenido del mensaje dinámicamente -->
                <p style="font-weight: bold; color: #333;">Mensaje cargado dinámicamente.</p>
            </div>
        </div>
    </div>
</div>

  
            
            <!-- Pestañas de Generación de Registro e Información de Gestión en la misma fila -->
            <div class="d-flex mb-3">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="generacion-tab" data-toggle="tab" href="#generacion" role="tab" aria-controls="generacion" aria-selected="true">Generación de Registro</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="gestion-tab" data-toggle="tab" href="#gestion" role="tab" aria-controls="gestion" aria-selected="false">Información de Gestión</a>
                    </li>
                </ul>
            </div>

            <!-- Contenido de las pestañas -->
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="generacion" role="tabpanel" aria-labelledby="generacion-tab">
                    <div class="d-flex justify-content-around mb-3">
                        <button class="btn btn-outline-success text-white" onclick="mostrarTabla('tabla-datos')" style="background-color: #a9c242 !important;">Resumen de CP</button>
                        <button class="btn btn-outline-success text-white" style="background-color: #a9c242 !important;">Propuesta del RVIE</button>
                        <button class="btn btn-outline-success text-white" style="background-color: #a9c242 !important;">Preliminar del RVIE</button>
                        <button class="btn btn-outline-success text-white" style="background-color: #a9c242 !important;">Inconsistencias</button>
                        <button class="btn btn-outline-success text-white" style="background-color: #a9c242 !important;">Generación del RVIE</button>
                        <button class="btn btn-outline-success text-white" style="background-color: #a9c242 !important;">Gestión de Ajustes Posteriores</button>
                    </div>
                </div>
                <div class="tab-pane fade" id="gestion" role="tabpanel" aria-labelledby="gestion-tab">
                    <ul class="nav nav-tabs" id="gestionTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="consultas-tab" data-toggle="tab" href="#consultas" role="tab" aria-controls="consultas" aria-selected="true">Consultas y Descargas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="estadistica-tab" data-toggle="tab" href="#estadistica" role="tab" aria-controls="estadistica" aria-selected="false">Gestión de Información Estadística</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="reporte-tab" data-toggle="tab" href="#reporte" role="tab" aria-controls="reporte" aria-selected="false">Reporte de Cumplimiento</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="compras-tab" data-toggle="tab" href="#compras" role="tab" aria-controls="compras" aria-selected="false">Compras e Ingresos Para la DDJJ</a>
                        </li>
                    </ul>
                    <div class="tab-content mt-3" id="gestionTabContent">
                        <!-- Contenido de Consultas y Descargas -->
                        <div class="tab-pane fade show active" id="consultas" role="tabpanel" aria-labelledby="consultas-tab">
                            <ul>
                                <li><a href="#" onclick="mostrarTabla('estadoEnvioTicket')">Estado de envío de Ticket</a></li>
                            </ul>
                        </div>

                        <!-- Otras pestañas de gestión (contenido vacío para futuras funcionalidades) -->
                        <div class="tab-pane fade" id="estadistica" role="tabpanel" aria-labelledby="estadistica-tab"></div>
                        <div class="tab-pane fade" id="reporte" role="tabpanel" aria-labelledby="reporte-tab"></div>
                        <div class="tab-pane fade" id="compras" role="tabpanel" aria-labelledby="compras-tab"></div>
                    </div>
            </div>

            <!-- Tabla de Resumen -->
            <div id="tabla-datos" class="table-responsive mt-4" style="display: none;">
                @if (isset($data) && count($data) > 0)
                    <table class="table table-bordered">
                        <tbody>
                            @foreach ($data as $item)
                                <tr>
                                    <td>{{ $item['tipo_documento'] }}</td>
                                    <td>{{ $item['total_documentos'] }}</td>
                                    <td>{{ $item['valor_facturado_exportacion'] }}</td>
                                    <td>{{ $item['base_imponible_gravada'] }}</td>
                                    <td>{{ $item['dscto_base_imponible'] }}</td>
                                    <td>{{ $item['monto_total_igv'] }}</td>
                                    <td>{{ $item['dscto_igv'] }}</td>
                                    <td>{{ $item['importe_exonerada'] }}</td>
                                    <td>{{ $item['importe_inafecta'] }}</td>
                                    <td>{{ $item['isc'] }}</td>
                                    <td>{{ $item['otros_tributos'] }}</td>
                                    <td>{{ $item['total_cp'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="alert alert-warning mt-4">
                        No se encontraron datos para el resumen seleccionado.
                    </div>
                @endif
            </div>

            <!-- Tabla de Estado de Envío de Ticket -->
            <div id="estadoEnvioTicket" class="mt-4" style="display: none;">
                <h4>Estado de Envío de Ticket - RVIE</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Número de Ticket</th>
                            <th>Fecha Envío</th>
                            <th>Nombre Archivo</th>
                            <th>Tipo de Proceso</th>
                            <th>Estado Proceso</th>
                            
                            <th>Reportes y Descargas</th>
                        </tr>
                    </thead>
                    <tbody id="tablaEstadoTicket">
                        <!-- Aquí se llenará dinámicamente el estado del ticket -->
                    </tbody>
                </table>
            </div>            
        </div>
    </div>
</div>

<script>
    // Función para mostrar las tablas
    function mostrarTabla(tablaId) {
        // Oculta ambas tablas primero
        document.getElementById("tabla-datos").style.display = "none";
        document.getElementById("estadoEnvioTicket").style.display = "none";
        
        // Muestra solo la tabla correspondiente
        document.getElementById(tablaId).style.display = "block";
    }
    document.getElementById('anio').addEventListener('change', function() {
        var anioSeleccionado = this.value;
        var mesesSelect = document.getElementById('periodo');
        mesesSelect.innerHTML = '<option value="">Seleccionar</option>';

        @foreach ($periodos as $year => $ejercicio)
            if (anioSeleccionado === '{{ $year }}') {
                @foreach ($ejercicio['lisPeriodos'] as $periodo)
                    @php
                        $mes = substr($periodo['perTributario'], -2);
                        $nombreMes = [
                            '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', 
                            '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio',
                            '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre',
                            '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
                        ][$mes];
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

<script>
    function consultarEstadoTicket() {
        const anio = document.getElementById('anio').value;
        const mes = document.getElementById('periodo').value;

        if (!anio || !mes) {
            alert("Por favor, selecciona un año y un mes.");
            return;
        }

        const periodoConsulta = anio + mes;

        // Llamada fetch para consultar el estado del ticket
        fetch(`/consultar-estado-periodo?anio=${anio}&mes=${mes}`)
            .then(response => response.json())
            .then(data => {
                // Mostrar los datos obtenidos
                const tabla = document.getElementById('tablaEstadoTicket');
                tabla.innerHTML = ''; // Limpiar tabla

                if (data.registros && data.registros.length > 0) {
                    data.registros.forEach(item => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${item.numTicket || 'No disponible'}</td>
                            <td>${item.fecInicioProceso || 'No disponible'}</td>
                            <td>
                                ${item.archivoReporte ? item.archivoReporte.map(archivo => `<span>${archivo.nomArchivoContenido}</span>`).join(', ') : 'No disponible'}
                            </td>
                            <td>${item.desProceso || 'No disponible'}</td>
                            <td>${item.desEstadoProceso || 'No disponible'}</td>
                            
                            <td>
    ${item.archivoReporte ? item.archivoReporte.map(archivo => {
        const codTipoArchivoReporte = archivo.codTipoArchivoReporte || '01';

        // Dividir el nombre del archivo por el guion bajo y tomar la última parte
        const nombreVisible = archivo.nomArchivoReporte.split('_').pop();

        // Generar el enlace con el nombre visible y ajustado para la descarga
        return `<a href="/descargar-archivo?nomArchivoReporte=${archivo.nomArchivoReporte}&codTipoArchivoReporte=${codTipoArchivoReporte}" download="${nombreVisible}">${nombreVisible}</a>`;
    }).join(', ') : 'No disponible'}
</td>


                        `;
                        tabla.appendChild(row);
                    });
                } else {
                    tabla.innerHTML = '<tr><td colspan="7" class="text-center">No se encontraron registros.</td></tr>';
                }
                // Mostrar la tabla de estado del ticket
                document.getElementById('estadoEnvioTicket').style.display = 'block';
            })
            .catch(error => {
                console.error('Error al consultar el estado del ticket:', error);
                alert("Hubo un problema al consultar el estado del ticket.");
            });
    }

    document.getElementById('anio').addEventListener('change', function() {
        var anioSeleccionado = this.value;
        var mesesSelect = document.getElementById('periodo');
        mesesSelect.innerHTML = '<option value="">Seleccionar</option>';

        @foreach ($periodos as $year => $ejercicio)
            if (anioSeleccionado === '{{ $year }}') {
                @foreach ($ejercicio['lisPeriodos'] as $periodo)
                    @php
                        $mes = substr($periodo['perTributario'], -2);
                        $nombreMes = [
                            '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', 
                            '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio',
                            '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre',
                            '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
                        ][$mes];
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



@endsection
