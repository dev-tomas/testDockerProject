<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequirementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requirements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('correlative',10);
            $table->unsignedBigInteger('warehouse_id');
            $table->string('requested')->nullable();
            $table->string('authorized')->nullable();
            $table->char('type_requirement');
            $table->unsignedBigInteger('centercost_id');
            $table->char('status');
            $table->decimal('total', 10,2);
            $table->timestamps();

            $table->foreign('warehouse_id')->references('id')->on('warehouses');
            $table->foreign('centercost_id')->references('id')->on('costs_center');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requirements');
    }
}
