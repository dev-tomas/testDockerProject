<?php

namespace App\Http\Controllers;

use DB;
use PDF;
use Auth;
use App\Sale;
use App\User;
use App\Client;
use App\Product;
use App\Customer;
use App\Provider;
use App\Shopping;
use Carbon\Carbon;
use App\HeadQuarter;
use App\PaymentCredit;
use Carbon\CarbonPeriod;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Exports\InvoicesExport;
use App\Exports\ReportSalesExport;
use Caffeinated\Shinobi\Models\Role;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportShoppingExport;


class ReportController extends Controller
{
    public $headquarter;

    public function __construct()
    {
        $this->middleware(['auth', 'status.client']);
        $this->middleware(function ($request, $next) {
            $this->headquarter = session()->has('headlocal') == true ? session()->get('headlocal') : Auth::user()->headquarter_id;
            return $next($request);
        });
    }

    public function report()
    {
        $headquarters = HeadQuarter::where('client_id', auth()->user()->client_id)->get();
        $sellers =  User::whereHas('roles', function ($q) {
            $q->where('slug', 'seller');
        })->where('client_id', auth()->user()->headquarter->client_id)->get();
        $customers = Customer::where('client_id', auth()->user()->headquarter->client_id)->get();
        $products = Product::where('client_id', auth()->user()->headquarter->client_id)->get();

        return view('commercial.reports.index', compact('headquarters', 'sellers', 'customers', 'products'));
    }

    public function getReport(Request $request)
    {
        $desde = Carbon::createFromFormat('d/m/Y', Str::before($request->dates, ' -'))->format('Y-m-d');
        $hasta = Carbon::createFromFormat('d/m/Y', Str::after($request->dates, '- '))->format('Y-m-d');
        $by = $request->dateby;

        $dates = array();
        $total = array();
        $dates2 = array();


        $period = CarbonPeriod::create($desde, '1 days', $hasta);
        foreach ($period as $key => $date) {
            $dates[] = $date->format('d-m');
            $dates2[] = $date->format('Y-m-d');
        }

        if(auth()->user()->hasRole('manager') || auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin') || auth()->user()->hasPermissionTo('ventas.all')) {
            $sales = Sale::selectRaw('SUM(sales.total) as total, sales.issue')
                ->where('sales.client_id', auth()->user()->client_id)
                ->whereNull('low_communication_id')
                ->whereIn('date', $dates2)
                ->where(function ($query) use($request, $desde, $hasta) {
                    if ($request->headquarter != '') {
                        $query->where('sales.headquarter_id', $request->headquarter);
                    }

                    if ($request->customer != '') {
                        $query->where('sales.customer_id', $request->customer);
                    }

                    if ($request->payment != '') {
                        $query->where('sales.condition_payment', $request->payment);
                    }

                    if ($request->status == 4) { // PAGADOS
                        $query->where('status_condition', 1)
                            ->where('paidout', 1);
                    }

                    if ($request->status == 2) {    // PENDIENTE
                        $query->where('expiration', '>=', date('Y-m-d'))
                            ->where('status_condition', 0)
                            ->where('paidout', '!=', 1);
                    }

                    if ($request->status == 3) { // PENDIENTE VENCIDOS
                        $query->whereBetween('expiration',  [$desde, date('Y-m-d')]);
                        $query->where('status_condition', 0)
                            ->where('paidout', '!=', 1);

                    }
                })
                ->groupBy(['issue'])
                ->get();
        } else {
            $sales = Sale::selectRaw('SUM(sales.total) as total, sales.issue')
                ->where('sales.client_id', auth()->user()->client_id)
                ->whereNull('low_communication_id')
                ->whereIn('date', $dates2)
                ->where(function ($query) use($request, $desde) {
                    if ($request->seller != '') {
                        $query->where('sales.user_id', $request->seller);
                    }

                    if ($request->headquarter != '') {
                        $query->where('sales.headquarter_id', $request->headquarter);
                    }

                    if ($request->customer != '') {
                        $query->where('sales.customer_id', $request->customer);
                    }

                    if ($request->status == 4) { // PAGADOS
                        $query->where('status_condition', 1)
                            ->where('paidout', 1);
                    }

                    if ($request->status == 2) {    // PENDIENTE
                        $query->where('expiration', '>=', date('Y-m-d'))
                            ->where('status_condition', 0)
                            ->where('paidout', '!=', 1);
                    }

                    if ($request->status == 3) { // PENDIENTE VENCIDOS
                        $query->whereBetween('expiration',  [$desde, date('Y-m-d')]);
                        $query->where('status_condition', 0)
                            ->where('paidout', '!=', 1);

                    }
                })
                ->groupBy(['issue'])
                ->get();
        }

        for ($i=0; $i < count($dates2); $i++) {
            $total[] = 0;
            foreach ($sales as $t) {
                if ($t->issue == $dates2[$i]) {
                    $total[$i] = $t->total;
                }
            }
        }
        
        return response()->JSON(array('dates' => $dates, 'total' => $total));
    }

