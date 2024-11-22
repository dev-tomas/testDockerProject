<?php

namespace App\Http\Controllers;

use DB;
use App\User;
use App\Brand;
Use App\Client;
use App\Store;
use App\Ubigeo;
use App\Product;
use App\Category;
use App\UserInfo;
use App\PriceList;
use App\Warehouse;
use App\Correlative;
use App\HeadQuarter;
use App\ProductPriceList;
use App\Mail\RegisterMail;
use Illuminate\Http\Request;
use Caffeinated\Shinobi\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\AjaxController;
use Caffeinated\Shinobi\Models\Permission;
use Illuminate\Support\Facades\Session;
use mysql_xdevapi\Table;

class ManageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('status.client');
        $this->ajax = new AjaxController();
    }

    public function index()
    {
        if (!auth()->user()->hasRole('superadmin')) {
            abort(401, 'Acción no autorizada.');
        }
        return view('manage.index');
    }

    public function supplant($ruc)
    {
        session()->forget('saou');
        $client = Client::where('document', $ruc)->first();
        if ($client == null) {
            toastr()->warning('Cliente no encontrado.');
            return back();
        }
        $userAdmin = User::where('client_id', $client->id)->where('ia', '1')->first();
        if ($userAdmin == null) {
            toastr()->warning('Usuario administrador no encontrado.');
            return back();
        }
        $superAdminUser = auth()->id();

        if ($userAdmin->id !== $superAdminUser) {
            session()->put('saou', $superAdminUser);

            auth()->login($userAdmin);
        }
        /**
         * Cambio a Local Principal
        */
        $headquarter= HeadQuarter::where('client_id', auth()->user()->headquarter->client_id)->get(['id', 'description'])->first();

        session()->forget('headlocal');
        $newLocal = $headquarter->id;
        session()->put('headlocal', $newLocal);
        /**
         * Fin cambio a Local Principal
        */

        return redirect()->route('home');
    }

    public function revertir()
    {
        $superAdminUser = session()->get('saou');
        auth()->loginUsingId($superAdminUser);
        session()->forget('saou');
        /**
         * Cambio a Local Principal
         */
        $headquarter= HeadQuarter::where('client_id', auth()->user()->headquarter->client_id)->get(['id', 'description'])->first();

        session()->forget('headlocal');
        $newLocal = $headquarter->id;
        session()->put('headlocal', $newLocal);
        /**
         * Fin cambio a Local Principal
         */
        return redirect()->route('mange.index');
    }
}
