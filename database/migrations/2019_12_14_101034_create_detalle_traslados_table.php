<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetalleTrasladosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_traslados', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('idtraslado');
            $table->foreign('idtraslado')->references('id')->on('traslados')->onDelete('cascade');

            $table->unsignedBigInteger('idarticulo');
            $table->foreign('idarticulo')->references('id')->on('articulos');

            $table->integer('cantidad');
            $table->string('ubicacion',50);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detalle_traslados');
    }
}
