<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldIdToDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('devices', function (Blueprint $table) {
        $table->unsignedBigInteger('field_id')->nullable()->after('type');
    });
}


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
{
    Schema::table('devices', function (Blueprint $table) {
        $table->dropColumn('field_id');
    });
}
}
