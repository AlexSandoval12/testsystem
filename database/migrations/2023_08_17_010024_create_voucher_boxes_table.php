<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVoucherBoxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voucher_boxes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('voucher_number');
            $table->unsignedInteger('branch_id');
            $table->integer('from_invoice_number')->nullable();
            $table->integer('until_invoice_number')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->foreign('branch_id')->references('id')->on('branches');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedInteger('stamped_id')->nullable();
            $table->foreign('stamped_id')->references('id')->on('stampeds');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('voucher_boxes');
    }
}
