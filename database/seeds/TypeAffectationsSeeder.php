<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeAffectationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('typeaffectations')->insert([
            'description' => 'Gravado - Operación Onerosa',
            'code' => '10'
        ]);

        DB::table('typeaffectations')->insert([
            'description' => 'Gravado – Retiro por premio',
            'code' => '11'
        ]);

        DB::table('typeaffectations')->insert([
            'description' => 'Gravado – Retiro por donación',
            'code' => '12'
        ]);

        DB::table('typeaffectations')->insert([
            'description' => 'Gravado – Retiro',
            'code' => '13'
        ]);

        DB::table('typeaffectations')->insert([
            'description' => 'Gravado – Retiro por publicidad',
            'code' => '14'
        ]);

        DB::table('typeaffectations')->insert([
            'description' => 'Gravado – Bonificaciones',
            'code' => '15'
        ]);

        DB::table('typeaffectations')->insert([
            'description' => 'Gravado – Retiro por entrega a trabajadores',
            'code' => '16'
        ]);

        DB::table('typeaffectations')->insert([
            'description' => 'Gravado – IVAP',
            'code' => '17'
        ]);

        DB::table('typeaffectations')->insert([
            'description' => 'Exonerado - Operación Onerosa',
            'code' => '20'
        ]);

        DB::table('typeaffectations')->insert([
            'description' => 'Exonerado – Transferencia Gratuita',
            'code' => '21'
        ]);

        DB::table('typeaffectations')->insert([
            'description' => 'Inafecto - Operación Onerosa',
            'code' => '30'
        ]);

        DB::table('typeaffectations')->insert([
            'description' => 'Inafecto – Retiro por Bonificación',
            'code' => '31'
        ]);

        DB::table('typeaffectations')->insert([
            'description' => 'Inafecto – Retiro',
            'code' => '32'
        ]);

        DB::table('typeaffectations')->insert([
            'description' => 'Inafecto – Retiro por Muestras Médicas',
            'code' => '33'
        ]);

        DB::table('typeaffectations')->insert([
            'description' => 'Inafecto - Retiro por Convenio Colectivo',
            'code' => '34'
        ]);

        DB::table('typeaffectations')->insert([
            'description' => 'Inafecto – Retiro por premio',
            'code' => '35'
        ]);

        DB::table('typeaffectations')->insert([
            'description' => 'Inafecto - Retiro por publicidad',
            'code' => '36'
        ]);

        DB::table('typeaffectations')->insert([
            'description' => 'Exportación',
            'code' => '40'
        ]);
        //no gravadas -008
        DB::table('typeaffectations')->insert([
            'description' => 'No gravadas',
            'code' => '41'
        ]);
    }
}
