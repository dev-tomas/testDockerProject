<?php

namespace App\Http\Controllers;

use DB;
use PDF;
use Str;
use App\Cash;
use App\Coin;
use Response;
use App\Brand;
use App\Store;
use App\Client;
use App\Kardex;
use App\Product;
use App\Category;
use App\Customer;
use App\Provider;
use App\Shopping;
use App\Inventory;
use App\PriceList;
use App\Warehouse;
use Carbon\Carbon;
use App\BankAccount;
use App\Correlative;
use App\CostsCenter;
use App\HeadQuarter;
use App\Mail\SendOC;
use App\Requirement;
use App\TypeVoucher;
use App\TypeDocument;
use App\CashMovements;
use App\OperationType;
use App\PaymentMethod;
use App\PurchaseOrder;
use App\Classification;
use App\PurchaseCredit;
use App\ShoppingDetail;
use App\ProductPriceLog;
use App\Http\Controllers;
use App\ProviderQuotation;
use App\RequirementDetails;
use App\PurchaseOrderDetail;
use Illuminate\Http\Request;
use App\PurchaseCreditPayment;
use App\Exports\ProvidersExport;
use App\Imports\ProvidersImport;
use App\Exports\PurchasesExports;
use http\Exception\RuntimeException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PurchaseOrdersExports;
use App\Mail\SendRequirementsPurchase;

class LogisticController extends Controller
{
    private $_ajax;
    public $headquarter;
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('status.client');

        $this->middleware('can:proveedores.show')->only(['providers','dt_providers']);
        // $this->middleware('can:proveedores.create')->only(['createProviders']);
        $this->middleware('can:proveedores.importar')->only(['importProviders']);
        $this->middleware('can:proveedores.export')->only(['exportProviders','exportProvidersTemplate']);
        $this->middleware('can:proveedores.delete')->only(['deleteProviders']);

        $this->middleware('can:propuestas.show')->only(['showProposals', 'getProposals', 'createOc']);

        $this->middleware('can:compras.show')->only(['purchases','dt_shopping']);
        $this->middleware('can:compras.fisica')->only(['physicalRecord']);
        $this->middleware('can:compras.electronica')->only(['purchaseElec', 'registerProviderXML', 'registerProductXML']);
        $this->middleware('can:compras.new')->only(['sendRequirements']);
        $this->middleware('can:compras.export')->only(['excelPurchases']);

        $this->middleware('can:ocompra.show')->only(['indexOC','dt_purchaseOrders']);
        $this->middleware('can:ocompra.send')->only(['sendOC']);
        $this->middleware('can:ocompra.edit')->only(['editOC', 'updateOC']);
        $this->middleware('can:ocompra.delete')->only(['deleteOC']);
        $this->middleware('can:ocompra.export')->only(['excelOc']);

        $this->_ajax = new AjaxController();

