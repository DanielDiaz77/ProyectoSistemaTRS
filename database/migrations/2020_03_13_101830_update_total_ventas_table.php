<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateTotalVentasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::select(DB::raw('ALTER TABLE `ventas` CHANGE COLUMN `adeudo` `adeudo` decimal(13,4);'));
        DB::select(DB::raw('ALTER TABLE `ventas` CHANGE COLUMN `total` `total` decimal(13,4);'));
        DB::select(DB::raw('ALTER TABLE `deposits` CHANGE COLUMN `total` `total` decimal(13,4);'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::select(DB::raw('ALTER TABLE `ventas` CHANGE COLUMN `adeudo` `adeudo` decimal(11,2);'));
        DB::select(DB::raw('ALTER TABLE `ventas` CHANGE COLUMN `total` `total` decimal(11,2);'));
        DB::select(DB::raw('ALTER TABLE `deposits` CHANGE COLUMN `total` `total` decimal(11,2);'));
    }
}
