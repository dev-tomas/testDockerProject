<?php

use App\Classification;
use App\Customer;
use App\PriceList;
use App\ProductPriceList;
use App\Ubigeo;
Use App\Client;
use App\HeadQuarter;
use App\User;
use App\Warehouse;
use App\Product;
use App\Store;
use App\Correlative;
use App\Category;
use App\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\AjaxController;
use Illuminate\Support\Facades\DB;
use Caffeinated\Shinobi\Models\Role;
use App\UserInfo;

class SuperAdminUserSeeder extends Seeder
{
    public function __construct()
    {
        $this->ajax = new AjaxController();
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();
        //$info = $this->ajax->getCompanyInfo('20571569885');

        try {
            $client = Client::create([
                'status'        =>  1,
                'document'      =>  '20571569885',
                'plan_id'       =>  1,
                //'trade_name'    =>  $info->razonSocial,
                //'business_name' =>  $info->razonSocial,
                'trade_name'    =>  'EXTRALEY PERU E.I.R.L.',
                'business_name' =>  'EXTRALEY PERU E.I.R.L.',
                //'address'       =>  $info->direccion . ' - ' . $info->departamento . ' - ' . $info->provincia . ' - ' . $info->distrito,
                'address' => 'CAL.ALFREDO MALDONADO NRO. 654 (FRENTE AL COLEGIO EL CARMELO) LIMA - LIMA - PUEBLO LIBRE (MAGDALENA VIEJA)',
                'email'         =>  'admin@bautifak.pe',
                'phone'         =>  '947313750',
                'igv_percentage'=>  18
            ]);


            $ubigeo = Ubigeo::where([
                //['department', $info->departamento],
                //['province', $info->provincia],
                //['district', $info->distrito]
                ['department', 'Lima'],
                ['province', 'Lima'],
                ['district', 'Pueblo Libre']
            ])->first();

            $clientId = $client->id;

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
                //'address'       => $info->direccion . ' - ' . $info->departamento . ' - ' . $info->provincia . ' - ' . $info->distrito,
                'address' => 'CAL.ALFREDO MALDONADO NRO. 654 (FRENTE AL COLEGIO EL CARMELO) LIMA - LIMA - PUEBLO LIBRE (MAGDALENA VIEJA)',
                'code'          => '001'
            ]);

            $warehouse = Warehouse::create([
                'code'              =>  '001',
                'description'       =>  'ALMACÉN PRINCIPAL',
                //'address'           =>  $info->direccion,
                'address' => 'CAL.ALFREDO MALDONADO NRO. 654 (FRENTE AL COLEGIO EL CARMELO) LIMA - LIMA - PUEBLO LIBRE (MAGDALENA VIEJA)',
                'status'            =>  1,
                'headquarter_id'    =>  $headquarter->id,
                'responsable'       =>  'Administrador',
                'client_id'     => $client->id,
            ]);
            $user = User::create([
                'name' => 'Super Admin',
                'email' => 'admin@bautifak.com',
                'phone' => '947313750',
                'password'  => Hash::make('AdminBautif@k'),
                'headquarter_id' => $headquarter->id,
                'status' => 1,
                'client_id' => $client->id,
                'ia'    => '1'
            ]);

            $customer = new Customer();
            $customer->description          =   'CLIENTES VARIOS';
            $customer->document             =   '00000000';
            $customer->phone                =   '-';
            $customer->address              =   '-';
            $customer->typedocument_id      =   2;
            $customer->client_id            =   $client->id;
            $customer->email                =   '-';
            $customer->tradename            =   'CLIENTES VARIOS';
            $customer->user_id              =   $user->id;
            $customer->save();

            $userInfo = new UserInfo;
            $userInfo->user_id = $user->id;
            $userInfo->save();

            $roleAdmin = Role::create([
                'name'      =>  'Administrador',
                'slug'      =>  'admin',
                'special'   =>  'all-access',
                'client_id' => $client->id,
            ]);

            Role::create([
                'name'      =>  'Gerente',
                'slug'      =>  'manager',
                'special'   =>  'all-access',
                'client_id' => $client->id,
            ]);

            Role::create([
                'name'      =>  'Contador',
                'slug'      =>  'counter',
                'client_id' => $client->id,
            ]);

            Role::create([
                'name'      =>  'Administrador log.',
                'slug'      =>  'logistic_administrator',
                'client_id' => $client->id,
            ]);

            Role::create([
                'name'      =>  'Logísitca',
                'slug'      =>  'logistic',
                'client_id' => $client->id,
            ]);

            Role::create([
                'name'      =>  'Vendedor',
                'slug'      =>  'seller',
                'administrable' => 0,
                'client_id' => $client->id,
            ]);

            DB::table('role_user')->insert([
                'role_id' => 1,
                'user_id'   => $user->id
            ]);

            $category = Category::create([
                'description' => 'SIN CATEGORÍA',
                'status' => 1,
                'client_id' =>  $client->id,        ]);
            $brand = Brand::create([
                'description' => 'SIN MARCA',
                'client_id' =>  $client->id,        ]);
            $classification = Classification::create([
                'description'   =>  'SIN CLASIFICACIÓN',
                'state'         =>  1
            ]);

