<?php

namespace App\Http\Controllers;

use App\Client as CClient;
use App\Correlative;
use App\CreditNote;
use App\CreditNoteDetail;
use App\LowCommunication;
use App\Providers\AppServiceProvider;
use App\ReferenceGuide;
use App\ReferenceGuideDetail;
use App\Retention as R;
use App\Perception as P;
use App\Sale;
use App\SaleReferralGuide;
use App\SunatCode;
use App\Summary as Sum;
use App\SummaryDetail as SumDet;
use App\SaleDetail as SaleDet;
use App\Client as CL;

use Greenter\Model\Despatch\Despatch;
use Greenter\Model\Despatch\DespatchDetail;
use Greenter\Model\Despatch\Direction;
use Greenter\Model\Despatch\Shipment;
use Greenter\Model\Despatch\Transportist;
use Greenter\Model\Response\BillResult;
use Greenter\Model\Summary\Summary;
use Greenter\Model\Summary\SummaryDetail;
use Greenter\Model\Summary\SummaryPerception;
use Greenter\Model\Voided\Voided;
use Greenter\Model\Voided\VoidedDetail;
use Greenter\Ws\Services\ConsultCdrService;
use Greenter\Ws\Services\ExtService;
use Greenter\Ws\Services\SoapClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use NumerosEnLetras;
use Greenter\Model\Response\SummaryResult;

use Greenter\Model\Sale\Invoice;
use Greenter\Report\HtmlReport;
use Greenter\Report\PdfReport;
use Greenter\Model\Sale\SaleDetail;
use Greenter\Model\Sale\Legend;
use Greenter\Model\Company\Address;
use Greenter\Model\Company\Company;
use Greenter\Model\Client\Client;
use Greenter\Model\Sale\Document;
use Greenter\Model\Sale\Note;
use Util;
use Greenter\Report\Render\QrRender;

use Greenter\Ws\Services\SunatEndpoints;

use Greenter\Model\Retention\Exchange;
use Greenter\Model\Retention\Payment;
use Greenter\Model\Retention\Retention;
use Greenter\Model\Retention\RetentionDetail;
use Greenter\Model\Perception\Perception;
use Greenter\Model\Perception\PerceptionDetail;

class SunatController extends Controller
{
    public $clientDocument;
    public function index() {}

    public function consultCDR(array $arguments, $sale, $client, $type)
    {
        $this->clientDocument = $client->document;
        if($client->production == 1) {
            $user = $client->document . $client->usuario_sol;
            $password = $client->clave_sol;
        } else {
            return response()->json(['status' => false, 'message' => "La consulta CDR solo esta diponible en el MODO PRODUCCION"]);
        }


        $serve = SunatEndpoints::FE_CONSULTA_CDR.'?wsdl';

        $ws = new SoapClient($serve);
        $ws->setCredentials($user, $password);

        $service = new ConsultCdrService();
        $service->setClient($ws);

        $result = $service->getStatusCdr(...$arguments);
        $cdr = $result->getCdrResponse();

        if (! $result->isSuccess()) {
            $code = $result->getError()->getCode();

            if (is_numeric($code)) {
                $code = (int) $code;
            } else {
                $code = 100;
            }

            $sc = SunatCode::where('code', $code)->first();

            if ($sc == null) {
                $codigo = 2;
            } else {
                $codigo = $sc->id;
            }

            $sales = Sale::find($sale);
            $sales->response_sunat = $codigo;
            $sales->status_sunat = 1;
            $sales->save();
            return response()->json(['status' => false, 'message' => $result->getError()->getMessage() . $code]);
        }

        if ($cdr == null || $result->getCdrZip() == null) {
            return response()->json(['status' => false, 'message' => "No se obtuvo un CDR de SUNAT"]);
        }

        $code = $cdr->getCode();

        $sc = SunatCode::where('code', $code)->first();

        $codigo = $sc->id;

        if ($sc == null) {
            $codigo = 2;
        }

        if ($type == 1) {
            $i = Sale::with('client', 'type_voucher')->find($sale);

            if ($result->getCdrZip()) {
                $filename = "R-{$i->client->document}-{$i->type_voucher->code}-{$i->serialnumber}-{$i->correlative}.zip";
                self::writeFile($filename, $result->getCdrZip(), 'cdr', $client->id);
            } else {
                return false;
            }

            $sales = Sale::find($sale);
            $sales->status_sunat = 1;
            $sales->response_sunat = $codigo;
            $sales->save();
        } else {
            if ($result->getCdrZip()) {
                $filename = 'R-'.implode('-', $arguments).'.zip';
                self::writeFile($filename, $result->getCdrZip(), 'cdr', $client->id);
            } else {
                return false;
            }

            $sales = CreditNote::find($sale);
            $sales->status_sunat = 1;
            $sales->response_sunat = $codigo;
            $sales->save();
        }


        return response()->json(['status' => true, 'message' => $sc->description]);
    }

    public static function writeFile($filename, $content, string $type, $client) {
        $c = CClient::find($client);
        $folder_client = $c->document;
        $xml_folder = '/public/' . $type . '/' . $folder_client . '/';

        Storage::disk('local')->put($xml_folder . $filename, $content);
    }

    public function sendSummaryS($id, $from = null)
    {
        return response()->json(AppServiceProvider::constructSummary($id));
    }

