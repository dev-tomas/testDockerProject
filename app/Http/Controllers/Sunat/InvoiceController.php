<?php

namespace App\Http\Controllers\Sunat;

use App\BankAccount;
use App\Sale;
use App\SalePayment;
use App\SunatCode;
use App\Taxe;
use chillerlan\QRCode\QRCode;
use Dompdf\Dompdf;
use Greenter\Model\Client\Client;
use Greenter\Model\Company\Address;
use Greenter\Model\Company\Company;
use Greenter\Model\DocumentInterface;
use Greenter\Model\Sale\Charge;
use Greenter\Model\Sale\Cuota;
use Greenter\Model\Sale\FormaPagos\FormaPagoContado;
use Greenter\Model\Sale\FormaPagos\FormaPagoCredito;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Sale\Legend;
use Greenter\Model\Sale\SaleDetail;
use Greenter\See;
use Greenter\Ws\Services\SunatEndpoints;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use NumerosEnLetras;
use Storage;

class InvoiceController extends Controller
{
    public $headquarter;
    public $client;

    public function constructInvoice(Sale $sale)
    {
        $headquarter = $sale->headquarter;
        $this->headquarter = $headquarter;
        $this->client = $sale->client;

        $see = self::getCredentials(null, $headquarter);
        $client = self::setCustomer($sale->customer);
        $customerInfo = self::setCustomer($sale->customer);
        $company = self::getCompany($headquarter->client, $headquarter);
        $invoice = self::setInvoice($sale, $client, $company);
        $items = array();

        foreach($sale->detail as $detail) {
            $items[] = self::setSaleDetail($detail, $sale->client);
        }

        $decimal = Str::after($sale->total, '.');
        $int = Str::before($sale->total, '.');

        $leyenda = NumerosEnLetras::convertir($int) .' con ' . $decimal . '/100';
        $legends = array();
        $legend = self::setLegend(Str::upper($leyenda . ' ' . $sale->coin->description));
        $legends[] = $legend;

        $invoice->setDetails($items)
            ->setLegends($legends);

        $hash = self::getHash($see, $invoice);
        $qrCode = self::getQrCode($invoice, $hash);
        $qr = (new QRCode)->render($qrCode);
        $bankInfo = BankAccount::where('client_id', $headquarter->client_id)->get();
        $clientInfo = \App\Client::find($headquarter->client_id);
        $igv = Taxe::where('id', '=',1)->first();

        $sale = Sale::with('coin', 'detail', 'payments')->find($sale->id);
        $sale->qr_text = $qrCode;
        $sale->hash_code = $hash;
        $sale->save();

        $html = '';

        if($headquarter->client->invoice_size == 1) {
            $html = view('commercial.sale.ticket',
                compact('sale','leyenda', 'bankInfo', 'clientInfo', 'qrCode','qr', 'hash', 'igv'));
        } else {
            $html = view('commercial.sale.pdf',
                compact('sale','leyenda', 'bankInfo', 'clientInfo', 'qrCode','qr', 'hash', 'igv'));
        }

        $dompdf = new Dompdf();
        $GLOBALS['bodyHeight'] = 0;
        $dompdf->setCallbacks(
            array(
                'myCallbacks' => array(
                    'event' => 'end_frame', 'f' => function ($infos) {
                        $frame = $infos["frame"];
                        if (strtolower($frame->get_node()->nodeName) === "body") {
                            $padding_box = $frame->get_padding_box();
                            $GLOBALS['bodyHeight'] += (double) $padding_box['h'];
                        }
                    }
                )
            )
        );
        $dompdf->loadHtml($html);
        $dompdf->render();

        $folder_client = $headquarter->client->document;

        /**
         * Guardar el PDF
         */
        $pdf_folder = '/public/pdf/' . $folder_client . '/';
        Storage::disk('local')->put($pdf_folder . $sale->serialnumber . '-' . $sale->correlative . '.pdf',
            $dompdf->output());

        /**
         * Guardar el XML
         */

        if($sale->typevoucher_id != 1 && $sale->client->type_send_boletas == 0) {
            return -3;
        }
        $result = $see->send($invoice);

        self::writeXmlByClient($invoice, $see->getFactory()->getLastXml(), $sale->client);

        if($result->isSuccess()) {
            $cdr = $result->getCdrResponse();

            self::writeCdrByClient($invoice, $result->getCdrZip(), $sale->client);

            $code = $cdr->getCode();

            $sc = SunatCode::where('code', 'like', '%' .$code. '%')->first();

            if ($sc == null) {
                $in = 2;
            } else {
                $in = $sc->id;
            }

            $description = $cdr->getDescription();
            $notes = json_encode($cdr->getNotes());

            $sales = Sale::find($sale->id);
            $sales->status_sunat = 1;
            $sales->response_sunat = $in;
            $sales->sunat_errors = "Success:" . json_encode((array) $result->getCdrResponse());;
            $sales->save();

            return true;
        } else {
            self::writeCdrByClient($invoice, $result->getCdrZip(), $sale->client);
            $cdr = $result->getCdrResponse();
            $code = $result->getError()->getCode();

            $message = $result->getError()->getMessage();
            $notes = $result->getError()->getMessage();

            $sc = SunatCode::where('code', 'like', '%' .$code. '%')->first();
            if ($sc == null) {
                $in = 2;
            } else {
                $in = $sc->id;
            }

            $sales = Sale::find($sale->id);
            $sales->status_sunat = 1;
            $sales->response_sunat = $in;
            $sales->sunat_errors = "Error:" . json_encode((array) $result) . json_encode((array) $result->getError());
            $sales->save();

            return false;
        }
    }

