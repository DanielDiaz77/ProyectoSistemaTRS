<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetalleIngresosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_ingresos', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('idingreso');

            $table->foreign('idingreso')->references('id')->on('ingresos')->onDelete('cascade');
            $table->unsignedBigInteger('idarticulo');

            $table->foreign('idarticulo')->references('id')->on('articulos');
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
        Schema::dropIfExists('detalle_ingresos');
    }
}
