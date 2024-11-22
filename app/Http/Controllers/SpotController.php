<?php

namespace App\Http\Controllers;

use App\Client;
use App\Coin;
use App\Correlative;
use App\Customer;
use App\Perception;
use App\PerceptionDetail;
use App\Regime;
use App\Retention;
use App\RetentionDetail;
use App\Sale;
use App\TypeDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use PDF;

class SpotController extends SunatController
{
    public $headquarter;
    public $_ajax;
    public function __construct()
    {
        $this->_ajax = new AjaxController();
        $this->middleware(function ($request, $next) {
            $this->headquarter = session()->has('headlocal') == true ? session()->get('headlocal') : Auth::user()->headquarter_id;
            return $next($request);
        });
    }

    public function retentions()
    {
        return view('accounting.retention.index');
    }

    public function createRetention()
    {
        $date = date('d-m-Y');
        $correlative = Correlative::where([
            ['client_id', '=', Auth()->user()->headquarter->client_id],
            ['typevoucher_id', '=', 23]
        ])->first();

        $data = array(
            'customers'     =>  $this->_ajax->getCustomers(),
            'coins' => Coin::all(),
            'typedocuments' => TypeDocument::all(),
            'correlative'   =>  $correlative,
            'currentDate'   =>  $date,
            'regimes'       =>  Regime::where('type', 0)->get(),
            'sales'         =>  Sale::where([['typevoucher_id', 1], ['headquarter_id', $this->headquarter]])->get()
        );

        return view('accounting.retention.create')->with($data);
    }

    public function dt_retention()
    {
        $retentions = Retention::with('sunat_code',
            'customer',
            'regime',
            'detail',
            'type_voucher',
            'detail.sale.type_voucher',
            'customer',
            'customer.document_type'
        )->where('headquarter_id', $this->headquarter)->get();
        return datatables()->of($retentions)->toJson();
    }

    public function showPdfRetention($id)
    {
        $util = \Util::getInstance();
        $retention = Retention::with('sunat_code',
            'customer',
            'regime',
            'detail',
            'type_voucher',
            'detail.sale.type_voucher'
        )->where('id', $id)->first();
        $invoice = $this->convertRetention($id);
        $hash = $util->getHash($invoice);
        $clientInfo = Client::find(Auth::user()->headquarter->client_id);

        $pdf = PDF::loadView('accounting.retention.pdf',
            compact('clientInfo', 'retention', 'hash'))->setPaper('A4');

        return $pdf->stream('RETENCIÓN ' . $retention->serial_number . '-' . $retention->correlative . '.pdf');
    }

