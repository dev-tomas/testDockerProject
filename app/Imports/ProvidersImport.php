<?php

namespace App\Imports;

use App\Provider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProvidersImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if (!isset($row['numero'])) {
            return null;
        }

        $existsProvider = Provider::where('document', $row['numero'])->where('client_id', auth()->user()->headquarter->client_id)->first();

        $existProviderWithCode = Provider::where('code', $row['codigo'])->where('client_id', auth()->user()->headquarter->client_id)->first();

        $typedocument_id = DB::table('typedocuments')
                    ->where('code','=',$row['tipo_de_documento_6_ruc_1_dni_varios_ventas_menores_a_s70000_y_otros_4_carnet_de_extranjeria_7_pasaporte_a_cedula_diplomatica_de_identidad_0_no_domiciliado_sin_ruc_exportacion'])->first();
        
        if ($typedocument_id == null) {
            return null;
        }

        $typeDocument = $typedocument_id->id;

        if ($existsProvider == null && $existProviderWithCode == null) {
            $lastCode = Provider::where('client_id', auth()->user()->headquarter->client_id)->orderBy('id', 'desc')->select('code')->first();

            if (is_numeric($lastCode->code)) {
                $code = (int) $lastCode->code + 1; 
            } else {
                $code = 1;
            }

            return new Provider([
                'typedocument_id'   =>  $typeDocument,
                'document'          =>  $row['numero'],
                'description'       =>  $row['razon_social'],
                'tradename'         =>  $row['razon_comercial'],
                'address'           =>  $row['direccion'],
                'email'             =>  $row['correo_1'],
                'secondary_email'   =>  $row['correo_2'],
                'phone'             =>  $row['telefono'],
                'detraction'        =>  $row['detraccion'],
                'contact'        =>  $row['contacto'],
                'code'          => str_pad($code, 5, 0, STR_PAD_LEFT), 
                'client_id'         =>  Auth::user()->headquarter->client_id,
                'user_id'           =>  Auth::user()->id,
            ]);
        } else {
            return null;
        }
    }
}
