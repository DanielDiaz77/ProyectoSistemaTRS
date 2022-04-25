<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditArticulosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('articulos', function(Blueprint $table){

            $table->boolean('comprometido')->default(0)->after('stock');

            $table->unsignedBigInteger('idusuario')->nullable()->after('comprometido');
            $table->foreign('idusuario')->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('articulos', function (Blueprint $table) {

            $table->dropColumn('comprometido');
            $table->dropColumn('idusuario');

        });
    }
}
