<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('idcliente');
            $table->unsignedBigInteger('idusuario');
            $table->string('tipo_comprobante',20);
            $table->string('num_comprobante',20);
            $table->string('title',50);
            $table->text('content',250)->nullable();
            $table->dateTime('inicio');
            $table->dateTime('fin');
            $table->decimal('impuesto',4,2);
            $table->decimal('total',11,2);
            $table->decimal('adeudo',11,2);
            $table->string('forma_pago',50)->nullable();
            $table->string('lugar_entrega',50)->nullable();
            $table->string('estado',20);
            $table->boolean('pagado')->default(0);
            $table->boolean('pagado_parcial')->default(0);
            $table->boolean('entregado')->default(0);
            $table->boolean('entregado_parcial')->default(0);
            $table->boolean('flete')->default(0);
            $table->boolean('instalacion')->default(0);
            $table->string('area',20);
            $table->string('tipo_facturacion',50);
            $table->string('observacion',256)->nullable();
            $table->string('observacionpriv',256)->nullable();
            $table->timestamps();
            $table->foreign('idcliente')->references('id')->on('personas')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('idusuario')->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
}
