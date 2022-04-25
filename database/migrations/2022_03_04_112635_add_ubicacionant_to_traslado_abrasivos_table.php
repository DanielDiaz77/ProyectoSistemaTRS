<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUbicacionantToTrasladoAbrasivosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('traslado_abrasivos', function (Blueprint $table) {
            $table->string('ubicacionant',100)->nullable()->after('nueva_ubicacion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('traslado_abrasivos', function (Blueprint $table) {
            $table->dropColumn('ubicacionant');
        });
    }
}
