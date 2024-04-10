<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVoucherDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('voucher_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('voucher_id');
            $table->foreign('voucher_id')->references('id')->on('vouchers');
            $table->unsignedInteger('service_invoice_id');
            $table->foreign('service_invoice_id')->references('id')->on('service_invoices');
            $table->string('description');
            $table->integer('quantity');
            $table->decimal('amount',11,2);
            $table->decimal('excenta',11,2)->nullable();
            $table->decimal('iva5',11,2)->nullable();
            $table->decimal('iva10',11,2)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('voucher_details');
    }
}