        $this->middleware(function ($request, $next) {
            $this->headquarter = session()->has('headlocal') == true ? session()->get('headlocal') : Auth::user()->headquarter_id;
            return $next($request);
        });
    }

    /**
     * Providers
     */
    public function providers() {
        $data = array(
            'typedocuments' => $this->_ajax->getTypeDocuments(),
        );
        return view('logistic.provider.list')->with($data);
    }

    public function dt_providers(Request $request)
    {
        return datatables()->of(
            Db::table('providers')
                ->join('typedocuments','providers.typedocument_id','=','typedocuments.id')
                ->where(function ($query) use($request) {
                    if($request->get('search') != ''){
                        $query->where('providers.description', 'like', '%' . $request->get('search') . '%')
                                ->orWhere('providers.document', 'like', '%' . $request->get('search') . '%');
                        }
                    }
                )
                ->where('providers.client_id', auth()->user()->headquarter->client_id)
                ->get([
                    'typedocuments.description as td_description',
                    'providers.description as c_description',
                    'providers.document',
                    'providers.phone',
                    'providers.address',
                    'providers.email',
                    'providers.detraction',
                    'providers.id',
                    'providers.code'
                ])
        )->toJson();
    }

    public function dt_shopping(Request $request)
{
    // Iniciar la consulta sobre la tabla shoppings
    $query = Shopping::with('provider:id,document,description', 'coin:id,symbol', 'typeVoucher:id,code', 'credit')
        ->where('shoppings.client_id', auth()->user()->headquarter->client_id)
        ->where('shoppings.headquarter_id', $this->headquarter)
        ->where('shoppings.type', 1); // Filtramos por tipo 1

    // Aplicar filtros comunes (denominación, serial, estado, fechas)
    $query->where(function($query) use ($request) {
        if ($request->denomination != '') {
            $query->whereHas('provider', function($q) use ($request) {
                $q->where('description', 'like', "%{$request->denomination}%")
                  ->orWhere('document', 'like', "%{$request->denomination}%");
            });
        }

        if ($request->serial != '') {
            $query->where('shopping_correlative', 'like', "%{$request->serial}%");
        }

        if ($request->filter_status != '') {
            $query->where('paidout', $request->filter_status);
        }

        if ($request->get('dateOne') != '') {
            $query->whereBetween('shoppings.date', [$request->get('dateOne'), $request->get('dateTwo')]);
        }
    });

    // Condición para el shopping_filter
    if ($request->shopping_filter != '') {
        // Si hay un filtro, hacer el join con shopping_details y aplicar el filtro
        $query->join('shopping_details', 'shoppings.id', '=', 'shopping_details.shopping_id')
              ->where('shopping_details.type_purchase', $request->shopping_filter)
              ->select([
                  'shoppings.serial',
                  'shoppings.correlative',
                  'shoppings.provider_id',
                  'shoppings.coin_id',
                  'shoppings.type_vouchers_id',
                  'shoppings.date',
                  'shoppings.shopping_serie',
                  'shoppings.shopping_correlative',
                  'shoppings.igv',
                  'shoppings.total',
                  'shoppings.id',
                  'shopping_details.type_purchase' // Incluir este campo solo si se aplica el join
              ]);
    } else {
        // Si no hay filtro, solo seleccionar campos de shoppings
        $query->select([
            'shoppings.serial',
            'shoppings.correlative',
            'shoppings.provider_id',
            'shoppings.coin_id',
            'shoppings.type_vouchers_id',
            'shoppings.date',
            'shoppings.shopping_serie',
            'shoppings.shopping_correlative',
            'shoppings.igv',
            'shoppings.total',
            'shoppings.id',
            'shoppings.shopping_type' // Este campo de shoppings se muestra cuando no hay filtro
        ]);
    }

    // Ejecutar la consulta y obtener resultados
    $shoppings = $query->get();

    return datatables()->of($shoppings)->toJson();
}



    public function showPurchase($serie, $correlative)
    {
        $purchase = Shopping::where('serial', $serie)->where('type', 1)->where('correlative', $correlative)->where('headquarter_id',$this->headquarter)->first();

        return view('logistic.purchase.show', compact('purchase'));
    }

    public function pdf_shopping($id)
    {
        $purchase = Shopping::where('type', 1)->where('headquarter_id',$this->headquarter)->find($id);
        $clientInfo = $purchase->client;

        $pdf = PDF::loadView('logistic.purchase.pdf', compact('purchase', 'clientInfo'))->setPaper('A4');
        return $pdf->stream("COMPRA {$purchase->shopping_serie}-{$purchase->shopping_correlative}.pdf");
    }

    public function importProviders(Request $request)
    {
        Excel::import(new ProvidersImport, $request->file('file'));
        toastr()->success('Se importaron los proveedores con éxito.');
        return back();
    }

    public function exportProviders()
    {
        return Excel::download(new ProvidersExport, 'proveedores.xlsx');
    }


    public function exportProvidersTemplate()
    {
        $file= public_path(). "/templates/Plantilla_Proveedores.xlsx";


        $headers = array(
            'Content-Type: application/xlsx',
        );

        return response()->download($file, 'Plantilla_Proveedores' . date('d-m-Y') . '.xlsx', $headers);
    }

    public function getLastProviderCode()
    {
        $lastCode = Provider::where('client_id', auth()->user()->headquarter->client_id)->orderBy('id', 'desc')->select('code')->first();

        if ($lastCode != null) {
            if (is_numeric($lastCode->code)) {
                $code = (int) $lastCode->code + 1; 
            } else {
                $code = 1;
            }
        } else {
            $code = 1;
        }

        return response()->json(str_pad($code, 5, 0, STR_PAD_LEFT));
    }

    public function createProviders(Request $request){
        if($request->post('provider_id')){
            $provider = Provider::find($request->post('provider_id'));

        }else{
            $provider = new Provider;

            $existCustomer = Provider::where('document', $request->document)->where('client_id', auth()->user()->headquarter->client_id)->first();

            if ($existCustomer != null) {
                return response()->json(-99);
            }
        }

        $existCustomerWithCode = Provider::where('code', $request->code)->where('id', '!=', $request->provider_id)->where('client_id', auth()->user()->headquarter->client_id)->first();

        if ($existCustomerWithCode != null) {
            return response()->json(-99);
        }

        $provider->description      = $request->post('description');
        $provider->document         = $request->post('document');
        $provider->code         = $request->post('code');
        $provider->phone            = $request->post('phone');
        $provider->address          = $request->post('address');
        $provider->email            = $request->post('email');
        $provider->secondary_email  = $request->post('email');// Lo ingreso por que no permite que sea null
        $provider->tradename        = $request->post('description');// ''''
        $provider->detraction       = $request->post('detraction');
        $provider->typedocument_id  = $request->post('typedocument');
        $provider->client_id        = Auth::user()->headquarter->client_id;
        $provider->contact          = $request->post('contact');

        return response()->json($provider->save());
    }

    public function saveProvider(Request $request){
        if($request->post('provider_id')){
            $provider = Provider::find($request->post('provider_id'));

        }else{
            $provider = new Provider;
        }

        $provider->description      = $request->post('description');
        $provider->document         = $request->post('document');
        $provider->code         = $request->post('code');
        $provider->phone            = $request->post('phone');
        $provider->address          = $request->post('address');
        $provider->email            = $request->post('email');
        $provider->secondary_email  = $request->post('email');// Lo ingreso por que no permite que sea null
        $provider->tradename        = $request->post('email');// ''''
        $provider->detraction       = $request->post('detraction');
        $provider->typedocument_id  = $request->post('typedocument');   
        $provider->client_id        = Auth::user()->headquarter->client_id;
        $provider->contact          = $request->post('contact');

        return response()->json($provider->save());
    }


    /**
     * Crud Brands
     */
    public function saveBrand(Request $request)
    {
        $brand = new Brand;
        $brand->description = $request->post('add_brand');
        $brand->client_id = Auth::user()->headquarter->client_id;
        return response()->json($brand->save());
    }

    public function getBrand(Request $request)
    {
        if($request->get('brand_id')) {
            return Brand::find($request->get('brand_id'));
        } else {
            return Brand::where('client_id', Auth::user()->headquarter->client_id)->get();
        }
    }

    public function getProviders(Request $request)
    {
        return response()->json(Provider::find($request->get('provider_id')));
    }

    public function getAllProviders()
    {
        return response()->json(Provider::all());
    }

    public function deleteProviders(Request $request){
        $provider=Provider::find($request->get('provider_id'));
        return response()->json($provider->delete());
    }

    public function purchases() {
        $data = array(
            'typedocuments' => $this->_ajax->getTypeDocuments(),
        );
        $requirements = Requirement::where('client_id', Auth::user()->headquarter->client_id)->where('status', '1')->get();
        $providers = Provider::where('client_id', Auth::user()->headquarter->client_id)->get(['description','id']);
        $cashes = Cash::where('client_id', auth()->user()->headquarter->client_id)->where('headquarter_id', auth()->user()->headquarter->id)->get(['id','name']);
        $bankAccounts = BankAccount::where('client_id', Auth::user()->headquarter->client_id)->where('bank_account_type_id', '!=','3')->get();
        $paymentMethods = PaymentMethod::where('client_id', auth()->user()->headquarter->client_id)->get(['name', 'id']);

        return view('logistic.purchase.list', compact('requirements', 'providers', 'cashes', 'bankAccounts', 'paymentMethods'))->with($data);
    }

    public function getProvider()
    {
        $providers = Provider::where('client_id', Auth::user()->headquarter->client_id)->get(['description','id']);

        return $providers;
    }


    public function physicalRecord(Request $request){
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
        return view('logistic.purchase.addphysical')->with($data);
    }

    public function saveShopping(Request $request){
        DB::beginTransaction();
        try{
            if($request->post('producto') == NUll){
                return -1;
            }

            $existShoppingWithSameCorrelative = Shopping::query()->where('shopping_serie', $request->shoppingSerie)
                                                        ->where('shopping_correlative', $request->shoppingCorrelative)
                                                        ->where('provider_id', $request->post('provider'))
                                                        ->where('client_id', auth()->user()->headquarter->client_id)
                                                        ->where('status', '!=', 9)
                                                        ->first();

            if ($existShoppingWithSameCorrelative != null) {
                return response()->json(-18);
            }

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
            $Shopping->shipping_register = 1;
            $Shopping->payment_type = $request->payment;
            $Shopping->subtotal = $request->post('c_subtotal');
            $Shopping->discount = $request->post('c_discount');
            $Shopping->unaffected = $request->post('inat');
            $Shopping->exonerated = $request->post('exot');
            $Shopping->taxed = $request->post('gravt');
            $Shopping->igv = $request->post('igvt');
            $Shopping->total = $request->post('totalt');
            $Shopping->provider_id = $request->post('provider');
            $Shopping->coin_id = $request->post('moneda');
            $Shopping->type_vouchers_id = $request->post('tipvou');
            $Shopping->headquarter_id = $this->headquarter;
            $Shopping->client_id = Auth::user()->headquarter->client_id;
            $Shopping->user_id  =   Auth::user()->id;
            $Shopping->tax_base = $request->post('tax_base');
            $Shopping->payment_type = $request->post('condition');
            $Shopping->igv_percentage = $request->post('igv_percentage');
            $Shopping->observations = $request->post('observations');
            $Shopping->paidout = 0;
            if ($request->has('cash') && $request->post('condition') == 'EFECTIVO') {
                $Shopping->cash_id = $request->cash;
                $Shopping->paidout = 1;
            }

            if ($request->has('bank') && $request->post('condition') == 'DEPOSITO EN CUENTA') {
                $Shopping->bank_account_id = $request->bank;
                $Shopping->paidout = 1;
            }

            if ($request->has('mp') && ($request->post('condition') == 'TARJETA DE CREDITO' || $request->post('condition') == 'TARJETA DE DEBITO')) {
                $Shopping->payment_method_id = $request->mp;
                $Shopping->paidout = 1;
            }
            $Shopping->save();
            $shopping_id = $Shopping->id;

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
                    $shopping_detail->unit_value = $request->post('val_uni')[$i];
                    $shopping_detail->unit_price = $request->post('pre_uni')[$i];
                    $shopping_detail->subtotal = $request->post('subtotal')[$i];
                    $shopping_detail->total = $request->post('total')[$i];
                    $shopping_detail->product_id = $request->post('producto')[$i];
                    $shopping_detail->shopping_id = $shopping_id;
                    $shopping_detail->type_purchase = $request->typepurchase[$i];
                    if ($request->typepurchase[$i] == 1) {
                        $shopping_detail->warehouse_id = $request->location[$i];
                    } else {
                        $shopping_detail->center_cost_id = $request->location[$i];
                    }
                    $shopping_detail->save();

                    $cost = $request->post('val_uni')[$i];

                    if ($request->post('moneda') == 2 && (float) $request->post('exchange_rate') > 0.00) {
                        $cost = (float) $cost * (float) $request->post('exchange_rate');
                    }

                    $productos = Product::where('id', $request->post('producto')[$i])
                                            ->where('client_id', Auth::user()->headquarter->client_id)
                                            ->first();
                    $productos->cost = $cost;
                    $productos->update();

                    if ($request->typepurchase[$i] == 1) {
                        $stock = Store::where('product_id', $request->producto[$i])->where('warehouse_id', $request->location[$i])->first();

                        if($stock == null) {
                            $stock = new Store;
                            $stock->stock = $request->cantidad[$i];
                            $stock->price = 0.00;
                            $stock->higher_price = 0.00;
                            $stock->maximum_stock = 0.00;
                            $stock->minimum_stock = 0.00;
                            $stock->location = 0.00;
                            $stock->product_id = $request->producto[$i];
                            $stock->warehouse_id = $request->location[$i];
                            $nuevoStock = $request->cantidad[$i];
                        } else {
                            $os = $stock->stock;
                            $stock->stock = (int) $os + (int) $request->cantidad[$i];
                            $nuevoStock = (int) $os + (int) $request->cantidad[$i];
                        }

                        $stock->save();


                        /**
                         * Crear un nuevo registro de precios al log
                         */
                        $product_price_log = new ProductPriceLog();
                        $product_price_log->price           =   $request->pre_uni[$i];
                        $product_price_log->stock           =   $request->cantidad[$i];
                        $product_price_log->initial_stock   =   $request->cantidad[$i];
                        $product_price_log->product_id      =   $request->producto[$i];
                        $product_price_log->warehouse_id    =   $request->location[$i];
                        $product_price_log->state           =   1;
                        $product_price_log->save();

                        $correlatives = Correlative::where([
                            ['client_id', auth()->user()->headquarter->client_id],
                            ['typevoucher_id', 20]
                        ])->first();

                        $setCorrelative = (int) $correlatives->correlative + 1;
                        $repeat = strlen($correlatives->correlative) - strlen($setCorrelative);
                        $final = str_repeat('0',($repeat >=0) ? $repeat : 0).$setCorrelative;

                        $correlative = Correlative::find($correlatives->id);
                        $correlative->correlative = $final;
                        $correlative->save();

                        $existInventory = Inventory::where('client_id', auth()->user()->headquarter->client_id)->where('product_id', $request->producto[$i])
                                                    ->where('warehouse_id', $request->location[$i])->where('headquarter_id', $this->headquarter)->get();

                        if (!$existInventory->isEmpty()) {
                            foreach ($existInventory as $iv) {
                                $in = Inventory::find($iv->id);
                                $in->status = 0;
                                $in->save();
                            }
                        }

                        $admission = new Inventory;
                        $admission->shopping_id = $shopping_id;
                        $admission->provider_id = $request->provider;
                        $admission->guide = $request->shoppingSerie . '-' . $request->shoppingCorrelative;
                        $admission->warehouse_id = $request->location[$i];
                        $admission->client_id = auth()->user()->headquarter->client_id;
                        $admission->headquarter_id =  $this->headquarter;
                        $admission->admission = date("Y-m-d", strtotime($request->fecha));
                        $admission->serie = $correlative->serialnumber;
                        $admission->correlative = $final;
                        $admission->place = null;
                        $admission->responsable = auth()->user()->name;
                        $admission->serial = null;
                        $admission->lot = null;
                        $admission->expiration = date("Y-m-d", strtotime($request->fecha));
                        $admission->warranty = null;
                        $admission->amount_entered = $request->cantidad[$i];
                        $admission->observation = null;
                        $admission->product_id = $request->producto[$i];
                        $admission->save();

                        $kardex = new Kardex;
                        $kardex->type_transaction = 'COMPRA';
                        $kardex->number = $request->shoppingSerie . '-' . $request->shoppingCorrelative;
                        $kardex->entry = $request->cantidad[$i];
                        $kardex->balance = $stock->stock;
                        $kardex->cost = $stock->product->cost;
                        $kardex->warehouse_id = $request->location[$i];
                        $kardex->client_id = auth()->user()->headquarter->client_id;
                        $kardex->product_id = $request->producto[$i];
                        $kardex->date_created_at = date('Y-m-d');
                        $kardex->created_at = Carbon::parse($request->post('fecha'));
                        $kardex->coin_id = $request->post('moneda');
                        $kardex->exchange_rate = $request->post('exchange_rate');
                        $kardex->save();

                        $shopping = Shopping::find($shopping_id);
                        $shopping->status = 1;
                        $shopping->update();
                    }
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
                $credit->purchase_id = $shopping_id;
                $credit->client_id = Auth::user()->headquarter->client_id;
                $credit->provider_id = $request->post('provider');
                $credit->save();
            }

            DB::commit();
            return response()->json(true);
        }
        catch (\Exception $e) {
            DB::rollBack();
            $rpta = '';
            switch ($e->getCode()) {
                default:
                    $rpta = $e;
                    break;
            }

            return $rpta;
        }
    }

    public function editPurchase($id)
    {
        $mainWarehouse = Warehouse::where('headquarter_id', $this->headquarter)->first();
        $mainWarehouseId = $mainWarehouse->id;
        $products = Product::with('operation_type', 'category', 'brand', 'coin_product', 'stock', 'product_price_list.price_list')->whereHas('stock', function ($query) use($mainWarehouseId) {
            $query->where('warehouse_id', $mainWarehouseId);
        })->where('client_id', auth()->user()->headquarter->client_id)
            ->where('status',1)
            ->get();

        $shopping = Shopping::with('detail')->find($id);
        $shoppingDetails = $shopping->detail;

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
            'shoppingDetails' => $shoppingDetails
        );
        return view('logistic.purchase.edit')->with($data);
    }

    public function updatePurchase(Request $request)
    {
        DB::beginTransaction();
        try{
            if($request->post('producto') == NUll){
                return -1;
            }

            $fecha = date("Y-m-d", strtotime($request->post('fecha')));
            $Shopping = Shopping::find($request->shopping_id);
            $Shopping->date = $fecha;
            $Shopping->exchange_rate = $request->post('exchange_rate');
            $Shopping->shopping_type = 1;
            $Shopping->shopping_serie = $request->shoppingSerie;
            $Shopping->shopping_correlative = $request->shoppingCorrelative;
            $Shopping->shipping_register = 1;
            $Shopping->payment_type = $request->payment;
            $Shopping->subtotal = $request->post('c_subtotal');
            $Shopping->discount = $request->post('c_discount');
            $Shopping->unaffected = $request->post('inat');
            $Shopping->exonerated = $request->post('exot');
            $Shopping->taxed = $request->post('gravt');
            $Shopping->igv = $request->post('igvt');
            $Shopping->total = $request->post('totalt');
            $Shopping->provider_id = $request->post('provider');
            $Shopping->coin_id = $request->post('moneda');
            $Shopping->type_vouchers_id = $request->post('tipvou');
            $Shopping->headquarter_id = $this->headquarter;
            $Shopping->client_id = Auth::user()->headquarter->client_id;
            $Shopping->user_id  =   Auth::user()->id;
            $Shopping->tax_base = $request->post('tax_base');
            $Shopping->payment_type = $request->post('condition');
            $Shopping->paidout = 0;
            if ($request->has('cash') && $request->post('condition') == 'EFECTIVO') {
                $Shopping->cash_id = $request->cash;
                $Shopping->paidout = 1;
            }

            if ($request->has('bank') && $request->post('condition') == 'DEPOSITO EN CUENTA') {
                $Shopping->bank_account_id = $request->bank;
                $Shopping->paidout = 1;
            }

            if ($request->has('mp') && ($request->post('condition') == 'TARJETA DE CREDITO' || $request->post('condition') == 'TARJETA DE DEBITO')) {
                $Shopping->payment_method_id = $request->mp;
                $Shopping->paidout = 1;
            }
            $Shopping->igv_percentage = $request->post('igv_percentage');
            $Shopping->observations = $request->post('observations');
            $Shopping->save();
            $shopping_id = $Shopping->id;

            if ($request->condition == 'EFECTIVO') {
                $deleteMovement = CashMovements::where('observation', "{$Shopping->shopping_serie}-{$Shopping->shopping_correlative}")->first();
                if ($deleteMovement) {
                    $deleteMovement->delete();
                }
            }

            ShoppingDetail::where('shopping_id', $Shopping->id)->get()->each(function ($item, $index) use ($Shopping) {
                if ($item->type_purchase == 1) {
                    $stock = Store::where('product_id', $item->product_id)
                                    ->where('warehouse_id', $item->warehouse_id)->first();
                    if ($stock) {
                        $stock->stock = (float) $stock->stock - (float) $item->quantity;
                        $stock->save();

                        $kardex = new Kardex;
                        $kardex->type_transaction = 'ANULACION DE COMPRA';
                        $kardex->number = "{$Shopping->shopping_serie}-{$Shopping->shopping_correlative}";
                        $kardex->output = $item->quantity;
                        $kardex->balance = $stock->stock;
                        $kardex->cost = $stock->product->cost;
                        $kardex->warehouse_id = $item->warehouse_id;
                        $kardex->client_id = auth()->user()->headquarter->client_id;
                        $kardex->product_id = $item->product_id;
                        $kardex->date_created_at = date('Y-m-d');
                        $kardex->created_at = Carbon::parse($Shopping->date);
                        $kardex->coin_id = $Shopping->coin_id;
                        $kardex->exchange_rate = $Shopping->exchange_rate;
                        $kardex->save();
                    }
                }

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
                    $shopping_detail->unit_value = $request->post('val_uni')[$i];
                    $shopping_detail->unit_price = $request->post('pre_uni')[$i];
                    $shopping_detail->subtotal = $request->post('subtotal')[$i];
                    $shopping_detail->total = $request->post('total')[$i];
                    $shopping_detail->product_id = $request->post('producto')[$i];
                    $shopping_detail->shopping_id = $shopping_id;
                    $shopping_detail->type_purchase = $request->typepurchase[$i];
                    if ($request->typepurchase[$i] == 1) {
                        $shopping_detail->warehouse_id = $request->location[$i];
                    } else {
                        $shopping_detail->center_cost_id = $request->location[$i];
                    }
                    $shopping_detail->save();

                    $cost = $request->post('val_uni')[$i];

                    if ($request->post('moneda') == 2 && (float) $request->post('exchange_rate') > 0.00) {
                        $cost = (float) $cost * (float) $request->post('exchange_rate');
                    }

                    $productos = Product::where('id', $request->post('producto')[$i])
                        ->where('client_id', Auth::user()->headquarter->client_id)
                        ->first();
                    $productos->cost = $cost;
                    $productos->update();

                    if ($request->typepurchase[$i] == 1) {
                        $stock = Store::where('product_id', $request->producto[$i])->where('warehouse_id', $request->location[$i])->first();

                        if($stock == null) {
                            $stock = new Store;
                            $stock->stock = $request->cantidad[$i];
                            $stock->price = 0.00;
                            $stock->higher_price = 0.00;
                            $stock->maximum_stock = 0.00;
                            $stock->minimum_stock = 0.00;
                            $stock->location = 0.00;
                            $stock->product_id = $request->producto[$i];
                            $stock->warehouse_id = $request->location[$i];
                            $nuevoStock = $request->cantidad[$i];
                        } else {
                            $os = $stock->stock;
                            $stock->stock = (int) $os + (int) $request->cantidad[$i];
                            $nuevoStock = (int) $os + (int) $request->cantidad[$i];
                        }

                        $stock->save();


                        /**
                         * Crear un nuevo registro de precios al log
                         */
                        $product_price_log = new ProductPriceLog();
                        $product_price_log->price           =   $request->pre_uni[$i];
                        $product_price_log->stock           =   $request->cantidad[$i];
                        $product_price_log->initial_stock   =   $request->cantidad[$i];
                        $product_price_log->product_id      =   $request->producto[$i];
                        $product_price_log->warehouse_id    =   $request->location[$i];
                        $product_price_log->state           =   1;
                        $product_price_log->save();

                        $correlatives = Correlative::where([
                            ['client_id', auth()->user()->headquarter->client_id],
                            ['typevoucher_id', 20]
                        ])->first();

                        $setCorrelative = (int) $correlatives->correlative + 1;
                        $repeat = strlen($correlatives->correlative) - strlen($setCorrelative);
                        $final = str_repeat('0',($repeat >=0) ? $repeat : 0).$setCorrelative;

                        $correlative = Correlative::find($correlatives->id);
                        $correlative->correlative = $final;
                        $correlative->save();

                        $existInventory = Inventory::where('client_id', auth()->user()->headquarter->client_id)->where('product_id', $request->producto[$i])
                            ->where('warehouse_id', $request->location[$i])->where('headquarter_id', $this->headquarter)->get();

                        if (!$existInventory->isEmpty()) {
                            foreach ($existInventory as $iv) {
                                $in = Inventory::find($iv->id);
                                $in->status = 0;
                                $in->save();
                            }
                        }

                        $admission = new Inventory;
                        $admission->shopping_id = $shopping_id;
                        $admission->provider_id = $request->provider;
                        $admission->guide = $request->shoppingSerie . '-' . $request->shoppingCorrelative;
                        $admission->warehouse_id = $request->location[$i];
                        $admission->client_id = auth()->user()->headquarter->client_id;
                        $admission->headquarter_id =  $this->headquarter;
                        $admission->admission = date("Y-m-d", strtotime($request->fecha));
                        $admission->serie = $correlative->serialnumber;
                        $admission->correlative = $final;
                        $admission->place = null;
                        $admission->responsable = auth()->user()->name;
                        $admission->serial = null;
                        $admission->lot = null;
                        $admission->expiration = date("Y-m-d", strtotime($request->fecha));
                        $admission->warranty = null;
                        $admission->amount_entered = $request->cantidad[$i];
                        $admission->observation = null;
                        $admission->product_id = $request->producto[$i];
                        $admission->save();

                        $kardex = new Kardex;
                        $kardex->type_transaction = 'COMPRA';
                        $kardex->number = $request->shoppingSerie . '-' . $request->shoppingCorrelative;
                        $kardex->entry = $request->cantidad[$i];
                        $kardex->balance = $stock->stock;
                        $kardex->cost = $stock->product->cost;
                        $kardex->warehouse_id = $request->location[$i];
                        $kardex->client_id = auth()->user()->headquarter->client_id;
                        $kardex->product_id = $request->producto[$i];
                        $kardex->date_created_at = date('Y-m-d');
                        $kardex->created_at = Carbon::parse($request->post('fecha'));
                        $kardex->coin_id = $request->post('moneda');
                        $kardex->exchange_rate = $request->post('exchange_rate');
                        $kardex->save();

                        $shopping = Shopping::find($shopping_id);
                        $shopping->status = 1;
                        $shopping->update();
                    }
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
                $credit->purchase_id = $shopping_id;
                $credit->client_id = Auth::user()->headquarter->client_id;
                $credit->provider_id = $request->post('provider');
                $credit->save();
            }

            DB::commit();
            return response()->json(true);
        }
        catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            $rpta = '';
            switch ($e->getCode()) {
                default:
                    $rpta = $e;
                    break;
            }

            return $rpta;
        }
    }

    public function updateMethodPaymentShopping(Request $request)
    {
        try{
            $shopping = Shopping::find($request->sid);
            $shopping->payment_type = $request->payment;
            $total = $shopping->total;
            $date = date('Y-m-d',strtotime($shopping->date));
            $provider = $shopping->provider_id;
            $shopping->update();

            if ($request->payment == 'CREDITO') {
                $now = Carbon::now();
                $quotes = (int) $request->quotes;
                $credit = new PurchaseCredit;
                $credit->date = $date;
                $credit->total = $total;
                $credit->quotes = $quotes;
                $credit->expiration = date('Y-m-d',strtotime(date('Y-m-d', strtotime($date)). '+' . $quotes . 'months'));
                $credit->debt = $total;
                $credit->purchase_id = $request->sid;
                $credit->client_id = Auth::user()->headquarter->client_id;
                $credit->provider_id = $provider;
                $credit->save();

                $totalPerQuote = (float) $request->totalt / (float) $quotes;

                for ($i=1; $i <= $quotes; $i++) {
                    $quoteExpiration = date('Y-m-d',strtotime(date('Y-m-d', strtotime($date)).'+' . $i . 'months'));

                    $payment = new PurchaseCreditPayment;
                    $payment->date = $date;
                    $payment->expiration = date('Y-m-d', strtotime($quoteExpiration));
                    $payment->quote = $i;
                    $payment->payment = $totalPerQuote;
                    $payment->purchase_credit_id = $credit->id;
                    $payment->client_id = Auth::user()->headquarter->client_id;
                    $payment->save();
                }
            }

            return response()->json(true);
        } catch (\Exception $e) {
            echo $e->getCode();
            $rpta = 'Ooops';

            return response()->json($rpta);
        }
    }

    public function registerSirePurchase(Request $request)
    {
        DB::beginTransaction();
        try {
            $file = $request->file('file');
            $file = fopen($file, "r");
            $firstRow = true;
            $totalLines = 0;
            $insertedLines = 0;
            $errorLines = 0;

            while (($data = fgetcsv($file, 1000, "|")) !== FALSE) {
                if ($firstRow) {
                    $firstRow = false;
                    continue;
                }

                $totalLines++;

                if (count($data) != 80) {
                    $errorLines++;
                    continue;
                }

                $typeDocument = $data[11];
                $document = $data[12];
                $denomination = $data[13];
                $unaffected = abs($data[20]);
                $taxed = abs($data[14]);
                $igv = abs($data[15]);
                $total = abs($data[24]);
                $coin = $data['25'] == 'PEN' ? 1 : 2;

                $provider = Provider::where('client_id', auth()->user()->headquarter->client_id)
                    ->where('document', $document)
                    ->first();

                if ($provider == null) {
                    $td = TypeDocument::whereCode($typeDocument)->first();
                    $provider = new Provider;
                    $provider->client_id = auth()->user()->headquarter->client_id;
                    $provider->document = $document;
                    $provider->description = $denomination;
                    $provider->typedocument_id = $td->id;
                    $provider->code = $this->getLastProviderCode()->getContent();
                    $provider->save();
                }


                $existShoppingWithSameCorrelative = Shopping::query()->where('shopping_serie', $data[7])
                    ->where('shopping_correlative', $data[9])
                    ->where('provider_id', $provider->id)
                    ->where('client_id', auth()->user()->headquarter->client_id)
                    ->where('status', '!=', 9)
                    ->first();

                if ($existShoppingWithSameCorrelative != null) {
                    continue;
                }

                $cash = Cash::where('client_id', auth()->user()->headquarter->client_id)
                    ->where('headquarter_id', auth()->user()->headquarter->id)
                    ->select('id','name')->first();

                $typeVoucher = TypeVoucher::whereCode($data[6])->first();

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

                $fecha = date("Y-m-d", strtotime($data[4]));
                $Shopping = new Shopping;
                $Shopping->date = Carbon::createFromFormat('d/m/Y', $data[4])->format('Y-m-d');
                $Shopping->exchange_rate = $data[25] != 'PEN' ? $data[26] : null;
                $Shopping->serial = $correlatives->serialnumber;
                $Shopping->correlative = $final;
                $Shopping->shopping_type = 1;
                $Shopping->shopping_serie = $data[7];
                $Shopping->shopping_correlative = $data[9];
                $Shopping->shipping_register = 1;
                $Shopping->payment_type = 'EFECTIVO';
                $Shopping->subtotal = $taxed;
                $Shopping->discount = 0;
                $Shopping->unaffected = $unaffected;
                $Shopping->exonerated = 0;
                $Shopping->taxed = $taxed;
                $Shopping->igv = $igv;
                $Shopping->total = $total;
                $Shopping->provider_id = $provider->id;
                $Shopping->coin_id = $coin;
                $Shopping->type_vouchers_id = $typeVoucher->id;
                $Shopping->headquarter_id = $this->headquarter;
                $Shopping->client_id = Auth::user()->headquarter->client_id;
                $Shopping->user_id  =   Auth::user()->id;
                $Shopping->tax_base = '006';
                $Shopping->payment_type = 'EFECTIVO';
                $Shopping->igv_percentage = 18;
                $Shopping->cash_id = $cash->id;
                $Shopping->paidout = 1;
                $Shopping->save();

                $insertedLines++;
            }
            fclose($file);


            DB::commit();;

            toastr()->success("Se registraron correctamente los datos. Total Líneas: {$totalLines}. Total Líneas con error: {$errorLines}. Total Líneas insertada: {$insertedLines}");

            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();

            toastr()->error('Ocurrió un error al intentar registrar los datos');

            return redirect()->back();
        }
    }

    public function registerSireReceipts(Request $request)
{
    DB::beginTransaction();
    try {
        $file = $request->file('file');
        $file = fopen($file, "r");
        $firstRow = true;
        $totalLines = 0;
        $insertedLines = 0;
        $errorLines = 0;
        $receiptsData = []; // Array para almacenar los datos de los recibos

        while (($data = fgetcsv($file, 1000, "|")) !== FALSE) {
            if ($firstRow) {
                $firstRow = false;
                continue;
            }

            $totalLines++;

            // Verificar si los datos son suficientes
            if (count($data) < 15) { // Asegúrate de que haya al menos 15 columnas
                $errorLines++;
                continue;
            }

            // Verificar el estado
            $estado = trim($data[3]); // Cambia la posición según donde esté el estado
            if ($estado !== "NO ANULADO") {
                continue; // Si el estado no es "NO ANULADO", pasar a la siguiente línea
            }

            // Convertir la fecha al formato correcto
            $fecha = Carbon::createFromFormat('d/m/Y', $data[0])->format('Y-m-d'); // Fecha en formato d/m/Y

            $recibo = explode('-', $data[2]); // Separar recibo en serie y número
            if (count($recibo) != 2) {
                $errorLines++;
                continue;
            }

            $serie = $recibo[0];  // 'E001'
            $numero = $recibo[1]; // '49'

            $documento = $data[5]; // RUC
            $denominacion = trim($data[6]); // Razón social
            
            // Asignar moneda como 1 (Soles)
            $moneda = 1; // 1 para Soles
            $totalRetencion = abs(floatval($data[13])); // Monto de retención
            $total = abs(floatval($data[14])); // Monto total

            // Buscar o crear el proveedor (por RUC/DNI/ETC)
            $provider = Provider::where('client_id', auth()->user()->headquarter->client_id)
                ->where('document', $documento)
                ->first();

            if ($provider == null) {
                $provider = new Provider;
                $provider->client_id = auth()->user()->headquarter->client_id;
                $provider->document = $documento;
                $provider->description = $denominacion;
                $provider->typedocument_id = 1; // Asignar tipo de documento 1 directamente
                $provider->code = $this->getLastProviderCode()->getContent();
                $provider->save();
            }

            // Verificar si el recibo ya existe
            $existReceiptWithSameCorrelative = Shopping::query()->where('shopping_serie', $serie)
                ->where('shopping_correlative', $numero) 
                ->where('provider_id', $provider->id)
                ->where('client_id', auth()->user()->headquarter->client_id)
                ->where('status', '!=', 9)
                ->first();

            if ($existReceiptWithSameCorrelative != null) {
                continue;
            }

            // Crear el nuevo recibo por honorarios
            $receipt = new Shopping;
            $receipt->date = $fecha;
            $receipt->shopping_serie = $serie; 
            $receipt->shopping_correlative = $numero; 
            $receipt->total_retention = $totalRetencion;
            $receipt->total = $total;
            $receipt->provider_id = $provider->id;
            $receipt->coin_id = $moneda; 
            $receipt->client_id = auth()->user()->headquarter->client_id; 
            $receipt->user_id = auth()->user()->id; 
            $receipt->type_vouchers_id = 25; 
            $receipt->headquarter_id = auth()->user()->headquarter->id; 
            $receipt->shopping_type = 1; 
            $receipt->type = 2; 
            $receipt->save();

            $insertedLines++;

            // Almacenar los datos del recibo en el array
            $receiptsData[] = [
                'date' => $fecha,
                'serie' => $serie,
                'correlative' => $numero,
                'total_retention' => $totalRetencion,
                'total' => $total,
                'provider_id' => $provider->id,
                'coin_id' => $moneda,
            ];
        }

        fclose($file);

        DB::commit();

        toastr()->success("Se registraron correctamente los datos. Total Líneas: {$totalLines}. Total Líneas con error: {$errorLines}. Total Líneas insertadas: {$insertedLines}");

        return redirect()->back();
    } catch (\Exception $e) {
        DB::rollBack();

        toastr()->error('Ocurrió un error al intentar registrar los datos: ' . $e->getMessage());

        return redirect()->back();
    }
}



    
    public function purchaseElec(Request $request)
    {
        $xml = simplexml_load_file($request->file('filex'));

        $file = $request->file('filex');
        $extension = $file->getClientOriginalExtension();
        $fileName = $file->getClientOriginalName() . '-' . Auth::user()->headquarter->client_id . '.' . $extension;
        $path = public_path('purchases/xmls/'.Auth::user()->headquarter->client_id);
        $file->move($path, $fileName);

        $currentXMLPath = $fileName;

        $invoice = simplexml_load_file($path . '/' . $currentXMLPath);
        $provider = '';
        foreach($invoice->xpath('//cac:AccountingSupplierParty/cbc:CustomerAssignedAccountID') as $ruc){
            $provider = $ruc;
        }

        if (empty($invoice->xpath('//cac:AccountingSupplierParty/cbc:CustomerAssignedAccountID'))) {
            foreach ($invoice->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyIdentification/cbc:ID') as $ruc) {
                $provider = $ruc;
            }
        }

        $sProvider = Provider::where('document', $provider)->where('client_id', Auth::user()->headquarter->client_id)->first();

        if ($sProvider != null) {
            foreach($invoice->xpath('//cac:InvoiceLine/cac:Item/cbc:Description') as $product){
                $currentProducts = Product::where('description', $product)->where('client_id', Auth::user()->headquarter->client_id)->first();
            }
            if ($currentProducts == null) {
                $rpta = -2;
            } else {
                $products = array();
                $quantities = array();
                $vu = array();
                $pu = array();
                $igv2 = array();
                $totals = array();

                foreach ($invoice->xpath('//cac:Signature/cbc:ID') as $el) {
                    $serial = $el;
                }
                foreach ($invoice->xpath('//cbc:PayableAmount/@currencyID') as $el) {
                    $currency = $el;
                }
                foreach ($xml->xpath('//cbc:InvoiceTypeCode') as $el) {
                    $typeDocCode = $el;
                }
                foreach ($invoice->xpath('//cac:LegalMonetaryTotal/cbc:PayableAmount') as $el) {
                    $total = $el;
                }
                foreach ($invoice->xpath('//cac:TaxTotal/cbc:TaxAmount') as $el) {
                    $igv = $el;
                }
                foreach ($invoice->xpath('//cbc:IssueDate') as $el) {
                    $fecha = $el;
                }

                foreach($invoice->xpath('//cac:InvoiceLine/cbc:InvoicedQuantity') as $el) {
                    $quantities[] = $el;
                }
                foreach($invoice->xpath('//cac:InvoiceLine/cac:Price/cbc:PriceAmount') as $el) {
                    $pu[] = $el;
                }
                foreach($invoice->xpath('//cac:InvoiceLine/cac:PricingReference/cac:AlternativeConditionPrice/cbc:PriceAmount') as $el) {
                    $vu[] = $el;
                }
                foreach($invoice->xpath('//cac:InvoiceLine/cac:TaxTotal/cbc:TaxAmount') as $el) {
                    $igv2[] = $el;
                }
                foreach($invoice->xpath('//cac:InvoiceLine/cac:Item/cbc:Description') as $el) {
                    $product = Product::where('description', $el)->where('client_id', Auth::user()->headquarter->client_id)->first();
                    $products[] = $product->id;
                }
                foreach($invoice->xpath('//cac:InvoiceLine/cbc:LineExtensionAmount') as $el) {
                    $totals[] = $el;
                }

                $mainWarehouse = Warehouse::where('headquarter_id', $this->headquarter)->first();
                $mainWarehouseId = $mainWarehouse->id;

                for ($i=0; $i < count($products); $i++) {
                    $currentStock = Store::where('product_id', $products[$i])->where('warehouse_id', $mainWarehouseId)->first();
                    $currentStockActually = $currentStock->stock;

                    $kardex = new Kardex;
                    $kardex->type_transaction = 'Compra';
                    $kardex->entry = $quantities[$i];
                    $kardex->cost = $currentStock->product->cost;
                    $kardex->balance = (int) $currentStockActually + (int) $quantities[$i];
                    $kardex->warehouse_id = $mainWarehouseId;
                    $kardex->client_id = auth()->user()->headquarter->client_id;
                    $kardex->product_id = $products[$i];
                    $kardex->save();
                }

                $provider = $sProvider->id;
                $serie = Str::before($serial, '-');
                $correlativeShopping = Str::after($serial, '-');
                $typeRegister = 2;
                $typeShopping = 2;
                $currency == 'USD' ? $coin = 2 : $coin = 1;
                $typeDoc = TypeVoucher::where('code', $typeDocCode)->first();
                $typeDocId = $typeDoc->id;
                $headquarter = $this->headquarter;

                $correlatives = Correlative::where([
                    ['headquarter_id', Auth::user()->headquarter->client_id],
                    ['typevoucher_id', 12],
                ])->first();

                $setCorrelative = (int) $correlatives->correlative + 1;
                $repeat = strlen($correlatives->correlative) - strlen($setCorrelative);
                $final = str_repeat('0',($repeat >=0) ? $repeat : 0).$setCorrelative;

                $correlative = Correlative::find($correlatives->id);
                $correlative->correlative = $final;
                $correlative->save();

                $Shopping = new Shopping;
                $Shopping->date = $fecha;
                $Shopping->serial = $correlatives->serialnumber;
                $Shopping->correlative = $final;
                $Shopping->igv = $igv;
                $Shopping->total = $total;
                $Shopping->shopping_serie = $serie;
                $Shopping->shopping_correlative = $correlativeShopping;
                $Shopping->shopping_type = $typeShopping;
                $Shopping->shipping_register = $typeRegister;
                $Shopping->provider_id = $provider;
                $Shopping->coin_id = $coin;
                $Shopping->type_vouchers_id = $typeDocId;
                $Shopping->headquarter_id = $headquarter;
                $Shopping->client_id = auth()->user()->headquarter->client_id;
                if($Shopping->save()) {
                    for($i = 0; $i < count($products); $i++){
                        $shopping_detail = new ShoppingDetail;
                        $shopping_detail->quantity = $quantities[$i];
                        $shopping_detail->unit_value = $vu[$i];
                        $shopping_detail->unit_price = $pu[$i];
                        $shopping_detail->subtotal = (int) $totals[$i] - (int) $igv2[$i];
                        $shopping_detail->igv = $igv2[$i];
                        $shopping_detail->total = $totals[$i];
                        $shopping_detail->product_id = $products[$i];
                        $shopping_detail->shopping_id = $Shopping->id;
                        $shopping_detail->save();

                        $productos = Product::where('id', $products[$i])->where('client_id', Auth::user()->headquarter->client_id)->first();
                        $productos->cost = $pu[$i];
                        $productos->update();
                    }
                }

                $rpta = true;
            }
        } else {
            $rpta = -1;
        }

        return response()->JSON(array('rpta' => $rpta, 'path' => $currentXMLPath));
    }
    public function registerProviderXML(Request $request)
    {
        $path = public_path('purchases/xmls/'.Auth::user()->headquarter->client_id);
        $currentXMLPath = $request->path;

        $xml = simplexml_load_file($path . '/' . $currentXMLPath);

        foreach($xml->xpath('//cac:AccountingSupplierParty/cbc:CustomerAssignedAccountID') as $el){
            $doc = $el;
        }

        if (empty($xml->xpath('//cac:AccountingSupplierParty/cbc:CustomerAssignedAccountID'))) {
            foreach ($xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyIdentification/cbc:ID') as $el) {
                $doc = $el;
            }
        }

        foreach ($xml->xpath('//cac:AccountingSupplierParty/cbc:AdditionalAccountID') as $el) {
            $docCode = $el;
        }

        if (empty($xml->xpath('//cac:AccountingSupplierParty/cbc:AdditionalAccountID'))) {
            foreach ($xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyIdentification/cbc:ID/@schemeID') as $el) {
                $docCode = $el;
            }
        }
        foreach ($xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyName/cbc:Name') as $el) {
            $name = $el;
        }
        foreach ($xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyLegalEntity/cbc:RegistrationName') as $el) {
            $tradeName = $el;
        }
        foreach ($xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PostalAddress/cbc:StreetName') as $el) {
            $street = $el;
        }
        if (empty($xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PostalAddress/cbc:StreetName'))) {
            foreach ($xml->xpath('//cac:RegistrationAddress/cac:AddressLine/cbc:Line') as $el) {
                $address = (string) $el;
            }
        } else {
            foreach ($xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PostalAddress/cbc:CityName') as $el) {
                $city = $el;
            }
            foreach ($xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PostalAddress/cbc:CountrySubentity') as $el) {
                $province = $el;
            }
            foreach ($xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PostalAddress/cbc:District') as $el) {
                $district = $el;
            }

            $address = $street . ' ' . $city . ' ' . $district . ' ' . $province;
        }

        $document = TypeDocument::where('code', $docCode)->first();
        $documentId = $document->id;
        $clientId = Auth::user()->headquarter->client_id;

        $provider = new Provider;
        $provider->description = $name;
        $provider->document = $doc;
        $provider->address = $address;
        $provider->tradename = $tradeName;
        $provider->typedocument_id = $documentId;
        $provider->client_id = $clientId;

        if($provider->save()) {
            foreach($xml->xpath('//cac:InvoiceLine/cac:Item/cbc:Description') as $product){
                $currentProducts = Product::where('description', $product)->where('client_id', Auth::user()->headquarter->client_id)->first();
                if ($currentProducts == null) {
                    $rpta = -2;
                } else {
                    $products = array();
                    $quantities = array();
                    $vu = array();
                    $pu = array();
                    $igv = array();
                    $totals = array();

                    foreach ($xml->xpath('//cac:Signature/cbc:ID') as $el) {
                        $serial = $el;
                    }
                    foreach ($xml->xpath('//cbc:PayableAmount/@currencyID') as $el) {
                        $currency = $el;
                    }
                    foreach ($xml->xpath('//sac:SUNATTransaction/cbc:ID') as $el) {
                        $typeDocCode = $el;
                    }
                    foreach ($xml->xpath('//cac:LegalMonetaryTotal/cbc:PayableAmount') as $el) {
                        $total = $el;
                    }
                    foreach ($xml->xpath('//cac:TaxTotal/cbc:TaxAmount') as $el) {
                        $igv = $el;
                    }
                    foreach ($xml->xpath('//cbc:IssueDate') as $el) {
                        $fecha = $el;
                    }
                    foreach($xml->xpath('//cac:InvoiceLine/cbc:InvoicedQuantity') as $el) {
                        $quantities[] = $el;
                    }
                    foreach($xml->xpath('//cac:InvoiceLine/cac:Price/cbc:PriceAmount') as $el) {
                        $pu[] = $el;
                    }
                    foreach($xml->xpath('//cac:InvoiceLine/cac:PricingReference/cac:AlternativeConditionPrice/cbc:PriceAmount') as $el) {
                        $vu[] = $el;
                    }
                    foreach($xml->xpath('//cac:InvoiceLine/cac:TaxTotal/cbc:TaxAmount') as $el) {
                        $igv[] = $el;
                    }
                    foreach($xml->xpath('//cac:InvoiceLine/cac:Item/cbc:Description') as $el) {
                        $product = Product::where('description', $el)->where('client_id', Auth::user()->headquarter->client_id)->first();
                        $products[] = $product->id;
                    }
                    foreach($xml->xpath('//cac:InvoiceLine/cbc:LineExtensionAmount') as $el) {
                        $totals[] = $el;
                    }

                    $provider = $provider->id;
                    $serie = Str::before($serial, '-');
                    $correlativeShopping = Str::after($serial, '-');
                    $typeRegister = 2;
                    $typeShopping = 2;
                    $currency == 'USD' ? $coin = 2 : $coin = 1;
                    $typeDoc = TypeVoucher::where('code', $typeDocCode)->first();
                    $typeDocId = $typeDoc->id;
                    $headquarter = $this->headquarter;

                    $correlatives = Correlative::where([
                        ['headquarter_id', Auth::user()->headquarter->client_id],
                        ['typevoucher_id', 12],
                    ])->first();

                    $setCorrelative = (int) $correlatives->correlative + 1;
                    $repeat = strlen($correlatives->correlative) - strlen($setCorrelative);
                    $final = str_repeat('0',($repeat >=0) ? $repeat : 0).$setCorrelative;

                    $correlative = Correlative::find($correlatives->id);
                    $correlative->correlative = $final;
                    $correlative->save();

                    $Shopping = new Shopping;
                    $Shopping->date = $fecha;
                    $Shopping->serial = $correlatives->serialnumber;
                    $Shopping->correlative = $final;
                    $Shopping->igv = $igv;
                    $Shopping->total = $total;
                    $Shopping->shopping_serie = $serie;
                    $Shopping->shopping_correlative = $correlativeShopping;
                    $Shopping->shopping_type = $typeShopping;
                    $Shopping->shipping_register = $typeRegister;
                    $Shopping->provider_id = $provider;
                    $Shopping->coin_id = $coin;
                    $Shopping->type_vouchers_id = $typeDocId;
                    $Shopping->headquarter_id = $headquarter;
                    $Shopping->client_id = auth()->user()->headquarter->client_id;
                    if($Shopping->save()) {
                        for($i = 0; $i < count($products); $i++){
                            $shopping_detail = new ShoppingDetail;
                            $shopping_detail->quantity = $quantities[$i];
                            $shopping_detail->unit_value = $vu[$i];
                            $shopping_detail->unit_price = $pu[$i];
                            $shopping_detail->subtotal = (int) $totals[$i] - (int) $igv[$i];
                            $shopping_detail->igv = $igv[$i];
                            $shopping_detail->total = $totals[$i];
                            $shopping_detail->product_id = $products[$i];
                            $shopping_detail->shopping_id = $Shopping->id;
                            $shopping_detail->save();

                            $productos = Product::where('id', $products[$i])->where('client_id', Auth::user()->headquarter->client_id)->first();
                            $productos->cost = $pu[$i];
                            $productos->update();
                        }
                    }

                    $mainHeadquarter = HeadQuarter::where('client_id', Auth::user()->headquarter->client_id)->first();
                    $mainHeadquarterId = $mainHeadquarter->id;

                    $mainWarehouse = Warehouse::where('headquarter_id', $this->headquarter)->first();
                    $mainWarehouseId = $mainWarehouse->id;

                    for ($i=0; $i < count($products); $i++) {
                        $currentStock = Store::where('product_id', $products[$i])->where('warehouse_id', $mainWarehouseId)->first();
                        $currentStockActually = $currentStock->stock;

                        $kardex = new Kardex;
                        $kardex->type_transaction = 'Compra';
                        $kardex->entry = $quantities[$i];
                        $kardex->cost = $currentStock->product->cost;
                        $kardex->balance = (int) $currentStockActually + (int) $quantities[$i];
                        $kardex->warehouse_id = $mainWarehouseId;
                        $kardex->client_id = auth()->user()->headquarter->client_id;
                        $kardex->product_id = $products[$i];
                        $kardex->save();
                    }

                    $rpta = true;
                }
            }
        }

        return response()->JSON(array('rpta' => $rpta, 'path' => $currentXMLPath));
    }

    public function registerProductXML(Request $request)
    {
        $path = public_path('purchases/xmls/'.Auth::user()->headquarter->client_id);
        $currentXMLPath = $request->path;

        $xml = simplexml_load_file($path . '/' . $currentXMLPath);

        $typeProd = array();
        $precioUnitario = array();
        $description = array();
        $code = array();

        foreach($xml->xpath('//cac:InvoiceLine/cbc:InvoicedQuantity/@unitCode') as $el){
            $typeProd[] = $el;
        }
        foreach($xml->xpath('//cac:InvoiceLine/cac:Price/cbc:PriceAmount') as $el){
            $precioUnitario[] = $el;
        }
        foreach($xml->xpath('//cac:InvoiceLine/cac:Price/cbc:PriceAmount/@currencyID') as $el){
            $precioUnitarioMoneda = $el;
        }
        foreach($xml->xpath('//cac:InvoiceLine/cac:Item/cbc:Description') as $el){
            $description[] = $el;
        }
        foreach($xml->xpath('//cac:InvoiceLine/cac:Item/cac:SellersItemIdentification/cbc:ID') as $el){
            $code[] = $el;
        }
        $clientId = auth()->user()->client_id;
        $category = Category::where('client_id', $clientId)->where('description', 'SIN CATEGORÍA')->first();
        $brand = Brand::where('client_id', $clientId)->where('description', 'SIN MARCA')->first();

        for ($i=0; $i < count($description); $i++) {
            $precioUnitarioMoneda[$i] == 'USD' ? $coin = 2 : $coin = 1;
            $unit = OperationType::where('code', $typeProd[$i])->first();
            $unitId = $unit->id;


            $product = new Product;
            $product->description = $description[$i];
            if (empty($code)) {
                $product->code = time();
                $product->internalcode = time();
            } else {
                $product->code = $code[$i];
                $product->internalcode = $code[$i];
            }
            $product->category_id = $category->id;
            $product->brand_id = $brand->id;
            $product->status = 1;
            $product->client_id = $clientId;
            $product->coin_id = $coin;
            $product->operation_type = $unitId;
            $product->save();
        }

        $products = array();
        $quantities = array();
        $vu = array();
        $pu = array();
        $igv2 = array();
        $totals = array();

        foreach ($xml->xpath('//cac:Signature/cbc:ID') as $el) {
            $serial = $el;
        }
        foreach ($xml->xpath('//cbc:PayableAmount/@currencyID') as $el) {
            $currency = $el;
        }
        foreach ($xml->xpath('//cbc:InvoiceTypeCode') as $el) {
            $typeDocCode = $el;
        }
        foreach ($xml->xpath('//cac:LegalMonetaryTotal/cbc:PayableAmount') as $el) {
            $total = $el;
        }
        foreach ($xml->xpath('//cac:TaxTotal/cbc:TaxAmount') as $el) {
            $igv = $el;
        }
        foreach ($xml->xpath('//cbc:IssueDate') as $el) {
            $fecha = $el;
        }
        foreach($xml->xpath('//cac:InvoiceLine/cbc:InvoicedQuantity') as $el) {
            $quantities[] = $el;
        }
        foreach($xml->xpath('//cac:InvoiceLine/cac:Price/cbc:PriceAmount') as $el) {
            $pu[] = $el;
        }
        foreach($xml->xpath('//cac:InvoiceLine/cac:PricingReference/cac:AlternativeConditionPrice/cbc:PriceAmount') as $el) {
            $vu[] = $el;
        }
        foreach($xml->xpath('//cac:InvoiceLine/cac:TaxTotal/cbc:TaxAmount') as $el) {
            $igv2[] = $el;
        }
        foreach($xml->xpath('//cac:InvoiceLine/cac:Item/cbc:Description') as $el) {
            $product = Product::where('description', $el)->where('client_id', Auth::user()->headquarter->client_id)->first();
            $products[] = $product->id;
        }
        foreach($xml->xpath('//cac:InvoiceLine/cbc:LineExtensionAmount') as $el) {
            $totals[] = $el;
        }
        foreach($xml->xpath('//cac:AccountingSupplierParty/cbc:CustomerAssignedAccountID') as $ruc){
            $provider = $ruc;
        }
        if (empty($xml->xpath('//cac:AccountingSupplierParty/cbc:CustomerAssignedAccountID'))) {
            foreach ($xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyIdentification/cbc:ID') as $ruc) {
                $provider = $ruc;
            }
        }

        $mainWarehouse = Warehouse::where('headquarter_id', $this->headquarter)->first();
        $mainWarehouseId = $mainWarehouse->id;

        for ($i=0; $i < count($products); $i++) {
            $store = new Store;
            $store->stock = $quantities[$i];
            $store->price = $precioUnitario[$i];
            $store->product_id = $products[$i];
            $store->warehouse_id = $mainWarehouseId;
            $store->save();
        }

        $sProvider = Provider::where('document', $provider)->where('client_id', Auth::user()->headquarter->client_id)->first();

        $provider = $sProvider->id;
        $serie = Str::before($serial, '-');
        $correlativeShopping = Str::after($serial, '-');
        $typeRegister = 2;
        $typeShopping = 2;
        $currency == 'USD' ? $coin = 2 : $coin = 1;
        $typeDoc = TypeVoucher::where('code', $typeDocCode)->first();
        $typeDocId = $typeDoc->id;
        $headquarter = $this->headquarter;

        $correlatives = Correlative::where([
            ['headquarter_id', Auth::user()->headquarter->client_id],
            ['typevoucher_id', 12],
        ])->first();

        $setCorrelative = (int) $correlatives->correlative + 1;
        $repeat = strlen($correlatives->correlative) - strlen($setCorrelative);
        $final = str_repeat('0',($repeat >=0) ? $repeat : 0).$setCorrelative;

        $correlative = Correlative::find($correlatives->id);
        $correlative->correlative = $final;
        $correlative->save();

        $Shopping = new Shopping;
        $Shopping->date = $fecha;
        $Shopping->serial = $correlatives->serialnumber;
        $Shopping->correlative = $final;
        $Shopping->igv = $igv;
        $Shopping->total = $total;
        $Shopping->shopping_serie = $serie;
        $Shopping->shopping_correlative = $correlativeShopping;
        $Shopping->shopping_type = $typeShopping;
        $Shopping->shipping_register = $typeRegister;
        $Shopping->provider_id = $provider;
        $Shopping->coin_id = $coin;
        $Shopping->type_vouchers_id = $typeDocId;
        $Shopping->headquarter_id = $headquarter;
        $Shopping->client_id = auth()->user()->headquarter->client_id;
        if($Shopping->save()) {
            for($i = 0; $i < count($products); $i++){
                $shopping_detail = new ShoppingDetail;
                $shopping_detail->quantity = $quantities[$i];
                $shopping_detail->unit_value = $vu[$i];
                $shopping_detail->unit_price = $pu[$i];
                $shopping_detail->subtotal = (int) $totals[$i] - (int) $igv2[$i];
                $shopping_detail->igv = $igv2[$i];
                $shopping_detail->total = $totals[$i];
                $shopping_detail->product_id = $products[$i];
                $shopping_detail->shopping_id = $Shopping->id;
                $shopping_detail->save();

                $productos = Product::where('id', $products[$i])->where('client_id', Auth::user()->headquarter->client_id)->first();
                $productos->cost = $pu[$i];
                $productos->update();
            }
        }

        $rpta = true;

        return response()->JSON(array('rpta' => $rpta, 'path' => $currentXMLPath));
    }

    public function excelPurchases(Request $request)
    {
        $from = Carbon::createFromFormat('d/m/Y', \Illuminate\Support\Str::before($request->date, ' -'))->format('Y-m-d');
        $to = Carbon::createFromFormat('d/m/Y', Str::after($request->date, '- '))->format('Y-m-d');

        $shoppings = Shopping::where('headquarter_id', auth()->user()->headquarter_id)
                            ->whereBetween('date', [$from, $to])
                            ->where(function($query) use ($request) {
                                if ($request->status != '') {
                                    $query->where('paidout', $request->status);
                                }
                            })
                            ->get();

        return Excel::download(new PurchasesExports($shoppings), 'compras.xlsx');
    }

    public function updateShopping(Request $request)
    {
        $shopping = Shopping::find($request->sid);
        $shopping->shopping_serie = $request->sserie;
        $shopping->shopping_correlative = $request->scorrelative;
        $shopping->update();
        return response()->JSON(true);
    }

    public function sendRequirements(Request $request)
    {
        $requirement = Requirement::find($request->requirements);
        $clientInfo = Client::find(Auth::user()->headquarter->client_id);
        $pdf = PDF::loadView('logistic.requirements.pdf', compact('requirement', 'clientInfo'))->setPaper('A4');

        $provider = Provider::where('client_id', Auth::user()->headquarter->client_id)->whereIn('id', $request->provider)->get(['email', 'description']);
        $ruc = Auth::user()->headquarter->client->document;

        foreach ($provider as $p) {
            if ($p->email == null) {
                return response()->json(-5);
            }

            Mail::to($p->email)
                ->send(
                    new SendRequirementsPurchase(
                        $requirement,
                        $pdf,
                        $clientInfo,
                        $ruc,
                        $p->description
                    )
                );
        }

        if(Mail::failures()) {
            return response()->json('No se pudo enviar el Correo');
        } else {
            return response()->JSON(true);
        }
    }

    public function index(){
        $data = array(
            'typedocuments'     => $this->_ajax->getTypeDocuments(),
        );
        return view('logistic.requirements.index')->with($data);
    }

    public function addRequirements(){
        $data = array(
            'typevouchers'      => TypeVoucher::all(),
        );
        return view('logistic.requirements.addRequirements')->with($data);
    }

    public function showProposals()
    {
        $requirements = Requirement::where('client_id', Auth::user()->headquarter->client_id)->where('status', '1')->get();

        return view('logistic.provider.proposal', compact('requirements'));
    }

    public function getProposals(Request $request)
    {
        $requirement = $request->requirement;

        $proposals = DB::table('providers_quotations')->join('providers', 'providers.id', '=', 'providers_quotations.provider_id')
                                        ->where('providers.client_id', Auth::user()->headquarter->client_id)
                                        ->where('providers_quotations.client_id', Auth::user()->headquarter->client_id)
                                        ->where('providers_quotations.requirement_id', $requirement)
                                        ->get([
                                            'providers_quotations.file as file',
                                            'providers.document as document',
                                            'providers_quotations.id as id'
                                        ]);

        return response()->JSON($proposals);
    }

    public function createOc(Request $request)
    {
        if ($request->id == 'undefined') {
            return response()->JSON(-1);
        }
        $correlatives = Correlative::where([
            ['client_id', '=', Auth()->user()->headquarter->client_id],
            ['typevoucher_id', 15],
        ])->first();

        $setCorrelative = (int) $correlatives->correlative + 1;
        $repeat = strlen($correlatives->correlative) - strlen($setCorrelative);
        $final = str_repeat('0',($repeat >=0) ? $repeat : 0).$setCorrelative;

        $correlative = Correlative::find($correlatives->id);
        $correlative->correlative = $final;
        $correlative->save();

        $requirement = Requirement::find($request->rquid);
        $pro = ProviderQuotation::find($request->id);
        $provider = $pro->provider_id;
        $pro->status = 1;
        $pro->update();

        $order = new PurchaseOrder;
        $order->serie = $correlatives->serialnumber;
        $order->correlative = $final;
        $order->delivery_term = $request->plazo;
        $order->condition = $request->condicion;
        $order->delivery = $request->entrega;
        $order->investment = $request->inversion;
        $order->igv = (int) $request->inversion * 0.18;
        $order->status = 1;
        $order->client_id = Auth::user()->headquarter->client_id;
        $order->providerquotation_id = $request->id;
        $order->typevoucher_id = 14;
        $order->provider_id = $provider;
        $order->requirement_id = $requirement->id;
        if($order->save()) {
            $orderId = $order->id;
            $requirementDetails = RequirementDetails::where('requirement_id', $requirement->id)->get();

            foreach ($requirementDetails as $rd) {
                $orderDetail = new PurchaseOrderDetail;
                $orderDetail->order_id = $orderId;
                $orderDetail->product_id = $rd->product_id;
                $orderDetail->quantity = $rd->quantity;
                $orderDetail->observation = $rd->observation;
                $orderDetail->save();
            }

            $upt = ProviderQuotation::find($request->id);
            if ($upt != null) {
                $rq = Requirement::find($upt->requirement_id);
                $rq->status = 5;
                $rq->update();
            }
        }

        return response()->JSON(true);
    }

    public function indexOC()
    {
        return view('logistic.purchase.orders.index');
    }

    public function deleteOC(Request $request)
    {
        $quotation = PurchaseOrder::find($request->get('order_id'));
        $quotation->status = 0;
        return response()->json($quotation->save());
    }

    public function dt_purchaseOrders()
    {
        return datatables()->of(
            Db::table('purchase_orders')
                ->join('providers','purchase_orders.provider_id','=','providers.id')
                ->where('purchase_orders.client_id', Auth::user()->headquarter->client_id)
                ->where('purchase_orders.status', '!=', '0')
                ->where('providers.client_id', Auth::user()->headquarter->client_id)
                ->get([
                    'providers.document as document',
                    'providers.description as pname',
                    'purchase_orders.delivery_term as plazo',
                    'purchase_orders.delivery as entrega',
                    'purchase_orders.condition as condicion',
                    'purchase_orders.serie as serie',
                    'purchase_orders.correlative as correlative',
                    'purchase_orders.investment as total',
                    'purchase_orders.id as id',
                    'purchase_orders.created_at as date',
                ])
        )->toJson();
    }

    public function sendOC(Request $request)
    {
        $oc = $request->oc_id;

        $order = PurchaseOrder::find($oc);
        $clientInfo = Client::find(Auth::user()->headquarter->client_id);
        $pdf = PDF::loadView('logistic.purchase.orders.pdf', compact('order', 'clientInfo'))->setPaper('A4');

        $email = Provider::where('id', $order->provider_id)->where('client_id', Auth::user()->headquarter->client_id)->pluck('email');
        $provider = Provider::where('id', $order->provider_id)->where('client_id', Auth::user()->headquarter->client_id)->first();
        $cotizacion = ProviderQuotation::where('requirement_id', $order->requirement_id)->where('client_id', Auth::user()->headquarter->client_id)->where('status', 1)->where('provider_id',$order->provider_id)->first();

        Mail::to($email)
            ->send(
                new SendOC(
                    $order,
                    $pdf,
                    $clientInfo,
                    $provider,
                    $cotizacion->file
                )
            );
        if(Mail::failures()) {
            return response()->json('No se pudo enviar el Correo');
        } else {
            return response()->JSON(true);
        }
    }

    public function editOC(Request $request, $serie, $correlative)
    {
        $order = PurchaseOrder::where('serie', $serie)->where('correlative', $correlative)->where('client_id', Auth::user()->headquarter->client_id)->first();
        $categories = $this->getCategories();
        $products = $this->getProducts();

        return view('logistic.purchase.orders.edit', compact('order', 'categories', 'products'));
    }

    public function updateOC(Request $request)
    {
        if (count($request->quantity) > 0) {
            $order = PurchaseOrder::find($request->opid);
            $order->delivery_term = $request->delivery_term;
            $order->condition = $request->condition;
            $order->delivery = $request->delivery;
            $order->status = 1;
            $order->igv = $request->investment * 0.18;
            $order->investment = $request->investment;

            if ($order->update()) {
                if ($request->has('dpoid')) {
                    for ($o=0; $o < count($request->dpoid); $o++) {
                        $d = PurchaseOrderDetail::find($request->dpoid[$o]);
                        if ($d != null) {
                            $d->delete();
                        }
                    }
                }
                for ($i=0; $i < count($request->quantity); $i++) {
                    $details = new PurchaseOrderDetail;
                    $details->order_id = $order->id;
                    $details->product_id = $request->product[$i];
                    $details->quantity = $request->quantity[$i];
                    $details->observation = $request->observation[$i];
                    $details->save();
                }
                $r = true;
            } else {
                $r = -1;
            }

            $rpta = array(
                'response'      =>  $r
            );
            echo json_encode($rpta);
        }
    }

    public function getCategories()
    {
        $categories = Category::where('client_id', auth()->user()->headquarter->client_id)->get();
        return  $categories;
    }

    public function getProducts()
    {
        // return Product::where('client_id', auth()->user()->headquarter->client_id)->get();

        return Db::table('products')
                ->leftJoin('operations_type','products.operation_type','=','operations_type.id')
                ->leftJoin('categories','products.category_id','=','categories.id')
                ->leftjoin('clients','products.client_id','=','clients.id')
                ->leftJoin('brands','products.brand_id','=','brands.id')
                ->leftJoin('coins', 'products.coin_id','=','coins.id')
                ->leftjoin('stores', 'products.id', '=', 'stores.product_id')
                ->leftjoin('measures', 'products.measure_id', 'measures.id')
                ->where('products.client_id', auth()->user()->headquarter->client_id)
                ->get([
                    'products.description',
                    'products.id',
                    'stores.stock',
                    'stores.price',
                    'products.operation_type',
                    'products.code',
                    'measures.description as measure'
                ]);
    }
    public function excelOc()
    {
        return Excel::download(new PurchaseOrdersExports, 'ordenes_de_compra.xlsx');
    }


    public function priceListIndex()
    {
        return view('logistic.pricelist.index');
    }

    public function priceListDt()
    {
        return datatables()->of(
            Db::table('price_lists')
                ->where('client_id', '=', Auth::user()->headquarter->client_id)
                ->get([
                    'id',
                    'description',
                    'state'
                ])
        )->toJson();
    }

    public function priceListSave(Request $request)
    {
        if($request->post('price_id')) {
            $price = PriceList::find($request->post('price_id'));
        } else{
            $price = new PriceList;
        }

        $price->description = $request->post('description');
        $price->state = $request->post('status');
        $price->client_id = Auth::user()->headquarter->client_id;

        if($price->save()) {
            echo json_encode(true);
        } else {
            echo response()->json(false);
        }
    }

    public function priceListGet(Request $request)
    {
        $price = PriceList::where('id',$request->get('price_id'))->where('client_id', Auth::user()->headquarter->client_id)->first();
        return response()->json($price);
    }

    public function deletePurchase(Request $request)
    {
        DB::beginTransaction();
        try {
            $purchase = Shopping::with('detail')->find($request->purchase);

            $purchase->detail->each(function($item, $index) use ($purchase) {
                if ($item->type_purchase == 1) {
                    $stock = Store::where('product_id', $item->product_id)->where('warehouse_id', $item->warehouse_id)->first();

                    if($stock == null) {
                        $stock = new Store;
                        $stock->stock = 0;
                        $stock->price = 0.00;
                        $stock->higher_price = 0.00;
                        $stock->maximum_stock = 0.00;
                        $stock->minimum_stock = 0.00;
                        $stock->location = 0.00;
                        $stock->product_id = $item->product_id;
                        $stock->warehouse_id = $item->warehouse_id;
                    } else {
                        $os = $stock->stock;
                        $stock->stock = (float) $os - (float) $item->quantity;
                    }

                    $stock->save();

                    $kardex = new Kardex;
                    $kardex->type_transaction = 'ANULACION DE COMPRA';
                    $kardex->number = "{$item->shopping_serie}-{$item->shopping_correlative}";
                    $kardex->cost = $stock->product->cost;
                    $kardex->output = $item->quantity;
                    $kardex->balance = $stock->stock;
                    $kardex->warehouse_id = $item->warehouse_id;
                    $kardex->client_id = auth()->user()->headquarter->client_id;
                    $kardex->product_id = $item->product_id;
                    $kardex->date_created_at = date('Y-m-d');
                    $kardex->coin_id = $purchase->coin_id;
                    $kardex->exchange_rate = auth()->user()->headquarter->client->exchange_rate_sale;
                    $kardex->save();
                }
            });

            $purchase->status = 9;
            $purchase->save();
            
            DB::commit();

            return response()->json(true);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json(false);
        }
    }

    public function getTotalShoppings(Request $request)
{
    $from = Carbon::createFromFormat('d/m/Y', Str::before($request->dates, ' -'))->format('Y-m-d');
    $to = Carbon::createFromFormat('d/m/Y', Str::after($request->dates, '- '))->format('Y-m-d');

    // Crear la consulta base
    $query = Shopping::query()
        ->where('shoppings.headquarter_id', $this->headquarter)
        ->where('shoppings.type', 1)
        ->whereBetween('shoppings.date', [$from, $to])
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

            if ($request->filter_status != '') {
                $query->where('shoppings.paidout', $request->filter_status);
            }
        });

    // Si hay un filtro en shopping_filter, hacer el join con shopping_details
    if ($request->shopping_filter != '') {
        $query->join('shopping_details', 'shoppings.id', '=', 'shopping_details.shopping_id')
              ->where('shopping_details.type_purchase', $request->shopping_filter);
    }

    // Contar documentos y sumar totales
    $totalDocs = $query->count();
    $totalSoles = $query->where('shoppings.coin_id', 1)->sum('shoppings.total'); 
    $totalDolares = $query->where('shoppings.coin_id', 2)->sum('shoppings.total');

    // Calcular pendientes
    $pendings = PurchaseCredit::query()
        ->where('client_id', auth()->user()->headquarter->client_id)
        ->where('status', 0)
        ->whereBetween('date', [$from, $to])
        ->get();

    $totalPending = $pendings->sum('debt');
    $countPending = $pendings->count();

    return compact('totalDocs', 'totalDolares', 'totalSoles', 'totalPending', 'countPending');
}


}
