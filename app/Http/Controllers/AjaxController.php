<?php
namespace App\Http\Controllers;

use App\BankAccountType;
use App\Brand;
use App\Category;
use App\Coin;
use App\Measure;
use App\Product;
use App\ProductPriceList;
use App\Sale;
use App\TypeDocument;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use MongoDB\BSON\Type;
use Peru\Http\ContextClient;
use Peru\Jne\{Dni, DniParser};
use Peru\Sunat\UserValidator;
use Peru\Sunat\{HtmlParser, Ruc, RucParser};
use Illuminate\Http\Request;
use App\Customer;
use App\TypeVoucher;
use Illuminate\Support\Facades\Auth;
use App\Warehouse;

class AjaxController extends Controller
{
    public $headquarter;
    public function __construct()
    {
        $this->middleware('status.client');
        $this->middleware(function ($request, $next) {
            $this->headquarter = session()->has('headlocal') == true ? session()->get('headlocal') : \Illuminate\Support\Facades\Auth::user()->headquarter_id;
            return $next($request);
        });
    }

    public function searchProductByCodeBar(Request $request) {
        $product = Product::with('operation_type', 'product_price_list.price_list')->where('code', $request->get('bar_code'))->first();
        return response()->json($product);
    }

    public function getSales($type = null)
    {
        $sales = Sale::where([
            ['headquarter_id', $this->headquarter],
            ['typevoucher_id', $type]
        ])->get();
        return response()->json($sales);
    }

    public function getIgv()
    {
        return DB::table('taxes')->where('id', '=',1)->first();
    }

    public function getProductsSunat()
    {
        $array = array();
        return $array;
    }

    public function searchProduct(Request $request) {
        $headquarter = session()->has('headlocal') == true ? session()->get('headlocal') : Auth::user()->headquarter_id;
        $mainWarehouse = Warehouse::where('headquarter_id', $headquarter)->first();
        $mainWarehouseId = $mainWarehouse->id;
        $products =  Db::table('products')
            ->leftJoin('operations_type','products.operation_type','=','operations_type.id')
            ->leftJoin('categories','products.category_id','=','categories.id')
            ->leftjoin('clients','products.client_id','=','clients.id')
            ->leftJoin('brands','products.brand_id','=','brands.id')
            ->leftJoin('coins', 'products.coin_id','=','coins.id')
            ->leftjoin('stores', 'products.id', '=', 'stores.product_id')
            ->leftjoin('taxes', 'products.tax_id', '=', 'taxes.id')
            ->where('products.client_id', auth()->user()->headquarter->client_id)
            ->where('stores.warehouse_id', $mainWarehouseId)
            ->where('products.description', 'like', '%' . $request->get('search') . '%')
            ->where('products.status', 1)
            ->get([
                'products.description',
                'products.image',
                'products.id',
                'stores.stock',
                'stores.price',
                'products.operation_type',
                'products.exonerated',
                'products.internalcode',
                'products.category_id as category',
                'products.brand_id as brand',
                'products.type_igv_id',
                'taxes.value as tax',
                'taxes.base as taxbase',
            ]);
        return $products;
    }

    public function getProducts($type = 1)
    {
        $headquarter = session()->has('headlocal') == true ? session()->get('headlocal') : Auth::user()->headquarter_id;
        $mainWarehouse = Warehouse::where('headquarter_id', $headquarter)->first();
        $mainWarehouseId = $mainWarehouse->id;
        $products =  Db::table('products')
            ->leftJoin('operations_type','products.operation_type','=','operations_type.id')
            ->leftJoin('categories','products.category_id','=','categories.id')
            ->leftjoin('clients','products.client_id','=','clients.id')
            ->leftJoin('brands','products.brand_id','=','brands.id')
            ->leftJoin('coins', 'products.coin_id','=','coins.id')
            ->leftjoin('stores', 'products.id', '=', 'stores.product_id')
            ->leftjoin('taxes', 'products.tax_id', '=', 'taxes.id')
            ->where('products.client_id', auth()->user()->headquarter->client_id)
            ->where('stores.warehouse_id', $mainWarehouseId)
            ->where('type_product',1)
            ->where('products.status', 1)
            ->where(function ($query) use ($type) {
                if ($type == 2) {
                    $query->where('products.operation_type', '!=', 2);
                }
            })
            ->get([
                'products.description',
                'products.image',
                'products.id',
                'stores.stock',
                'stores.price',
                'products.operation_type',
                'products.exonerated',
                'products.internalcode',
                'products.category_id as category',
                'products.brand_id as brand',
                'products.type_product',
                'products.type_igv_id',
                'taxes.value as tax',
                'taxes.base as taxbase',
                'products.priceIncludeRC as priceIncludeRC',
                'products.is_kit as is_kit'
            ]);
        return $products;
    }

