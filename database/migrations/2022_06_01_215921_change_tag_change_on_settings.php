<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use TCG\Voyager\Models\Setting;

class ChangeTagChangeOnSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $setting = Setting::where('key', 'device.tag.change')->first();
        if ($setting) {
            $setting->details = '{"default":"14400","options":{"5":"5 dk","10":"10 dk","15":"15 dk","30":"30 dk","60":"60 dk","90":"90 dk","120":"2 Saat","180":"3 Saat","220":"4 Saat","300":"5 Saat","7200":"12 Saat","14400":"24 Saat","21600":"36 Saat","28800":"2 Gun","33200":"3 Gun"}}';
            $setting->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $setting = Setting::where('key', 'device.tag.change')->first();
        if ($setting) {
            $setting->details = '{"default":"5","options":{"5":"5 dk","10":"10 dk","15":"15 dk","30":"30 dk","60":"1 Saat","120":"2 Saat","180":"3 Saat"}}';
            $setting->save();
        }
    }
}
