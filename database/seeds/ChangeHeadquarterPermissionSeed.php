<?php

use Illuminate\Database\Seeder;
use Caffeinated\Shinobi\Models\Permission;

class ChangeHeadquarterPermissionSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create([
            'name'          =>  'Cambiar entre locales',
            'slug'          =>  'change.headquarter',
            'description'   =>  'Permite cambiar al usuario entre locales.',
            'section' => 'configuraciones',
            'section_2' => 'usuarios',
        ]);
    }
}