    public function getReportPDF(Request $request)
    {
        $desde = Carbon::createFromFormat('d/m/Y', Str::before($request->dates, ' -'))->format('Y-m-d');
        $hasta = Carbon::createFromFormat('d/m/Y', Str::after($request->dates, '- '))->format('Y-m-d');
        $by = $request->dateby;

        $dates = array();
        $total = array();
        $dates2 = array();

        if(auth()->user()->hasRole('manager') || auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin') || auth()->user()->hasPermissionTo('ventas.all')) {
            $sales = Sale::with('customer', 'coin', 'type_voucher', 'credit_note', 'headquarter', 'user', 'credito')
                ->where([
                    ['sales.headquarter_id', $this->headquarter],
                    ['sales.client_id', auth()->user()->client_id]
                ])
                ->whereNull('low_communication_id')
                ->whereBetween('date',  [$desde, $hasta])
                ->where(function ($query) use($request, $desde, $hasta) {
                    if ($request->headquarter != '') {
                        $query->where('sales.headquarter_id', $request->headquarter);
                    }

                    if ($request->payment != '') {
                        $query->where('sales.condition_payment', $request->payment);
                    }

                    if ($request->customer != '') {
                        $query->where('sales.customer_id', $request->customer);
                    }

                    if ($request->status == 4) { // PAGADOS
                        $query->where('status_condition', 1)
                            ->where('paidout', 1);
                    }

                    if ($request->status == 2) {    // PENDIENTE
                        $query->where('expiration', '>=', date('Y-m-d'))
                            ->where('status_condition', 0)
                            ->where('paidout', '!=', 1);
                    }

                    if ($request->status == 3) { // PENDIENTE VENCIDOS
                        $query->whereBetween('expiration',  [$desde, date('Y-m-d')]);
                        $query->where('status_condition', 0)
                            ->where('paidout', '!=', 1);

                    }
                })
                ->get();
        } else {
            $sales = Sale::with('customer', 'coin', 'type_voucher', 'credit_note', 'headquarter', 'user', 'credito')

                ->where('sales.client_id', auth()->user()->client_id)
                ->whereNull('low_communication_id')
                ->whereBetween('date',  [$desde, $hasta])
                ->where(function ($query) use($request, $desde) {
                    if ($request->seller != '') {
                        $query->where('sales.user_id', $request->seller);
                    }

                    if ($request->headquarter != '') {
                        $query->where('sales.headquarter_id', $request->headquarter);
                    }

                    if ($request->customer != '') {
                        $query->where('sales.customer_id', $request->customer);
                    }

                    if ($request->status == 4) { // PAGADOS
                        $query->where('status_condition', 1)
                            ->where('paidout', 1);
                    }

                    if ($request->status == 2) {    // PENDIENTE
                        $query->where('expiration', '>=', date('Y-m-d'))
                            ->where('status_condition', 0)
                            ->where('paidout', '!=', 1);
                    }

                    if ($request->status == 3) { // PENDIENTE VENCIDOS
                        $query->whereBetween('expiration',  [$desde, date('Y-m-d')]);
                        $query->where('status_condition', 0)
                            ->where('paidout', '!=', 1);

                    }
                })
                ->get();
        }

        $saless = array();
        $cont = 0;
        $totals = array();

        foreach ($sales as $sale) {
            $saless[$cont]['date'] = date('d-m-Y', strtotime($sale->issue));
            $saless[$cont]['user'] = $sale->user->name;
            $saless[$cont]['headquarter'] = $sale->headquarter->description;
            $saless[$cont]['customer'] = "{$sale->customer->document}-{$sale->customer->description}";
            $saless[$cont]['product'] = $sale->detail[0]->product->description;
            $saless[$cont]['total'] = $sale->total;
            $saless[$cont]['status_condition'] = $sale->status_condition == 1 ? 'PAGADO' : 'PENDIENTE';
            $saless[$cont]['credit'] = $sale->credito != null ? $sale->credito->debt : '0.00';

            if ($sale->credit_note != null) {
                $saless[$cont]['total'] = (float) $saless[$cont]['total'] - (float) $sale->credit_note->total;
            }

            $cont++;
        }
        
        $clientInfo = Client::find(Auth::user()->headquarter->client_id);

        $pdf = PDF::loadView('commercial.reports.pdf', compact('saless',  'desde', 'hasta', 'clientInfo'))->setPaper('A4');
        return $pdf->stream('REPORTE DE VENTAS ' . $request->dates . '.pdf');
    }
    public function getReportExcel(Request $request)
    {
        $desde = Carbon::createFromFormat('d/m/Y', Str::before($request->dates, ' -'))->format('Y-m-d');
        $hasta = Carbon::createFromFormat('d/m/Y', Str::after($request->dates, '- '))->format('Y-m-d');
        $by = $request->dateby;

        $dates = array();
        $total = array();
        $dates2 = array();

        if(auth()->user()->hasRole('manager') || auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin') || auth()->user()->hasPermissionTo('ventas.all')) {
            $sales = Sale::with('customer', 'coin', 'type_voucher', 'credit_note', 'headquarter', 'user', 'credito')
                ->where([
                    ['sales.headquarter_id', $this->headquarter],
                    ['sales.client_id', auth()->user()->client_id]
                ])
                ->whereNull('low_communication_id')
                ->whereBetween('date',  [$desde, $hasta])
                ->where(function ($query) use($request, $desde, $hasta) {
                    if ($request->headquarter != '') {
                        $query->where('sales.headquarter_id', $request->headquarter);
                    }

                    if ($request->customer != '') {
                        $query->where('sales.customer_id', $request->customer);
                    }

                    if ($request->payment != '') {
                        $query->where('sales.condition_payment', $request->payment);
                    }

                    if ($request->status == 4) { // PAGADOS
                        $query->where('status_condition', 1)
                            ->where('paidout', 1);
                    }

                    if ($request->status == 2) {    // PENDIENTE
                        $query->where('expiration', '>=', date('Y-m-d'))
                            ->where('status_condition', 0)
                            ->where('paidout', '!=', 1);
                    }

                    if ($request->status == 3) { // PENDIENTE VENCIDOS
                        $query->whereBetween('expiration',  [$desde, date('Y-m-d')]);
                        $query->where('status_condition', 0)
                            ->where('paidout', '!=', 1);

                    }
                })
                ->get();
        } else {
            $sales = Sale::with('customer', 'coin', 'type_voucher', 'credit_note', 'headquarter', 'user', 'credito')

                ->where('sales.client_id', auth()->user()->client_id)
                ->whereNull('low_communication_id')
                ->whereBetween('date',  [$desde, $hasta])
                ->where(function ($query) use($request, $desde) {
                    if ($request->seller != '') {
                        $query->where('sales.user_id', $request->seller);
                    }

                    if ($request->headquarter != '') {
                        $query->where('sales.headquarter_id', $request->headquarter);
                    }

                    if ($request->customer != '') {
                        $query->where('sales.customer_id', $request->customer);
                    }

                    if ($request->status == 4) { // PAGADOS
                        $query->where('status_condition', 1)
                            ->where('paidout', 1);
                    }

                    if ($request->status == 2) {    // PENDIENTE
                        $query->where('expiration', '>=', date('Y-m-d'))
                            ->where('status_condition', 0)
                            ->where('paidout', '!=', 1);
                    }

                    if ($request->status == 3) { // PENDIENTE VENCIDOS
                        $query->whereBetween('expiration',  [$desde, date('Y-m-d')]);
                        $query->where('status_condition', 0)
                            ->where('paidout', '!=', 1);

                    }
                })
                ->get();
        }

        $saless = array();
        $cont = 0;
        $totals = array();

        foreach ($sales as $sale) {
            $saless[$cont]['date'] = date('d-m-Y', strtotime($sale->issue));
            $saless[$cont]['user'] = $sale->user->name;
            $saless[$cont]['headquarter'] = $sale->headquarter->description;
            $saless[$cont]['customer'] = "{$sale->customer->document}-{$sale->customer->description}";
            $saless[$cont]['product'] = $sale->detail[0]->product->description;
            $saless[$cont]['total'] = $sale->total;
            $saless[$cont]['status_condition'] = $sale->status_condition == 1 ? 'PAGADO' : 'PENDIENTE';
            $saless[$cont]['credit'] = $sale->credito != null ? $sale->credito->debt : '0.00';

            if ($sale->credit_note != null) {
                $saless[$cont]['total'] = (float) $saless[$cont]['total'] - (float) $sale->credit_note->total;
            }

            $cont++;
        }

        return Excel::download(new ReportSalesExport($saless), 'REPORTE DE VENTAS.xlsx');
    }
    public function getReportTable(Request $request)
    {
        $desde = Carbon::createFromFormat('d/m/Y', Str::before($request->dates, ' -'))->format('Y-m-d');
        $hasta = Carbon::createFromFormat('d/m/Y', Str::after($request->dates, '- '))->format('Y-m-d');
        $by = $request->dateby;
        $dates = array();

        if(auth()->user()->hasRole('manager') || auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin') || auth()->user()->hasPermissionTo('ventas.all')) {
            $sales = Sale::with('customer', 'coin', 'type_voucher', 'credit_note', 'headquarter', 'user', 'credito')
                ->where([
                    ['sales.headquarter_id', $this->headquarter],
                    ['sales.client_id', auth()->user()->client_id]
                ])
                ->whereNull('low_communication_id')
                ->whereBetween('date',  [$desde, $hasta])
                ->where(function ($query) use($request, $desde, $hasta) {
                    if ($request->headquarter != '') {
                        $query->where('sales.headquarter_id', $request->headquarter);
                    }

                    if ($request->customer != '') {
                        $query->where('sales.customer_id', $request->customer);
                    }

                    if ($request->payment != '') {
                        $query->where('sales.condition_payment', $request->payment);
                    }

                    if ($request->status == 4) { // PAGADOS
                        $query->where('status_condition', 1)
                            ->where('paidout', 1);
                    }

                    if ($request->status == 2) {    // PENDIENTE
                        $query->where('expiration', '>=', date('Y-m-d'))
                            ->where('status_condition', 0)
                            ->where('paidout', '!=', 1);
                    }

                    if ($request->status == 3) { // PENDIENTE VENCIDOS
                        $query->whereBetween('expiration',  [$desde, date('Y-m-d')]);
                        $query->where('status_condition', 0)
                            ->where('paidout', '!=', 1);

                    }
                })
                ->get();
        } else {
            $sales = Sale::with('customer', 'coin', 'type_voucher', 'credit_note', 'headquarter', 'user', 'credito')
                ->where('sales.headquarter_id', $this->headquarter)
                ->where('sales.client_id', auth()->user()->client_id)
                ->whereNull('low_communication_id')
                ->whereBetween('date',  [$desde, $hasta])
                ->where(function ($query) use($request, $desde) {
                    if ($request->seller != '') {
                        $query->where('sales.user_id', $request->seller);
                    }

                    if ($request->headquarter != '') {
                        $query->where('sales.headquarter_id', $request->headquarter);
                    }

                    if ($request->customer != '') {
                        $query->where('sales.customer_id', $request->customer);
                    }

                    if ($request->status == 4) { // PAGADOS
                        $query->where('status_condition', 1)
                            ->where('paidout', 1);
                    }

                    if ($request->status == 2) {    // PENDIENTE
                        $query->where('expiration', '>=', date('Y-m-d'))
                            ->where('status_condition', 0)
                            ->where('paidout', '!=', 1);
                    }

                    if ($request->status == 3) { // PENDIENTE VENCIDOS
                        $query->whereBetween('expiration',  [$desde, date('Y-m-d')]);
                        $query->where('status_condition', 0)
                            ->where('paidout', '!=', 1);

                    }
                })
                ->get();
        }

        $saless = array();
        $cont = 0;
        $totals = array();

        foreach ($sales as $sale) {
            $saless[$cont]['date'] = date('d-m-Y', strtotime($sale->issue));
            $saless[$cont]['user'] = $sale->user->name;
            $saless[$cont]['headquarter'] = $sale->headquarter->description;
            $saless[$cont]['customer'] = "{$sale->customer->document}-{$sale->customer->description}";
            $saless[$cont]['product'] = $sale->detail[0]->product->description;
            $saless[$cont]['total'] = $sale->total;
            $saless[$cont]['status_condition'] = $sale->status_condition == 1 ? 'PAGADO' : 'PENDIENTE';
            $saless[$cont]['credit'] = $sale->credito != null ? $sale->credito->debt : '0.00';

            if ($sale->credit_note != null) {
                $saless[$cont]['total'] = (float) $saless[$cont]['total'] - (float) $sale->credit_note->total;
            }

            $cont++;
        }

//        if ($by == "1") {
//            $type = false;
//            $saless = array();
//            $cont = 0;
//            $totals = array();
//
//            $sales = Sale::with('credit_note', 'debit_note')
//                        ->whereBetween('sales.issue', [$desde, $hasta])
//                        ->where('sales.client_id', auth()->user()->headquarter->client_id)
//                        ->whereNull('sales.low_communication_id')
//                        ->where('sales.paidout', 1)
//                        ->where(function ($query) use ($request){
//                            if($request->seller != null){
//                                $query->where('sales.user_id', $request->seller);
//                            }
//                            if($request->customer != null){
//                                $query->where('sales.customer_id', $request->customer);
//                            }
//                            if($request->headquarter != null){
//                                $query->where('sales.headquarter_id', $request->headquarter);
//                            }
//                        })
//                        ->join('headquarters', function ($join) {
//                            $join->on('headquarters.id', '=', 'sales.headquarter_id');
//                        })
//                        ->leftJoin('users', function ($join) {
//                            $join->on('users.id', '=', 'sales.user_id');
//                        })
//                        ->orderBy('sales.issue', 'ASC')
//                        ->get();
//
//            foreach ($sales as $sale) {
//                $saless[$cont]['date'] = $sale->issue;
//                $saless[$cont]['user'] = $sale->name;
//                $saless[$cont]['local'] = $sale->headquarter->description;
//                $saless[$cont]['total'] = $sale->total;
//
//                if ($sale->credit_note != null) {
//                    $saless[$cont]['total'] = (float) $saless[$cont]['total'] - (float) $sale->credit_note->total;
//                }
//                if ($sale->debit_note != null) {
//                    $saless[$cont]['total'] = (float) $saless[$cont]['total'] + (float) $sale->debit_note->total;
//                }
//
//                $cont++;
//            }
//
//            $payments = PaymentCredit::where('client_id', auth()->user()->headquarter->client_id)
//                                         ->whereBetween('date', [$desde, $hasta])
//                                         ->get();
//
//            foreach ($payments as $payment) {
//                $saless[$cont]['date'] = $payment->date;
//                $saless[$cont]['user'] = '';
//                $saless[$cont]['local'] = '';
//                $saless[$cont]['total'] = $payment->payment;
//                $cont++;
//            }
//
//            $cont2 = 0;
//            foreach ($saless as $item)  {
//                if (!isset($totals[$item['date']])) {
//                    $totals[$item['date']] = [];
//                    $dates[] = $item['date'];
//                }
//                foreach ($item as $key => $value) {
//                    if ($key == 'total') {
//                        if (!isset( $totals[$item['date']][$key] )) {
//                            $totals[$item['date']][$key] = 0;
//                        }
//                        $totals[$item['date']][$key] = (float) $value + (float) $totals[$item['date']][$key];
//                    } else {
//                        $totals[$item['date']][$key] = $value;
//                    }
//                }
//
//                $cont2++;
//            }
//
//        } else if ($by == "2") {
//            $period = CarbonPeriod::create($desde, '1 month', $hasta);
//            $meses = array();
//
//            setlocale(LC_TIME, 'es_ES');
//
//            foreach ($period as $key => $date) {
//                $fecha = \DateTime::createFromFormat('!m', $date->format('m'));
//                $meses[] = $date->format('m-Y');
//                $mes = strftime("%B", $fecha->getTimestamp());
//                $dates[] = $date->format('M');
//            }
//
//            $type = true;
//            $saless = array();
//            $cont = 0;
//            $totals = array();
//
//            $sales = Sale::with('credit_note', 'debit_note')
//                            ->whereIn(DB::raw('MONTH(sales.issue)'), $meses)
//                            ->whereNull('low_communication_id')
//                            ->where('sales.paidout', 1)
//                            ->where('sales.client_id', auth()->user()->headquarter->client_id)
//                            ->where(function ($query) use ($request){
//                                if($request->seller != null){
//                                    $query->where('sales.user_id', $request->seller);
//                                }
//                                if($request->customer != null){
//                                    $query->where('sales.customer_id', $request->customer);
//                                }
//                                if($request->headquarter != null){
//                                    $query->where('sales.headquarter_id', $request->headquarter);
//                                }
//                            })
//                            ->join('headquarters', function ($join) {
//                                $join->on('headquarters.id', '=', 'sales.headquarter_id');
//                            })
//                            ->where(function ($query) use ($request){
//                                if($request->seller != null){
//                                    $query->where('sales.headquarter_id', $request->headquarter);
//                                }
//                            })
//                            ->leftJoin('users', function ($join) {
//                                $join->on('users.id', '=', 'sales.user_id');
//                            })
//                            ->where(function ($query) use ($request){
//                                if($request->seller != null){
//                                    $query->where('sales.user_id', $request->seller);
//                                }
//                            })
//                            ->orderBy('issue', 'ASC')
//                            ->get();
//
//            foreach ($sales as $sale) {
//                $saless[$cont]['date'] = date('M', strtotime($sale->issue));
//                $saless[$cont]['user'] = $sale->name;
//                $saless[$cont]['local'] = $sale->headquarter->description;
//                $saless[$cont]['total'] = $sale->total;
//
//                if ($sale->credit_note != null) {
//                    $saless[$cont]['total'] = (float) $saless[$cont]['total'] - (float) $sale->credit_note->total;
//                }
//                if ($sale->debit_note != null) {
//                    $saless[$cont]['total'] = (float) $saless[$cont]['total'] + (float) $sale->debit_note->total;
//                }
//
//                $cont++;
//            }
//
//            $payments = PaymentCredit::where('client_id', auth()->user()->headquarter->client_id)
//                                        ->whereIn(DB::raw('MONTH(date)'), $meses)
//                                         ->get();
//
//            foreach ($payments as $payment) {
//                $saless[$cont]['date'] = $payment->date;
//                $saless[$cont]['user'] = '';
//                $saless[$cont]['local'] = '';
//                $saless[$cont]['total'] = $payment->payment;
//                $cont++;
//            }
//
//            $cont2 = 0;
//            foreach ($saless as $item)  {
//                if (!isset($totals[$item['date']])) {
//                    $totals[$item['date']] = [];
//                }
//                foreach ($item as $key => $value) {
//                    if ($key == 'total') {
//                        if (!isset( $totals[$item['date']][$key] )) {
//                            $totals[$item['date']][$key] = 0;
//                        }
//                        $totals[$item['date']][$key] = (float) $value + (float) $totals[$item['date']][$key];
//                    } else {
//                        $totals[$item['date']][$key] = $value;
//                    }
//                }
//
//                $cont2++;
//            }
//
//        } else if($by == '3') {
//            $period = CarbonPeriod::create($desde, '1 year', $hasta);
//            foreach ($period as $key => $date) {
//                $dates[] = $date->year;
//            }
//
//            $saless = array();
//            $cont = 0;
//            $totals = array();
//
//            $sales = Sale::with('credit_note', 'debit_note')
//                        ->whereIn(DB::raw('YEAR(sales.issue)'), $dates)
//                        ->whereNull('low_communication_id')
//                        ->where('sales.paidout', 1)
//                        ->where('sales.client_id', auth()->user()->headquarter->client_id)
//                        ->where(function ($query) use ($request){
//                            if($request->seller != null){
//                                $query->where('sales.user_id', $request->seller);
//                            }
//                            if($request->customer != null){
//                                $query->where('sales.customer_id', $request->customer);
//                            }
//                            if($request->headquarter != null){
//                                $query->where('sales.headquarter_id', $request->headquarter);
//                            }
//                        })
//                        ->join('headquarters', function ($join) {
//                            $join->on('headquarters.id', '=', 'sales.headquarter_id');
//                        })
//                        ->where(function ($query) use ($request){
//                            if($request->seller != null){
//                                $query->where('sales.headquarter_id', $request->headquarter);
//                            }
//                        })
//                        ->leftJoin('users', function ($join) {
//                            $join->on('users.id', '=', 'sales.user_id');
//                        })
//                        ->where(function ($query) use ($request){
//                            if($request->seller != null){
//                                $query->where('sales.user_id', $request->seller);
//                            }
//                        })
//                        ->orderBy('issue', 'ASC')
//                        ->get();
//
//            foreach ($sales as $sale) {
//                $saless[$cont]['date'] = date('Y', strtotime($sale->issue));
//                $saless[$cont]['user'] = $sale->name;
//                $saless[$cont]['local'] = $sale->headquarter->description;
//                $saless[$cont]['total'] = $sale->total;
//
//                if ($sale->credit_note != null) {
//                    $saless[$cont]['total'] = (float) $saless[$cont]['total'] - (float) $sale->credit_note->total;
//                }
//                if ($sale->debit_note != null) {
//                    $saless[$cont]['total'] = (float) $saless[$cont]['total'] + (float) $sale->debit_note->total;
//                }
//
//                $cont++;
//            }
//
//            $payments = PaymentCredit::where('client_id', auth()->user()->headquarter->client_id)
//                                        ->whereIn(DB::raw('YEAR(date)'), $dates)
//                                         ->get();
//
//            foreach ($payments as $payment) {
//                $saless[$cont]['date'] = $payment->date;
//                $saless[$cont]['user'] = '';
//                $saless[$cont]['local'] = '';
//                $saless[$cont]['total'] = $payment->payment;
//                $cont++;
//            }
//
//            $cont2 = 0;
//            foreach ($saless as $item)  {
//                if (!isset($totals[$item['date']])) {
//                    $totals[$item['date']] = [];
//                }
//                foreach ($item as $key => $value) {
//                    if ($key == 'total') {
//                        if (!isset( $totals[$item['date']][$key] )) {
//                            $totals[$item['date']][$key] = 0;
//                        }
//                        $totals[$item['date']][$key] = (float) $value + (float) $totals[$item['date']][$key];
//                    } else {
//                        $totals[$item['date']][$key] = $value;
//                    }
//                }
//
//                $cont2++;
//            }
//        }
        
        return response()->json(array('totals' => $saless, 'dates' => $dates));
    }

