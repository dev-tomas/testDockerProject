<?php

use App\Regime;
use Illuminate\Database\Seeder;

class RegimesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Regime::create([
            'description'   =>  'TASA 3%',
            'rate'          =>  2,
            'type'          =>  0,
            'code'          =>  '01'
        ]);

        Regime::create([
            'description'   =>  'PERCEPCION VENTA INTERNA ',
            'rate'          =>  0.5,
            'type'          =>  1,
            'code'          =>  '01'
        ]);

        Regime::create([
            'description'   =>  'PERCEPCION A LA ADQUISICION DE COMBUSTIBLE',
            'rate'          =>  0.5,
            'type'          =>  1,
            'code'          =>  '02'
        ]);

        Regime::create([
            'description'   =>  'PERCEPCION REALIZADA AL AGENTE DE PERCEPCION CON TASA ESPECIAL',
            'rate'          =>  2,
            'type'          =>  1,
            'code'          =>  '03'
        ]);
    }
}
