<?php

namespace App\Http\Controllers;

use App\ProductPriceLog;
use DB;
use PDF;
use Auth;
use App\Store;
use App\Client;
use App\Kardex;
use App\Product;
use App\Provider;
use App\Shopping;
use App\Inventory;
use App\Warehouse;
use App\Correlative;
use Illuminate\Http\Request;
use App\Exports\InventaryExport;
use Maatwebsite\Excel\Facades\Excel;

class InventoryController extends Controller
{
    public $headquarter;
    public $_ajax;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('status.client');
        $this->middleware('can:ingresos')->only(['newAdmission','storeAdmission']);
        $this->middleware('can:inventario.show')->only(['index', 'dt_inventory']);

        $this->_ajax = new AjaxController();

        $this->middleware(function ($request, $next) {
            $this->headquarter = session()->has('headlocal') == true ? session()->get('headlocal') : Auth::user()->headquarter_id;
            return $next($request);
        });
    }

    public function inventarioReal() {
        $shoppings = Shopping::where('client_id', auth()->user()->headquarter->client_id)->where('status', 0)->get(['shopping_serie', 'id', 'shopping_correlative']);
        $warehouses = Warehouse::where('client_id', auth()->user()->headquarter->client_id)->get();
        $wh = Warehouse::where('client_id', auth()->user()->headquarter->client_id)->where('headquarter_id', $this->headquarter)->first();
        $products = Product::where('client_id', auth()->user()->headquarter->client_id)->where('operation_type', '!=', 2)->get(['id','internalcode', 'description']);
        return view('inventory.real', compact('shoppings', 'warehouses', 'products', 'wh'));
    }

    public function dtInventarioReal() {
        $headquarter = Auth::user()->headquarter->client_id;
        $store = Store::with('product.brand', 'product.category', 'warehouse')->whereHas('warehouse', function ($query) use ($headquarter) {
            $query->where('client_id', $headquarter);
        })->whereHas('product', function($query) {
            $query->where('operation_type', '!=', 2);
        })->get();

        return datatables()->of($store)->toJson();
    }

    public function index()
    {
        $shoppings = Shopping::where('client_id', auth()->user()->headquarter->client_id)->where('status', 0)->get(['shopping_serie', 'id', 'shopping_correlative']);
        $warehouses = Warehouse::where('client_id', auth()->user()->headquarter->client_id)->get();
        $wh = Warehouse::where('client_id', auth()->user()->headquarter->client_id)->where('headquarter_id', $this->headquarter)->first();
        $products = Product::where('client_id', auth()->user()->headquarter->client_id)->where('operation_type', '!=', 2)->get(['id','internalcode', 'description']);

        return view('inventory.index', compact('shoppings', 'warehouses', 'products', 'wh'));
    }

    public function newAdmission($serie, $correlative)
    {
        $shopping = Shopping::where('shopping_serie', $serie)->where('shopping_correlative', $correlative)->first();
        $warehouses = Warehouse::where('client_id', auth()->user()->headquarter->client_id)->get();
        $providers = Provider::where('client_id', auth()->user()->headquarter->client_id)->get();
        $date = date('d-m-Y');

        return view('inventory.newAdmission', compact('shopping', 'warehouses', 'providers', 'date'));
    }

    public function edit($serie,$correlative)
    {
        $providers = Provider::where('client_id', auth()->user()->headquarter->client_id)->get();
        $warehouses = Warehouse::where('client_id', auth()->user()->headquarter->client_id)->get();
        $ingreso = Inventory::where('serie', $serie)->where('correlative', $correlative)->where('client_id', auth()->user()->client_id)->first();

        return view('inventory.edit', compact('ingreso', 'providers','warehouses'));
    }

    public function storeAdmission(Request $request)
    {
        DB::beginTransaction();
        try {
            $correlatives = Correlative::where([
                ['client_id',  auth()->user()->headquarter->client_id],
                ['typevoucher_id', 20]
            ])->first();

            if ($correlatives == null) {
                return response()->JSON(-9);
            }

            for ($i=0; $i < count($request->admission_pid); $i++) { 

                $stock = Store::where('product_id', $request->admission_pid[$i])->where('warehouse_id', $request->warehouse)->first();

                if($stock == null) {
                    $stock = new Store;
                    $stock->stock = $request->quantity_admission[$i];
                    $stock->price = 0.00;
                    $stock->product_id = $request->admission_pid[$i];
                    $stock->warehouse_id = $request->warehouse;
                    $nuevoStock = $request->quantity_admission[$i];
                } else {
                    $os = $stock->stock;
                    $stock->stock = (int) $os + (int) $request->quantity_admission[$i];
                    $nuevoStock = (int) $os + (int) $request->quantity_admission[$i];
                }

                $stock->save();


                /**
                 * Crear un nuevo registro de precios al log
                 */
                $product_price_log = new ProductPriceLog();
                $product_price_log->price           =   $request->unit_price[$i];
                $product_price_log->stock           =   $request->quantity_admission[$i];
                $product_price_log->initial_stock   =   $request->quantity_admission[$i];
                $product_price_log->product_id      =   $request->admission_pid[$i];
                $product_price_log->warehouse_id    =   $request->warehouse;
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

                $existInventory = Inventory::where('client_id', auth()->user()->headquarter->client_id)->where('product_id', $request->admission_pid[$i])
                                            ->where('warehouse_id', $request->warehouse)->where('headquarter_id', $this->headquarter)->get();

                if (!$existInventory->isEmpty()) {
                    foreach ($existInventory as $iv) {
                        $in = Inventory::find($iv->id);
                        $in->status = 0;
                        $in->save();
                    }
                }
    
                $admission = new Inventory;
                $admission->shopping_id = $request->sid;
                $admission->provider_id = $request->provider;
                $admission->guide = $request->guide;
                $admission->warehouse_id = $request->warehouse;
                $admission->client_id = auth()->user()->headquarter->client_id;
                $admission->headquarter_id =  $this->headquarter;
                $admission->admission = date("Y-m-d", strtotime($request->date));
                $admission->serie = $correlative->serialnumber;
                $admission->correlative = $final;
                $admission->place = null;
                $admission->responsable = $request->requested;
                $admission->serial = $request->serie[$i];
                $admission->lot = $request->lot[$i];
                $admission->expiration = date("Y-m-d", strtotime($request->expiration[$i]));
                $admission->warranty = $request->warranty[$i];
                $admission->amount_entered = $request->quantity_admission[$i];
                $admission->observation = $request->observation[$i];
                $admission->product_id = $request->admission_pid[$i];
                $admission->save();

                $kardexBalance = Kardex::where('client_id', auth()->user()->headquarter->client_id)->where('warehouse_id', $request->warehouse)
                                        ->where('product_id', $request->admission_pid[$i])->first();
                if ($kardexBalance != null) {
                    $oldBalance = $kardexBalance->balance;
                    $newBalance = (float) $oldBalance + (float) $request->quantity_admission[$i];
                } else {
                    $newBalance = $request->quantity_admission[$i];
                }

                $producto = Product::find($request->admission_pid[$i]);

                $kardex = new Kardex;
                $kardex->type_transaction = 'Compra';
                $kardex->entry = $request->quantity_admission[$i];
                $kardex->balance = $newBalance;
                $kardex->cost = $producto->cost;
                $kardex->warehouse_id = $request->warehouse;
                $kardex->client_id = auth()->user()->headquarter->client_id;
                $kardex->product_id = $request->admission_pid[$i];
                $kardex->date_created_at = date('Y-m-d');
                $kardex->coin_id = 1;
                $kardex->exchange_rate = auth()->user()->headquarter->client->exchange_rate_sale;
                $kardex->save();

                $shopping = Shopping::find($request->sid);
                $shopping->status = 1;
                $shopping->update();
            }

            DB::commit();

            return response()->json(true);
        } catch (\Exception $e) {
            DB::rollBack();
            $rpta = '';
            switch ($e->getCode()) {
                default:
                    $rpta = $e->getMessage();
                    break;
            }

            return response()->JSON($rpta);
        }
    }

    public function updateAdmission(Request $request)
    {
        $admission = Inventory::find($request->ingid);
        $admission->provider_id = $request->provider;
        $admission->guide = $request->guide;
        $admission->warehouse_id = $request->warehouse;
        $admission->admission = date("Y-m-d", strtotime($request->date));
        $admission->place = $request->place;
        $admission->responsable = $request->requested;
        $admission->serial = $request->serie;
        $admission->lot = $request->lot;
        $admission->expiration = date("Y-m-d", strtotime($request->expiration));
        $admission->warranty = $request->warranty;
        $admission->amount_entered = $request->quantity_admission;
        $admission->observation = $request->observation;
        $admission->save();

        return response()->json(true);
    }

    public function dt_inventory(Request $request)
    {
        $warehouse = Warehouse::where('client_id', auth()->user()->headquarter->client_id)->where('headquarter_id',$this->headquarter)->first();
        return datatables()->of(
            DB::table('inventory')
            ->join('products', 'inventory.product_id', 'products.id')
            ->join('stores', 'stores.product_id', 'products.id')
            ->join('categories','products.category_id','=','categories.id')
            ->join('brands','products.brand_id','=','brands.id')
            ->join('operations_type', 'operations_type.id', 'products.operation_type')
            ->join('warehouses', 'warehouses.id', 'inventory.warehouse_id')
            ->where('inventory.client_id', auth()->user()->headquarter->client_id)
            // ->where('inventory.headquarter_id', $this->headquarter)
            ->where('products.operation_type', '!=', 2)
            ->where('stores.warehouse_id', $warehouse->id)
            ->where(function ($query) use($request) {
                if($request->get('product') != ''){
                    $query->where('inventory.product_id', $request->get('product'));
                }
                if($request->get('warehouse') != ''){
                    $query->where('inventory.warehouse_id', $request->get('warehouse'));
                }
                if($request->get('dateOne') != ''){
                    $query->whereBetween('admission',  [$request->get('dateOne'), $request->get('dateTwo')]);
                }
            })
            // ->distinct('product_id')
            ->get([
                'inventory.admission as date',
                'warehouses.description as warehouse',
                'inventory.warehouse_id as wi',
                'stores.location as place',
                'inventory.status as status',
                'products.internalcode as code',
                'operations_type.code as ot',
                'categories.description as category',
                'brands.description as brand',
                'products.description as product',
                'stores.stock as stock',
                'inventory.id as ii',
                'inventory.product_id as pi',
                'stores.price as price',
                'inventory.serie as serie',
                'inventory.correlative as correlative',
                'inventory.amount_entered as amount',
                'stores.minimum_stock as minimum_stock',
                'stores.maximum_stock as maximum_stock'
            ])
        )->toJson();
    }

    public function generateShoppingPDF($id)
    {
        $shopping = Shopping::find($id);
        $clientInfo = Client::find(Auth::user()->headquarter->client_id);
        $igv = DB::table('taxes')->where('id', '=',1)->first();
        $pdf = PDF::loadView('inventory.pdf', compact('shopping', 'clientInfo', 'igv'))->setPaper('A4');

        return $pdf->download('COMPRA ' . $shopping->serial . ' - ' . $shopping->correlative . '.pdf');
    }

    public function getPDFInventary(Request $request)
    {
        $warehouse = Warehouse::where('client_id', auth()->user()->headquarter->client_id)->where('headquarter_id',$this->headquarter)->first();
        $clientInfo = Client::find(auth()->user()->headquarter->client_id);
        $inventaries = DB::table('inventory')
            ->join('products', 'inventory.product_id', 'products.id')
            ->join('stores', 'stores.product_id', 'products.id')
            ->where('inventory.client_id', auth()->user()->headquarter->client_id)
            ->where('inventory.headquarter_id', $this->headquarter)
            ->where('inventory.warehouse_id', $warehouse->id)
            ->where('stores.warehouse_id', $warehouse->id)
            ->where('inventory.status', 1)
            ->where('products.operation_type', '!=', 2)
            ->distinct('product_id')
            ->get([
                'inventory.admission as date',
                'inventory.place as place',
                'products.status as status',
                'products.internalcode as code',
                'products.cost as cost',
                'products.description as product',
                'stores.stock as stock',
                'inventory.id as ii',
                'inventory.product_id as pi',
                'stores.price as price',
                'inventory.serie as serie',
                'inventory.correlative as correlative'
            ]);

        $date = date('d-m-Y H:m:s');
        
        $pdf = PDF::loadView('inventory.inventaryPDF', compact('inventaries', 'warehouse', 'clientInfo', 'date'))->setPaper('A4');
        return $pdf->download('INVENTARIO ' . date('d-m-Y') . '.pdf');
    }

    public function getEXCELInventary(Request $request)
    {
        $warehouse = Warehouse::where('client_id', auth()->user()->headquarter->client_id)->where('headquarter_id',$this->headquarter)->first();
        $clientInfo = Client::find(auth()->user()->headquarter->client_id);
        $inventaries = DB::table('inventory')
            ->join('products', 'inventory.product_id', 'products.id')
            ->join('stores', 'stores.product_id', 'products.id')
            ->where('inventory.client_id', auth()->user()->headquarter->client_id)
            ->where('inventory.headquarter_id', $this->headquarter)
            ->where('inventory.warehouse_id', $warehouse->id)
            ->where('stores.warehouse_id', $warehouse->id)
            ->where('inventory.status', 1)
            ->where('products.operation_type', '!=', 2)
            ->distinct('product_id')
            ->get([
                'inventory.admission as date',
                'inventory.place as place',
                'products.status as status',
                'products.internalcode as code',
                'products.cost as cost',
                'products.description as product',
                'stores.stock as stock',
                'inventory.id as ii',
                'inventory.product_id as pi',
                'stores.price as price',
                'inventory.serie as serie',
                'inventory.correlative as correlative'
            ]);

        $date = date('d-m-Y');

        return (new InventaryExport($clientInfo, $inventaries))->download('INVENTARIO ' . date('d-m-Y') . '.xlsx');
    }
}
