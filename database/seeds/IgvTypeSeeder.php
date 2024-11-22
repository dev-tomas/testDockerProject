<?php

use Illuminate\Database\Seeder;
use App\IgvType;

class IgvTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        IgvType::create([
            'description'  =>  'Gravado - Operación Onerosa',
            'code'  =>  '10',
        ]);
        IgvType::create([
            'description'  =>  '[Gratuita] Gravado – Retiro por premio',
            'code'  =>  '11',
        ]);
        IgvType::create([
            'description'  =>  '[Gratuita] Gravado – Retiro por donación',
            'code'  =>  '12',
        ]);
        IgvType::create([
            'description'  =>  '[Gratuita] Gravado – Retiro',
            'code'  =>  '13',
        ]);
        IgvType::create([
            'description'  =>  '[Gratuita] Gravado – Retiro por publicidad',
            'code'  =>  '14',
        ]);
        IgvType::create([
            'description'  =>  '[Gratuita] Gravado – Bonificaciones',
            'code'  =>  '15',
        ]);
        IgvType::create([
            'description'  =>  '[Gratuita] Gravado – Retiro por entrega a trabajadores',
            'code'  =>  '16',
        ]);
        IgvType::create([
            'description'  =>  'Exonerado - Operación Onerosa',
            'code'  =>  '20',
        ]);
        IgvType::create([
            'description'  =>  'Inafecto - Operación Onerosa',
            'code'  =>  '30',
        ]);
        IgvType::create([
            'description'  =>  '[Gratuita] Inafecto – Retiro por Bonificación',
            'code'  =>  '31',
        ]);
        IgvType::create([
            'description'  =>  '[Gratuita] Inafecto – Retiro',
            'code'  =>  '32',
        ]);
        IgvType::create([
            'description'  =>  '[Gratuita] Inafecto – Retiro por Muestras Médicas',
            'code'  =>  '33',
        ]);
        IgvType::create([
            'description'  =>  '[Gratuita] Inafecto - Retiro por Convenio Colectivo',
            'code'  =>  '33',
        ]);
        IgvType::create([
            'description'  =>  '[Gratuita] Inafecto – Retiro por premio',
            'code'  =>  '35',
        ]);
        IgvType::create([
            'description'  =>  '[Gratuita] Inafecto - Retiro por publicidad',
            'code'  =>  '36',
        ]);
        IgvType::create([
            'description'  =>  'Exportación',
            'code'  =>  '40',
        ]);
        IgvType::create([
            'description'  =>  '[Gratuita] Exonerado - Transferencia gratuita',
            'code'  =>  '21',
        ]);
    }
}


