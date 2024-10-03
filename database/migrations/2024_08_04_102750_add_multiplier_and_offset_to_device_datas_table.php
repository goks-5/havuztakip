<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMultiplierAndOffsetToDeviceDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('device_datas', function (Blueprint $table) {
            $table->decimal('multiplier', 11, 6)->nullable()->default(null);
            $table->decimal('offset', 11, 6)->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('device_datas', function (Blueprint $table) {
            $table->dropColumn(['multiplier', 'offset']);
        });
    }
}