    public function getCoins()
    {
        $coins = Coin::all();
        return $coins;
    }

    public function getBankAccountTypes()
    {
        return response()->json(BankAccountType::all());
    }

    public static function getTypeDocuments()
    {
        $typeDocuments = TypeDocument::all();
        return $typeDocuments;
    }

    public function getTypeVouchers()
    {
        $typeVoucher = TypeVoucher::where('visible', 1)->get();
        return $typeVoucher;
    }

    public function getCustomers($type = null)
    {
        if($type == 1) {
            return Customer::where([
                ['client_id', auth()->user()->headquarter->client_id],
                ['typedocument_id', 4]
            ])->get();
        } else if ($type == 2) {
            return Customer::where([
                ['client_id', auth()->user()->headquarter->client_id],
                ['typedocument_id', '!=', 4]
            ])->get();
        } else {
            return Customer::where([
                ['client_id', auth()->user()->headquarter->client_id],
            ])->get();
        }
    }

    public function checkKeySun($document)
    {
        $cs = new UserValidator(new ContextClient());
        $valid = $cs->valid($document);
        if ($valid) {
            echo 'Válido';
        } else {
            echo 'Inválido';
        }
    }

    public function getComparePersonInfo($document)
    {
        if(strlen($document) !== 8) {
            return false;
        }
        $client = new Client();

        $data = ['dni' => $document];

        try {
            $res = $client->request('POST', 'https://integraciones.gyomanager.com/api/consults/v1/dni', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'EDx6ah3NJN4D3Nj9ZntwyxwmO1gd6TMYZBd4uOsX',
                    'Token' => '444b31aa-15a8-4f0d-8f2e-86442c0b5098',
                ],
                'json' =>  $data,
            ]);

            return json_decode($res->getBody(), true);
        } catch (RequestException $e) {
            $res = Message::toString($e->getRequest());
            if ($e->hasResponse()) {
                $res = Message::toString($e->getResponse());
            }

            return json_decode($res, true);
        }
    }

    public function getCompareCompanyInfo($document)
    {
        if(strlen($document) !== 11) {
            return false;
        }
        $client = new Client();

        $data = ['ruc' => $document];

        try {
            $res = $client->request('POST', 'https://integraciones.gyomanager.com/api/consults/v1/ruc', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'EDx6ah3NJN4D3Nj9ZntwyxwmO1gd6TMYZBd4uOsX',
                    'Token' => '444b31aa-15a8-4f0d-8f2e-86442c0b5098',
                ],
                'json' =>  $data,
            ]);

            return json_decode($res->getBody(), true);
        } catch (RequestException $e) {
            $res = Message::toString($e->getRequest());
            if ($e->hasResponse()) {
                $res = Message::toString($e->getResponse());
            }

            return json_decode($res, true);
        }
    }

    public function getPersonInfo($document)
    {
        $data = $this->getComparePersonInfo($document);

        if (! isset($data['success'])) {
            return response()->json(false);
        } else {
            $res['nombres'] = $data['data']['nombre'];
            $res['apellidoPaterno'] = '';
            $res['apellidoMaterno'] = '';
            return $res;
        }
    }

    public function getCompanyInfo($document)
    {
        $data = $this->getCompareCompanyInfo($document);

        if (! isset($data['success'])) {
            return response()->json(false);
        } else {
            $res['razonSocial'] = $data['nombre_o_razon_social'];
            $res['direccion'] = $data['direccion'];
            $res['departamento'] = $data['departamento'];
            $res['provincia'] = $data['provincia'];
            $res['distrito'] = $data['distrito'];
            return $res;
        }
    }

    /**
     * Correlatives
     */
    public function getCorrelative($correlative_id)
    {
        $headquarter = session()->has('headlocal') == true ? session()->get('headlocal') : Auth::user()->headquarter_id;
        return DB::table('correlatives')
            ->where(function($query) use ($correlative_id ) {
                if(isset($correlative_id)) {
                    $query->where('id', '=', $correlative_id);
                }
            })
            ->where('headquarter_id', $headquarter)
            ->first();
    }

    public function getCategories()
    {
        return Category::where('client_id', auth()->user()->headquarter->client_id)->get();
    }

    public function getMeasures()
    {
        return Measure::all();
    }

    public function getBrands()
    {
        return Brand::where('client_id', auth()->user()->headquarter->client_id)->get();
    }

    public function getProductPriceList(Request $request) {
        return response()->json(ProductPriceList::with('price_list')->where('product_id', $request->id)->get());
    }
}
