<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetalleTrasladoAbrasivosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_traslado_abrasivos', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('idtrasladoabrasivo');
            $table->foreign('idtrasladoabrasivo')->references('id')->on('traslado_abrasivos')->onDelete('cascade');

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
        Schema::dropIfExists('detalle_traslado_abrasivos');
    }
}
