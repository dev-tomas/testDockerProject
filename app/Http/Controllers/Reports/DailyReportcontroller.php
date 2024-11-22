<?php

namespace App\Http\Controllers\Reports;

use App\Exports\ReportDailyExport;
use App\HeadQuarter;
use App\Sale;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class DailyReportcontroller extends Controller
{
    public function __construct()
    {
        $this->middleware('can:reportediario.show');
    }

    public function index()
    {
        $headquarters = HeadQuarter::where('client_id', auth()->user()->headquarter->client_id)->get(['id', 'description']);

        return view('commercial.reports.daily.index', compact('headquarters'));
    }

    public function generate(Request $request)
    {
        $data = $this->getData($request);

        return response()->json($data);
    }

    public function excel(Request $request)
    {
        $data = $this->getData($request);

        return Excel::download(new ReportDailyExport($data), 'REPORTE DE INGRESOS DIARIO.xlsx');
    }

    public function getData($request)
    {
        $from = Carbon::createFromFormat('d/m/Y', Str::before($request->dates, ' -'))->format('Y-m-d');
        $to = Carbon::createFromFormat('d/m/Y', Str::after($request->dates, '- '))->format('Y-m-d');

        $sales = Sale::with('type_voucher', 'customer', 'coin', 'detail')
                        ->where('client_id', auth()->user()->headquarter->client_id)
                        ->whereBetween('date', [$from, $to])
                        ->whereNull('low_communication_id')
                        ->whereNull('credit_note_id')
                        ->where('status', 1)
                        ->where(function($query) use ($request) {
                            if ($request->headquarter_filter != '') {
                                $query->where('headquarter_id', $request->headquarter_filter);
                            }
                        })
                        ->get();

        $data = [];
        $cont = 0;

        $totalQuantity = 0;

        $cashPen = 0;
        $depositoPen = 0;
        $tarjetaPen = 0;
        $creditoPen = 0;

        $cashUsd = 0;
        $depositoUsd = 0;
        $tarjetaUsd = 0;
        $creditoUsd = 0;

        $data['details'] = [];

        foreach ($sales as $sale) {
            $data['details'][$cont]['date'] = date('d-m-Y', strtotime($sale->date));
            $data['details'][$cont]['document'] = "{$sale->serialnumber}-{$sale->correlative}";
            $data['details'][$cont]['type_voucher'] = $sale->type_voucher->description;
            $data['details'][$cont]['customer'] = $sale->customer->description;
            $data['details'][$cont]['coin'] = $sale->coin->code_str;
            $data['details'][$cont]['payment_doc'] = '';
            $data['details'][$cont]['cash'] = $sale->condition_payment == 'EFECTIVO' ? $sale->total : 0;
            $data['details'][$cont]['deposito'] = $sale->condition_payment == 'DEPOSITO EN CUENTA' ? $sale->total : 0;
            $data['details'][$cont]['credito'] = $sale->condition_payment == 'CREDITO' ? $sale->total : 0;
            $data['details'][$cont]['tarjeta'] = $sale->condition_payment == 'TARJETA DE CREDITO' || $sale->condition_payment == 'TARJETA DE DEBITO' ? $sale->total : 0;

            if ($sale->coin_id == 1) {
                if ($sale->condition_payment == 'EFECTIVO') {
                    $cashPen = (float) $cashPen + (float) $sale->total;
                }
                if ($sale->condition_payment == 'DEPOSITO EN CUENTA') {
                    $depositoPen = (float) $depositoPen + (float) $sale->total;
                }
                if ($sale->condition_payment == 'CREDITO') {
                    $creditoPen = (float) $creditoPen + (float) $sale->total;

                }
                if ($sale->condition_payment == 'TARJETA DE CREDITO' || $sale->condition_payment == 'TARJETA DE DEBITO') {
                    $tarjetaPen = (float) $tarjetaPen + (float) $sale->total;
                }
            } else {
                if ($sale->condition_payment == 'EFECTIVO') {
                    $cashUsd = (float) $cashUsd + (float) $sale->total;
                }
                if ($sale->condition_payment == 'DEPOSITO EN CUENTA') {
                    $depositoUsd = (float) $depositoUsd + (float) $sale->total;
                }
                if ($sale->condition_payment == 'CREDITO') {
                    $creditoUsd = (float) $creditoUsd + (float) $sale->total;
                }
                if ($sale->condition_payment == 'TARJETA DE CREDITO' || $sale->condition_payment == 'TARJETA DE DEBITO') {
                    $tarjetaUsd = (float) $tarjetaUsd + (float) $sale->total;
                }
            }

            $contDetail = 0;
            $data['destails'][$cont]['detail'] = [];

            foreach ($sale->detail as $detail) {
                $totalQuantity = $detail->quantity + $totalQuantity;

                $data['details'][$cont]['detail'][$contDetail]['product'] = $detail->product->description;
                $data['details'][$cont]['detail'][$contDetail]['operation'] = $detail->product->measure->description;
                $data['details'][$cont]['detail'][$contDetail]['quantity'] = number_format($detail->quantity, 2, '.', '');
                $data['details'][$cont]['detail'][$contDetail]['price'] = number_format($detail->price, 2, '.', '');

                $contDetail++;
            }

            $cont++;
        }

        $data['total_quantity'] = number_format($totalQuantity, 2, '.', '');

        $data['cash_pen'] = number_format($cashPen, 2, '.', '');
        $data['deposito_pen'] = number_format($depositoPen, 2, '.', '');
        $data['tarjeta_pen'] = number_format($tarjetaPen, 2, '.', '');
        $data['credito_pen'] = number_format($creditoPen, 2, '.', '');

        $data['cash_usd'] = number_format($cashUsd, 2, '.', '');
        $data['deposito_usd'] = number_format($depositoUsd, 2, '.', '');
        $data['tarjeta_usd'] = number_format($tarjetaUsd, 2, '.', '');
        $data['credito_usd'] = number_format($creditoUsd, 2, '.', '');

        return collect($data);
    }
}
