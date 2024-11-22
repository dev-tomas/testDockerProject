<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsInfoToClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('detraction', 20)->nullable()->unique();
            $table->boolean('invoice_size')->default(0);
            $table->boolean('retention_size')->default(0);
            $table->boolean('ticket_size')->default(0);
            $table->boolean('perception_size')->default(0);
            $table->boolean('price_type')->default(0);
            $table->boolean('automatic_consumption_surcharge')->default(0);
            $table->decimal('automatic_consumption_surcharge_price', 10, 2)->nullable();
            $table->boolean('jungle_region_goods')->default(0);
            $table->boolean('jungle_region_services')->default(0);
            $table->boolean('consumption_tax_plastic_bags')->default(0);
            $table->decimal('consumption_tax_plastic_bags_price', 10, 2)->nullable();
            $table->string('observation')->nullable();
            $table->boolean('issue_with_previous_data')->default(0);
            $table->boolean('issue_with_previous_data_days')->nullable()->default(2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            //
        });
    }
}
