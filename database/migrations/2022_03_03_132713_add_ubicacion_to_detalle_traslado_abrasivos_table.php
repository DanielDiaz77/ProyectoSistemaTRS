<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUbicacionToDetalleTrasladoAbrasivosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('detalle_traslado_abrasivos', function (Blueprint $table) {
            $table->string('ubicacion',50)->nullable()->after('cantidad');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('detalle_traslado_abrasivos', function (Blueprint $table) {
            $table->dropColumn('ubicacion');

        });
    }
}
