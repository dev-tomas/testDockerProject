<?php

namespace App\Http\Controllers\Manage;

use App\Brand;
use App\Category;
use App\Client;
use App\Correlative;
use App\HeadQuarter;
use App\Ubigeo;
use App\User;
use App\UserInfo;
use App\Warehouse;
use Caffeinated\Shinobi\Models\Permission;
use Caffeinated\Shinobi\Models\Role;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\AjaxController;

class CompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->ajax = new AjaxController();
    }

    public function index()
    {
        if (!auth()->user()->hasRole('superadmin')) {
            abort(401, 'Acción no autorizada.');
        }
        return view('manage.company.index');
    }

    public function dt_clients(Request $request)
    {
        if (!auth()->user()->hasRole('superadmin')) {
            abort(401, 'Acción no autorizada.');
        }

        $clients = Client::where(function ($query) use($request) {
            if($request->get('status') != ''){
                $query->where('status', $request->get('status'));
            }
            if($request->get('production') != ''){
                $query->where('production', $request->get('production'));
            }
            if($request->get('company') != ''){
                $query->where('trade_name','like', '%' . $request->get('company') . '%');
            }
        })
            ->get([
                'document',
                'trade_name',
                'business_name',
                'phone',
                'phone2',
                'email',
                'certificate',
                'expiration_certificate',
                'status',
                'production',
                'production_at'
            ]);

        return datatables()->of($clients)->toJson();
    }

    public function storeCompany(Request $request)
    {
        if (!auth()->user()->hasRole('superadmin')) {
            abort(401, 'Acción no autorizada.');
        }

        DB::beginTransaction();
        try {
            $info = $this->ajax->getCompanyInfo($request->ruc);
            $client = Client::create([
                'status'        =>  1,
                'document'      =>  $request->ruc,
                'plan_id'       =>  1,
                'trade_name'    =>  $info['razonSocial'],
                'business_name' =>  $info['razonSocial'],
                'address'       =>  $info['direccion'] . ' - ' . $info['departamento'] . ' - ' . $info['provincia'] . ' - ' . $info['distrito'],
                'email'         =>  $request->email,
                'phone'         =>  $request->phone,
                'igv_percentage' => 18.00,
                'days_to_send_collections_notifications' => 7
            ]);

            $clientId = $client->id;

            $ubigeo = Ubigeo::where([
                ['department', $info['departamento']],
                ['province', $info['provincia']],
                ['district', $info['distrito']]
            ])->first();

            if($ubigeo == false) {
                $ubigeo_id = 1;
            } else {
                $ubigeo_id = $ubigeo->id;
            }

            $headquarter = HeadQuarter::create([
                'description'   => 'LOCAL PRINCIPAL',
                'status'        => 1,
                'client_id'     => $client->id,
                'ubigeo_id'     => $ubigeo_id,
                'main'          => 1,
                'address'       => $info['direccion'] . ' - ' . $info['departamento'] . ' - ' . $info['provincia'] . ' - ' . $info['distrito'],
                'code'          => '001'
            ]);

            $warehouse = Warehouse::create([
                'code'              =>  '001',
                'description'       =>  'ALMACÉN PRINCIPAL',
                'address'           =>  $info['direccion'],
                'status'            =>  1,
                'headquarter_id'    =>  $headquarter->id,
                'responsable'       =>  'Administrador',
                'client_id'     => $client->id,
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'headquarter_id'    =>  $headquarter->id,
                'client_id' => $client->id,
                'ia'    => '1'
            ]);

            $userInfo = new UserInfo;
            $userInfo->user_id = $user->id;
            $userInfo->save();
            //dd ($clientId);

            DB::table('roles')->insert([
                'name'      =>  'Administrador',
                'slug'      =>  'admin',
                'client_id' => $client->id
            ]);

            $roleAdmin = Role::where('client_id',$client->id)->where('name', 'Administrador')->first();

            //dd($roleAdmin);

            DB::table('roles')->insert([
                'name'      =>  'Gerente',
                'slug'      =>  'manager',
                'special'   =>  'all-access',
                'client_id' => $client->id
            ]);

            DB::table('roles')->insert([
                'name'      =>  'Contador',
                'slug'      =>  'counter',
                'client_id' => $client->id
            ]);

            DB::table('roles')->insert([
                'name'      =>  'Administrador log.',
                'slug'      =>  'logistic_administrator',
                'client_id' => $client->id
            ]);

            DB::table('roles')->insert([
                'name'      =>  'Logística',
                'slug'      =>  'logistic',
                'client_id' => $client->id
            ]);

            DB::table('roles')->insert([
                'name'      =>  'Vendedor',
                'slug'      =>  'seller',
                'administrable' => 0,
                'client_id' => $client->id
            ]);
            /*
            Role::create([
                'name'      =>  'Gerente',
                'slug'      =>  'manager',
                'special'   =>  'all-access',
                'client_id' => $client->id
            ]);

            Role::create([
                'name'      =>  'Contador',
                'slug'      =>  'counter',
                'client_id' => $client->id
            ]);

            Role::create([
                'name'      =>  'Administrador log.',
                'slug'      =>  'logistic_administrator',
                'client_id' => $client->id
            ]);

            Role::create([
                'name'      =>  'Logística',
                'slug'      =>  'logistic',
                'client_id' => $client->id
            ]);

            Role::create([
                'name'      =>  'Vendedor',
                'slug'      =>  'seller',
                'administrable' => 0,
                'client_id' => $client->id
            ]);*/

            DB::table('role_user')->insert([
                'role_id' => $roleAdmin->id,
                'user_id'   => $user->id
            ]);

            $permissions = Permission::pluck('id');

            $user->permissions()->sync($permissions);

            Category::create([
                'description' => 'SIN CATEGORÍA',
                'status' => 1,
                'client_id' => $clientId
            ]);
            $brand = Brand::create([
                'description' => 'SIN MARCA',
                'client_id' => $clientId
            ]);

//            $icmper = Product::create([
//                'description' => 'Bolsa de Plástico',
//                'internalcode' => 'bp0001',
//                'status' => 1,
//                'measure_id' => 7,
//                'client_id' => $client->id,
//                'coin_id' => 1,
//                'operation_type' => 22,
//                'brand_id' => $brand->id,
//            ]);

            // $priceList

            // $product_price_list = new ProductPriceList;
            // $product_price_list->detail         =   '';
            // $product_price_list->price          =   0.10;
            // $product_price_list->product_id     =   $icmper->id;
            // $product_price_list->utility_percentage = 0.00;
            // $product_price_list->price_list_id =
            // $product_price_list->save();

//            $storeIcmper = Store::create([
//                'stock' => '0',
//                'price' => 0.10,
//                'warehouse_id' => $warehouse->id,
//                'higher_price'  =>  0,
//                'product_id' => $icmper->id,
//                'maximum_stock' =>  -1,
//                'minimum_stock' =>  -1
//            ]);
            Correlative::create([
                'serialnumber' => 'TK',
                'correlative' => '000',
                'headquarter_id' => $headquarter->id,
                'typevoucher_id' => 8,
                'contingency' => 0,
                'visible'   => 0,
                'client_id'     => $clientId,
            ]);
            $CDB = Correlative::create([
                'serialnumber' => 'DB',
                'correlative' => '000',
                'headquarter_id' => $headquarter->id,
                'typevoucher_id' => 9,
                'contingency' => 0,
                'visible'   => 0,
                'client_id'     => $clientId,
            ]);
            $CDAFP = Correlative::create([
                'serialnumber' => 'DAFP',
                'correlative' => '000',
                'headquarter_id' => $headquarter->id,
                'typevoucher_id' => 10,
                'contingency' => 0,
                'visible'   => 0,
                'client_id'     => $clientId,
            ]);
            $CGRT = Correlative::create([
                'serialnumber' => 'DGRT',
                'correlative' => '000',
                'headquarter_id' => $headquarter->id,
                'typevoucher_id' => 11,
                'contingency' => 0,
                'visible'   => 0,
                'client_id'     => $clientId,
            ]);
            Correlative::create([
                'serialnumber' => 'CP',
                'correlative' => '000',
                'headquarter_id' => $headquarter->id,
                'typevoucher_id' => 12,
                'contingency' => 0,
                'visible'   => 0,
                'client_id'     => $clientId,
            ]);
            $CCOT = Correlative::create([
                'serialnumber' => 'COT',
                'correlative' => '000',
                'headquarter_id' => $headquarter->id,
                'typevoucher_id' => 13,
                'contingency' => 0,
                'visible'   => 0,
                'client_id'     => $clientId,
            ]);
            $CCOM = Correlative::create([
                'serialnumber' => 'COM',
                'correlative' => '000',
                'headquarter_id' => $headquarter->id,
                'typevoucher_id' => 14,
                'contingency' => 0,
                'visible'   => 0,
                'client_id'     => $clientId,
            ]);
            $COC = Correlative::create([
                'serialnumber' => 'OC',
                'correlative' => '000',
                'headquarter_id' => $headquarter->id,
                'typevoucher_id' => 15,
                'contingency' => 0,
                'visible'   => 0,
                'client_id'     => $clientId,
            ]);
            $CRQ = Correlative::create([
                'serialnumber' => 'RQ',
                'correlative' => '000',
                'headquarter_id' => $headquarter->id,
                'typevoucher_id' => 16,
                'contingency' => 0,
                'visible'   => 0,
                'client_id'     => $clientId,
            ]);

            $LOW = Correlative::create([
                'serialnumber' => 'L001',
                'correlative' => '000',
                'headquarter_id' => $headquarter->id,
                'typevoucher_id' => 19,
                'contingency' => 0,
                'visible'   => 0,
                'client_id'     => $clientId,
            ]);
            $ING = Correlative::create([
                'serialnumber' => 'ING',
                'correlative' => '000',
                'headquarter_id' => $headquarter->id,
                'typevoucher_id' => 20,
                'contingency' => 0,
                'visible'   => 0,
                'client_id'     => $clientId,
            ]);
            $TRANS = Correlative::create([
                'serialnumber' => 'TR',
                'correlative' => '000',
                'headquarter_id' => $headquarter->id,
                'typevoucher_id' => 21,
                'contingency' => 0,
                'visible'   => 0,
                'client_id'     => $clientId,
            ]);
            $summary = Correlative::create([
                'serialnumber' => 'RD01',
                'correlative' => '000',
                'headquarter_id' => $headquarter->id,
                'typevoucher_id' => 22,
                'contingency' => 0,
                'visible'   => 0,
                'client_id'     =>  $client->id,
            ]);

            $pathCdr = storage_path('app/public/cdr/') .  Auth::user()->headquarter->client->document;
            $pathPdf = storage_path('app/public/pdf/') .  Auth::user()->headquarter->client->document;
            $pathXml = storage_path('app/public/xml/') .  Auth::user()->headquarter->client->document;
            File::makeDirectory($pathCdr, $mode = 0777, true, true);
            File::makeDirectory($pathPdf, $mode = 0777, true, true);
            File::makeDirectory($pathXml, $mode = 0777, true, true);

            DB::commit();

            return response()->json(true);
        } catch(\Exception $e) {
            DB::rollBack();

            return response()->json($e->getMessage());
        }
    }
}
