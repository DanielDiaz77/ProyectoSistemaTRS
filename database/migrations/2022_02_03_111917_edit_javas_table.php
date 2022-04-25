<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditJavasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('javas', function(Blueprint $table){

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
        Schema::table('javas', function (Blueprint $table) {

            $table->dropColumn('comprometido');
            $table->dropColumn('idusuario');

        });
    }
}
