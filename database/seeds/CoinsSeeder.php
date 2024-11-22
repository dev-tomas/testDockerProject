<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CoinsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('coins')->insert([
            'description' => 'SOLES',
            'symbol' => 'S/',
            'code' => 1,
            'code_str' => 'PEN'
        ]);

        DB::table('coins')->insert([
            'description' => 'DÓLARES AMERICANOS',
            'symbol' => '$',
            'code' => 2,
            'code_str' => 'USD'
        ]);

        DB::table('coins')->insert([
            'description' => 'EURO',
            'symbol' => '€',
            'code' => 9,
            'code_str' => 'EUR'
        ]);
    }
}
