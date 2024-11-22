<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyTransferTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('transfers');

        Schema::create('transfers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('serie');
            $table->string('correlative');
            $table->text('responasble');
            $table->text('motive')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('warehouse_origin');
            $table->unsignedBigInteger('warehouse_destination');
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('warehouse_origin')->references('id')->on('warehouses');
            $table->foreign('warehouse_destination')->references('id')->on('warehouses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transfer', function (Blueprint $table) {
            //
        });
    }
}
