<?php

namespace App\Http\Controllers;

use App\Client;
use App\TransferDetail;
use DB;
use App\Transfer;
use App\Correlative;
use App\Store;
use App\Kardex;
use Illuminate\Http\Request;
use App\Warehouse;
use App\Product;
use App\Inventory;
use Auth;
use PDF;

class TransferController extends Controller
{
    public $headquarter;
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('status.client');
        $this->middleware('can:transfers.show')->only(['index', 'dt_transfer']);
        $this->middleware('can:transfers.create')->only(['store']);
        $this->middleware('can:transfers.delete')->only(['disabled']);
        $this->middleware(function ($request, $next) {
            $this->headquarter = session()->has('headlocal') == true ? session()->get('headlocal') : Auth::user()->headquarter_id;
            return $next($request);
        });
    }

    public function index()
    {
        $currentWarehouse = Warehouse::where('client_id', auth()->user()->headquarter->client_id)
                                        ->where('headquarter_id', $this->headquarter)
                                        ->first();
        $warehouses = Warehouse::where('client_id', auth()->user()->headquarter->client_id)
                                ->get();
        $products = Product::whereHas('stock', function($q) use ($currentWarehouse) {
                                $q->where('warehouse_id', $currentWarehouse->id)->where('stock', '>', 0);
                            })
                            ->where('status', 1)
                            ->where('operation_type', '!=', 2)
                            ->where('type_product', 1)
                            ->where('client_id', auth()->user()->client_id)
                            ->get();

        return view('transfer.index', compact('warehouses', 'products', 'currentWarehouse'));
    }

    public function store(Request $request)
    {
        // dd($request);
        DB::beginTransaction();

        try {
            $correlatives = Correlative::where([
                ['client_id', auth()->user()->headquarter->client_id],
                ['typevoucher_id', 21]
            ])->first();

            $setCorrelative = (int) $correlatives->correlative + 1;
            $repeat = strlen($correlatives->correlative) - strlen($setCorrelative);
            $final = str_repeat('0',($repeat >=0) ? $repeat : 0).$setCorrelative;

            $correlative = Correlative::find($correlatives->id);
            $correlative->correlative = $final;
            $correlative->save();

            $tranfer = new Transfer;
            $tranfer->serie = $correlatives->serialnumber;
            $tranfer->correlative = $final;
            $tranfer->responasble = auth()->user()->name;
            $tranfer->motive = $request->transMotivo;
            $tranfer->client_id = auth()->user()->headquarter->client_id;
            $tranfer->warehouse_origin = $request->warehouseOrigin;
            $tranfer->warehouse_destination = $request->warehouseDestination;
            $tranfer->save();

            for ($i=0; $i < count($request->product); $i++) {
                $detail = new TransferDetail;
                $detail->product_id = $request->product[$i];
                $detail->quantity = $request->transer[$i];
                $detail->transfer_id = $tranfer->id;
                $detail->save();

                $store = Store::where('product_id', $request->product[$i])->where('warehouse_id', $request->warehouseOrigin)->first();
                if ($store == null) {
                    $store = new Store;
                    $viejoStock = 0;
                    $store->price = 0.00;
                    $store->product_id = $request->product[$i];
                    $store->warehouse_id = $request->warehouseOrigin;
                } else {
                    $viejoStock = (float) $store->stock;
                }
                $store->stock =  $viejoStock - (float) $request->transer[$i];
                $finalStock = $viejoStock - (float) $request->transer[$i];
                $store->save();

                // ACTUALIZA EL INVENTARIO DEL ALMACEN DE ORIGIN

                $oldInventary = Inventory::where('client_id', auth()->user()->headquarter->client_id)
                    ->where('product_id', $request->product[$i])
                    ->where('warehouse_id', $request->warehouseOrigin)
                    ->first();


                if ($oldInventary == null) {
                    $admission = new Inventory;
                    $admission->warehouse_id = $request->warehouseOrigin;
                    $admission->client_id = auth()->user()->headquarter->client_id;
                    $admission->headquarter_id = $this->headquarter;
                    $admission->admission = date("Y-m-d");
                    $admission->serie = $correlative->serialnumber;
                    $admission->correlative = $final;
                    $admission->place = null;
                    $admission->responsable = auth()->user()->name;
                    $admission->serial = null;
                    $admission->lot = null;
                    $admission->expiration = null;
                    $admission->warranty = null;
                    $admission->amount_entered = $finalStock;
                    $admission->observation = 'Transferencia';
                    $admission->product_id = $request->product[$i];
                    $admission->save();
                } else {
                    $oldInventaryStock = $oldInventary->amount_entered;
                    $oldInventary->amount_entered = (int) $oldInventaryStock - (int) $request->transer[$i];
                    $oldInventary->update();
                }

                $producto = Product::where('client_id', auth()->user()->headquarter->client_id)->find($request->product[$i]);

                $kardex = new Kardex;
                $kardex->type_transaction = 'TRASLADO ENTRE ALMACENES';
                $kardex->number = $tranfer->serie . '-' . $tranfer->correlative;
                $kardex->output = (int) $request->transer[$i] * -1;
                $kardex->balance = $finalStock;
                $kardex->warehouse_id = $request->warehouseOrigin;
                $kardex->client_id = auth()->user()->headquarter->client_id;
                $kardex->product_id = $request->product[$i];
                $kardex->cost = $producto->cost;
                $kardex->save();


                // ACTUALIZA EN EL ALMACEN DE LLEGADA

                $destinationWarehouse = Warehouse::find($tranfer->warehouse_destination);

                $existInventory = Inventory::where('product_id', $request->product[$i])
                                            ->where('warehouse_id', $tranfer->warehouse_destination)
                                            ->where('client_id', auth()->user()->headquarter->client_id)
                                            ->where('headquarter_id', $destinationWarehouse->headquarter_id)
                                            ->first();

                $newStore = Store::where('product_id', $request->product[$i])
                                ->where('warehouse_id', $tranfer->warehouse_destination)
                                ->first();
                if ($newStore == null) {
                    $newStore = new Store;
                    $newStore->price = 0.00;
                    $newStore->stock = $request->transer[$i];
                    $stock_new = $request->transer[$i];
                } else {
                    $oldStock = $newStore->stock;
                    $nuevoStock = (float) $request->transer[$i] + (float) $oldStock;
                    $newStore->stock = $nuevoStock;
                    $stock_new = $nuevoStock;
                }
                $newStore->product_id = $request->product[$i];
                $newStore->warehouse_id = $tranfer->warehouse_destination;
                $newStore->save();

                if ($existInventory == null) {
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

                    $existInventory = new Inventory;
                    $existInventory->warehouse_id = $tranfer->warehouse_destination;
                    $existInventory->client_id = auth()->user()->headquarter->client_id;
                    $existInventory->headquarter_id = $destinationWarehouse->headquarter_id;
                    $existInventory->admission = date("Y-m-d");
                    $existInventory->serie = $correlative->serialnumber;
                    $existInventory->correlative = $final;
                    $existInventory->place = null;
                    $existInventory->responsable = $destinationWarehouse->responsable;
                    $existInventory->serial = null;
                    $existInventory->lot = null;
                    $existInventory->expiration = null;
                    $existInventory->warranty = null;
                    $existInventory->amount_entered = $request->transer[$i];
                    $existInventory->observation = 'Transferencia';
                    $existInventory->product_id = $request->product[$i];
                    $existInventory->save();
                } else {
                    $existInventory->admission = date("Y-m-d");
                    $existInventory->amount_entered =  $request->transer[$i];
                    $existInventory->observation = 'Transferencia';
                    $existInventory->save();
                }

                $producto = Product::where('client_id', auth()->user()->headquarter->client_id)
                                    ->find($request->product[$i]);


                $kardex = new Kardex;
                $kardex->type_transaction = 'TRASLADO ENTRE ALMACENES';
                $kardex->number = $tranfer->serie . '-' . $tranfer->correlative;
                $kardex->entry = (int)  $request->transer[$i];
                $kardex->balance = $stock_new;
                $kardex->warehouse_id = $tranfer->warehouse_destination;
                $kardex->client_id = auth()->user()->headquarter->client_id;
                $kardex->product_id = $request->product[$i];
                $kardex->cost = $producto->cost;
                $kardex->save();
            }

            DB::commit();

            return response()->json(true);
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return response()->json($e->getMessage());
        }
    }

    public function dt_transfer()
    {
        return datatables()->of(
            DB::table('transfers')
            ->leftJoin('warehouses', 'transfers.warehouse_origin', 'warehouses.id')
            ->leftJoin('warehouses as wd', 'transfers.warehouse_destination', 'wd.id')
            ->where('transfers.client_id', auth()->user()->headquarter->client_id)
            ->orderBy('correlative', 'desc')
            ->get([
                'transfers.serie as serie',
                'transfers.correlative as correlative',
                'warehouses.description as wod',
                'wd.description as wdd',
                'transfers.id as id',
                'transfers.created_at as date',
                'transfers.status as status',
            ])
        )->toJson();
    }

    public function getWarehousesTransfer(Request $request)
    {
        $warehouses = Warehouse::where('client_id', auth()->user()->headquarter->client_id)
                                ->where('id', '!=', $request->warehouse_origin)
                                ->get(['id', 'description']);

        return response()->json($warehouses);
    }

    public function getProductsByWarehouse(Request $request)
    {
         $products = Product::where('status', 1)
                            ->where('client_id', auth()->user()->headquarter->client_id)
                            ->get(['id', 'code', 'description']);

         $data = [];
         $cont = 0;
         foreach ($products as $product) {
             if ($product->stockByWarehouse($request->warehouse)) {
                 $data[$cont]['id'] = $product->id;
                 $data[$cont]['code'] = $product->code;
                 $data[$cont]['description'] = $product->description;
                 $data[$cont]['stock'] = $product->stockByWarehouse($request->warehouse) ? $product->stockByWarehouse($request->warehouse)->stock : 0;

                 $cont++;
             }
         }

        return response()->json($data);
    }

    public function showPdf($id)
    {
        $transfer = Transfer::with('warehouseDestination', 'warehouseOrigin', 'detail')->where('client_id', auth()->user()->headquarter->client_id)->find($id);

        $clientInfo = Client::find(auth()->user()->headquarter->client_id);

        $pdf = PDF::loadView('transfer.pdf', compact('clientInfo', 'transfer'))->setPaper('A4');

        return $pdf->stream('TRANSFERENCIA.pdf');
    }

    public function disabled(Request $request)
    {
        DB::beginTransaction();
        try {

            $transfer = Transfer::find($request->transfer);
            $transfer->status = 9;
            $transfer->save();

            foreach ($transfer->detail as $detail) {
                $quantity = $detail->quantity;

                // DESCONTAR STOCK DEL ALMACEN DE DESTINO
                $store = Store::where('product_id', $detail->product_id)->where('warehouse_id', $transfer->warehouse_destination)->first();
                $store->stock = $store->stock - $quantity;
                $store->save();

                $kardex = new Kardex;
                $kardex->type_transaction = 'ANULACION DE TRANSFERENCIA';
                $kardex->number = $transfer->serie . '-' . $transfer->correlative;
                $kardex->output = $quantity * -1;
                $kardex->balance = $store->stock;
                $kardex->warehouse_id = $transfer->warehouse_destination;
                $kardex->client_id = auth()->user()->headquarter->client_id;
                $kardex->product_id = $detail->product_id;
                $kardex->cost = $detail->product->cost;
                $kardex->save();

                // AUMENTAR STOCK AL ALMACEN DE ORIGEN
                $storeOrigin = Store::where('product_id', $detail->product_id)->where('warehouse_id', $transfer->warehouse_origin)->first();
                $storeOrigin->stock = $storeOrigin->stock + $quantity;
                $storeOrigin->save();

                $kardexOrigin = new Kardex;
                $kardexOrigin->type_transaction = 'ANULACION DE TRANSFERENCIA';
                $kardexOrigin->number = $transfer->serie . '-' . $transfer->correlative;
                $kardexOrigin->entry = $quantity;
                $kardexOrigin->balance = $storeOrigin->stock;
                $kardexOrigin->warehouse_id = $transfer->warehouse_origin;
                $kardexOrigin->client_id = auth()->user()->headquarter->client_id;
                $kardexOrigin->product_id = $detail->product_id;
                $kardexOrigin->cost = $detail->product->cost;
                $kardexOrigin->save();
            }

            DB::commit();

            return response()->json(true);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(false);
        }
    }

    public function getDataForDuplicate(Request $request)
    {
        $transfer = Transfer::find($request->transfer);

        $data = [];
        $data['warehouse_origin'] = $transfer->warehouse_origin;
        $data['warehouse_destination'] = $transfer->warehouse_destination;

        $cont = 0;
        foreach ($transfer->detail as $item) {
            if ($item->product->stockByWarehouse($transfer->warehouse_origin)) {
                $data['detail'][$cont]['product'] = $item->product_id;
                $data['detail'][$cont]['quantity'] = $item->quantity;
                $data['detail'][$cont]['stock'] = $item->product->stockByWarehouse($transfer->warehouse_origin)->stock;

                $cont++;
            }
        }

        return response()->json($data);
    }
}
