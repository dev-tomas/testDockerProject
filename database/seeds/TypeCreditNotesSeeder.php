<?php

use App\TypeCreditNote;
use Illuminate\Database\Seeder;

class TypeCreditNotesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TypeCreditNote::create([
            'description'   =>  'Anulación de la Operación',
            'code'          =>  '01'
        ]);

        TypeCreditNote::create([
            'description'   =>  'Anulación por error en el RUC',
            'code'          =>  '02'
        ]);

        TypeCreditNote::create([
            'description'   =>  'Corrección por error en la descripción',
            'code'          =>  '03'
        ]);

        TypeCreditNote::create([
            'description'   =>  'Descuento global',
            'code'          =>  '04'
        ]);

        TypeCreditNote::create([
            'description'   =>  'Descuento por ítem',
            'code'          =>  '05'
        ]);

        TypeCreditNote::create([
            'description'   =>  'Devolución total',
            'code'          =>  '06'
        ]);

        TypeCreditNote::create([
            'description'   =>  'Devolución por ítem',
            'code'          =>  '07'
        ]);

        TypeCreditNote::create([
            'description'   =>  'Bonificación',
            'code'          =>  '08'
        ]);

        TypeCreditNote::create([
            'description'   =>  'Disminución en el valor',
            'code'          =>  '09'
        ]);

        TypeCreditNote::create([
            'description'   =>  'Otro Concepto',
            'code'          =>  '10'
        ]);
    }
}
