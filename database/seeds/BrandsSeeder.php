<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('brands')->insert([
            'description' => 'HP'
        ]);

        DB::table('brands')->insert([
            'description' => 'HALION'
        ]);
    }
}
