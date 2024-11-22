<?php

use Illuminate\Database\Seeder;
use App\TypeOperation;

class TypeOperationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TypeOperation::create([
            'operation'          =>  'VENTA INTERNA'
        ]);
        TypeOperation::create([
            'operation'          =>  'ANTICIPO O DEDUCCIÓN DE ANTICIPO EN VENTA INTERNA'
        ]);
        TypeOperation::create([
            'operation'          =>  'EXPORTACIÓN'
        ]);
        TypeOperation::create([
            'operation'          =>  'VENTAS NO DOMICILIADOS QUE NO CALIFICAN COMO EXPORTACIÓN'
        ]);
        TypeOperation::create([
            'operation'          =>  'OPERACIÓN SUJETA A DETRACCIÓN'
        ]);
        TypeOperation::create([
            'operation'          =>  'OPERACIÓN SUJETA A DETRACCIÓN-SERVICIOS DE TRANSPORTE CARGA'
        ]);
    }
}