    public function convertNote($id, $type)
    {
        $util = \Util::getInstance();
        if($type == 1) {
            $table_head = 'credit_notes';
            $table_detail = 'credit_note_details';
            $table_relation = 'credit_note_id';
            $type_note = 'type_credit_notes';
            $type_id = 'type_credit_note_id';
        } else {
            $table_head = 'debit_notes';
            $table_detail = 'debit_note_details';
            $table_relation = 'debit_note_id';
            $type_note = 'type_debit_notes';
            $type_id = 'type_debit_note_id';
        }
        $note = Db::table($table_head)
            ->join('customers','' . $table_head .'.customer_id','=','customers.id')
            ->join('typedocuments','customers.typedocument_id','=','typedocuments.id')
            ->join('typevouchers','' . $table_head .'.typevoucher_id','=','typevouchers.id')

            ->join($type_note, '' . $table_head .'.' . $type_id . '','=','' . $type_note . '.id')
            ->join('sales', '' . $table_head . '.sale_id', '=', 'sales.id')
            ->join('typevouchers as tv', 'sales.typevoucher_id', '=', 'tv.id')
            ->where('' . $table_head .'.id', '=', $id)
            ->first([
                '' . $table_head .'.*',
                'customers.description as customer_description',
                'customers.document as customer_document',
                'typedocuments.code as typedocument_code',
                'typedocuments.description',
                'typevouchers.code as typevoucher_code',
                'tv.code as tv_code',
                '' . $type_note . '.description as type_note_description',
                '' . $type_note . '.code as type_note_code',
                'sales.serialnumber as sales_serial',
                'sales.correlative as sales_correlative',
            ]);

        $note_detail = Db::table($table_detail)
            ->join('products','' . $table_detail .'.product_id','=','products.id')
            ->join('operations_type', 'products.operation_type', '=', 'operations_type.id')
            ->where('' . $table_detail .'.' . $table_relation . '', '=', $id)
            ->get([
                '' . $table_detail .'.*',
                'products.description as product_description',
                'operations_type.code as product_code'
            ]);


        $client = new Client();
        $client->setTipoDoc($note->typedocument_code)
            ->setNumDoc($note->customer_document)
            ->setRznSocial($note->customer_description);

        $invoice = new Note();
        $invoice
            ->setUblVersion('2.1')

            ->setTipDocAfectado($note->tv_code)
            ->setNumDocfectado($note->sales_serial . '-' . $note->sales_correlative)
            ->setCodMotivo($note->type_note_code)
            ->setDesMotivo($note->type_note_description)

            ->setTipoDoc($note->typevoucher_code)
            ->setSerie($note->serial_number)
            ->setFechaEmision(new \DateTime(date("d-m-Y H:i:s", strtotime($note->date_issue))))
            ->setCorrelativo($note->correlative)
            ->setTipoMoneda('PEN')
            ->setCompany($util->getCompany())
            ->setClient($client)
            ->setMtoOperGravadas($this->format($note->total - $note->igv))
            ->setMtoIGV($this->format($note->igv))
            ->setTotalImpuestos($this->format($note->igv))
            ->setMtoImpVenta($this->format($note->total))
        ;

        $items = array();

        foreach ($note_detail as $sd) {
            $unit_val = $this->format($sd->price / 1.18);
            $igv = $this->format($sd->total - $sd->subtotal);
            $item = new SaleDetail();
            $item->setDescripcion($sd->product_description)
                ->setUnidad($sd->product_code)
                ->setCantidad($sd->quantity)
                ->setMtoValorUnitario($this->format($unit_val))
                ->setMtoValorVenta($this->format($sd->subtotal))
                ->setMtoBaseIgv($this->format($sd->subtotal))
                ->setPorcentajeIgv(18)
                ->setIgv($this->format($igv))
                ->setTipAfeIgv('10')
                ->setTotalImpuestos($this->format($igv))
                ->setMtoPrecioUnitario($this->format($sd->price));
            array_push($items, $item);
        }

        $legend = new Legend();
        $legend->setCode('1000')
            ->setValue(NumerosEnLetras::convertir($note->total,'Soles',true));

        $invoice->setDetails($items)
            ->setLegends([$legend]);

        return $invoice;
    }

    public function convertPerception($id)
    {
        $per = P::with('sunat_code',
            'customer',
            'regime',
            'detail',
            'type_voucher',
            'detail.sale.type_voucher'
        )->where('id', $id)->first();

        $util = Util::getInstance();
        $perception = new Perception();
        $perception
            ->setSerie($per->serial_number)
            ->setCorrelativo($per->correlative)
            ->setFechaEmision(new \DateTime($per->issue))
            ->setCompany($util->getCompany())
            ->setProveedor($this->getClient($per->customer, $per->customer->document_type))
            ->setObservacion($per->observation)
            ->setImpPercibido($this->format($per->retained_amount))
            ->setImpCobrado($this->format($per->amount_paid))
            ->setRegimen($per->regime->code)
            ->setTasa($per->regime->rate);

        $change = new Exchange();
        $change->setFecha(new \DateTime())
            ->setFactor(1)
            ->setMonedaObj('PEN')
            ->setMonedaRef('PEN');

        $details = array();
        foreach($per->detail as $p) {
            $pays = array();
            $pay = new Payment();

            $pay->setMoneda('PEN')
                ->setFecha(new \DateTime())
                ->setImporte($this->format($p->no_retention));
            array_push($pays, $pay);

            $detail = new RetentionDetail();
            $detail->setTipoDoc('01')
                ->setNumDoc($p->sale->serialnumber . '-' . $p->sale->correlative)
                ->setFechaEmision(new \DateTime($p->sale->issue))
                ->setFechaRetencion(new \DateTime($per->issue))
                ->setMoneda('PEN')
                ->setImpTotal($this->format($p->no_perceived))
                ->setImpPagar($this->format($p->amount_received))
                ->setImpRetenido($this->format($p->amount_charged))
                ->setPagos($pays)
                ->setTipoCambio($change);

            array_push($details, $detail);
        }

        $perception->setDetails($details);

        return $perception;
    }

