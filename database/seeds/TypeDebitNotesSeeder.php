<?php

use App\TypeDebitNote;
use Illuminate\Database\Seeder;

class TypeDebitNotesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TypeDebitNote::create([
            'description'   =>  'Intereses por mora',
            'code'          =>  '01'
        ]);

        TypeDebitNote::create([
            'description'   =>  'Aumento en valor',
            'code'          =>  '02'
        ]);

        TypeDebitNote::create([
            'description'   =>  'Penalidades',
            'code'          =>  '03'
        ]);
    }
}
