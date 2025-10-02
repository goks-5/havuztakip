<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToDevicesTable extends Migration
{
    public function up()
{
    Schema::table('devices', function (Blueprint $table) {
        $table->boolean('status')->default(1); // 0 = Pasif, 1 = Aktif
    });
}

public function down()
{
    Schema::table('devices', function (Blueprint $table) {
        $table->dropColumn('status');
    });
}

}
