<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->increments('id');

            $table->date('date');

            $table->integer('number');

            $table->unsignedInteger('branch_id');
            $table->foreign('branch_id')->references('id')->on('branches');


            $table->unsignedTinyInteger('condition');

            $table->unsignedInteger('provider_id');
            $table->foreign('provider_id')->references('id')->on('providers');

            $table->string('razon_social');
            $table->string('ruc');
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->longText('observation')->nullable();
            $table->decimal('amount',11,2);
            $table->boolean('status')->default(true);

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

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
        Schema::dropIfExists('purchase_orders');
    }
}
