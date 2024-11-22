<?php

namespace App\Http\Controllers\Commercial;

use App\BankAccount;
use App\Sale;
use App\Taxe;
use chillerlan\QRCode\QRCode;
use DB;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use NumerosEnLetras;

class SalesController extends Controller
{
    public $headquarter;
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('status.client');
        $this->middleware(function ($request, $next) {
            $this->headquarter = session()->has('headlocal') == true ? session()->get('headlocal') : Auth::user()->headquarter_id;
            return $next($request);
        });
    }

    public function changePayment(Request $request)
    {
        DB::beginTransaction();
        try {
            $sale = Sale::find($request->change_payment_sale);
            $sale->condition_payment = $request->change_condition;
            $sale->cash_id = null;
            $sale->bank_account_id = null;
            $sale->payment_method_id = null;
            if ($request->change_condition == 'EFECTIVO') {
                $sale->cash_id = $request->change_cash;
            }
            if ($request->change_condition == 'DEPOSITO EN CUENTA') {
                $sale->bank_account_id = $request->change_bank;
            }
            if ($request->change_condition == 'TARJETA DE CREDITO' && $request->change_condition == 'TARJETA DE DEBITO') {
                $sale->payment_method_id = $request->change_method;
            }
            $sale->can_change_payment = 1;
            $sale->save();

            $qr = (new QRCode)->render($sale->qr_text);
            $qrCode = $sale->qr_text;
            $hash = $sale->hash_code;
            $decimal = Str::after($sale->total, '.');
            $int = Str::before($sale->total, '.');
            $leyenda = NumerosEnLetras::convertir($int) . ' con ' . $decimal . '/100';

            $bankInfo = BankAccount::where('client_id', $sale->client_id)->get();
            $clientInfo = $sale->client;
            $igv = Taxe::where('id', '=',1)->first();

            if($sale->client->invoice_size == 1) {
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

            $folder_client = $sale->client->document;

            /**
             * Guardar el PDF
             */
            $pdf_folder = '/public/pdf/' . $folder_client . '/';
            Storage::disk('local')->put($pdf_folder . $sale->serialnumber . '-' . $sale->correlative . '.pdf',
                $dompdf->output());

            DB::commit();

            return response()->json(true);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(false);
        }
    }
}
