<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('start');
            $table->dateTime('end');
            $table->string('title',50);
            $table->string('content',256)->nullable();
            $table->string('class',50);
            $table->boolean('estado')->default(0);
            $table->unsignedBigInteger('idusuario')->nullable();
            $table->foreign('idusuario')->references('id')->on('users');
            $table->unsignedBigInteger('idcliente')->nullable();
            $table->foreign('idcliente')->references('id')->on('personas');
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
        Schema::dropIfExists('events');
    }
}
