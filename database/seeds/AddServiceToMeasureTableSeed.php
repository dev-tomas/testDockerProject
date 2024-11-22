<?php

use App\Measure;
use Illuminate\Database\Seeder;

class AddServiceToMeasureTableSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Measure::create([
            'description' => 'SERVICIOS',
            'code' => '16'
        ]);
    }
}
