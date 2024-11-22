<?php

namespace App\Http\Controllers;

use DB;
use App\Sale;
use App\Client;
use App\Summary;
use App\DebitNote;
use App\Quotation;
use App\CreditNote;
use App\SaleDetail;
use App\Correlative;
use App\SummaryDetail;
use App\ReferenceGuide;
use App\DebitNoteDetail;
use App\QuotationDetail;
use App\CreditNoteDetail;
use App\LowCommunication;
use Illuminate\Http\Request;
use App\ReferenceGuideDetail;
use App\LowCommunicationDetail;

class ProductionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function production(Request $request)
    {
        date_default_timezone_set("America/Lima");
        $idUser     = auth()->id();
        $idClient   = auth()->user()->headquarter->client_id;
        $date       = date("Y-m-d H:i:s");

        DB::beginTransaction();
        try {

            $sal = Sale::where('client_id', $idClient)->get();
            foreach ($sal as $s) {
                $sale = Sale::find($s->id);
                $sale->low_communication_id = null;
                $sale->debit_note_id = null;
                $sale->credit_note_id = null;
				$sale->quotation_id = null;
                $sale->update();
            }

            /**
             * Low Communications
             */


            $client                 = Client::find($idClient);
            $client->production     = 1;
            $client->production_at  = $date;

            $lows = LowCommunication::where('client_id', $idClient)->get();
            foreach($lows as $l) {
                $lowDetail = LowCommunicationDetail::where('low_communication_id', $l->id)->get()->each;
                if($lowDetail->delete()){
                    $l->delete();
                } else {
                    echo 'error';
                }
            }

            /**
             * Summaries
             */
            $summaries = Summary::where('client_id', $idClient)->get();
            foreach ($summaries as $s) {
                $summary_detail = SummaryDetail::where('summary_id', $s->id)->get()->each;
                if($summary_detail->delete()) {
                    $s->delete();
                } else {
                    echo 'error';
                }
            }

            /**
             * Guides
             */
            $referral_guide = ReferenceGuide::where('client_id', $idClient)->get();
            foreach ($referral_guide as $rg) {
                $referral_guide_detail = ReferenceGuideDetail::where('client_id', $rg->id)->get()->each;
                if($referral_guide_detail->delete()) {
                    $rg->delete();
                } else {
                    echo 'error';
                }
            }

            /**
             * Credit Notes
             */
            $credit_notes = CreditNote::where('client_id', $idClient)->get();
            foreach ($credit_notes as $cn) {
                $credit_note_detail = CreditNoteDetail::where('credit_note_id', $cn->id)->get()->each;
                if($credit_note_detail->delete()) {
                    $cn->delete();
                } else {
                    echo 'error';
                }
            }

            /**
             * Debit Notes
             */
            $debit_notes = DebitNote::where('client_id', $idClient)->get();
            foreach ($debit_notes as $dn) {
                $debit_note_detail = DebitNoteDetail::where('debit_note_id', $dn->id)->get()->each;
                if($debit_note_detail->delete()) {
                    $dn->delete();
                } else {
                    echo 'error';
                }
            }

            $cotizaciones = Db::table('quotations')
				->join('headquarters', 'quotations.headquarter_id', '=', 'headquarters.id')
				->where('headquarters.client_id', auth()->user()->headquarter->client_id)
				->get([
					'quotations.id',
				]);

            foreach ($cotizaciones as $c) {
				$cotd = QuotationDetail::where('quotation_id', $c->id)->get()->each;
				if($cotd->delete()) {
					$cot = Quotation::find($c->id);
					$cot->delete();
				}
			}

            if($client->update()){

                $sales = Sale::where('client_id', $idClient)
                                ->get([
                                    'id'
                                ]);

                foreach($sales as $sale){
                    $saleDetail = SaleDetail::where('sale_id', $sale->id)->get()->each;

                    if($saleDetail->delete()){

                        $sal = Sale::find($sale->id);
                        if($sal->delete()){


                        }else{
                            $rpta = array(
                                'response'            => false,
                                'message'           => 'ERROR AL MOMENTO DE ELIMINAR LAS VENTA '.$sale->id,
                            );

                            return json_encode($rpta);
                        }

                    }else{
                        $rpta = array(
                            'response'            => false,
                            'message'           => 'ERROR AL MOMENTO DE ELIMINAR EL DETALLE DE LA VENTA '.$sale->id,
                        );

                        return json_encode($rpta);
                    }
                }

                DB::commit();
                $rpta = array(
                    'response'            => true,
                    'message'           => 'MODO PRODUCCIÓN ACTIVADO',
                );

                $correlatives = Correlative::where('client_id', $idClient)->get();
                foreach ($correlatives as $c) {
                    $c = Correlative::find($c->id);
                    $c->correlative = '000000';
                    $c->save();
                }

                return json_encode($rpta);

            }else{
                $rpta = array(
                    'response'            => false,
                    'message'           => 'ERROR AL MOMENTO DE ACTUALIZAR EL CLIENTE',
                );

                return json_encode($rpta);
            }
        }catch (\Exception $e) {
            DB::rollBack();
            $rpta = array(
                'respuesta' => false,
                'mensaje'   => $e->getMessage()
            );

            return json_encode($rpta);

        }
    }
}
