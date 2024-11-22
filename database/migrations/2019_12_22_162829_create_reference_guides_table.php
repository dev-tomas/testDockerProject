<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReferenceGuidesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reference_guides', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('serialnumber');
            $table->string('correlative');
            $table->date('date');
            $table->date('traslate');
            $table->char('guide_type',1);
            $table->char('motive',1);
            $table->char('modality',1);
            $table->decimal('weight',10,2);
            $table->text('receiver');
            $table->string('receiver_document',15);
            $table->text('start_address');
            $table->text('arrival_address');
            $table->string('transport_name', 255);
            $table->string('transport_document', 15);
            $table->string('driver_document', 15);
            $table->string('driver_name', 255);
            $table->string('vehicle');
            $table->unsignedBigInteger('transport_type_document_id');
            $table->unsignedBigInteger('driver_type_document_id');
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('headquarter_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('typevoucher_id');
            $table->timestamps();

            $table->foreign('transport_type_document_id')->references('id')->on('typedocuments');
            $table->foreign('driver_type_document_id')->references('id')->on('typedocuments');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('headquarter_id')->references('id')->on('headquarters');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('typevoucher_id')->references('id')->on('typevouchers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reference_guides');
    }
}
