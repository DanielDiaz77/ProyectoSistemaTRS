<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAbrasivosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('abrasivos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('codigo', 50)->nullable();
            $table->string('sku',50)->nullable();
            $table->decimal('precio_venta',11,2)->nullable();
            $table->integer('stock');
            $table->string('descripcion',256)->nullable();
            $table->string('ubicacion',50)->nullable();
            $table->string('file',128)->nullable();
            $table->boolean('condicion')->default(1);
            $table->integer('comprometido')->default(0);
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
        Schema::dropIfExists('abrasivos');
    }
}
