<?php

use Illuminate\Database\Seeder;
use App\BankAccountType;

class BankAccountTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        BankAccountType::create([
            'description'   =>  'CUENTA CORRIENTE',
            'code'          =>  '01'
        ]);

        BankAccountType::create([
            'description'   =>  'CUENTA DE AHORROS',
            'code'          =>  '02'
        ]);

        BankAccountType::create([
            'description'   =>  'CUENTA DE DETRACCIONES',
            'code'          =>  '03'
        ]);
    }
}
