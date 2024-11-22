<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class SireController extends Controller
{
    private $client;

    public function __construct()
{
    $this->middleware('auth');  // Asegura que el usuario esté autenticado
    $this->middleware('status.client');  // Verifica el estado del cliente si es necesario
    $this->client = new Client();  // Inicialización del cliente HTTP u otra lógica necesaria
}
    
    public function getToken()
    {
        $client = new Client();
        $response = $client->post('https://api-seguridad.sunat.gob.pe/v1/clientessol/'.env('SUNAT_CLIENT_ID').'/oauth2/token/', [
            'form_params' => [
                'grant_type' => 'password',
                'scope' => 'https://api-sire.sunat.gob.pe',
                'client_id' => env('SUNAT_CLIENT_ID'),
                'client_secret' => env('SUNAT_CLIENT_SECRET'),
                'username' => env('SUNAT_USERNAME'),
                'password' => env('SUNAT_PASSWORD'),
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);
        return $data['access_token'];
    }

    public function index()
    {
    $token = $this->getToken();
    $client = new Client();
    $response = $client->get('https://api-sire.sunat.gob.pe/v1/contribuyente/migeigv/libros/rvierce/padron/web/omisos/140000/periodos', [
        'headers' => [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ],
    ]);

    

    // Decodificamos la respuesta JSON
    $periodos = json_decode($response->getBody()->getContents(), true);

    // Reorganizamos los periodos por año
    $periodosPorAnio = [];
    foreach ($periodos as $ejercicio) {
        $year = $ejercicio['numEjercicio'];
        $periodosPorAnio[$year] = [
            'desEstado' => $ejercicio['desEstado'], // Guardamos el estado del año
            'lisPeriodos' => $ejercicio['lisPeriodos'],
        ];
    }

    // Pasamos los periodos organizados a la vista
    return view('sire.index', ['periodos' => $periodosPorAnio]);
    }

    public function obtenerComprobantes(Request $request)
{
    $anio = $request->input('anio');
    $mes = str_pad($request->input('periodo'), 2, '0', STR_PAD_LEFT);
    $consulta = $anio . $mes;
    $token = $this->getToken();
    $url = "https://api-sire.sunat.gob.pe/v1/contribuyente/migeigv/libros/rvierce/resumen/web/resumencomprobantes/{$consulta}/1/0/exporta?codLibro=140000";

    $client = new Client();

    try {
        $response = $client->get($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);

        if ($response->getStatusCode() == 200) {
            $comprobantes = $response->getBody()->getContents();

            if (!empty($comprobantes)) {
                $comprobantesArray = explode("\n", $comprobantes);
                $data = [];
                foreach ($comprobantesArray as $linea) {
                    $campos = explode("|", $linea);
                    $data[] = [
                        'tipo_documento' => $campos[0] ?? '',
                        'total_documentos' => $campos[1] ?? '',
                        'valor_facturado_exportacion' => $campos[2] ?? '',
                        'base_imponible_gravada' => $campos[3] ?? '',
                        'dscto_base_imponible' => $campos[4] ?? '',
                        'monto_total_igv' => $campos[5] ?? '',
                        'dscto_igv' => $campos[6] ?? '',
                        'importe_exonerada' => $campos[7] ?? '',
                        'importe_inafecta' => $campos[8] ?? '',
                        'isc' => $campos[9] ?? '',
                        'base_imponible_arroz' => $campos[10] ?? '',
                        'impuesto_arroz' => $campos[11] ?? '',
                        'icbper' => $campos[12] ?? '',
                        'otros_tributos' => $campos[13] ?? '',
                        'total_cp' => $campos[14] ?? '',
                    ];
                }

                $periodosPorAnio = $this->index()->getData()['periodos'];
                return view('sire.index', ['data' => $data, 'periodos' => $periodosPorAnio]);
            } else {
                return redirect()->route('sunat.index')->with('error', 'La respuesta está vacía.');
            }
        } else {
            return redirect()->route('sunat.index')->with('error', 'No se obtuvo una respuesta exitosa.');
        }
    } catch (\Exception $e) {
        return redirect()->route('sunat.index')->with('error', 'Error al obtener los comprobantes: ' . $e->getMessage());
    }
}

public function obtenerComprobantesCompras(Request $request)
{
    $anio = $request->input('anio');
    $mes = str_pad($request->input('periodo'), 2, '0', STR_PAD_LEFT);
    $consulta = $anio . $mes;
    $token = $this->getToken();
    $url = "https://api-sire.sunat.gob.pe/v1/contribuyente/migeigv/libros/rvierce/resumen/web/resumencomprobantes/{$consulta}/1/0/exporta?codLibro=080000";

    $client = new Client();

    try {
        $response = $client->get($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);

        if ($response->getStatusCode() == 200) {
            $comprobantes = $response->getBody()->getContents();

            if (!empty($comprobantes)) {
                $comprobantesArray = explode("\n", $comprobantes);
                $data = [];
                foreach ($comprobantesArray as $linea) {
                    $campos = explode("|", $linea);
                    $data[] = [
                        'tipo_documento' => $campos[0] ?? '',
                        'total_documentos' => $campos[1] ?? '',
                        'BI_ Gravado_DG' => $campos[2] ?? '',
                        'IGV/IPM_DG' => $campos[3] ?? '',
                        'BI_Gravado_DGNG' => $campos[4] ?? '',
                        'IGV/IPM_DGNG' => $campos[5] ?? '',
                        'BI_Gravado_DNG' => $campos[6] ?? '',
                        'IGV/IPM_DNG' => $campos[7] ?? '',
                        'Valor_Adq._NG' => $campos[8] ?? '',
                        'ISC' => $campos[9] ?? '',
                        'ICBPER' => $campos[10] ?? '',
                        'Otro_Trib/Cargos' => $campos[11] ?? '',
                        'Total_CP' => $campos[12] ?? '',
                    ];
                }

                $periodosPorAnio = $this->index_compras()->getData()['periodos'];
                return view('sire.compras', ['data' => $data, 'periodos' => $periodosPorAnio]);
            } else {
                return redirect()->route('sunat.compras')->with('error', 'La respuesta está vacía.');
            }
        } else {
            return redirect()->route('sunat.compras')->with('error', 'No se obtuvo una respuesta exitosa.');
        }
    } catch (\Exception $e) {
        return redirect()->route('sunat.compras')->with('error', 'Error al obtener los comprobantes: ' . $e->getMessage());
    }
}

public function exportarPropuesta(Request $request)
{
    $anio = $request->input('anio');
    $periodo = str_pad($request->input('periodo'), 2, '0', STR_PAD_LEFT);

    if (!$anio || !$periodo) {
        return response()->json(['error' => 'Año y período son obligatorios'], 400);
    }

    $consulta = $anio . $periodo;
    $token = $this->getToken();

    $url = "https://api-sire.sunat.gob.pe/v1/contribuyente/migeigv/libros/rvie/propuesta/web/propuesta/{$consulta}/exportapropuesta?codTipoArchivo=0";
    
    try {
        $response = $this->client->get($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
        ]);

        // Decodificar el cuerpo de la respuesta
        $data = json_decode($response->getBody()->getContents(), true);

        // Verificar si se obtuvo el ticket y retornar solo el número de ticket en el formato deseado
        if (isset($data['numTicket'])) {
            return response()->json(['numTicket' => $data['numTicket']]);
        } else {
            return response()->json(['error' => 'No se pudo generar el número de ticket'], 400);
        }
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error en la solicitud: ' . $e->getMessage()], 400);
    }
}

public function consultarEstadoPeriodo(Request $request)
{
    $anio = $request->input('anio');
    $mes = str_pad($request->input('mes'), 2, '0', STR_PAD_LEFT);
    $periodoConsulta = $anio . $mes;
    $token = $this->getToken();
    $url = "https://api-sire.sunat.gob.pe/v1/contribuyente/migeigv/libros/rvierce/gestionprocesosmasivos/web/masivo/consultaestadotickets?perIni={$periodoConsulta}&perFin={$periodoConsulta}&page=1&perPage=5";

    try {
        // Realizar la solicitud a la API
        $response = $this->client->get($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
        ]);

        if ($response->getStatusCode() == 200) {
            $result = json_decode($response->getBody()->getContents(), true);
            
            // Aquí puedes devolver el JSON para ver los datos de la API
            return response()->json($result);  // Muestra los datos completos en formato JSON

        } else {
            return redirect()->route('sunat.index')->with('error', 'No se obtuvo una respuesta exitosa.');
        }
    } catch (\Exception $e) {
        return redirect()->route('sunat.index')->with('error', 'Error al obtener los datos: ' . $e->getMessage());
    }
}

public function descargarArchivo(Request $request)
{
    $nomArchivoReporte = $request->query('nomArchivoReporte');
    $codTipoArchivoReporte = $request->query('codTipoArchivoReporte', '01');

    if (!$nomArchivoReporte) {
        return redirect()->back()->with('error', 'Nombre de archivo no proporcionado.');
    }

    $token = $this->getToken();
    $url = "https://api-sire.sunat.gob.pe/v1/contribuyente/migeigv/libros/rvierce/gestionprocesosmasivos/web/masivo/archivoreporte?nomArchivoReporte={$nomArchivoReporte}&codTipoArchivoReporte={$codTipoArchivoReporte}&codLibro=140000";

    try {
        $response = $this->client->get($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
        ]);

        if ($response->getStatusCode() == 200) {
            $fileContent = $response->getBody()->getContents();
            $fileName = "{$nomArchivoReporte}.zip";

            return response($fileContent)
                ->header('Content-Type', 'application/zip')
                ->header('Content-Disposition', "attachment; filename=\"{$fileName}\"");
        } else {
            return redirect()->back()->with('error', 'No se pudo descargar el archivo.');
        }
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error al descargar el archivo: ' . $e->getMessage());
    }
}


public function index_compras()
{
    $token = $this->getToken();
    $client = new Client();
    $response = $client->get('https://api-sire.sunat.gob.pe/v1/contribuyente/migeigv/libros/rvierce/padron/web/omisos/080000/periodos', [
        'headers' => [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ],
    ]);

    

    // Decodificamos la respuesta JSON
    $periodos = json_decode($response->getBody()->getContents(), true);

    // Reorganizamos los periodos por año
    $periodosPorAnio = [];
    foreach ($periodos as $ejercicio) {
        $year = $ejercicio['numEjercicio'];
        $periodosPorAnio[$year] = [
            'desEstado' => $ejercicio['desEstado'], // Guardamos el estado del año
            'lisPeriodos' => $ejercicio['lisPeriodos'],
        ];
    }

    // Pasamos los periodos organizados a la vista
    return view('sire.compras', ['periodos' => $periodosPorAnio]);
    }


}
