<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewColumnsToReferenceGuidesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reference_guides', function (Blueprint $table) {
            $table->string('ticket', 200)->nullable();
            $table->timestamp('reception_date')->nullable();
            $table->string('weight_measure', 10)->default('KGM')->after('weight');
            $table->string('sunat_code_start')->nullable()->after('start_address');
            $table->string('sunat_code_arrival')->nullable()->after('arrival_address');
            $table->string('driver_firstname')->nullable()->after('driver_name');
            $table->string('driver_familyname')->nullable()->after('driver_firstname');
            $table->string('driver_license')->nullable()->after('driver_familyname');
            $table->char('has_cdr', 1)->default(1);
            $table->string('mtc_register_code')->nullable();
            $table->string('mtc_register_number')->nullable();
            $table->string('mtc_register')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reference_guides', function (Blueprint $table) {

        });
    }
}
