<?php

namespace App\Http\Controllers\Api;

use App\ClientToken;
use App\Product;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Str;

class ProductsController extends Controller
{
    public function getProducts(Request $request)
    {
        $client = ClientToken::where('token', $request->header('Authorization'))
                                ->select('client_id')
                                ->first();

        $unique_products = DB::table('products')
                                ->select('products.code', DB::raw('GROUP_CONCAT(brands.id) as brand_ids'))
                                ->distinct()
                                ->join('brands', 'products.brand_id', '=', 'brands.id')
                                ->where('products.client_id', $client->client_id)
                                ->where('products.type_product', 1)
                                ->groupBy('products.code')
                                ->get();

        $unique_products = $unique_products->map(function ($product) {
                                                $product->brand_ids = explode(',', $product->brand_ids);
                                                return $product;
                                            })->toArray();

        $data = [];

        for ($i = 0; $i < count($unique_products); $i++) {
            $up = $unique_products[$i];
            $products = Product::query()->with('brand:id,description',
                'product_price_list:product_id,price_list_id,price,id',
                'product_price_list.price_list:id,description', 'category')
                ->where('products.client_id', $client->client_id)
                ->where('products.code', $up->code)
                ->where('products.status', 1)
                ->whereIn('products.brand_id', $up->brand_ids)
                ->get(['code', 'description', 'detail', 'image', 'id', 'brand_id', 'category_id']);

            $contProducts = 0;
            $itemP = 0;

            foreach ($products as $product) {
                if ($itemP == 0) {
                    $data[$i]['internalcode'] = trim($product->code);
                    $data[$i]['description'] = trim($product->description);
                    $data[$i]['detail'] = trim($product->detail);
                    $data[$i]['category'] = $product->category ? $product->category->description : 'SIN CATEGORIA';
                    $data[$i]['image'] = asset("storage/{$product->image}");
                    $data[$i]['prices'] = [];
                    $data[$i]['brands'] = [];
                    foreach ($product->product_price_list as $pl) {
                        $data[$i]['prices'][Str::snake(Str::title($pl->price_list->description))] = $pl->price;
                    }
                }

                $data[$i]['brands'][] = [
                    'brand' => $product->brand->description,
                    'stock' => $product->stockGlobal->sum('stock'),
                    'color' => $this->convertir_color(trim($product->brand->description)),
                ];

                $itemP++;
            }
        }

        return response()->json($data, 200);
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
}
