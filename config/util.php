<?php

use App\ClientCredential;
use Greenter\Data\SharedStore;
use Greenter\Model\DocumentInterface;
use Greenter\Model\Response\CdrResponse;
use Greenter\Report\HtmlReport;
use Greenter\Report\PdfReport;
use Greenter\Report\Resolver\DefaultTemplateResolver;
use Greenter\See;
use Greenter\Ws\Services\ExtService;
use Greenter\Ws\Services\SoapClient;
use Illuminate\Support\Facades\Auth;


use Greenter\Model\Client\Client;
use Greenter\Model\Company\Address;
use Greenter\Model\Company\Company;
use Illuminate\Support\Facades\Storage;


final class Util
{
    private static $current;
    /**
     * @var SharedStore
     */
    public $shared;
    private function __construct()
    {
        $this->shared = new SharedStore();
    }
    public static function getInstance()
    {
        if (!self::$current instanceof self) {
            self::$current = new self();
        }
        return self::$current;
    }

    public function getTicket($ticket) {
        $client = null;
        if($client != null) {
            $certificate = $client->certificate;
            $user = $client->document . $client->usuario_sol;
            $pass = $client->clave_sol;
        } else {
            $certificate = Auth::user()->headquarter->client->certificate;
            $user = Auth::user()->headquarter->client->document . Auth::user()->headquarter->client->usuario_sol;
            $pass = Auth::user()->headquarter->client->clave_sol;
        }

        $soap = new SoapClient();
        $soap->setService('https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService');
        $soap->setCredentials($user, $pass);
        $sender = new ExtService();
        $sender->setClient($soap);
        return $sender->getStatus($ticket);
    }
    /**
     * @param string $endpoint
     * @return See
     */
    public function getSee($endpoint, $client = null)
    {
        $see = new See();
        $see->setService($endpoint);
        //$see->setCodeProvider(new XmlErrorCodeProvider());
        if($client != null) {
            $certificate = $client->certificate;
            $user = $client->document . $client->usuario_sol;
            $pass = $client->clave_sol;
        } else {
            $certificate = Auth::user()->headquarter->client->certificate;
            $user = Auth::user()->headquarter->client->document . Auth::user()->headquarter->client->usuario_sol;
            $pass = Auth::user()->headquarter->client->clave_sol;
        }

        //$see->setCertificate(file_get_contents(__DIR__ . '/../certs/cert.pem'));
        //$see->setCertificate(file_get_contents(Storage::get($certificate)));
        $see->setCertificate(file_get_contents(__DIR__ . '/../storage/app/public/' . $certificate));
//        $see->setCertificate(file_get_contents(storage_path('app/public/') . $certificate));
        /**
         * Clave SOL
         * Ruc     = 20000000001
         * Usuario = MODDATOS
         * Clave   = moddatos
         */
        $see->setCredentials($user, $pass);
        $see->setCachePath(__DIR__ . '/../storage/sunat/cache');
        return $see;
    }

    public function getSeeApi(\App\Client $client)
    {
        $credentials = ClientCredential::where('client_id', $client->id)->first();

        $authUrl = "https://api-seguridad.sunat.gob.pe/v1";
        $cpeUrl = "https://api-cpe.sunat.gob.pe/v1";
        $usuarioSol = $client->usuario_sol;
        $claveSol = $client->clave_sol;
        if ($client->production == 0) {
            $authUrl = "https://gre-test.nubefact.com/v1";
            $cpeUrl = "https://gre-test.nubefact.com/v1";
            $usuarioSol = "MODDATOS";
            $claveSol = "MODDATOS";
        }
        $api = new \Greenter\Api([
            'auth' => $authUrl,
            'cpe' => $cpeUrl,
        ]);
        $certificate = file_get_contents(__DIR__ . "/../storage/app/public/{$client->certificate}");
//        $certificate = file_get_contents(storage_path("app/public/{$client->certificate}"));
        if ($certificate === false) {
            throw new Exception('No se pudo cargar el certificado');
        }
        return $api->setBuilderOptions([
            'strict_variables' => true,
            'optimizations' => 0,
            'debug' => true,
            'cache' => false,
        ])
            ->setApiCredentials($credentials->sunat_client_id, $credentials->sunat_client_secret)
            ->setClaveSOL($client->document, $usuarioSol, $claveSol)
            ->setCertificate($certificate);
    }

    public function showResponse(DocumentInterface $document, CdrResponse $cdr)
    {
        $filename = $document->getName();
        require __DIR__.'/../views/response.php';
    }

    public function getErrorResponse(\Greenter\Model\Response\Error $error)
    {
        $result = <<<HTML
        <h2 class="text-danger">Error:</h2><br>
        <b>Código:</b>{$error->getCode()}<br>
        <b>Descripción:</b>{$error->getMessage()}<br>
HTML;
        return $result;
    }

    public function writeXml(DocumentInterface $document, $xml)
    {
        $this->writeFile($document->getName().'.xml', $xml, 'xml');
    }

