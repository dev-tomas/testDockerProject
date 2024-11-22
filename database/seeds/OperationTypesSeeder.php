<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OperationTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('operations_type')->insert([
            'description'   =>  'PRODUCTOS',
            'code'          =>  'NIU'
        ]);

        DB::table('operations_type')->insert([
            'description'   =>  'SERVICIOS',
            'code'          =>  'ZZ'
        ]);

        DB::table('operations_type')->insert([
            'description'   =>  'KILOGRAMO',
            'code'          =>  'KGM'
        ]);

        DB::table('operations_type')->insert([
            'description'   =>  'CAJA',
            'code'          =>  'BX'
        ]);

        DB::table('operations_type')->insert([
            'description'   =>  'BOLSA',
            'code'          =>  'BG'
        ]);

        DB::table('operations_type')->insert([
            'description'   =>  'DOCENA',
            'code'          =>  'DZN'
        ]);

        DB::table('operations_type')->insert([
            'description'   =>  'GALON',
            'code'          =>  'WG'
        ]);

        DB::table('operations_type')->insert([
            'description'   =>  'BARRIL',
            'code'          =>  'KG'
        ]);

        DB::table('operations_type')->insert([
            'description'   =>  'GRAMO',
            'code'          =>  'GRM'
        ]);

        DB::table('operations_type')->insert([
            'description'   =>  'TONELADA',
            'code'          =>  'TNE'
        ]);

        DB::table('operations_type')->insert([
            'description'   =>  'PAR',
            'code'          =>  'PR'
        ]);

        DB::table('operations_type')->insert([
            'description'   =>  'ROLLO',
            'code'          =>  'RO'
        ]);

        DB::table('operations_type')->insert([
            'description'   =>  'METRO',
            'code'          =>  'MTR'
        ]);

        DB::table('operations_type')->insert([
            'description'   =>  'PAQUETE',
            'code'          =>  'PK'
        ]);

        DB::table('operations_type')->insert([
            'description'   =>  'BOTELLA',
            'code'          =>  'BO'
        ]);

        DB::table('operations_type')->insert([
            'description'   =>  'METRO CUBICO',
            'code'          =>  'MTQ'
        ]);

        DB::table('operations_type')->insert([
            'description'   =>  'MIL',
            'code'          =>  'MIL'
        ]);

        DB::table('operations_type')->insert([
            'description'   =>  'SACO',
            'code'          =>  'SA'
        ]);

        DB::table('operations_type')->insert([
            'description'   =>  'LITRO',
            'code'          =>  'LTR'
        ]);

        DB::table('operations_type')->insert([
            'description'   =>  'JAR',
            'code'          =>  'JR'
        ]);
        DB::table('operations_type')->insert([
            'description'   =>  'CIENTO',
            'code'          =>  'CEN'
        ]);
        DB::table('operations_type')->insert([
            'description'   =>  'BOLSA DE PLASTICO',
            'code'          =>  'BGP'
        ]);
    }
}
