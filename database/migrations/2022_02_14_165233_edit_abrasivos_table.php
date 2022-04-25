<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditAbrasivosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('abrasivos', function(Blueprint $table){

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
        Schema::table('abrasivos', function (Blueprint $table) {

            $table->dropColumn('idusuario');

        });
    }
}
