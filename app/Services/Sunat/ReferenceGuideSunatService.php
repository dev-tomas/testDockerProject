<?php

namespace App\Services\Sunat;

use App\SunatCode;
use App\Http\Controllers\SunatController;

class ReferenceGuideSunatService extends SunatService
{
    public function sendDocument($reference, $fileName)
    {
        $client = auth()->user()->headquarter->client;

        $response = $this->send($client, $fileName);

        if ($response['success']) {
            $reference->ticket = $response['data']['numTicket'];
            $reference->reception_date = $response['data']['fecRecepcion'];
            $reference->status_sunat = 1;
            $reference->save();

            $consultResponse = $this->consultDocument($client, $reference);

            return $consultResponse;
        } else {
            $reference->sunat_soap_error = json_encode($response);
            $reference->status_sunat = 0;
            $reference->save();

            return ['success' => false, 'code' => 100, 'description' => 'Ocurrio un error al generar la Guía de Remisión. Revise si se generó en el panel.', 'response' => false];
        }
    }

    public function consultDocument($client, $reference)
    {
        if ($reference->ticket == null) {
            $res = (new SunatController)->sendReferralGuide($reference->id, auth()->user()->headquarter->client->gre_verion);
            if ($res['response']) {
                $response = $this->consult($client, $reference);
            } else {
                return response()->json($res);
            }
        } else {
            $response = $this->consult($client, $reference);
        }

        if ($response['success']) {
            $data = $response['data'];

            if ($data['codRespuesta'] == 99 && isset($data['error'])) {
                $sunatCode = SunatCode::where('code', $data['error']['numError'])->first();

                if ($sunatCode == null) {
                    $sunatCode = SunatCode::find(2);
                }

                $reference->response_sunat = $sunatCode->id;
                $reference->sunat_accepted = 0;
                $reference->sunat_description = $data['error']['desError'];
                $reference->sunat_notes = '[]';
                $reference->sunat_soap_error = json_encode($data['error']);

                if ($data['indCdrGenerado'] == '1') {
                    $fileName = "R-{$reference->client->document}-{$reference->type_voucher->code}-{$reference->serialnumber}-{$reference->correlative}.zip";
                    $this->storeCdr($data['arcCdr'], $reference->client->document, $fileName);

                    $reference->has_cdr = 1;
                } else {
                    $reference->has_cdr = 0;
                }

                $reference->save();

                $responseData['code'] = $reference->sunat_code->code;
                $responseData['description'] = $reference->sunat_code->description;
                $responseData['response'] = true;
                return $responseData;
            } else if ($data['codRespuesta'] == 98) {
                $reference->response_sunat = null;
                $reference->sunat_notes = $data['codRespuesta'];
                $reference->save();

                $responseData['code'] = 100;
                $responseData['description'] = 'En Proceso';
                $responseData['response'] = true;
                return $responseData;
            } else {
                $reference->response_sunat = 1;
                $reference->sunat_accepted = 1;
                $reference->sunat_description = 'La GRE ha sido aceptada por SUNAT';
                $reference->sunat_notes = '[]';
                $reference->sunat_soap_error = null;

                if ($data['indCdrGenerado'] == '1') {
                    $fileName = "R-{$reference->client->document}-{$reference->type_voucher->code}-{$reference->serialnumber}-{$reference->correlative}.zip";
                    $this->storeCdr($data['arcCdr'], $reference->client->document, $fileName);

                    $reference->has_cdr = 1;
                } else {
                    $reference->has_cdr = 0;
                }

                $reference->save();

                $responseData['code'] = $reference->sunat_code->code;
                $responseData['description'] = $reference->sunat_code->description;
                $responseData['response'] = true;

                return $responseData;
            }
        }
    }
}