    public function setLegend($amountInLetter, $code = '1000'): Legend
    {
        return (new Legend())
            ->setCode($code)
            ->setValue(trim($amountInLetter));
    }

    private function setSaleDetail($detail, $client): SaleDetail
    {
        $igv = $detail->price - $detail->price_unit;
        $igvPercentage = $detail->igv_percentage;
        if(isset($detail->igv)) {
            $igv = $detail->igv;
        }

        $unitValue = $detail->price_unit;
        $price = self::format($detail->price);

        if ($detail->product->type_igv_id == 8 || $detail->product->type_igv_id == 9) {
            $igv = 0;
            $igvPercentage = 18.00;
            $unitValue = $detail->price;
        }

        if ($client->consumption_tax_plastic_bags == 1 && $detail->product->operation_type == 22) {
            $factor = $client->consumption_tax_plastic_bags_price;
            $icbper = (float) $factor * (float) $detail->quantity;

            $item = (new SaleDetail());
            $item->setUnidad($detail->product->ot->code);
            $item->setCantidad($detail->quantity);
            $item->setDescripcion($detail->product->description);
            $item->setMtoBaseIgv(self::format($detail->subtotal));
            $item->setPorcentajeIgv($igvPercentage);
            $item->setIgv(self::format($igv));
            $item->setTipAfeIgv($detail->type_igv_id == null ? $detail->product->type_igv->code : $detail->type_igv->code);
            $item->setIcbper($icbper);
            $item->setFactorIcbper($factor);
            $item->setTotalImpuestos(self::format($igv));
            $item->setMtoValorVenta(self::format($detail->subtotal));
            $item->setMtoValorUnitario($detail->price_unit);
            $item->setMtoPrecioUnitario(self::format($detail->price));
            if ($detail->type_igv_id == 6) {
                $price = 0;
                $unit_val = 0;
                $freePrice = $detail->price;
                $igv = (float) $detail->total * 0.18;
                $totalIgv = 0;

                $item->setIgv(self::format($igv));
                $item->setMtoValorUnitario($unit_val);
                $item->setTotalImpuestos(self::format($totalIgv));
                $item->setMtoPrecioUnitario(self::format($price, 4));
                $item->setMtoValorGratuito(self::format($freePrice));
            }

            return $item;
        } else {
            $item = new SaleDetail();
            $item->setUnidad($detail->product->ot->code);
            $item->setCantidad($detail->quantity);
            $item->setDescripcion($detail->product->description);
            $item->setMtoBaseIgv(self::format($detail->subtotal));
            $item->setPorcentajeIgv($igvPercentage);
            $item->setIgv(self::format($igv));
            $item->setTipAfeIgv($detail->type_igv_id == null ? $detail->product->type_igv->code : $detail->type_igv->code);
            $item->setTotalImpuestos(self::format($igv));
            $item->setMtoValorVenta(self::format($detail->subtotal));
            $unitValue = $detail->price / 1.18;
            $item->setMtoValorUnitario(self::format($unitValue, 8));
            $item->setMtoPrecioUnitario($price);
            if ($detail->type_igv_id == 6) {
                $price = 0;
                $unit_val = 0;
                $freePrice = $detail->price;
                $igv = (float) $detail->total * 0.18;
                $totalIgv = 0;

                $item->setIgv(self::format($igv));
                $item->setMtoValorUnitario(self::format($unit_val, 10));
                $item->setTotalImpuestos(self::format($totalIgv));
                $item->setMtoPrecioUnitario(self::format($freePrice, 4));
                $item->setMtoValorGratuito(self::format($freePrice));
            }
            return $item;
        }
    }
    private function format($number, $decimals = 2)
    {
        return number_format($number, $decimals,'.','');
    }

