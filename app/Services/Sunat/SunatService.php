<?php

namespace App\Services\Sunat;

use PhpZip\ZipFile;
use PhpZip\Exception\ZipException;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Storage;
use App\Services\SunatCredentialsService;
use GuzzleHttp\Exception\ClientException;

class SunatService
{
    const SUNAT_SEND_API_ENDPOINT = 'https://api-cpe.sunat.gob.pe/v1/contribuyente/gem/comprobantes/';
    const SUNAT_SEND_API_ENDPOINT_TEST = "https://gre-test.nubefact.com/v1/contribuyente/gem/comprobantes/";
    const SUNAT_CONSULT_API_ENDPOINT = 'https://api-cpe.sunat.gob.pe/v1/contribuyente/gem/comprobantes/envios/';
    const SUNAT_CONSULT_API_ENDPOINT_TEST = "https://gre-test.nubefact.com/v1/contribuyente/gem/comprobantes/envios/";

    private $fileName;
    private $ruc;
    protected $token;
    private $client;
    protected $disk;

    public function send($client, $fileName) {
        $this->disk = 's3';
        if (auth()->user()->headquarter->client->production == 0) {
            $this->disk = 'local';
        }

        $this->client = $client;
        $this->fileName = $fileName;
        $this->token = (new SunatCredentialsService($client))->getToken();

        return $this->request();
    }

    public function consult($client, $reference)
    {
        $this->token = (new SunatCredentialsService($client))->getToken();
        $response = $this->consultDocumentSunat($reference);

        return $response;
    }

    private function request()
    {
        $client = new GuzzleClient();

        try {
            $data = [
                'archivo' => [
                    'nomArchivo' => "$this->fileName.zip",
                    'arcGreZip' => self::getFileBinary(),
                    'hashZip' => self::getFileHash(),
                ],
            ];

            $data = json_encode($data);
            $urlSend = self::SUNAT_SEND_API_ENDPOINT;
            if ($this->client->production == 0) {
                $urlSend = self::SUNAT_SEND_API_ENDPOINT_TEST;
            }

            $res = $client->request('POST', $urlSend."{$this->fileName}", [
                    'headers' => [
                        'User-Agent' => 'GyOManager/1.0',
                        'Content-Type' => 'application/json',
                        'Authorization' => "Bearer {$this->token['token']}",
                    ],
                    'body' => $data,
                ]);

            $response = json_decode($res->getBody(), true);

            return ['success' => true, 'data' => $response];
        } catch (ClientException $e) {
            $response = json_decode($e->getResponse()->getBody()->getContents(), true);

            return ['success' => false, 'data' => $response];
        }
    }

    private function consultDocumentSunat($reference)
    {
        $client = new GuzzleClient();
        $urlSend = self::SUNAT_CONSULT_API_ENDPOINT;
        if (auth()->user()->headquarter->client->production == 0) {
            $urlSend = self::SUNAT_CONSULT_API_ENDPOINT_TEST;
        }
        try {
            $res = $client->request('GET', $urlSend."{$reference->ticket}", [
                'headers' => [
                    'User-Agent' => 'GyOManager/1.0',
                    'Content-Type' => 'application/json',
                    'Authorization' => "Bearer {$this->token['token']}",
                ],
            ]);

            $response = json_decode($res->getBody(), true);

            return ['success' => true, 'data' => $response];
        } catch (ClientException $e) {
            $response = json_decode($e->getResponse()->getBody()->getContents(), true);

            return ['success' => false, 'data' => $response];
        }
    }

    protected function getFileBinary()
    {
        $file = self::getFileCompressed();

        $binary = base64_encode(Storage::disk('local')->get("{$file['file_name_compressed']}"));

        return $binary;
    }

    protected function getFileHash()
    {
        $file = self::getFileCompressed();

        $hash = hash_file('sha256', Storage::disk('local')->path("{$file['file_name_compressed']}"));

        return $hash;
    }

    protected function getFileCompressed()
    {
        $this->disk = 's3';
        if (auth()->user()->headquarter->client->production == 0) {
            $this->disk = 'local';
        }
        $xml_folder = "{$this->client->document}/xml";
        $fileS3 = Storage::disk($this->disk)->get("{$xml_folder}/{$this->fileName}.xml");
        $tempNameFile = "gyo_temp/{$this->fileName}.xml";
        Storage::disk('local')->put($tempNameFile, $fileS3);
        $file = Storage::disk('local')->path($tempNameFile);
        $tempName = "gyo_temp/{$this->fileName}.zip";

        try {
            $zipFile = new ZipFile();
            $zipFile->addFile($file)
                    ->saveAsFile(Storage::disk('local')->path($tempName))
                    ->close();

            return ['success' => true, 'file_name_compressed' => $tempName];
        } catch(ZipException $e){
            return ['success' => false];
        } finally{
            $zipFile->close();
        }
    }

    public function storeCdr($fileBinary, $folder_client, $fileName)
    {
        $xml_folder = "{$folder_client}/cdr/";

        Storage::disk('s3')->put($xml_folder . $fileName, base64_decode($fileBinary));
    }
}
