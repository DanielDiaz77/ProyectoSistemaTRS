<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetalleIngresoBloquesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_ingreso_bloques', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('idingresobloque');
            $table->foreign('idingresobloque')->references('id')->on('ingreso_bloques')->onDelete('cascade');
            $table->unsignedBigInteger('idbloque');
            $table->foreign('idbloque')->references('id')->on('bloques');
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
        Schema::dropIfExists('detalle_ingreso_bloques');
    }
}
