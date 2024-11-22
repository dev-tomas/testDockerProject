<?php

namespace App\Http\Controllers\Commands;

use App\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Manage\ConsulCPEController;

class ConsutlCPEController extends Controller
{
    public function consultAuthomaticOfDay()
    {
        $yesterday = Carbon::yesterday()->format('Y-m-d');

        $sales = Sale::with('client', 'type_voucher')
                        ->whereHas('client', function($query) {
                            $query->where('production', 1);
                        })
                        ->where('status', '!=', 2)
                        ->where('issue', $yesterday)
                        ->get();

        foreach ($sales as $sale) {
            $args = self::constructArgs($sale);
            $consult = (new ConsulCPEController)->consultCPE($args);

            if ($consult['success']) {
                $sale->cpe_status = $consult['cpeStatus'];
                $sale->invoice_status = self::setCPEStatus($consult['cpeStatus'], $sale);
                $sale->save();
            } else {
                $sale->cpe_status = '-';
                $sale->save();
            }
        }

        return response()->json(true);
    }

    public function consultManual($sale)
    {
        $sale = Sale::with('client', 'type_voucher')
                        ->whereHas('client', function($query) {
                            $query->where('production', 1);
                        })
                        ->where('status', '!=', 2)
                        ->find($sale);

        if ($sale == null) {
            return response()->json(['success' => false, 'message' => 'La consulta del estado del comprobante solo esta disponible para clientes en produccion']);
        }

        $args = self::constructArgs($sale);
        $consult = (new ConsulCPEController)->consultCPE($args);

        if ($consult['success']) {
            $sale->cpe_status = $consult['cpeStatus'];
            $sale->invoice_status = self::setCPEStatus($consult['cpeStatus'], $sale);
            $sale->save();
            $status = '-';

            switch ($consult['cpeStatus']) {
                case '0': $status = 'NO EXISTE'; break;
                case '1': $status = 'ACEPTADO'; break;
                case '2': $status = 'ANULADO'; break;
                case '3': $status = 'AUTORIZADO'; break;
                case '4': $status = 'NO AUTORIZADO'; break;
            }

            return response()->json(['success' => true, 'message' => "Estado del comprobante en SUNAT: {$status}"]);
        } else {
            $sale->cpe_status = '-';
            $sale->save();
        }

        return response()->json(['success' => false, 'message' => $consult['message']]);
    }


    public function consultbyDate($date)
    {
        $issue = date('Y-m-d', strtotime($date));

        $sales = Sale::with('client', 'type_voucher')
                        ->whereHas('client', function($query) {
                            $query->where('production', 1);
                        })
                        ->where('status', '!=', 2)
                        ->where('issue', $issue)
                        // ->orWhere('date', $issue)
                        ->where(function($query) {
                            $query->where(function($q) {
                                $q->whereNull('cpe_status')
                                    ->whereNull('invoice_status');
                            });

                            $query->orWhere(function($q) {
                                $q->where('cpe_status', 0)
                                    ->where('invoice_status', 0);
                            });
                            $query->orWhere(function($q) {
                                $q->where('cpe_status', '-')
                                    ->whereNull('invoice_status');
                            });
                            $query->orWhere(function($q) {
                                $q->where('cpe_status', 3)
                                    ->where('invoice_status', 2);
                            });
                            $query->orWhere(function($q) {
                                $q->whereNull('cpe_status')
                                    ->where('invoice_status', 0);
                            });
                        })
                        ->take(100)
                        ->get();

        foreach ($sales as $sale) {
            $args = self::constructArgs($sale);
            $consult = (new ConsulCPEController)->consultCPE($args);

            if ($consult['success']) {
                $sale->cpe_status = $consult['cpeStatus'];
                $sale->invoice_status = self::setCPEStatus($consult['cpeStatus'], $sale);
                $sale->save();
            } else {
                $sale->cpe_status = '-';
                $sale->save();
            }
        }

        return response()->json(true);
    }

    private function setCPEStatus($status, $sale)
    {
        if ($this->isSend($sale) && $this->isAccepted($sale) && $status == 0) {
            return 0; // NO EXISTE
        }

        if ($this->isLow($sale) && $status != 2) {
            return 2; // NO ANULADO - NO EXISTE
        }

        if ($this->isLow($sale) && $status == 2) {
            return 9; // COINCIDE
        }

        if ($this->isSend($sale) && $this->isAccepted($sale) && $status == 1) {
            return 9; // COINCIDE
        }
    }

    public function isLow($sale)
    {
        return $sale->low_communication_id != null;
    }

    public function isSend($sale)
    {
        return $sale->status_sunat == 1;
    }

    public function isAccepted($sale)
    {
        return $sale->response_sunat == 1;
    }

    private function constructArgs($sale) {
        $args = array();

        $args['ruc'] = $sale->client->document;
        $args['typeDoc'] = $sale->type_voucher->code;
        $args['serie'] = $sale->serialnumber;
        $args['correlative'] = $sale->correlative;
        $args['date'] = date('d/m/Y', strtotime($sale->issue));
        $args['total'] = $sale->total;

        return $args;
    }
}
