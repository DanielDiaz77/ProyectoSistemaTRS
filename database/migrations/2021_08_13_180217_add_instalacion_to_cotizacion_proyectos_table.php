<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInstalacionToCotizacionProyectosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cotizacion_proyectos', function (Blueprint $table) {
            $table->boolean('instalacion')->default(0)->after('flete');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cotizacion_proyectos', function (Blueprint $table) {
            $table->dropColumn('instalacion');
        });
    }
}
