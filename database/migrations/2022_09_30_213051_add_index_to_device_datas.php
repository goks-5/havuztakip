<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexToDeviceDatas extends Migration
{
    /**
     * Run the migrations.
     *,'device_id'
     * @return void
     */
    public function up()
    {
        Schema::table('device_datas', function (Blueprint $table) {
            $table->index(['device_id','data_id','hourly'],'hours_device_data');
            $table->index(['device_id','data_id','created_at'],'minutes_device_data');
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
            //
        });
    }
}
