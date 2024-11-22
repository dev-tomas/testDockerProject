<?php

namespace App\Http\Controllers;

use App\ClientCredential;
use App\ClientCredentials;
use App\Exports\UsersExport;
use App\Services\SunatCredentialsService;
use Image;
use App\Coin;
use App\Sale;
use App\User;
use App\Client;
use App\Ubigeo;
use App\UserInfo;
use App\Quotation;
use App\Warehouse;
use App\BankAccount;
use App\Correlative;
use App\HeadQuarter;
use App\TypeVoucher;
use App\IconDashboard;
use App\PaymentMethod;
use App\BankAccountType;
use App\Themes as Theme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Caffeinated\Shinobi\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Caffeinated\Shinobi\Models\Permission;
use Maatwebsite\Excel\Facades\Excel;

class ConfigurationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('status.client');
        $this->middleware('can:empresa.show')->only(['company']);
        $this->middleware('can:empresa.emisor')->only(['saveOne']);
        $this->middleware('can:empresa.impresa')->only(['saveTwo']);
        $this->middleware('can:empresa.adicional')->only(['saveThree']);
        $this->middleware('can:localserie.show')->only(['headquarters','dt_headquarters']);
        $this->middleware('can:localserie.create')->only(['addHeadquarter']);
        $this->middleware('can:apariencia.show')->only(['appearance']);
        $this->middleware('can:usuarios.show')->only(['users','dt_users']);
        $this->middleware('can:usuarios.create')->only(['newUser','createUser', 'updateUser']);
    }
    public function index() {}

    public function company() {
        $data = array(
            'coins' =>  Coin::all(),
            'bank_account_types'    =>  BankAccountType::all(),
            'bank_accounts'         =>  BankAccount::where('client_id', Auth::user()->headquarter->client_id)->get(),
            'exists'                =>  Storage::disk('public')->exists(Auth::user()->headquarter->client->certificate),
            'icons'                 =>  IconDashboard::all(),
            'themes'                =>  Theme::all(),
            'payment_methods'       =>  PaymentMethod::where('client_id', auth()->user()->headquarter->client_id)->get(),
        );

        return view('configuration.company')->with($data);
    }

    public function invoices()
    {
        $exists = Storage::disk('public')->exists(Auth::user()->headquarter->client->certificate);
        $data = array(
            'exists'    =>  $exists
        );

        return view('configuration.invoice')->with($data);
    }

    public function appearance(){
        return view('configuration.appearance');
    }

    /**
     * Create Bank Account and Update Data
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteBankAccount(Request $request)
    {
        $bankAccount = BankAccount::find($request->input('id'));
        return response()->json($bankAccount->delete());
    }

    public function deletePaymentMethod(Request $request)
    {
        $method = PaymentMethod::find($request->input('id'));
        return response()->json($method->delete());
    }

    public function saveOne(Request $request)
    {
        DB::beginTransaction();
        try {
            $client = Client::find(Auth::user()->headquarter->client_id);
            $client->trade_name = $request->post('trade_name');
            $client->business_name = $request->post('business_name');
            $client->address = $request->post('address');
            $client->email = $request->post('email');
            $client->phone = $request->post('phone');
            $client->web = $request->post('web');
            $client->save();

            if($request->post('account_type')) {
                for ($x = 0; $x < count($request->post('account_type')); $x++) {
                    if ($request->post('account_type')[$x] && $request->post('account_type')[$x] != '') {
                        $client->banks()
                            ->attach($request->post('account_type')[$x], [
                                'number' => $request->post('account_number')[$x],
                                'bank_name' => $request->post('name_bank')[$x],
                                'headline' => $request->post('headline')[$x],
                                'cci' => $request->post('cci')[$x],
                                'observation' => $request->post('additional_description')[$x],
                                'coin_id' => $request->post('coin')[$x],
                                'accounting_account' => $request->post('accounting_account')[$x],
                            ]);
                    }
                }
            }

            if ($request->has('name_payment_method')) {
                for ($x = 0; $x < count($request->post('name_payment_method')); $x++) {
                    if ($request->post('name_payment_method')[$x] && $request->post('account_number_payment_method')[$x] != '') {
                        $payment = new PaymentMethod;
                        $payment->name = $request->post('name_payment_method')[$x];
                        $payment->account = $request->post('account_number_payment_method')[$x];
                        $payment->client_id = auth()->user()->headquarter->client_id;
                        $payment->save();
                    }
                }
            }

            DB::commit();

            return response()->json(true);
        } catch (\Exception $e) {
            DB::rollBack();
            $rpta = '';
            switch ($e->getCode()) {
                /*case '23000':
                    $rpta = 'Este producto ya está registrado.';
                    break;*/
                default:
                    $rpta = $e->getMessage();
                    break;
            }

            return response()->json($rpta);
            // return $rpta;
        }
    }

    public function saveTwo(Request $request)
    {
        DB::beginTransaction();
        try {
            $client = Client::find(Auth::user()->headquarter->client_id);
            $client->invoice_size = $request->post('invoice_size');
            $client->retention_size = $request->post('retention_size');
            $client->ticket_size = $request->post('ticket_size');
            $client->perception_size = $request->post('perception_size');
            $client->quotation_size = $request->post('quotation_size');
            $client->reference_guide_size = $request->post('reference_guide_size');
            $client->pdf_header = $request->pdf_header;
            $client->pdf_footer = $request->pdf_footer;
            $client->save();

            DB::commit();

            return response()->json(true);
        } catch (\Exception $e) {
            DB::rollBack();
            $rpta = '';
            switch ($e->getCode()) {
                /*case '23000':
                    $rpta = 'Este producto ya está registrado.';
                    break;*/
                default:
                    $rpta = $e->getMessage();
                    break;
            }

            return response()->json(false);
        }
    }

    public function saveImage(Request $request)
    {
        $t = $this;
        try{
            $client = Client::find(Auth::user()->headquarter->client_id);
            $image = $request->file('logo');
            $new_name = 'logo_' . $client->document . '.' . $image->getClientOriginalExtension();

            //$new_image = $new_name;
            $destinationPath = public_path('images/');
            $img = Image::make($image->getRealPath());
            $img->resize(320, 80, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath . $new_name);

            $destinationPath = public_path('thumbnail');
            $image->move($destinationPath, $new_name);

            $client->logo = $new_name;
            $client->save();

            return Redirect::back()->with('success','Imagen subida satisfactoriamente!');
        } catch (\Exception $e) {
            return Redirect::back()->with('error','Ocurrió un error al intentar subir la imagen!');
        }
    }

    public function saveThree(Request $request)
    {
        DB::beginTransaction();
        try {
            if($request->post('automatic_consumption_surcharge') != null) {
                $automatic_consumption_surcharge = 1;
                $automatic_consumption_surcharge_price = $request->post('automatic_consumption_surcharge_price');
            } else {
                $automatic_consumption_surcharge = 0;
                $automatic_consumption_surcharge_price = 0;
            }

            if($request->post('consumption_tax_plastic_bags') != null) {
                $consumption_tax_plastic_bags = 1;
            } else {
                $consumption_tax_plastic_bags = 0;
            }

            if($request->post('issue_with_previous_data') != null) {
                $issue_with_previous_data = 1;
                $issue_with_previous_data_days = $request->post('issue_with_previous_data_days');
            } else {
                $issue_with_previous_data = 0;
                $issue_with_previous_data_days = 0;
            }

            if($request->post('jungle_region_goods') != null) {
                $jungle_region_goods = 1;
            } else {
                $jungle_region_goods = 0;
            }

            if($request->post('jungle_region_services') != null) {
                $jungle_region_services = 1;
            } else {
                $jungle_region_services = 0;
            }

            if($request->post('price_type')) {
                $price_type = $request->post('price_type');
            } else {
                $price_type = 0;
            }
            if($request->post('less_employees')) {
                $less_employees = $request->post('less_employees');
            } else {
                $less_employees = 0;
            }

            if ($request->has('status')) {
                $status = $request->post('status');
            } else {
                $status = 0;
            }

            $client = Client::find(Auth::user()->headquarter->client_id);
            $client->price_type = $price_type;
            $client->automatic_consumption_surcharge = $automatic_consumption_surcharge;
            $client->automatic_consumption_surcharge_price = $automatic_consumption_surcharge_price;
            $client->consumption_tax_plastic_bags = $consumption_tax_plastic_bags;
            $client->consumption_tax_plastic_bags_price = $request->post('consumption_tax_plastic_bags_price');
            $client->issue_with_previous_data = $issue_with_previous_data;
            $client->issue_with_previous_data_days = $issue_with_previous_data_days;
            $client->jungle_region_goods = $jungle_region_goods;
            $client->jungle_region_services = $jungle_region_services;
            $client->less_employees = $less_employees;
            $client->exchange_rate_purchase = $request->post('exchange_rate_pusrchase');
            $client->exchange_rate_sale = $request->post('exchange_rate_sale');
            $client->days_to_send_collections_notifications = serialize($request->day);
            if ($request->has('status')) {
                $client->status = $status;
            }
            $client->cash_type = $request->filled('cash_type') ? 1 : 0;
            $client->type_send_boletas = $request->post('type_send_boletas');
            $client->save();

            DB::commit();

            return response()->json(true);
        } catch (\Exception $e) {
            DB::rollBack();
            $rpta = '';
            switch ($e->getCode()) {
                /*case '23000':
                    $rpta = 'Este producto ya está registrado.';
                    break;*/
                default:
                    $rpta = $e->getMessage();
                    break;
            }

            return response()->json($rpta);
        }
    }

    /**
     * HeadQuarters
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editHeadQuarter($id)
    {
        $data =  array(
            'headquarter'       =>  HeadQuarter::where('id', $id)->first(),
            'correlatives'      =>  Correlative::where('visible', 1)->where('headquarter_id', $id)->where('client_id', Auth::user()->headquarter->client_id)->get(),
            'ubigeos'           =>  Ubigeo::all(),
            'typeVoucher'       =>  TypeVoucher::where('visible', 1)->get(),
            'headquarter_id'    =>  $id
        );
        return view('configuration.headquarter.edit')->with($data);
    }

    public function headquarters()
    {
        return view('configuration/headquarters');
    }

    public function dt_headquarters()
    {
        $headquarters = HeadQuarter::where(
            'client_id', Auth::user()->headquarter->client_id
        )->with('ubigeos', 'correlatives', 'correlatives.type_voucher', 'client')->get();
        return datatables()->of($headquarters)->toJson();
    }

    public function addHeadquarter()
    {
        $correlatives = Correlative::where('headquarter_id', Auth::user()->headquarter->id)->with('type_voucher')->get();
        $typeVoucher = TypeVoucher::where('visible', 1)->get();
        $codeH = HeadQuarter::where('client_id', Auth::user()->headquarter->client_id)->get()->last();
        $newCodeH = (int) $codeH->code + 1;
        $users = User::where('headquarter_id', Auth::user()->headquarter->id)->get();
        $data = array(
            'correlatives'  =>  $correlatives,
            'users'         =>  $users,
            'ubigeos'       =>  Ubigeo::all(),
            'typeVoucher'   =>  $typeVoucher,
            'newCodeH'      =>  $newCodeH
        );
        return view('configuration/create')->with($data);
    }

    public function saveHeadQuarter(Request $request)
    {
        DB::beginTransaction();
        try{
            $headQuarter = new HeadQuarter;
            $headQuarter->description   =   $request->post('description');
            $headQuarter->ubigeo_id     =   $request->post('ubigeo');
            $headQuarter->address       =   $request->post('address');
            $headQuarter->code          =   $request->post('code');
            $headQuarter->status        =   1;
            $headQuarter->client_id     =   Auth::user()->headquarter->client_id;
            $headQuarter->main          =   0;
            $headQuarter->sunat_code    =   $request->post('sunat_code');
            $headQuarter->save();

            $warehouse = new Warehouse;
            $warehouse->code = $request->post('code');
            $warehouse->description = $request->post('description');
            $warehouse->address = $request->post('address');
            $warehouse->status = 1;
            $warehouse->responsable = Auth::user()->name;
            $warehouse->headquarter_id = $headQuarter->id;
            $warehouse->client_id = Auth::user()->headquarter->client_id;
            $warehouse->save();

            if ($request->post('document_type')) {
                for($x = 0; $x < count($request->post('document_type')); $x++) {
                    if(isset($request->post('contingency')[$x])) {
                        $contingency = $request->post('contingency')[$x];
                    } else {
                        $contingency = 0;
                    }
                    $correlative                    =   new Correlative;
                    $correlative->serialnumber      =   $request->post('serial_number')[$x];
                    $correlative->correlative       =   $request->post('correlative')[$x];
                    $correlative->headquarter_id    =   $headQuarter->id;
                    $correlative->typevoucher_id   =   $request->post('document_type')[$x];
                    $correlative->contingency       =   $contingency;
                    $correlative->client_id       =   Auth::user()->headquarter->client_id;
                    $correlative->visible = 1;
                    $correlative->save();
                }
            }

            DB::commit();
            return response()->json(true);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($e);
        }
    }

    public function deleteCorrelative(Request $request)
    {
        $correlative = Correlative::find($request->get('correlative_id'));
        $quotations = Quotation::where([
            ['headquarter_id', $request->get('headquarter')],
            ['typevoucher_id', $correlative->typevoucher_id]
        ])->count();

        $sales = Sale::where([
            ['headquarter_id', $request->get('headquarter')],
            ['typevoucher_id', $correlative->typevoucher_id]
        ])->count();

        if($quotations > 0 || $sales > 0) {
            return response()->json(-5);
        }

        return response()->json($correlative->delete());
    }

    public function updateHeadQuarter(Request $request)
    {
        DB::beginTransaction();
        try{
            $headQuarter                =   HeadQuarter::find($request->post('headquarter_id'));
            $headQuarter->description   =   $request->post('description');
            $headQuarter->ubigeo_id     =   $request->post('ubigeo');
            $headQuarter->address       =   $request->post('address');
            $headQuarter->code          =   $request->post('code');
            $headQuarter->status        =   1;
            $headQuarter->client_id     =   Auth::user()->headquarter->client_id;
            $headQuarter->sunat_code    =   $request->post('sunat_code');
            $headQuarter->save();

            if ($request->post('document_type')) {
                for($x = 0; $x < count($request->post('document_type')); $x++) {
                    if(isset($request->post('contingency')[$x])) {
                        $contingency = $request->post('contingency')[$x];
                    } else {
                        $contingency = 0;
                    }
                    $correlative                    =   new Correlative;
                    $correlative->serialnumber      =   $request->post('serial_number')[$x];
                    $correlative->correlative       =   $request->post('correlative')[$x];
                    $correlative->headquarter_id    =   $headQuarter->id;
                    $correlative->typevoucher_id   =   $request->post('document_type')[$x];
                    $correlative->contingency       =   $contingency;
                    $correlative->visible       =   1;
                    $correlative->client_id       =   Auth::user()->headquarter->client_id;
                    $correlative->save();
                }
            }

            DB::commit();
            return response()->json(true);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($e);
        }
    }

    /**
     * Invoices
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveSol(Request $request)
    {
        $client = Client::find(Auth::user()->headquarter->client_id);
        $client->usuario_sol    =   $request->input('user');
        $client->clave_sol      =   $request->input('password');
        $client->expiration_certificate  =   $request->input('expiration');
        return response()->json($client->save());
    }

    /**
     * Users
     */
    public function users()
    {
        $roles = Role::where('client_id', Auth::user()->headquarter->client_id)->get();
        $headquarters = HeadQuarter::where('client_id', Auth::user()->headquarter->client_id)->get();
        $permissions = Permission::get();
        $pmenu = Permission::where('section','menu')->get();
        $pcotizaciones = Permission::where('section','acomercial')->where('section_2','cotizaciones')->get();
        $pventas = Permission::where('section','acomercial')->where('section_2','ventas')->get();
        $pclientes = Permission::where('section','acomercial')->where('section_2','clientes')->get();
        $preportes = Permission::where('section','acomercial')->where('section_2','reportes')->get();
        $pproveedores = Permission::where('section','alogistica')->where('section_2','proveedores')->get();
        $pcenterCost = Permission::where('section','alogistica')->where('section_2','centrocostos')->get(); 
        $pcompras = Permission::where('section','alogistica')->where('section_2','compras')->get();
        $pocompras = Permission::where('section','alogistica')->where('section_2','ordencompras')->get();
        $pservicios = Permission::where('section','alogistica')->where('section_2','pservicios')->get();
        $pcategorias = Permission::where('section','alogistica')->where('section_2','categorias')->get();
        $prequerimientos = Permission::where('section','alogistica')->where('section_2','requerimientos')->get();
        $palmacenes = Permission::where('section','almacen')->where('section_2','almacenes')->get();
        $pinventarios = Permission::where('section','almacen')->where('section_2','inventarios')->get();
        $pcomprobantes = Permission::where('section','contabilidad')->where('section_2','contabilidad')->get();
        $panulaciones = Permission::where('section','acontabilidad')->where('section_2','anulaciones')->get();
        $pempresa = Permission::where('section','configuraciones')->where('section_2','empresa')->get();
        $plocalserie = Permission::where('section','configuraciones')->where('section_2','localserie')->get();
        $papariencia = Permission::where('section','configuraciones')->where('section_2','apariencia')->get();
        $pusuarios = Permission::where('section','configuraciones')->where('section_2','usuarios')->get();
        $proles = Permission::where('section','configuraciones')->where('section_2','roles')->get();

        return view('configuration.users', compact('pinventarios','pcomprobantes','panulaciones','pcenterCost','preportes','roles','headquarters','permissions','pmenu','pcotizaciones','pventas','pclientes','pproveedores','pcompras','pocompras','pservicios','pcategorias','palmacenes','pempresa','plocalserie','papariencia','pusuarios','proles','prequerimientos'));
    }

    public function editUser(User $user)
    {
        $data = array(
            // 'user'          =>  User::where('id', $id)->with('roles')->first(),
            // 'roles'         =>  Role::All(),
            'headquarters'  =>  HeadQuarter::where('client_id', Auth::user()->headquarter->client_id)->get()
        );
        $roles = Role::where('client_id', Auth::user()->headquarter->client_id)->get();
        $permissions = Permission::get();
        $pmenu = Permission::where('section','menu')->get();
        $pcotizaciones = Permission::where('section','acomercial')->where('section_2','cotizaciones')->get();
        $pventas = Permission::where('section','acomercial')->where('section_2','ventas')->get();
        $pclientes = Permission::where('section','acomercial')->where('section_2','clientes')->get();
        $preportes = Permission::where('section','acomercial')->where('section_2','reportes')->get();
        $pproveedores = Permission::where('section','alogistica')->where('section_2','proveedores')->get();
        $pcenterCost = Permission::where('section','alogistica')->where('section_2','centrocostos')->get(); 
        $pcompras = Permission::where('section','alogistica')->where('section_2','compras')->get();
        $pocompras = Permission::where('section','alogistica')->where('section_2','ordencompras')->get();
        $pservicios = Permission::where('section','alogistica')->where('section_2','pservicios')->get();
        $pcategorias = Permission::where('section','alogistica')->where('section_2','categorias')->get();
        $prequerimientos = Permission::where('section','alogistica')->where('section_2','requerimientos')->get();
        $palmacenes = Permission::where('section','almacen')->where('section_2','almacenes')->get();
        $pinventarios = Permission::where('section','almacen')->where('section_2','inventarios')->get();
        $pcomprobantes = Permission::where('section','contabilidad')->where('section_2','contabilidad')->get();
        $panulaciones = Permission::where('section','acontabilidad')->where('section_2','anulaciones')->get();
        $pempresa = Permission::where('section','configuraciones')->where('section_2','empresa')->get();
        $plocalserie = Permission::where('section','configuraciones')->where('section_2','localserie')->get();
        $papariencia = Permission::where('section','configuraciones')->where('section_2','apariencia')->get();
        $pusuarios = Permission::where('section','configuraciones')->where('section_2','usuarios')->get();
        $proles = Permission::where('section','configuraciones')->where('section_2','roles')->get();

        return view('configuration.user.edit', compact('pinventarios','pcomprobantes','panulaciones','pcenterCost','preportes','user','roles','permissions','pmenu','pcotizaciones','pventas','pclientes','pproveedores','pcompras','pocompras','pservicios','pcategorias','palmacenes','pempresa','plocalserie','papariencia','pusuarios','proles','prequerimientos'))->with($data);
    }

    public function updateStatusUser(Request $request)
    {
        $user           =   User::find(Auth::user()->id);
        $user->status   =   $request->input('status');
        return response()->json($user->save());
    }

    public function newUser()
    {
        $data = array(
            'roles'         =>  Role::all(),
            'headquarters'  =>  HeadQuarter::where('client_id', Auth::user()->headquarter->client_id)->get()
        );
        return view('configuration.user.create')->with($data);
    }

    public function createUser(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = new User;
            $user->name             = $request->post('name');
            $user->email            = $request->post('email');
            $user->phone            = $request->post('phone');
            $user->password         = Hash::make($request->post('password'));
            $user->headquarter_id   = $request->post('headquarter');
            $user->client_id        = auth()->user()->headquarter->client_id;
            $user->is_supervisor    = $request->filled('is_supervisor');
            $user->pin    = $request->pin;
            if($request->post('status') != null) {
                $user->status   = $request->post('status');
            } else {
                $user->status   = 0;
            }

            if ($request->has('logo')) {
                $image = $request->file('logo');
                $new_name = 'logo_' . $request->post('email') . '.' . $image->getClientOriginalExtension();

                $destinationPath = public_path('images/profile/');
                $img = Image::make($image->getRealPath());
                $img->resize(80, 80, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($destinationPath . $new_name);
            } else {
                $new_name = 'logo.png';
            }

            $user->logo             = $new_name;
            $user->save();
            $user->roles()->sync($request->rol);
            $user->permissions()->sync($request->get('permissions'));
            
            $userInfo = new UserInfo;
            $userInfo->user_id = $user->id;
            $userInfo->save();

            DB::commit();
            return Redirect::back()->with('success','Usuario Registrado Satisfactoriamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return Redirect::back()->with('error','Ocurrió un error al intentar registrar el usuario.');
        }
    }

    public function updateUser(Request $request,$id)
    {
        DB::beginTransaction();
        try {
            $user = User::find($id);
            $user->name             = $request->post('name');
            $user->email            = $request->post('email');
            $user->phone            = $request->post('phone');
            if($request->post('password') !== '' && $request->post('password') != null) {
                $user->password         = Hash::make($request->post('password'));
            }
            $user->headquarter_id   = $request->post('headquarter');
            $user->client_id        = auth()->user()->headquarter->client_id;
            $user->is_supervisor    = $request->filled('is_supervisor');
            $user->pin    = $request->pin;
            if($request->post('status') != null) {
                $user->status   = $request->post('status');
            } else {
                $user->status   = 0;
            }

            if ($request->has('logo')) {
                $image = $request->file('logo');
                $new_name = 'logo_' . $request->post('email') . '.' . $image->getClientOriginalExtension();

                $destinationPath = public_path('images/profile/');
                $img = Image::make($image->getRealPath());
                $img->resize(80, 80, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($destinationPath . $new_name);
            } else {
                $new_name = 'logo.png';
            }

            $user->logo             = $new_name;
            $user->save();

            // if(auth()->user()->roles[0]->slug !== 'superadmin') {
                $user->roles()->sync($request->rol);
                $user->permissions()->sync($request->get('permissions'));
            // } else {
            //     $user->roles()->sync($request->rol);
            //     $user->permissions()->sync($request->get('permissions'));
            // }

            DB::commit();
            toastr()->success('Usuario Actualizado Satisfactoriamente.', 'Usuario Actualizado');
            return redirect()->route('users');
        } catch (\Exception $e) {
            DB::rollBack();
            toastr()->error('Ocurrió un error al intentar actualizar el usuario..', 'Error');
            return redirect()->route('users');
        }
    }

    public function dt_users(Request $request)
    {
        $users = User::with('headquarter', 'roles')->where('client_id', auth()->user()->headquarter->client_id)->get();
        return datatables()->of($users)->toJson();
    }

    public function userExport() {
        return Excel::download(new UsersExport, 'Usuarios.xlsx');
    }

    public function storeSunatCredentialsApi(Request $request)
    {
        DB::beginTransaction();
        try {
            $credentials = ClientCredential::where('client_id', auth()->user()->headquarter->client_id)->first();
            if ($credentials == null) {
                $credentials = new ClientCredential;
                $credentials->client_id = auth()->user()->headquarter->client_id;
            }
            $credentials->sunat_client_id = $request->sunat_client_id;
            $credentials->sunat_client_secret = $request->sunat_client_secret;
            $credentials->save();

            DB::commit();

            $sunatResponse = (new SunatCredentialsService($credentials->client))->generateToken();

            if ($sunatResponse['success']) {
                return response()->json(['success' => true, 'message' => 'Conección con SUNAT correcta.']);
            }
            return response()->json(['success' => false, 'message' => 'Error al generar la Conección con SUNAT. Revise los datos ingresados.']);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json(false);
        }
    }
}
