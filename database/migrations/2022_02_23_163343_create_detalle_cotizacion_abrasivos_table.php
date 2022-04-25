<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetalleCotizacionAbrasivosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_cotizacion_abrasivos', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('idcotizacionabrasivo');
            $table->foreign('idcotizacionabrasivo')->references('id')->on('cotizacion_abrasivos')->onDelete('cascade');

            $table->unsignedBigInteger('idabrasivo');
            $table->foreign('idabrasivo')->references('id')->on('abrasivos');

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
        Schema::dropIfExists('detalle_cotizacion_abrasivos');
    }
}
