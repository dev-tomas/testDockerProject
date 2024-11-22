<?php

namespace App\Http\Controllers;

use App\Coin;
use App\Taxe;
use Doctrine\DBAL\Schema\Schema;
use Response;
use App\Brand;
use App\Store;
use App\Client;
use App\Kardex;
use App\Measure;
use App\Product;
use App\Category;
use App\Inventory;
use App\PriceList;
use App\Warehouse;
use App\SaleDetail;
use App\Correlative;
use App\CostsCenter;
use App\HeadQuarter;
// use http\Env\Response;
use App\OperationType;
use App\Classification;
use App\QuotationDetail;
use App\ProductPriceList;
use Illuminate\Http\Request;
use App\Exports\ProductsExport;
use App\Imports\ProductsImport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\AjaxController;
use Illuminate\Database\Eloquent\Builder;

class WarehouseController extends Controller
{
    public $_ajax;
    public $headquarter;
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('status.client');
        $this->middleware('can:pservicios.show')->only(['productList','dt_products']);
        $this->middleware('can:pservicios.edit')->only(['getProduct']);
        $this->middleware('can:pservicios.delete')->only(['deleteProduct','deleteProducts']);
        $this->middleware('can:pservicios.disable')->only(['updateStatus','updateStatusProducts']);
        $this->middleware('can:categorias.show')->only(['categories','dt_categories']);
        $this->middleware('can:categorias.edit')->only(['getCategory']);
        $this->middleware('can:categorias.delete')->only(['categories','dt_categories']);
        $this->middleware('can:almacenes.show')->only(['warehouseList','dt_warehouses']);

        $this->_ajax = new AjaxController();

