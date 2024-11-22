<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeDocumentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('typedocuments')->insert([
            'description' => 'OTROS TIPOS DE DOCUMENTOS',
            'code' => 0
        ]);

        DB::table('typedocuments')->insert([
            'description' => 'DNI',
            'code' => 1
        ]);

        DB::table('typedocuments')->insert([
            'description' => 'CARNET DE EXTRANJERIA',
            'code' => 4
        ]);

        DB::table('typedocuments')->insert([
            'description' => 'RUC',
            'code' => 6
        ]);

        DB::table('typedocuments')->insert([
            'description' => 'PASAPORTE',
            'code' => 7
        ]);
        DB::table('typedocuments')->insert([
            'description' => 'VARIOS',
            'code' => 8
        ]);
    }
}
