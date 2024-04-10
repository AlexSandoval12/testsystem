<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseMovements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_movements', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('status');
            $table->string('type_operation');
            $table->string('type_movement');
            $table->string('recived_person');   
            $table->string('observation');   
            $table->string('invoice_number');   
            $table->string('invoice_condition');   
            $table->string('invoice_stamped');
            $table->string('stamp_validity');
            $table->string('reason_deleted');   
            $table->date('invoice_date');
            $table->date('date_payment');
            $table->date('date_deleted');

            $table->unsignedInteger('branch_id');
            $table->foreign('branch_id')->references('id')->on('branches');

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
        Schema::dropIfExists('purchase_movements');
    }
}
