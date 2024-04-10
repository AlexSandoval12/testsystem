<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseBudgetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_budgets', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('wish_purchase_id');
            $table->foreign('wish_purchase_id')->references('id')->on('wish_purchases');

            $table->unsignedBigInteger('confirmation_user_id')->nullable();
            $table->foreign('confirmation_user_id')->references('id')->on('users');

            $table->datetime('confirmation_date')->nullable();
            $table->string('name');
            $table->string('original_name');
            $table->integer('status')->default(1);

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
        Schema::dropIfExists('purchase_budgets');
    }
}
