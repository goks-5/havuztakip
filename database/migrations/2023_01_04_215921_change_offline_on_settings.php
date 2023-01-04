<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use TCG\Voyager\Models\Setting;

class ChangeOfflineOnSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $setting = Setting::where('key', 'device.ofline')->first();
        if ($setting) {
            $setting->details = '{"default" : "5",
                "options" : {
                "5" : "5 dk" ,
                "10":"10 dk" ,
                "15":"15 dk",
                "30":"30 dk",
                "60":"1 Saat",
                "120":"2 Saat",
                "180":"3 Saat",
                "360":"6 Saat",
                "570":"9 Saat",
                "720":"12 Saat",
                "900":"15 Saat",
                "1080":"18 Saat",
                "1440":"1 Gün"}
            }';$setting->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $setting = Setting::where('key', 'device.ofline')->first();
        if ($setting) {
            $setting->details = '{"default" : "5",
                "options" : {
                "5" : "5 dk" ,
                "10":"10 dk" ,
                "15":"15 dk",
                "30":"30 dk",
                "60":"1 Saat",
                "120":"2 Saat",
                "180":"3 Saat"}
            }';
            $setting->save();
        }
    }
}
