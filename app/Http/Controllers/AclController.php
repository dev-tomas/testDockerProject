<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 22/05/2019
 * Time: 14:10
 */

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\User;
class AclController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('acl/types');
    }

    public function users()
    {
        return view('acl/users');
    }

    public function roles()
    {
        return view('acl/roles');
    }

    /**
     * Métodos para construir DataTable
     */
    public function getRoleUserDataTable()
    {
        return datatables()->of(User::query())->make(true);
    }
}