<?php

use Illuminate\Database\Seeder;
use App\PriceList;

class PriceListsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PriceList::create([
            'description' => 'PRECIO MAYORISTA',
            'state' => '1',
        ]);
        PriceList::create([
            'description' => 'PRECIO POR CAJA',
            'state' => '1',
        ]);
        PriceList::create([
            'description' => 'PRECIO POR UNIDAD',
            'state' => '1',
        ]);
    }
}
