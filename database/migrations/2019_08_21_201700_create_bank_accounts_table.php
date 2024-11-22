<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBankAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('number', 20)->unique();
            $table->string('bank_name', 30);
            $table->string('headline', 40);
            $table->string('cci', 4);
            $table->string('observation', 255);
            $table->unsignedBigInteger('coin_id');
            $table->unsignedBigInteger('bank_account_type_id');
            $table->unsignedBigInteger('client_id');
            $table->timestamps();

            $table->foreign('coin_id')->references('id')->on('coins');
            $table->foreign('bank_account_type_id')->references('id')->on('bank_account_types');
            $table->foreign('client_id')->references('id')->on('clients');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bank_accounts');
    }
}
