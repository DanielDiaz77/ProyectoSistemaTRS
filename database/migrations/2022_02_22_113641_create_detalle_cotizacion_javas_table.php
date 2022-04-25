<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetalleCotizacionJavasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_cotizacion_javas', function (Blueprint $table) {
            $table->bigIncrements('id');


            $table->unsignedBigInteger('idcotizacionjava');
            $table->foreign('idcotizacionjava')->references('id')->on('cotizacion_javas')->onDelete('cascade');
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
        Schema::dropIfExists('detalle_cotizacion_javas');
    }
}
