<?php

use Illuminate\Database\Seeder;
use Caffeinated\Shinobi\Models\Permission;

class CashPermissionSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create([
            'name'          =>  'Cajas',
            'slug'          =>  'cashes',
            'description'   =>  'Permite listar y dar mantenimiento las cajas.',
            'section' => 'acomercial',
            'section_2' => 'ventas',
        ]);
        Permission::create([
            'name'          =>  'POS',
            'slug'          =>  'pos',
            'description'   =>  'Permite acceder al Punto de Venta.',
            'section' => 'acomercial',
            'section_2' => 'ventas',
        ]);
    }
}
