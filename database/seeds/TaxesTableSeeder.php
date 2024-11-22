<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaxesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('taxes')->insert([
            'description' => 'IGV',
            'value' =>  18
        ]);
    }
}
