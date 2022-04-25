<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticulosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articulos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('idcategoria');
            $table->string('codigo',50)->unique();
            $table->string('sku',50);
            $table->string('nombre',100)->nullable();
            $table->string('terminado',50);
            $table->decimal('largo',4,2);
            $table->decimal('alto',4,2);
            $table->decimal('metros_cuadrados',6,4);
            $table->decimal('espesor',4,2);
            $table->decimal('precio_venta',11,2)->nullable();
            $table->string('ubicacion',50);
            $table->string('contenedor',50)->nullable();
            $table->integer('stock');
            $table->string('descripcion',256)->nullable();
            $table->string('observacion',256)->nullable();
            $table->string('origen',50)->nullable();
            $table->date('fecha_llegada')->nullable();
            $table->string('file',128)->nullable();
            $table->boolean('condicion')->default(1);
            $table->timestamps();

            $table->foreign('idcategoria')->references('id')->on('categorias');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articulos');
    }
}
