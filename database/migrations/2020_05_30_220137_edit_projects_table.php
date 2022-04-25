<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::select(DB::raw('ALTER TABLE `personas` CHANGE COLUMN `telefono` `telefono` varchar(50);'));
        DB::select(DB::raw('ALTER TABLE `ventas` CHANGE COLUMN `lugar_entrega` `lugar_entrega` varchar(120);'));
        DB::select(DB::raw('ALTER TABLE `projects` CHANGE COLUMN `lugar_entrega` `lugar_entrega` varchar(120);'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::select(DB::raw('ALTER TABLE `personas` CHANGE COLUMN `telefono` `telefono` varchar(20);'));
        DB::select(DB::raw('ALTER TABLE `ventas` CHANGE COLUMN `lugar_entrega` `lugar_entrega` varchar(50);'));
        DB::select(DB::raw('ALTER TABLE `projects` CHANGE COLUMN `lugar_entrega` `lugar_entrega` varchar(50);'));
    }
}
