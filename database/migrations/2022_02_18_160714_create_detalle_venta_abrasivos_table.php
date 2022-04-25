<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetalleVentaAbrasivosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_venta_abrasivos', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('idventaabrasivo');
            $table->foreign('idventaabrasivo')->references('id')->on('venta_abrasivos')->onDelete('cascade');
            $table->unsignedBigInteger('idabrasivo');
            $table->foreign('idabrasivo')->references('id')->on('abrasivos');
            $table->integer('cantidad');
            $table->integer('por_entregar')->nullable();
            $table->integer('entregadas')->nullable();
            $table->integer('pendientes')->nullable();
            $table->boolean('completado')->default(0);
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
        Schema::dropIfExists('detalle_venta_abrasivos');
    }
}
