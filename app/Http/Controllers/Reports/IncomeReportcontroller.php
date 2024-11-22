<?php

namespace App\Http\Controllers\Reports;

use App\Exports\ReportIncomeExport;
use App\HeadQuarter;
use App\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class IncomeReportcontroller extends Controller
{
    public function __construct()
    {
        $this->middleware('can:reporteingresos.show');
    }

    public function index()
    {
        $headquarters = HeadQuarter::where('client_id', auth()->user()->headquarter->client_id)->get(['id', 'description']);

        return view('commercial.reports.income.index', compact('headquarters'));
    }

    public function generate(Request $request)
    {
        $data = $this->getData($request);

        return response()->json($data);
    }

    public function excel(Request $request)
    {
        $data = $this->getData($request);

        return Excel::download(new ReportIncomeExport($data), 'REPORTE DE COBRANZAS Y CANJES.xlsx');
    }

    public function getData($request)
    {
        $from = Carbon::createFromFormat('d/m/Y', Str::before($request->dates, ' -'))->format('Y-m-d');
        $to = Carbon::createFromFormat('d/m/Y', Str::after($request->dates, '- '))->format('Y-m-d');

        $sales = Sale::with('type_voucher', 'customer', 'coin', 'detail', 'credit', 'credit.payments')
            ->where('client_id', auth()->user()->headquarter->client_id)
            ->whereBetween('date', [$from, $to])
            ->where(function($query) use ($request) {
                if ($request->headquarter_filter != '') {
                    $query->where('headquarter_id', $request->headquarter_filter);
                }

                if ($request->customer_filter != '') {
                    $query->whereHas('customer', function($q) use ($request) {
                        $q->where('description', 'like', "%$request->customer_filter%")
                            ->orWhere('document', 'like', "%$request->customer_filter%");
                    });
                }

                if ($request->status_filter != '') {
                    if ($request->status_filter == 1 || $request->status_filter == 0) {
                        $query->where('status_condition', $request->status_filter)
                            ->whereNull('low_communication_id')
                            ->whereNull('credit_note_id');
                    } else {
                        if ($request->status_filter == 2) {
                            $query->whereNotNull('low_communication_id');
                        } else {
                            $query->whereNotNull('credit_note_id');
                        }
                    }
                }
            })
            ->get();

        $data = [];
        $cont = 0;

        foreach ($sales as $sale) {
            $status = "CANCELADO";

            if ($sale->low_communication_id != null || $sale->status != 1) {
                $status = 'ANULADO';
            } else if ($sale->credit_note_id != null) {
                $status = 'ANULADO NC';
            } else {
                if ($sale->condition_payment == 'CREDITO') {
                    if ($sale->credito && $sale->credito->status == 0) {
                        $status = 'PENDIENTE';
                    } else {
                        $status = 'CANCELADO';
                    }
                } else {
                    $status = 'CANCELADO';
                }
            }

            $data[$cont]['date'] = date('d-m-Y', strtotime($sale->date));
            $data[$cont]['document'] = "{$sale->serialnumber}-{$sale->correlative}";
            $data[$cont]['condition'] = $sale->condition_payment == 'CREDITO' ? 'CREDITO' : 'CONTADO';
            $data[$cont]['customer'] = $sale->customer->description;
            $data[$cont]['total'] = $sale->total;
            $data[$cont]['coin'] = $sale->coin->code_str;
            $data[$cont]['status'] = $status;

            $data[$cont]['detail'] = [];

            $contDetail = 0;

            if ($sale->condition_payment == 'CREDITO' && $sale->credito) {
                if ($sale->credito->payments->count() == 0) {
                    $data[$cont]['detail'][$contDetail]['doc'] = "{$sale->serialnumber}-{$sale->correlative}";
                    $data[$cont]['detail'][$contDetail]['payment_date'] = date('d-m-Y', strtotime($sale->date));
                    $data[$cont]['detail'][$contDetail]['payment'] = $sale->condition_payment;
                    $data[$cont]['detail'][$contDetail]['debt'] = $sale->total;
                    $data[$cont]['detail'][$contDetail]['paid'] = "0.00";
                    $data[$cont]['detail'][$contDetail]['balance'] = $sale->total;
                } else {
                    foreach ($sale->credito->payments as $payment) {
                        $debt = isset($data[$cont]['detail'][$contDetail - 1]['balance']) ? $data[$cont]['detail'][$contDetail - 1]['balance'] :  $sale->total;
                        $data[$cont]['detail'][$contDetail]['doc'] = $payment->operation_bank;
                        $data[$cont]['detail'][$contDetail]['payment_date'] = date('d-m-Y', strtotime($payment->date));
                        $data[$cont]['detail'][$contDetail]['payment'] = $payment->payment_type;
                        $data[$cont]['detail'][$contDetail]['debt'] = $debt;
                        $data[$cont]['detail'][$contDetail]['paid'] = $payment->payment;
                        $data[$cont]['detail'][$contDetail]['balance'] = (float) $debt - (float) $payment->payment;

                        $contDetail++;
                    }
                }
            } else {
                $data[$cont]['detail'][$contDetail]['doc'] = "{$sale->serialnumber}-{$sale->correlative}";
                $data[$cont]['detail'][$contDetail]['payment_date'] = date('d-m-Y', strtotime($sale->date));
                $data[$cont]['detail'][$contDetail]['payment'] = $sale->condition_payment;
                $data[$cont]['detail'][$contDetail]['debt'] = $sale->total;
                $data[$cont]['detail'][$contDetail]['paid'] = $sale->total;
                $data[$cont]['detail'][$contDetail]['balance'] = "0.00";
            }

            $cont++;
        }

        return collect($data);
    }
}
