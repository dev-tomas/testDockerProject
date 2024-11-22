<?php

namespace App\Http\Controllers;

use App\Warehouse;
use App\CostsCenter;
use App\Category;
use App\Product;
use App\Requirement;
use App\RequirementDetails;
use App\Http\Controllers\AjaxController;
use Illuminate\Http\Request;
use DB;
use App\Correlative;
use Auth;
use PDF;
use App\Client;
use App\Exports\RequirementsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\OperationType;
use App\Brand;
use App\Coin;

class RequirementController extends Controller
{
    public $headquarter;
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('status.client');
        $this->middleware('can:requirement.show')->only(['index','dt_requirements']);
        $this->middleware('can:requirement.create')->only(['create','store']);
        $this->middleware('can:requirement.export')->only(['exportRequirement']);
        $this->middleware('can:requirement.edit')->only(['edit', 'update']);

        $this->middleware(function ($request, $next) {
            $this->headquarter = session()->has('headlocal') == true ? session()->get('headlocal') : Auth::user()->headquarter_id;
            return $next($request);
        });
    }

    public function index()
    {
        return view('logistic.requirements.index');  
    }

    public function dt_requirements()
    {
        return datatables()->of(
            Db::table('requirements')
            ->leftjoin('warehouses','requirements.warehouse_id','=','warehouses.id')
            ->leftjoin('costs_center','requirements.centercost_id','=','costs_center.id')
            ->where('requirements.headquarter_id', $this->headquarter)
            ->get([
                'requirements.serie as serie',
                'requirements.correlative as correlative',
                'warehouses.description as description',
                'requirements.requested as requested',
                'requirements.type_requirement as type',
                'costs_center.center as center',
                'requirements.status as status',
                'requirements.total as total',
                'requirements.created_at as create',
                'requirements.status as status',
                'requirements.id as id'
            ])
        )->toJson();
    }

    public function create()
    {
        $warehouses = Warehouse::where('client_id', auth()->user()->client_id)->get();
        $costsCenter = CostsCenter::where('client_id', auth()->user()->headquarter->client_id)->get();
        $operations_type = OperationType::get();
        $categories = $this->getCategories();
        $products = $this->getProducts();
        $clientInfo = Client::find(Auth::user()->headquarter->client_id);

        $data = array(
            'operations_type'   => OperationType::all(),
            'brands'            => Brand::where('client_id', auth()->user()->headquarter->client_id)->get(),
            'clientInfo'        => $clientInfo,
            'coins' => Coin::all(),
        );

        // dd($products);

        return view('logistic.requirements.addRequirements', compact('warehouses','costsCenter','categories','products'))->with($data);
    }

    public function store(Request $request)
    {
        if (count($request->quantity) > 0) {
            $correlatives = Correlative::where([
                ['client_id', '=', Auth()->user()->headquarter->client_id],
                ['typevoucher_id', 16],
            ])->first();

            $setCorrelative = (int) $correlatives->correlative + 1;
            $repeat = strlen($correlatives->correlative) - strlen($setCorrelative);
            $final = str_repeat('0',($repeat >=0) ? $repeat : 0).$setCorrelative;

            $correlative = Correlative::findOrFail($correlatives->id);
            $correlative->correlative = $final;
            $correlative->save();

            $requirement = new Requirement;
            $requirement->serie = $correlatives->serialnumber;
            $requirement->correlative = $final;
            $requirement->warehouse_id = $request->warehouse;
            $requirement->requested = $request->requested;
            $requirement->type_requirement = $request->typerequirement;
            $requirement->centercost_id = $request->centercost;
            $requirement->status = 0;
            
            // $total = 0;
            // for ($u=0; $u < count($request->productprice); $u++) { 
            //     $total = ((float) $request->productprice[$u] * (float) $request->quantity[$u] ) + $total;
            // }
            
            $requirement->total = 0;
            $requirement->headquarter_id = $this->headquarter;
            $requirement->client_id = Auth::user()->headquarter->client_id;

            if ($requirement->save()) {
                for ($i=0; $i < count($request->product); $i++) { 
                    $details = new RequirementDetails;
                    $details->requirement_id = $requirement->id;
                    $details->category_id = $request->category[$i];
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
            return response()->json($rpta);
        }
    }

    public function edit($serie, $correlative)
    {
        $requirement = Requirement::where('serie', $serie)->where('correlative', $correlative)->where('headquarter_id',$this->headquarter)->first();
        $warehouses = Warehouse::where('headquarter_id', $this->headquarter)->get();
        $costsCenter = CostsCenter::where('client_id', auth()->user()->headquarter->client_id)->get();
        $categories = $this->getCategories();
        $products = $this->getProducts();

        return view('logistic.requirements.edit', compact('requirement', 'warehouses', 'costsCenter', 'categories', 'products'));
    }

    public function update(Request $request)
    {
        if (count($request->quantity) > 0) {
            if ($request->status == null) {
                $r = -2;
            } else {
                $requirement = Requirement::find($request->rqid);
                $requirement->warehouse_id = $request->warehouse;
                $requirement->authorized = $request->authorized;
                $requirement->type_requirement = $request->typerequirement;
                $requirement->centercost_id = $request->centercost;
                $requirement->status = $request->status;
                
                $total = 0;
                for ($u=0; $u < count($request->productprice); $u++) { 
                    $total = ((float) $request->productprice[$u] * (float) $request->quantity[$u] ) + $total;
                }

                $requirement->total = $total;

                if ($requirement->update()) {
                    for ($o=0; $o < count($request->drqid); $o++) { 
                        $d = RequirementDetails::find($request->drqid[$o]);
                        $d->delete();
                    }
                    for ($i=0; $i < count($request->productprice); $i++) { 
                        $details = new RequirementDetails;
                        $details->requirement_id = $requirement->id;
                        $details->category_id = $request->category[$i];
                        $details->product_id = $request->product[$i];
                        $details->quantity = $request->quantity[$i];
                        $details->observation = $request->observation[$i];
                        $details->save();
                    }
                    $r = true;
                } else {
                    $r = -1;
                }
            }
            

            $rpta = array(
                'response'      =>  $r
            );
            return response()->json($rpta);
        }
    }

    public function getCategories()
    {
        $categories = Category::where('client_id', auth()->user()->headquarter->client_id)->get();
        return  $categories;
    }

    public function getProducts()
    {
        $mainWarehouse = Warehouse::where('headquarter_id', $this->headquarter)->first();
        $mainWarehouseId = $mainWarehouse->id;
       
        return Db::table('products')
                ->leftJoin('operations_type','products.operation_type','=','operations_type.id')
                ->leftJoin('categories','products.category_id','=','categories.id')
                ->leftjoin('clients','products.client_id','=','clients.id')
                ->leftJoin('brands','products.brand_id','=','brands.id')
                ->leftJoin('coins', 'products.coin_id','=','coins.id')
                ->leftjoin('stores', 'products.id', '=', 'stores.product_id')
                ->leftjoin('measures', 'products.measure_id', 'measures.id')
                ->where('products.client_id', auth()->user()->headquarter->client_id)
                ->where('stores.warehouse_id', $mainWarehouse->id)
                ->get([
                    'products.description',
                    'products.id',
                    'stores.stock',
                    'stores.price',
                    'products.operation_type',
                    'products.internalcode',
                    'measures.description as measure'
                ]);
    }

    public function showPDF($id)
    {
        $requirement = Requirement::find($id);
        $clientInfo = Client::find(Auth::user()->headquarter->client_id);
        $pdf = PDF::loadView('logistic.requirements.pdf', compact('requirement', 'clientInfo'))->setPaper('A4');
        return $pdf->stream('REQUERIMIENTO ' . $requirement->serie . '-' . $requirement->correlative . '.pdf');
    }

    public function exportRequirement()
    {
        return Excel::download(new RequirementsExport, 'requerimientos.xlsx');
    }
}
