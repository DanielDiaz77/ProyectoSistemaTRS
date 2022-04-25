<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVentaAbrasivosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('venta_abrasivos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('idcliente');
            $table->foreign('idcliente')->references('id')->on('personas');
            $table->unsignedBigInteger('idusuario');
            $table->foreign('idusuario')->references('id')->on('users');
            $table->string('tipo_comprobante',20);
            $table->string('num_comprobante',20);
            $table->dateTime('fecha_hora');
            $table->decimal('impuesto',4,2);
            $table->decimal('total',11,2);
            $table->decimal('adeudo',11,2)->nullable();
            $table->string('forma_pago',50)->nullable();
            $table->string('tiempo_entrega',50)->nullable();
            $table->string('lugar_entrega',50)->nullable();
            $table->boolean('entregado')->default(0);
            $table->boolean('entrega_parcial')->default(0);
            $table->string('estado',20);
            $table->string('moneda',20);
            $table->decimal('tipo_cambio',11,4)->nullable();
            $table->integer('num_cheque')->nullable();
            $table->string('banco',50)->nullable();
            $table->string('tipo_facturacion',50);
            $table->boolean('pagado')->default(0);
            $table->boolean('pago_parcial')->default(0);
            $table->string('observacion',256)->nullable();
            $table->string('file',128)->nullable();
            $table->string('observacionpriv',256)->nullable();
            $table->boolean('facturado')->default(0);
            $table->boolean('factura_env')->default(0);
            $table->string('num_factura',35)->nullable();
            $table->boolean('auto_entrega')->default(0);
            $table->boolean('special')->default(0);
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
        Schema::dropIfExists('venta_abrasivos');
    }
}
