<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditDetalleSalidasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('detalle_salidas', function(Blueprint $table){
            $table->integer('por_entregar')->nullable()->after('cantidad');
            $table->integer('entregadas')->nullable()->after('por_entregar');
            $table->integer('pendientes')->nullable()->after('entregadas');
            $table->boolean('completado')->default(0)->after('pendientes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('detalle_salidas', function (Blueprint $table) {
            $table->dropColumn('por_entregar');
            $table->dropColumn('entregadas');
            $table->dropColumn('pendientes');
            $table->dropColumn('completado');
        });
    }
}
