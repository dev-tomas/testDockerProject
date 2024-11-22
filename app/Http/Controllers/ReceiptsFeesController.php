<?php

namespace App\Http\Controllers;

use App\BankAccount;
use App\Brand;
use App\Cash;
use App\CashMovements;
use App\Category;
use App\Classification;
use App\Client;
use App\Coin;
use App\Correlative;
use App\CostsCenter;
use App\Exports\ReceiptFeesExport;
use App\OperationType;
use App\PaymentMethod;
use App\PriceList;
use App\Product;
use App\Provider;
use App\PurchaseCredit;
use App\Requirement;
use App\Shopping;
use App\ShoppingDetail;
use App\Store;
use App\TypeVoucher;
use App\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ReceiptsFeesController extends Controller
{
    private $_ajax;
    public $headquarter;
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('status.client');
        $this->_ajax = new AjaxController();

        $this->middleware(function ($request, $next) {
            $this->headquarter = session()->has('headlocal') == true ? session()->get('headlocal') : Auth::user()->headquarter_id;
            return $next($request);
        });
    }

    public function index() {
        $data = array(
            'typedocuments' => $this->_ajax->getTypeDocuments(),
        );
        $requirements = Requirement::where('client_id', Auth::user()->headquarter->client_id)->where('status', '1')->get();
        $providers = Provider::where('client_id', Auth::user()->headquarter->client_id)->get(['description','id']);
        $cashes = Cash::where('client_id', auth()->user()->headquarter->client_id)->where('headquarter_id', auth()->user()->headquarter->id)->get(['id','name']);
        $bankAccounts = BankAccount::where('client_id', Auth::user()->headquarter->client_id)->where('bank_account_type_id', '!=','3')->get();
        $paymentMethods = PaymentMethod::where('client_id', auth()->user()->headquarter->client_id)->get(['name', 'id']);

        return view('logistic.receipst-fees.index', compact('requirements', 'providers', 'cashes', 'bankAccounts', 'paymentMethods'))->with($data);
    }

    public function dt_shopping(Request $request)
    {
        $shoppings = Shopping::with('provider:id,document,description', 'coin:id,symbol', 'credit')
            ->where('client_id', auth()->user()->headquarter->client_id)
            ->where('headquarter_id', $this->headquarter)
            ->where('type', 2)
            ->where(function($query) use ($request) {
                if ($request->denomination != '') {
                    $query->whereHas('provider', function($q) use ($request) {
                        $q->where('description', 'like', "%{$request->denomination}%")
                            ->orWhere('document', 'like', "%{$request->denomination}%");
                    });
                }

                if ($request->serial != '') {
                    $query->where('shopping_correlative', 'like', "%{$request->serial}%");
                }

                if($request->get('dateOne') != ''){
                    $query->whereBetween('date',  [$request->get('dateOne'), $request->get('dateTwo')]);
                }
            })
            ->get([
                'serial',
                'correlative',
                'provider_id',
                'coin_id',
                'date',
                'shopping_serie',
                'shopping_correlative',
                'igv',
                'total',
                'shopping_type',
                'shipping_register',
                'id',
                'status',
                'payment_type',
                'total_retention'
            ]);

        return datatables()->of($shoppings)->toJson();
    }

    public function create(Request $request)
    {
        $mainWarehouse = Warehouse::where('headquarter_id', $this->headquarter)->first();
        $mainWarehouseId = $mainWarehouse->id;

        $products = Product::with('operation_type', 'category', 'brand', 'coin_product', 'stock', 'product_price_list.price_list')->whereHas('stock', function ($query) use($mainWarehouseId) {
            $query->where('warehouse_id', $mainWarehouseId);
        })->where('client_id', auth()->user()->headquarter->client_id)
            ->where('status',1)
            ->get();

        $shopping = null;

        $hasData = $request->has('has_data') && $request->has_data && $request->has('shopping');

        if ($hasData) {
            $shopping = Shopping::with('detail')->find($request->shopping);
        }

        $data = array(
            'date' => date('d-m-Y'),
            'typedocuments'     => $this->_ajax->getTypeDocuments(),
            'typevouchers'      => TypeVoucher::all(),
            'coin'              => Coin::all(),
            'product'           => $products,
            'providers'         => Provider::where('client_id',Auth::user()->headquarter->client_id)->get(),
            'categories'        => Category::where('client_id',Auth::user()->headquarter->client_id)->get(),
            'operations_type'   => OperationType::all(),
            'brands'            => Brand::where('client_id',Auth::user()->headquarter->client_id)->get(),
            'correlative'       => Correlative::where('typevoucher_id',12)->where('headquarter_id',$this->headquarter)->first(),
            'classifications'   =>  Classification::all(),
            'clientInfo' => Client::find(Auth::user()->headquarter->client_id),
            'coins'             => Coin::all(),
            'price_lists'       => PriceList::whereNull('client_id')->get(),
            'costCenters'       => CostsCenter::where('client_id', auth()->user()->headquarter->client_id)->get(),
            'warehouses'        => Warehouse::where('client_id', auth()->user()->headquarter->client_id)->get(['id','description']),
            'cashes' => Cash::where('client_id', auth()->user()->headquarter->client_id)->where('headquarter_id', auth()->user()->headquarter->id)->get(['id','name']),
            'bankAccounts' => BankAccount::where('client_id', Auth::user()->headquarter->client_id)->where('bank_account_type_id', '!=','3')->get(),
            'paymentMethods' => PaymentMethod::where('client_id', auth()->user()->headquarter->client_id)->get(['name', 'id']),
            'hasData' => $hasData,
            'shopping' => $shopping,
        );

        return view('logistic.receipst-fees.create')->with($data);
    }

    public function edit($s)
    {
        $mainWarehouse = Warehouse::where('headquarter_id', $this->headquarter)->first();
        $mainWarehouseId = $mainWarehouse->id;

        $products = Product::with('operation_type', 'category', 'brand', 'coin_product', 'stock', 'product_price_list.price_list')->whereHas('stock', function ($query) use($mainWarehouseId) {
            $query->where('warehouse_id', $mainWarehouseId);
        })->where('client_id', auth()->user()->headquarter->client_id)
            ->where('status',1)
            ->get();


        $shopping = Shopping::with('detail')->find($s);

        $data = array(
            'date' => date('d-m-Y'),
            'typedocuments'     => $this->_ajax->getTypeDocuments(),
            'typevouchers'      => TypeVoucher::all(),
            'coin'              => Coin::all(),
            'product'           => $products,
            'providers'         => Provider::where('client_id',Auth::user()->headquarter->client_id)->get(),
            'categories'        => Category::where('client_id',Auth::user()->headquarter->client_id)->get(),
            'operations_type'   => OperationType::all(),
            'brands'            => Brand::where('client_id',Auth::user()->headquarter->client_id)->get(),
            'correlative'       => Correlative::where('typevoucher_id',12)->where('headquarter_id',$this->headquarter)->first(),
            'classifications'   =>  Classification::all(),
            'clientInfo' => Client::find(Auth::user()->headquarter->client_id),
            'coins'             => Coin::all(),
            'price_lists'       => PriceList::whereNull('client_id')->get(),
            'costCenters'       => CostsCenter::where('client_id', auth()->user()->headquarter->client_id)->get(),
            'warehouses'        => Warehouse::where('client_id', auth()->user()->headquarter->client_id)->get(['id','description']),
            'cashes' => Cash::where('client_id', auth()->user()->headquarter->client_id)->where('headquarter_id', auth()->user()->headquarter->id)->get(['id','name']),
            'bankAccounts' => BankAccount::where('client_id', Auth::user()->headquarter->client_id)->where('bank_account_type_id', '!=','3')->get(),
            'paymentMethods' => PaymentMethod::where('client_id', auth()->user()->headquarter->client_id)->get(['name', 'id']),
            'shopping' => $shopping,
        );

        return view('logistic.receipst-fees.edit')->with($data);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $correlatives = Correlative::where([
                ['client_id', auth()->user()->headquarter->client_id],
                ['typevoucher_id', 14],
            ])->first();

            $setCorrelative = (int) $correlatives->correlative + 1;
            $repeat = strlen($correlatives->correlative) - strlen($setCorrelative);
            $final = str_repeat('0',($repeat >=0) ? $repeat : 0).$setCorrelative;

            $correlative = Correlative::find($correlatives->id);
            $correlative->correlative = $final;
            $correlative->save();

            $fecha = date("Y-m-d", strtotime($request->post('fecha')));
            $Shopping = new Shopping;
            $Shopping->date = $fecha;
            $Shopping->exchange_rate = $request->post('exchange_rate');
            $Shopping->serial = $correlatives->serialnumber;
            $Shopping->correlative = $final;
            $Shopping->shopping_type = 1;
            $Shopping->shopping_serie = $request->shoppingSerie;
            $Shopping->shopping_correlative = $request->shoppingCorrelative;
            $Shopping->shipping_register = 9;
            $Shopping->payment_type = $request->payment;
            $Shopping->subtotal = $request->post('gravt');
            $Shopping->discount = 0;
            $Shopping->unaffected = 0;
            $Shopping->exonerated =0;
            $Shopping->taxed = 0;
            $Shopping->igv = 0;
            $Shopping->total = $request->post('totalt');
            $Shopping->provider_id = $request->post('provider');
            $Shopping->coin_id = $request->post('moneda');
            $Shopping->type_vouchers_id = 25;
            $Shopping->has_retention = $request->filled('has_retention');
            $Shopping->total_retention = $request->igvt;
            $Shopping->type = 2;
            $Shopping->status = 1;
            $Shopping->headquarter_id = $this->headquarter;
            $Shopping->client_id = Auth::user()->headquarter->client_id;
            $Shopping->user_id  =   Auth::user()->id;
            $Shopping->payment_type = $request->post('condition');
            if ($request->has('cash') && $request->post('condition') == 'EFECTIVO') {
                $Shopping->cash_id = $request->cash;
            }
            if ($request->has('bank') && $request->post('condition') == 'DEPOSITO EN CUENTA') {
                $Shopping->bank_account_id = $request->bank;
            }
            if ($request->has('mp') && ($request->post('condition') == 'TARJETA DE CREDITO' || $request->post('condition') == 'TARJETA DE DEBITO')) {
                $Shopping->payment_method_id = $request->mp;
            }
            $Shopping->save();

            if ($request->condition == 'EFECTIVO') {
                $movement = new CashMovements;
                $movement->movement = 'SALIDA';
                $movement->amount = "{$Shopping->total}";
                $movement->observation = "{$Shopping->shopping_serie}-{$Shopping->shopping_correlative}";
                $movement->cash_id = $request->cash;
                $movement->user_id = auth()->user()->id;
                $movement->save();
            }

            for($i = 0; $i < count($request->post('producto')); $i++){
                if($request->post('producto')[$i] != '' && $request->post('producto')[$i] != 'undefined') {
                    $shopping_detail = new ShoppingDetail;
                    $shopping_detail->quantity = $request->post('cantidad')[$i];
                    $shopping_detail->unit_value = 0;
                    $shopping_detail->unit_price = $request->post('pre_uni')[$i];
                    $shopping_detail->subtotal = 0;
                    $shopping_detail->total = $request->post('total')[$i];
                    $shopping_detail->product_id = $request->post('producto')[$i];
                    $shopping_detail->shopping_id = $Shopping->id;
                    $shopping_detail->type_purchase = $request->typepurchase[$i];
                    $shopping_detail->center_cost_id = $request->location[$i];
                    $shopping_detail->save();
                }
            }

            if ($request->condition == 'CREDITO') {
                $now = Carbon::now();
                $quotes = 1;
                $credit = new PurchaseCredit;
                $credit->date = $fecha;
                $credit->total = $request->post('totalt');
                $credit->quotes = $quotes;
                $credit->expiration = $fecha;
                $credit->debt = $request->post('totalt');
                $credit->purchase_id = $Shopping->id;
                $credit->client_id = Auth::user()->headquarter->client_id;
                $credit->provider_id = $request->post('provider');
                $credit->save();
            }

            DB::commit();

            return response()->json(true);
        } catch (\Exception $e) {
            DB::rollBack();

            dd($e);

            return response()->json(false);
        }
    }

    public function update(Request $request)
    {
        DB::beginTransaction();
        try {
            $fecha = date("Y-m-d", strtotime($request->post('fecha')));
            $Shopping = Shopping::find($request->current_shopping);
            $Shopping->date = $fecha;
            $Shopping->exchange_rate = $request->post('exchange_rate');
            $Shopping->shopping_type = 1;
            $Shopping->shopping_serie = $request->shoppingSerie;
            $Shopping->shopping_correlative = $request->shoppingCorrelative;
            $Shopping->shipping_register = 9;
            $Shopping->payment_type = $request->payment;
            $Shopping->subtotal = $request->post('gravt');
            $Shopping->discount = 0;
            $Shopping->unaffected = 0;
            $Shopping->exonerated =0;
            $Shopping->taxed = 0;
            $Shopping->igv = 0;
            $Shopping->total = $request->post('totalt');
            $Shopping->provider_id = $request->post('provider');
            $Shopping->coin_id = $request->post('moneda');
            $Shopping->type_vouchers_id = 25;
            $Shopping->has_retention = $request->filled('has_retention');
            $Shopping->total_retention = $request->igvt;
            $Shopping->type = 2;
            $Shopping->status = 1;
            $Shopping->headquarter_id = $this->headquarter;
            $Shopping->client_id = Auth::user()->headquarter->client_id;
            $Shopping->user_id  =   Auth::user()->id;
            $Shopping->payment_type = $request->post('condition');
            if ($request->has('cash') && $request->post('condition') == 'EFECTIVO') {
                $Shopping->cash_id = $request->cash;
            }
            if ($request->has('bank') && $request->post('condition') == 'DEPOSITO EN CUENTA') {
                $Shopping->bank_account_id = $request->bank;
            }
            if ($request->has('mp') && ($request->post('condition') == 'TARJETA DE CREDITO' || $request->post('condition') == 'TARJETA DE DEBITO')) {
                $Shopping->payment_method_id = $request->mp;
            }
            $Shopping->save();

            if ($request->condition == 'EFECTIVO') {
                $movement = new CashMovements;
                $movement->movement = 'SALIDA';
                $movement->amount = "{$Shopping->total}";
                $movement->observation = "{$Shopping->shopping_serie}-{$Shopping->shopping_correlative}";
                $movement->cash_id = $request->cash;
                $movement->user_id = auth()->user()->id;
                $movement->save();
            }

            ShoppingDetail::where('shopping_id', $Shopping->id)->get()->each(function ($item, $index) use ($Shopping) {
                $credit = PurchaseCredit::where('purchase_id', $Shopping->id)->first();
                if ($credit) {
                    $credit->delete();
                }

                $item->delete();
            });

            for($i = 0; $i < count($request->post('producto')); $i++){
                if($request->post('producto')[$i] != '' && $request->post('producto')[$i] != 'undefined') {
                    $shopping_detail = new ShoppingDetail;
                    $shopping_detail->quantity = $request->post('cantidad')[$i];
                    $shopping_detail->unit_value = 0;
                    $shopping_detail->unit_price = $request->post('pre_uni')[$i];
                    $shopping_detail->subtotal = 0;
                    $shopping_detail->total = $request->post('total')[$i];
                    $shopping_detail->product_id = $request->post('producto')[$i];
                    $shopping_detail->shopping_id = $Shopping->id;
                    $shopping_detail->type_purchase = $request->typepurchase[$i];
                    $shopping_detail->center_cost_id = $request->location[$i];
                    $shopping_detail->save();
                }
            }

            if ($request->condition == 'CREDITO') {
                $now = Carbon::now();
                $quotes = 1;
                $credit = new PurchaseCredit;
                $credit->date = $fecha;
                $credit->total = $request->post('totalt');
                $credit->quotes = $quotes;
                $credit->expiration = $fecha;
                $credit->debt = $request->post('totalt');
                $credit->purchase_id = $Shopping->id;
                $credit->client_id = Auth::user()->headquarter->client_id;
                $credit->provider_id = $request->post('provider');
                $credit->save();
            }

            DB::commit();

            return response()->json(true);
        } catch (\Exception $e) {
            DB::rollBack();

            dd($e);

            return response()->json(false);
        }
    }

    public function show($id)
    {
        $purchase = Shopping::where('type', 2)
                            ->where('headquarter_id',$this->headquarter)
                            ->find($id);

        return view('logistic.receipst-fees.show', compact('purchase'));
    }

    public function delete(Request $request)
    {
        DB::beginTransaction();
        try {
            $shopping = Shopping::find($request->purchase);
            $shopping->status = 9;
            $shopping->save();

            DB::commit();

            return response()->json(true);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(false);
        }
    }

    public function export(Request $request)
    {
        $from = Carbon::createFromFormat('d/m/Y', Str::before($request->date, ' -'))->format('Y-m-d') . ' 00:00:00';
        $to = Carbon::createFromFormat('d/m/Y', Str::after($request->date, '- '))->format('Y-m-d') . ' 00:00:00';

        $shoppings = Shopping::with('provider', 'provider.document_type','voucher',)
                            ->whereHas('detail', function($q){
                                $q->orderBy('total', 'DESC');
                            })
                            ->where('status', '!=', 9)
                            ->where('type', 2)
                            ->where('client_id', auth()->user()->headquarter->client_id)
                            ->whereBetween('date', [$from, $to])
                            ->get();

        return Excel::download(new ReceiptFeesExport($shoppings), 'RECIBOS POR HONORARIOS.xlsx');
    }
}
