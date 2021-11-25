<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStockLimitsToReplacementParts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('replacement_parts', function (Blueprint $table) {
            $table->integer('lower_limit')->default(0)->after('unit');
            $table->integer('upper_limit')->default(1000)->after('unit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('replacement_parts', function (Blueprint $table) {
            $table->dropColumn('lower_limit');
            $table->dropColumn('upper_limit');
        });
    }
}
