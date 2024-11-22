<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDebitNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('debit_notes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('serial_number', 20);
            $table->string('correlative', 15);
            $table->decimal('total', 10,2);
            $table->date('date_issue')->default(now());
            $table->date('due_date')->default(now());
            $table->timestamps();
            $table->string('observation', 250)->nullable();
            $table->boolean('status_sunat')->default(0);
            $table->boolean('status')->default(1);
            $table->boolean('send_customer')->default(0);
            $table->unsignedBigInteger('sale_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('typevoucher_id');
            $table->unsignedBigInteger('headquarter_id');
            $table->unsignedBigInteger('response_sunat')->nullable();
            $table->unsignedBigInteger('type_debit_note_id');
            $table->foreign('sale_id')->references('id')->on('sales');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('headquarter_id')->references('id')->on('headquarters');
            $table->foreign('response_sunat')->references('id')->on('sunat_codes');
            $table->foreign('type_debit_note_id')->references('id')->on('type_debit_notes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('debit_notes');
    }
}
