<?php

namespace App\Http\Controllers\Logistic;

use App\Store;
use App\Kardex;
use App\Product;
use App\Warehouse;
use App\ProductKit;
use App\ProductKitDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class KitsController extends Controller
{
	public $headquarter;

	public function __construct()
	{
        $this->middleware('auth');
		$this->middleware('status.client');
		$this->middleware(function($request, $next) {
			$this->headquarter = session()->has('headlocal') == true ? session()->get('headlocal') : Auth::user()->headquarter_id;

			return $next($request);
		});
	}

    public function index()
    {
        $products = Product::where('client_id', auth()->user()->headquarter->client_id)
                                    ->where('is_kit', 1)
                                    ->get(['internalcode', 'id', 'description']);

        $productsNotKits = Product::where('client_id', auth()->user()->headquarter->client_id)
                                    ->where('is_kit', 0)
                                    ->get(['internalcode', 'id', 'description']);
        $currentHeadquarter = $this->headquarter;
        
        return view('warehouse.kits.index', compact('products', 'currentHeadquarter', 'productsNotKits'));
    }

    public function dt(Request $request)
    {
        $kits = ProductKit::where('client_id', auth()->user()->headquarter->client_id)
                    ->where(function ($query) use ($request) {
                        if ($request->search != "") {
                            $query->where('name', 'like', "%{$request->search}%");
                        }
                    })->get();

        $data = array();
        $cont = 0;

        foreach ($kits as $kit) {
            $data[$cont]['id'] = $kit->id;
            $data[$cont]['description'] = $kit->name;
            $data[$cont]['product_sale'] = "{$kit->productSale->internalcode} - {$kit->productSale->description}";
            $data[$cont]['count_items'] = $kit->countItems();

            $cont++;
        }

        return datatables()->of($data)->toJson();
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->kit_id != '') {
                $kit = ProductKit::find($request->kit_id);
            } else {
                $kit = new ProductKit;
            }
            $kit->name = $request->description;
            $kit->product_id = $request->product_sale;
            $kit->client_id = auth()->user()->headquarter->client_id;
            $kit->save();

            if ($request->kit_id != '') {
                $d = ProductKitDetail::where('product_kit_id', $kit->id)->get()->each(function ($item, $index) {
                    $item->delete();
                });
            }
            
            for ($i=0; $i < count($request->product_detail); $i++) { 
                $detail = new ProductKitDetail;
                $detail->quantity = $request->quantity[$i];
                $detail->product_id = $request->product_detail[$i];
                $detail->product_kit_id = $kit->id;
                $detail->save();
            }

            
            DB::commit();
            return response()->json(true);
        } catch (\Exception $e) {

            DB::rollback();
            return response()->json(false);
        }
    }

    public function prepare(Request $request)
    {
        $kit = ProductKit::with('productSale', 'details')->find($request->kit);
        $warehouse = Warehouse::where('headquarter_id', $this->headquarter)->first();

        $data = array();
        $cont = 0;

        $data['id'] = $kit->id;
        $data['product_sale'] = "{$kit->productSale->internalcode} - {$kit->productSale->description}";
        $data['product_id'] = $kit->product_id;
        $data['description'] = $kit->name;

        foreach ($kit->details as $detail) {
            $data['details'][$cont]['quantity'] = $detail->quantity;
            $data['details'][$cont]['product'] = "{$detail->product->internalcode} - {$detail->product->description}";
            $data['details'][$cont]['product_id'] = $detail->product_id;
            
            $cont++;
        }

        return response()->json($data);
    }

    public function generate(Request $request)
    {
        DB::beginTransaction();
        try {
            $warehouse = Warehouse::where('headquarter_id', $this->headquarter)->first();
            $details = ProductKitDetail::with('product')->where('product_kit_id', $request->kit)->get();
            $kit = ProductKit::find($request->kit);
            $quantity = $request->quantity_generate;

            foreach ($details as $detail) {
                $store = Store::where('product_id', $detail->product_id)->where('warehouse_id', $warehouse->id)->first();
                $quantityNeeded = (float) $quantity * (float) $detail->quantity;
                $newStock = (float) $store->stock - (float) $quantityNeeded;
                $store->stock = $newStock;
                $store->save();

                $kardex = new Kardex;
                $kardex->type_transaction = 'DESCUENTO PARA KIT';
                $kardex->number = "KIT - {$kit->name}";
                $kardex->from = "KIT - {$kit->name}";
                $kardex->output = $quantityNeeded;
                $kardex->balance = $newStock;
                $kardex->warehouse_id = $warehouse->id;
                $kardex->client_id = auth()->user()->headquarter->client_id;
                $kardex->product_id = $detail->product_id;
                $kardex->cost = $detail->product->cost;
                $kardex->save();
            }

            $store = Store::where('product_id', $kit->product_id)->where('warehouse_id', $warehouse->id)->first();
            $oldStock = $store->stock;
            $quantityGenerated = (float) $store->stock + (float) $quantity;
            $store->stock = $quantityGenerated;
            $store->save();

            $kardex = new Kardex;
            $kardex->type_transaction = 'KIT';
            $kardex->number = "KIT - {$kit->name}";
            $kardex->from = "KIT - {$kit->name}";
            $kardex->entry = $quantity;
            $kardex->balance = (float) $oldStock + (float) $quantityGenerated;
            $kardex->warehouse_id = $warehouse->id;
            $kardex->client_id = auth()->user()->headquarter->client_id;
            $kardex->product_id = $kit->product_id;
            $kardex->cost = $kit->productSale->cost;
            $kardex->save();
            
            DB::commit();
            return response()->json(true);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(false);
        }
    }

    public function getItems(Request $request)
    {
        $items = ProductKit::with('details.product.product_price_list.price_list', 'details.product.stock', 'details.product.tax')
                            ->where('product_id', $request->product)
                            ->first();

        return response()->json($items->details);
    }
}
