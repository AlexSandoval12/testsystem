<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseMovementsDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_movements_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('quantity');
            $table->integer('affects_stock');
            $table->decimal('price_cost', 13,2)->nullable();

            $table->unsignedInteger('purchase_movement_id');
            $table->foreign('purchase_movement_id')->references('id')->on('purchase_movements');

            $table->unsignedInteger('purchases_order_detail_id');
            $table->foreign('purchases_order_detail_id')->references('id')->on('purchase_order_details');

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
        Schema::dropIfExists('purchase_movements_details');
    }
}
