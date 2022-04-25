<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('start');
            $table->dateTime('end');
            $table->string('title',50);
            $table->string('content',256)->nullable();
            $table->boolean('status')->default(0);

            $table->unsignedBigInteger('idemisor')->nullable();
            $table->foreign('idemisor')->references('id')->on('users');

            $table->unsignedBigInteger('idreceptor')->nullable();
            $table->foreign('idreceptor')->references('id')->on('users');

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
        Schema::dropIfExists('activities');
    }
}
