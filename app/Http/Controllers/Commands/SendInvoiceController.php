<?php

namespace App\Http\Controllers\Commands;

use App\Http\Controllers\Sunat\InvoiceController;
use App\Sale;
use App\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\SunatController;

class SendInvoiceController extends Controller
{
    public function consultCDRInvoice($date)
    {
        try {
            $date = date('Y-m-d', strtotime($date));

            $clients = Client::where('production', 1)->pluck('id');

            $sales = Sale::with('client')
                            ->whereHas('client', function($q) {
                                $q->where('production', 1);
                            })
                            ->where('typevoucher_id', 1)
                            ->where('issue', $date)
                            ->where('status', '!=', 2)
                            ->where(function($q) {
                                $q->whereNull('response_sunat');
                                $q->orWhere(function($query) {
                                    $query->where('response_sunat', '!=', 1)
                                            ->where('response_sunat', '<', 2100);
                                });
                            })
                            ->take(50)
                            ->get();

            $status = array();
            $status['count'] = $sales->count();
            $cont = 0;

            foreach ($sales as $sale) {
                $s = Sale::with('client', 'referralguide', 'customer', 'customer.document_type', 'coin',
                            'type_voucher', 'operation', 'detail', 'detail.product', 'detail.product.ot',
                            'detail.igvType', 'headquarter', 'headquarter.ubigeo')
                            ->find($sale->id);

                $arguments = [
                    $s->client->document,
                    $s->type_voucher->code,
                    $s->serialnumber,
                    intval($s->correlative)
                ];

                $sunatGYO = (new SunatController)->consultCDR($arguments, $s->id, $s->client, 1);

                $result = $sunatGYO->getData();

                $status[$cont]['sale'] = $s->id;
                $status[$cont]['consult'] = json_encode($sunatGYO->getContent());

                if ($result->status == false) {
                    $sendSunat = (new InvoiceController)->constructInvoice($s);

                    $status[$cont]['send'] = json_encode($sendSunat);
                }

                $cont++;
            }

            $log = fopen(public_path('storage/log.txt'), 'a');
            $txt = 'CONSULT:'. date('Y-m-d H:i'). ': ' . json_encode($status). "\n";
            fwrite($log, "\n".$txt);
            fclose($log);

            return response()->json($status);
        } catch (\Exception $e) {
            $log = fopen(public_path('storage/log.txt'), 'a');
            $txt = 'CONSULT ERROR' . date('Y-m-d H:i') . " : " . $e . ' ' . $e->getMessage()  . "\n";
            fwrite($log, "\n".$txt);
            fclose($log);

            return response()->json(false);
        }
    }
}
