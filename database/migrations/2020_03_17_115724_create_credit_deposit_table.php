<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreditDepositTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credit_deposit', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('credit_id')->nullable();
            $table->unsignedBigInteger('deposit_id')->nullable();

            $table->foreign('credit_id')->references('id')->on('credits')
            ->onDelete('set null')
            ->onUpdate('set null');

            $table->foreign('deposit_id')->references('id')->on('deposits')
            ->onDelete('cascade')
            ->onUpdate('cascade');

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
        Schema::dropIfExists('credit_deposit');
    }
}
