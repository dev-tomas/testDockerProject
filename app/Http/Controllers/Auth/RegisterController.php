<?php

namespace App\Http\Controllers\Auth;

use App\Ubigeo;
use App\User;
Use App\Client;
use App\HeadQuarter;
use App\Http\Controllers\Controller;
use App\Warehouse;
use App\Product;
use App\Store;
use App\Correlative;
use App\Category;
use App\Brand;
use Dotenv\Exception\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Mail\RegisterMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Mail;
// use Caffeinated\Shinobi\Models\Role;
use App\Role;
use App\UserInfo;

USE App\Http\Controllers\AjaxController;

class RegisterController extends Controller
{
    public $ajax;
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
        $this->ajax = new AjaxController();
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'document' => ['required'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        DB::beginTransaction();
        $info = $this->ajax->getCompanyInfo($data['document']);
        try {
            $client = Client::create([
                'status'        =>  1,
                'document'      =>  $data['document'],
                'plan_id'       =>  1,
                'trade_name'    =>  $info->razonSocial,
                'business_name' =>  $info->razonSocial,
                'address'       =>  $info->direccion . ' - ' . $info->departamento . ' - ' . $info->provincia . ' - ' . $info->distrito,
                'email'         =>  $data['email'],
                'phone'         =>  $data['phone']
            ]);

            $clientId = $client->id;

            $ubigeo = Ubigeo::where([
                ['department', $info->departamento],
                ['province', $info->provincia],
                ['district', $info->distrito]
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
                'address'       => $info->direccion . ' - ' . $info->departamento . ' - ' . $info->provincia . ' - ' . $info->distrito,
                'code'          => '001'
            ]);

            $warehouse = Warehouse::create([
                'code'              =>  '001',
                'description'       =>  'ALMACÉN PRINCIPAL',
                'address'           =>  $info->direccion,
                'status'            =>  1,
                'headquarter_id'    =>  $headquarter->id,
                'responsable'       =>  'Administrador',
                'client_id'     => $client->id,
            ]);
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'phone' => $data['phone'],
                'headquarter_id'    =>  $headquarter->id,
                'client_id' => $client->id,
                'ia'    => '1'
            ]);

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
                'role_id' => $roleAdmin->id,
                'user_id'   => $user->id
            ]);

            Category::create([
                'description' => 'SIN CATEGORÍA',
                'status' => 1,
                'client_id' =>  $client->id,        ]);
            Brand::create([
                'description' => 'SIN MARCA',
                'client_id' =>  $client->id,        ]);

            $icmper = Product::create([
                'description' => 'Bolsa de Plástico',
                'internalcode' => 'bp0001',
                'status' => 1,
                'measure_id' => 7,
                'client_id' => $client->id,
                'coin_id' => 1,
                'operation_type' => 22
            ]);

            $storeIcmper = Store::create([
                'stock' => '0',
                'price' => 0.10,
                'warehouse_id' => $warehouse->id,
                'product_id' => $icmper->id
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
                'client_id'     => $clientId,
            ]);

            $summary = Correlative::create([
                'serialnumber' => 'RD01',
                'correlative' => '001',
                'headquarter_id' => $headquarter->id,
                'typevoucher_id' => 22,
                'contingency' => 0,
                'visible'   => 0,
                'client_id'     => $clientId,
            ]);

            $summary = Correlative::create([
                'serialnumber' => 'R001',
                'correlative' => '001',
                'headquarter_id' => $headquarter->id,
                'typevoucher_id' => 23,
                'contingency' => 0,
                'visible'   => 0,
                'client_id'     => $clientId,
            ]);

            $summary = Correlative::create([
                'serialnumber' => 'P001',
                'correlative' => '001',
                'headquarter_id' => $headquarter->id,
                'typevoucher_id' => 24,
                'contingency' => 0,
                'visible'   => 0,
                'client_id'     => $clientId,
            ]);

            DB::commit();
            Mail::to($data['email'])->send(new RegisterMail($data['name']));
        } catch(\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
        }

        return $user;
    }
}
