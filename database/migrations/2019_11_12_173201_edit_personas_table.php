<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditPersonasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){

        Schema::table('personas', function(Blueprint $table){
            $table->unsignedBigInteger('idusuario')->nullable()->after('observacion');
            $table->foreign('idusuario')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){

        Schema::table('personas', function (Blueprint $table) {
            $table->dropColumn('idusuario');
        });
    }
}
