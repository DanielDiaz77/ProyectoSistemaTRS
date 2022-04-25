<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetalleIngresoJavasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_ingreso_javas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('idingresojava');

            $table->foreign('idingresojava')->references('id')->on('ingreso_javas')->onDelete('cascade');
            $table->unsignedBigInteger('idjava');

            $table->foreign('idjava')->references('id')->on('javas');
            $table->integer('cantidad');

            $table->decimal('precio_compra', 11, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detalle_ingreso_javas');
    }
}