        $this->middleware(function ($request, $next) {
            $this->headquarter = session()->has('headlocal') == true ? session()->get('headlocal') : Auth::user()->headquarter_id;
            return $next($request);
        });
    }

    public function index() {}

    /**
     * Warehouses
     */
    public function warehouseList()
    {
        return view('warehouse.warehouse.list');
    }

    public function dt_warehouses()
    {
        return datatables()->of(
            Db::table('warehouses')
                ->where('client_id',auth()->user()->headquarter->client_id)
                ->where('headquarter_id', $this->headquarter)
                ->get([
                    'code',
                    'description',
                    'address',
                    'responsable',
                    'id',
                ])
        )->toJson();
    }

    public function saveWarehouse(Request $request)
    {
        if($request->post('warehouse_id')) {
            $warehouse = Warehouse::find($request->post('warehouse_id'));
        } else{
            $warehouse = new Warehouse;
        }

        $warehouse->code = $request->post('code');
        $warehouse->description = $request->post('description');
        $warehouse->address = $request->post('address');
        $warehouse->status = 1;
        $warehouse->responsable = $request->post('responsable');
        $warehouse->headquarter_id = $this->headquarter;
        $warehouse->client_id = Auth::user()->headquarter->client_id;

        if($warehouse->save()) {
            echo json_encode(true);
        } else {
            echo json_encode(false);
        }
    }

    public function getWareHouse(Request $request)
    {
        echo json_encode(
            DB::table('warehouses')->where('id','=', $request->get('warehouse_id'))->first()
        );
    }


    /**
     * Products
     */
    public function productList()
    {
        $clientInfo = Client::find(Auth::user()->headquarter->client_id);
        $data = array(
            'measures'          => $this->_ajax->getMeasures(),
            'categories'        => $this->_ajax->getCategories(),
            'brands'            => $this->_ajax->getBrands(),
            'products_sunat'    => $this->_ajax->getProductsSunat(),
            'price_lists'       => PriceList::whereNull('client_id')->get(),
            'coins'             => Coin::all(),
            'operations_type'   => OperationType::all(),
            'clientInfo'        => $clientInfo,
            'warehouses'        => Warehouse::where([
                ['headquarter_id', $this->headquarter]
            ])->get(),
            'classifications'   =>  Classification::all(),
            'taxes'             => Taxe::where('client_id', auth()->user()->headquarter->client_id)->get(),
            'costsCenters'       => CostsCenter::where('client_id', auth()->user()->headquarter->client_id)->get()
        );

        //dd($data);
        return view('warehouse.product.list')->with($data);
    }

    public function dt_products(Request $request) {
        $mainWarehouse = Warehouse::where('headquarter_id', $this->headquarter)->first();
        $search = $request->get('search2');
        $products = Store::with('product.operation_type', 'product.category', 'product.brand', 'product.coin_product', 'warehouse', 'product.product_price_list.price_list')
            ->whereHas('product', function ($query) use ($search) {
                if($search != null && $search != '') {
                    $query->where('description', 'like', '%' . $search. '%')
                            ->orWhere('code', 'like', '%' . $search. '%');
                }
                $query->where('type_product', 1);
            })->where('warehouse_id', $mainWarehouse->id)->get();

        return datatables()->of($products)->toJson();
    }

    public function deleteProduct(Request $request)
    {

        /**
         * Delete product
         */
        /*$sale = SaleDetail::where('product_id', $request->get('product_id'))->count();
        $quotation = QuotationDetail::where('product_id', $request->get('product_id'))->count();
        if($sale > 0 || $quotation > 0){
            return response()->json(-1);
        }*/

        /*$store = Store::where('product_id', $request->get('product_id'));
        $product = Product::find($request->get('product_id'));
        $store->delete();*/

        $product = Product::find($request->get('product_id'));
        $product->status = 0;

        return response()->json($product->save());
    }

    public function deleteProducts(Request $request)
    {
        /**
         * Delete product
         */
        if(count($request->post('check_s')) > 0) {
            for($x = 0; $x < count($request->post('check_s')); $x++) {
                $sale = SaleDetail::where('product_id', $request->post('check_s')[$x])->count();
                $quotation = QuotationDetail::where('product_id', $request->post('check_s')[$x])->count();
                if($sale > 0 || $quotation > 0){
                    return response()->json(-1);
                }
                $store = Store::where('product_id', $request->post('check_s')[$x]);
                $product = Product::find($request->post('check_s')[$x]);
                $store->delete();
                $product->delete();
            }
        } else {
            $sale = SaleDetail::where('product_id', $request->post('product_id'))->count();
            $quotation = QuotationDetail::where('product_id', $request->post('product_id'))->count();
            if($sale > 0 || $quotation > 0){
                return response()->json(-1);
            }

            $store = Store::where('product_id', $request->post('product_id'));
            $product = Product::find($request->post('product_id'));
            $store->delete();
            $product->delete();
        }

        return response()->json(true);
    }

    public function getProduct(Request $request)
    {
        $mainWarehouse = Warehouse::where('headquarter_id', $this->headquarter)->first();
        $mainWarehouseId = $mainWarehouse->id;
        
        $products = Product::where('id',$request->get('product_id'))->with(['product_price_list.price_list','stock' => function ($query) use ($mainWarehouseId) {
            $query->where('warehouse_id', $mainWarehouseId);
        }])->first();

        return response()->json($products);
    }

    public function saveProduct(Request $request)
    {
        DB::beginTransaction();
        try {
            $warehouse = $request->post('warehouse') ? $request->post('warehouse') : Warehouse::where('headquarter_id', '=', $this->headquarter)->first()->id;
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

            $image = $request->file('imageProduct');
            $url = 'products/default.jpg';
            if($image !== null) {
                $image = Storage::disk('public')->put('products', $request->file('imageProduct'), 'public');
                $url = str_replace('/storage/', '', Storage::disk('local')->url($image));
            }

            if($request->post('product_id') != '') {
                $product = Product::find($request->post('product_id'));
                $product->description = $request->post('description');
                $product->code = $request->post('code');
                $product->internalcode = $request->post('internalcode');
                $product->cost = $request->post('cost');
                $product->utility = $request->post('utility');
                $product->status = 1; //<-- Debe actualizarse por formulario
                $product->measure_id = $request->measure;
                $product->category_id = $request->post('category');
                $product->client_id = Auth::user()->headquarter->client_id;
                $product->brand_id = $request->post('brand');
                $product->coin_id = $request->post('coin');
                $product->sunat_code = $request->post('sunat_code');
                $product->operation_type = $request->post('type');
                $product->operation_type_purchase = $request->typePurchase;
                $product->quantity_purchase = $request->quantityGenCompra;
                $product->quantity_unit_purchase = $request->quantityUnitCompra;
                $product->type_product = $request->product_type;
                $product->is_kit = $request->filled('is_kit');
                if($image !== null) {
                    $product->image = $url;
                }

                $product->classification_id =   $request->post('classification');
                $product->equivalence_code =   $request->post('equivalence_code');
                $product->external_code =   $request->post('external_code');
                $product->detail =   $request->post('detailProduct');
                $product->tax_id = $request->tax;

                $product->initial_stock = $request->initial_stock;
                $product->initial_date = date('Y-m-d', strtotime($request->intial_date));

                $product->type_igv_id = $request->post('igv_type');
                $product->priceIncludeRC = 0;

                if($request->post('imagePath') !== null && $request->post('imagePath') !== undefined) {
                    if($request->post('imagePath') != $product->image) {
                        $product->image     =   $url;
                        $img = str_replace(env('AWS_URL'), '', $product->image);
                        if($img[1] != 'products/default.jpg') {
                            Storage::disk('local')->delete($img[1]);
                        }
                    }
                }

                if($request->post('exonerated')) {
                    $product->exonerated = $request->post('exonerated');
                }
                $product->save();

                if ($request->product_type == 1) {
                    $admission = Inventory::where('product_id', $product->id)
                        ->where('warehouse_id', $warehouse) #
                        ->where('client_id', auth()->user()->headquarter->client_id)
                        ->where('headquarter_id', $this->headquarter)->first();
                    if ($admission == null) {
                        $admission = new Inventory;
                        $admission->product_id = $product->id;
                    }
                    $admission->warehouse_id = $warehouse;
                    $admission->client_id = auth()->user()->headquarter->client_id;
                    $admission->headquarter_id = $this->headquarter;
                    $admission->admission = date("Y-m-d");
                    $admission->serie = $correlative->serialnumber;
                    $admission->correlative = $final;
                    $admission->place = $request->location;
                    $admission->responsable = auth()->user()->name;
                    $admission->expiration = date("Y-m-d");
                    $admission->amount_entered = $request->post('quantity');
                    $admission->observation = 'Producto Modificado';
                    $admission->save();
                }

                /**
                 * Agregar la lista de Precios del producto
                 */
                if ($request->post('product_price_list')) {
                    for($x = 0; $x < count($request->post('product_price_list')); $x++) {
                        if($request->post('product_price_list')[$x] != '') {
                            $priceList = ProductPriceList::where([
                                ['price_list_id', $request->post('product_price_list')[$x]],
                                ['product_id', $request->post('product_id')]
                            ])->first();
                        } else {
                            $priceList = new ProductPriceList();
                        }

                        if ($priceList == '' || $priceList == null || $priceList == 'undefined') {
                            $priceList = new ProductPriceList();
                        }

                        $priceList->price_list_id       =   $request->post('product_price_list')[$x];
                        $priceList->utility_percentage  =   $request->post('pricePercentage')[$x];
                        $priceList->price               =   $request->post('priceListValue')[$x];
                        $priceList->product_id          =   $request->post('product_id');
                        $priceList->save();
                    }
                } else {}
            } else {
                $product = new Product;
                $product->description = $request->post('description');
                $product->code = $request->post('code');
                $product->internalcode = $request->post('internalcode');
                $product->cost = $request->post('cost');
                $product->utility = $request->post('utility');
                $product->status = 1; //<-- Debe actualizarse por formulario
                $product->measure_id = $request->measure;
                $product->category_id = $request->post('category');
                $product->client_id = Auth::user()->headquarter->client_id;
                $product->brand_id = $request->post('brand');
                $product->coin_id = $request->post('coin');
                $product->sunat_code = $request->post('sunat_code');
                $product->operation_type = $request->post('type');
                $product->operation_type_purchase = $request->typePurchase;
                $product->quantity_purchase = $request->quantityGenCompra;
                $product->quantity_unit_purchase = $request->quantityUnitCompra;
                $product->type_product = $request->product_type;
                $product->is_kit = $request->filled('is_kit');
                $product->classification_id =   $request->post('classification');
                $product->equivalence_code =   $request->post('equivalence_code');
                $product->external_code =   $request->post('external_code');
                $product->detail =   $request->post('detailProduct');
                $product->image     =   $url;
                $product->tax_id = $request->tax;

                $product->initial_stock = $request->initial_stock;
                $product->initial_date = date('Y-m-d', strtotime($request->intial_date));

                $product->type_igv_id = $request->post('igv_type');
                $product->priceIncludeRC = 0;

                if($request->post('exonerated')) {
                    $product->exonerated = $request->post('exonerated');
                }
                $product->save();

                $admission = new Inventory;
                $admission->warehouse_id = $warehouse;
                $admission->client_id = auth()->user()->headquarter->client_id;
                $admission->headquarter_id = $this->headquarter;
                $admission->admission = date("Y-m-d");
                $admission->serie = $correlative->serialnumber;
                $admission->correlative = $final;
                $admission->place = $request->location;
                $admission->responsable = auth()->user()->name;
                $admission->expiration = date("Y-m-d");
                $admission->amount_entered = $request->post('quantity');
                $admission->observation = 'Producto Nuevo';
                $admission->product_id = $product->id;
                $admission->save();

                if ($request->post('type') != 2) {
                    $kardex = new Kardex;
                    $kardex->type_transaction = 'Stock Inicial';
                    $kardex->entry = $request->post('quantity');
                    $kardex->balance = $request->post('quantity');
                    $kardex->cost = $request->post('cost');
                    $kardex->warehouse_id = $warehouse;
                    $kardex->cost = $product->cost;
                    $kardex->client_id = auth()->user()->headquarter->client_id;
                    $kardex->product_id = $product->id;
                    $kardex->coin_id = 1;
                    $kardex->exchange_rate = auth()->user()->headquarter->client->exchange_rate_sale;
                    $kardex->save();
                }

                /**
                 * Agregar la lista de Precios del producto
                 */
                if ($request->post('product_price_list')) {
                    for($x = 0; $x < count($request->post('product_price_list')); $x++) {
                        $priceList = new ProductPriceList();
                        $priceList->price_list_id       =   $request->post('product_price_list')[$x];
                        $priceList->price               =   (float)$request->post('priceListValue')[$x];
                        $priceList->utility_percentage  =   (float)$request->post('pricePercentage')[$x];
                        $priceList->product_id          =   $product->id;
                        $priceList->save();
                    }
                }
            }

            if ($request->post('price') == null ) {
                $price = 0;
            } else {
                $price = $request->post('price');
            }

            $store = Store::where('product_id', $request->product_id)->where('warehouse_id', $warehouse)->first();
            if ($store == null) {
                $store = new Store;
                $store->stock           = $request->post('quantity');
                $store->product_id      = $product->id;
                $store->warehouse_id    = $warehouse;
                $store->price           = $price;
                $store->higher_price    = $request->post('higher_price');
                $store->maximum_stock   = $request->post('maximum_stock');
                $store->minimum_stock   = $request->post('minimum_stock');
                $store->location        = $request->post('location');
            } else {
                $store->stock           = $request->post('quantity');
                $store->price = $price;
                $store->higher_price    = $request->post('higher_price');
                $store->maximum_stock   = $request->post('maximum_stock');
                $store->minimum_stock   = $request->post('minimum_stock');
                $store->location        = $request->post('location');
            }

            $store->save();

            DB::commit();

            return response()->json(true);
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }
    }

    public function updateStatus(Request $request){
        /**
         * Update status product
         */
        $product = Product::find($request->post('product_id'));
        $product->status = $request->post('status_change');
        return response()->json($product->save());
    }

    public function updateStatusProducts(Request $request){
        /**
         * Update status products
         */
        if(count($request->post('check_s')) > 0){
            for($x = 0; $x < count($request->post('check_s')); $x++) {
                $product = Product::find($request->post('check_s')[$x]);
                $product->status = $request->post('status_change');
                $product->save();
            }
        }else{
            $product = Product::find($request->post('product_id'));
            $product->status = $request->post('status_change');
        }

        return response()->json(true);
    }

    public function export()
    {
        return Excel::download(new ProductsExport, 'Productos['. date('d-m-Y') .'].xlsx');
    }

    public function exportCustomersTemplate()
    {
        $file= public_path(). "/templates/Plantilla_Clientes.xlsx";

        $headers = array(
            'Content-Type: application/xlsx',
        );
        return Response::download($file, 'Plantilla Clientes.xlsx', $headers);
        // return Response::download($file, $headers);
    }

    /**
     * Categories
     */
    public function categories()
    {
        return view('warehouse.category.list');
    }

    public function dt_categories()
    {
        return datatables()->of(
            Db::table('categories')
                ->where('client_id', '=', Auth::user()->headquarter->client_id)
                ->get([
                    'id',
                    'description',
                    'status'
                ])
        )->toJson();
    }

    public function saveClassification(Request $request) {
        if($request->post('classification_id')) {
            $classification = Classification::find($request->post('classification_id'));
        } else{
            $classification = new Classification();
        }

        $classification->description = $request->post('description');

        if($classification->save()) {
            echo json_encode(true);
        } else {
            echo response()->json(false);
        }
    }

    public function saveCategory(Request $request)
    {
        if($request->post('category_id')) {
            $category = Category::find($request->post('category_id'));
        } else{
            $category = new Category;
        }

        $category->description = $request->post('description');
        if($request->post('status')) {
            $category->status = $request->post('status');
        } else {
            $category->status = 1;
        }
        $category->client_id = Auth::user()->headquarter->client_id;

        if($category->save()) {
            echo json_encode(true);
        } else {
            echo response()->json(false);
        }
    }

    public function deleteCategory(Request $request)
    {
        $category = Category::find($request->get('category_id'));
        $category->delete();

        return response()->json($category->delete());
    }

    public function getCategory(Request $request)
    {
        $category = Category::where('id',$request->get('category_id'))->where('client_id', Auth::user()->headquarter->client_id)->first();
        return response()->json($category);
    }

    public function getAllCategories(){
        return Category::where('client_id', Auth::user()->headquarter->client_id)->get();
    }

    public function getAllClassifications() {
        return Classification::all();
    }

    public function importProducts(Request $request)
    {
        try {
            $customers = Excel::import(new ProductsImport(), $request->file('file'));
            toastr()->success('Se importaron correctamente los productos.');
        
        } catch (\Exeption $e) {
            toastr()->error('Ocurrio un error');
        }
        return back();
    }
    public function exportProductsTemplate()
    {
        $file= public_path(). "/templates/Plantilla_Productos.xlsx";

        $headers = array(
            'Content-Type: application/xlsx',
        );
        return Response::download($file, 'Plantilla Productos.xlsx', $headers);
        // return Response::download($file, $headers);
    }

    /**
     * Eliminar precio de lista
     * @param Request $request
     */
    public function deleteProductPriceList(Request $request) {
        $product_price_list = ProductPriceList::find($request->post('id'));
        if ($product_price_list) {
            return response()->json($product_price_list->delete());
        } else {

        }
    }
}
