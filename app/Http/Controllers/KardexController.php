<?php

namespace App\Http\Controllers;

use PDF;
use Str;
use Auth;
use App\Store;
use App\Client;
use App\Kardex;
use App\Product;
use App\Inventory;
use App\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Exports\KardexExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class KardexController extends Controller
{
    public $headquarter;
    public function __contruct()
    {
        $this->middleware('auth');
        $this->middleware('status.client');
        $this->middleware('can:kardex');

        $this->middleware(function ($request, $next) {
            $this->headquarter = session()->has('headlocal') == true ? session()->get('headlocal') : Auth::user()->headquarter_id;
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        // dd($request->product);
        $p = $request->product;
        $w = $request->warehouse;

        $warehouses = Warehouse::where('client_id', auth()->user()->headquarter->client_id)->get(['id', 'description']);
        $products = Product::where('client_id', auth()->user()->headquarter->client_id)
            ->where([
                ['operation_type', '!=', 2],
                ['type_product', 1]
            ])->get(['id', 'description']);

        return view('warehouse.kardex.index', compact('warehouses', 'products', 'p', 'w'));
    }

    public function generate(Request $request)
    {
        $kardexs = $this->getDataKardex($request);

        return response()->json($kardexs);
    }

    public function getDataKardex($request)
    {
        $from = Carbon::createFromFormat('d/m/Y', Str::before($request->dates, ' -'))->format('Y-m-d');
        $to = Carbon::createFromFormat('d/m/Y', Str::after($request->dates, '- '))->format('Y-m-d');

        $kardexs = Kardex::where('product_id', $request->product)
                    ->where('warehouse_id', $request->warehouse)
                    ->where('client_id', auth()->user()->headquarter->client_id)
                    ->whereDate('created_at', '>=', $from)
                    ->whereDate('created_at', '<=', $to)
                    ->orderBy('created_at', 'asc')
                    ->get();

        $cont = 0;
        $data = [];

        foreach ($kardexs as $kardex) {
            $data[$cont]['id'] = $cont + 1;
            $data[$cont]['date'] = date('d/m/Y', strtotime($kardex->created_at));
            $data[$cont]['type_transaction'] = Str::upper($kardex->type_transaction);
            $data[$cont]['description'] = $kardex->description != null ? $kardex->description : '-';
            $data[$cont]['number'] = $kardex->number != null ? $kardex->number : '-';
            if ($kardex->number != null) {
                $data[$cont]['document_serie'] =  preg_replace('/\s+/', '',Str::before($kardex->number, '-'));
                $data[$cont]['document_correlative'] =  preg_replace('/\s+/', '',Str::after($kardex->number, '-'));
            } else {
                $data[$cont]['document_serie'] = '-';
                $data[$cont]['document_correlative'] =  '-';
            }

            if (Str::upper($kardex->type_transaction) == "VENTA" || Str::upper($kardex->type_transaction) == "COMPRA" || 
                Str::upper($kardex->type_transaction) == "ANULACION DE COMPRA" || 
                Str::upper($kardex->type_transaction) == "ANULACIÓN DE BOLETA" || 
                Str::upper($kardex->type_transaction) == "ANULACION DE FACTURA") {
                    if (substr($kardex->number, 0, 1) == "F") {
                        $data[$cont]['type_document'] = '01';
                    } else if (substr($kardex->number, 0, 1) == "B") {
                        $data[$cont]['type_document'] = '03';
                    } else {
                        $data[$cont]['type_document'] = '00';
                    }
            } else  {
                $data[$cont]['type_document'] = '00';
            }
            if (Str::upper($kardex->type_transaction) == "STOCK INICIAL") {
                $data[$cont]['operation'] = '16';
            } else if (Str::upper($kardex->type_transaction) == "VENTA" || 
                Str::upper($kardex->type_transaction) == "ANULACION DE BOLETA" || 
                Str::upper($kardex->type_transaction) == "ANULACION DE FACTURA") {
                $data[$cont]['operation'] = '01';
            } else if (Str::upper($kardex->type_transaction) == "COMPRA" || 
                Str::upper($kardex->type_transaction) == "ANULACION DE COMPRA") {
                $data[$cont]['operation'] = '02';
            } else  {
                $data[$cont]['operation'] = '99';
            }

            $cost = (float) $kardex->cost;

            if ($kardex->coin_id != 1 && (float) $kardex->exchange_rate > 0) {
                $cost = $kardex->cost * $kardex->exchange_rate;
            }

            $totalEntry = 0;
            if ($kardex->entry != null) {
                $totalEntry = (float) $kardex->entry * $cost;
                $data[$cont]['cost_entry'] = number_format($cost, 2, '.', '');
                $data[$cont]['entry'] = number_format($kardex->entry, 2, '.', '');
                $data[$cont]['entry_cost'] = number_format($totalEntry, 2,'.','');

                $data[$cont]['cost_output'] = '0.00';
                $data[$cont]['output'] = '0.00';
                $data[$cont]['output_cost'] = '0.00';
            }

            $totalOutput = 0;
            if ($kardex->output != null) {
                $totalOutput = (float) abs($kardex->output) * $cost;
                $data[$cont]['cost_output'] = number_format($cost, 2, '.', '');
                $data[$cont]['output'] = number_format(abs($kardex->output), 2, '.', '');
                $data[$cont]['output_cost'] = number_format($totalOutput, 2,'.','');

                $data[$cont]['cost_entry'] = '0.00';
                $data[$cont]['entry'] = '0.00';
                $data[$cont]['entry_cost'] = '0.00';
            }

            if ($cont < 1) {
                $balanceTotal = $totalEntry - $totalOutput;
                $balancetQuantity = (float) $kardex->entry - (float) abs($kardex->output);
                if ($balancetQuantity > 0) {
                    $balanceCost = $balanceTotal / $balancetQuantity;
                } else {
                    $balanceCost = 0;
                }
            } else {
                $balanceTotal = (float) $data[$cont - 1]['balance_cost'] + (float) $totalEntry - (float) $totalOutput;
                $balancetQuantity = $data[$cont - 1]['balance'] + (float) $kardex->entry - (float) abs($kardex->output);
                if ($balancetQuantity > 0) {
                    $balanceCost = $balanceTotal / $balancetQuantity;
                } else {
                    $balanceCost = 0;
                }
            }

            $data[$cont]['cost_balance'] = number_format($balanceCost, 2, '.', '');
            $data[$cont]['balance'] = number_format($balancetQuantity, 2, '.', '');
            $data[$cont]['balance_cost'] = number_format($balanceTotal, 2,'.','');

            $cont++;
        }

        return $data;
    }

    public function generateExcel(Request $request)
    {
        $kardexs = $this->getDataKardex($request);

        $product = Product::find($request->product); 
        $warehouse = Warehouse::with('headquarter')->find($request->warehouse);

        return (new KardexExport($kardexs, $product, $warehouse, 1))->download('Kardex.xlsx');
    }

    public function generatePDF(Request $request)
    {
        $from = Carbon::createFromFormat('d/m/Y', Str::before($request->dates, ' -'))->format('Y-m-d');
        $to = Carbon::createFromFormat('d/m/Y', Str::after($request->dates, '- '))->format('Y-m-d');

        $product = Product::find($request->product);
        $warehouse = Warehouse::find($request->warehouse);
        $clientInfo = Client::find(Auth::user()->headquarter->client_id);

        $kardexs = Kardex::where('client_id', auth()->user()->headquarter->client_id)->where('product_id', $request->product)
                            ->where('warehouse_id', $request->warehouse)
                            ->whereBetween('created_at', [$from, $to])
                            ->get();

        $pdf = PDF::loadView('warehouse.kardex.pdf', compact('kardexs', 'product', 'warehouse', 'clientInfo', 'from', 'to'))->setPaper('A4');
        return $pdf->download('KARDEX ' . $request->dates . '.pdf');
    }

    public function addMovement(Request $request)
    {
        DB::beginTransaction();
        try {
            $stock = Store::where('product_id', $request->movement_product)->where('warehouse_id', $request->movement_warehouse)->first();

            if ($stock == null) {
                return response()->json(-1);
            }

            if ($request->type == 1) {
                $type = "Ventas por consumo";
                $output = (float) $request->quantity * -1;
                $entry = null;
                $balance = (float) $stock->stock - (float) $request->quantity;
            } else {
                $type = "Ajuste por Inventario";
                $output = null;
                $entry = (float) $request->quantity;
                $balance = (float) $stock->stock + (float) $request->quantity;
            }

            $stock->stock = $balance;
            $stock->save();

            $inventory = Inventory::where('product_id', $request->movement_product)
                ->where('warehouse_id', $request->movement_warehouse)
                ->where('client_id', auth()->user()->headquarter->client_id)->where('headquarter_id', $this->headquarter)->first();
            if ($inventory != null) {
                $inventory->amount_entered = $balance;
                $inventory->update();
            }

            $kardex = new Kardex;
            $kardex->number = '-';
            $kardex->type_transaction = $type;
            $kardex->description = $request->description;
            $kardex->output = $output;
            $kardex->balance = $balance;
            $kardex->cost = $stock->product->cost;
            $kardex->entry = $entry;
            $kardex->warehouse_id = $request->movement_warehouse;
            $kardex->client_id = auth()->user()->headquarter->client_id;
            $kardex->product_id = $request->movement_product;
            $kardex->coin_id = 1;
            $kardex->exchange_rate = auth()->user()->headquarter->client->exchange_rate_sale;
            $kardex->save();

            DB::commit();
        
            return response()->json(true);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(false);
        }
    }

    public function indexValorize(Request $request)
    {
        // dd($request->product);
        $p = $request->product;
        $w = $request->warehouse;

        $warehouses = Warehouse::where('client_id', auth()->user()->headquarter->client_id)->get(['id', 'description']);
        $products = Product::where('client_id', auth()->user()->headquarter->client_id)
            ->where([
                ['operation_type', '!=', 2],
                ['type_product', 1]
            ])->get(['id', 'description']);

        return view('accountancy.kardex.index', compact('warehouses', 'products', 'p', 'w'));
    }

    public function generateValorize(Request $request)
    {
        $kardexs = $this->getDataKardex($request);

        return response()->json($kardexs);
    }

    public function generateValorizeExcel(Request $request)
    {
        $kardexs = $this->getDataKardex($request);

        $product = Product::find($request->product); 
        $warehouse = Warehouse::with('headquarter')->find($request->warehouse);

        return (new KardexExport($kardexs, $product, $warehouse, 2))->download('Kardex Valorizado.xlsx');
    }

    public function generateValorizePDF(Request $request)
    {
        $from = Carbon::createFromFormat('d/m/Y', Str::before($request->dates, ' -'))->format('Y-m-d');
        $to = Carbon::createFromFormat('d/m/Y', Str::after($request->dates, '- '))->format('Y-m-d');

        $product = Product::find($request->product);
        $warehouse = Warehouse::find($request->warehouse);
        $clientInfo = Client::find(Auth::user()->headquarter->client_id);

        $kardexs = Kardex::where('client_id', auth()->user()->headquarter->client_id)->where('product_id', $request->product)
                            ->where('warehouse_id', $request->warehouse)
                            ->whereBetween('created_at', [$from, $to])
                            ->get();

        $pdf = PDF::loadView('accountancy.kardex.pdf', compact('kardexs', 'product', 'warehouse', 'clientInfo', 'from', 'to'))->setPaper('A4');
        return $pdf->download('KARDEX ' . $request->dates . '.pdf');
    }

    public function indexFisic(Request $request)
    {
        // dd($request->product);
        $p = $request->product;
        $w = $request->warehouse;

        $warehouses = Warehouse::where('client_id', auth()->user()->headquarter->client_id)->get(['id', 'description']);
        $products = Product::where('client_id', auth()->user()->headquarter->client_id)
            ->where([
                ['operation_type', '!=', 2],
                ['type_product', 1]
            ])->get(['id', 'description']);

        return view('accountancy.kardex-fisic.index', compact('warehouses', 'products', 'p', 'w'));
    }

    public function generateFisic(Request $request)
    {
        $kardexs = $this->getDataKardex($request);

        return response()->json($kardexs);
    }

    public function generateFisicExcel(Request $request)
    {
        $kardexs = $this->getDataKardex($request);

        $product = Product::find($request->product); 
        $warehouse = Warehouse::with('headquarter')->find($request->warehouse);

        //return (new KardexExport($kardexs, $product, $warehouse, 3))->download('Kardex Fisico.xlsx');
        return Excel::download(new KardexExport($kardexs, $product, $warehouse, 3), 'Kardex Fisico.xlsx');
    }

    public function generateFisicPDF(Request $request)
    {
        $from = Carbon::createFromFormat('d/m/Y', Str::before($request->dates, ' -'))->format('Y-m-d');
        $to = Carbon::createFromFormat('d/m/Y', Str::after($request->dates, '- '))->format('Y-m-d');

        $product = Product::find($request->product);
        $warehouse = Warehouse::find($request->warehouse);
        $clientInfo = Client::find(Auth::user()->headquarter->client_id);

        $kardexs = Kardex::where('client_id', auth()->user()->headquarter->client_id)->where('product_id', $request->product)
                            ->where('warehouse_id', $request->warehouse)
                            ->whereBetween('created_at', [$from, $to])
                            ->get();

        $pdf = PDF::loadView('accountancy.kardex-fisic.pdf', compact('kardexs', 'product', 'warehouse', 'clientInfo', 'from', 'to'))->setPaper('A4');
        return $pdf->download('KARDEX FISICO' . $request->dates . '.pdf');
    }
}
