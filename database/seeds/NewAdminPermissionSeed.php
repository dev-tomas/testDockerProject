<?php

use Illuminate\Database\Seeder;
use Caffeinated\Shinobi\Models\Permission;

class NewAdminPermissionSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create([
            'name'          =>  'Ver todas lass Ventas',
            'slug'          =>  'ventas.all',
            'description'   =>  'Lista y Muestra todas las Ventas del Sistema.',
            'section' => 'acomercial',
            'section_2' => 'ventas'
        ]);
    }
}
