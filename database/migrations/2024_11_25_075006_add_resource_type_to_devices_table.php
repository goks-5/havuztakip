<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddResourceTypeToDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('devices', function (Blueprint $table) {
        $table->string('resource_type')->nullable()->after('field_name');
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
        $table->dropColumn('resource_type');
    });
}
}
