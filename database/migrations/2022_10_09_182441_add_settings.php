<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use TCG\Voyager\Models\Setting;

class AddSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $setting =new Setting();
        $setting->key = 'device.default_token';
        $setting->display_name = 'Default Token';
        $setting->value = '';
        $setting->details = '';
        $setting->type = 'text';
        $setting->order = '7';
        $setting->group = 'Device';
        $setting->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
