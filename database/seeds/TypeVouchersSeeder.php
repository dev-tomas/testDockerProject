<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeVouchersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('typevouchers')->insert([
            'description' => 'FACTURA',
            'code' => '01',
            'visible'   => 1,
        ]);

        DB::table('typevouchers')->insert([
            'description' => 'BOLETA DE VENTA',
            'code' => '03',
            'visible'   => 1,
        ]);

        DB::table('typevouchers')->insert([
            'description' => 'NOTA DE CRÉDITO DE BOLETA',
            'code' => '07',
            'visible'   => 1,
        ]);

        DB::table('typevouchers')->insert([
            'description' => 'NOTA DE CRÉDITO DE FACTURA',
            'code' => '07',
            'visible'   => 1,
        ]);

        DB::table('typevouchers')->insert([
            'description' => 'NOTA DE DÉBITO DE BOLETA',
            'code' => '08',
            'visible'   => 1,
        ]);

        DB::table('typevouchers')->insert([
            'description' => 'NOTA DE DÉBITO DE FACTURA',
            'code' => '08',
            'visible'   => 1,
        ]);

        DB::table('typevouchers')->insert([
            'description' => 'GUÍA DE REMISIÓN REMITENTE',
            'code' => '09',
            'visible'   => 1,
        ]);

        DB::table('typevouchers')->insert([
            'description' => 'TICKET DE MAQUINA REGISTRADORA',
            'code' => '12',
            'visible'   => 0,
        ]);

        DB::table('typevouchers')->insert([
            'description' => 'DOCUMENTO EMITIDO POR BANCOS, ETC',
            'code' => '',
            'visible'   => 0,
        ]);

        DB::table('typevouchers')->insert([
            'description' => 'DOCUMENTOS EMITIDOS POR LAS AFP',
            'code' => '18',
            'visible'   => 0,
        ]);

        DB::table('typevouchers')->insert([
            'description' => 'GUIA DE REMISIÓN TRANSPORTISTA',
            'code' => '31',
            'visible'   => 0,
        ]);

        DB::table('typevouchers')->insert([
            'description' => 'COMPROBANTE DE PAGO SEAE',
            'code' => '56'
        ]);

        DB::table('typevouchers')->insert([
            'description' => 'COTIZACIÓN',
            'code' => '00',
            'visible'   => 0,
        ]);

        DB::table('typevouchers')->insert([
            'description' => 'COMPRA',
            'code' => '99',
            'visible'   => 0,
        ]);

        DB::table('typevouchers')->insert([
            'description' => 'ORDEN DE COMPRA',
            'code' => '13',
            'visible'   => 0,
        ]);

        DB::table('typevouchers')->insert([
            'description' => 'REQUERIMIENTO',
            'code' => '10',
            'visible'   => 0,
        ]);

        DB::table('typevouchers')->insert([
            'description' => 'COMPROBANTE DE PERCEPCIÓN',
            'code' => '40',
            'visible'   => 1,
        ]);

        DB::table('typevouchers')->insert([
            'description' => 'COMPROBANTE DE RETENCIÓN',
            'code' => '20',
            'visible'   => 1,
        ]);

        DB::table('typevouchers')->insert([
            'description' => 'COMUNICACIÓN DE BAJA',
            'code' => '98',
            'visible'   => 0,
        ]);

        DB::table('typevouchers')->insert([
            'description' => 'INGRESOS',
            'code' => '96',
            'visible'   => 0,
        ]);

        DB::table('typevouchers')->insert([
            'description' => 'TRANSFERENCIAS',
            'code' => '97',
            'visible'   => 0,
        ]);

        DB::table('typevouchers')->insert([
            'description' => 'RESUMEN DIARIO DE BOLETAS',
            'code' => '95',
            'visible'   => 0,
        ]);

        DB::table('typevouchers')->insert([
            'description' => 'RETENCIÓN',
            'code' => '94',
            'visible'   => 0,
        ]);

        DB::table('typevouchers')->insert([
            'description' => 'PERCEPCIÓN',
            'code' => '93',
            'visible'   => 0,
        ]);

        DB::table('typevouchers')->insert([
            'description' => 'RECIBO POR HONORARIOS',
            'code' => '',
            'visible'   => 1,
        ]);
    }
}