    public function getReportIndex()
    {
        $now = new \DateTime();
        $today = $now->format('Y-m-d');
        $month = $now->format('m');

        $ahora = Carbon::now();
        $desde = $ahora->firstOfMonth()->format('Y-m-d');
        $hasta = $ahora->lastOfMonth()->format('Y-m-d'); 
        
        $totalToday = array();
        $totalMonth = array();

        $paidLastMonth = 0;
        $creditNoteTotal = 0;
        $paidDay = 0;
        $count = 0;
        $salesMonth = Sale::with('credit_note', 'debit_note')->whereBetween('issue', [$desde, $hasta])
                        ->whereNull('low_communication_id')
                        ->where(function ($query) {
                            if (
                                auth()->user()->hasRole('admin') ||
                                auth()->user()->hasRole('superadmin') ||
                                auth()->user()->hasRole('manager')
                            ) {} else {
                                $query->where('user_id', Auth::id());
                            }

                            $query->where('paidout', 1);
                            $query->where('client_id', auth()->user()->headquarter->client_id);
                        })->get();

        $paymentsMonthly = PaymentCredit::where('client_id', auth()->user()->headquarter->client_id)
                                    ->whereBetween('date', [$desde, $hasta])
                                    ->sum('payment');

        foreach ($salesMonth as $sale) {
            $paidLastMonth += $sale->total;
            $totalMonth[] = (int) $sale->total;

            if ($sale->credit_note_id != null) {
                $paidLastMonth -= $sale->credit_note->total;
            }

            if ($sale->debit_note_id != null) {
                $paidLastMonth += $sale->debit_note->total;
            }

            $count++;
        }

        $salesDay = Sale::with('credit_note', 'debit_note')->where('issue', $today)
                        ->whereNull('low_communication_id')
                        ->where(function ($query) {
                            if (
                                auth()->user()->hasRole('admin') ||
                                auth()->user()->hasRole('superadmin') ||
                                auth()->user()->hasRole('manager')
                            ) {} else {
                                $query->where('user_id', Auth::id());
                            }

                            $query->where('paidout', 1);
                            $query->where('client_id', auth()->user()->headquarter->client_id);
                        })->get();

        $paymentsDayly = PaymentCredit::where('client_id', auth()->user()->headquarter->client_id)
                        ->where('date', $today);

        foreach ($salesDay as $sale) {
            $paidDay += $sale->total;
            $totalToday[] = (int) $sale->total;

            if ($sale->credit_note_id != null) {
                $paidDay -= $sale->credit_note->total;
            }

            if ($sale->debit_note_id != null) {
                $paidDay += $sale->debit_note->total;
            }

            $count++;
        }

        return response()->JSON(array('day' => $paidDay + $paymentsDayly->sum('payment'), 'mes' => $paidLastMonth + $paymentsMonthly, 'month' => $totalMonth, 'today' => $totalToday));
    }
    