    /**
     * @throws \Exception
     */
    private function setInvoice(Sale $invoice, Client $client, Company $company)
    {
        $i = new Invoice();
        $i->setUblVersion(2.1);
        $i->setTipoOperacion('0101'); // Falta terminar;
        $i->setTipoDoc($invoice->type_voucher->code);
        $i->setSerie($invoice->serialnumber);
        $i->setCorrelativo($invoice->correlative);
        $i->setFechaEmision(new \DateTime($invoice->issue));
        $i->setTipoMoneda($invoice->coin->code_str);
        $i->setClient($client);
        if ($invoice->condition_payment != 'CREDITO') {
            $i->setFormaPago(new FormaPagoContado());
        } else {
            $i->setFormaPago(new FormaPagoCredito(self::format($invoice->condition_payment_amount)));
            $payments = SalePayment::where('sale_id',$invoice->id)->get();

            $p = [];

            foreach ($payments as $pay) {
                $quote = (new Cuota())
                    ->setMonto(self::format($pay->mount))
                    ->setFechaPago(new \DateTime(date("d-m-Y H:i:s", strtotime($pay->date))));

                array_push($p, $quote);
            }

            $i->setCuotas($p);
        }
        if ($invoice->taxed > 0.00) {
            $i->setMtoOperGravadas(self::format($invoice->taxed));
        }
        if ($invoice->exonerated > 0.00) {
            $i->setMtoOperExoneradas(self::format($invoice->exonerated));
        }
        if ($invoice->unaffected > 0.00) {
            $i->setMtoOperInafectas(self::format($invoice->unaffected));
        }
        if ($invoice->free > 0.00) {
            $i->setMtoOperGratuitas(self::format($invoice->free));
            $i->setMtoIGVGratuitas(self::format($invoice->free * 0.18));
        }

        if ($invoice->discount > 0.00) {
            $base = $invoice->taxed + $invoice->igv;
            $factor = number_format($invoice->discount / $base,5,'.','');
            $i->setDescuentos([
                (new Charge())
                    ->setCodTipo('03') // Catalog. 53 (03: Descuento global que no afecta la Base Imponible)
                    ->setMontoBase($base)
                    ->setFactor($factor)
                    ->setMonto($invoice->discount) // Mto Dscto
            ]);
            $i->setSumOtrosDescuentos($invoice->discount);
        }

        $i->setMtoIGV(self::format($invoice->igv));
        $i->setTotalImpuestos(self::format($invoice->igv));
        $i->setValorVenta(self::format($invoice->taxed + $invoice->exonerated + $invoice->unaffected));
        $i->setSubTotal(self::format($invoice->total + $invoice->discount));
        $i->setMtoImpVenta(self::format($invoice->total));
        $i->setCompany($company);
        if ($invoice->icbper > 0.00) {
            $i->setIcbper(self::format($invoice->icbper));
        }

        return $i;
    }

