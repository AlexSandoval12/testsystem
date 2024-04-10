<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseNoteCreditTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_note_credits', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('purchase_id');
            $table->foreign('purchase_id')->references('id')->on('purchases');

            $table->unsignedInteger('purchase_invoice_id');
            $table->foreign('purchase_invoice_id')->references('id')->on('purchases');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_note_credits');
    }
}
