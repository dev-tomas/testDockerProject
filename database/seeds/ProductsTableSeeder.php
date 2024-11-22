<?php

use App\Product;
use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::create([
            'description'       => 'Bolsa Plástica',
            'code'              => 'BP001',
            'internalcode'      => 'BP001',
            'price'             => '00.00',
            'status'            => '1',
            'coin_id'           =>  1,
            'operation_type'    =>  5,
            'client_id'         =>  0
        ]);
    }
}