    public function convertRetention($id)
    {
        $ret = R::with('sunat_code',
            'customer',
            'regime',
            'detail',
            'type_voucher',
            'detail.sale.type_voucher'
        )->where('id', $id)->first();

        $util = Util::getInstance();
        $retention = new Retention();
        $retention
            ->setSerie($ret->serial_number)
            ->setCorrelativo($ret->correlative)
            ->setFechaEmision(new \DateTime($ret->issue))
            ->setCompany($util->getCompany())
            ->setProveedor($this->getClient($ret->customer, $ret->customer->document_type))
            ->setObservacion($ret->observation)
            ->setImpRetenido($this->format($ret->retained_amount))
            ->setImpPagado($this->format($ret->amount_paid))
            ->setRegimen($ret->regime->code)
            ->setTasa($ret->regime->rate);

        $change = new Exchange();
        $change->setFecha(new \DateTime())
            ->setFactor(1)
            ->setMonedaObj('PEN')
            ->setMonedaRef('PEN');

        $details = array();
        foreach($ret->detail as $r) {
            $pays = array();
            $pay = new Payment();

            $pay->setMoneda('PEN')
                ->setFecha(new \DateTime())
                ->setImporte($r->no_retention);
            array_push($pays, $pay);

            $detail = new RetentionDetail();
            $detail->setTipoDoc('01')
                ->setNumDoc($r->sale->serialnumber . '-' . $r->sale->correlative)
                ->setFechaEmision(new \DateTime($r->sale->issue))
                ->setFechaRetencion(new \DateTime($ret->issue))
                ->setMoneda('PEN')
                ->setImpTotal($this->format($r->no_retention))
                ->setImpPagar($this->format($r->retained_amount))
                ->setImpRetenido($this->format($r->amount_paid))
                ->setPagos($pays)
                ->setTipoCambio($change);

            array_push($details, $detail);
        }

        $retention->setDetails($details);

        return $retention;
    }

    public function convertSummary($id)
    {

    }

    public function convertSale($id)
    {
        $util = \Util::getInstance();
        $sale = Db::table('sales')
            ->join('customers','sales.customer_id','=','customers.id')
            ->join('typedocuments','customers.typedocument_id','=','typedocuments.id')
            ->join('typevouchers','sales.typevoucher_id','=','typevouchers.id')
            ->join('coins','sales.coin_id','=','coins.id')
            ->where('sales.id', '=', $id)
            ->first([
                'sales.*',
                'customers.description as customer_description',
                'customers.document as customer_document',
                'typedocuments.code as typedocument_code',
                'typedocuments.description',
                'typevouchers.code as typevoucher_code'
            ]);


        $sale_detail = Db::table('saledetails')
            ->join('products','saledetails.product_id','=','products.id')
            ->join('operations_type', 'products.operation_type', '=', 'operations_type.id')
            ->where('saledetails.sale_id', '=', $id)
            ->get([
                'saledetails.*',
                'products.description as product_description',
                'operations_type.code as product_code'
            ]);

        $client = new Client();
        $client->setTipoDoc($sale->typedocument_code)
            ->setNumDoc($sale->customer_document)
            ->setRznSocial($sale->customer_description);

        $invoice = new Invoice();
        $invoice
            ->setUblVersion('2.1')
            ->setFecVencimiento(new \DateTime())
            ->setTipoOperacion('0101')
            ->setTipoDoc($sale->typevoucher_code)
            ->setSerie($sale->serialnumber)
            ->setCorrelativo($sale->correlative)
            ->setFechaEmision(new \DateTime(date("d-m-Y H:i:s", strtotime($sale->date))))
            ->setTipoMoneda('PEN')
            ->setClient($client)
            ->setMtoOperGravadas($this->format($sale->subtotal))
            ->setMtoOperExoneradas(0)
            ->setMtoIGV($this->format($sale->total - $sale->subtotal))
            ->setTotalImpuestos($this->format($sale->total - $sale->subtotal))
            ->setValorVenta($this->format($sale->subtotal))
            ->setMtoImpVenta($this->format($sale->total))
            ->setCompany($util->getCompany())
        ;
        $items = array();

        foreach ($sale_detail as $sd) {
            $unit_val = $this->format($sd->price / 1.18);
            $igv = $this->format($sd->igv);
            $item = new SaleDetail();
            $item->setDescripcion($sd->product_description)
                ->setUnidad($sd->product_code)
                ->setCantidad($sd->quantity)
                ->setMtoValorUnitario($unit_val)
                ->setMtoValorVenta($this->format($sd->subtotal))
                ->setMtoBaseIgv($this->format($sd->subtotal))
                ->setPorcentajeIgv(18)
                ->setIgv($this->format($igv))
                ->setTipAfeIgv('10')
                ->setTotalImpuestos($this->format($igv))
                ->setMtoPrecioUnitario($this->format($sd->price));
            array_push($items, $item);
        }

        $legend = new Legend();
        $legend->setCode('1000')
            ->setValue(NumerosEnLetras::convertir($sale->total,'Soles',true));

        $invoice->setDetails($items)
            ->setLegends([$legend]);

        return $invoice;
    }

