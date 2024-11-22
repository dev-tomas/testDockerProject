<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use PDF;
use Auth;
use App\Client;
use App\Customer;
use App\Exports\InvoicesExport;
use App\Exports\ReportSalesExport;
use App\Exports\ReportShoppingExport;
use App\HeadQuarter;
use App\PaymentCredit;
use App\Product;
use App\Provider;
use App\Sale;
use App\Shopping;
use App\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

use App\Charts\MyChartName;

class AnalyticsController extends Controller
{
    public $headquarter;

    public function __contruct()
    {
        $this->middleware('auth');
        $this->middleware('status.client');

        $this->middleware(function ($request, $next) {
            $this->headquarter = session()->has('headlocal') == true ? session()->get('headlocal') : Auth::user()->headquarter_id;
            return $next($request);
        });
    }

    public function report()
    {
        $headquarters = HeadQuarter::where('client_id', auth()->user()->client_id)->get();
        $sellers = User::whereHas('roles', function ($q) {
            $q->where('slug', 'seller');
        })->where('client_id', auth()->user()->headquarter->client_id)->get();
        $customers = Customer::where('client_id', auth()->user()->headquarter->client_id)->get();
        $products = Product::where('client_id', auth()->user()->headquarter->client_id)->get();

        return view('analytics.index', compact('headquarters', 'sellers', 'customers', 'products'));
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

        $newIngresoB=Sale::where('sales.headquarter_id', $this->headquarter)
            ->where('sales.client_id', auth()->user()->client_id)
            ->where('sales.user_id', auth()->user()->id)
            ->whereBetween('date', [$request->get('dateOne'), $request->get('dateTwo')])
            ->where(function ($query) use ($request) {
                if ($request->get('dateOne') != '') {
                    $query->whereBetween('date', [$request->get('dateOne'), $request->get('dateTwo')]);
                }
            })
            ->get();

        if (auth()->user()->hasRole('manager') || auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin') || auth()->user()->hasPermissionTo('ventas.all')) {
            $sales = Sale::selectRaw('SUM(sales.subtotal) as total, sales.issue')
                ->where('sales.client_id', auth()->user()->client_id)
                ->whereNull('low_communication_id')
                ->whereIn('date', $dates2)
                ->where(function ($query) use ($request, $desde, $hasta) {
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
                        $query->whereBetween('expiration', [$desde, date('Y-m-d')]);
                        $query->where('status_condition', 0)
                            ->where('paidout', '!=', 1);

                    }
                })
                ->groupBy(['issue'])
                ->get();
        } else {
            $sales = Sale::selectRaw('SUM(sales.subtotal) as total, sales.issue')
                ->where('sales.client_id', auth()->user()->client_id)
                ->whereNull('low_communication_id')
                ->whereIn('date', $dates2)
                ->where(function ($query) use ($request, $desde) {
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
                        $query->whereBetween('expiration', [$desde, date('Y-m-d')]);
                        $query->where('status_condition', 0)
                            ->where('paidout', '!=', 1);

                    }
                })
                ->groupBy(['issue'])
                ->get();
        }

        for ($i = 0; $i < count($dates2); $i++) {
            $total[] = 0;
            foreach ($sales as $t) {
                if ($t->issue == $dates2[$i]) {
                    $total[$i] = $t->total;
                }
            }
        }
        //--------------------
        $ingresoB = DB::table('sales')
        ->where('client_id', auth()->user()->headquarter->client_id)
        ->where(function ($query) use ($request) {
            if ($request->headquarter != '') {
                $query->where('headquarter_id', $request->headquarter);
            }
        })
        
        ->whereBetween('date', [$desde, $hasta])
            ->where('status_condition', '1')
            ->sum('subtotal');

        $credit = DB::table('credit_notes')
            ->whereBetween('date_issue', [$desde, $hasta])
            ->where('client_id', auth()->user()->headquarter->client_id)
            ->where(function ($query) use ($request) {
                if ($request->headquarter != '') {
                    $query->where('headquarter_id', $request->headquarter);
                }
            })
            ->sum('taxed');

        $debit = DB::table('debit_notes')
        ->whereBetween('date_issue', [$desde, $hasta])
        ->where('client_id', auth()->user()->headquarter->client_id)
        ->where(function ($query) use ($request) {
            if ($request->headquarter != '') {
                $query->where('headquarter_id', $request->headquarter);
            }
        })
        ->sum('taxed');

        $descDevo = $credit - $debit;
        $ingresoN = ($ingresoB - abs($descDevo));

        $costSale = DB::table('kardex')
        ->join('warehouses', 'kardex.warehouse_id', '=', 'warehouses.id')
        ->where('kardex.client_id', auth()->user()->headquarter->client_id)
        ->where('kardex.type_transaction', 'Venta')
        ->whereBetween('kardex.created_at', [$desde, $hasta])
        ->where(function ($query) use ($request) {
            if ($request->headquarter != '') {
                $query->where('warehouses.headquarter_id', $request->headquarter);
            }
        })
        ->select(DB::raw('((kardex.output * (-1)) * kardex.cost) as total'))
            ->get()->sum('total');
        
        $filtroC = DB::table('kardex')
        ->join('warehouses', 'kardex.warehouse_id', '=', 'warehouses.id')
        ->where('kardex.client_id', auth()->user()->headquarter->client_id)
        ->where(function ($query) {
            $query->where('kardex.type_transaction', 'Nota de Crédito')
                ->orWhere('kardex.type_transaction', 'Comunicación de Baja');
        })
        ->where(function ($query) use ($request) {
            if ($request->headquarter != '') {
                $query->where('warehouses.headquarter_id', $request->headquarter);
            }
        })
        ->whereBetween('kardex.created_at', [$desde, $hasta])
        ->select(DB::raw('(kardex.entry * kardex.cost) as result'))
            ->get()->sum('result');

        $costSale = $costSale - $filtroC;

        $gastos = DB::table('shopping_details')
        ->where('client_id', auth()->user()->headquarter->client_id)
            ->join('shoppings', 'shopping_details.shopping_id', '=', 'shoppings.id')
            ->select('shoppings.status', 'shopping_details.subtotal', 'shopping_details.type_purchase')
            ->where('shoppings.status', '0')
            ->where('shopping_details.type_purchase', '2')
            ->whereBetween('shoppings.date', [$desde, $hasta])
            ->get()
            ->sum(function ($item) {
                return (float)$item->subtotal;
            });

        $utilidad = $ingresoN - $costSale; // utilidad bruta
        $utilidadO = $utilidad - $gastos; // utilidad operativa

        $sales = [
            // 'day' => $paidDay + $paymentsDayly->sum('payment'),
            // 'mes' => $paidLastMonth + $paymentsMonthly,
            // 'month' => $totalMonth,
            // 'today' => $totalToday,
            'dates' => $dates,
            'total' => $total,
            'sales' => $ingresoB,
            'discount' => $descDevo,
            'incomeneto' => $ingresoN,
            'salescost' => $costSale,
            'utilitybrute' => $utilidad,
            'expenses' => $gastos,
            'utilityoperative' => $utilidadO,
        ];

        return response()->json($sales);

     }

    public function getReportPDF(Request $request)
    {
        $desde = Carbon::createFromFormat('d/m/Y', Str::before($request->dates, ' -'))->format('Y-m-d');
        $hasta = Carbon::createFromFormat('d/m/Y', Str::after($request->dates, '- '))->format('Y-m-d');
        $by = $request->dateby;

        $dates = array();
        $total = array();
        $dates2 = array();

        if (auth()->user()->hasRole('manager') || auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin') || auth()->user()->hasPermissionTo('ventas.all')) {
            $sales = Sale::with('customer', 'coin', 'type_voucher', 'credit_note', 'headquarter', 'user', 'credito')
                ->where([
                    ['sales.headquarter_id', $this->headquarter],
                    ['sales.client_id', auth()->user()->client_id]
                ])
                ->whereNull('low_communication_id')
                ->whereBetween('date', [$desde, $hasta])
                ->where(function ($query) use ($request, $desde, $hasta) {
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
                        $query->whereBetween('expiration', [$desde, date('Y-m-d')]);
                        $query->where('status_condition', 0)
                            ->where('paidout', '!=', 1);

                    }
                })
                ->get();
        } else {
            $sales = Sale::with('customer', 'coin', 'type_voucher', 'credit_note', 'headquarter', 'user', 'credito')
                ->where('sales.client_id', auth()->user()->client_id)
                ->whereNull('low_communication_id')
                ->whereBetween('date', [$desde, $hasta])
                ->where(function ($query) use ($request, $desde) {
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
                        $query->whereBetween('expiration', [$desde, date('Y-m-d')]);
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
                $saless[$cont]['total'] = (float)$saless[$cont]['total'] - (float)$sale->credit_note->total;
            }

            $cont++;
        }

        $clientInfo = Client::find(Auth::user()->headquarter->client_id);

        $pdf = PDF::loadView('analytics.index', compact('saless', 'desde', 'hasta', 'clientInfo'))->setPaper('A4');
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

        if (auth()->user()->hasRole('manager') || auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin') || auth()->user()->hasPermissionTo('ventas.all')) {
            $sales = Sale::with('customer', 'coin', 'type_voucher', 'credit_note', 'headquarter', 'user', 'credito')
                ->where([
                    ['sales.headquarter_id', $this->headquarter],
                    ['sales.client_id', auth()->user()->client_id]
                ])
                ->whereNull('low_communication_id')
                ->whereBetween('date', [$desde, $hasta])
                ->where(function ($query) use ($request, $desde, $hasta) {
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
                        $query->whereBetween('expiration', [$desde, date('Y-m-d')]);
                        $query->where('status_condition', 0)
                            ->where('paidout', '!=', 1);

                    }
                })
                ->get();
        } else {
            $sales = Sale::with('customer', 'coin', 'type_voucher', 'credit_note', 'headquarter', 'user', 'credito')
                ->where('sales.client_id', auth()->user()->client_id)
                ->whereNull('low_communication_id')
                ->whereBetween('date', [$desde, $hasta])
                ->where(function ($query) use ($request, $desde) {
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
                        $query->whereBetween('expiration', [$desde, date('Y-m-d')]);
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
                $saless[$cont]['total'] = (float)$saless[$cont]['total'] - (float)$sale->credit_note->total;
            }

            $cont++;
        }

        return Excel::download(new ReportSalesExport($saless), 'REPORTE DE VENTAS.xlsx');
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
        $totalSalesBrute = array();

        $paidLastMonth = 0;
        $creditNoteTotal = 0;
        $paidDay = 0;
        $count = 0;



        $ingresoB = DB::table('sales')
        ->where('client_id', auth()->user()->headquarter->client_id)
        ->where('headquarter_id', auth()->user()->headquarter_id)   
        ->whereBetween('date', [$desde, $hasta])
            ->where('status_condition', '1')
            ->sum('subtotal');

        $credit = DB::table('credit_notes')
            ->whereBetween('date_issue', [$desde, $hasta])
            ->where('client_id', auth()->user()->headquarter->client_id)
            ->where('headquarter_id', auth()->user()->headquarter_id)  
            ->sum('taxed');

        $debit = DB::table('debit_notes')
        ->whereBetween('date_issue', [$desde, $hasta])
        ->where('client_id', auth()->user()->headquarter->client_id)
        ->where('headquarter_id', auth()->user()->headquarter_id) 
        ->sum('taxed');

        $descDevo = $credit - $debit;
        $ingresoN = ($ingresoB - abs($descDevo));

        $costSale = DB::table('kardex')
        ->join('warehouses', 'kardex.warehouse_id', '=', 'warehouses.id')
        ->where('kardex.client_id', auth()->user()->headquarter->client_id)
        ->where('kardex.type_transaction', 'Venta')
        ->whereBetween('kardex.created_at', [$desde, $hasta])
        ->where('warehouses.headquarter_id', auth()->user()->headquarter_id)
        ->select(DB::raw('((kardex.output * (-1)) * kardex.cost) as total'))
            ->get()->sum('total');
      

        $filtroC = DB::table('kardex')
        ->join('warehouses', 'kardex.warehouse_id', '=', 'warehouses.id')
        ->where('kardex.client_id', auth()->user()->headquarter->client_id)
        ->where(function ($query) {
            $query->where('kardex.type_transaction', 'Nota de Crédito')
                ->orWhere('kardex.type_transaction', 'Comunicación de Baja');
        })
        ->where('warehouses.headquarter_id', auth()->user()->headquarter_id)
        ->whereBetween('kardex.created_at', [$desde, $hasta])
        ->select(DB::raw('(kardex.entry * kardex.cost) as result'))
            ->get()->sum('result');
        $costSale = $costSale - $filtroC;

        $gastos = DB::table('shopping_details')
        ->where('client_id', auth()->user()->headquarter->client_id)
            ->join('shoppings', 'shopping_details.shopping_id', '=', 'shoppings.id')
            ->select('shoppings.status', 'shopping_details.subtotal', 'shopping_details.type_purchase')
            ->where('shoppings.status', '0')
            ->where('shopping_details.type_purchase', '2')
            ->whereBetween('shoppings.date', [$desde, $hasta])
            ->get()
            ->sum(function ($item) {
                return (float)$item->subtotal;
            });

        $utilidad = $ingresoN - $costSale; // utilidad bruta
        $utilidadO = $utilidad - $gastos; // utilidad operativa

        $sales = [
            // 'day' => $paidDay + $paymentsDayly->sum('payment'),
            // 'mes' => $paidLastMonth + $paymentsMonthly,
            // 'month' => $totalMonth,
            // 'today' => $totalToday,
            'sales' => $ingresoB,
            'discount' => $descDevo,
            'incomeneto' => $ingresoN,
            'salescost' => $costSale,
            'utilitybrute' => $utilidad,
            'expenses' => $gastos,
            'utilityoperative' => $utilidadO,
        ];

        return response()->json($sales);
    }

    public function reportSales()
    {
        return view('reports.contasys');
    }

    public function downloadReport($since, $until, $type_voucher)
    {
        return Excel::download(new InvoicesExport($since, $until, $type_voucher), 'Reporte de ventas.xlsx');
    }


    /*=========================a partir de aca es lo que hizo fabrizio========================*/
    public function filtro(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        $localId = $request->input('locales');

        $ventasQuery = Sale::query();

        if ($localId != -1) {
            // Si el valor no es -1, aplicar el filtro por local
            $ventasQuery->where('headquarter_id', $localId);
        } else {
            // Si el valor es -1, obtener todos los locales del cliente actual
            $cliente_id = auth()->user()->client_id;
            $localesCliente = HeadQuarter::where('client_id', $cliente_id)->pluck('id');
            $ventasQuery->whereIn('headquarter_id', $localesCliente);
        }
    
        $ventasQuery->whereBetween('date', [$fechaInicio, $fechaFin]);

        // Verificar si hay fechas válidas
        if ($fechaInicio && $fechaFin) {    // Ingreso Brutro
            $ingresoB = $ventasQuery->where('sales.status_condition', 1)
            ->sum('subtotal');
            
            $credit = DB::table('credit_notes')
            ->whereRaw("DATE(created_at) BETWEEN ? AND ?", [$fechaInicio, $fechaFin])
            ->where('client_id', auth()->user()->client_id)
            ->when($localId != -1, function ($query) use ($localId) {
                return $query->where('headquarter_id', $localId);
            })
            ->sum('taxed');

            $debit = DB::table('debit_notes')
            ->whereBetween('date_issue', [$fechaInicio, $fechaFin])
            ->where('client_id', auth()->user()->client_id)
            ->when($localId != -1, function ($query) use ($localId) {
                return $query->where('headquarter_id', $localId);
            })
            ->sum('taxed');

            $descDevo = $credit - $debit;
            $ingresoN = ($ingresoB - abs($descDevo));

            $costSale = DB::table('kardex')
            ->join('warehouses', 'kardex.warehouse_id', '=', 'warehouses.id')
            ->where('kardex.client_id', auth()->user()->client_id)
            ->where('kardex.type_transaction', 'Venta')
            ->whereBetween('kardex.created_at', [$fechaInicio, $fechaFin])
            ->when($localId != -1, function ($query) use ($localId) {
                return $query->where('warehouses.headquarter_id', $localId);
            })
            ->select(DB::raw('((kardex.output * (-1)) * kardex.cost) as total'))
            ->get()
            ->sum('total');

            $filtroC = DB::table('kardex')
            ->join('warehouses', 'kardex.warehouse_id', '=', 'warehouses.id')
            ->where('kardex.client_id', auth()->user()->client_id)
            ->where(function ($query) {
                $query->where('kardex.type_transaction', 'Nota de Crédito')
                    ->orWhere('kardex.type_transaction', 'Comunicación de Baja');
            })
            ->whereBetween('kardex.created_at', [$fechaInicio, $fechaFin])
            ->when($localId != -1, function ($query) use ($localId) {
                return $query->where('warehouses.headquarter_id', $localId);
            })
            ->select(DB::raw('(kardex.entry * kardex.cost) as result'))
            ->get()
            ->sum('result');

            $costSale = $costSale - $filtroC;

            $gastos = DB::table('shopping_details')
            ->join('shoppings', 'shopping_details.shopping_id', '=', 'shoppings.id')
            ->select('shoppings.status', 'shopping_details.subtotal', 'shopping_details.type_purchase')
            ->where('shoppings.status', '0')
            ->where('shopping_details.type_purchase', '2')
            ->whereBetween('shoppings.date', [$fechaInicio, $fechaFin])
            ->where('shoppings.client_id', auth()->user()->client_id) 
            ->when($localId != -1, function ($query) use ($localId) {
                return $query->where('shoppings.headquarter_id', $localId);
            })
            ->get()
            ->sum(function ($item) {
                return (float) $item->subtotal;
            });

            $utilidad = $ingresoN - $costSale; // utilidad bruta
            $utilidadO = $utilidad - $gastos; // utilidad operativa

            //=======================================//Consulta para el desglose de gastos//======================================
            $gastosCO = DB::table('shopping_details')
            ->join('shoppings', 'shopping_details.shopping_id', '=', 'shoppings.id')
            ->select('shoppings.status', 'shopping_details.subtotal', 'shopping_details.type_purchase')
            ->where('shoppings.status', '0')
            ->where('shoppings.type','1') 
            ->where('shopping_details.type_purchase', '2')
            ->whereBetween('shoppings.date', [$fechaInicio, $fechaFin])
            ->where('shoppings.client_id', auth()->user()->client_id)
            
            ->when($localId != -1, function ($query) use ($localId) {
                return $query->where('shoppings.headquarter_id', $localId);
            })
            ->get()
            ->sum(function ($item) {
                return (float) $item->subtotal;
            });

            $gastosRH = DB::table('shopping_details')
            ->join('shoppings', 'shopping_details.shopping_id', '=', 'shoppings.id')
            ->select('shoppings.status', 'shopping_details.subtotal', 'shopping_details.type_purchase')
            ->where('shoppings.status', '0')
            ->where('shoppings.type','2') 
            ->where('shopping_details.type_purchase', '2')
            ->whereBetween('shoppings.date', [$fechaInicio, $fechaFin])
            ->where('shoppings.client_id', auth()->user()->client_id)
            
            ->when($localId != -1, function ($query) use ($localId) {
                return $query->where('shoppings.headquarter_id', $localId);
            })
            ->get()
            ->sum(function ($item) {
                return (float) $item->subtotal;
            });


        } else {
            // Establecer un valor predeterminado si no hay fechas válidas
            $gastosRH = 0;
            $gastosCO =0;
            $ingresoB = 0;
            $credit = 0;
            $debit = 0;
            $descDevo = 0;
            $ingresoN = 0;
            $costSale = 0;
            $gastos = 0;
            $utilidad = 0;
            $utilidadO = 0;
        }

        $data = [
            'gastosRH' => $gastosRH,
            'gastosCO' => $gastosCO,
            'ingresoB' => $ingresoB,
            'credit' => $credit,
            'descDevo' => $descDevo,
            'ingresoN' => $ingresoN,
            'costSale' => $costSale,
            'gastos' => $gastos,
            'utilidad' => $utilidad,
            'utilidadO' => $utilidadO,
        ];
        $chartData = [
            'gastosRH' => $gastosRH,
            'gastosCO' => $gastosCO,
            'ingresoB' => $ingresoB,
            'descDevo' => $descDevo,
            'ingresoN' => $ingresoN,
            'costSale' => $costSale,
            'gastos' => $gastos,
            'utilidad' => $utilidad,
            'utilidadO' => $utilidadO,
        ];
        $chart = new MyChartName($chartData);
 
        $cliente_id = auth()->user()->client_id; // Obtiene el ID de la empresa actual

        // Obtener la lista de locales de la empresa actual
        $locales = HeadQuarter::where('client_id', $cliente_id)->pluck('description', 'id');

        return view('analytics.analytics', compact('data', 'chart','locales'));

    }

    public function show()
    {
        $ingresoB = DB::table('sales')->where('status_condition', '1')->sum('subtotal');    // Ingreso bruto

        $credit = DB::table('credit_notes')->sum('taxed');  //selecciona la operacion

        $debit = DB::table('debit_notes')->sum('taxed');

        $descDevo = $credit - $debit;


        $ingresoN = $ingresoB - $credit + $debit; //Ingresos netos

        $costSale = DB::table('kardex')->where('type_transaction', 'Venta')
        ->select(DB::raw('((output * (-1)) * cost) as total'))
            ->get()->sum('total');


        $filtroC = DB::table('kardex')->where('type_transaction', 
        'Nota de Crédito')->orWhere('type_transaction', 'Comunicación de Baja')
            ->select(DB::raw('(entry * cost) as result'))->get()->sum('result');


        $costSale = $costSale - $filtroC;

        $gastos = DB::table('shopping_details')
            ->join('shoppings', 'shopping_details.shopping_id', '=', 'shoppings.id')
            ->select('shoppings.status', 'shopping_details.subtotal', 'shopping_details.type_purchase')
            ->where('shoppings.status', '0')
            ->where('shopping_details.type_purchase', '2')
            ->get()
            ->sum(function ($item) {
                return (float)$item->subtotal;
            });


        $utilidad = $ingresoN - $costSale; //utilidad bruta
        $utilidadO = $utilidad - $gastos; //utilidad operativa


        // Pasar las variables a la vista...
        return view('analytics.analytics', compact('ingresoB', 'costSale', 'utilidad', 'gastos', 'descDevo', 'ingresoN', 'utilidadO'));


        return view('analytics.analytics'); // Devuelve la vista
    }
    /*========================= fin fabrizio========================*/

}
