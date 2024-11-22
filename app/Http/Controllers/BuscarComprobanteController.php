<?php

namespace App\Http\Controllers;

use PDF;
use App\Sale;
use App\Client;
use App\Customer;
use App\DebitNote;
use App\CreditNote;
use App\SaleDetail;
use App\BankAccount;
use App\TypeVoucher;
use NumerosEnLetras;
use App\TypeDocument;
use MongoDB\BSON\Type;
use App\ReferenceGuide;
use BaconQrCode\Writer;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use BaconQrCode\Renderer\Image\Png;
use Greenter\Report\Render\QrRender;
use App\Http\Controllers\SunatController;
use BaconQrCode\Common\ErrorCorrectionLevel;

class BuscarComprobanteController extends Controller
{
    public function index ($ruc = null)
    {
        return view('buscar-comprobante.index', compact('ruc'));
    }

    public function search(Request $request)
    {
        $ruc = $request->ruc;
        $voucher = $request->voucher;
        $client = Client::where('document', $request->ruc)->first();
        $typeVoucher = TypeVoucher::where('code', $request->voucher)->first();
        $document = TypeDocument::where('code', $request->type_document)->first();

        if ($request->voucher == '01' || $request->voucher == '03') {
            $sale = Sale::with('coin')->where('serialnumber', $request->serie)->where('correlative', $request->number)->where('typevoucher_id', $typeVoucher->id)
                        ->where('client_id', $client->id)->where('total', $request->total)->first(); 
            $serie = $sale->serialnumber;
            $issue = $sale->issue;
            $expiration = $sale->expiration;
        } else if($request->voucher == '07') {
            $sale = CreditNote::where('serial_number', $request->serie)
                                ->where('correlative', $request->number)
                                ->where('typevoucher_id', 4)
                                ->orWhere('typevoucher_id', 3)
                                ->where('client_id', $client->id)
                                ->where('total', $request->total)
                                ->first();
            $serie = $sale->serial_number;
            $issue = $sale->date_issue;
            $expiration = $sale->due_date;
        } else if($request->voucher == '08') {
            $sale = DebitNote::where('serial_number', $request->serie)
                                ->where('correlative', $request->number)
                                ->where('typevoucher_id', 6)
                                ->orWhere('typevoucher_id', 5)
                                ->where('client_id', $client->id)
                                ->where('total', $request->total)
                                ->first();
            $serie = $sale->serial_number;
            $issue = $sale->date_issue;
            $expiration = $sale->due_date;
        } else if($request->voucher == '09') {
            $sale = ReferenceGuide::where('serialnumber', $request->serie)
                                ->where('correlative', $request->number)
                                ->where('typevoucher_id', 7)
                                ->where('client_id', $client->id)
                                ->first();
            $serie = $sale->serialnumber;
            $issue = $sale->date;
            $expiration = false;
        }

        if ($sale == null) {
            return 'error';
        }

        $customer = Customer::where('client_id', $client->id)->where('id',$sale->customer_id)->first();


        $xml = asset('files/xml/'.$request->ruc . '-' . $sale->type_voucher->code . '-' . $serie . '-' . $sale->correlative . '.xml');


        return view('buscar-comprobante.show', compact('xml', 'customer', 'typeVoucher', 'sale', 'ruc','voucher', 'serie', 'issue', 'expiration'));
    }

    public function showPdfSale($id)
    {

        
        $sunat = new SunatController;
        $sale = Sale::where('id', $id)->with('coin', 'type_voucher')->first();
        $invoice = $sunat->convertSale($id);
        $qrCode = $this->getImage($invoice);
        $sale_detail = SaleDetail::where('sale_id', $id)->with('product', 'product.coin')->get();
        $decimal = Str::after($sale->total, '.');
        $int = Str::before($sale->total, '.');
        $leyenda = NumerosEnLetras::convertir($int) . ' con ' . $decimal . '/100';
        $bankInfo = BankAccount::where('client_id', Auth::user()->headquarter->client_id)->first();
        $clientInfo = Client::find(Auth::user()->headquarter->client_id);
        $customerInfo = Customer::find($sale->customer_id);
        $igv = DB::table('taxes')->where('id', '=',1)->first();
        $data = array(
            'sale'         =>  $sale,
            'sale_detail'  =>  $sale_detail,
            'leyenda'           =>  $leyenda,
        );

        if($clientInfo->ticket_size == 0) {
            $pdf = PDF::loadView('commercial.sale.pdf', compact('sale','sale_detail','leyenda', 'bankInfo', 'clientInfo','customerInfo', 'invoice','igv', 'qrCode'))->setPaper('A4');
        } else {
            $pdf = PDF::loadView('commercial.sale.ticket', compact('sale','sale_detail','leyenda', 'bankInfo', 'clientInfo','customerInfo', 'invoice','igv', 'qrCode'))->setPaper('A4');
        }
        return $pdf->stream('VENTA ' . $sale->serialnumber . '-' . $sale->correlative . '.pdf');
    }
    public function getImage($sale)
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

        return $content;
    }

    private function getQrImage($content)
    {
        $renderer = new Png();
        $renderer->setHeight(120);
        $renderer->setWidth(120);
        $renderer->setMargin(0);
        $writer = new Writer($renderer);
        $qrCode = $writer->writeString($content, 'UTF-8', ErrorCorrectionLevel::Q);

        return $qrCode;
    }
}