    public function reportSales()
    {
        return view('reports.contasys');
    }
    
    public function downloadReport($since, $until, $type_voucher)
    {
        return Excel::download(new InvoicesExport($since, $until, $type_voucher), 'Reporte de ventas.xlsx');
    }


    public function reportPurchase()
    {
        $headquarters = HeadQuarter::where('client_id', auth()->user()->client_id)->get();
        $providers = Provider::where('client_id',  auth()->user()->client_id)->get();
        $products = Product::where('client_id', auth()->user()->headquarter->client_id)->get();
        $customers = Customer::where('client_id', auth()->user()->headquarter->client_id)->get();

        return view('logistic.reports.index', compact('headquarters', 'providers', 'customers', 'products'));
    }

    public function getLogisticReport(Request $request)
    {
        $desde = Carbon::createFromFormat('d/m/Y', Str::before($request->dates, ' -'))->format('Y-m-d');
        $hasta = Carbon::createFromFormat('d/m/Y', Str::after($request->dates, '- '))->format('Y-m-d');
        
        $shoppings = Shopping::whereHas('detail', function ($query) use ($request) {
                                    if($request->product != null){
                                        return $query->where('product_id', $request->product);
                                    }
                                })
                                ->where('type', 1)
                                ->where('status', '!=', 9)
                                ->where(function ($query) use ($request){
                                    if($request->headquarter != null){
                                        $query->where('headquarter_id', $request->headquarter);
                                    }
                                })
                                ->where(function ($query) use ($request){
                                    if($request->provider != null){
                                        $query->where('provider_id', $request->provider);
                                    }

                                    if ($request->condition != '') {
                                        $query->where('payment_type', $request->condition);
                                    }

                                    if ($request->filter_status == 3) {
                                        $query->where('paidout', 1);
                                    }

                                    if ($request->filter_status == 2) {
                                        $query->where('paidout', 0);
                                    }
                                })
                                ->where('headquarter_id', $this->headquarter)
                                ->whereBetween('shoppings.date', [$desde, $hasta])
                                ->where('client_id', auth()->user()->client_id)
                                ->get();

        $data = [];
        $cont = 0;

        $data['total'] = $shoppings->sum('total');

        $totalCredit = 0;
        $totalQuantity = 0;
        $glosa = [];

        foreach ($shoppings as $shopping) {
            $totalCredit = (float)($shopping->credit != null ? $shopping->credit->debt : "0") + (float)$totalCredit;
            $totalQuantity = (float)$shopping->detail[0]->quantity + (float)$totalQuantity;

            $data['detail'][$cont]['date'] = date('d-m-Y', strtotime($shopping->date));
            $data['detail'][$cont]['document'] = "{$shopping->typeVoucher->description} - {$shopping->shopping_serie}-{$shopping->shopping_correlative}";
            $data['detail'][$cont]['headquarter'] = $shopping->headquarter->description;
            $data['detail'][$cont]['provider'] = $shopping->provider->description;
            $data['detail'][$cont]['product'] = $shopping->detail[0]->product->description;
            $data['detail'][$cont]['total'] = $shopping->total;
            $data['detail'][$cont]['credit'] = $shopping->credit != null ? $shopping->credit->debt : "0";

            $detail = Str::upper($shopping->detail[0]->product->description);

            if ($shopping->detail[0]->type_purchase == '0') {
                $glosa[] = "ACTIVO FIJO DE {$detail}";
            } else if ($shopping->detail[0]->type_purchase == '1') {
                $glosa[] = "COMPRA DE {$detail}";
            } else if ($shopping->detail[0]->type_purchase == '2') {
                $glosa[] = "GASTO DE {$detail}";
            }

            $cont++;
        }

        $data['totalCredit'] = $totalCredit;
        $data['totalQuantity'] = $totalQuantity;
        $data['glosas'] = $glosa;

        return response()->json($data);
    }

