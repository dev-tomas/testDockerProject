<?php

namespace App\Http\Controllers\Logistic;

use App\Brand;
use App\Category;
use App\Client;
use App\Exports\CatalogExport;
use App\Exports\CatalogSheetExport;
use App\Product;
use PDF;
use DB;
use Excel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Str;

class CatalogController extends Controller
{
    public function generateCatalog($client = null)
    {
        if ($client == null) {
            $client = auth()->user()->headquarter->client;
        } else {
            $client = Client::find($client);
        }

        $categories = Category::where('client_id', $client->id)
                                ->where('description', '!=', 'SIN CATEGORIA')
                                ->get(['description', 'id']);

        $data = [];
        $contCategories = 0;

        foreach ($categories as $category) {
            $data[$contCategories]['category'] = $category->description;
            $unique_products = DB::table('products')
                ->select('products.code', DB::raw('GROUP_CONCAT(brands.id) as brand_ids'))
                ->distinct()
                ->join('brands', 'products.brand_id', '=', 'brands.id')
                ->where('products.client_id', $client->id)
                ->where('products.category_id', $category->id)
                ->where('products.type_product', 1)
                ->groupBy('products.code')
                ->get();

            $unique_products = $unique_products->map(function ($product) {
                $product->brand_ids = explode(',', $product->brand_ids);
                return $product;
            })->toArray();

            $data[$contCategories]['products'] = [];

            for ($i = 0; $i < count($unique_products); $i++) {
                $up = $unique_products[$i];
                $products = Product::query()->with('brand:id,description',
                                                'product_price_list:product_id,price_list_id,price,id',
                                                'product_price_list.price_list:id,description')
                    ->where('products.client_id', $client->id)
                    ->where('products.category_id', $category->id)
                    ->where('products.code', $up->code)
                    ->where('products.status', 1)
                    ->whereIn('products.brand_id', $up->brand_ids)
                    ->get(['code', 'description', 'detail', 'image', 'id', 'brand_id', 'category_id']);

                $contProducts = 0;
                $itemP = 0;

                foreach ($products as $product) {
                    if ($itemP == 0) {
                        $data[$contCategories]['products'][$i]['internalcode'] = trim($product->code);
                        $data[$contCategories]['products'][$i]['description'] = trim($product->description);
                        $data[$contCategories]['products'][$i]['detail'] = trim($product->detail);
                        $data[$contCategories]['products'][$i]['image'] = public_path("storage/{$product->image}");
                        $data[$contCategories]['products'][$i]['prices'] = [];
                        $data[$contCategories]['products'][$i]['brands'] = [];
                        foreach ($product->product_price_list as $pl) {
                            $data[$contCategories]['products'][$i]['prices'][Str::snake(Str::title($pl->price_list->description))] = $pl->price;
                        }
                    }

                    $data[$contCategories]['products'][$i]['brands'][] = [
                        'brand' => $product->brand->description,
                        'stock' => $product->stockGlobal->sum('stock'),
                        'color' => $this->convertir_color(trim($product->brand->description)),
                        'text' => $this->invertColor($this->convertir_color(trim($product->brand->description))),
                    ];

                    $itemP++;
                }

                $data[$contCategories]['products'] = collect($data[$contCategories]['products'])->sortByDesc(function ($item) {
                    return count($item['brands']);
                })->toArray();
            }

            $contCategories++;
        }

        $pdf = PDF::loadView('warehouse.product.catalog-pdf', compact('data', 'client'))->setPaper('A3');
        return $pdf->stream('Stock Actualizado ' . now()->format('d-m-y') . '.pdf');

        return Excel::download(new CatalogExport($data, $client), 'Stock Actualizado ' . now()->format('d-m-y') . '.xlsx');
    }

    public function externalReport($client)
    {
        $client = Client::where('document', $client)->first();

        if ($client == null) {
            abort(404);
        }

        return $this->generateCatalog($client->id);
    }

    public function convertir_color($color_espanol) {
        $colores = array(
            "AZUL"=> "#0000FF",
            "ROJO" => "#FF0000",
            "NEGRO" => "#000000",
            "NARANJA" => "#FFA500",
            "BLANCO" => "#FFFFFF",
            "CELESTE" => "#B2FFFF",
            "MORADO" => "#800080",
            "ROJO" => "#FF0000",
            "ROSADO" =>  "#FFC0CB",
            "VERDE" => "#008000",
            "VERDE C."=> "#00FF7F",
            "VERDE O."=> "#FFA500",
            "GRIS"=> "#808080",
            "PLATEADO"=> "#C0C0C0",
            "AMARILLO"=> "#FFFF00",
            "AMARILLO FOSFORECENTE"=> "#CCFF00",
            "AZUL MARINO"=> "#000080",
            "AZULINO"=> "#ADD8E6",
            "FUCSIA"=> "#FF00FF",
            "LILA"=> "#C8A2C8",
            "MARRON"=> "#964B00",
            "NATURAL"=> "#F0E68C",
            "VERDE PETROLEO"=> "#006666",
        );
        return isset($colores[strtoupper($color_espanol)]) ? $colores[strtoupper($color_espanol)] : "#FFFFFF";
    }

    public function invertColor($color)
    {
        $rgb = $this->hex2rgb($color);

        $form = (float) $rgb['red'] * 0.299  + (float) $rgb['green'] * 0.587 + (float) $rgb['blue'] * 0.114;

        $color = '#000000';

        if ($form > 186) {
            $color = '#000000';
        } else {
            $color = '#ffffff';
        }

        return $color;
    }

    public function hex2rgb( $colour ) {
        if ( $colour[0] == '#' ) {
            $colour = substr( $colour, 1 );
        }
        if ( strlen( $colour ) == 6 ) {
            list( $r, $g, $b ) = array( $colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5] );
        } elseif ( strlen( $colour ) == 3 ) {
            list( $r, $g, $b ) = array( $colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2] );
        } else {
            return false;
        }
        $r = hexdec( $r );
        $g = hexdec( $g );
        $b = hexdec( $b );
        return array( 'red' => $r, 'green' => $g, 'blue' => $b );
    }
}