    public function sendSunat($id, $type = null)
    {
        $util = \Util::getInstance();
        $s = Sale::find($id);
        $sale = Db::table('sales')
            ->join('customers','sales.customer_id','=','customers.id')
            ->join('typedocuments','customers.typedocument_id','=','typedocuments.id')
            ->join('typevouchers','sales.typevoucher_id','=','typevouchers.id')
            ->join('coins','sales.coin_id','=','coins.id')
            ->join('typeoperation','sales.typeoperation_id','=','typeoperation.id')
            ->where('sales.id', '=', $id)
            ->first([
                'sales.*',
                'customers.description as customer_description',
                'customers.document as customer_document',
                'typedocuments.code as typedocument_code',
                'typedocuments.description',
                'typevouchers.code as typevoucher_code',
                'typeoperation.id as typeoperation_id'
            ]);

        $sale_detail = Db::table('saledetails')
            ->join('products','saledetails.product_id','=','products.id')
            ->join('igv_type','saledetails.type_igv_id','=','igv_type.id')
            ->join('operations_type', 'products.operation_type', '=', 'operations_type.id')
            ->where('saledetails.sale_id', '=', $id)
            ->get([
                'saledetails.*',
                'igv_type.code as code_igv_type',
                'igv_type.description as description_igv_type',
                'products.description as product_description',
                'operations_type.code as product_code',
                'igv_type.id as id_igv_type',
            ]);

        $client = new Client();
        $client->setTipoDoc($sale->typedocument_code)
            ->setNumDoc($sale->customer_document)
            ->setRznSocial($sale->customer_description);

        $invoice = new Invoice();
        $invoice
            ->setUblVersion('2.1')
            ->setFecVencimiento(new \DateTime())
            ->setTipoOperacion('0101')
            ->setTipoDoc($sale->typevoucher_code)
            ->setSerie($sale->serialnumber)
            ->setCorrelativo($sale->correlative)
            ->setFechaEmision(new \DateTime(date("d-m-Y H:i:s", strtotime($sale->date))))
            ->setTipoMoneda('PEN')
            ->setClient($client)
            ->setMtoOperGratuitas($this->format($s->free))
            ->setMtoOperInafectas($this->format($s->unaffected))
            ->setIcbper($this->format($s->icbper))
            ->setMtoOperGravadas($this->format($sale->taxed))
            ->setMtoOperExoneradas($sale->exonerated)
            ->setMtoIGV($this->format($sale->igv))
            ->setTotalImpuestos($this->format($sale->igv))
            ->setValorVenta($this->format($sale->subtotal))
            ->setSubTotal($this->format($sale->total))
            ->setMtoImpVenta($this->format($sale->total))
            ->setCompany($util->getCompany())
        ;

        $items = array();

        foreach ($sale_detail as $sd) {
            $unit_val = number_format($sd->price / 1.18, 2, '.', '');
            $item = new SaleDetail();
            $item->setDescripcion($sd->product_description)
                ->setUnidad($sd->product_code)
                ->setCantidad($sd->quantity)
                ->setMtoValorUnitario($this->format($unit_val))
                ->setMtoValorVenta($this->format($sd->subtotal))
                ->setMtoBaseIgv($this->format($sd->subtotal));

                if($sd->id_igv_type == 8) {
                    $item->setIgv(0)
                    ->setTotalImpuestos($this->format(0))
                    ->setPorcentajeIgv(0)
                    ->setTipAfeIgv('20');
                } else {
                    $item->setIgv($this->format($sd->igv))
                    ->setTotalImpuestos($this->format($sale->igv))
                    ->setPorcentajeIgv(18)
                    ->setTipAfeIgv($sd->code_igv_type);
                }
                $item->setMtoPrecioUnitario($this->format($sd->price));
            array_push($items, $item);

            /*if($sd->typeoperation_id == 22) {
                $item = new SaleDetail();
                $item
                    ->setCodProducto('P002')
                    ->setUnidad('NIU')
                    ->setCantidad($sd->quantity)
                    ->setDescripcion('BOLSA DE PLASTICO')
                    ->setMtoValorUnitario(0.05)
                    ->setMtoPrecioUnitario(0.059)
                    ->setMtoValorVenta(0.20)
                    ->setTipAfeIgv('10')
                    ->setMtoBaseIgv(0.20)
                    ->setPorcentajeIgv(18.0)
                    ->setIgv(0.04)
                    ->setTotalImpuestos(0.44)
                    ->setIcbper(0.40) // (cantidad)*(factor ICBPER)
                    ->setFactorIcbper(0.10)
                ;
            }*/
        }

        $legend = new Legend();
        $legend->setCode('1000')
            ->setValue(NumerosEnLetras::convertir($sale->total,'Soles',true));

        $invoice->setDetails($items)
            ->setLegends([$legend]);

        $see = $util->getSee(SunatEndpoints::FE_BETA);
        $res = $see->send($invoice);
        $util->writeXml($invoice, $see->getFactory()->getLastXml());

        if ($res->isSuccess()) {
            $sales = Sale::find($sale->id);
            $cdr = $res->getCdrResponse();
            $util->writeCdr($invoice, $res->getCdrZip());
            $sc = SunatCode::where('code', $cdr->getCode())->first();
            $sales->status_sunat = 1;
            $sales->response_sunat = $sc->id;
            $sales->save();

            if($type == 1) {
                $response['response'] = true;
                return response()->json($response);
            } else {
                return true;
            }
        } else {
            $util->writeCdr($invoice, $res->getCdrZip());
            $cdr = $res->getCdrResponse();
            $code = $res->getError()->getCode();

            $sc = SunatCode::where('code', $code)->first();
            $sales = Sale::find($sale->id);
            $sales->status_sunat = 1;
            $sales->response_sunat = $sc->id;
            $sales->save();

            if($type == 1) {
                $response['response'] = $this->responseCode($code);
                return response()->json($response);
            } else {
                return $this->responseCode($code);
            }
        }
    }