    public function generateExcelReportExcel(Request $request)
    {
        $desde = Carbon::createFromFormat('d/m/Y', Str::before($request->dates, ' -'))->format('Y-m-d');
        $hasta = Carbon::createFromFormat('d/m/Y', Str::after($request->dates, '- '))->format('Y-m-d');

        $date = $request->dates;
        $headquarter = $request->headquarter;
        $product = $request->product;

        $provider = '';
        if ($request->provider != '') {
            $provider = Provider::find($request->provider);
        }
        $product = '';
        if ($request->product != '') {
            $product = Product::find($request->$product);
        }
        
        $shoppings = Shopping::where('type', 1)
                                ->whereHas('detail', function ($query) use ($request) {
                                    if($request->product != null){
                                        return $query->where('product_id', $request->product);
                                    }
                                })
                                ->where(function ($query) use ($request){
                                    if($request->headquarter != null){
                                        $query->where('headquarter_id', $request->headquarter);
                                    }
                                })
                                ->where(function ($query) use ($request){
                                    if($request->provider != null){
                                        $query->where('provider_id', $request->provider);
                                    }

                                    if ($request->filter_status == 3) {
                                        $query->where('paidout', 1);
                                    }

                                    if ($request->filter_status == 2) {
                                        $query->where('paidout', 0);
                                    }
                                })
                                ->whereBetween('shoppings.date', [$desde, $hasta])
                                ->where('client_id', auth()->user()->client_id)
                                ->get();

        $clientInfo = Client::find(Auth::user()->headquarter->client_id);

        return Excel::download(new ReportShoppingExport($date, $shoppings,$headquarter, $product, $desde, $hasta, $provider, $clientInfo), 'REPORTE DE COMPRA.xlsx');
    }
    public function generatePDFReportPDF(Request $request)
    {
        $desde = Carbon::createFromFormat('d/m/Y', Str::before($request->dates, ' -'))->format('Y-m-d');
        $hasta = Carbon::createFromFormat('d/m/Y', Str::after($request->dates, '- '))->format('Y-m-d');

        $date = $request->dates;
        $headquarter = $request->headquarter;
        $product = $request->product;

        $provider = '';
        if ($request->provider != '') {
            $provider = Provider::find($request->provider);
        }
        $product = '';
        if ($request->product != '') {
            $product = Product::find($request->$product);
        }
        
        $shoppings = Shopping::where('type', 1)
                                ->whereHas('detail', function ($query) use ($request) {
                                    if($request->product != null){
                                        return $query->where('product_id', $request->product);
                                    }
                                })
                                ->where(function ($query) use ($request){
                                    if($request->headquarter != null){
                                        $query->where('headquarter_id', $request->headquarter);
                                    }
                                })
                                ->where(function ($query) use ($request){
                                    if($request->provider != null){
                                        $query->where('provider_id', $request->provider);
                                    }

                                    if ($request->filter_status == 3) {
                                        $query->where('paidout', 1);
                                    }

                                    if ($request->filter_status == 2) {
                                        $query->where('paidout', 0);
                                    }
                                })
                                ->whereBetween('shoppings.date', [$desde, $hasta])
                                ->where('client_id', auth()->user()->client_id)
                                ->get();

        $clientInfo = Client::find(Auth::user()->headquarter->client_id);

        $pdf = PDF::loadView('logistic.reports.pdf', compact('date','headquarter','product','desde','hasta','provider','clientInfo','shoppings'))->setPaper('A4');
        return $pdf->download('REPORTE DE COMPRA ' . $request->dates . '.pdf');
    }
}
