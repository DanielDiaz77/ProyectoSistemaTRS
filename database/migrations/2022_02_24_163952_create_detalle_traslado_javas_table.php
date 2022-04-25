<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetalleTrasladoJavasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_traslado_javas', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('idtrasladojava');
            $table->foreign('idtrasladojava')->references('id')->on('traslado_javas')->onDelete('cascade');

            $table->unsignedBigInteger('idjava');
            $table->foreign('idjava')->references('id')->on('javas');

            $table->integer('cantidad');
            $table->string('ubicacion',50);
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
        Schema::dropIfExists('detalle_traslado_javas');
    }
}
