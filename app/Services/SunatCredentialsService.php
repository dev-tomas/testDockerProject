<?php

namespace App\Services;

use App\Client;
use Carbon\Carbon;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;

class SunatCredentialsService
{
	protected $client;
	protected $credentials;
	protected $sunatClientId;
	protected $sunatClientSecret;
	protected $username;
	protected $password;

    public function __construct(Client $client)
	{
		$this->client = $client;
		$this->credentials = $client->sunatCredentials;
		$this->sunatClientId = $client->sunatCredentials->sunat_client_id;
		$this->sunatClientSecret = $client->sunatCredentials->sunat_client_secret;
		$this->username = $client->document.$client->usuario_sol;
		$this->password = $client->clave_sol;
	}

	public function getToken()
	{
		if (! self::tokenIsExpired()) {
			return ['success' => true, 'token' => $this->credentials->sunat_access_token];
		}

		return self::generateToken();
	}

	private function tokenIsExpired()
	{
		$credentials = $this->client->sunatCredentials;
		$timeToExpire = Carbon::parse($this->credentials->sunat_access_token_expires);
		$now = Carbon::now();

		return $timeToExpire < $now;
	}

	public function generateToken()
	{
		$responseSunat = $this->generateSunatToken();

		if ($responseSunat['success'] == false) {
			return ['success' => $responseSunat['success'], 'message' => $responseSunat['response']['error_description']];
		}

		$responseStore = self::storeToken($responseSunat['response']);

		return ['success' => true, 'message' => 'Credenciales generadas Correctamente', 'token' => $responseStore['token']];
	}

	private function storeToken($responseSunat)
	{
		$credentials = $this->client->sunatCredentials;
		$credentials->sunat_access_token = $responseSunat['access_token'];
		$credentials->sunat_access_token_expires = Carbon::now()->addSeconds($responseSunat['expires_in'])->format('Y-m-d H:i:s');
		$credentials->connection_status = 'connected';
		$credentials->save();

		return ['success' => true, 'token' => $credentials->sunat_access_token];
	}

    private function generateSunatToken()
    {
        $sunatUri = "https://api-seguridad.sunat.gob.pe/v1/clientessol/{$this->sunatClientId}/oauth2/token/";
        $params = [
            'grant_type' => 'password',
            'scope' => 'https://api-cpe.sunat.gob.pe',
            'client_id' => $this->sunatClientId,
            'client_secret' => $this->sunatClientSecret,
            'username' => $this->username,
            'password' => $this->password
        ];
        if ($this->client->production == 0) {
            $sunatUri = "https://gre-test.nubefact.com/v1/clientessol/test-85e5b0ae-255c-4891-a595-0b98c65c9854/oauth2/token";
            $params = [
                'grant_type' => 'password',
                'scope' => 'https://api-cpe.sunat.gob.pe',
                'client_id' => "test-85e5b0ae-255c-4891-a595-0b98c65c9854",
                'client_secret' => "test-Hty/M6QshYvPgItX2P0+Kw==",
                'username' =>  "{$this->client->document}MODDATOS",
                'password' => "MODDATOS"
            ];
        }

		try {
			$client = new GuzzleClient();
			$res = $client->request('POST', $sunatUri, [
				'form_params' => $params
			]);

			$response = json_decode($res->getBody()->getContents(), true);

			return ['success' => true, 'response' => $response];
		} catch (ClientException $e) {
			$response = json_decode($e->getResponse()->getBody()->getContents(), true);

			return ['success' => false, 'response' => $response, 'code' => $e->getCode()];
		}
    }
}