    private function getCompany($client, $headquarter): Company
    {
        return (new Company())
            ->setRuc($client->document)
            ->setNombreComercial($client->business_name)
            ->setRazonSocial($client->trade_name)
            ->setAddress((new Address())
                ->setUbigueo($headquarter->ubigeo->code)
                ->setDistrito($headquarter->ubigeo->district)
                ->setProvincia($headquarter->ubigeo->province)
                ->setDepartamento($headquarter->ubigeo->department)
                ->setCodLocal('0000')
                ->setDireccion($client->address))
            ->setEmail($client->email)
            ->setTelephone($client->phone);
    }

    private function setCustomer($customer): Client
    {
        $client = new Client();
        $client->setTipoDoc($customer->document_type->code)
            ->setNumDoc($customer->document)
            ->setRznSocial($customer->description);
        return $client;
    }

    private function getCredentials($type = null, $headquarter): See
    {
        $client = $headquarter->client;
        if ($type == null) {
            $endPoints = SunatEndpoints::FE_BETA;
        } elseif ($type == 9) {
            $endPoints = SunatEndpoints::GUIA_BETA;
        }
        $ruc = '20000000001';
        $userSol = 'MODDATOS';
        $passwordSol = 'moddatos';
        $certificateUrl = file_get_contents(public_path('storage/certificates/demo.pem'));
        if($client->production != 0) {
            $certificateUrl = file_get_contents(public_path("storage/{$client->certificate}"));
            if (null == null) {
                $endPoints = SunatEndpoints::FE_PRODUCCION;
            } elseif (null == 9) {
                $endPoints = SunatEndpoints::GUIA_PRODUCCION;
            }
            $userSol = $client->usuario_sol;
            $passwordSol = $client->clave_sol;
            $ruc = $client->document;
        }

        $see = new See();
        $see->setService($endPoints);
        $see->setCertificate($certificateUrl);
        $see->setCachePath(''); //limpiecito
        $see->setCredentials($ruc . $userSol, $passwordSol);
        return $see;
    }

    public function getHash($see, DocumentInterface $document): ?string
    {
        $see = self::getCredentials(null, $this->headquarter);
        $xml = $see->getXmlSigned($document);
        return (new \Greenter\Report\XmlUtils())->getHashSign($xml);
    }

    public function getQrCode($sale, $hash): string
    {
        $client = $sale->getClient();
        $params = [
            $sale->getCompany()->getRuc(),
            $sale->getTipoDoc(),
            $sale->getSerie(),
            $sale->getCorrelativo(),
            number_format($sale->getMtoIGV(), 2, '.', ''),
            number_format($sale->getMtoImpVenta(), 2, '.', ''),
            $sale->getFechaEmision()->format('Y-m-d'),
            $client->getTipoDoc(),
            $client->getNumDoc(),
        ];
        $content = implode('|', $params).'|';

        return $content . $hash;
    }

    public static function writeXmlByClient(DocumentInterface $document, $xml,\App\Client $client) {
        self::writeFileByClient($document->getName().'.xml', $xml, 'xml', $client);
    }

    public static function writeCdrByClient(DocumentInterface $document, $zip,\App\Client $client)
    {
        self::writeFileByClient('R-'.$document->getName().'.zip', $zip, 'cdr', $client);
    }

    public static function writeFileByClient($filename, $content, string $type,\App\Client $client) {
        $folder_client = $client->document;
        $xml_folder = '/public/' . $type . '/' . $folder_client . '/';

        Storage::disk('local')->put($xml_folder . $filename, $content);
    }
}
