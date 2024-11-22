<?php

use Illuminate\Database\Seeder;
use Caffeinated\Shinobi\Models\Role;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create([
            'name'      =>  'Super Administrador',
            'slug'      =>  'superadmin',
            'special'   =>  'all-access',
        ]);
    }
}