    public function sendNote($id, $type, $opc = null)
    {
        $credit_note = CreditNote::find($id);

        $send = AppServiceProvider::constructCreditNote($credit_note);

        return $send;
    }

    public function printBoucher()
    {
        $util = Util::getInstance();

        $sale = Db::table('sales')
            ->join('customers','sales.customer_id','=','customers.id')
            ->join('typedocuments','customers.typedocument_id','=','typedocuments.id')
            ->join('typevouchers','sales.typevoucher_id','=','typevouchers.id')
            ->join('coins','sales.coin_id','=','coins.id')
            ->first([
                'sales.*',
                'customers.description as customer_description',
                'customers.document as customer_document',
                'typedocuments.code as typedocument_code',
                'typedocuments.description',
                'typevouchers.code as typevoucher_code'
            ]);

        $saledetail = Db::table('saledetails')
            ->join('products','saledetails.product_id','=','products.id')
            ->first([
                'saledetails.*',
                'products.description as product_description'
            ]);

        $client = new Client();
        $client->setTipoDoc($sale->typedocument_code)
            ->setNumDoc($sale->customer_document)
            ->setRznSocial($sale->customer_description);

        $invoice = new Invoice();
        $invoice
            ->setUblVersion('2.1')
            ->setTipoOperacion('0101')
            ->setTipoDoc($sale->typevoucher_code)
            ->setSerie($sale->serialnumber)
            ->setCorrelativo('1')
            ->setFechaEmision(new \DateTime(date("d-m-Y H:i:s", strtotime($sale->date))))
            ->setTipoMoneda('PEN')
            ->setCompany($util->getCompany())
            ->setClient($client)
            ->setMtoOperGravadas($sale->total)
            ->setMtoIGV($sale->total - $sale->subtotal)
            ->setValorVenta($sale->subtotal)
            ->setTotalImpuestos($sale->subtotal)
            ->setMtoImpVenta($sale->total)
        ;

        $res = array();

        $item1 = new SaleDetail();
        $item1->setDescripcion($saledetail->product_description)
            ->setUnidad('NIU')
            ->setCantidad($saledetail->quantity)
            ->setMtoBaseIgv($this->format($saledetail->total))
            ->setPorcentajeIgv($this->format($sale->igv))
            ->setIgv($this->format($saledetail->total - $sale->subtotal))
            ->setTipAfeIgv('10')
            ->setTotalImpuestos($this->format($saledetail->total - $sale->subtotal))
            ->setMtoValorVenta($this->format($saledetail->total))
            ->setMtoValorUnitario($this->format($saledetail->price))
            ->setMtoPrecioUnitario($this->format($saledetail->price));

        $legend = new Legend();
        $legend->setCode('1000')
            ->setValue(strtoupper(NumerosEnLetras::convertir($sale->total,'Soles',true)));

        $invoice->setDetails([$item1])
            ->setLegends([$legend]);

        $html = new HtmlReport('', [
            'cache' => __DIR__ . '../../../storage/sunat/cache',
            'strict_variables' => true,
        ]);

        //$html->setTemplate('invoice.html.twig');

        $report = new PdfReport($html);
        $report->setOptions( [
            'no-outline',
            'viewport-size' => '1280x1024',
            'page-width' => '21cm',
            'page-height' => '29.7cm',
        ]);


        $report->setBinPath('C:/Program Files/wkhtmltopdf/bin/wkhtmltopdf.exe');

        // $logo = file_get_contents(__DIR__ . '/../../../public/images/logo.png');
        $logo = public_path('images/logo.png');
        $params = [
            'system' => [
                'logo' => $logo,
                'hash' => ''
            ],
            'user' => [
                'resolucion' => '212321',
                'header'     => 'Telf: <b>' . 0535112 . '</b>',
                'extras'     => [
                    ['name' => 'CONDICION DE PAGO', 'value' => 'Efectivo'     ],
                    ['name' => 'VENDEDOR'         , 'value' => 'GITHUB SELLER'],
                ],
            ]
        ];

        $pdf = $report->render($invoice, $params);
        //$pdf = $util->getPdf($invoice);

        try {
            /*header("Content-disposition: attachment; filename=invoice.pdf");
            header("Content-type: MIME");
            readfile("invoice.pdf");

            file_put_contents('invoice.pdf', $pdf);*/
            $util->showPdf($pdf, $invoice->getName().'.pdf');

        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * Lows
     */
    public function sendLowCommunicationSunatOnly($low_id)
    {
        $util = Util::getInstance();
        $l = LowCommunication::with('detail','detail.sale','detail.sale.type_voucher')->where('id', $low_id)->first();

        $low_detail = new VoidedDetail();
        $low_detail->setTipoDoc($l->detail->sale->type_voucher->code)
            ->setSerie($l->detail->sale->serialnumber)
            ->setCorrelativo($l->correlative)
            ->setDesMotivoBaja($l->detail->motive);

        $low_s = new Voided();
        $low_s->setCorrelativo($l->correlative)
            ->setFecGeneracion(new \DateTime($l->generation_date))
            ->setFecComunicacion(new \DateTime($l->communication_date))
            ->setCompany($util->getCompany())
            ->setDetails([$low_detail]);

        $see = $util->getSee(SunatEndpoints::FE_BETA);

        $res = $see->send($low_s);
        $util->writeXml($low_s, $see->getFactory()->getLastXml());

        $ticket = $res->getTicket();

        if (!$res->isSuccess()) {
            $code = $res->getError()->getCode();

            if($code === 'HTTP') {
                $low = LowCommunication::find($low_id);
                $low->ticket        =   $ticket;
                $low->status_sunat  =   1;
                $low->save();
            } else {
                $sc = SunatCode::where('code', $code)->first();
                $low = LowCommunication::find($low_id);
                $low->ticket        =   $ticket;
                $low->status_sunat  =   1;
                $low->sunat_code_id =   $sc->id;
                $low->save();
            }

            return $this->responseCode($code);
        }

        $res = $see->getStatus($ticket);
        if (!$res->isSuccess()) {
            $code = $res->getError()->getCode();

            if($code === 'HTTP') {
                $low = LowCommunication::find($low_id);
                $low->ticket        =   $ticket;
                $low->status_sunat  =   1;
                $low->save();
            } else {
                $sc = SunatCode::where('code', $code)->first();
                $low = LowCommunication::find($low_id);
                $low->ticket        =   $ticket;
                $low->status_sunat  =   1;
                $low->sunat_code_id =   $sc->id;
                $low->save();
            }

            return $this->responseCode($code);
        }

        $cdr = $res->getCdrResponse();
        $util->writeCdr($low_s, $res->getCdrZip());

        $sc = SunatCode::where([
            ['code', $res->getCode()]
        ])->first();

        if($sc == null) {
            $sc = SunatCode::where([
                ['code', $cdr->getCode()]
            ])->first();
        }

        $low = LowCommunication::find($low_id);
        $low->ticket        =   $ticket;
        $low->status_sunat  =   1;
        $low->sunat_code_id =   $sc->id;
        $low->save();

        return true;
    }

    public function sendLowCommunicationSunat(array $data, $low_id)
    {
        $util = Util::getInstance();
        $low_detail = new VoidedDetail();
        $low_detail->setTipoDoc($data['document_type'])
            ->setSerie($data['serial_number'])
            ->setCorrelativo($data['correlative'])
            ->setDesMotivoBaja($data['motive']);

        $low = new Voided();
        $low->setCorrelativo($data['low_correlative'])
            ->setFecGeneracion($data['date_generation'])
            ->setFecComunicacion($data['date_communication'])
            ->setCompany($util->getCompany())
            ->setDetails([$low_detail]);

        // Send to Sunat.
        $see = $util->getSee(SunatEndpoints::FE_BETA);

        $res = $see->send($low);
        $util->writeXml($low, $see->getFactory()->getLastXml());
        $ticket = $res->getTicket();

        if (!$res->isSuccess()) {
            $code = $res->getError()->getCode();

            if($code === 'HTTP') {
                $low = LowCommunication::find($low_id);
                $low->ticket        =   $ticket;
                $low->status_sunat  =   1;
                $low->save();
            } else {
                $sc = SunatCode::where('code', $code)->first();
                $low = LowCommunication::find($low_id);
                $low->ticket        =   $ticket;
                $low->status_sunat  =   1;
                $low->sunat_code_id =   $sc->id;
                $low->save();
            }

            return $this->responseCode($code);
        }

        $res = $see->getStatus($ticket);
        if (!$res->isSuccess()) {
            $code = $res->getError()->getCode();

            if($code === 'HTTP') {
                $low = LowCommunication::find($low_id);
                $low->ticket        =   $ticket;
                $low->status_sunat  =   1;
                $low->save();
            } else {
                $sc = SunatCode::where('code', $code)->first();
                $low = LowCommunication::find($low_id);
                $low->ticket        =   $ticket;
                $low->status_sunat  =   1;
                $low->sunat_code_id =   $sc->id;
                $low->save();
            }

            return $this->responseCode($code);
        }

        $cdr = $res->getCdrResponse();
        $util->writeCdr($low, $res->getCdrZip());

        $sc = SunatCode::where([
            ['code', $res->getCode()]
        ])->first();

        if($sc == null) {
            $sc = SunatCode::where([
                ['code', $cdr->getCode()]
            ])->first();
        }

        $low = LowCommunication::find($low_id);
        $low->ticket        =   $ticket;
        $low->status_sunat  =   1;
        $low->sunat_code_id =   $sc->id;
        $low->save();

        return true;
    }

    public function lowCommunications()
    {
        return view('commercial.low.index');
    }

    public function getClient($data, $document_type)
    {
        $client = new Client();
        $client->setTipoDoc($document_type->code);
        if($data->document != null) {
            $client->setNumDoc($data->document);
        } else {
            $client->setNumDoc('');
        }
        if($data->description != null) {
            $client->setRznSocial($data->description);
        } else {
            $client->setRznSocial('');
        }

        return $client;
    }

    /**
     * Referral Guide
     * @throws \Exception
     */
    public function sendReferralGuide($id)
    {
        $referral = ReferenceGuide::with('type_voucher', 'docDriver', 'docTransport', 'docReceiver')->where('id', $id)->first();
        $referral_detail = ReferenceGuideDetail::with('product')->where('reference_guide_id', $id)->get();

        $util = Util::getInstance();

        $carrier = new Transportist();
        $carrier->setTipoDoc($referral->docTransport->code)
            ->setNumDoc($referral->transport_document)
            ->setRznSocial($referral->transport_name)
            ->setPlaca($referral->vehicle)
            ->setChoferTipoDoc($referral->docDriver->code)
            ->setChoferDoc($referral->driver_name);

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
            $motive = 'Otras no incluida en los puntos anteriores';
        }

        $shipping = new Shipment();
        $shipping->setModTraslado($referral->modality)
            ->setCodTraslado($code_motive)
            ->setDesTraslado($motive)
            ->setFecTraslado(new \DateTime($referral->traslate))
            ->setPesoTotal($referral->weight)
            ->setUndPesoTotal('KGM')
            ->setLlegada(new Direction($referral->ubigeo_arrival->code, $referral->arrival_address))
            ->setPartida(new Direction($referral->ubigeo_start->code, $referral->start_address))
            ->setTransportista($carrier);

        $dispatched = new Despatch();
        $dispatched->setTipoDoc($referral->type_voucher->code)
            ->setSerie($referral->serialnumber)
            ->setCorrelativo($referral->correlative)
            ->setFechaEmision(new \DateTime(date('Y-m-d H:i:s', strtotime($referral->date))))
            ->setCompany($util->getCompany())
            ->setDestinatario((new Client())
                ->setTipoDoc($referral->docReceiver->code)
                ->setNumDoc($referral->receiver_document)
                ->setRznSocial($referral->receiver))
            ->setEnvio($shipping);

        /*if(isset($rel)) {
            $dispatched->setRelDoc($rel);
        }*/

        $details = array();
        foreach($referral_detail as $d) {
            $detail = new DespatchDetail();
            $detail->setCantidad($d->quantity)
                ->setUnidad('NIU')
                ->setDescripcion($d->product->description);

            array_push($details, $detail);
        }

        $dispatched->setDetails($details);

        // Envio a SUNAT.
        $see = $util->getSee(SunatEndpoints::FE_BETA);

        $res = $see->send($dispatched);

        $util->writeXml($dispatched, $see->getFactory()->getLastXml());
        if ($res->isSuccess()) {
            $cdr = $res->getCdrResponse();
            $sc = SunatCode::where([
                        ['code', $cdr->getCode()]
                    ])->first();
            $ref = ReferenceGuide::find($id);
            $ref->status_sunat      =   1;
            $ref->response_sunat    =   $sc->id;
            $ref->save();
            /**@var $res BillResult*/
            $util->writeCdr($dispatched, $res->getCdrZip());

            $response['response'] = true;
            return response()->json($response);
        } else {
            $cdr = $res->getCdrResponse();
            $util->writeCdr($dispatched, $res->getCdrZip());
            $code = $res->getError()->getCode();
            $sc = SunatCode::where('code', $code)->first();
            $sales = ReferenceGuide::find($referral->id);
            $sales->status_sunat = 1;
            $sales->response_sunat = $sc->id;
            $sales->save();

            return response()->json($this->responseCode($code));
        }
    }

    public function responseCode($code)
    {
        if($code > 0 && $code <= 1999) {
            return -1;
        } else if($code >= 2000 && $code <= 3999) {
            return -2;
        } else if($code >= 4000) {
            return -3;
        } else {
            return -4;
        }
    }

    public function sendRetention($id)
    {
        $ret = P::with('sunat_code',
            'customer',
            'regime',
            'detail',
            'type_voucher',
            'detail.sale.type_voucher'
        )->where('id', $id)->first();

        $util = Util::getInstance();
        $retention = new Retention();
        $retention
            ->setSerie($ret->serial_number)
            ->setCorrelativo($ret->correlative)
            ->setFechaEmision(new \DateTime($ret->issue))
            ->setCompany($util->getCompany())
            ->setProveedor($this->getClient($ret->customer, $ret->customer->document_type))
            ->setObservacion($ret->observation)
            ->setImpRetenido($this->format($ret->retained_amount))
            ->setImpPagado($this->format($ret->amount_paid))
            ->setRegimen($ret->regime->code)
            ->setTasa($ret->regime->rate);

        $change = new Exchange();
        $change->setFecha(new \DateTime())
            ->setFactor(1)
            ->setMonedaObj('PEN')
            ->setMonedaRef('PEN');

        $details = array();
        foreach($ret->detail as $r) {
            $pays = array();
            $pay = new Payment();

            $pay->setMoneda('PEN')
                ->setFecha(new \DateTime())
                ->setImporte($this->format($r->no_retention));
            array_push($pays, $pay);

            $detail = new RetentionDetail();
            $detail->setTipoDoc('01')
                ->setNumDoc($r->sale->serialnumber . '-' . $r->sale->correlative)
                ->setFechaEmision(new \DateTime($r->sale->issue))
                ->setFechaRetencion(new \DateTime($ret->issue))
                ->setMoneda('PEN')
                ->setImpTotal($this->format($r->no_retention))
                ->setImpPagar($this->format($r->retained_amount))
                ->setImpRetenido($this->format($r->amount_paid))
                ->setPagos($pays)
                ->setTipoCambio($change);

            array_push($details, $detail);
        }

        $retention->setDetails($details);

        $see = $util->getSee(SunatEndpoints::FE_BETA);
        $res = $see->send($retention);
        $util->writeXml($retention, $see->getFactory()->getLastXml());

        if ($res->isSuccess()) {
            $cdr = $res->getCdrResponse();
            $sc = SunatCode::where([
                ['code', $cdr->getCode()]
            ])->first();
            $rt = R::find($id);
            $rt->status_sunat      =   1;
            $rt->response_sunat    =   $sc->id;
            $rt->save();
            /**@var $res BillResult*/
            $util->writeCdr($retention, $res->getCdrZip());

            return response()->json(true);
        } else {
            $cdr = $res->getCdrResponse();
            $util->writeCdr($retention, $res->getCdrZip());
            $code = $res->getError()->getCode();
            $sc = SunatCode::where('code', $code)->first();
            $rt = R::find($id);
            $rt->status_sunat = 1;
            $rt->response_sunat = $sc->id;
            $rt->save();

            return response()->json($this->responseCode($code));
        }
    }

    public function sendPerception($id)
    {
        $per = P::with('sunat_code',
            'customer',
            'regime',
            'detail',
            'type_voucher',
            'detail.sale.type_voucher'
        )->where('id', $id)->first();

        $util = Util::getInstance();
        $perception = new Perception();
        $perception
            ->setSerie($per->serial_number)
            ->setCorrelativo($per->correlative)
            ->setFechaEmision(new \DateTime($per->issue))
            ->setCompany($util->getCompany())
            ->setProveedor($this->getClient($per->customer, $per->customer->document_type))
            ->setObservacion($per->observation)
            ->setImpPercibido($this->format($per->amount_received))
            ->setImpCobrado($this->format($per->amount_charged))
            ->setRegimen($per->regime->code)
            ->setTasa(2);

        $change = new Exchange();
        $change->setFecha(new \DateTime())
            ->setFactor(1)
            ->setMonedaObj('PEN')
            ->setMonedaRef('PEN');

        $details = array();
        foreach($per->detail as $p) {
            $pays = array();
            $pay = new Payment();

            $pay->setMoneda('PEN')
                ->setFecha(new \DateTime())
                ->setImporte($this->format($p->no_perceived));
            array_push($pays, $pay);

            $detail = new PerceptionDetail();
            $detail->setTipoDoc('01')
                ->setNumDoc($p->sale->serialnumber . '-' . $p->sale->correlative)
                ->setFechaEmision(new \DateTime($p->sale->issue))
                ->setFechaPercepcion(new \DateTime($per->issue))
                ->setMoneda('PEN')
                ->setImpTotal($this->format($p->no_perceived))
                ->setImpCobrar($this->format($p->amount_received))
                ->setImpPercibido($this->format($p->amount_charged))
                ->setCobros($pays)
                ->setTipoCambio($change);

            array_push($details, $detail);
        }

        $perception->setDetails($details);

        $see = $util->getSee(SunatEndpoints::FE_BETA);
        $res = $see->send($perception);

        $util->writeXml($perception, $see->getFactory()->getLastXml());

        if ($res->isSuccess()) {
            $cdr = $res->getCdrResponse();
            $sc = SunatCode::where([
                ['code', $cdr->getCode()]
            ])->first();
            $rt = P::find($id);
            $rt->status_sunat      =   1;
            $rt->response_sunat    =   $sc->id;
            $rt->save();
            /**@var $res BillResult*/
            $util->writeCdr($perception, $res->getCdrZip());

            return response()->json(true);
        } else {
            $cdr = $res->getCdrResponse();
            $util->writeCdr($perception, $res->getCdrZip());
            $code = $res->getError()->getCode();
            $sc = SunatCode::where('code', $code)->first();
            $rt = P::find($id);
            $rt->status_sunat = 1;
            $rt->response_sunat = $sc->id;
            $rt->save();

            return response()->json($this->responseCode($code));
        }
    }

    public function format($number)
    {
        return (double) number_format((double) $number, 2,'.','');
    }
}
