<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientsTable extends Migration
{

    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('address');
            $table->string('neighborhood');
            $table->string('ruc');
            $table->string('razon_social');
            $table->integer('civil_status');
            $table->integer('document_number')->unique();
            $table->date('birth_date')->nullable();
            $table->tinyInteger('gender');

            $table->unsignedInteger('nationality_id');
            $table->foreign('nationality_id')->references('id')->on('nationalities');

            $table->longtext('observation')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('clients');
    }
}
