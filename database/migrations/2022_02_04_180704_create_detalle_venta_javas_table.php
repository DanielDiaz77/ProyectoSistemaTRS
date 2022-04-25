<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetalleVentaJavasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_venta_javas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('idventajava');
            $table->foreign('idventajava')->references('id')->on('venta_javas')->onDelete('cascade');
            $table->unsignedBigInteger('idjava');
            $table->foreign('idjava')->references('id')->on('javas');
            $table->integer('cantidad');
            $table->decimal('precio',11,2);
            $table->decimal('descuento',11,2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detalle_venta_javas');
    }
}
