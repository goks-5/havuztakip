<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class changeTitleSizeFromReports extends Migration
{
    /**
     * Run the migrations.
     *,'device_id'
     * @return void
     */
    public function up()
    {
        Schema::table('reports', function (Blueprint $table) { 
            $table->string('tags', 5000)->change();
            $table->string('titles', 10000)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->string('tags', 1000)->change();
            $table->string('titles', 2000)->change();
        });
    }
}
