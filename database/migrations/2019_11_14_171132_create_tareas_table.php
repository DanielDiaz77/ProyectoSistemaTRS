<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTareasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tareas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre',50);
            $table->string('descripcion',256)->nullable();
            $table->string('tipo',20);
            $table->date('fecha')->nullable();
            $table->boolean('estado')->default(0);

            $table->unsignedBigInteger('idusuario');
            $table->foreign('idusuario')->references('id')->on('users');

            $table->unsignedBigInteger('idcliente');
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
        Schema::dropIfExists('tareas');
    }
}
