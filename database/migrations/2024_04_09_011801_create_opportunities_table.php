<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpportunitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('opportunities', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('contact_medium_id');
            $table->foreign('contact_medium_id')->references('id')->on('contact_mediums');

            $table->string('fullname');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->unsignedInteger('document_number')->nullable();

            $table->longtext('observation')->nullable();

            $table->integer('status');

            $table->unsignedBigInteger('seller_id');
            $table->foreign('seller_id')->references('id')->on('users');

            // $table->unsignedInteger('insurance_id')->nullable();
            // $table->foreign('insurance_id')->references('id')->on('insurances');
            $table->unsignedBigInteger('city_id')->nullable();
            $table->string('address')->nullable();
            $table->foreign('city_id')->references('id')->on('ciudades');
            $table->unsignedInteger('type_plan')->nullable();

            $table->string('lead')->nullable();

            $table->datetime('processed_at')->nullable();
            $table->datetime('closed_at')->nullable();
            $table->datetime('selled_at')->nullable();
            $table->datetime('rejected_at')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->datetime('deleted_at')->nullable();
            $table->longtext('deleted_reason')->nullable();

            $table->unsignedBigInteger('deleted_user_id')->nullable();
            $table->foreign('deleted_user_id')->references('id')->on('users');
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
        Schema::dropIfExists('opportunities');
    }
}
