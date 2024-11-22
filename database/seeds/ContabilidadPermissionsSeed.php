<?php

use Illuminate\Database\Seeder;
use Caffeinated\Shinobi\Models\Permission;

class ContabilidadPermissionsSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create([
            'name'          =>  'Interfaz Compras',
            'slug'          =>  'int.compras',
            'description'   =>  'Permite Generar interfaz de compras',
            'section' => 'contabilidad',
            'section_2' => 'contabilidad',
        ]);
        Permission::create([
            'name'          =>  'Interfaz Ventas',
            'slug'          =>  'int.ventas',
            'description'   =>  'Permite Generar interfaz de ventas',
            'section' => 'contabilidad',
            'section_2' => 'contabilidad',
        ]);
        Permission::create([
            'name'          =>  'Interfaz Cuentas por Cobrar',
            'slug'          =>  'int.cuentas',
            'description'   =>  'Permite Generar interfaz de cuentas por cobrar',
            'section' => 'contabilidad',
            'section_2' => 'contabilidad',
        ]);
        Permission::create([
            'name'          =>  'Cuentas Contables Productos',
            'slug'          =>  'int.cuentas.producto',
            'description'   =>  'Permite asignar las cuentas contables de los productos.',
            'section' => 'contabilidad',
            'section_2' => 'contabilidad',
        ]);
        Permission::create([
            'name'          =>  'Configuración de Cuentas Contables',
            'slug'          =>  'int.cuentas.contables',
            'description'   =>  'Permite configurar cuentas contables.',
            'section' => 'contabilidad',
            'section_2' => 'contabilidad',
        ]);
    }
}
