<?php

namespace App\Http\Controllers\Manage;

use App\SystemToken;
use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Jobs\ConsultCPEsJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ConsulCPEController extends Controller
{
    public $token = '';

    private function getToken(): void
    {
        $apiInstance = new \Greenter\Sunat\ConsultaCpe\Api\AuthApi(
            new Client()
        );

        $grant_type = 'client_credentials'; // Constante
        $scope = 'https://api.sunat.gob.pe/v1/contribuyente/contribuyentes'; // Constante
        $client_id = env('SUNAT_CLIENT_ID'); // client_id generado en menú sol
        $client_secret = env('SUNAT_SECRET'); // client_secret generado en menú sol

        try {
            $result = $apiInstance->getToken($grant_type, $scope, $client_id, $client_secret);

            $systemToken = SystemToken::where('type', 'consult_sunat')->first();
            if ($systemToken == null) {
                $systemToken = new SystemToken;
                $systemToken->type = 'consult_sunat';
            }

            $systemToken->token = $result->getAccessToken();
            $systemToken->expired_at = Carbon::parse($result->getExpiresIn())->format('Y-m-d');
            $systemToken->save();
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function tokenExpired(SystemToken $systemToken)
    {
        if (Carbon::parse($systemToken->expired_at) < Carbon::now()) {
            return true;
        }
        return false;
    }

    public function consultByDate($date)
    {
        $date = date('Y-m-d', strtotime($date));

        $job = new ConsultCPEsJob($date);
        dispatch($job);

        return response()->json(true);
    }

    public function consultCPE($args)
    {
        $systemToken = SystemToken::where('type', 'consult_sunat')->first();
        if ($systemToken == null) {
            self::getToken();
            $systemToken->refresh();
        }

        if ($this->tokenExpired($systemToken)) {
            self::getToken();
            $systemToken->refresh();
        }

        $this->token = $systemToken->token;

        $config = \Greenter\Sunat\ConsultaCpe\Configuration::getDefaultConfiguration()->setAccessToken($this->token);

        $apiInstance = new \Greenter\Sunat\ConsultaCpe\Api\ConsultaApi(new Client(), $config->setHost($config->getHostFromSettings(1)));
    
        $cpeFilter = (new \Greenter\Sunat\ConsultaCpe\Model\CpeFilter())
            ->setNumRuc($args['ruc'])
            ->setCodComp($args['typeDoc']) // Tipo de comprobanteme
            ->setNumeroSerie($args['serie'])
            ->setNumero(intval($args['correlative']))
            ->setFechaEmision($args['date'])
            ->setMonto($args['total']);
        
        try {
            $result = $apiInstance->consultarCpe($args['ruc'], $cpeFilter);
            if (!$result->getSuccess()) {
                return ['success' => false, 'message' => $result->getMessage()];
            }
        
            $data = $result->getData();

            return ['success' => true, 'cpeStatus' => $data->getEstadoCp(), 'rucStatus' => $data->getEstadoRuc(), 'rucCondition' => $data->getCondDomiRuc()];
        
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
