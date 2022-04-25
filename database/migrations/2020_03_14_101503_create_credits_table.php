<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('num_documento',15)->unique();
            $table->decimal('total',13,4);
            $table->string('forma_pago',35);
            $table->dateTime('fecha_hora');
            $table->text('observacion')->nullable();
            $table->string('estado',20)->default('Vigente');
            $table->morphs('creditable');
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
        Schema::dropIfExists('credits');
    }
}
