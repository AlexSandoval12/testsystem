<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesCollectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases_collects', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('purchase_id');
            $table->foreign('purchase_id')->references('id')->on('purchases');
            $table->integer('number');
            $table->date('expiration');
            $table->integer('amount');
            $table->integer('residue');
            
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
        Schema::dropIfExists('purchases_collects');
    }
}
