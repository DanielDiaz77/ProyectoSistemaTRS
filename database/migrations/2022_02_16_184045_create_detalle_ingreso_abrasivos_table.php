<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetalleIngresoAbrasivosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_ingreso_abrasivos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('idingresoabrasivo');

            $table->foreign('idingresoabrasivo')->references('id')->on('ingreso_abrasivos')->onDelete('cascade');
            $table->unsignedBigInteger('idabrasivo');

            $table->foreign('idabrasivo')->references('id')->on('abrasivos');
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
        Schema::dropIfExists('detalle_ingreso_abrasivos');
    }
}