    public function writeCdr(DocumentInterface $document, $zip)
    {
        $this->writeFile('R-'.$document->getName().'.zip', $zip, 'cdr');
    }

    public function writeFile($filename, $content, string $type)
    {
        if (getenv('GREENTER_NO_FILES')) {
            return;
        }

        file_put_contents( public_path() . '/files/' . $type . '/'. $filename, $content);
    }
    public function getPdf(DocumentInterface $document)
    {
        $html = new HtmlReport('', [
            'cache' => __DIR__ . '/../storage/sunat/cache',
            'strict_variables' => true,
        ]);

        $resolver = new DefaultTemplateResolver();
        $template = $resolver->getTemplate($document);
        $html->setTemplate($template);
        $render = new PdfReport($html);
        $render->setOptions( [
            'no-outline',
            'viewport-size' => '1280x1024',
            'page-width' => '21cm',
            'page-height' => '29.7cm',

        ]);
        $binPath = self::getPathBin();
        if (file_exists($binPath)) {
            $render->setBinPath($binPath);
        }
        $hash = $this->getHash($document);
        $params = self::getParametersPdf();
        $params['system']['hash'] = $hash;
        $params['user']['footer'] = '<div>consulte en <a href="https://github.com/giansalex/sufel">sufel.com</a></div>';
        $pdf = $render->render($document, $params);
        if ($pdf === false) {
            $error = $render->getExporter()->getError();
            echo 'Error: '.$error;
            exit();
        }
        // Write html
        $this->writeFile($document->getName().'.html', $render->getHtml());
        return $pdf;
    }
    public function getGenerator($type)
    {
        $factory = new \Greenter\Data\GeneratorFactory();
        $factory->shared = $this->shared;
        return $factory->create($type);
    }
    public static function generator($item, $count)
    {
        $items = [];
        for ($i = 0; $i < $count; $i++) {
            $items[] = $item;
        }
        return $items;
    }
    public function showPdf($content, $filename)
    {
        $this->writeFile($filename, $content, 'pdf');
        header('Content-type: application/pdf');
        header('Content-Disposition: inline; filename="' . $filename . '"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . strlen($content));
        echo $content;
    }
    public static function getPathBin()
    {
        /*$path = __DIR__.'/../vendor/bin/wkhtmltopdf-i386';
        if (self::isWindows()) {
            $path .= '.exe';
        }
        return $path;*/
    }
    public static function isWindows()
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }
    public static function inPath($command) {
        $whereIsCommand = self::isWindows() ? 'where' : 'which';
        $process = proc_open(
            "$whereIsCommand $command",
            array(
                0 => array("pipe", "r"), //STDIN
                1 => array("pipe", "w"), //STDOUT
                2 => array("pipe", "w"), //STDERR
            ),
            $pipes
        );
        if ($process !== false) {
            $stdout = stream_get_contents($pipes[1]);
            stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            proc_close($process);
            return $stdout != '';
        }
        return false;
    }

    public function getHash(DocumentInterface $document)
    {
        $see = $this->getSee('');
        $xml = $see->getXmlSigned($document);
        $hash = (new \Greenter\Report\XmlUtils())->getHashSign($xml);
        return $hash;
    }

    private static function getParametersPdf()
    {
        $logo = file_get_contents(__DIR__.'/../public/vendor/adminlte3/gyo/img/logo.png');
        return [
            'system' => [
                'logo' => $logo,
                'hash' => ''
            ],
            'user' => [
                'resolucion' => '212321',
                'header' => 'Telf: <b>(056) 123375</b>',
                'extras' => [
                    ['name' => 'CONDICION DE PAGO', 'value' => 'Efectivo'],
                    ['name' => 'VENDEDOR', 'value' => 'GITHUB SELLER'],
                ],
            ]
        ];
    }

    public function getCompany($client = null)
    {
        if($client != null) {
            return (new Company())
                ->setRuc($client->document)
                ->setNombreComercial($client->business_name)
                ->setRazonSocial($client->trade_name)
                ->setAddress((new Address())
                    ->setCodLocal('0000')
                    ->setDireccion($client->address))
                ->setEmail('admin@rodmensoft.com')
                ->setTelephone($client->phone);
        } else {
            return (new Company())
                ->setRuc(Auth::user()->headquarter->client->document)
                ->setNombreComercial(Auth::user()->headquarter->client->business_name)
                ->setRazonSocial(Auth::user()->headquarter->client->trade_name)
                ->setAddress((new Address())
                    ->setUbigueo(Auth::user()->headquarter->ubigeo->code)
                    ->setDistrito(Auth::user()->headquarter->ubigeo->district)
                    ->setProvincia(Auth::user()->headquarter->ubigeo->province)
                    ->setDepartamento(Auth::user()->headquarter->ubigeo->department)
                    ->setCodLocal('0000')
                    ->setDireccion(Auth::user()->headquarter->client->address))
                ->setEmail('admin@rodmensoft.com')
                ->setTelephone(Auth::user()->headquarter->client->phone);
        }
    }
}
