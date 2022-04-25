<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetalleCotizacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_cotizaciones', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('idcotizacion');
            $table->foreign('idcotizacion')->references('id')->on('cotizaciones')->onDelete('cascade');

            $table->unsignedBigInteger('idarticulo');
            $table->foreign('idarticulo')->references('id')->on('articulos');

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
        Schema::dropIfExists('detalle_cotizaciones');
    }
}
