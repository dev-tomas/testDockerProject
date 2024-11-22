<?php

namespace App\Http\Controllers\Reports;

use App\Category;
use App\Exports\ReportStockProductByWarehouseExport;
use App\Product;
use App\Warehouse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Str;

class StockWarehouseReportcontroller extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::where('client_id', auth()->user()->headquarter->client_id)
                                ->orderBy('id', 'desc')
                                ->get(['id', 'description']);

        $products = Product::where('type_product', '1')
                            ->where('client_id', auth()->user()->headquarter->client_id)
                            ->where('status', 1)
                            ->get(['id', 'code', 'description']);

        $categories = Category::where('client_id', auth()->user()->headquarter->client_id)->get(['id', 'description']);

        return view('warehouse.reports.stock.index', compact('warehouses', 'products', 'categories'));
    }

    public function generate(Request $request)
    {
        $data = $this->getData($request);

        return response()->json($data);
    }

    public function excel(Request $request)
    {
        $data = $this->getData($request);
        $warehouses = Warehouse::where('client_id', auth()->user()->headquarter->client_id)
            ->orderBy('id', 'desc')
            ->get(['id', 'description']);

        return Excel::download(new ReportStockProductByWarehouseExport($data, $warehouses), 'REPORTE DE STOCK DE PRODUCTOS POR ALMACEN.xlsx');
    }

    public function getData($request)
    {
        $warehouses = Warehouse::where('client_id', auth()->user()->headquarter->client_id)
            ->orderBy('id', 'desc')
            ->get(['id', 'description']);

        $products = Product::with(['ot', 'stockGlobal' => function ($q){
                                $q->orderBy('warehouse_id', 'desc');
                            }])
                            ->where('type_product', '1')
                            ->where('measure_id', 7)
                            ->where('client_id', auth()->user()->headquarter->client_id)
                            ->where('status', 1)
                            ->where(function($query) use ($request) {
                                if ($request->product != '') {
                                    $query->where('id', $request->product);
                                }

                                if ($request->category != '') {
                                    $query->where('category_id', $request->category);
                                }
                            })
                            ->get();

        $data = [];
        $cont = 0;

        foreach ($products as $product) {
            $data[$cont]['product'] = $product->description;
            $data[$cont]['code'] = (string) $product->code;
            $data[$cont]['operation'] = $product->measure ? $product->measure->description : 'UNIDADES';
            foreach ($warehouses as $warehouse) {
                $stock = $product->stockGlobal->where('warehouse_id', $warehouse->id)->first();
                $data[$cont][str_replace(' ', '', strtolower($this->eliminar_tildes($warehouse->description)))] = $stock == null ? 0 : $stock->stock;
            }
            $data[$cont]['total'] = $product->stockGlobal->sum('stock');

            $cont++;
        }

        return $data;
    }

    public function eliminar_tildes($cadena)
    {
        //Ahora reemplazamos las letras
        $cadena = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $cadena
        );

        $cadena = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $cadena );

        $cadena = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $cadena );

        $cadena = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
            $cadena );

        $cadena = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $cadena );

        $cadena = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'),
            array('n', 'N', 'c', 'C'),
            $cadena
        );

        return $cadena;
    }
}
