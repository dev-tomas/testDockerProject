<?php

namespace App\Http\Controllers;

use App\Product;
use Auth;
use App\Sale;
use App\Shopping;
use Carbon\Carbon;
use App\CreditClient;
use App\IconDashboard;
use App\PaymentCredit;
use App\ShoppingDetail;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('status.client');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $icons = IconDashboard::all();

        $date = Carbon::now();

        $now = $date->format('Y-m-d');
        $lastMonth = $date->subDays(30);
        $lastYear = $date->subYears(1);

        $ahora = Carbon::now();
        $desde = $ahora->firstOfMonth()->format('Y-m-d');
        $hasta = $ahora->lastOfMonth()->format('Y-m-d');
        $top10Products = $this->getProductsMoreSale($desde, $hasta);

        $totalLastMonth = $this->getPaidLastMonth($desde, $hasta, 1);
                                
        $totalSpending = Shopping::selectRaw('sum(total) as total')
                            ->where('client_id', auth()->user()->headquarter->client_id)
                            ->whereBetween('date', [$desde, $hasta])
                            ->where('status', '!=', 9)
                            ->where(function($query) {
                                if (
                                    auth()->user()->hasRole('admin') ||
                                    auth()->user()->hasRole('superadmin') ||
                                    auth()->user()->hasRole('manager')
                                ) {} else {
                                    $query->where('user_id', \Illuminate\Support\Facades\Auth::id());
                                }
                            })->first();

        $paidLastMonth = $this->getPaidLastMonth2($desde, $hasta);

        $defeated = Sale::selectRaw('sum(total) as total, count(id) as count')
                        ->where('client_id', auth()->user()->headquarter->client_id)
                        ->whereNull('low_communication_id')
                        ->whereNull('credit_note_id')
                        ->whereBetween('issue', [$lastYear, $now])
                        ->whereDate('expiration', '<', date('Y-m-d'))
                        ->where('paidout', '!=', 1)
                        ->where(function($query) {
                            if (
                                auth()->user()->hasRole('admin') ||
                                auth()->user()->hasRole('superadmin') ||
                                auth()->user()->hasRole('manager')
                            ) {} else {
                                $query->where('user_id', \Illuminate\Support\Facades\Auth::id());
                            }
                        })
                        ->first();

        $defeated = CreditClient::query()->where('client_id', auth()->user()->headquarter->client_id)
                        ->whereHas('sale', function($query) {
                            if (
                                auth()->user()->hasRole('admin') ||
                                auth()->user()->hasRole('superadmin') ||
                                auth()->user()->hasRole('manager')
                            ) {} else {
                                $query->where('user_id', Auth::id());
                            }

                            $query->whereNull('low_communication_id')
                            ->whereNull('credit_note_id')
                            ->where('status_condition', 0)
                            ->where('paidout', '!=', 1);
                        })
                        ->where('status', 0)
                        ->whereDate('expiration', '<', $desde)
                        ->sum('debt');

        $pending = Sale::where('client_id', auth()->user()->headquarter->client_id)
                        ->where(function ($query) {
                            if (
                                auth()->user()->hasRole('admin') ||
                                auth()->user()->hasRole('superadmin') ||
                                auth()->user()->hasRole('manager')
                            ) {} else {
                                $query->where('user_id', Auth::id());
                            }
                        })
                        ->whereNull('low_communication_id')
                        ->whereNull('credit_note_id')
                        ->where('status_condition', 0)
                        ->where('paidout', '!=', 1)
                        ->whereBetween('issue', [$desde, $hasta])
                        ->get()
                        ->pluck(['id']);

        $pend = CreditClient::whereIn('sale_id', $pending)->sum('debt');
        
        return view('home', compact('top10Products', 'icons','totalLastMonth','totalSpending','paidLastMonth','defeated','pend'));
    }

    public function generateSaleMonth()
    {
        $now = Carbon::now();
        $dates = array();
        $total = array();
        $dates2 = array();
        $desde = $now->firstOfMonth()->format('Y-m-d');
        $hasta = $now->lastOfMonth()->format('Y-m-d');

        $period = CarbonPeriod::create($desde, '1 days', $hasta);
        foreach ($period as $key => $date) {
            $dates[] = $date->format('d');
            $dates2[] = $date->format('Y-m-d');
        }

        $totals = DB::table('sales')
                    ->selectRaw('SUM(sales.total) as suma_total, sales.issue')
                    ->whereBetween('issue', [$desde, $hasta])
                    ->where('client_id', auth()->user()->headquarter->client_id)
                    ->where(function ($query) {
                        if (
                            auth()->user()->hasRole('admin') ||
                            auth()->user()->hasRole('superadmin') ||
                            auth()->user()->hasRole('manager')
                        ) {} else {
                            $query->where('user_id', \Illuminate\Support\Facades\Auth::id());
                        }
                    })
                    ->whereNull('low_communication_id')
                    ->where('status', 1)
                    // ->whereNull('credit_note_id')
                    ->groupBy('sales.issue')
                    ->orderBy('sales.issue', 'ASC')
                    ->get();

        for ($i=0; $i < count($dates2); $i++) { 
            $total[] = 0;
            foreach ($totals as $t) {
                if ($t->issue == $dates2[$i]) {
                $total[$i] = $t->suma_total;
                } 
            }
        }

        return response()->json(array('dates' => $dates, 'total' => $total));
    }

    public function generateIncome()
    { 

        $ahora = Carbon::now();
        $desde = $ahora->firstOfMonth()->format('Y-m-d');
        $hasta = $ahora->lastOfMonth()->format('Y-m-d');     
        $lastYear = $ahora->subYear(1);

        $paidLastMonth = $this->getPaidLastMonth2($desde, $hasta);

        $defeated = Sale::selectRaw('sum(total) as total, count(id) as count')
                    ->where('client_id', auth()->user()->headquarter->client_id)
                    ->whereBetween('issue', [$desde, $hasta])
                    ->whereDate('expiration', '<', date('Y-m-d'))
                    ->whereNull('low_communication_id')
                    ->whereNull('credit_note_id')
                    ->where('status', 1)
                    ->where('paidout', '=', 0)
                    ->where(function ($query) {
                        if (
                            auth()->user()->hasRole('admin') ||
                            auth()->user()->hasRole('superadmin') ||
                            auth()->user()->hasRole('manager')
                        ) {} else {
                            $query->where('user_id', Auth::id());
                        }
                    })
                    ->first();


        $defeated = CreditClient::query()->where('client_id', auth()->user()->headquarter->client_id)
                        ->whereHas('sale', function($query) {
                            if (
                                auth()->user()->hasRole('admin') ||
                                auth()->user()->hasRole('superadmin') ||
                                auth()->user()->hasRole('manager')
                            ) {} else {
                                $query->where('user_id', Auth::id());
                            }

                            $query->whereNull('low_communication_id')
                            ->whereNull('credit_note_id')
                            ->where('status_condition', 0)
                            ->where('status', 1)
                            ->where('paidout', '!=', 1);
                        })
                        ->where('status', 0)
                        ->whereDate('expiration', '<', $desde)
                        ->sum('debt');

        $pending = Sale::selectRaw('sum(total) as total, count(id) as count')
                    ->where('client_id', auth()->user()->headquarter->client_id)
                    ->whereBetween('issue', [$desde, $hasta])
                    ->whereNull('low_communication_id')
                    ->whereNull('credit_note_id')
                    ->where('paidout', 0)
                    ->where('status', 1)
                    ->where(function ($query) {
                        if (
                            auth()->user()->hasRole('admin') ||
                            auth()->user()->hasRole('superadmin') ||
                            auth()->user()->hasRole('manager')
                        ) {} else {
                            $query->where('user_id', Auth::id());
                        }
                    })
                    ->first();

    $pending = Sale::where('client_id', auth()->user()->headquarter->client_id)
                    ->where(function ($query) {
                        if (
                            auth()->user()->hasRole('admin') ||
                            auth()->user()->hasRole('superadmin') ||
                            auth()->user()->hasRole('manager')
                        ) {} else {
                            $query->where('user_id', Auth::id());
                        }
                    })
                    ->whereNull('low_communication_id')
                    ->whereNull('credit_note_id')
                    ->where('status_condition', 0)
                    ->where('paidout', '!=', 1)
                    ->where('status', 1)
                    ->whereBetween('issue', [$desde, $hasta])
                    ->get()
                    ->pluck(['id']);

    $pend = CreditClient::whereIn('sale_id', $pending)->sum('debt');

        $totals = array(
            'totals' => [
                $paidLastMonth,
                $defeated,
                $pend
            ]
        );

        return response()->json($totals);
    }

    public function getPaidLastMonth2($desde, $hasta) {
        $paidLastMonth = 0;
        $creditNoteTotal = 0;
        $count = 0;
        $sales = Sale::with('credit_note', 'debit_note')
            ->whereBetween('issue', [$desde, $hasta])
            ->whereNull('low_communication_id')
            ->where('status', 1)
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

        foreach ($sales as $sale) {
            $paidLastMonth += $sale->total;

            if (isset($sale->credit_note)) {
                $paidLastMonth -= $sale->credit_note->total;
            }

            if (isset($sale->debit_note)) {
                $paidLastMonth += $sale->debit_note->total;
            }

            $count++;
        }

        $paymentQuery = PaymentCredit::where('client_id', auth()->user()->headquarter->client_id)
                                        ->whereBetween('date', [$desde, $hasta]);

        return $paidLastMonth + $paymentQuery->sum('payment');
    }

    public function generateSpending(Request $request)
    {
        if ($request->period == 'current_month') {
            $ahora = Carbon::now();
            $desde = $ahora->firstOfMonth()->format('Y-m-d');
            $hasta = $ahora->lastOfMonth()->format('Y-m-d');
        } else {
            $ahora = Carbon::now()->firstOfMonth()->subDays(1);
            $desde = $ahora->firstOfMonth()->format('Y-m-d');
            $hasta = $ahora->lastOfMonth()->format('Y-m-d');
        }

        $shoppingsStock = ShoppingDetail::whereHas('shopping', function($q) use ($desde, $hasta) {
            $q->where('client_id', auth()->user()->headquarter->client_id)->whereBetween('date', [$desde, $hasta])
            ->where('status', '!=', 9);
            if (
                auth()->user()->hasRole('admin') ||
                auth()->user()->hasRole('superadmin') ||
                auth()->user()->hasRole('manager')
            ) {} else {
                $q->where('user_id', auth()->user()->id);
            }
        })->where('type_purchase', 1)->selectRaw('SUM(total) as suma_total')->first();

        $shoppingsGasto = ShoppingDetail::whereHas('shopping', function($q) use ($desde, $hasta) {
                $q->where('client_id', auth()->user()->headquarter->client_id)->whereBetween('date', [$desde, $hasta])
                ->where('status', '!=', 9);
                if (
                    auth()->user()->hasRole('admin') ||
                    auth()->user()->hasRole('superadmin') ||
                    auth()->user()->hasRole('manager')
                ) {} else {
                    $q->where('user_id', auth()->user()->id);
                }
        })->where('type_purchase', 2)->selectRaw('SUM(total) as suma_total')->first();

        $shoppingsActivo = ShoppingDetail::whereHas('shopping', function($q) use ($desde, $hasta) {
                $q->where('client_id', auth()->user()->headquarter->client_id)->whereBetween('date', [$desde, $hasta])
                ->where('status', '!=', 9);
            if (
                auth()->user()->hasRole('admin') ||
                auth()->user()->hasRole('superadmin') ||
                auth()->user()->hasRole('manager')
            ) {} else {
                $q->where('user_id', auth()->user()->id);
            }
        })->where('type_purchase', 0)->selectRaw('SUM(total) as suma_total')->first();

        $totalSpending = Shopping::selectRaw('sum(total) as total')
            ->where('client_id', auth()->user()->headquarter->client_id)
            ->whereBetween('date', [$desde, $hasta])
            ->where('status', '!=', 9)
            ->where(function($query) {
                if (
                    auth()->user()->hasRole('admin') ||
                    auth()->user()->hasRole('superadmin') ||
                    auth()->user()->hasRole('manager')
                ) {} else {
                    $query->where('user_id', \Illuminate\Support\Facades\Auth::id());
                }
            })->first();

        $totals = array(
            'totals' => [
                $shoppingsStock->suma_total == null ? 0.00 : $shoppingsStock->suma_total, 
                $shoppingsGasto->suma_total == null ? 0.00 : $shoppingsGasto->suma_total, 
                $shoppingsActivo->suma_total == null ? 0.00 : $shoppingsActivo->suma_total, 
            ],
            'totalSpending' => number_format($totalSpending->total, 2, '.', '')
        );

        return response()->json($totals);
    }

    public function getPaidLastMonth($desde, $hasta, $type = null) {
        $paidLastMonth = 0;
        $count = 0;
        $sales = Sale::with('credit_note', 'debit_note')
            ->whereBetween('issue', [$desde, $hasta])
            ->whereNull('low_communication_id')
            ->where('status', 1)
            ->where('client_id', auth()->user()->headquarter->client_id)
            ->where(function ($query) use ($type) {
                if (
                    auth()->user()->hasRole('admin') ||
                    auth()->user()->hasRole('superadmin') ||
                    auth()->user()->hasRole('manager')
                ) {} else {
                    $query->where('user_id', \Illuminate\Support\Facades\Auth::id());
                }

                if ($type == null) {
                    $query->where('paidout', 1);
                }
            })->get();

            foreach ($sales as $sale) {
                $paidLastMonth += $sale->total;
    
                if (isset($sale->credit_note)) {
                    $paidLastMonth -= $sale->credit_note->total;
                }
    
                if (isset($sale->debit_note)) {
                    $paidLastMonth += $sale->debit_note->total;
                }
    
                $count++;
            }

        return array(
            'total' =>  $paidLastMonth,
            'count' =>  $count
        );
    }

    public function getProductsMoreSale($from, $to): array
    {
        $top_10_products = Product::query()
            ->select('products.id', 'products.description', DB::raw('SUM(saledetails.quantity) as total_sold'))
            ->join('saledetails', 'products.id', '=', 'saledetails.product_id')
            ->join('sales', 'sales.id', '=', 'saledetails.sale_id')
            ->whereBetween('sales.date', [$from, $to])
            ->whereNull('sales.low_communication_id')
            ->where('sales.status', 1)
            ->where('sales.client_id', auth()->user()->headquarter->client_id)
            ->groupBy('products.id', 'products.description')
            ->orderBy(DB::raw('SUM(saledetails.quantity)'), 'desc')
            ->limit(10)
            ->get()
            ->toArray();

        return  $top_10_products;
    }
}
