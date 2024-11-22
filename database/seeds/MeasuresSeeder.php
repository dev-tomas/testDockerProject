<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MeasuresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('measures')->insert([
            'description' => 'KILOGRAMOS',
            'code' => '01'
        ]);

        DB::table('measures')->insert([
            'description' => 'LIBRAS',
            'code' => '02'
        ]);

        DB::table('measures')->insert([
            'description' => 'TONELADAS LARGAS',
            'code' => '03'
        ]);

        DB::table('measures')->insert([
            'description' => 'TONELADAS MÉTRICAS',
            'code' => '04'
        ]);

        DB::table('measures')->insert([
            'description' => 'TONELADAS CORTAS',
            'code' => '05'
        ]);

        DB::table('measures')->insert([
            'description' => 'GRAMOS',
            'code' => '06'
        ]);

        DB::table('measures')->insert([
            'description' => 'UNIDADES',
            'code' => '07'
        ]);

        DB::table('measures')->insert([
            'description' => 'LITROS',
            'code' => '08'
        ]);

        DB::table('measures')->insert([
            'description' => 'GALONES',
            'code' => '09'
        ]);

        DB::table('measures')->insert([
            'description' => 'BARRILES',
            'code' => '10'
        ]);

        DB::table('measures')->insert([
            'description' => 'LATAS',
            'code' => '11'
        ]);

        DB::table('measures')->insert([
            'description' => 'CAJAS',
            'code' => '12'
        ]);

        DB::table('measures')->insert([
            'description' => 'MILLARES',
            'code' => '13'
        ]);

        DB::table('measures')->insert([
            'description' => 'METROS CÚBICOS',
            'code' => '14'
        ]);

        DB::table('measures')->insert([
            'description' => 'METROS',
            'code' => '15'
        ]);

        DB::table('measures')->insert([
            'description' => 'OTROS (ESPECIFICAR)',
            'code' => '99'
        ]);
    }
}