    /**
     * Crud Retention
     * @param Request $request
     * @return JsonResponse
     */
    public function storeRetention(Request $request)
    {
        DB::beginTransaction();
        try{
            $correlatives = Correlative::where([
                ['serialnumber', $request->serial_number],
                ['headquarter_id', $this->headquarter],
                ['typevoucher_id', '23']
            ])->first();

            $setCorrelative = (int) $correlatives->correlative + 1;
            $repeat = strlen($correlatives->correlative) - strlen($setCorrelative);
            $final = str_repeat('0',($repeat >=0) ? $repeat : 0).$setCorrelative;

            $correlative = Correlative::findOrFail($correlatives->id);
            $correlative->correlative = $final;
            $correlative->save();

            $retention = new Retention;
            $retention->serial_number       =   $correlatives->serialnumber;
            $retention->correlative         =   $final;
            $retention->issue               =   date('Y-m-d', strtotime($request->issue_create));
            $retention->coin                =   $request->coin;
            $retention->observation         =   $request->observation;
            $retention->retained_amount     =   $request->retained;
            $retention->amount_paid         =   $request->total;
            $retention->amount              =   $request->subtotal;
            /*$retention->exchange_factor     =   $request->exchange_factor;
            $retention->exchange_obj        =   $request->exchange_obj;
            $retention->exchange_ref        =   $request->exchange_ref;*/
            $retention->customer_id         =   $request->customer;
            $retention->regime_id           =   $request->regime;
            $retention->user_created        =   Auth::user()->id;
            $retention->user_updated        =   Auth::user()->id;
            $retention->status_sunat        =   0;
            $retention->status_sunat        =   0;
            $retention->observation         =   $request->observation;
            $retention->typevoucher_id      =   23;
            $retention->headquarter_id      =   $this->headquarter;
            $retention->save();

            for($x = 0;$x < count($request->sale); $x++) {
                if($request->issue[$x] != '') {
                    $retention_detail = new RetentionDetail;
                    $retention_detail->coin             =   'PEN';
                    $retention_detail->no_retention     =   $request->no_retention[$x];
                    $retention_detail->dues             =   1;
                    $retention_detail->payment_number   =   1;
                    $retention_detail->retained_amount  =   $request->retained_amount[$x];
                    $retention_detail->amount_paid      =   $request->amount_paid[$x];
                    $retention_detail->line_modify      =   1;
                    $retention_detail->retention_id     =   $retention->id;
                    $retention_detail->sale_id          =   $request->sale[$x];
                    $retention_detail->save();
                }
            }

            $response = $this->sendRetention($retention->id);

            DB::commit();

            return response()->json($response);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage());
        }
    }

    public function perceptions()
    {
        return view('accounting.perception.index');
    }

    public function createPerception()
    {
        $date = date('d-m-Y');
        $correlative = Correlative::where([
            ['client_id', '=', Auth()->user()->headquarter->client_id],
            ['typevoucher_id', '=', 24]
        ])->first();

        $data = array(
            'customers'     =>  $this->_ajax->getCustomers(),
            'coins' => Coin::all(),
            'typedocuments' => TypeDocument::all(),
            'correlative'   =>  $correlative,
            'currentDate'   =>  $date,
            'regimes'       =>  Regime::where('type', 1)->get(),
            'sales'         =>  Sale::where([['typevoucher_id', 1], ['headquarter_id', $this->headquarter]])->get()
        );

        return view('accounting.perception.create')->with($data);
    }

    public function dt_perception()
    {
        $perceptions = Perception::with('sunat_code',
            'customer',
            'regime',
            'detail',
            'type_voucher',
            'detail.sale.type_voucher',
            'customer',
            'customer.document_type'
        )->where('headquarter_id', $this->headquarter)->get();
        return datatables()->of($perceptions)->toJson();
    }

    public function showPdfPerception($id)
    {
        $util = \Util::getInstance();
        $perception = Perception::with('sunat_code',
            'customer',
            'regime',
            'detail',
            'type_voucher',
            'detail.sale.type_voucher'
        )->where('id', $id)->first();

        $invoice = $this->convertPerception($id);
        $hash = $util->getHash($invoice);
        $clientInfo = Client::find(Auth::user()->headquarter->client_id);

        $pdf = PDF::loadView('accounting.perception.pdf',
            compact('clientInfo', 'perception', 'hash'))->setPaper('A4');

        return $pdf->stream('PERCEPCIÓN ' . $perception->serial_number . '-' . $perception->correlative . '.pdf');
    }

    /**
     * Crud Retention
     * @param Request $request
     * @return JsonResponse
     */
    public function storePerception(Request $request)
    {
        DB::beginTransaction();
        try{
            $correlatives = Correlative::where([
                ['serialnumber', $request->serial_number],
                ['headquarter_id', $this->headquarter],
                ['typevoucher_id', '24']
            ])->first();

            $setCorrelative = (int) $correlatives->correlative + 1;
            $repeat = strlen($correlatives->correlative) - strlen($setCorrelative);
            $final = str_repeat('0',($repeat >=0) ? $repeat : 0).$setCorrelative;

            $correlative = Correlative::findOrFail($correlatives->id);
            $correlative->correlative = $final;
            $correlative->save();

            $perception = new Perception;
            $perception->serial_number       =   $correlatives->serialnumber;
            $perception->correlative         =   $final;
            $perception->issue               =   date('Y-m-d', strtotime($request->issue_create));
            $perception->coin                =   $request->coin;
            $perception->observation         =   $request->observation;
            $perception->amount_received     =   $request->perceived;
            $perception->amount_charged      =   $request->total;
            $perception->amount              =   $request->subtotal;
            /*$perception->exchange_factor     =   $request->exchange_factor;
            $perception->exchange_obj        =   $request->exchange_obj;
            $perception->exchange_ref        =   $request->exchange_ref;*/
            $perception->customer_id         =   $request->customer;
            $perception->regime_id           =   $request->regime;
            $perception->user_created        =   Auth::user()->id;
            $perception->user_updated        =   Auth::user()->id;
            $perception->status_sunat        =   0;
            $perception->status_sunat        =   0;
            $perception->observation         =   $request->observation;
            $perception->typevoucher_id      =   24;
            $perception->headquarter_id      =   $this->headquarter;
            $perception->save();

            for($x = 0;$x < count($request->sale); $x++) {
                if($request->issue[$x] != '') {
                    $perception_detail = new PerceptionDetail;
                    $perception_detail->coin             =   'PEN';
                    $perception_detail->no_perceived     =   $request->no_perceived[$x];
                    $perception_detail->dues             =   1;
                    $perception_detail->payment_number   =   1;
                    $perception_detail->amount_received  =   $request->received[$x];
                    $perception_detail->amount_charged   =   $request->amount_charged[$x];
                    $perception_detail->line_modify      =   1;
                    $perception_detail->perception_id    =   $perception->id;
                    $perception_detail->sale_id          =   $request->sale[$x];
                    $perception_detail->save();
                }
            }

            $response = $this->sendPerception($perception->id);

            DB::commit();

            return response()->json($response);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($e->getMessage());
        }
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
}