            $icmper = new Product;
            $icmper->description = 'Bolsa de Plástico';
           $icmper->internalcode = 'bp0001';
            $icmper->status = 1;
           $icmper->measure_id = 7;
            $icmper->client_id = $client->id;
            $icmper->coin_id = 1;
            $icmper->brand_id  =  $brand->id;
            $icmper->category_id   =  $category->id;
            $icmper->operation_type = 22;
            $icmper->image =  'products/default.jpg';
            $icmper->save();

            $price_list = new PriceList();
            $price_list->description = 'PRECIO NORMAL';
            $price_list->state = 1;
            $price_list->client_id = $client->id;
            $price_list->save();

            $price_list_icm                     =   new ProductPriceList;
            $price_list_icm->price_list_id      =   $price_list->id;
            $price_list_icm->price              =   0.10;
            $price_list_icm->product_id         =   $icmper->id;
            $price_list_icm->utility_percentage =   0;
            $price_list_icm->save();


            $storeIcmper = Store::create([
                'stock' => '0',
                'price' => 0.10,
                'warehouse_id' => $warehouse->id,
                'product_id' => $icmper->id,
                'higher_price'  =>  0,
                'maximum_stock' =>  -1,
                'minimum_stock' =>  -1
            ]);

            Correlative::create([
                'serialnumber' => 'TK',
                'correlative' => '000',
                'headquarter_id' => $headquarter->id,
                'typevoucher_id' => 8,
                'contingency' => 0,
                'visible'   => 0,
                'client_id'     =>  $client->id,
            ]);
            $CDB = Correlative::create([
                'serialnumber' => 'DB',
                'correlative' => '000',
                'headquarter_id' => $headquarter->id,
                'typevoucher_id' => 9,
                'contingency' => 0,
                'visible'   => 0,
                'client_id'     =>  $client->id,
            ]);
            $CDAFP = Correlative::create([
                'serialnumber' => 'DAFP',
                'correlative' => '000',
                'headquarter_id' => $headquarter->id,
                'typevoucher_id' => 10,
                'contingency' => 0,
                'visible'   => 0,
                'client_id'     =>  $client->id,
            ]);
            $CGRT = Correlative::create([
                'serialnumber' => 'DGRT',
                'correlative' => '000',
                'headquarter_id' => $headquarter->id,
                'typevoucher_id' => 11,
                'contingency' => 0,
                'visible'   => 0,
                'client_id'     =>  $client->id,
            ]);
            Correlative::create([
                'serialnumber' => 'CP',
                'correlative' => '000',
                'headquarter_id' => $headquarter->id,
                'typevoucher_id' => 12,
                'contingency' => 0,
                'visible'   => 0,
                'client_id'     =>  $client->id,
            ]);
            $CCOT = Correlative::create([
                'serialnumber' => 'COT',
                'correlative' => '000',
                'headquarter_id' => $headquarter->id,
                'typevoucher_id' => 13,
                'contingency' => 0,
                'visible'   => 0,
                'client_id'     =>  $client->id,
            ]);
            $CCOM = Correlative::create([
                'serialnumber' => 'COM',
                'correlative' => '000',
                'headquarter_id' => $headquarter->id,
                'typevoucher_id' => 14,
                'contingency' => 0,
                'visible'   => 0,
                'client_id'     =>  $client->id,
            ]);
            $COC = Correlative::create([
                'serialnumber' => 'OC',
                'correlative' => '000',
                'headquarter_id' => $headquarter->id,
                'typevoucher_id' => 15,
                'contingency' => 0,
                'visible'   => 0,
                'client_id'     =>  $client->id,
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

            $CRQ = Correlative::create([
                'serialnumber' => 'RQ',
                'correlative' => '000',
                'headquarter_id' => $headquarter->id,
                'typevoucher_id' => 16,
                'contingency' => 0,
                'visible'   => 0,
                'client_id'     =>  $client->id,
            ]);

            $LOW = Correlative::create([
                'serialnumber' => 'L001',
                'correlative' => '000',
                'headquarter_id' => $headquarter->id,
                'typevoucher_id' => 19,
                'contingency' => 0,
                'visible'   => 0,
                'client_id'     =>  $client->id,
            ]);
            $ING = Correlative::create([
                'serialnumber' => 'ING',
                'correlative' => '000',
                'headquarter_id' => $headquarter->id,
                'typevoucher_id' => 20,
                'contingency' => 0,
                'visible'   => 0,
                'client_id'     => $client->id
            ]);
            $TRANS = Correlative::create([
                'serialnumber' => 'TR',
                'correlative' => '000',
                'headquarter_id' => $headquarter->id,
                'typevoucher_id' => 21,
                'contingency' => 0,
                'visible'   => 0,
                'client_id'     => $client->id,
            ]);
            DB::commit();
        } catch(\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
        }
    }
}
