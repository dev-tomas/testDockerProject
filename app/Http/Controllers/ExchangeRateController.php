<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class ExchangeRateController extends Controller
{
    public function getExchange()
    {
        $ruta = "https://ruc.com.pe/api/v1/consultas";
        $token = "f5fadf6e-8942-4dbd-bff5-c6d2ee25cdfd-4abdc7ff-4757-4156-8771-64db7794107f";

        $data = array(
            "token"	=> $token,
            "tipo_cambio" => [
                "moneda" => "PEN",
                "fecha_inicio" => date('d/m/Y'),
                "fecha_fin" => date('d/m/Y'),
            ],
        );
            
        $data_json = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $ruta);
        curl_setopt(
            $ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
            )
        );
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $respuesta  = curl_exec($ch);
        curl_close($ch);

        $leer_respuesta = json_decode($respuesta, true);
        if (isset($leer_respuesta['errors'])) {
            return response()->json($leer_respuesta['errors']);
        } else {
            return response()->json($leer_respuesta);
        }
    }

    public function getExchangeByDate($date)
    {
        $ruta = "https://api.apis.net.pe/v1/tipo-cambio-sunat?fecha=".date('Y-m-d', strtotime($date));
        $content = file_get_contents($ruta);

        $leer_respuesta = json_decode($content, true);

        return response()->json($leer_respuesta);
    }
}
