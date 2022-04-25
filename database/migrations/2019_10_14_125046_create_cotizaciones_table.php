<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCotizacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cotizaciones', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('idcliente');
            $table->foreign('idcliente')->references('id')->on('personas');

            $table->unsignedBigInteger('idusuario');
            $table->foreign('idusuario')->references('id')->on('users');

            $table->string('tipo_comprobante',20);
            $table->string('num_comprobante',20);
            $table->dateTime('fecha_hora');
            $table->dateTime('vigencia');
            $table->decimal('impuesto',4,2);
            $table->decimal('total',11,2);
            $table->string('estado',20);
            $table->string('moneda',20)->nullable();
            $table->decimal('tipo_cambio',11,4)->nullable();
            $table->string('observacion',256)->nullable();
            $table->string('forma_pago',50)->nullable();
            $table->string('tiempo_entrega',50)->nullable();
            $table->string('lugar_entrega',50)->nullable();
            $table->boolean('aceptado')->default(0);

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
        Schema::dropIfExists('cotizaciones');
    }
}
