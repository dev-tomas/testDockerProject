<?php

namespace App\Providers;

use App\ReferenceGuideDetail;
use App\Sale;
use App\Taxe;
use DateTime;
use App\Store;
use Greenter\Model\Despatch\Driver;
use Greenter\Model\Despatch\Vehicle;
use Greenter\See;
use App\DebitNote;
use App\SunatCode;
use Dompdf\Dompdf;
use App\CreditNote;
use App\BankAccount;
use App\SalePayment;
use NumerosEnLetras;
use App\ReferenceGuide;
use App\Summary as Sum;
use App\Perception as P;
use App\LowCommunication;
use App\Client as CClient;
use Illuminate\Support\Str;
use chillerlan\QRCode\QRCode;
use Greenter\Model\Sale\Note;
use Greenter\Model\Sale\Cuota;
use Greenter\Model\Sale\Charge;
use Greenter\Model\Sale\Legend;
use Greenter\Model\Sale\Invoice;
use Greenter\Model\Client\Client;
use Greenter\Model\Sale\Document;
use Greenter\Model\Voided\Voided;
use Illuminate\Support\Facades\DB;
use Greenter\Model\Company\Address;
use Greenter\Model\Company\Company;
use Greenter\Model\Sale\SaleDetail;
use Greenter\Model\Summary\Summary;
use Greenter\Ws\Services\ExtService;
use Greenter\Ws\Services\SoapClient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Greenter\Model\Despatch\Despatch;
use Greenter\Model\Despatch\Shipment;
use Greenter\Model\DocumentInterface;
use Greenter\Model\Retention\Payment;
use Greenter\Model\Despatch\Direction;
use Greenter\Model\Retention\Exchange;
use Illuminate\Support\Facades\Schema;
use Greenter\Model\Retention\Retention;
use Greenter\Model\Voided\VoidedDetail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Greenter\Ws\Services\SunatEndpoints;
use Greenter\Model\Despatch\Transportist;
use Greenter\Model\Perception\Perception;
use Greenter\Model\Summary\SummaryDetail;
use Greenter\Model\Despatch\DespatchDetail;
use Greenter\Model\Retention\RetentionDetail;
use Greenter\Model\Perception\PerceptionDetail;
use Greenter\Model\Sale\FormaPagos\FormaPagoContado;
use Greenter\Model\Sale\FormaPagos\FormaPagoCredito;
use Util;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     */

    public function register()
    {
        Schema::defaultStringLength(120);
        $this->app->register(TelescopeServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

    }

    public static function constructRetention($retention) {
        $see = self::getCredentials();
        $retention_sunat = new Retention();
        $retention_sunat
            ->setSerie($retention->serial_number)
            ->setCorrelativo($retention->correlative)
            ->setFechaEmision(new \DateTime($retention->issue))
            ->setCompany(self::getCompany())
            ->setProveedor(self::setCustomer($retention->customer))
            ->setObservacion($retention->observation)
            ->setImpRetenido(self::format($retention->retained_amount))
            ->setImpPagado(self::format($retention->amount_paid))
            ->setRegimen($retention->regime->code)
            ->setTasa($retention->regime->rate);

        $change = new Exchange();
        $change->setFecha(new \DateTime())
            ->setFactor(1)
            ->setMonedaObj('PEN')
            ->setMonedaRef('PEN');

        $details = array();
        foreach($retention->detail as $r) {
            $pays = array();
            $pay = new Payment();

            $pay->setMoneda('PEN')
                ->setFecha(new \DateTime())
                ->setImporte(self::format($r->no_retention));
            array_push($pays, $pay);

            $detail = new RetentionDetail();
            $detail->setTipoDoc('01')
                ->setNumDoc($r->sale->serialnumber . '-' . $r->sale->correlative)
                ->setFechaEmision(new \DateTime($r->sale->issue))
                ->setFechaRetencion(new \DateTime($retention->issue))
                ->setMoneda('PEN')
                ->setImpTotal(self::format($r->no_retention))
                ->setImpPagar(self::format($r->retained_amount))
                ->setImpRetenido(self::format($r->amount_paid))
                ->setPagos($pays)
                ->setTipoCambio($change);

            array_push($details, $detail);
        }

        $retention_sunat->setDetails($details);
        $res = $see->send($retention_sunat);
        self::writeXml($retention_sunat, $see->getFactory()->getLastXml());
        if ($res->isSuccess()) {
            self::writeCdr($retention, $res->getCdrZip());
            /**
             * Mostrar mensaje
             */
        } else {
            return false;
            /**
             * Mostrar Error
             */
        }
    }

    public static function constructPerception($perception) {
        $see = self::getCredentials();
        $perception_sunat = new Perception();
        $perception_sunat
            ->setSerie($perception->serial_number)
            ->setCorrelativo($perception->correlative)
            ->setFechaEmision(new \DateTime($perception->issue))
            ->setCompany(self::getCompany())
            ->setProveedor(self::setCustomer($perception->customer))
            ->setObservacion($perception->observation)
            ->setImpPercibido(self::format($perception->amount_received))
            ->setImpCobrado(self::format($perception->amount_charged))
            ->setRegimen($perception->regime->code)
            ->setTasa(2);

        $change = new Exchange();
        $change->setFecha(new \DateTime())
            ->setFactor(1)
            ->setMonedaObj('PEN')
            ->setMonedaRef('PEN');

        $details = array();
        foreach($perception->detail as $p) {
            $pays = array();
            $pay = new Payment();

            $pay->setMoneda('PEN')
                ->setFecha(new \DateTime())
                ->setImporte(self::format($perception->no_perceived));
            array_push($pays, $pay);

            $detail = new PerceptionDetail();
            $detail->setTipoDoc('01')
                ->setNumDoc($p->sale->serialnumber . '-' . $p->sale->correlative)
                ->setFechaEmision(new \DateTime($p->sale->issue))
                ->setFechaPercepcion(new \DateTime($perception->issue))
                ->setMoneda('PEN')
                ->setImpTotal(self::format($p->no_perceived))
                ->setImpCobrar(self::format($p->amount_received))
                ->setImpPercibido(self::format($p->amount_charged))
                ->setCobros($pays)
                ->setTipoCambio($change);

            array_push($details, $detail);
        }

        $perception_sunat->setDetails($details);
        $res = $see->send($perception_sunat);
        self::writeXml($perception_sunat, $see->getFactory()->getLastXml());

        if ($res->isSuccess()) {
            self::writeCdr($perception_sunat, $res->getCdrZip());

            /**
             * Mostrar mensaje
             */
        } else {
            return false;
            /**
             * Mostrar error
             */
        }
    }

    public static function constructReferralGuide($id) {
        $see = self::getCredentials(9);
        $referral = ReferenceGuide::with('type_voucher', 'docDriver', 'docTransport', 'docReceiver', 'client')
            ->where('id', $id)
            ->first();
        $referral_detail = ReferenceGuideDetail::with('product')->where('reference_guide_id', $id)->get();

        $util = Util::getInstance();

        if($referral->motive == 1) {
            $code_motive = '14';
            $motive = 'Venta sujeta a confirmación de la misma empresa';
        } else if($referral->motive == 2) {
            $code_motive = '04';
            $motive = 'Traslado entre establecimientos';
        } else if($referral->motive == 3) {
            $code_motive = '08';
            $motive = 'Traslado de bienes para transformación ';
        } else if($referral->motive == 4) {
            $code_motive = '02';
            $motive = 'Recojo de bienes';
        } else if($referral->motive == 5) {
            $code_motive = '18';
            $motive = 'Traslado por emisor itinerante';
        } else if($referral->motive == 6) {
            $code_motive = '19';
            $motive = 'Traslado zona primaria';
        } else if($referral->motive == 7) {
            $code_motive = '01';
            $motive = 'Venta con entrega a terceros';
        } else if($referral->motive == 8) {
            $code_motive = '13';
            $motive = 'OTROS';
        } else if($referral->motive == 9) {
            $code_motive = '01';
            $motive = 'Venta';
        } else if($referral->motive == 10) {
            $code_motive = '02';
            $motive = 'Compra';
        } else if ($referral->motive == 11) {
            $code_motive = '08';
            $motive = 'Importacion';
        } else if ($referral->motive == 12) {
            $code_motive = '09';
            $motive = 'Exportacion';
        } else {
            $code_motive = '01';
            $motive = 'Venta';
        }
        $transp = null;
        if ($referral->modality == '01') {
            $transp = new Transportist();
            $transp->setTipoDoc($referral->docTransport->code)
                ->setNumDoc($referral->transport_document)
                ->setRznSocial($referral->transport_name);
        } else {
            $vehiculoPrincipal = (new Vehicle())
                ->setPlaca($referral->vehicle);
            $chofer = (new Driver())
                ->setTipo('Principal')
                ->setTipoDoc($referral->docDriver->code)
                ->setNroDoc($referral->driver_document)
                ->setLicencia($referral->driver_license)
                ->setNombres($referral->driver_firstname)
                ->setApellidos($referral->driver_familyname);
        }

        $shipping = new Shipment();
        $shipping->setModTraslado($referral->modality);
        $shipping->setCodTraslado($code_motive);
        $shipping->setDesTraslado($motive);
        $shipping->setFecTraslado(new \DateTime($referral->traslate));
        $shipping->setPesoTotal($referral->weight);
        $shipping->setUndPesoTotal('KGM');
        $dirreccionLlegada = new Direction($referral->ubigeo_arrival->code, $referral->arrival_address);
        $dirreccionPartida = new Direction($referral->ubigeo_start->code, $referral->start_address);
        if ($code_motive == '04') {
            $dirreccionLlegada->setCodLocal($referral->sunat_code_arrival)->setRuc($referral->client->document);
            $dirreccionPartida->setCodLocal($referral->sunat_code_start)->setRuc($referral->client->document);
        }
        $shipping->setLlegada($dirreccionLlegada);
        $shipping->setPartida($dirreccionPartida);
        if ($referral->modality == '01') {
            $shipping->setTransportista($transp);
        } else {
            $shipping->setVehiculo($vehiculoPrincipal);
            $shipping->setChoferes([$chofer]);
        }

        $dispatched = new Despatch();
        $dispatched->setVersion('2022')
            ->setTipoDoc($referral->type_voucher->code)
            ->setSerie($referral->serialnumber)
            ->setCorrelativo($referral->correlative)
            ->setFechaEmision(new \DateTime(date('Y-m-d H:i:s', strtotime($referral->date))))
            ->setCompany($util->getCompany())
            ->setDestinatario((new Client())
                ->setTipoDoc($referral->docReceiver->code)
                ->setNumDoc($referral->receiver_document)
                ->setRznSocial($referral->receiver))
            ->setEnvio($shipping);

        $details = [];
        foreach($referral_detail as $d) {
            $detail = new DespatchDetail();
            $detail->setCantidad($d->quantity)
                ->setUnidad('NIU')
                ->setDescripcion($d->product->description);

            array_push($details, $detail);
        }

        $dispatched->setDetails($details);

        $guide = $id;

        $see = $util->getSee('', $referral->client);
        $xml = $see->getXmlSigned($dispatched);

        $api = $util->getSeeApi($referral->client);
        $res = $api->sendXml($dispatched->getName(), $xml);
        self::writeXml($dispatched, $xml);

//        dd($res);

        if ($res->isSuccess()) {
            $ticket = $res->getTicket();
            $response = $api->getStatus($ticket);
            $code = $response->getCode();
            $cdr = $response->getCdrResponse();
            $sc = SunatCode::where('code', (int) $code)->first();

            if ($sc == null) {
                $in = 2;
            } else {
                $in = $sc->id;
            }

            $description = null;
            $notes = null;
            $qrText = null;

            if ($cdr != null) {
                $description = $cdr->getDescription();
                $notes = json_encode($cdr->getNotes());
                $qrText = $cdr->getReference();
            }

            $referral->ticket = $ticket;
            $referral->reception_date = now();
            $referral->status_sunat = 1;
            $referral->response_sunat = $in;
            $referral->sunat_accepted = true;
            $referral->sunat_notes = $notes;
            $referral->sunat_description = $description;
            $referral->qr_text = $qrText;
            $referral->save();

            self::writeCdr($dispatched, $response->getCdrZip());
            return ['success' => true,
                'code' => $referral->sunat_code->code,
                'description' => $referral->sunat_code->description,
                'response' => true];
        } else {
            $in = 2;
            $referral->status_sunat = 1;
            $referral->response_sunat = $in;
            $referral->sunat_accepted = false;
            $referral->sunat_notes = "";
            $referral->save();

            return ['success' => true,
                'code' => $referral->sunat_code->code,
                'description' => $referral->sunat_code->description,
                'response' => false];
        }
    }

    public static function consultReferenceApi($referral)
    {
        $util = Util::getInstance();
        $api = $util->getSeeApi($referral->client);
        $response = $api->getStatus($referral->ticket);

        $code = $response->getCode();
        $cdr = $response->getCdrResponse();
        $sc = SunatCode::where('code', 'like', $code)->first();

        if ($sc == null) {
            $in = 2;
        } else {
            $in = $sc->id;
        }

        $description = null;
        $notes = null;
        $qrText = null;

        if ($cdr != null) {
            $description = $cdr->getDescription();
            $notes = json_encode($cdr->getNotes());
            $qrText = $cdr->getReference();
        }

        $referral->status_sunat = 1;
        $referral->response_sunat = $in;
        $referral->sunat_accepted = true;
        $referral->sunat_notes = $notes;
        $referral->sunat_description = $description;
        $referral->qr_text = $qrText;
        $referral->save();

        return ['success' => true,
            'code' => $referral->sunat_code->code,
            'description' => $referral->sunat_code->description,
            'response' => true];
    }

    public static function constructLowCommunication($low_communication_id) {
        $low_communication = LowCommunication::with('detail_low.sale')->find($low_communication_id);
        $see = self::getCredentials();

        if ($low_communication->ticket == null) {
            $details = array();
            foreach($low_communication->detail_low as $low_detail) {
                $detail = new VoidedDetail();
                if ($low_detail->sale != null) {
                    $detail->setTipoDoc('01')
                        ->setSerie($low_detail->sale->serialnumber)
                        ->setCorrelativo($low_detail->sale->correlative)
                        ->setDesMotivoBaja($low_detail->motive);
                } else if ($low_detail->debit_note != null) {
                    $detail->setTipoDoc('08')
                        ->setSerie($low_detail->debit_note->serial_number)
                        ->setCorrelativo($low_detail->debit_note->correlative)
                        ->setDesMotivoBaja($low_detail->motive);
                }
    
                array_push($details, $detail);
            }
    
            $voided = new Voided();
            $voided->setCorrelativo(intval($low_communication->correlative))
                ->setFecGeneracion(new \DateTime($low_communication->generation_date))
                ->setFecComunicacion(new \DateTime($low_communication->communication_date))
                ->setCompany(self::getCompany())
                ->setDetails($details);

            $res = $see->send($voided);
    
            self::writeXml($voided, $see->getFactory()->getLastXml());

            if (!$res->isSuccess()) {
                $sunat_code = SunatCode::where('code', $res->getError()->getCode())->first('id');
                $low_communication->sunat_code_id = $sunat_code->id;
                $low_communication->save();
                return response()->json(false);
            }

            $ticket = $res->getTicket();
        } else {
            $ticket = $low_communication->ticket;
        }

        $res = self::getTicket($ticket);

        if (!$res->isSuccess()) {
            $sunat_code = SunatCode::where('code', $res->getError()->getCode())->first('id');
            if ($sunat_code == null) {
                $code = 2;
            } else {
                $code =  $sunat_code->id;
            }
            $low_communication->status_sunat = $code;
            $low_communication->ticket = $ticket;
            $low_communication->save();
            return response()->json(false);
        }
        $low_communication = LowCommunication::find($low_communication_id);
        $low_communication->sunat_code_id = 1;
        $low_communication->ticket = $ticket;
        $low_communication->save();

        $cdr = $res->getCdrResponse();
        
        if($low_communication->ticket == null) {
            self::writeCdr($voided, $res->getCdrZip());
		} else {
            $type = 'cdr';
            $date = date('Ymd', strtotime($low_communication->communication_date));
            $headquarter = Auth::user()->headquarter;
            $folder_client = $headquarter->client->document;
            $xml_folder = '/public/' . $type . '/' . $folder_client . '/';

            Storage::disk('local')->put($xml_folder.'R-' . auth()->user()->headquarter->client->document . '-RA-' . $date . '-' . $low_communication->correlative . '.zip', $res->getCdrZip());
		}

        return response()->json(true);
    }

    public static function constructDebitNote($debit_note) {
        $headquarter = Auth::user()->headquarter;
        $see = self::getCredentials();
        $invoice = self::setNote($debit_note);

        $items = array();

        foreach($debit_note->sale->detail as $detail) {
            array_push($items, self::setSaleDetail($detail));
        }

        $decimal = Str::after($debit_note->total, '.');
        $int = Str::before($debit_note->total, '.');

        $leyenda = NumerosEnLetras::convertir($int) . ' con ' . $decimal . '/100';
        $legends = array();
        $legend = self::setLegend(\NumerosEnLetras::convertir($debit_note->total, 'Soles',true));
        array_push($legends, $legend);

        $invoice->setDetails($items)
            ->setLegends($legends);

        $hash = self::getHash($see, $invoice);
        $qrCode = self::getQrCode($invoice, $hash);
        $qr = (new QRCode)->render($qrCode);
        $bankInfo = BankAccount::where('client_id', $headquarter->client_id)->get();
        $clientInfo = \App\Client::find($headquarter->client_id);
        $customer = \App\Customer::find($debit_note->customer_id);
        $igv = Taxe::where('id', '=',1)->first();

        if($headquarter->client->invoice_size == 1) {
            $html = view('commercial.note.pdfdebit',
                compact('debit_note','legend', 'leyenda','bankInfo', 'clientInfo', 'customer', 'invoice', 'qrCode','qr', 'hash', 'igv'));
        } else {
            $html = view('commercial.note.pdfdebit',
                compact('debit_note','legend','leyenda', 'bankInfo', 'customer', 'clientInfo','invoice', 'qrCode','qr', 'hash', 'igv'));
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
        Storage::disk('local')->put($pdf_folder . $debit_note->type_voucher->code . '-' . $debit_note->serial_number . '-' . $debit_note->correlative . '.pdf',
            $dompdf->output());

        $result = $see->send($invoice);

        /**
         * Guardar el XML
         */
        self::writeXml($invoice, $see->getFactory()->getLastXml());

        if (!$result->isSuccess()) {
            $response_sunat = SunatCode::where('code', $result->getError()->getCode())->select('id')->first();
            if($response_sunat == null) {
                $code_sunat_id = 2;
            } else {
                $code_sunat_id = $response_sunat->id;
            }
            $sale_update = DebitNote::find($debit_note->id);
            $sale_update->response_sunat    =   $code_sunat_id;
            $sale_update->status_sunat      =   1;
            $sale_update->save();
            return response()->json(false);
        }

        /**
         * Guardar el CDR
         */
        self::writeCdr($invoice, $result->getCdrZip());

        $sale_update = DebitNote::find($debit_note->id);
        $sale_update->response_sunat    =   1;
        $sale_update->status_sunat      =   1;
        $sale_update->save();

//        return response()->json(true);
        if (!$result->isSuccess()) {
            $sunat_code = SunatCode::where('code', $result->getError()->getCode())->first();

            if($sunat_code !== null) {
                $code_for_update = $sunat_code->id;
            } else {
                $code_for_update = 2;
            }
            $credit_note_update                 =   DebitNote::find($debit_note->id);
            $credit_note_update->status_sunat   =   1;
            $credit_note_update->response_sunat =   $code_for_update;
            $credit_note_update->save();
            return response()->json(false);
        }

        return response()->json(true);
    }

    public static function constructCreditNote($credit_note) {
        $headquarter = Auth::user()->headquarter;
        $see = self::getCredentials();
        $invoice = self::setNote($credit_note);

        $items = array();

        foreach($credit_note->detail as $detail) {
            $igv =  $detail->subtotal * 0.18;
            $igvPercentage = $detail->igv_percentage;
            $unitValue = $detail->price_unit;
            $price = self::format($detail->price);

            if ($detail->product->type_igv_id == 8 || $detail->product->type_igv_id == 9) {
                $igv = 0;
                $igvPercentage = 18.00;
                $unitValue = $detail->price;
            }

            $itemDescription = $detail->product->description;
            if ($credit_note->type_credit_note_id == 3) {
                $itemDescription = "DICE: {$detail->product->description} DEBE DECIR: {$detail->new_description}";
            }

            $item = new SaleDetail();
            $item->setUnidad($detail->product->ot->code);
            $item->setCantidad($detail->quantity);
            $item->setDescripcion($itemDescription);
            $item->setMtoBaseIgv(self::format($detail->subtotal));
            $item->setPorcentajeIgv($igvPercentage);
            $item->setIgv(self::format($igv));
            $item->setTipAfeIgv($detail->type_igv_id == null ? $detail->product->type_igv->code : $detail->type_igv->code);
            $item->setTotalImpuestos(self::format($igv));
            $item->setMtoValorVenta(self::format($detail->subtotal));
            $unitValue = $detail->subtotal / $detail->quantity;
            $item->setMtoValorUnitario($unitValue);
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

            $items[] = $item;
        }

        $decimal = Str::after($credit_note->total, '.');
        $int = Str::before($credit_note->total, '.');

        $leyenda = NumerosEnLetras::convertir($int) . ' con ' . $decimal . '/100';
        $legends = array();
        $legend = self::setLegend(\NumerosEnLetras::convertir($credit_note->total, 'Soles',true));
        array_push($legends, $legend);

        $invoice->setDetails($items)
            ->setLegends($legends);

        $hash = self::getHash($see, $invoice);
        $qrCode = self::getQrCode($invoice, $hash);
        $qr = (new QRCode)->render($qrCode);
        $bankInfo = BankAccount::where('client_id', $headquarter->client_id)->get();
        $clientInfo = \App\Client::find($headquarter->client_id);
        $customer = \App\Customer::find($credit_note->customer_id);
        $igv = Taxe::where('id', '=',1)->first();

        if($headquarter->client->invoice_size == 1) {
            $html = view('commercial.note.pdf', compact('credit_note','customer', 'legend', 'leyenda', 'bankInfo', 'clientInfo', 'invoice', 'qrCode','qr', 'hash', 'igv'));
        } else {
            $html = view('commercial.note.pdf', compact('credit_note','customer', 'legend','leyenda', 'bankInfo', 'clientInfo','invoice', 'qrCode','qr', 'hash', 'igv'));
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
        Storage::disk('local')->put($pdf_folder . $credit_note->type_voucher->code . '-' . $credit_note->serial_number . '-' . $credit_note->correlative . '.pdf',
            $dompdf->output());

        $result = $see->send($invoice);

        /**
         * Guardar el XML
         */
        self::writeXml($invoice, $see->getFactory()->getLastXml());

        if (!$result->isSuccess()) {
            $response_sunat = SunatCode::where('code', $result->getError()->getCode())->select('id')->first();
            if($response_sunat == null) {
                $code_sunat_id = 2;
            } else {
                $code_sunat_id = $response_sunat->id;
            }
            $sale_update = CreditNote::find($credit_note->id);
            $sale_update->response_sunat    =   $code_sunat_id;
            $sale_update->status_sunat      =   1;
            $sale_update->save();
            return response()->json(false);
        }

        /**
         * Guardar el CDR
         */
        self::writeCdr($invoice, $result->getCdrZip());

        $sale_update = CreditNote::find($credit_note->id);
        $sale_update->response_sunat    =   1;
        $sale_update->status_sunat      =   1;
        $sale_update->save();

//        return response()->json(true);
        if (!$result->isSuccess()) {
            $sunat_code = SunatCode::where('code', $result->getError()->getCode())->first();

            if($sunat_code !== null) {
                $code_for_update = $sunat_code->id;
            } else {
                $code_for_update = 2;
            }
            $credit_note_update                 =   CreditNote::find($credit_note->id);
            $credit_note_update->status_sunat   =   1;
            $credit_note_update->response_sunat =   $code_for_update;
            $credit_note_update->save();
            return response()->json(false);
        }

        return response()->json(true);
    }

    public static function constructSummary($id, $client) {
        $see = self::getCredentialsByClient($client);

        if($id != null) {
            $summary = Sum::with(
                'client',
                'detail.sale',
                'detail.sale.customer',
                'detail.sale.customer.document_type',
                'detail.sale.type_voucher',
                'detail.sale.credit_note.type_voucher'
            )->where('id', $id)->first();
        }

        if ($summary->ticket == null) {
            $pays = array();
    
            foreach($summary->detail as $sd) {
                $pays[] = self::setSummaryDetail($sd);
            }
    
            $summary_sunat = new Summary();
            $summary_sunat->setFecGeneracion(new \DateTime($summary->date_issues))
                ->setFecResumen(new \DateTime($summary->date_issues))
                ->setCorrelativo($summary->correlative)
                ->setCompany(self::getCompany($summary->client))
                ->setDetails($pays);
    
            $result = $see->send($summary_sunat);

            self::writeXmlByClient($summary_sunat, $see->getFactory()->getLastXml(), $client);

            if (!$result->isSuccess()) {
                $response_sunat = SunatCode::where('code', $result->getError()->getCode())->select('id')->first();
                if($response_sunat == null) {
                    $code_sunat_id = 2;
                } else {
                    $code_sunat_id = $response_sunat->id;
                }
                $summary_update = Sum::find($summary->id);
                $summary_update->response_sunat    =   $code_sunat_id;
                $summary_update->status_sunat      =   1;
                $summary_update->save();
                return response()->json(false);
            }
    
            $ticket = $result->getTicket();

            $summary_update = Sum::find($summary->id);
            $summary_update->ticket = $ticket;
            $summary_update->save();
            
        } else {
            $ticket = $summary->ticket;
        }

        $res = self::getTicketByClient($ticket, $client);

        if (! $res->isSuccess()) {
            $response_sunat = SunatCode::where('code', $res->getError()->getCode())->select('id')->first();
            if($response_sunat == null) {
                $code_sunat_id = 2;
            } else {
                $code_sunat_id = $response_sunat->id;
            }

            $summary_update = Sum::find($summary->id);
            $summary_update->response_sunat    =   $code_sunat_id;
            $summary_update->status_sunat      =   1;
            $summary_update->save();

            return response()->json(json_encode((array) $res->getError()));
        }

        $cdr = $res->getCdrResponse();

        if ($summary->ticket == null) {
            self::writeCdrByClient($summary_sunat, $res->getCdrZip(), $client);
        } else {
            $date = date('Ymd', strtotime($summary->date_issues));
            $folder_client = auth()->user()->headquarter->client->document;
            $xml_folder = '/public/cdr/' . $folder_client . '/';
            $filename = 'R-' . auth()->user()->headquarter->client->document . '-RC-' . $date . '-' . $summary->correlative . '.zip';
    
            Storage::disk('local')->put($xml_folder . $filename, $res->getCdrZip());
        }

        $summary_update = Sum::find($summary->id);
        $summary_update->response_sunat =   1;
        $summary_update->status_sunat   =   1;
        $summary_update->ticket         =   $ticket;
        $summary_update->save();

        foreach ($summary_update->detail as $detail) {
            $detail->sale->status_sunat = 1;
            $detail->sale->response_sunat = 1;
            $detail->sale->save();
        }

        return true;
    }

    public static function constructInvoice(Sale $sale) {
        /**
         * Falta configurar si es bolsa de plástico
         */
        $headquarter = $sale->headquarter;
        $see = self::getCredentials();
        $client = self::setCustomer($sale->customer);
        $customerInfo = self::setCustomer($sale->customer);
        $company = self::getCompany($headquarter->client);
        $invoice = self::setInvoice($sale, $client, $company);
        $items = array();

        foreach($sale->detail as $detail) {
            array_push($items, self::setSaleDetail($detail));
        }

        $decimal = Str::after($sale->total, '.');
        $int = Str::before($sale->total, '.');

        $leyenda = NumerosEnLetras::convertir($int) .' con ' . $decimal . '/100';
        $legends = array();
        $legend = self::setLegend(Str::upper($leyenda . ' ' . $sale->coin->description));
        array_push($legends, $legend);

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

//        if($sale->typevoucher_id != 1 && $sale->client->type_send_boletas == 0) {
//            return -3;
//        }
        $result = $see->send($invoice);

        self::writeXml($invoice, $see->getFactory()->getLastXml());

        if($result->isSuccess()) {
            $cdr = $result->getCdrResponse();

            self::writeCdr($invoice, $result->getCdrZip());

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
            self::writeCdr($invoice, $result->getCdrZip());
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

//        if (! $result->isSuccess()) {
//            $response_sunat = SunatCode::where('code', $result->getError()->getCode())->select('id')->first();
//            if($response_sunat == null) {
//                $code_sunat_id = 2;
//            } else {
//                $code_sunat_id = $response_sunat->id;
//            }
//            $sale_update = Sale::find($sale->id);
//            $sale_update->response_sunat    =   $code_sunat_id;
//            $sale_update->status_sunat      =   1;
//            $sale_update->sunat_errors = "Error:" . json_encode((array) $result) . json_encode((array) $result->getError());
//            $sale_update->save();
//            return response()->json(false);
//        } else {
//            /**
//             * Guardar el CDR
//             */
//            self::writeCdr($invoice, $result->getCdrZip());
//
//            $sale_update = Sale::find($sale->id);
//            $sale_update->response_sunat    =   1;
//            $sale_update->status_sunat      =   1;
//            $sale_update->sunat_errors = "Success:" . json_encode((array) $result->getCdrResponse());
//            $sale_update->save();
//
//            return true;
//        }
    }

    public static function getCredentials($type = null) {
        $headquarter = Auth::user()->headquarter;
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
            if ($type == null) {
                $endPoints = SunatEndpoints::FE_PRODUCCION;
            } elseif ($type == 9) {
                $endPoints = SunatEndpoints::GUIA_PRODUCCION;
            }
            $userSol = $client->usuario_sol;
            $passwordSol = $client->clave_sol;
            $ruc = $client->document;
        }

        $see = new See();
        $see->setService($endPoints);
        $see->setCertificate($certificateUrl);
        $see->setCachePath(storage_path('sunat/cache'));
        $see->setCredentials($ruc . $userSol, $passwordSol);
        return $see;
    }
    public static function getCredentialsByClient($client) {
        $client = CClient::find($client);

        $endPoints = SunatEndpoints::FE_BETA;
        $ruc = '20000000001';
        $userSol = 'MODDATOS';
        $passwordSol = 'moddatos';
        $certificateUrl = file_get_contents(public_path('storage/certificates/demo.pem'));
        if($client->production == 1) {
            $certificateUrl = file_get_contents(public_path("storage/{$client->certificate}"));
            $endPoints = SunatEndpoints::FE_PRODUCCION;
            $userSol = $client->usuario_sol;
            $passwordSol = $client->clave_sol;
            $ruc = $client->document;
        }

        $user = "{$ruc}{$userSol}";

        $see = new See();
        $see->setService($endPoints);
        $see->setCachePath(storage_path('sunat/cache'));
        $see->setCertificate($certificateUrl);
        $see->setCredentials($user, $passwordSol);
        return $see;
    }

    public static function setCustomer($customer) {
        $client = new Client();
        $client->setTipoDoc($customer->document_type->code)
            ->setNumDoc($customer->document)
            ->setRznSocial($customer->description);
        return $client;
    }

    public static function setAddressTransmitter() {
        $headquarter = Auth::user()->headquarter;
        $address = new Address();
        $address->setUbigueo($headquarter->ubigeo->code)
            ->setDepartamento($headquarter->ubigeo->department)
            ->setProvincia($headquarter->ubigeo->province)
            ->setDistrito($headquarter->ubigeo->district)
            ->setDireccion($headquarter->address);
        return $address;
    }

    public static function setCompanyTransmitter($address) {
        $headquarter = Auth::user()->headquarter;
        $company = new Company();
        $company->setRuc($headquarter->client->document)
            ->setRazonSocial($headquarter->client->trade_name)
            ->setNombreComercial($headquarter->client->business_name)
            ->setAddress($address);
        return $company;
    }

    public static function setNote($credit_note) {
        $note = new Note();
        $note->setUblVersion('2.1');
        $note->setTipDocAfectado($credit_note->sale->type_voucher->code);
        $note->setNumDocfectado($credit_note->sale->serialnumber . '-' . $credit_note->sale->correlative);
        $note->setCodMotivo($credit_note->typeCreditNote->code);
        $note->setDesMotivo($credit_note->typeCreditNote->description);
        $note->setTipoDoc($credit_note->type_voucher->code);
        $note->setSerie($credit_note->serial_number);
        $note->setFechaEmision(new DateTime(date("d-m-Y H:i:s", strtotime($credit_note->date_issue))));
        $note->setCorrelativo($credit_note->correlative);
        $note->setTipoMoneda($credit_note->sale->coin->code_str);
        $note->setCompany(self::getCompany());
        $note->setClient(self::setCustomer($credit_note->customer));
        if ($credit_note->taxed > 0.00) {
            $note->setMtoOperGravadas(self::format($credit_note->taxed));
        }
        if ($credit_note->taxed > 0.00) {
            $note->setMtoOperExoneradas(self::format($credit_note->exonerated));
        }
        if ($credit_note->taxed > 0.00) {
            $note->setMtoOperInafectas(self::format($credit_note->unaffected));
        }
        if ($credit_note->taxed > 0.00) {
            $igvFree = (float) $credit_note->free * 0.18;
            $note->setMtoOperGratuitas(self::format($credit_note->free));
            $note->setMtoIGVGratuitas(self::format($igvFree));
        }
        $note->setMtoIGV(self::format($credit_note->igv));
        $note->setTotalImpuestos(self::format($credit_note->igv));
        $note->setMtoImpVenta(self::format($credit_note->total));

        return $note;
    }

    public static function setNoteDetail() {

    }

    public static function setInvoice($invoice, $client, $company) {
        $i = new Invoice();
        $i->setUblVersion(2.1);
        $i->setTipoOperacion('0101'); // Falta terminar;
        $i->setTipoDoc($invoice->type_voucher->code);
        $i->setSerie($invoice->serialnumber);
        $i->setCorrelativo($invoice->correlative);
        $i->setFechaEmision(new DateTime($invoice->issue));
        $i->setTipoMoneda($invoice->coin->code_str);
        $i->setClient($client);
        if ($invoice->condition_payment == 'EFECTIVO' || $invoice->condition_payment == 'DEPOSITO EN CUENTA' || $invoice->condition_payment == 'TARJETA DE CREDITO' ||
            $invoice->condition_payment == 'TARJETA DE DEBITO') {
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
            $factor = number_format($invoice->discount / $base, 5, '.', '');
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

    public static function setSaleDetail($detail) {
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

        if (auth()->user()->headquarter->client->consumption_tax_plastic_bags == 1 && $detail->product->operation_type == 22) {
            $factor = auth()->user()->headquarter->client->consumption_tax_plastic_bags_price;
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
            $unitValue = $detail->subtotal / $detail->quantity;
            $item->setMtoValorUnitario($unitValue);
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

    public static function setLegend($amountInLetter, $code = '1000') {
        return (new Legend())
            ->setCode($code)
            ->setValue(trim($amountInLetter));
    }

    public static function getHash($see, DocumentInterface $document) {
        $see = self::getCredentials();
        $xml = $see->getXmlSigned($document);
        return (new \Greenter\Report\XmlUtils())->getHashSign($xml);
    }

    public static function getQrCode($sale, $hash)
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

    public static function writeXmlByClient(DocumentInterface $document, $xml, $client) {
        self::writeFileByClient($document->getName().'.xml', $xml, 'xml', $client);
    }

    public static function writeCdrByClient(DocumentInterface $document, $zip, $client)
    {
        self::writeFileByClient('R-'.$document->getName().'.zip', $zip, 'cdr', $client);
    }

    public static function writeFileByClient($filename, $content, string $type, $client) {
        $c = CClient::find($client);
        $folder_client = $c->document;
        $xml_folder = '/public/' . $type . '/' . $folder_client . '/';

        Storage::disk('local')->put($xml_folder . $filename, $content);
    }

    public static function writeXml(DocumentInterface $document, $xml) {
        self::writeFile($document->getName().'.xml', $xml, 'xml');
    }

    public static function writeCdr(DocumentInterface $document, $zip)
    {
        self::writeFile('R-'.$document->getName().'.zip', $zip, 'cdr');
    }

    public static function writeFile($filename, $content, string $type) {
        $headquarter = Auth::user()->headquarter;
        $folder_client = $headquarter->client->document;
        $xml_folder = '/public/' . $type . '/' . $folder_client . '/';

        Storage::disk('local')->put($xml_folder . $filename, $content);
    }

    public static function getCompany($client = null)
    {
        if($client !== null) {
            return (new Company())
                ->setRuc($client->document)
                ->setNombreComercial($client->business_name)
                ->setRazonSocial($client->trade_name)
                ->setAddress((new Address())
                    ->setCodLocal('0000')
                    ->setDireccion($client->address))
                ->setEmail($client->email)
                ->setTelephone($client->phone);
        } else {
            $headquarter = Auth::user()->headquarter;
            return (new Company())
                ->setRuc($headquarter->client->document)
                ->setNombreComercial($headquarter->client->business_name)
                ->setRazonSocial($headquarter->client->trade_name)
                ->setAddress((new Address())
                    ->setUbigueo($headquarter->ubigeo->code)
                    ->setDistrito($headquarter->ubigeo->district)
                    ->setProvincia($headquarter->ubigeo->province)
                    ->setDepartamento($headquarter->ubigeo->department)
                    ->setCodLocal('0000')
                    ->setDireccion($headquarter->client->address))
                ->setEmail($headquarter->client->email)
                ->setTelephone($headquarter->client->phone);
        }
    }

    public static function setSummaryDetail($detail) {
        $ticket = new SummaryDetail();
        if($detail->sale->credit_note_id !== null) {
            $ticket->setTipoDoc($detail->sale->credit_note->type_voucher->code)
                ->setSerieNro($detail->sale->credit_note->serial_number . '-' . $detail->sale->credit_note->correlative)
                ->setDocReferencia((new Document())
                    ->setTipoDoc($detail->sale->type_voucher->code)
                    ->setNroDoc($detail->sale->serialnumber . '-' . $detail->sale->correlative)
                )
                ->setEstado(1)
                ->setClienteTipo($detail->sale->customer->document_type->code)
                ->setClienteNro($detail->sale->customer->document)
                ->setTotal(self::format($detail->sale->total))
                ->setMtoOperGravadas(self::format($detail->sale->taxed))
                ->setMtoOperExoneradas(self::format($detail->sale->unaffected))
                ->setMtoOperInafectas(self::format($detail->sale->exonerated))
                // ->setIcbper(self::format($detail->sale->icbper))
                ->setMtoIGV(self::format($detail->sale->igv));
        } else {
            $ticket->setTipoDoc($detail->sale->type_voucher->code)
                ->setSerieNro($detail->sale->serialnumber . '-' . $detail->sale->correlative)
                ->setEstado($detail->condition)
                ->setClienteTipo($detail->sale->customer->document_type->code)
                ->setClienteNro($detail->sale->customer->document)
                ->setTotal(self::format($detail->sale->total))
                ->setMtoOperGravadas(self::format($detail->sale->taxed))
                ->setMtoOperExoneradas(self::format($detail->sale->unaffected))
                ->setMtoOperInafectas(self::format($detail->sale->exonerated))
                ->setMtoOperGratuitas(self::format($detail->sale->free))
                // ->setIcbper(self::format($detail->sale->exonerated))
                ->setMtoIGV(self::format($detail->sale->igv));
        }

        return $ticket;
    }

    public static function format($number, $decimals = 2)
    {
        return (double) number_format((double) $number, $decimals,'.','');
    }

    public static function getTicket($ticket) 
    {
        $headquarter = Auth::user()->headquarter;
		if($headquarter->client->production != 0) {
			$user = $headquarter->client->document . $headquarter->client->usuario_sol;
			$pass = $headquarter->client->clave_sol;
		} else {
			$user = '20000000001MODDATOS';
			$pass = 'moddatos';
		}

        if(auth()->user()->headquarter->client->production == 1) {
            $serve = 'https://e-factura.sunat.gob.pe/ol-ti-itcpfegem/billService?wsdl';
            $soap = new SoapClient();
            $soap->setService($serve);
        } else {
            $serve = 'https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService?wsdl';
            $soap = new SoapClient($serve, ["trace" => 1, "soap_version" => SOAP_1_1]);
        }

		$soap->setCredentials($user, $pass); 

		$sender = new ExtService();
		$sender->setClient($soap);

		return $sender->getStatus($ticket);
    }
   
    public static function getTicketByClient($ticket, $client) {
        $client = CClient::find($client);
        if($client->production != 0) {
            $user = $client->document . $client->usuario_sol;
            $pass = $client->clave_sol;
        } else {
            $user = '20000000001MODDATOS';
            $pass = 'moddatos';
        }

        if($client->production == 1) {
            $serve = 'https://e-factura.sunat.gob.pe/ol-ti-itcpfegem/billService?wsdl';
            $soap = new SoapClient();
            $soap->setService($serve);
        } else {
            $serve = 'https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService?wsdl';
            $soap = new SoapClient($serve, ["trace" => 1, "soap_version" => SOAP_1_1]);
        }

		$soap->setCredentials($user, $pass); 

		$sender = new ExtService();
		$sender->setClient($soap);

		return $sender->getStatus($ticket);
    }
}
