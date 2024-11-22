<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Caffeinated\Shinobi\Models\Role;
use Caffeinated\Shinobi\Models\Permission;
use DB;

class RolesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('status.client');
        $this->middleware('can:roles.show')->only(['index','getRoles']);
        $this->middleware('can:roles.create')->only(['create','store']);
        $this->middleware('can:roles.edit')->only(['edit','update']);
        $this->middleware('can:roles.delete')->only(['destroy']);
    }

    public function index()
    {
        return view('roles.index');
    }

    public function getRoles()
    {
        return datatables()->of(
            Db::table('roles')
            ->where('client_id', auth()->user()->client_id)
            ->get([
                'name',
                'id',
                'administrable'
            ])
        )->toJson();
    }

    public function create()
    {
        $permissions = Permission::get();
        $pmenu = Permission::where('section','menu')->get();
        $pcotizaciones = Permission::where('section','acomercial')->where('section_2','cotizaciones')->get();
        $pventas = Permission::where('section','acomercial')->where('section_2','ventas')->get();
        $pclientes = Permission::where('section','acomercial')->where('section_2','clientes')->get();
        $pproveedores = Permission::where('section','alogistica')->where('section_2','proveedores')->get();
        $pservicios = Permission::where('section','alogistica')->where('section_2','pservicios')->get();
        $pcategorias = Permission::where('section','alogistica')->where('section_2','categorias')->get();
        $palmacenes = Permission::where('section','almacen')->where('section_2','almacenes')->get();
        // $pcorrelativos = Permission::where('section','configuraciones')->where('section_2','correlativos')->get();
        $pempresa = Permission::where('section','configuraciones')->where('section_2','empresa')->get();
        $plocalserie = Permission::where('section','configuraciones')->where('section_2','localserie')->get();
        $papariencia = Permission::where('section','configuraciones')->where('section_2','apariencia')->get();
        $pusuarios = Permission::where('section','configuraciones')->where('section_2','usuarios')->get();
        $proles = Permission::where('section','configuraciones')->where('section_2','roles')->get();

        return view('roles.create', compact('permissions','pmenu','pcotizaciones','pventas','pclientes','pproveedores','pservicios','pcategorias','palmacenes','pempresa','plocalserie','papariencia','pusuarios','proles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->role_id == null) {
            $role = new Role;
            $role->name = $request->name;
            $role->slug = $request->name;
            $role->description = $request->description;
            $role->client_id = auth()->user()->client_id;
            $role->save();
            $role->permissions()->sync($request->get('permissions'));

            toastr()->success('El role fue creado correctamente.', 'Role Creado');

            echo json_encode(true);
        } else {
            $role = Role::find($request->role_id);
            $role->name = $request->name;
            $role->description = $request->description;
            $role->update();
            $role->permissions()->sync($request->get('permissions'));
            echo json_encode(true);
        }
    }

    public function prepare(Request $request)
    {
        $role = Role::find($request->get('role_id'));

        echo json_encode($role);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        return view('roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        $permissions = Permission::get();
        $pmenu = Permission::where('section','menu')->get();
        $pcotizaciones = Permission::where('section','acomercial')->where('section_2','cotizaciones')->get();
        $pventas = Permission::where('section','acomercial')->where('section_2','ventas')->get();
        $pclientes = Permission::where('section','acomercial')->where('section_2','clientes')->get();
        $pproveedores = Permission::where('section','alogistica')->where('section_2','proveedores')->get();
        $pservicios = Permission::where('section','alogistica')->where('section_2','pservicios')->get();
        $pcategorias = Permission::where('section','alogistica')->where('section_2','categorias')->get();
        $palmacenes = Permission::where('section','almacen')->where('section_2','almacenes')->get();
        // $pcorrelativos = Permission::where('section','configuraciones')->where('section_2','correlativos')->get();
        $pempresa = Permission::where('section','configuraciones')->where('section_2','empresa')->get();
        $plocalserie = Permission::where('section','configuraciones')->where('section_2','localserie')->get();
        $papariencia = Permission::where('section','configuraciones')->where('section_2','apariencia')->get();
        $pusuarios = Permission::where('section','configuraciones')->where('section_2','usuarios')->get();
        $proles = Permission::where('section','configuraciones')->where('section_2','roles')->get();

        return view('roles.edit', compact('permissions', 'role','pmenu','pcotizaciones','pventas','pclientes','pproveedores','pservicios','pcategorias','palmacenes','pempresa','plocalserie','papariencia','pusuarios','proles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        // Actualizar un role
        $role->update($request->all());

        // Actualizar permisos
        $role->permissions()->sync($request->get('permissions'));
        toastr()->success('El role fue actualizado correctamente.', 'Role Actualizado');
        return redirect()->route('roles.edit', $role->id);
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $role->delete();

        echo json_encode(true);
    }
}
